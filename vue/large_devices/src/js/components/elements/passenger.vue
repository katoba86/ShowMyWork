<template>
  <div class="dropdown d-block bg-white">
              <span ref="travelerModal" v-on-clickaway="away" class="dropdown-toggle" data-toggle="dropdown"
                    @click="showDropDown()">{{ getPassengerStr }}</span>
    <div class="dropdown-menu w-400" v-bind:class="(passengerDropDown)?'show':''">
      <h6 class="vario text-dark">Wieviele Personen reisen mit?</h6>

      <label>Erwachsene</label>
      <div class="varioNumeric">
        <a class="operator minus" href="javascript:void(0)" @click="changeTraveler('adult',false)"></a>
        <span class="value">{{ getPassenger.adult }} Erwachsener</span>
        <a class="operator plus" href="javascript:void(0)" @click="changeTraveler('adult',true)"></a>
      </div>
      <label>Kinder</label>
      <div class="varioNumeric">
        <a class="operator minus" href="javascript:void(0)" @click="changeTraveler('child',false)"></a>
        <span class="value">{{ getPassenger.child }} Kinder</span>
        <a class="operator plus" href="javascript:void(0)" @click="changeTraveler('child',true)"></a>
      </div>
      <label>Kleinkinder</label>
      <div class="varioNumeric">
        <a class="operator minus" href="javascript:void(0)" @click="changeTraveler('baby',false)"></a>
        <span class="value">{{ getPassenger.baby }} Kleinkinder</span>
        <a class="operator plus" href="javascript:void(0)" @click="changeTraveler('baby',true)"></a>
      </div>

    </div>
  </div>
</template>




<script type="text/babel">


  import {mixin as clickaway} from "vue-clickaway";
  import {GET_PASSENGER, GET_PASSENGER_STR, SET_PASSENGER} from "@/js/state/modules/dtc.types";
  import {mapActions, mapGetters} from "vuex";

  const NAMESPACE = 'dtc';

  export default {
    name: 'passenger',
    mixins: [clickaway],
    computed:{
      ...mapGetters(NAMESPACE,{
        getPassengerStr:GET_PASSENGER_STR,
        getPassenger:GET_PASSENGER
      })
    },
    data(){
      return {
        passengerDropDown:false,
      }
    },
    methods:{
      ...mapActions(NAMESPACE,{
        setPassenger:SET_PASSENGER
      }),
      changeTraveler(which,inc){
        this.setPassenger({who:which,type:inc});
      },
      showDropDown(){
        this.passengerDropDown = true;
      },
      away(evt) {

        if (
            evt.target.classList.contains('dropdown-toggle') ||
            evt.target.classList.contains('toggler') ||
            evt.target.classList.contains('dropdown-menu') ||
            evt.target.classList.contains('operator') ||
            evt.target.classList.contains('value') ||
            evt.target.classList.contains('varioNumeric') ||
            evt.target.classList.contains('vario')
        ) {
          return;
        }

        this.passengerDropDown = false;
        //this.travelerDropdown = false;
      },

    }
  }


</script>