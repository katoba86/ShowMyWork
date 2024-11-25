<template>
    <div class="page" >
        <div class="page-content">
          <page-nav :title="headline"/>
            <section class="container" style="margin-top:70px;">
                <div class="searchWrapper" id="fromCitySearch">
                  <MaterialInput :placeholder="placeholder" :inputFunc="debounceListener" :value="displayValue"></MaterialInput>
                </div>
            </section>


           <div v-if="isLoading">
             <Loader/>
           </div>


          <div v-if="getSuggestions.length === 0 || displayValue.length<=2">
            <div :id="cid" class="restDtc  text-center mt-5" data-load="SIZE_RECT_BIG"></div>
          </div>

            <ul  class="search-list" v-if="getSuggestions.length > 0 && displayValue.length>2">
              <li  @click="setSuggestion(index)" v-for="(result,index) in getSuggestions" :result="result" :key="'slotSuggest'+index"  class="search-list-item">
                <div class="search-list-item-content suggestionList">
                  <mdicon  :name="getIcon(result.type)" />
                  <div class="search-list-item-text" v-if="('bundesland' in result)">
                    <span>{{ result.name }}</span>
                    <span>Bundesland: {{  result.bundesland.name }}</span>
                  </div>
                  <span class="search-list-item-text"  v-if="!('bundesland' in result)">{{ result.name}}</span>
                  <mdicon name="chevron-right" />
                </div>
              </li>
            </ul>



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
        .mdi{
          margin-right:32px;
          &.mdi-chevron-right{
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


<script>

    import {mapActions, mapGetters} from 'vuex';
    import Loader from '@/js/main/components/Loader';
    import PageNav from '@/js/main/components/PageNav';
    import MaterialInput from "@/js/main/components/MaterialInput";
    import {ref} from "@vue/reactivity";

    import {
      GET_SUGGESTIONS,
        CLEAR_SUGGESTIONS,
      IS_LOADING,
      RETRIEVE_SUGGESTIONS_HST,
      RETRIEVE_SUGGESTIONS_MIXED
    } from "@/js/common/state/modules/common.types";
    import {getModalHeadline, getPlaceholder} from "@/js/common/Text";
    import {GET_HST} from "@/js/common/state/modules/hst.types";
    import dtc from "@/js/common/dtc";

    export default {
        name: 'citySelect',
      components: {MaterialInput, PageNav,Loader},
      mixins:[dtc],

      mounted() {
        this.clear();
        this.notify(this.cid)
      },
      setup(props) {
        let timeoutRef = null;
        const displayValue = ref("");
        const debouncedValue = ref("");
        const headline = ref("");
        const placeholder = ref("");
         headline.value = getModalHeadline(props.ns,props.type.toUpperCase());
         placeholder.value = getPlaceholder(props.ns,props.type.toUpperCase());

        const debounceListener = e => {
          if (timeoutRef !== null) {
            clearTimeout(timeoutRef);
          }
          displayValue.value = e.target.value;
          timeoutRef = setTimeout(() => {
            debouncedValue.value = e.target.value;
          }, 800);
        };

        return { debouncedValue, displayValue, debounceListener,headline,placeholder};
      },
        props: {
            type: {
                type: String,
                required: true
            },
            ns: {
              type: String,
              required: true
            },
        },
      beforeMount() {

        // eslint-disable-next-line no-unused-vars
        this.cid = [...Array(8)].map(i => (~~(Math.random() * 36)).toString(36)).join('');
      },
      computed: {
          ...mapGetters('common',{
            getSuggestions:GET_SUGGESTIONS,
            isLoading:IS_LOADING
          }),

        },
        watch:{
          debouncedValue(val){
            this.search(val);
          }
        },
        methods: {

          ...mapActions("common",{
              clear:CLEAR_SUGGESTIONS,
              retrieveCities:RETRIEVE_SUGGESTIONS_MIXED,
              retrieveHst:RETRIEVE_SUGGESTIONS_HST
          }),
          setSuggestion(i){

            let dispatch = this.ns+"/"+this.type.replace("GET_","SET_");
            const item = this.getSuggestions[i];
            this.$store.dispatch(dispatch,item);
            this.$router.back();
          },
          search(val){
            if(this.type === GET_HST){
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
        },

    }
</script>


