<template>
  <div class="fbb-modal-content">
    <div class="modal-body p-md-0">
      <button class="close" @click="$emit('close')">X</button>
      <div class="row">
        <div class="col-12 text-center">
          <h3>{{ loadingText }}</h3>
        </div>
        <div class="col-12 text-center" rel="content_target" id="content_target">
          <div ref="dtc" :id="cid" class="restDtc" data-load="SIZE_HOR_BIG"></div>
        </div>
        <div class="col-12 mt-2 text-center">

          <div v-if="isLoading">
            <button type="button" disabled class="btn btn-primary">Bitte warten ( {{formattedTimeLeft}} )</button>
          </div>
          <div v-if="!isLoading  && (url!==false || gurl!==false)">
            <a :href="getUrl()" :target="target" class="btn btn-primary">Weiter</a>
          </div>

          <div v-if="!isLoading && (url===false && gurl===false)">
            <a @click="$emit('close');" target="_blank" class="btn btn-primary">Weiter</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>


<script type="text/babel">

import {mapGetters} from "vuex";
import {GET_RDR} from "@/js/state/modules/common.types";
import Dtc from "@/js/components/mixins/dtc";

export default {
  mixins: [Dtc],
  props: {
    'timeout': {
      type: Number,
      required: false,
      default: 5
    },
    'target': {
      type: String,
      required: false,
      default: '_self'
    },
    'url': {
      required: false,
      default: '#'
    },
    'fetch': {
      required: false,
      default: false
    },
    'params': {
      required: false,
      default: null
    }

  },
  computed: {
    ...mapGetters("common", {
      rdr: GET_RDR
    }),
    loadingText() {
      return (this.isLoading) ? 'Ihre Anfrage wird vorbereitet' : 'Fertig! Weiter...';
    },
    formattedTimeLeft(){
      return this.timeLeft;
    },
    timeLeft() {
      return this.timeout - this.timePassed;
    },
  },
  name: 'RedirModal',


  mounted() {
    this.notify();
    this.start();
    this.startTimer();


  },
  watch: {
    timeLeft(newValue) {
      if (newValue === 0) {
        this.onTimesUp();
      }
    }
  },

  methods: {
    onTimesUp() {
      clearInterval(this.timerInterval);
      this.isLoading = false;
    },

    startTimer() {
      this.timerInterval = setInterval(() => (this.timePassed += 1), 1000);
    },

    getUrl() {
      return (this.gurl === false) ? this.url : this.gurl;
    },


    fetchData() {
      return new Promise((resolve,reject)=>{
        this.fetch(this.params).then((response) => {
          if (response.hasOwnProperty('url')) {
            this.gurl = response.url;
            resolve(true);
          } else if (response.hasOwnProperty('data')) {
            this.gurl = response.data;
            resolve(true);
          }
          reject(false);
        });
      });

    },



    async start() {
      if (this.fetch !== false) {
        await this.fetchData();
      }
    }

  },
  created() {
    // eslint-disable-next-line no-unused-vars
    this.cid = [...Array(5)].map(i => (~~(Math.random() * 36)).toString(36)).join('');
  },
  data() {
    return {
      cid: null,
      timer: null,
      gurl: false,
      isLoading: true,
      timePassed: 0,
      timerInterval: null
    };
  }
}
</script>
