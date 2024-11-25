<template>
    <div class="col-6" >
    <div   class="mt-0 selected-folder split-panel--right " v-if="(journey!==null)">
        <div class="selected-folder__arrow  d-none split-panel__arrow" v-bind:style="{ top: getArrowTop}"  ></div>
        <div class="selected-folder__content">







            <div  v-for="(trip,tindex) in journey.trips">

                <div class="selected-folder__progress-box" :class="(tindex!==0)?'mt-4':''">
                    <div class="selected-folder__progress-box__row" v-if="(journey.trips.length===1)">
                        <div class="selected-folder__price-summary ">
                            <div>
                                {{ getDirection()}}
                            </div>
                            <div class="price">{{ fareD(journey.fare)}}</div>
                            <span>{{ getPassengers()}}</span>
                        </div>
                        <div class="selected-folder__button progress-button ">
                            <a class="button progress-button__button" tabindex="1" type="button" target="_blank" :href="getUrl(trip)">
                                <div class="progress-button__select-journey ">
                                    <div class="text">
                                        <p class="action">Weiter</p>
                                        <p>
                                            für <strong>Details</strong>
                                        </p>
                                    </div>
                                    <div class="chevron">
                                        <img src="/desktop/images/chevron.svg">
                                    </div>
                                </div>

                            </a>


                        </div>
                    </div>


                    <div class="selected-folder__progress-box__row" v-if="(journey.trips.length!==1)">
                        <div class="selected-folder__price-summary ">
                            <div>
                                {{ getDirection()}}
                            </div>
                            <div class="price">{{ fareD(trip.fare)}}</div>
                            <span>{{ getPassengers()}}</span>
                        </div>
                        <div class="selected-folder__button progress-button ">
                            <a class="button progress-button__button" tabindex="1" type="button" target="_blank"  :href="getUrl(trip)">
                                <div class="progress-button__select-journey ">
                                    <div class="text">
                                        <p class="action">Ticket {{ (tindex+1) }}</p>
                                        <p>
                                            mit <strong>{{ (trip.provider==='DB')?'der Bahn':trip.provider }}</strong>
                                        </p>
                                    </div>
                                    <div class="chevron">
                                        <img src="/desktop/images/chevron.svg">
                                    </div>
                                </div>

                            </a>


                        </div>
                    </div>


                </div>


            <h1 class="selected-folder__title">
                Strecke
                <span class="selected-folder__title-note"></span>
                <!---->  </h1>

            <a class="d-block segments-section segments--search " target="_blank" :href="getUrl(trip)" v-for="(step,sindex) in trip.steps">
                <div class="detail__body" v-if="(step.product.prodCtx!=='WALK')">
                    <div class="detail__od" >

                        <div class="detail__origin">
                            <label class="detail__departure-time">{{ step.departure.time}}</label>
                            <label class="detail__departure-station"> {{ step.origin.name}}</label>
                        </div>

                        <div class="detail__destination">
                            <label class="detail__arrival-time ">
                               {{ step.arrive.time}}
                            </label>
                            <label class="detail__arrival-station">
                                {{ step.destination.name}}
                            </label>
                        </div>
                    </div>

                    <label class="detail__carrier">
                        <div class="train-logo train-logo--train train-logo--sncf"></div>
                        <div class="train-logo__train">{{getProductName(step.product)}}</div>
                    </label>

                    <div class="detail__travel-class ">
                        <label class="detail__travel-class-label">
                            <!---->
                            <span>{{ step.duration.human}}</span>
                        </label>
                    </div>

                </div>

                <div class="detail__connection" v-if="(step.waiting!==null)">
                    <label class="detail__connection-time">
                        {{  step.waiting.inMinutes}} Minuten Zeit zum Umsteigen
                    </label>
                </div>
               </a>
            </div>

        </div>
    </div>
    </div>
</template>
<script type="text/babel">

    import {FBBConverter} from "../outside/FBBConverter";

    export default {
        name: 'app',
        computed:{
            getArrowTop(){
                return (300+(this.arrowTop)*70)+"px";
            }
        },
        props: {
            'journey': {

                required: false,
                default: null
            }
        },
        data: function () {
            return {
                arrowTop:0
            }
        },
        methods:{


            getUrl(trip){
                if(trip.provider==="DB"){
                    return FBBConverter.getDBLink(trip.origin.name,trip.destination.name,trip.departure.date,trip.departure.time);
                }else{
                    return FBBConverter.getFlixLink(trip.origin,trip.destination,trip.departure.date);
                }
            },

            getProductName(product){
                if(product.hasOwnProperty('flixInfos') && product.flixInfos!==null){
                    return "Flixbus";
                }
                let name = (typeof product.name !=='undefined')?product.name:null;
                if (typeof product === 'string' || product instanceof String){
                    name = product;
                }
                if(name === null){return '';}
                return name;
            },

            getDirection(){
               return FBBConverter.getDirectionString();
            },


            getPassengers(){
                return FBBConverter.getTravelers(input.travelers);
            },

            fareD(fare) {
                return FBBConverter.getFare(fare);
            },
        },


        created(){

            this.$eventBus.$on('send-data', (data) => {
                this.arrowTop = data.index;

            });


        }

    }
</script>
