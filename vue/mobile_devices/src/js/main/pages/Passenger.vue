<template>
  <div class="page" >
    <div class="page-content">
      <page-nav :check="true" :title="'Wer fährt mit?'"/>


      <div class="container">
      <label class="small" style="margin-top:70px;">Personen auswählen</label>



      <div class="passenger-input d-flex  flex-row justify-content-between" v-for="(val,key) in passengerX" v-bind:key="'p'+key">
        <div class="p-2 font-weight-bold">{{ passengerStr[key] }}</div>
        <div class="ml-auto d-flex flex-row p-2 number-spinner">
          <div  class="btn btn-xs  btn-primary" @click="dec(key)">

            <mdicon name="minus" />
          </div>
          <div class="ml-2 mr-2 font-weight-bold" style="font-size:1.1em">{{ val }}</div>
          <div  class="btn btn-xs  btn-primary" @click="inc(key)"> <mdicon name="plus" /></div>
        </div>
      </div>

      </div>

    </div>
  </div>
</template>


<style lang="scss">
.passenger-input{
  &:first-of-type{
    margin-top:0;
  }
  margin-top:1em;
}
.number-spinner{
  .btn{
    padding:4px 4px 2px 4px;
    height:26px;
    width:29px;
    >span{
      position:relative;
      top:-2px;
      svg{
        width:20px;
        height:20px;
      }
    }
  }
}

</style>

<script>
import {mapActions, mapGetters} from 'vuex';
import PageNav from '@/js/main/components/PageNav';
import {passenger} from '@/js/common/Text';
import {GET_PASSENGER, SET_PASSENGER} from "@/js/common/state/modules/dtc.types";


export default {
  name: 'citySelect',
  components: {PageNav},
  computed:{
    ...mapGetters("dtc",{
      passengerX:GET_PASSENGER
    }),
  },
  created() {
    this.passengerStr = passenger.p;
  },
  methods:{
    ...mapActions("dtc",{
      setPassenger:SET_PASSENGER
    }),
    inc(key){
      this.setPassenger({who:key,type:true});
    },
    dec(key){
      this.setPassenger({who:key,type:false});
    }
  }
}
</script>
