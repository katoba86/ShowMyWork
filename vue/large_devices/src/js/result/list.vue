<template>
    <div class="dtc__results--table split-panel--left">



        <div class="dtc__results--more-results previous default enabled "><span>Früher</span>
        </div>

        <!---->

        <div v-for="(journey,index) in sortedJourneys" >

            <div class="dtc__results--header" v-if="changedDate(journey,index)">
                <div class="first">{{ journey.time_departure.date}}</div>
                <div class="third">Reisedauer</div>
                <div class="fourth">Preis</div>
            </div>

            <div :id="'journey'+index" class="dtc__results--line-container" :class="isActive(index)" @click="showDetails(index)">
                <div class="dtc__results--line  ">
                    <div class="first">
                        <div>
                            <span class="time">{{journey.time_departure.time}} <span class="unicode">➜</span> {{journey.time_arrive.time}}</span>
                            <div class="time_line time_line--animated "><span
                                    class="time_line__label"> Mit {{getProducts(journey)}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="second">

                        <!---->

                    </div>

                    <div class="third ">
                        <div class="dtc__results-folder-container ">
                            <div class="dtc__results-folder has-price ">
                                <div class="dtc__results-folder-wrapper">
                                    <div class="dtc__results-folder-label">
                                        <span>{{durationD(journey.duration.humanSplit)}}</span>
                                        <!---->  </div>
                                </div>
                            </div>
                            <!----><!---->      </div>
                    </div>

                    <div class="fourth ">
                        <div class="dtc__results-folder-container ">
                            <div class="dtc__results-folder has-price ">
                                <div class="dtc__results-folder-wrapper">
                                    <div class="dtc__results-folder-label">
                                        <span>{{fareD(journey.fare)}}</span>
                                    </div>
                                </div>
                            </div>
                            <!----><!---->      </div>
                    </div>
                </div>
            </div>
        </div>



        <!----><!---->
        <div class="dtc__results--more-results next default enabled "><span>Später</span>
        </div>
    </div>
</template>
<script type="text/babel">
    import {FBBConverter} from "../outside/FBBConverter";

    export default {
        name: 'list',
        mounted(){


            this.$eventBus.$on('changeSort',(data)=>{
              this.toggleSort(data);
                this.$forceUpdate();
            });

        },
        props: {
            'journeys': {

                required: false,
                default: {}
            }
        },
        data: function () {
            return {
                sort:'fare',
                cindex:0,
                currentJourneyDate:null
            }
        },
        computed:{

            cSort(){

                return this.sort;
            },

            sortedJourneys: function() {

                return this.journeys.sort(this.sortProperty());
            }
        },
        methods:{

            isActive(index){
              return (index === this.cindex)?'active':'';
            },

            changedDate(journey,index){

                if(index === 0 || journey.time_departure.date !==this.currentJourneyDate){
                    this.currentJourneyDate = journey.time_departure.date;
                    return true;
                }
                return false;


            },
            getProducts(journey) {
                return FBBConverter.getProducts(journey);
            },

            durationD(split) {
                return (split.hours === 0) ? `${split.minutes} min.` : `${split.hours} Std.`;
            },
            fareD(fare) {
                return FBBConverter.getFare(fare,false)
            },
            sortByDuration() {
                return (a, b) => {
                    let aduration = a.duration.inMinutes;
                    let bduration = b.duration.inMinutes;
                    if (aduration <= 0) {
                        aduration = Number.MAX_SAFE_INTEGER;
                    }
                    if (bduration <= 0) {
                        bduration = Number.MAX_SAFE_INTEGER;
                    }

                    if (aduration < bduration)
                        return -1;
                    if (aduration > bduration)
                        return 1;
                    return 0;
                }
            },

            sortByDeparture() {

                return (a, b) => {

                    let atime = a.time_departure.timestamp;
                    let btime = b.time_departure.timestamp;



                    if (atime <= 0) {
                        atime = Number.MAX_SAFE_INTEGER;
                    }
                    if (btime <= 0) {
                        btime = Number.MAX_SAFE_INTEGER;
                    }

                    if (atime < btime)
                        return -1;
                    if (atime > btime)
                        return 1;
                    return 0;
                }
            },

            sortByFare() {
                return (a, b) => {

                    let afare = (a!==null && a.fare !==null && typeof a.fare.cent !=='undefined')?a.fare.cent:-1;
                    let bfare = (b!==null && b.fare !==null && typeof b.fare.cent !=='undefined')?b.fare.cent:-1;
                    if (afare <= 0) {
                        afare = Number.MAX_SAFE_INTEGER;
                    }
                    if (bfare <= 0) {
                        bfare = Number.MAX_SAFE_INTEGER;
                    }

                    if (afare < bfare)
                        return -1;
                    if (afare > bfare)
                        return 1;
                    return 0;
                }
            },

            sortProperty() {

                if (this.cSort === 'fare') {
                    return this.sortByFare();
                }
                if (this.cSort === 'duration') {
                    return this.sortByDuration();
                }
                if (this.cSort === 'departure') {
                    return this.sortByDeparture();
                }
            },


            toggleSort(what) {
                //this.$store.dispatch('setSort', what);
                this.sort = what;
                this.$forceUpdate();
            },



            showDetails(index){
                this.cindex = index;
                this.$eventBus.$emit('send-data', {index:index});
            }
        }

    }
</script>

