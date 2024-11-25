<template>
  <div class="page" >
    <div class="page-content">
      <page-nav :check="false" :title="'Gleich geht es weiter...'"/>
      <div class="container">
      <label class="small" style="margin-top:70px;">Einen Augenblick bitte...</label>


        <div ref="dtc"  :id="rand()" class="restDtc mt-2 mb-2" data-load="SIZE_RECT_BIG"></div>




        <a :href="getUrl" class="btn btn-label d-block btn-primary" :class="((timeout - ellapsed)>0)?'disabled':''">
          <label>   <mdicon name="chevron-right" /></label>
          {{ ((timeout - ellapsed)>0)?'Einen Moment bitte ('+ (timeout - ellapsed)+')':'Weiter zum Ergebnis'  }}
        </a>
      </div>
    </div>
  </div>
</template>




<script>

import PageNav from '@/js/main/components/PageNav';
import {getHstLink} from "@/js/common/api/local";
import {GET_HST, GET_HST_CITY_OBJECT} from "@/js/common/state/modules/hst.types";
import {mapGetters} from "vuex";
import {GET_LINK} from "@/js/common/state/modules/dtc.types";
import dtc from "@/js/common/dtc";
export default {
  mixins:[dtc],
  name: 'redirect',
  props:{
    timeout:{
      type:Number,
      required:false,
      default: 5,
    },
    url:{
      type:String,
      required:true,
    },
    fetch:{
      type:String,
      required:false,
      default:null,
    }
  },
  components: {PageNav},
  computed:{
    ...mapGetters("hst",{
      hstCity:GET_HST_CITY_OBJECT,
      hst:GET_HST
    }),
    ...mapGetters("dtc",{
      getLink:GET_LINK
    }),
    getUrl(){
      if(this.fetch!==null && this.fetched_url!==null){
        return this.fetched_url;
      }else{
        return atob(this.url);
      }
    }
  },
  mounted(){


     switch(this.fetch){
       case "hst":
         this.fetchHst();
         break;
       case "result":
         this.fetchResult();
         break;
       default:
         break;
     }

     const t =  window.setInterval(()=>{
        this.ellapsed++;
        if(this.ellapsed >= this.timeout){
          window.clearInterval(t);
        }
      },1000);

  },

  methods:{
    fetchResult(){

      let res = this.$router.resolve({name:'result'});

      this.fetched_url = "#"+res.fullPath;
    },
    fetchHst(){
      getHstLink({city:this.hstCity,hst:this.hst}).then((d)=>{
        this.fetched_url = d.url;
      });
    }
  },
  data(){
      return {
        ellapsed:0,
        fetched_url:null,
      }
  }
}
</script>
