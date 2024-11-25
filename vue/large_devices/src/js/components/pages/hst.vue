<template>
  <div>

    <navbar></navbar>
    <div class="varioForm">
        <form>
          <div class="row">
            <div class="col-12 mt-2">
              <modal-input :valid="check('origin')" :value="getHstOrigin" icon="#varioHome"  @click.native="showModalOrigin" />
            </div>
            <div class="col-12">
              <modal-input :disabled="isDisabled()" :valid="check('destination')"  :value="getHst" icon="#varioPin"  @click.native="showModalHst" />
            </div>

            <div class="col-12 text-right ">
              <button class="btn btn-cta finish" id="home_finish"  type="button"
                      @click="finish()">Jetzt suchen
              </button>
            </div>
          </div>
      </form>
    </div>

  </div>
</template>
<style type="text/css" scoped>
  #home_finish{
    background-color:#de6f20;color:white;padding: 10px 23px 10px;
  }
</style>

<script type="text/babel">



import Select from "@/js/components/modals/select";

import ModalInput from "@/js/components/elements/modalInput";
import {mapActions, mapGetters} from "vuex";
import {getModalHeadline} from "@/js/Text";
import {RETRIEVE_SUGGESTIONS_HST, RETRIEVE_SUGGESTIONS_MIXED} from "@/js/state/modules/common.types";
import Navbar from "@/js/components/elements/navbar";
import {mixin as clickaway} from "vue-clickaway";
import {
  GET_ENTIRE_STATE,
  GET_HST,
  GET_HST_CITY,
  GET_HST_CITY_OBJECT, HST_GET_URL,
  HST_IS_VALID,
  SET_HST,
  SET_HST_CITY
} from "@/js/state/modules/hst.types";
import Redir from "@/js/components/modals/redir";
import {getHstLink} from "@/js/api/local";

const NAMESPACE = 'hst';

export default {
  name: 'hst',
  components: {Navbar, ModalInput},
  mixins: [clickaway],
  data(){
    return {
      submitted:false
    }
  },
  computed: {



    ...mapGetters(NAMESPACE, {
        isValid:HST_IS_VALID,
        getHstOrigin:GET_HST_CITY,
        getHstOriginObject:GET_HST_CITY_OBJECT,
        getHst:GET_HST,
        all:GET_ENTIRE_STATE
      }
    ),

  },
  methods:{
    ...mapActions(NAMESPACE,{
      setHst:SET_HST,
      setHstCity:SET_HST_CITY
    }),
    isDisabled(){

      return (this.getHstOriginObject === null);
    },
    check(which){
      if(!this.submitted){
        return true;
      }
      return this.isValid[which];
    },
    showModalOrigin(){
        this.showModal(
            getModalHeadline(NAMESPACE,GET_HST_CITY),
            {
                action:["common",RETRIEVE_SUGGESTIONS_MIXED],
                placeholder:'z.B. Berlin, Dortmund oder Hamburg Hbf',
                dispatch:[NAMESPACE,SET_HST_CITY]
            },
            true
        );
    },
    showModalHst(){
      if(this.isDisabled()){
        return;
      }
      this.showModal(
          getModalHeadline(NAMESPACE,GET_HST),
          {
            action:["common",RETRIEVE_SUGGESTIONS_HST],
            placeholder:'z.B. Berlin, Dortmund oder Hamburg Hbf',
            dispatch:[NAMESPACE,SET_HST]
          },
          true
      );
    },
    showModal(headline,instantConfig,showNear){
      this.$modal.show(Select, {
        headline:headline,
        instantConfig:instantConfig,
        showNear
      }, {
        maxWidth: 920,
        width: 920,
        height: 'auto'
      });
    },






    finish(){
      this.submitted = true;
      if(!Object.values(this.isValid).every(item => item === true)){
          return;
      }

      //this.$ga.event('topForm', 'info', 'valid');
      //let url = '/rest/hst/link?hst='+this.form.hst.id+'&city='+this.form.origin.id;

      this.$modal.show(Redir, {
        fetch:getHstLink,
        params:this.all,
        timeout:8
      },{
        maxWidth:800,
        width:800,
        height:300
      });



    }
  }
}
</script>
