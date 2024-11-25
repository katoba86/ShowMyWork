<template>
  <div class="page" >
    <div class="page-content">
      <page-nav :check="false" :title="'Buslinien der Haltestelle'"/>



        <div style="margin-top:70px">

        <ul class="list-group list-group-flush" >

          <li class="list-group-item d-flex justify-content-between" v-bind:key="'r'+index" v-for="(linie,index) in list">
            <div><strong>{{ linie.linie}}</strong></div>
            <div>{{ linie.ziel}}</div>
            <div><a  :href="linie.url"><i class="material-icons">chevron_right</i></a></div>
          </li>

        </ul>

        </div>

    </div>
  </div>
</template>




<script>

import PageNav from '@/js/main/components/PageNav';
import {getInterchange} from "@/js/common/api/local";
import {SET_ERROR} from "@/js/common/state/modules/common.types";
import {mapActions} from "vuex";
export default {
  name: 'linie',
  props:{
    id:{
      type:Number,
      required:true,
    },
  },
  components: {PageNav},
  mounted(){
    getInterchange(this.id).then((data)=>{
      this.list = data.data;
    }).catch((error)=>{
      console.log(error);
        this.setError(true);
    });
  },

  methods:{
      ...mapActions("common",{
        setError:SET_ERROR
      })
  },
  data(){
      return {
        list:[]
      }
  }
}
</script>