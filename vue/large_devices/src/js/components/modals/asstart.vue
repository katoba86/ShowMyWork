<template>
    <div class="fbb-modal-content">
      <button class="close" @click="$emit('close')">X</button>
        <div class="modal-body p-md-0">
            <div class="row no-gutters">
                <div class="col-md-4 col-xl-5 d-none d-md-flex bg-img pl-xl-4 rounded-left " >

                    <div ref="dtc" :id="cid" class="restDtc" data-load="SIZE_RECT_BIG"></div>

                </div>

                <div class="col-md-8 col-xl-6 mx-auto" style="min-height:400px;">
                    <div  v-if="!selected">
                        <h4 class="fw-200 ">Wohin möchten Sie?</h4>
                        <p>Ab hier haben Sie 2 Möglichkeiten:</p>

                        <div class="card clickable card-shadow" @click="selected=true;isBus=true;">
                            <div class="card-body">
                                <div class="media">
                                    <img class="align-self-start mr-3 w-100px" src="/desktop/images/bus-icon1.png" alt="Mit dem Bus">
                                    <div class="media-body">
                                        <h6 class="mt-0">Mit dem Bus in die Nähe</h6>
                                        <p>Mit dem öffentlichen Nahverkehr (Bus, Straßenbahn, etc.) zu Haltestellen in der Nähe</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="divider mt-4 mb-4">oder</h6>
                        <div class="card clickable card-shadow mb-6" @click="selected=true;isTrain=true;">
                            <div class="card-body">
                                <div class="media">
                                    <img class="align-self-start mr-3 w-100px" src="/desktop/images/train_icon.png" alt="Mit der Bahn">
                                    <div class="media-body">
                                        <h6 class="mt-0">Mit Bahn & Bus in andere Städte</h6>
                                        <p>Ab hier in andere Städte reisen. Hierfür ermitteln wir den besten Preis.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Selected Train -->
                    <div v-if="isTrain">

                        <h4 class="fw-200 ">Okay, in eine andere Stadt.</h4>
                        <p>In welche Stadt möchten Sie reisen?</p>
                      <Instant @click="clear()" :config="instantConfigTrain"></Instant>

                        <div v-if="trainConfig.noData" class="mt-2 mb-2">
                            <div  v-bind:key="'loc2'+index" class="list-group list-group-flush small-2" v-for="(location,index) in trainConfig.near">
                                <a class="list-group-item py-1 clickable bt-0" @click="quickSelect(index,'train')">{{ location.name}}</a>
                            </div>
                        </div>

                    </div>
                    <!-- Selected Bus -->
                    <div v-if="isBus">
                        <h4 class="fw-200 ">Okay, nahegelegene Haltestellen.</h4>
                        <p>Wählen Sie Ihre Haltestelle</p>

                      <Instant @click="clear()" :config="instantConfigBus"></Instant>

                        <div v-if="busConfig.noData" class="mt-2 mb-2" style="max-height:400px;overflow-y:scroll;">
                            <div v-bind:key="'loc'+index" class="list-group list-group-flush small-2" v-for="(location,index) in busConfig.near">
                                <a class="list-group-item py-1 clickable bt-0" @click="quickSelect(index,'hst')">{{ location.name}}</a>
                            </div>
                        </div>


                    </div>

                </div>

            </div>
        </div>
    </div>
</template>
<script type="text/babel">

    import Dtc from "@/js/components/mixins/dtc";
    import {
      CLEAR_SUGGESTIONS,
      RETRIEVE_SUGGESTIONS_HST, RETRIEVE_SUGGESTIONS_MIXED
    } from "@/js/state/modules/common.types";
    import Instant from "@/js/components/elements/instant";
    import {hstById, searchCitiesNear, searchHstNear} from "@/js/api/remote";
    import {GET_HST_DESTINATION, SET_HST_DESTINATION} from "@/js/state/modules/hst.types";
    import {mapActions, mapGetters} from "vuex";
    import {FBBConverter} from "@/js/outside/FBBConverter";
    import Redir from "@/js/components/modals/redir";

    export default {
      name: 'asstart',
      components: {Instant},
      computed:{
        ...mapGetters(
            'hst',{
              getDestination:GET_HST_DESTINATION
            }
        )
      },
      mixins:[Dtc],
        props: {
            id: {
                type: String,
                required: true,
            }
        },
        watch:{
          getDestination(v){
            if(this.isBus){
              this.setSelectBus(v);
            }else{
              this.setSelectBus(v);
            }
          }
        },
        created(){
          // eslint-disable-next-line no-unused-vars
          this.cid = [...Array(5)].map(i=>(~~(Math.random()*36)).toString(36)).join('');
          if(typeof window.user_near !== 'undefined'){
            this.usernear = window.user_near;
          }
          this.getFullHst();
        },
        mounted() {
          this.clearSuggests();
          this.notify();
        },



        methods: {
          ...mapActions('common',{
            clearSuggests:CLEAR_SUGGESTIONS
          }),
          quickSelect(index,type){
            if(type === 'train') {
              let o = this.trainConfig.near[index];
              this.setSelectTrain({
                identifer: o.id,
                name: o.name,
                type: 'city'
              });
            }else{
              let o = this.busConfig.near[index];
              this.setSelectBus(o);
            }

          },
          setSelectTrain(object){
            this.redirect(FBBConverter.getDBLink(this.hst.name,object.name))
            this.$emit('close');
          },

          setSelectBus(object){
              this.redirect(FBBConverter.getDBLink(this.hst.name,object.name))
              this.$emit('close');
          },
          redirect(url){
            this.$modal.show(Redir, {
              url: url,
              timeout: 5
            }, {
              maxWidth: 800,
              width: 800,
              height: 300
            });
          },
            clear(){
              this.busConfig.near = [];
            },

            getFullHst(){
             hstById(this.id).then((data)=>{
                if(data.status===200){
                  this.hst = data.data;
                  this.hst.type ='hst';
                  this.getHstNear();
                  this.getCitiesNear(data.data.relCity);
                }
              });
             },

          getCitiesNear(rel){
            searchCitiesNear(rel,null,null).then((data)=>{
              if(data.status===200){
                this.trainConfig.near = data.data;
              }
            });
          },

            getHstNear(){
                searchHstNear(this.id,null,null).then((data)=>{
                  if(data.status===200){
                    this.busConfig.near = data.data;
                  }
              });
            },


        },
        data() {
            return {


                instantConfigBus:{
                      action:["common",RETRIEVE_SUGGESTIONS_HST],
                      placeholder:'z.B. Marktplatz',
                      dispatch:["hst",SET_HST_DESTINATION]
                },
              instantConfigTrain:{
                action:["common",RETRIEVE_SUGGESTIONS_MIXED],
                placeholder:'z.B. München',
                dispatch:["hst",SET_HST_DESTINATION]
              },

                cid:null,
                hst:null,
                al:false,
                isLoading: true,
                selected:false,
                isTrain:false,
                isBus:false,
                userNear:[],
                trainConfig:{
                    value:"",
                    suggestionAttribute:'name',
                    noData:true,
                },
                busConfig:{
                    near:[],
                    noData:true,
                    value:"",
                    suggestionAttribute:'name',
                },
                data: [],
            }
        }
    }
</script>
