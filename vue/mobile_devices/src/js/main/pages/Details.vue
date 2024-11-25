<template>
  <div class="page details">
    <div class="page-content" v-if="!loading">

      <page-nav title="Ihre Reiedaten"/>



      <div class="journey-header">

        <div class="left">
          <div class="leftWrap">
            <h4>Von {{ journey.origin.name }} nach {{ journey.destination.name }}</h4>
            <div>Am {{ journey.time_departure.date }} um {{ journey.time_departure.time}} Uhr</div>
          </div>
        </div>


      </div>
      <div  v-bind:key="'trip'+index" v-for="(trip,index) in journey.trips">
        <div class="overview">
          <div class="left">
            <div class="fare">{{ Utils.getExactFare(trip.fare,"€ ","",getNoFare()) }}</div>
            <div class="passenger">{{ pStr }}</div>
          </div>
          <div class="right">
            <a class="btn btn-danger" :href="getUrl(trip)">Ticket</a>
          </div>
        </div>


        <h6 class="small mt-4 pl-2 fw-600">Von {{trip.origin.name}} nach {{trip.destination.name}}</h6>
        <div class="timeline timeline-horizontal both" v-bind:key="'step2'+index+'_'+sindex"  v-for="(step,sindex) in trip.steps">
          <div class="timeline-item start" v-if="(sindex===0)"></div>
          <div class="timeline-item" >
            <div class="timeline-item_opposite">
             <small>{{  step.departure.time }}</small>
            </div>
            <div class="timeline-item_body">
              <h6>{{ step.origin.name}}</h6>
              <small>{{ step.product.prodCtx.name }}</small>
            </div>
          </div>

          <div class="timeline-item" >
            <div class="timeline-item_opposite">
              <small>{{  step.arrive.time }}</small>
            </div>
            <div class="timeline-item_body">
              <h6>{{ step.destination.name}}</h6>
              <small v-if="(sindex!==(trip.steps.length-1))">{{ step.product.prodCtx.name }}</small>
            </div>
          </div>

          <div class="timeline-item end" v-if="(sindex===(trip.steps.length-1))"></div>

        </div>



      </div>



    </div>
  </div>
</template>

<script>

import PageNav from "@/js/main/components/PageNav";
import Utils from "@/js/common/Utils";
import {mapGetters} from "vuex";
import {GET_PASSENGER_STR, GET_RESULTS} from "@/js/common/state/modules/dtc.types";
export default {
  name: 'detailPage',
  components: {
    PageNav
  },
  data() {
    return {
      Utils:Utils,
      journey: null,
      loading: true,
      input:window.input,
    }
  },
  computed:{
    ...mapGetters("dtc",{
      results:GET_RESULTS,
      pStr:GET_PASSENGER_STR
    })
  },
  props: {
    jid: {
      type: String,
      required: true
    }
  },
  mounted() {


    this.init();


    //this.journey = ;
  },
  methods: {



    getUrl(trip){
      if(trip.provider==="DB"){
        return Utils.getDBLink(trip.origin.name,trip.destination.name,trip.departure.date,trip.departure.time);
      }else{
        return Utils.getFlixLink(trip.origin,trip.destination,trip.departure.date);
      }
    },

    getNoFare(){
      if(this.journey.fare.tarif!=="Preisauskunft nicht möglich"){
        return this.journey.fare.tarif;
      }
      if(this.journey.fare.verbund!==null){
        return this.journey.fare.tarif+" Tarif";
      }
      return "Preis bitte anfragen";
    },

    init(){
      let jnys = this.results.journeys;

      this.journey = jnys.find((e) => {

        if (e.id === this.jid) {
          this.loading = false;

          return e;

        }
      });

    }

  }
}
</script>


