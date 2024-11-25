<template>
  <div class="page result gradient hst">

    <div class="page-content ">

     <page-nav :title="currentTitle"></page-nav>


      <div class="container" style="margin-top:70px;">
        <div class="row">
          <div class="col-12">
            <div  v-if="!selected">

              <div class="card clickable card-shadow" @click="selected=true;isBus=true;currentTitle='Mit dem Bus nach?'">
                <div class="card-body">
                  <div class="media">
                    <img class="align-self-start mr-3 w-100px" src="~../images/bus-icon1.png" alt="Mit dem Bus">
                    <div class="media-body">
                      <h6 class="mt-0">Mit dem Bus in die Nähe</h6>
                      <p>Mit dem öffentlichen Nahverkehr (Bus, Straßenbahn, etc.) zu Haltestellen in der Nähe</p>
                    </div>
                  </div>
                </div>
              </div>
              <h6 class="divider mt-4 mb-4">oder</h6>
              <div class="card clickable card-shadow mb-6" @click="selected=true;isTrain=true;currentTitle='Mit der Bahn nach?'">
                <div class="card-body">
                  <div class="media">
                    <img class="align-self-start mr-3 w-100px" src="~../images/train_icon.png" alt="Mit der Bahn">
                    <div class="media-body">
                      <h6 class="mt-0">Mit Bahn & Bus in andere Städte</h6>
                      <p>Ab hier in andere Städte reisen. Hierfür ermitteln wir den besten Preis.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>



            <div v-if="isTrain">

              <h4 class="fw-200 h6">Okay, in eine andere Stadt.</h4>
              <p class="small text-muted">In welche Stadt möchten Sie reisen?</p>
              <material-input placeholder="z.B. Marktplatz "  :inputFunc="debounceListener" :value="displayValue" ></material-input>


              <div v-if="isLoading">
                <Loader/>
              </div>
              <ul  class="search-list" v-if="getSuggestions.length > 0 && displayValue.length>2">
                <li @click="setSuggestion(index)" v-for="(result,index) in getSuggestions" :result="result" :key="'slotSuggest'+index"  class="search-list-item">
                  <div class="search-list-item-content">
                    <i class="search-list-item-icon material-icons" v-html="getIcon(result.type)"></i>
                    <div class="search-list-item-text" v-if="('bundesland' in result)">
                      <span>{{ result.name }}</span>
                      <span>Bundesland: {{  result.bundesland.name }}</span>
                    </div>
                    <span class="search-list-item-text"  v-if="!('bundesland' in result)">{{ result.name}}</span>
                    <i class="material-icons search-list-item-icon search-list-item-icon_right">chevron_right</i>
                  </div>
                </li>
              </ul>







            </div>
            <!-- Selected Bus -->
            <div v-if="isBus">

              <p class="small text-muted">An welcher Haltestelle möchten Sie aussteigen?</p>
              <material-input placeholder="z.B. Marktplatz "  :inputFunc="debounceListener" :value="displayValue" ></material-input>

              <div v-if="isLoading">
                <Loader/>
              </div>
              <ul  class="search-list" v-if="getSuggestions.length > 0 && displayValue.length>2">
                <li  @click="setSuggestion(index)" v-for="(result,index) in getSuggestions" :result="result" :key="'slotSuggest'+index"  class="search-list-item">
                  <div class="search-list-item-content">
                    <i class="search-list-item-icon material-icons" v-html="getIcon(result.type)"></i>
                    <div class="search-list-item-text" v-if="('bundesland' in result)">
                      <span>{{ result.name }}</span>
                      <span>Bundesland: {{  result.bundesland.name }}</span>
                    </div>
                    <span class="search-list-item-text"  v-if="!('bundesland' in result)">{{ result.name}}</span>
                    <i class="material-icons search-list-item-icon search-list-item-icon_right">chevron_right</i>
                  </div>
                </li>
              </ul>

            </div>



          </div>
        </div>
      </div>

      <!-- Selected Train -->





    </div>
  </div>
</template>
<style lang="scss">


