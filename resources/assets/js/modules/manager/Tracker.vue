<template>
    <b-container>
        <b-row class="justify-content-center">
            <b-col sm="3">
                <b-card no-body>
                    <h6 slot="header"
                        class="mb-0">
                        Пользователи
                    </h6>
                    <select-user v-model="selectedUser"></select-user>
                </b-card>
            </b-col>
            <b-col sm="9">
                <div class="d-flex align-items-center pt-1 pb-1">
                    <select-date-time-range v-model="dateFilter"></select-date-time-range>
                    <b-button class="ml-1" @click="loadCoordinates(selectedUser)">{{ $t("manager.refresh") }}</b-button>

                </div>
                <div>
                    <yandex-map
                            :settings="common.yandexMapSettings"
                            :coords="centerMap"
                            zoom="16"
                            :style="styleMap"
                            :placemarks="placemarks"
                            map-type="map"
                            @map-was-initialized="initYmap"
                    >
                    </yandex-map>
                </div>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    import { mapActions } from 'vuex';
    import SelectUser from "./components/SelectUser";
    import SelectDateTimeRange from "./components/SelectDateTimeRange";
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';

    export default {
        components: {
            yandexMap,
            SelectDateTimeRange,
            SelectUser
        },
        name:       "managerTracker",

        computed: {
            common(){
                return this.$store.state;
            },
            auth() {
                return this.$store.state.auth;
            },
            appHeight(){
                return this.$store.state.dimension.height - 130;
            },
            histCoordinatesAsArray(){
                let coords = [];
                this.histCoordinates.forEach((item, i)=>{
                    let accuracy = 0.1;
                    if(item.accuracy < 5){
                        accuracy = 0.9;
                    }else if(item.accuracy < 7.5){
                        accuracy = 0.7;
                    }else if(item.accuracy < 10){
                        accuracy = 0.5;
                    }else if(item.accuracy < 15){
                        accuracy = 0.3;
                    }else if(item.accuracy < 60){
                        accuracy = 0.2;
                    }
                    coords.push([item.lat, item.lon, item.timestamp, accuracy]);
                });
                return coords;
            },
        },

        data() {
            return {
                placemarks: [],
                loader: null,
                mapWasInitialized: false,
                ymaps: null,
                map: null,
                selectedUser: null,
                centerMap: [54.62896654088406, 39.731893822753904],
                styleMap:{
                    width: '100%',
                    height: '0%',
                    display: 'flex',
                },
                geolocation:[],
                histCoordinates:[],
                dateFilter: null,
            }
        },
        mounted() {
            this.loader = this.$loading.show();

            this.styleMap.height = this.appHeight + 'px';
        },
        watch: {
            appHeight(val) {
                this.styleMap.height = this.appHeight + 'px';
            },
            selectedUser(val){
                this.loadCoordinates(val);
            },
            histCoordinates(val){
                if(val.length < 1){
                    if(this.userMarker){this.userMarker.removeAll();this.userMarker = false;}
                    if(this.userTrail){this.userTrail.removeAll();this.userTrail = false;}

                    return;
                }
                let position = val[0];
                position = [position.lat, position.lon, position.timestamp, 'accuracy' in position ? position.accuracy : 0];
                this.geolocation = position;
                this.centerMap = position;

                this.refreshMark();
                this.refreshTrail();
            },
        },
        methods: {
            initYmap(a) {
                this.map = a;
                this.ymaps = ymaps;
                this.loader.hide();

                //this.updateGeolocationBrowser();
                if( ! this.mapWasInitialized){
//                    console.log('mapWasInitialized - true');
                    //this.startWatchPosition();
                    this.mapWasInitialized = true;
                }
            },
            loadCoordinates(userId){
                let loader = this.$loading.show();
                let url = '/api/v1/coordinates/?user_id='+userId;
                if(this.dateFilter && this.dateFilter.length > 1 && this.dateFilter[0] !== null && this.dateFilter[1] !== null){
                    let formatedDate = Math.floor(this.dateFilter[0].getTime()/1000) + ',' + Math.floor(this.dateFilter[1].getTime()/1000);
                    url += '&date-range='+formatedDate;
                }
                this.$http.get(url).then((response) => {
                    this.histCoordinates = response.data;
                    loader.hide();
                }).catch((data) => {
                    console.error('Not saved coords: ', data);
                    loader.hide();
                });
            },
            refreshMark(){
                if( ! this.userMarker || (this.userMarker && this.userMarker.getLength() === 0)){
                    this.userMarker = (new ymaps.GeoObjectCollection({},{})).add( new this.ymaps.Placemark(this.geolocation, {
                        // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                        balloonContentHeader: "Балун метки",
                        balloonContentBody:   "Содержимое <em>балуна</em> метки",
                        balloonContentFooter: "Подвал",
                        hintContent:          "Хинт метки"
                    }));
                    this.map.geoObjects
                        .add(this.userMarker);
                    return;
                }
                let marker = this.userMarker.getIterator().getNext();
                marker.geometry.setCoordinates(this.geolocation);
            },
            // refreshTrail(){
            //     if( ! this.userTrail) {
            //         this.userTrail = new ymaps.GeoObjectCollection({},{
            //             // Задаем опции геообъекта.
            //             // Отключаем кнопку закрытия балуна.
            //             balloonCloseButton: true,
            //             // Цвет линии.
            //             strokeColor:        "#000000",
            //             // Ширина линии.
            //             strokeWidth:        4,
            //             geodesic: true
            //         });
            //     }else{
            //         this.userTrail.removeAll();
            //     }
            //     this.histCoordinatesAsArray.forEach((item, i, arr) => {
            //
            //         if(this.userTrail.getLength() > i || arr.length <= i+1){return}
            //
            //         this.userTrail.add( new this.ymaps.Polyline([[item[0], item[1]], [arr[i+1][0], arr[i+1][1]]], {
            //             // Описываем свойства геообъекта.
            //             // Содержимое балуна.
            //             balloonContent: "След"
            //         }, {
            //             // Коэффициент прозрачности.
            //             strokeOpacity:      arr[i+1][3] ? arr[i+1][3] : 0.1
            //         }));
            //     });
            //
            //     this.map.geoObjects
            //         .add(this.userTrail);
            // },
            refreshTrail(){
                if( ! this.userTrail) {
                    this.userTrail = (new ymaps.GeoObjectCollection({},{})).add( new this.ymaps.Polyline(this.histCoordinatesAsArray, {
                        // Описываем свойства геообъекта.
                        // Содержимое балуна.
                        balloonContent: "След"
                    }, {
                        // Задаем опции геообъекта.
                        // Отключаем кнопку закрытия балуна.
                        balloonCloseButton: false,
                        // Цвет линии.
                        strokeColor:        "#000000",
                        // Ширина линии.
                        strokeWidth:        4,
                        // Коэффициент прозрачности.
                        strokeOpacity:      0.5
                    }));
                    this.map.geoObjects
                        .add(this.userTrail);
                    return
                }
                let trail = this.userTrail.getIterator().getNext();
                trail.geometry.setCoordinates(this.histCoordinatesAsArray);
            },
            ...mapActions([
                'title' // проксирует `this.title()` в `this.$store.dispatch('title')`
            ]),
        },
    }
</script>

<style scoped>

</style>