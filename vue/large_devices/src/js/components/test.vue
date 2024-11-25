<template>

    <datetime
        ref="maindate"
        type="datetime"
        v-model="date"
        input-class="d-none"
        value-zone="Europe/Berlin"

        zone="Europe/Berlin"
        :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }"
        :phrases="{ok: 'Weiter', cancel: 'Zurück'}"
        :hour-step="1"
        :minute-step="10"
        :min-datetime="minDate"
        :max-datetime="maxDate"
        @close = "setDate"

        :week-start="7"
        auto
    ></datetime>
</template>
<script type="text/babel">
import {DateTime as LuxonDateTime} from "luxon";
import {Datetime} from "vue-datetime";
export default {
  props:{
    setDate:{
      type:Function,
      required:true
    }
  },
  name:'datetest',
  components: {
    datetime: Datetime
  },

  data(){
    return {
      date:null,
      currentDate: null,
      minDate: LuxonDateTime.local().toISO(),
      maxDate: LuxonDateTime.local().plus({days: 180}).toISO(),
    }
  },
}
</script>