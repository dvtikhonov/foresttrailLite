<template>
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
            <b-row class="pt-2 pb-1">
                <b-col sm="12" md="4">
                    <select-date-time-range v-model="dateFilter"></select-date-time-range>
                </b-col>
                <b-col sm="12" md="8" class="pt-1 pt-md-0">
                    <treeselect  @input="selectSes()" :disabled="disabledSelectSession"
                                 :options="options2"
                                 placeholder="Выберите сессию"
                                 v-model="fields.sessionId"
                                 :normalizer="normalizer"
                    />
                </b-col>
            </b-row>
            <b-row class="pt-1 pb-2">
                <b-col sm="12" md="12">
                    <b-button @click="loadCoordinates(selectedUser)">{{ $t("manager.refresh") }}</b-button>
                    <b-button class="ml-1 d-none d-md-inline" @click.prevent="(poligonEditOn == '')  ? poligonEditOn='text-danger' : poligonEditOn=''"  v-bind:class="poligonEditOn " > Перенос точек</b-button>
                    <b-button class="ml-1" @click="deltaTrail()" >Сохранить</b-button>
                    <b-button variant="info" class="ml-1 float-right" @click="startCalculateVectors()" >Авторасчет</b-button>
                </b-col>
                <b-col sm="12" md="6" class="align-content-sm-center">
                </b-col>
                <!--<router-link class="btn btn-info btn-rounded" tag="button" :to="{name:'managerTracker'}">Вернуться</router-link>-->
            </b-row>
            <div>
                <yandex-map
                        :coords="centerMap"
                        zoom="18"
                        :style="styleMap"
                        :placemarks="placemarks"
                        map-type="map"
                        @map-was-initialized="initYmap"
                >
                </yandex-map>
            </div>
        </b-col>
        <!-- Для тестов дпоплнительное поле  -->
        <!--<div id="map" style="width:800px; height:600px"></div>-->
        <!--<input type="button" value="Завершить редактирование" id="stopEditPolyline"/>-->
        <!--<div id="geometry">-->
        <!--</div>-->
        <!--<textarea name="coords" id="id_coords" readonly="" rows="5"></textarea>-->
    </b-row>
</template>

