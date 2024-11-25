<template>
  <div class="page result gradient noScroll">
    <div class="page-content d-flex flex-column" style="height:100%;">
      <page-nav title="Ihre Reisedaten"/>


      <div class="result-info" style="margin-top: 40px">
        <div class="result-info-field first">
          <div class="small">Von</div>
          <div>{{ origin.name }}</div>
        </div>
        <div class="result-info-icon align-self-center">
          <mdicon name="chevron-double-right" />
        </div>
        <div class="result-info-field second " style="text-align:center">
          <div class="small">Nach</div>
          <div>{{ destination.name }}</div>
        </div>
        <div class="break"></div> <!-- break -->
        <div class="result-info-line">
          <div>{{ Utils.getDate(date,'day') }} ab {{ Utils.getDate(date,'time')}} - {{ passengerStr }}</div>
        </div>
      </div>


      <div class="connections result">

        <div v-if="isLoading">
          <Loader/>
        </div>


        <transition-group  name="list-complete" tag="div"   @enter="enter">
          <ResultItem class="resultItem"  :journey="journey"  v-for="(journey,index) in results.journeys" v-bind:key="'journey'+index" @click="navigate(journey,$event)" ></ResultItem>
        </transition-group>
      </div>






    </div>

    <nav class="fixed-bottom  navbar-result-bottom">

      <div class="btn-group btn-group-justified  w-100" role="group" aria-label="Basic example">
        <button v-bind:class="getClass('departure')" v-on:click="toggleSort('departure')" type="button"
                class="btn  h-100">Abfahrt
        </button>
        <button v-bind:class="getClass('fare')" v-on:click="toggleSort('fare')" type="button"
                class="btn  h-100">Preis
        </button>
        <button v-bind:class="getClass('duration')" @click="toggleSort('duration')" type="button"
                class="btn  h-100">Dauer
        </button>
      </div>


    </nav>

  </div>
</template>
<style lang="scss">
@import "~bootstrap/scss/functions";
@import "~bootstrap/scss/variables";
@import "~bootstrap/scss/mixins";
  @import "../../../scss/theme";
  @import "../../../scss/rewrite/result";

</style>
<script>
import PageNav from "@/js/main/components/PageNav";
import Loader from "@/js/main/components/Loader";
import Utils from "@/js/common/Utils";
import {mapActions, mapGetters} from "vuex";

import {
  GET_DATE_OBJ,
  GET_DESTINATION,
  GET_ORIGIN, GET_PASSENGER, GET_PASSENGER_STR,
  GET_RESULT_SORT,
  GET_RESULTS,
  GET_RESULTS_DB,
  GET_RESULTS_FLIX,
  SET_RESULT_SORT
} from "@/js/common/state/modules/dtc.types";
import ResultItem from "@/js/main/components/ResultItem";
import { IS_LOADING} from "@/js/common/state/modules/common.types";

const getAnime = () => import('animejs');

export default {
  components: {
    ResultItem,
    Loader,
    PageNav
  },
  async created() {

  },
  computed: {
    cSort() {
      return this.sortBy;
    },
    ...mapGetters('common',{
      isLoading:IS_LOADING
    }),

    ...mapGetters("dtc", {
      results: GET_RESULTS,
      origin:GET_ORIGIN,
      destination:GET_DESTINATION,
      passenger:GET_PASSENGER,
      passengerStr:GET_PASSENGER_STR,
      date:GET_DATE_OBJ
    }),
  },
  mounted() {
    getAnime().then((d)=>{
      this.finish = true;
      this.anime = d.default;
      this.fetch();
      this.setSort(this.sortProperty());
    });





  },
  methods:{


    enter() {
      if(this.finish) {
        this.anime({
          easing: 'easeInSine',
          targets: '.connections .resultItem',
          translateX: [-500,0],
          duration:500,
          delay: this.anime.stagger(400, {start: 100}) // delay starts at 500ms then increase by 100ms for each elements.
        });
      }
    },


    ...mapActions("dtc",{
      getDb:GET_RESULTS_DB,
      getFlix:GET_RESULTS_FLIX,
      setSort:SET_RESULT_SORT,
      getSorted:GET_RESULT_SORT,
    }),
    fetch(){

      document.getElementById('body').classList.add('blocked');


      let payload = {
        origin:this.origin,
        destination:this.destination,
        date:this.date,
        end:0,
        way:0,
        travelers:this.passenger,
      };


      this.getDb(payload).then(()=>{
        this.getFlix(payload);
      });

    },

    getClass(btnSort) {
      return {
        'btn-primary': (btnSort === this.sortBy),
        'btn-default': !(btnSort === this.sortBy)
      }
    },


    navigate(journey){
      this.$router.push({path: '/details/'+journey.id});
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
      this.sortBy = what;
      this.setSort(this.sortProperty());
    },
  },
  data() {
    return {
      Utils:Utils,
      anime:null,
      finish:false,
      isError:false,
      loaded:true,
      sortBy:'fare'
    }
  },
}
</script>
