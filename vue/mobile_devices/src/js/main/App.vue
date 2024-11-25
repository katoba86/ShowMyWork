<template>
  <div id="appContext">



    <div id="globalError" v-if="hasError" class="alert alert-danger fixed-top" role="alert">
      <strong>FEHLER!</strong> Bei der Abfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut!
      <button @click="hideError()" type="button" class="close" data-dismiss="alert" aria-label="Close"  style="position: absolute;top: 12px;right: 20px;">
        <span aria-hidden="true">×</span>
      </button>
    </div>


      <div v-if="isLoading" class="progress indeterminate progress-bar-animated">
        <div class="indeterminate"></div>
      </div>

    <nav>
      <ul class="tabs" role="menu">
        <router-link v-slot="{ navigate,isActive }" custom to="/">
          <li :class="[isActive && 'router-link-exact-active']" role="menuitem" @click="navigate"
              @keypress.enter="navigate">Stadt zu Stadt
          </li>
        </router-link>
        <router-link v-slot="{ navigate,isActive }" custom to="/hst">
          <li :class="[isActive && 'router-link-exact-active']" role="menuitem" @click="navigate"
              @keypress.enter="navigate">
            Haltestellen
          </li>
        </router-link>
      </ul>
    </nav>


      <router-view v-slot="slotProps">
        <transition  name="slide-fade"   mode="out-in">
          <component :is="slotProps.Component"></component>
        </transition>
      </router-view>


  </div>
</template>
<style lang="scss">
@import "../../scss/theme";

header.dtc-header{
  &:after{
    background-image: url(./images/bg_top.webp);
  }
}

%elevation_medium{
  box-shadow:0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
}
%card-border-radius_medium{
  border-radius:$card-border-radius;
}
$card-padding:12px;
 @import "../../scss/rewrite/aboveFold";

.slide-fade-enter-active,.slide-fade-leave-active  {
  transition: all 0s ease;
}
.slide-fade-enter-from, .slide-fade-leave-to
  /* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(100px);
  opacity: 0;
}
.slide-fade-enter-to, .slide-fade-leave-from
  /* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(0px);
  opacity: 1;
}


</style>
<script>

import {HAS_ERROR, PAGE_LOADING} from "@/js/common/state/modules/common.types";
import { mapGetters} from "vuex";
import {GET_HST} from "@/js/common/state/modules/hst.types";
export default {

  computed:{
   ...mapGetters("common",{
     isLoading:PAGE_LOADING,
     hasError:HAS_ERROR
   }),
    ...mapGetters("hst",{
      hst:GET_HST
    })
  },
  data(){
    return {
      hstForm:false,
    }
  },
  watch:{
    hst(){
      if(this.hstForm) {
        this.hstForm = false;

        setTimeout(()=>{

          this.emitter.emit("hstSubmit");
        },200)

      }
    }
  },
  methods:{


    afterEnter(el){
      console.log("After enter",el);
      console.log(this.$refs.trans);
    },

    hideError(){
      document.getElementById('globalError').classList.add('d-none');
      this.$router.back();
    }
  },
  mounted() {

    this.emitter.on('changeLinie',(d)=>{
      this.$router.push({path: '/linie/'+d.id}).catch((e) => {
        console.log(e);
      });
    });


    this.emitter.on('asStart',(d)=>{
      this.$router.push({path: '/asstart/'+d.id}).catch(() => {
        console.log('Error');
      });
    });

    this.emitter.on('showInMap',(d)=>{
      this.$router.push({path: '/map/'+d.id}).catch(() => {
        console.log('Error');
      });
    });

    this.emitter.on('searchHst',()=>{
      this.hstForm = true;
      if(this.$router.currentRoute.value.name==='hst'){
        this.$router.push({path: '/select', query: { type:'GET_HST',ns:'hst'}});
      }else{
        this.$router.push({path: '/hst'}).then(()=>{
          this.$router.push({path: '/select', query: { type:'GET_HST',ns:'hst'}});
        });
      }

    });
    window.i1 = window.setInterval(()=>{
      const event = new CustomEvent("loaded");
      window.dispatchEvent(event);
    },100);

  }


}
</script>