.search-list{
  margin-top:12px;
  width:100%;
  max-width: 100%;
  display: inline-block;
  vertical-align: top;

  padding:8px 0;
  flex-flow:column nowrap;
  position:relative;
  list-style:none;

  .search-list-item{
    width:100%;
    height: auto;
    position: relative;
    z-index: 2;
    margin: 0;
    padding: 0;
    display: inline-block;
    overflow: hidden;
    outline: none;
    background: transparent;
    border: 0;
    border-radius: 0;
    transition: .4s cubic-bezier(.4,0,.2,1);
    font-family: inherit;
    line-height: normal;
    text-decoration: none;
    vertical-align: top;
    white-space: nowrap;
    .search-list-item-content{

      min-height: 48px;
      padding: 4px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: padding .4s cubic-bezier(.25,.8,.25,1);
      will-change: padding;
      .search-list-item-icon{
        margin-right:32px;
        &.search-list-item-icon_right{
          margin:0 -10px 0 16px;
        }
      }

      .search-list-item-text{
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;

        line-height: 1em;
        white-space: nowrap;
        > * {
          width: 100%;
          margin: 0;
          overflow: hidden;
          line-height: 1.25em;
          text-overflow: ellipsis;
          &:last-child{
            font-size:.8em;
            color:var(--gray);
          }
        }
      }
    }
  }
}

</style>
<script type="text/babel">


import PageNav from "@/js/main/components/PageNav";
import MaterialInput from "@/js/main/components/MaterialInput";
import Loader from "@/js/main/components/Loader";
import {ref} from "@vue/reactivity";
import {mapActions, mapGetters} from "vuex";
import {
  CLEAR_SUGGESTIONS,
  GET_SUGGESTIONS,
  IS_LOADING,
  RETRIEVE_SUGGESTIONS_HST,
  RETRIEVE_SUGGESTIONS_MIXED
} from "@/js/common/state/modules/common.types";
import {GET_HST} from "@/js/common/state/modules/hst.types";
import {GET_DESTINATION, GET_ORIGIN} from "@/js/common/state/modules/dtc.types";
import Utils from "@/js/common/Utils";

export default {
  name: 'asstart',
  components: {MaterialInput, PageNav,Loader},
  data(){
    return {

      currentTitle:'Von hier nach?',

      hst:null,
      selected:false,
      isTrain:false,
      cid:null,
      isBus:false,
      nearHst:[],
      usernear:[],
    }
  },
  props:{
    id:{
      type:String,
      required:true,
    }
  },
  setup() {
    let timeoutRef = null;
    const displayValue = ref("");
    const debouncedValue = ref("");

    const debounceListener = e => {
      if (timeoutRef !== null) {
        clearTimeout(timeoutRef);
      }
      displayValue.value = e.target.value;
      timeoutRef = setTimeout(() => {
        debouncedValue.value = e.target.value;
      }, 800);
    };

    return { debouncedValue, displayValue, debounceListener};
  },

  mounted() {

    if(typeof window.user_near !== 'undefined'){
      this.usernear = window.user_near;
    }
  },
  computed:{
    ...mapGetters('common',{
      getSuggestions:GET_SUGGESTIONS,
      isLoading:IS_LOADING
    }),
    ...mapGetters('hst',{
      getHst:GET_HST
    }),
    ...mapGetters('dtc',{
        origin:GET_ORIGIN,
        destination:GET_DESTINATION,
    })
  },
  watch:{
    debouncedValue(val){
      this.search(val);
    }
  },
  methods:{


    ...mapActions("common",{
      clear:CLEAR_SUGGESTIONS,
      retrieveCities:RETRIEVE_SUGGESTIONS_MIXED,
      retrieveHst:RETRIEVE_SUGGESTIONS_HST
    }),
    setSuggestion(i){


      const item = this.getSuggestions[i];

      let url;
      if(this.isTrain){
        url = Utils.getDBLink(this.origin.name,item.name);
      }else{
        url = Utils.getDBLink(this.getHst,item.name);
      }


      this.$router.push({ name: 'redirect', params: { url:btoa(url) }});

    },
    search(val){
      if(!this.isTrain){
        this.retrieveHst(val);
      }else {
        this.retrieveCities(val);
      }
    },
    getIcon(type){
      switch(type){

        case 'station':
          return 'train';
        default:
          return 'home';
      }

    },



  }
}
</script>