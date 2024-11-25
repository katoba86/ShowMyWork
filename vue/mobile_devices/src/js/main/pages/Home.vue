<template>
  <div>



    <div class="topForm">




      <modal-input id="dtcOrigin" :error="text.origin.error" :label="text.origin.head"
                   :valid="check('origin')" :value="getOrigin" icon="from" @click="navigateSelect('select',GET_ORIGIN)"/>



      <modal-input v-if="isFoldedOut" id="dtcDestination" :error="text.destination.error" :label="text.destination.head"
                   :valid="check('destination')" :value="getDestination" icon="from" @click="navigateSelect('select',GET_DESTINATION)"/>


      <div class="row" v-if="isFoldedOut">
        <div class="col-6">

          <div id="dtcDate" class="inputWrapper input-small datetime mt-0 mb-0 mr-0 pr-0" @click="showDate">
            <label>{{ text.date.head }}</label>
            <div class="input">{{ getDate }}</div>
            <div class="icon date"></div>
          </div>

        </div>
        <div class="col-6 pl-0">
          <div id="dtcPassenger" class="inputWrapper input-small passengers mt-0 mb-0 ml-0 "
               @click="$router.push({path:'/passenger'})">
            <label>{{ text.passenger.head }}</label>
            <div class="input">{{ getPassengerStr }}</div>
            <div class="icon user"></div>
          </div>


        </div>

      </div>



      <div v-if="isFoldedOut" class="submitWrapper mt-4 d-flex justify-content-end">

        <button  class="btn btn-secondary finish" @click="finish()">Jetzt vergleichen</button>
      </div>
      <div v-if="!isFoldedOut" class="submitWrapper mt-4 d-flex justify-content-end">

        <button  class="btn btn-secondary finish" @click="setFolded(true)">Wohin?</button>
      </div>

      <label class="d-none" for="dateSelect">Date</label><input id="dateSelect" ref="dateSelect"
                                                                v-model="curDate" :min="''"
                                                                class="inputDate2" type="datetime-local"/>

    </div>
  </div>
</template>

<style>

</style>

<script>

import {FIELD_DTC_ORIGIN, FIELD_DTC_DESTINATION, FIELD_DTC_Date, FIELD_DTC_Passenger} from '@/js/common/Text';
import {mapActions, mapGetters} from "vuex";
import {
  GET_DATE,
  GET_DESTINATION,
  GET_LINK,
  GET_ORIGIN, GET_PASSENGER,
  GET_PASSENGER_STR,
  IS_VALID, SET_DATE
} from "@/js/common/state/modules/dtc.types";
import ModalInput from "@/js/main/components/modalInput";
import {ref,watch} from "vue";
import {useStore} from 'vuex';
import {IS_FOLDED_OUT, SET_FOLDED_OUT} from "../../common/state/modules/common.types";
const NAMESPACE = 'dtc';
export default {
  components: {
    ModalInput
  },
  mounted(){
    if(typeof window.topFormData!=='undefined' && window.topFormData.hasOwnProperty('smallHead')){
      this.showAll = !window.topFormData.smallHead;
    }
  },
   setup(){
    const curDate = ref("");
    const store = useStore();



    watch(curDate,()=>{
      store.dispatch(["dtc", SET_DATE].join("/"),curDate);
    });

    return {
      curDate,
      text:{
        origin: FIELD_DTC_ORIGIN,
        destination: FIELD_DTC_DESTINATION,
        date: FIELD_DTC_Date,
        passenger: FIELD_DTC_Passenger
      }
    }
  },

  computed: {
    ...mapGetters(NAMESPACE, {
      isValid: IS_VALID,
      getDate: GET_DATE,
      getOrigin: GET_ORIGIN,
      getDestination: GET_DESTINATION,
      getLink: GET_LINK,
      getPassenger: GET_PASSENGER_STR,
      getPassengerObj: GET_PASSENGER
    }),
    ...mapGetters('common',{
      isFoldedOut: IS_FOLDED_OUT,
    }),
    getPassengerStr(){
      const num = this.getPassengerObj.adult + this.getPassengerObj.child + this.getPassengerObj.baby;
      if(num > 1){
        return num+" Reisende";
      }
      return this.getPassenger;
    }
  },

  methods: {
    ...mapActions("dtc",{
      setD:SET_DATE
    }),
    ...mapActions("common",{
      setFolded:SET_FOLDED_OUT
    }),

    check(which) {
      if (!this.submitted) {
        return true;
      }
      return this.isValid[which];
    },
    finish() {
      this.submitted = true;
      if (!Object.values(this.isValid).every(item => item === true)) {
        console.log("Is not valid!");
        return false;
      }
      this.$router.push({ name: 'redirect', params: { url:'self' },query:{m:'result'}});

      return true;
    },
    showDate(e){
            document.getElementById('dateSelect').dispatchEvent(new e.constructor(e.type, e));
    },

    navigateSelect(to, type) {
      this.$router.push({path: to, query: {type: type,ns:'dtc'}}).catch(() => {
        console.log('Error');
      });
    },
  },
  data() {
    return {
      GET_ORIGIN:GET_ORIGIN,
      GET_DESTINATION:GET_DESTINATION,
      submitted: false
    }
  }

}
</script>


