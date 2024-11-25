<template>
  <div class="page hst">
    <div class="page-content">
      <page-nav :title="title"></page-nav>

      <Loader v-if="isLoading"></Loader>

      <gmap v-if="(hst!==null)" :detected="[this.hst]" :lat="lat" :lon="lon" style="margin-top:40px"/>



    </div>
  </div>
</template>

<script>

import PageNav from "@/js/main/components/PageNav";
import {GET_HST_BY_ID, IS_LOADING} from "@/js/common/state/modules/common.types";
import {mapActions, mapGetters} from "vuex";
import Loader from "@/js/main/components/Loader";
import {defineAsyncComponent} from "@vue/runtime-core";

export default {
  components: {
    Loader,
    PageNav,
    gmap: defineAsyncComponent(() =>
        import('@/js/main/components/vmap.vue')
    )
  },
  computed:{
    ...mapGetters("common",{
      isLoading:IS_LOADING
    })
  },
  methods:{
    ...mapActions("common",{
        getHst:GET_HST_BY_ID
    }),
    test(){

    }
  },
  mounted() {
    this.getHst(this.$route.params.id).then((data)=>{
      this.hst = data.data;
      this.mapLoaded = true;
      this.title = this.hst.name;
    });

  },

  data(){
    return {
      mapLoaded:false,
      title:'Einen Moment bitte...',
      hst:null,
      lat:51,
      lon:7
    }
  }




}


</script>
