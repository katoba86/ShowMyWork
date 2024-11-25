<template>
  <div>

    <div class="input-group">
      <input  :placeholder="config.placeholder" class="form-control form-control-sm instant-input"  @input="debounceInput" autocomplete="false"  v-on="listeners" type="search" >

    </div>


    <div v-if="isLoading">
      <div class="loader"><img alt="spinner" src="/desktop/images/spinner.gif"></div>
    </div>




    <div class='el-input-group__append'>
      <span class="small" v-if="noInput">Die beliebtesten Städte Deutschlands</span>
      <ul  class="ais-results" style="max-height:500px;overflow-y:auto;">
        <slot  v-for="(result,index) in getSuggestions" :result="result" >
          <div :key="'slotSuggest'+index" class="search-item" @click="setSuggestion(index)">
            <div class="search-item-icon" :class="result.type"></div>
            <div class="search-item-text">
              <h3>{{result.name }}</h3>
            </div>
          </div>
        </slot>
      </ul>
    </div>




  </div>
</template>
<script type="text/babel">



import debounce from "debounce";
import {mapActions, mapGetters} from "vuex";
import {
  RETRIEVE_SUGGESTIONS_MIXED,
  GET_SUGGESTIONS,
  IS_LOADING,
  CLEAR_SUGGESTIONS, SET_SUGGESTIONS
} from "@/js/state/modules/common.types";
import {GET_PRESELECT} from "@/js/state/modules/dtc.types";

export default {
  name:'instant',

  props:{
    config:{
      type:Object
    }
  },


  computed:{
    ...mapGetters('common',{
      getSuggestions:GET_SUGGESTIONS,
      isLoading:IS_LOADING
    }),
    ...mapGetters('dtc',{
      getPreselect:GET_PRESELECT
    }),
    listeners() {
      return {
        ...this.$listeners,
        // eslint-disable-next-line no-unused-vars
        input: () => {
          return null;
        },
      }
    },
  },
  mounted() {
    if(this.config.action[0] === "common" && this.config.action[1]==="RETRIEVE_SUGGESTIONS_MIXED") {
      this.setSuggestions(this.getPreselect);
    }
  },
  data(){
    return {
      noInput:true,
      internalValue:null,
      minChars:3,
      maxChars:9999
    };
  },

  methods:{
    ...mapActions("common",{
        retrieveSuggestionsMixed:RETRIEVE_SUGGESTIONS_MIXED,
        clearSuggestions:CLEAR_SUGGESTIONS,
        setSuggestions:SET_SUGGESTIONS
    }),

    setSuggestion(i){

      const item = this.getSuggestions[i];


      this.$store.dispatch(`${this.config.dispatch[0]}/${this.config.dispatch[1]}`,item);
      this.noInput = false;
      this.$emit('close');
      this.clearSuggestions();
    },



    debounceInput: debounce(function(e){
      this.noInput = false;


      if(e.target.value.length < this.minChars){
        this.isSoftError = true;
        this.softError = `Mindestens ${this.minChars} Zeichen eingeben`;
        return false;
      }
      this.$store.dispatch(`${this.config.action[0]}/${this.config.action[1]}`,e.target.value);


    },500),


    inputHandler(e) {
      const newValue = e.target.value
      this.$emit('input', newValue);
      this.internalValue = newValue;
    }
  }



}

</script>
