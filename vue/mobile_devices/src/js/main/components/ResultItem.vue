<template>

  <div class="jitem ">
    <div class=" connection d-block journey-item" >

    <div class="connection-info">{{ Utils.getExactFare(journey.fare,"","", "?") }} €</div>

    <div class="connection-detail">
      <div class="connection-detail_city two-row"><div>{{  journey.origin.name }}</div><div>{{journey.time_departure.time}}</div></div>
      <div class="connection-detail_line">
        <div class="line-icon"></div>
        <div class="duration">{{ Utils.getDuration(journey.duration.humanSplit)}}</div>
      </div>
      <div class="connection-detail_city two-row"><div>{{  journey.destination.name }}</div><div>{{journey.time_arrive.time}}</div></div>
    </div>
    <div class="connection-detail journey-detail_secondary">
      <div>{{ Utils.getInterchange(journey.interChange)}}</div>
      <div><span>{{ Utils.getTransport(journey) }}</span></div>
    </div>
    </div>


  </div>

</template>
<style lang="scss">




</style>
<script>
import Utils from "@/js/common/Utils";

export default {
  props:{
    journey:{
      type:Object,
      required:true
    }
  },

  data() {
    return {
      Utils:Utils
    }
  },
}
</script>