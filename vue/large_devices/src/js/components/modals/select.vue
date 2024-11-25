

<template>
  <div class="fbb-modal-content">

    <div class="modal-body p-md-0">
      <button class="close" @click="$emit('close')">X</button>
      <div class="row no-gutters">
        <div class="col-md-4 col-xl-5 d-none d-md-flex p-2  rounded-left">
          <div ref="dtc" :id="rand" class="restDtc" data-load="SIZE_RECT_BIG"></div>
        </div>

        <div class="col-md-8 col-xl-6 mx-auto" style="min-height:400px;">


          <h5 class="fw-600 pt-3">{{ headline }}</h5>

          <Instant @close="$emit('close')" :config="instantConfig"></Instant>






        </div>
      </div>
    </div>
  </div>
</template>

<script>

import Instant from "@/js/components/elements/instant";
import Dtc from "@/js/components/mixins/dtc";
export default {
  components: {Instant},
  mixins:[Dtc],
  props: {

    headline: {
      type:String,
      default:'not set'
    },
    instantConfig:{
      type:Object,
    },
    showNearLocations:{
      type:Boolean,
      default:false
    }

  },
  name: 'CityModal',
  data() {
    return {
      cid: null,


      usernear: [],
      value: "",

    }
  },
  watch: {
    value(i) {
      this.noData = !(i !== null && i.length >= 1);
      return i;
    }
  },
  mounted() {
    this.notify();
  },
  computed: {

    rand() {
      return this.cid;
    },

  },
  beforeMount() {
    if (null === this.cid) {
      // eslint-disable-next-line no-unused-vars
      this.cid = [...Array(5)].map(i => (~~(Math.random() * 36)).toString(36)).join('');
    }
  }
}
</script>
