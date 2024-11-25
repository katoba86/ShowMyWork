<template>
  <div>

    <div class="topForm">


      <modal-input id="dtcOrigin" :error="text.origin.error" :label="text.origin.head"
                   :valid="check('origin')" :value="getOrigin" icon="from" @click="navigateSelect('select',GET_HST_CITY,'hst')"/>

      <modal-input id="dtcDestination" :error="text.destination.error" :label="text.destination.head"
                   :valid="check('destination')" :value="getDestination" icon="from" @click="navigateSelect('select',GET_HST,'hst')"/>





      <div class="submitWrapper mt-0 d-flex justify-content-end">

        <button ref="hstFinish"  class="btn btn-secondary finish" @click="finish()">Jetzt suchen</button>
      </div>

    </div>
  </div>
</template>
<script>
import {FIELD_HST_DESTINATION, FIELD_HST_ORIGIN} from '@/js/common/Text';
import {mapGetters} from "vuex";
import {
  GET_HST,
  GET_HST_CITY,
  HST_IS_VALID

} from "@/js/common/state/modules/hst.types";
import ModalInput from "@/js/main/components/modalInput";

const NAMESPACE = 'hst';
export default {
  components: {
    ModalInput
  },

  computed: {
    ...mapGetters(NAMESPACE, {
      isValid: HST_IS_VALID,
      getOrigin: GET_HST_CITY,
      getDestination: GET_HST,
    })
  },
  methods: {

    check(which) {
      if (!this.submitted) {
        return true;
      }
      return this.isValid[which];
    },
    finish() {
      this.submitted = true;

      if (!Object.values(this.isValid).every(item => item === true)) {

        return false;
      }
      this.$router.push({ name: 'redirect', params: { url:'self' },query:{m:'hst'}});
    },
    change2() {
      this.test = "From outside";
    },
    navigateSelect(to, type, ns) {
      this.$router.push({path: to, query: {type: type,ns:ns}}).catch((e) => {
        console.error(e);
      });
    },
  },

  created() {

    this.emitter.on('hstSubmit', () => {
      if(typeof this.isValid!=='undefined'){
        this.finish();
      }

    });
  },

  data() {
    return {
      submitted: false,
      pop: false,
      curDate: null,
      GET_HST_CITY:GET_HST_CITY,
      GET_HST:GET_HST,
      text: {
        origin: FIELD_HST_ORIGIN,
        destination: FIELD_HST_DESTINATION
      }
    }
  }

}
</script>


