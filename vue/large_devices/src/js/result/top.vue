<template>
    <div class="dtc__results--filters-box">
        <div class="dtc__results--filters" v-if="(input!==null)">
            <div class="dtc__results--filters-flexibility "><label
                    class="results__flexibility-label">
                <input v-model="vsort"  v-bind:value="'fare'"  type="radio" >

                <div class="content">
                    <div>Günstig</div>
                    <small>Erhalten Sie den besten Preis</small>
                </div>
            </label>
                <label class="results__flexibility-label">
                    <input type="radio" v-model="vsort"  v-bind:value="'duration'" />
                    <div class="content">
                        <div>Am schnellsten</div>
                        <small>Am schnellsten von Stadt zu Stadt</small>
                    </div>
                </label>
                <label class="results__flexibility-label">
                    <input  type="radio" v-model="vsort"  v-bind:value="'departure'"  />

                    <div class="content">
                        <div>Abfahrt</div>
                        <small>Nach Abfahrt sortieren</small>
                    </div>
                </label>
            </div>


            <div class="results__summary">
                <div class="left">
                    <div class="results__summary_row">
                        <div class="results__summary_label">Von</div>
                        <div class="results__summary_value">{{ this.input.origin.name}}</div>
                    </div>
                    <div class="results__summary_row">
                        <div class="results__summary_label">
                            Nach
                        </div>
                        <div class="results__summary_value">
                            {{ this.input.destination.name}}
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="results__summary_row">
                        <div class="results__summary_label">
                            {{ getDateStr() }}
                        </div>
                        <div class="results__summary_value">
                            {{ getDate() }}
                        </div>
                    </div>
                    <div class="results__summary_row">
                        <div class="results__summary_label">
                            Reisende
                        </div>
                        <div class="results__summary_value">
                            {{ getPassengers()}}
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
</template>
<script type="text/babel">

    import {FBBConverter} from "../outside/FBBConverter";

    export default {
        name: 'top',

        data: function () {
            return {
               vsort:'fare'
            }
        },
        watch:{
          vsort(v){

              this.$eventBus.$emit('changeSort', v);
          }
        },
        methods:{
            getPassengers(){
                return FBBConverter.getTravelers(this.input.travelers);
            },
            getDate(){
              if(this.input.way){

                  return this.input.date.toFormat('dd.LL.yyyy HH:mm:00');

              }else{
                  return this.input.date.toFormat('dd.LL.yyyy HH:mm:00');
              }
            },
            convertDate(){

            },
          getDateStr(){
              return FBBConverter.getDirectionString();
          }
        },
        props: {
            'input': {

                required: false,
                default: null
            }
        }
    }
</script>