<script>
    import { mapActions } from 'vuex';
    import SelectUser from "./components/SelectUser";
    import SelectDateTimeRange from "./components/SelectDateTimeRange";
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';

    // для  treeselect
    const simulateAsyncOperation = fn => {
        setTimeout(fn, 2000)
    };


    export default {
        components: {
            SelectDateTimeRange,
            SelectUser,
            yandexMap
        },
        name:       "calibration",

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
                poligonEditOn: '',
                polygon: null,
                idTrail: null,
                dTrail: null,
                pTrail: null,
                myStartPoint: null, //  координата точку начала
                datePolyline: null, // дата текущей полилинии
                fields: {}, // для данных формы
                options: [],
                options2: [ {  // пример  для  treeselect
                    key: 'a',       //  _this4.options2[""0""].key
                    name: 'all',    //  _this.options2[""0""].name
                    subOptions: [ {
                        key: 'aa',
                        name: 'aa',
                    }, {
                        key: 'ab',
                        name: 'ab',
                    }],
                }, {
                    key: 'b',
                    name: 'bad',
                    subOptions: [ {
                        key: 'ba',
                        name: 'ba',
                    }, {
                        key: 'bb',
                        name: 'bb',
                    }],
                }, {
                    key: 'c',
                    name: 'c',
                }],
                errors: {}, // для сообщения оибок
                fields1: [],
                fields2: [], // для trail
                text: '', //  "disabled" кнопкам
                eMaxPoin: 6, // максимальнок количество редактируемых точек полилинии
                placemarks: [],
                loader: null,
                mapWasInitialized: false,
                disabledSelectSession: true,
                ymaps: null,
                map: null,
                selectedUser: null, // выбранный пользователь
                centerMap: [53.757171, 87.136716],
                styleMap:{
                    width: '100%',
                    height: '0%',
                    display: 'flex',
                },
                geolocation:[],
                histCoordinates:[],
                dateFilter: null,
                strokeWidth: 6,
                strokeColor: '#0000FF',
                draggable: true,
                myPolyline: null,
                myPolyline22: null,
                dPolyline: null,
                myPolyline1: [
                    [53.7499, 87.1360],
                    [53.7599, 87.1380],
                    [53.73, 87.1260],
                    [53.78, 87.09],
                ]
            }
        },
        mounted() {
            this.loader = this.$loading.show();

            this.styleMap.height = this.appHeight + 'px';
        },
        watch: {
            fields (val) {
                alert (val)
            },

            appHeight(val) {
                this.styleMap.height = this.appHeight + 'px';
            },
            selectedUser(val){
                this.InitSession(val);
            },
            poligonEditOn(val) {
                if (this.myPolyline) {
                    if (val == '')
                    {
                        this.map.geoObjects.remove(this.polygon);
                        this.myPolyline.editor.startEditing();

                    }else{
                        this.myPolyline.editor.stopEditing();

                        this.poligonEdit();
                    }
                }
            },
        },
        methods: {
            initYmap(a)
            {
                this.map = a;
                this.ymaps = ymaps;
                this.loader.hide();

                //this.updateGeolocationBrowser();
                if (!this.mapWasInitialized) {
                    //this.startWatchPosition();
                    this.mapWasInitialized = true;
                }

//                this.poligonNew(); // тест  poligonNew()
            },
            startCalculateVectors()
            {
                this.loader = this.$loading.show();
                return new Promise((resolve, reject) => {
                    this.$http.get(configs.apiUrl + '/calibration/start-calculate-vectors/').then((response) => {
                        this.loader.hide();
                        resolve(response);
                    }).catch((data) => {
                        console.error('Not saved coords: ', data);
                        this.loader.hide();
                        reject(data);
                    });
                })
            },
            loadCoordinates()
            {
                return new Promise((resolve, reject) => {
                    let url = configs.apiUrl + '/coordinates/?user_id=' + this.selectedUser + '&session_id='+this.fields.sessionId;
                    if (this.dateFilter && this.dateFilter.length > 1 && this.dateFilter[0] !== null && this.dateFilter[1] !== null) {
                        let formatedDate = Math.floor(this.dateFilter[0].getTime() / 1000) + ',' + Math.floor(this.dateFilter[1].getTime() / 1000);
                        url += '&date-range=' + formatedDate;
                    }
                    this.$http.get(url).then((response) => {
                        this.histCoordinates = response.data;
                        this.disabledSelectSession = false; // включить кнопки
                        resolve();
                    }).catch((data) => {
                        console.error('Not saved coords: ', data);
                        reject();
                    });
                })
            },

            refreshMark()
            {
                if (!this.userMarker || (this.userMarker && this.userMarker.getLength() === 0)) {
                    this.userMarker = (new ymaps.GeoObjectCollection({}, {})).add(new this.ymaps.Placemark(this.geolocation, {
                        // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                        balloonContentHeader: "Балун метки",
                        balloonContentBody: "Содержимое <em>балуна</em> метки",
                        balloonContentFooter: "Подвал",
                        hintContent: "Хинт метки"
                    }));
                    this.map.geoObjects
                        .add(this.userMarker);
                    return;
                }
                let marker = this.userMarker.getIterator().getNext();
                marker.geometry.setCoordinates(this.geolocation);
            },

            refreshTrail()
            {
                // if (!this.userTrail) {
                if (this.selectedUser === null) {
                    return  alert('пользователь не выбран');
                }
                this.datePolyline = '';
                const  itemSel = this.InintPolyline(); // инициалтзация редактируемой полилинии
                itemSel.then((result)=>{
                    this.map.geoObjects.remove(this.userTrail); // удалим всю коллекцию вместе с myPolyline


                    this.userTrail = (new ymaps.GeoObjectCollection({}, {}));
                    // var myCircle = new ymaps.GeoObject({
                    //     geometry: {
                    //         type: "Circle",
                    //         coordinates: [53.7499, 87.1360],
                    //         radius: 10,
                    //         strokeColor: "#00000088",
                    //     }
                    // });

                    var myPolylineTmp = new ymaps.Polyline([
                            [53.7499, 87.1360],
                        ], {}, {
                            /**
                             }
                             * Setting geo object options.
                             * Color with transparency.
                             */
                            strokeColor: "#00000088",
                            // The line width.
                            strokeWidth: 4,
                            // The maximum number of vertices in the polyline.
                            editorMaxPoints: this.eMaxPoin,
                            editorMinPoints: this.eMaxPoin,
                            // // Adding a new item to the context menu that allows deleting the polyline.
                            editorMenuManager: (items) => {
                                return [];   // заглушка
                                items.push({
                                    title: "Delete line",
                                    onClick: () => {
                                        this.map.geoObjects.remove(this.userTrail); // удалим всю коллекцию вместе с myPolyline
                                    }
                                });
                                if (this.myPolyline.geometry.getCoordinates().length === this.eMaxPoin) { //getNumPoints()
                                    this.disabledSelectSession = false;
                                };
//                            geoId[i] = this.map.geoObject.properties.get();
                                return items;
                            },
                        }
                    );
                    this.myPolyline  = myPolylineTmp;
                    this.myPolyline.geometry.events.add('change',function (a)  { // событие изменения полилинии
                            this.pTrail = a.originalEvent.newCoordinates;
                        if (this.myStartPoint)  {this.myStartPoint.geometry.setCoordinates(this.myPolyline.geometry.get(0))}; // сменить координаты точки
                        }.bind(this)
                    );
                    // this.myPolyline.events.add('geometrychange', function (e) {   // альт. вариант  событие изменения полилинии. e.originalEvent.target._geoObjectComponent._geometry._coordPath._coordinates[""0""]
                    //     console.log('событие ИЗМ');
                    // myPolyline.geometry.remove(myPolyline.geometry.getClosest(e.get(
                    //     'coords'))
                    //     .closestPointIndex);
                    // });

                    // Добавить в коллекцию объект myPolyline
                    this.userTrail.add(this.myPolyline);
                    // добавить в глобальный объект map коллекцию userTrail
                    this.map.geoObjects.add(this.userTrail);
                    this.generPolyline(result);

                    // выставвить маркер начало линии.
                    if ( ! this.myStartPoint) {
                        this.myStartPoint  = new ymaps.Placemark(this.myPolyline.geometry.get(0), {
                            preset: 'islands#circleIcon',
                            iconColor: '#3caa3c',
                            iconContent: '0'
                        });
                    }
                    this.userTrail.add(this.myStartPoint);


                    // Turning on the edit mode.
                    this.myPolyline.editor.startEditing();

                    // this.myPolyline.geometry.setCoordinates(
                    //     this.myPolyline1
                    // );

                    this.disabledSelectSession = false;
                    return
                    // }
                    // let trail = this.userTrail.getIterator().getNext();
                    // trail.geometry.setCoordinates(this.histCoordinatesAsArray);
                });

                this.loader.hide();
            },

        ...mapActions([
                'title' // проксирует `this.title()` в `this.$store.dispatch('title')`
            ]),

            printTrail () {
            return this.pTrail = this.myPolyline.getPoints() //geometry.getCoordinates(); //
            },

            deltaTrail () {
                if (this.pTrail.length > 0){
                const ddPolyline = [];
                for (let i = 0; i < this.pTrail.length-1; i++) {
                    ddPolyline.push([(this.pTrail[i][0]-this.pTrail[i+1][0])/0.00001*1.113004727, (this.pTrail[i][1]-this.pTrail[i+1][1])/0.00001*0.659567229]); // вычислить отрезки
                };
                ddPolyline.unshift([this.pTrail[0][0], this.pTrail[0][1]]); // Добавить нулевую координату
                this.dTrail = ddPolyline;
                this.inversedeltaTrail();
                this.saveTrail();
                }else{
                    console.log('нет точек полилинии');
                }
                return;
            },

            inversedeltaTrail () {
                const ddPolyline = [];
                ddPolyline.unshift([this.dTrail[0][0], this.dTrail[0][1]]); // Добавить нулевую координату
                for (let i = 1; i < this.dTrail.length; i++) {
                    ddPolyline.push([(ddPolyline[i-1][0]-this.dTrail[i][0]*0.00001/1.113004727), (ddPolyline[i-1][1]-this.dTrail[i][1]*0.00001/0.659567229)]); // вычислить координаты точек по отрезкам
                };
                this.idTrail = ddPolyline;
                return ddPolyline;
            },

            // сохранение  редактируемой полилинии
            saveTrail () {
                if (!this.fields.sessionId) {
                    return;
                }
                this.errors = {}; // очистить ошибки
                const tmpTrail = {};
                tmpTrail.items = [];
                tmpTrail.startPoint = this.dTrail[0];
                this.dTrail.forEach((item, i) => { // подготовка полилинии к записи в базу
                    if (i  === 0) {return}; // пропустить нулевой цикл
                    tmpTrail.items[i-1] = {};
                    tmpTrail.items[i-1].x = item[0];
                    tmpTrail.items[i-1].y = item[1];
                    tmpTrail.items[i-1].z = 0;
                    tmpTrail.items[i-1].user_id = this.selectedUser;
                    tmpTrail.items[i-1].tracker_sessions_id = this.fields.sessionId;
                    if (this.fields.items.length > 0) {
                        tmpTrail.items[i-1].id = this.fields.items[i-1].id;
                    }
                });
                let loader;
                loader = this.axios.put(configs.apiUrl + '/calibration/'+this.fields.sessionId, tmpTrail);// запись полилинии в базу
                    loader.then((response_data) => {       // обработка promise
                        if ( this.fields.items.length === 0)
                            {this.refreshTrail()};
                    console.log("Запись - сохраненна ")
                })
                    .catch((response_err) => {
                        alert('ERROR' + response_err);
                        console.log(this.errors);
                    });
                return;
            },

            // инициалтзация редактируемой полилинии
            InintPolyline() {  // редактировать запись из таблицы
//                let url = '/api/v1/coordinates/?user_id='+userId;

                return new Promise((resolve, reject) => {

                    let url = configs.apiUrl + '/calibration/'+this.fields.sessionId+'?user_id='+ this.selectedUser;
                    this.errors = {}; // очистить ошибки
                    this.axios.get(url)
                        .then((response) => {
                            this.fields.items = response.data.items // загрузить данные полученные от запроса
                            this.fields.arrLength =  response.data.arrLength
                            this.fields.lat = response.data.lat
                            this.fields.lon = response.data.lon
                            // this.InintPolylineNext();
                            console.log("Записи - загружены ")
                            resolve(this.InintPolylineNext()); // выполнить продолжение функции InintPolyline
                        }).catch((error) => {
                        // alert('ERROR' + error);
                        console.log(error);
                        this.errors.error =  error.response.data.error// загрузить ошибки полученные от запроса
                        reject(error);
                    });
                });
            },

            InintPolylineNext() {
                const itemTmp = this.objectToArray(this.fields1.items); // массив сессий
                const item = itemTmp.filter(e => e.id === this.fields.sessionId); // сформировать массив выбранной полилинии
                this.eMaxPoin = this.fields.arrLength; // определить  максимальное количество точек полилинии
                if (item.length === 0) {
                    this.datePolyline = 'нет данных';
                } else {
                    this.datePolyline = item[0].created_at
                }; // дата текущей полилинии}
                return item; //
            },

// Генератор массива полилинии  и загрузки в текущую карту
            generPolyline(ee)
            {
                let centerMap = this.map.getCenter(); //  из текущей карты вытащить координаты центра
                let newPolyline = [];

                if (this.fields.items.length === 0) {   // если нет векторов в сессии
                    if( this.histCoordinates.length > 0 ) { // если нет векторов в сессии и нет измерений полилинии
                        centerMap = [this.histCoordinates[0].lat, this.histCoordinates[0].lon];
                        this.histCoordinates.forEach(function (item) { //  сгенерировали по последней  истории
                                newPolyline.push([item.lat,item.lon]);
                            });
                    }else{
                        for (let i = 0; i <= this.fields.arrLength; i++) { // сгенерировали прототип
                            newPolyline.push([centerMap[0], centerMap[1] + 0.0001 * i]); // генерация координат массива координат  полилинии по количеству  записей полилинии/ по х 0.0003=19,787016875873 метров один участок., х 0.0001=6,595672292167, x 0.00001 = 0,659567229  метров один участок.  ,  y 0.0001= 11,13004777 метров один участок.,  y 0.00001= 1,113004727 метров один участок.
                        }
                    }
                }else {
                    this.dTrail = [];
                    if (this.fields.lat) { // если есть вектора и есть начало полилинии
                        centerMap = [this.fields.lat,this.fields.lon]; //  из текущей сессии вытащить координаты начала полилинии
                    }
                    this.dTrail.unshift(centerMap);
                    for (let i = 0; i < this.fields.items.length; i++) {
                        this.dTrail.push([this.fields.items[i].x, this.fields.items[i].y]); // генерация координат массива координат  полилинии по количеству  записей полилинии/ по х 0.0003=19,787016875873 метров один участок., х 0.0001=6,595672292167, x 0.00001 = 0,659567229  метров один участок.  ,  y 0.0001= 11,13004777 метров один участок.,  y 0.00001= 1,113004727 метров один участок.
                    }
                    newPolyline = this.inversedeltaTrail ();
                }
                this.map.setCenter(centerMap); // установить центр карты
                this.myPolyline.geometry.setCoordinates( // загрузить новую полилинию на карту
                    newPolyline
                );
                return;
            },

            objectToArray: function (obj) { //  преобразования из object в array,
                var arr = [];
                for (let o in obj) {
                    if (obj.hasOwnProperty(o)) {
                        arr.push(obj[o]);
                    }
                }
                return arr;
            },
            normalizer(node) { // переименовываем имена полей для  treeselect
                return {
                        id: node.key,
                    label: node.name,
  //                  children: node.subOptions,
                }
            },

// инициализация выбора сессии
            InitSession(user_id) {
                let url = configs.apiUrl + '/calibration/?user_id=' + user_id;

                if (this.dateFilter && this.dateFilter.length > 1 && this.dateFilter[0] !== null && this.dateFilter[1] !== null) { //добавить в запрос период выборки
                    let formatedDate = Math.floor(this.dateFilter[0].getTime() / 1000) + ',' + Math.floor(this.dateFilter[1].getTime() / 1000);
                    url += '&date-range=' + formatedDate;
                }

                this.fields.sessionId = null;
                this.errors = {};
                this.axios.get(url)
                    .then((response_data) => {
                        this.fields1 = response_data.data;
                        this.options2 =  this.selectSession(this.fields1.items); // сгенерировать для  treeselect массив выборки
                        this.fields.sessionId = this.options2[0].key; // загруить по умолчания номер сессии

                        console.log("данные сесии - загружены")
                    })
                    .catch((response_err) => {
                        this.errors = response_err
                    });
                return
            },

//  выборка массива уникальных, отсортированных значений из объекта, генерация  массива сеесий для  treeselect
            selectSession(ee) {
                const eeTmp = this.objectToArray(ee);
                let eSet = new Set();
                const session = eeTmp.map(e => {  // массив session записей полилиний
                    let e2 = '№ ' +e.id + '   Дата:  ' + e.created_at + ' ( '+ e.name+ ' ) '+ (e.is_checked ? ' Проверен ' : '') ;
                    if (eSet.has(e2)) {return};// проверить уникальность
                    eSet.add(e2);
                    return {key: e.id, name: e2}
                });  // пример [[1],['Номер 1__Дата__2019-09-04 13:52:23']]
                return session;

            },

//  событиия treeselect
            selectSes() {
                this.loadCoordinates().then(() => {
                    this.refreshTrail();
                    return console.log('событие selected - выполненно');
                });
            },

            // Редактор трассы
            poligonEdit() {
                let centerMap = this.map.getCenter(); //  из текущей карты вытащить координаты центра
                this.polygon = new ymaps.Polygon([[
                    [centerMap[0]-0.0001*2, centerMap[1]-0.0001*5],
                    [centerMap[0]-0.0001*2, centerMap[1]+0.0001*5],
                    [centerMap[0]+0.0001*2, centerMap[1]+0.0001*5],
                    [centerMap[0]+0.0001*2, centerMap[1]-0.0001*5],
                ]],{},{
                    draggable: true,
                });
                this.map.geoObjects.add(this.polygon);
                this.polygon.editor.startEditing();

                this.map.behaviors.disable('dblClickZoom'); // отключить двойной клик по карте

                // Метод работает только с корректно заданной картой.
                this.polygon.options.setParent(this.map.options);
                this.polygon.geometry.setMap(this.map);

// Проверка, входит ли точка клика в полигон, с заданной выше геометрией.
                this.polygon.events.add('mousemove', (e)  =>  {
                    if (this.dPolyline  && this.dPolyline.length > 0) {
                        let    selPoint = e.get('coords');
                        let    deltaX = selPoint[1] - this.dPolyline[0][1][1][0] ;
                        let    deltaY = selPoint[0] - this.dPolyline[0][1][0][0];

                        let newPoint = this.dPolyline.map(e => {  // массив dPolyline записей полилиний
                            this.myPolyline.geometry.set(e[0],[(e[1][0][0] + deltaY),(e[1][1][0] + deltaX)]); //  set(1, [20, 40])
                            return e;
                        });
                        console.log (this.dPolyline + ' -- ' + e.get('coords')  + ' -- ' + newPoint) ;
                    }}, this);
// Выбрать точки полилинии попавшие в полигон
                this.polygon.events.add('dblclick', ()=>{
                    if (!this.dPolyline) {
                        window.setTimeout( function (){
                            // сформировать массив выбранных полигоном точек(вершин)  полилинии this.dPolyline[1].__ob__.dep.id
                            this.dPolyline = [];
                            this.myPolyline.geometry.getCoordinates().forEach((item, i) => {  // массив dPolyline записей полилиний

                                if (this.polygon.geometry.contains(item)){
                                    this.polygon.options.set('fillColor', '#F39C1255'); // 55-прозрачность "#F39C1255"
                                    //                                return this.dPolyline[i] =  [ [item[0]] , [item[1]] ] ; //  [i , [ [item[0]] , [item[1]]] ]
                                    return this.dPolyline.push([i,[ [item[0]], [item[1]]]])
                                }
                            })
                        }.bind(this),500);
                    }
                    this.dPolyline = null;
                    this.polygon.options.set('fillColor', '#0066ff55'); // 55-прозрачность
                });
            },



// Пример асинхронного запуска запроса fetch(url)/axios c await и полной обработкой Promise
            async    fetchAsyncTodos() {
                const url = 'https://jsonplaceholder.typicode.com/todos/';
                const delay = ms => {
                    return new Promise(r => setTimeout(() => r(),ms )) // r - resolve
                };

                console.log('fetch todo started...')
                try {
                    await delay(2000);
                    const response = await fetch(url);
                    const data = await response.json();
                    console.log('Data:', data);
                } catch (e) {
                    console.error(e);
                } finally {}
            },

//            this.fetchAsyncTodos()

            // Пример карте с редактируемой polyline
            loadCoorPolyline()
            {
                ymaps.ready(init);

                function init() {
                    // Creating the map Новокузнецк.
                    var myMap = new ymaps.Map("map", {
                            center: [53.757171, 87.136716],
                            zoom: 10
                        },
                        {searchControlProvider: 'yandex#search'}
                    );
                    // Creating a polyline.
                    var myPolyline = new ymaps.Polyline([
                        // Specifying the coordinates of the vertices.
                        [53.757171, 87.136716]
                        // [53.7499, 87.1360],
                        // [53.7599, 87.1380],
                        // [53.73, 87.1260],
                        // [53.78, 87.09],
                    ], {}, {
                        /**
                         * Setting geo object options.
                         * Color with transparency.
                         */
                        strokeColor: "#00000088",
                        // The line width.
                        strokeWidth: 4,
                        // The maximum number of vertices in the polyline.
                        editorMaxPoints: 6,
                        // Adding a new item to the context menu that allows deleting the polyline.
                        editorMenuManager: function (items) {
                            items.push({
                                title: "Delete line",
                                onClick: function () {
                                    myMap.geoObjects.remove(myPolyline);
                                }
                            });
                            return items;
                        },
                    });
                    // Adding a line to the map.
                    myMap.geoObjects.add(myPolyline);

                    // Turning on the edit mode.
                    myPolyline.editor.startEditing();
//                    myPolyline.editor.startDrawing();


                    $('input').attr('disabled', false);

                    // Обработка нажатия на любую кнопку.
                    $('input').click(
                        function () {
                            // Отключаем кнопки, чтобы на карту нельзя было
                            // добавить более одного редактируемого объекта (чтобы в них не запутаться).
                            $('input').attr('disabled', true);

                            myPolyline.editor.stopEditing();

                            printGeometry(myPolyline.geometry.getCoordinates());

                        });

                    // Выводит массив координат геообъекта в <div id="geometry">
                    function printGeometry(coords) {
                        $('#geometry').html('Координаты: ' + stringify(coords));

                        function stringify(coords) {
                            var res = '';
                            if ($.isArray(coords)) {
                                res = '[ ';
                                for (var i = 0, l = coords.length; i < l; i++) {
                                    if (i > 0) {
                                        res += ', ';
                                    }
                                    res += stringify(coords[i]);
                                }
                                res += ' ]';
                            } else if (typeof coords == 'number') {
                                res = coords.toPrecision(6);
                            } else if (coords.toString) {
                                res = coords.toString();
                            }

                            return res;
                        }
                    }
                }
            },

            // Рассчитаем расстояние между Москвой и Нью-Йорком
            // Координаты Москвы
            moscow () {

                ymaps.geocode('Москва').then(function (res) {
                    var moscowCoords = res.geoObjects.get(0).geometry.getCoordinates();
                    // Координаты Нью-Йорка
                    ymaps.geocode('Нью-Йорк').then(function (res) {
                        var newYorkCoords = res.geoObjects.get(0).geometry.getCoordinates();
                        // Расстояние
                        // alert(ymaps.formatter.distance(
                        //     ymaps.coordSystem.geo.getDistance(moscowCoords, newYorkCoords) //([55.753215, 37.622504], [40.714599, -74.002791])
                        // ));
                        alert(
                            ymaps.coordSystem.geo.getDistance(moscowCoords, newYorkCoords), //([55.753215, 37.622504], [40.714599, -74.002791])
                            ymaps.coordSystem.geo.getDistance([55.753215, 37.622504], [40.714599, -74.002791]) //([55.753215, 37.622504], [40.714599, -74.002791])
                        );
                    });
                });

            },

            // Пример карты с поиском
            searchNew () {
                // var mousePos = ev.get('coords');
                // var indexNearest =  ev.originalEvent.target.geometry.getClosest(mousePos).closestPointIndex;

                ymaps.ready(init);

                function init() {
                    var myMap = new ymaps.Map("map", {
                            center: [53.757171, 87.136716],
                            zoom: 8
                        }, {
                            searchControlProvider: 'yandex#search'
                        }),
                        objects = ymaps.geoQuery([
                            {
                                type: 'Point',
                                coordinates: [53.757145, 87.136716]
                            },
                            {
                                type: 'Point',
                                coordinates: [53.757155, 87.136716]
                            },
                            {
                                type: 'Point',
                                coordinates: [53.757178, 87.136716]
                            }
                        ]).addToMap(myMap),
                        circle = new ymaps.Circle([[53.757177, 87.136715], 10000], null, {draggable: true});

                    circle.events.add('drag', function () {
                        // Объекты, попадающие в круг, будут становиться красными.
                        var objectsInsideCircle = objects.searchInside(circle);
                        objectsInsideCircle.setOptions('preset', 'islands#redIcon');
                        // Оставшиеся объекты - синими.
                        objects.remove(objectsInsideCircle).setOptions('preset', 'islands#blueIcon');
                    });
                    myMap.geoObjects.add(circle);
                }
            },


            // Пример полигон редактируемый
            poligonNew () {
                var myMap = new ymaps.Map("map", {
                        center: [53.757171, 87.136716],
                        zoom: 13
                    }, {
                        searchControlProvider: 'yandex#search',
                    }),
                    objects = ymaps.geoQuery([
                        {
                            type: 'Point',
                            coordinates: [53.755, 87.136716]
                        },
                        {
                            type: 'Point',
                            coordinates: [53.758, 87.136716]
                        },
                        {
                            type: 'Point',
                            coordinates: [53.759, 87.136716]
                        }
                    ]).addToMap(myMap);

                var polygon = new ymaps.Polygon([[
                    [53.756, 87.130],
                    [53.759, 87.130],
                    [53.759, 87.135],
                    [53.756, 87.135],
                ]],{},{
                    draggable: true,
                });
                myMap.geoObjects.add(polygon);

                this.myPolyline22 = new ymaps.Polyline([
                    // Specifying the coordinates of the vertices.
                    [53.757171, 87.136716],
                    [53.7499, 87.1360],
                    [53.7599, 87.1380],
                    [53.73, 87.1260],
                    [53.78, 87.09],
                ], {}, {
                    strokeColor: "#00000088",
                    // The line width.
                    strokeWidth: 4,
                    // The maximum number of vertices in the polyline.
                    editorMaxPoints: 255,
                    });

                myMap.geoObjects.add(this.myPolyline22);
               myMap.behaviors.disable('dblClickZoom'); // отключить двойной клик по карте

                // Метод работает только с корректно заданной картой.
                polygon.options.setParent(myMap.options);
                polygon.geometry.setMap(myMap);

// Проверка, входит ли точка клика в полигон, с заданной выше геометрией.
                polygon.events.add('mousemove', (e)  =>  {
                    if (this.dPolyline) {
                    let    selPoint = e.get('coords');
                    let    deltaX = selPoint[1] - this.dPolyline[0][1][1][0] ; //
                    let    deltaY = selPoint[0] - this.dPolyline[0][1][0][0];
                    let newPoint = this.dPolyline.map(e => {  // массив dPolyline записей полилиний
                        this.myPolyline22.geometry.set(e[0],[(e[1][0][0] + deltaY),(e[1][1][0] + deltaX)]); //  set(1, [20, 40])
                        return e;
                    });
                    console.log (this.dPolyline + ' -- ' + e.get('coords')  + ' -- ' + newPoint) ;
                }}, this);

                // В режиме добавления новых вершин меняем цвет обводки многоугольника.
                var stateMonitor = new ymaps.Monitor(polygon.editor.state);
                stateMonitor.add("drawing", function(newValue) {
                    polygon.options.set("strokeColor", newValue ? '#FF0000' : '#0000FF');
                    polygon.options.set("strokeWidth", newValue ? 3 : 1);
                });
                polygon.editor.startDrawing();
                polygon.vueContext = this;
                polygon.events.add('dblclick', ()=>{
                    if (!this.dPolyline) {
                    window.setTimeout( function (){
                        var objectsInsidePolygon = objects.searchInside(polygon);
                        objectsInsidePolygon.setOptions('preset', 'islands#redIcon');
                        // Оставшиеся объекты - синими.   // myMap.geoObjects.get(4).geometry.getCoordinates()
                        objects.remove(objectsInsidePolygon).setOptions('preset', 'islands#blueIcon');

                        // сформировать массив выбранных полигоном точек(вершин)  полилинии this.dPolyline[1].__ob__.dep.id
                        this.dPolyline = [];
                            this.myPolyline22.geometry.getCoordinates().forEach((item, i) => {  // массив dPolyline записей полилиний

                            if (polygon.geometry.contains(item)){
                                polygon.options.set('fillColor', '#F39C1255'); // 55-прозрачность "#F39C1255"
                                //                                return this.dPolyline[i] =  [ [item[0]] , [item[1]] ] ; //  [i , [ [item[0]] , [item[1]]] ]
                                return this.dPolyline.push([i,[ [item[0]], [item[1]]]])
                            }
                        })

                    }.bind(polygon.vueContext),500);
                }
                    this.dPolyline = null;
                    polygon.options.set('fillColor', '#0066ff55'); // 55-прозрачность

                });
            }
        }
    }


</script>

<style scoped>
    .active {
        border: 1px solid red;
    }
    .vue-treeselect   {
        color: black;
    }
</style>

