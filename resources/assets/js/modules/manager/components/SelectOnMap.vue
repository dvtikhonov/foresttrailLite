<template>
    <div>
        <yandex-map
                :settings="common.yandexMapSettings"
                :options="{
                        suppressMapOpenBlock: true,
                        yandexMapDisablePoiInteractivity: true
                    }"
                :coords="coords"
                zoom="18"
                :style="styleMap"
                @map-was-initialized="initYmap"
                :behaviors="['drag','multiTouch','dblClickZoom','scrollZoom']"
                :controls="['typeSelector', 'searchControl', 'fullscreenControl']"
        >
        </yandex-map>
    </div>
</template>

<script>
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';

    export default {
        props: ['value'],
        name: "SelectOnMap",
        components: {
            yandexMap
        },

        data() {
            return {
                ymaps: null,
                map: null,
                coords: [53.757171, 87.136716],
                myStartPoint: null, //  координата точку начала
                mapWasInitialized: false,
                styleMap: {
                    width: '100%',
                    height: '300px',
                    display: 'flex',
                },
            }
        },
        mounted() {
            this.loader = this.$loading.show();
        },
        computed: {
            common() {
                return this.$store.state;
            },
        },
        watch: {
            value(newValue){
                if( ! newValue){return;}
                this.coords = newValue;

                if( ! this.myStartPoint){return;}
                this.myStartPoint.geometry.setCoordinates(newValue);
            }
        },

        methods: {

            initYmap(a) {
                this.map = a;
                this.ymaps = ymaps;
                this.loader.hide();
                // добавляем точку
                this.myStartPoint  = new ymaps.Placemark( this.coords, {
                    iconCaption: 'точка POS'
                }, {
                    preset: 'islands#greenDotIconWithCaption'
                });
                this.map.geoObjects.add(this.myStartPoint);

                if (!this.mapWasInitialized) {
                    this.mapWasInitialized = true;
                }

                this.map.events.add('click', this.changeCoords);
            },

            changeCoords(e){
                let coords = e.get('coords');
                this.ymaps.geocode(coords, {kind: 'house', format: 'json', results: 1})
                    .then((result) => {
                        this.myStartPoint.geometry.setCoordinates(coords);

                        if (result.geoObjects.getLength() > 0){
                            coords[2] = result.geoObjects.get(0).properties.get('text');
                        }else{
                            coords[2] = null;
                        }
                        this.$emit('input', coords);
                    })
                    .catch((result=>{}));
            }

        }
    }
</script>

<style scoped>

</style>