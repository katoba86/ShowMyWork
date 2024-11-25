<template>
  <div>

    <navbar></navbar>
    <div class="varioForm">
        <form>
          <div class="row">
            <div class="col-12 mt-2">
              <modal-input :valid="check('origin')" :value="getOrigin" icon="#varioHome"  @click.native="showModalOrigin" />
            </div>
            <div class="col-12">
              <modal-input :valid="check('destination')"  :value="getDestination" icon="#varioPin"  @click.native="showModalDestination" />
            </div>
            <div class="col-12">
              <date-input ref="dinput"></date-input>
            </div>
            <div class="col-6">
              <passenger/>
            </div>
            <div class="col-6 text-right ">
              <button class="btn btn-cta finish" id="home_finish" style="background-color:#b82e2e;color:white;padding: 10px 23px 10px;"  type="button"
                      @click="finish()">Jetzt suchen
              </button>
            </div>





          </div>
      </form>
    </div>

  </div>
</template>

<script type="text/babel">



import DateInput from "@/js/components/elements/dateInput";
import Select from "@/js/components/modals/select";
import Redir from "@/js/components/modals/redir";
import Passenger from "@/js/components/elements/passenger";
import ModalInput from "@/js/components/elements/modalInput";
import {mapActions, mapGetters} from "vuex";
import {
  GET_DATE,
  GET_DESTINATION, GET_LINK,
  GET_ORIGIN,
  IS_VALID,
  SET_DESTINATION,
  SET_ORIGIN
} from "@/js/state/modules/dtc.types";
import {getModalHeadline} from "@/js/Text";
import {GET_RDR, RETRIEVE_SUGGESTIONS_MIXED} from "@/js/state/modules/common.types";
import Navbar from "@/js/components/elements/navbar";
import {mixin as clickaway} from "vue-clickaway";
import {EventBus, EVENTBUS_QUICKJOURNEY} from "@/js/eventbus";

const NAMESPACE = 'dtc';

export default {
  name: 'home',
  components: {Navbar, ModalInput,Passenger,DateInput},
  mixins: [clickaway],
  data(){
    return {
      submitted:false,

      quickJourney:false,
    }
  },
  computed: {

    ...mapGetters("common",{
      rdr:GET_RDR
    }),
    ...mapGetters(NAMESPACE, {
        isValid:IS_VALID,
        getDate:GET_DATE,
        getOrigin:GET_ORIGIN,
        getDestination:GET_DESTINATION,
        getLink:GET_LINK
      },
    ),

  },


  mounted() {
    EventBus.$on(EVENTBUS_QUICKJOURNEY, async (data) => {



      let action = data.action;
      if (action.hasOwnProperty('origin')) {
        await this.setOrigin(action.origin);
      }
      if (action.hasOwnProperty('destination')) {
        await this.setDestination(action.destination);
      }
      this.quickJourney = true;
      EventBus.$emit('OPEN_DATE',data.event);



    });
  },
  watch:{
    getDate(){
      if(this.quickJourney) {
        this.quickJourney = false;
        this.finish();
      }
    }
  },
  methods:{
    ...mapActions("dtc",{
      setOrigin:SET_ORIGIN,
      setDestination:SET_DESTINATION
    }),

    check(which){
      if(!this.submitted){
        return true;
      }
      return this.isValid[which];
    },
    showModalOrigin(){
        this.showModal(
            getModalHeadline(NAMESPACE,GET_ORIGIN),
            {
                action:["common",RETRIEVE_SUGGESTIONS_MIXED],
                placeholder:'z.B. Berlin, Dortmund oder Hamburg Hbf',
                dispatch:[NAMESPACE,SET_ORIGIN]
            },
            true
        );
    },
    showModalDestination(){
      this.showModal(
          getModalHeadline(NAMESPACE,GET_DESTINATION),
          {
            action:["common",RETRIEVE_SUGGESTIONS_MIXED],
            placeholder:'z.B. Berlin, Dortmund oder Hamburg Hbf',
            dispatch:[NAMESPACE,SET_DESTINATION]
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

    async finish(){
      this.submitted = true;
      if(!Object.values(this.isValid).every(item => item === true)){
        return;
      }



      this.$modal.show(Redir, {
        omio: true,
        url:this.getLink,
        timeout: 10
      }, {
        maxWidth: 800,
        width: 800,
        height: 300
      });


    }
  }
}
</script>
