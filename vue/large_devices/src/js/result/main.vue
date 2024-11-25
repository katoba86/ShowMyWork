<style type="text/css" scoped>
    .sk-folding-cube {
        margin: 20px auto;
        width: 40px;
        height: 40px;
        position: relative;
        -webkit-transform: rotateZ(45deg);
        transform: rotateZ(45deg);
    }

    .sk-folding-cube .sk-cube {
        float: left;
        width: 50%;
        height: 50%;
        position: relative;
        -webkit-transform: scale(1.1);
        -ms-transform: scale(1.1);
        transform: scale(1.1);
    }
    .sk-folding-cube .sk-cube:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #789934;
        -webkit-animation: sk-foldCubeAngle 2.4s infinite linear both;
        animation: sk-foldCubeAngle 2.4s infinite linear both;
        -webkit-transform-origin: 100% 100%;
        -ms-transform-origin: 100% 100%;
        transform-origin: 100% 100%;
    }
    .sk-folding-cube .sk-cube2 {
        -webkit-transform: scale(1.1) rotateZ(90deg);
        transform: scale(1.1) rotateZ(90deg);
    }
    .sk-folding-cube .sk-cube3 {
        -webkit-transform: scale(1.1) rotateZ(180deg);
        transform: scale(1.1) rotateZ(180deg);
    }
    .sk-folding-cube .sk-cube4 {
        -webkit-transform: scale(1.1) rotateZ(270deg);
        transform: scale(1.1) rotateZ(270deg);
    }
    .sk-folding-cube .sk-cube2:before {
        -webkit-animation-delay: 0.3s;
        animation-delay: 0.3s;
    }
    .sk-folding-cube .sk-cube3:before {
        -webkit-animation-delay: 0.6s;
        animation-delay: 0.6s;
    }
    .sk-folding-cube .sk-cube4:before {
        -webkit-animation-delay: 0.9s;
        animation-delay: 0.9s;
    }
    @-webkit-keyframes sk-foldCubeAngle {
        0%, 10% {
            -webkit-transform: perspective(140px) rotateX(-180deg);
            transform: perspective(140px) rotateX(-180deg);
            opacity: 0;
        } 25%, 75% {
              -webkit-transform: perspective(140px) rotateX(0deg);
              transform: perspective(140px) rotateX(0deg);
              opacity: 1;
          } 90%, 100% {
                -webkit-transform: perspective(140px) rotateY(180deg);
                transform: perspective(140px) rotateY(180deg);
                opacity: 0;
            }
    }

    @keyframes sk-foldCubeAngle {
        0%, 10% {
            -webkit-transform: perspective(140px) rotateX(-180deg);
            transform: perspective(140px) rotateX(-180deg);

            opacity: 0;
        } 25%, 75% {
              -webkit-transform: perspective(140px) rotateX(0deg);
              transform: perspective(140px) rotateX(0deg);
              opacity: 1;
          } 90%, 100% {
                -webkit-transform: perspective(140px) rotateY(180deg);
                transform: perspective(140px) rotateY(180deg);
                opacity: 0;

            }
    }
    .slide-fade-enter-active {
        transition: all .2s ease;
    }
    .slide-fade-leave-active {
        transition: all .2s cubic-bezier(.58,.47,.84,.5);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform:translateZ(800px) translateX(-500px);

        opacity: 0;
    }
</style>
<style lang="scss">
    @import '../../scss/app';
</style>
<template>
    <section class="dtc-result-wrapper ">
        <div class="dtc-result container  " style="position: relative">

        <div class="container">
            <transition name="slide-fade" appear mode="in-out">



                <div class="container " style="height:60vh"  v-if="(journeys.length===0 && noData)" >
                    <div class="row h-100 justify-content-center align-items-center">
                        <div class="card card-shadow">
                            <div class="card-body">
                                Leider konnten wir diese Strecken zur Zeit nicht berechnen. Wir leiten zu unserem Partner weiter<br/>
                                <div class="text-center">
                                  <a class="btn btn-danger" target="_blank" :href="getNoDataLink()">Weiter...</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            <div class="container " style="height:60vh"  v-if="(journeys.length===0 && !noData)" >
                <div class="row h-100 justify-content-center align-items-center">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <div class="sk-folding-cube">
                                <div class="sk-cube1 sk-cube"></div>
                                <div class="sk-cube2 sk-cube"></div>
                                <div class="sk-cube4 sk-cube"></div>
                                <div class="sk-cube3 sk-cube"></div>
                            </div><br/>
                            <div class="text-center">
                            <h4>Lade...</h4>
                            <h6>Bitte warten Sie einen Augenblick...</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row"  v-if="(journeys.length>0)" >
                <div class="col-6">

                    <top  v-if="(journeys.length>0)" v-bind:input="input"></top>
                    <list  v-if="(journeys.length>0)" v-bind:journeys="journeys"></list>

                </div>

                    <detail  v-if="(journeys.length>0)" v-bind:journey="currentJourney"></detail>

            </div>
            </transition>
        </div>

        </div>











    </section>
</template>

<script type="text/babel">
    import Top from "./top";
    import List from "./list";
    import Detail from "./detail";
    import { DateTime as LuxonDateTime } from 'luxon';
    import {FBBConverter} from "../outside/FBBConverter";
    import {calc} from "@/js/api/remote";

    export default {
        name: 'app',
        mounted(){
          // eslint-disable-next-line no-undef
           this.input = input;
           this.input.date = this.convertDate(this.input.date);
            setTimeout(()=>{
                this.fetch();
            },8);






            this.$eventBus.$on('send-data', (data) => {
                this.currentJourney = this.journeys[parseInt(data.index)];

            });

        },
        data: function () {
            return {
                noData:false,
                input:null,
                journeys:[],
                currentJourney:null
            }
        },
        methods:{


            getNoDataLink(){
                return FBBConverter.getDBLink(this.input.origin.name,this.input.destination.name);
            },

            convertDate(date){
                if(date === null){
                    return LuxonDateTime.local();
                }
                else if(typeof date === 'string'){
                    date = LuxonDateTime.fromISO(date);
                }
                let cur = LuxonDateTime.local();

                if((date.ts*1)<(cur.ts*1)){

                    date = cur;
                    return cur;
                }

                return date;
            },




            fetch(hash = null) {

                this.loaded = true;

                let from =this.input.origin;
                let to = this.input.destination;


              // eslint-disable-next-line no-unused-vars
                let d = this.input.date;
              // eslint-disable-next-line no-unused-vars
                let s = this.input.date;

                calc(
                    this.input,
                    "db",
                    hash
                )
                    .then((data) => {
                        this.next = btoa(data.data.next);
                        this.prev = btoa(data.data.prev);
                       this.journeys = [...this.journeys, ...data.data.journeys];



                        //this.$store.dispatch('setJourneys', this.journeys);
                        if(this.currentJourney === null){
                            this.currentJourney = this.journeys[0];
                        }
                        if (hash !== null || (from.type === "S" && to.type === "S")) {
                            return true;
                        }

                        calc(
                          this.input,
                            "flix"
                        )
                            .then((data) => {
                                this.journeys = [...this.journeys, ...data.data.journeys];
                               if(this.journeys.length === 0){
                                   this.noData = true;
                               }
                               // this.$store.dispatch('setJourneys', this.journeys);
                            });


                    });
            }

        },

        components: {
            top: Top,
            list: List,
            detail: Detail,
        }
    }
</script>
