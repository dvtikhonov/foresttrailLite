<template>
<div>
    <div class="map-wrapper" ref="loadingContainer">
        <yandex-map
            :settings="common.yandexMapSettings"
            :options="{
                    suppressMapOpenBlock: true,
                    yandexMapDisablePoiInteractivity: true,
                    maxZoom: 18
                }"
            :coords="centerMap"
            zoom="18"
            @map-was-initialized="initYmap"
            :behaviors="['drag','multiTouch','dblClickZoom','scrollZoom']"
            :controls="['typeSelector', 'searchControl', 'fullscreenControl']"
        >
        </yandex-map>
    </div>
</div>
</template>

<script>
    import { yandexMap, ymapMarker } from 'vue-yandex-maps';

    export default {
        name: "view-tracks",
        props: ['tracks'],
        components: {
            yandexMap
        },

        mounted() {
            this.loader = this.$loading.show({
                container: this.$refs.loadingContainer
            });
        },

        data() {
            return {
                ymaps: null,
                map: null,
                mapWasInitialized: false,
                centerMap: [53.757171, 87.136716],
                mapPaths: null,
                loader: null
            }
        },

        computed: {
            common() {
                return this.$store.state;
            }
        },

        watch: {
            tracks(val){
                this.showTracks(val);
            }
        },

        methods: {
            initYmap(a) {
                this.map = a;
                this.ymaps = ymaps;
                this.loader.hide();

                if (!this.mapWasInitialized) {
                    this.mapWasInitialized = true;
                }

                this.showTracks();
            },

            clearPaths(){
                if( ! this.mapPaths ){ return false; }
                this.map.geoObjects.remove(this.mapPaths);
                this.mapPaths = null;

                return true;
            },

            showTracks() {
                this.clearPaths();

                let tracks = Object.assign({}, this.tracks);

                let collection = (new this.ymaps.GeoObjectCollection({},{}));
                for (let track in tracks) {
                    collection = this.initTrack(tracks[track], collection);
                }
                this.mapPaths = collection;
                this.map.geoObjects
                    .add(collection);

                this.map.setBounds(collection.getBounds());
            },

            initTrack(track, collection){

                collection.add( new this.ymaps.Polyline(this.tracksAsCoordinates(track), {
                }, {
                    balloonCloseButton: false,
                    strokeColor:        "#475fff",
                    strokeWidth:        6,
                    strokeOpacity:      0.8
                }) );

                return collection;
            },

            tracksAsCoordinates(track){
                let coords = [];
                track.forEach((coord) => {
                    coords.push([coord.lat, coord.lon, coord.created_at, coord.accuracy]);
                });
                return coords;
            },
        }
    }
</script>

<style lang="scss" scoped>

div ::v-deep .map-wrapper {
    @import "~@styles/partials/view_tracks.scss";
}

</style>