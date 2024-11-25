<template>
  <div class="input-group bg-white mb-3">
    <input v-model="getDateStr" aria-label="Wann beginnen Sie Ihre Reise?" class="form-control varioForm-input"
           placeholder="Wann beginnen Sie Ihre Reise?"
           type="text"
           id="form_date_holder"
           v-on:click="showDate($event)">
    <div class="input-group-append varioForm-input-append bg-white">
        <span class="input-group-text">
          <svg role="img">
            <use v-bind:xlink:href="icon"  xmlns:xlink="http://www.w3.org/1999/xlink"></use>
          </svg>
        </span>
    </div>
    <datetime

        ref="datetime"
      @close="setDate"
        type="datetime"
        value-zone="Europe/Berlin"
        zone="Europe/Berlin"
        v-model="currentDate"
        :date="currentDate"
        :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }"
        :hour-step="1"
        :max-datetime="maxDate"
        :min-datetime="minDate"
        :minute-step="10"
        :phrases="{ok: 'Weiter', cancel: 'Zurück'}"
        :week-start="7"
        auto
        input-class="d-none"

    ></datetime>



  </div>
</template>
<script type="text/babel">
import {mapActions, mapGetters} from "vuex";
import {GET_DATE, SET_DATE} from "@/js/state/modules/dtc.types";
import {DateTime as LuxonDateTime} from "luxon";
import {Datetime} from "vue-datetime";
import {EventBus} from "@/js/eventbus";

const NAMESPACE = 'dtc';
export default {
  name:'date',
  components: {
    datetime: Datetime
  },
  data(){
    return {
      currentDate: null,
      minDate: LuxonDateTime.local().toISO(),
      maxDate: LuxonDateTime.local().plus({days: 180}).toISO(),
    }
  },
  props:{
    icon:{
      type:String,
      default:'#varioCalendar'
    },
  },
  computed:{
    ...mapGetters(NAMESPACE,{
        getDate:GET_DATE
    }),
    getDateStr:function dstr(){
      let s = this.getDate;

      if (typeof s === 'string') {
        return s;
      } else {
        return s.setZone('europe/berlin').toFormat('dd.LL.yyyy HH:mm:00');
      }
    },
  },
  mounted() {
    EventBus.$on('OPEN_DATE',(ev)=>{
      this.showDate(ev);
    });
  },
  methods:{
    ...mapActions(NAMESPACE,{
      setVuexDate:SET_DATE
    }),
    setDate(){
      this.setVuexDate(this.$refs.datetime.$data.datetime);
    },

    showDate(e) {
      console.log(e);
        this.$refs.datetime.open(e);
    },

  }
}


</script>
