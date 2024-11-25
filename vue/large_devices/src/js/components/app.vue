<template>
    <div id="app">


        <transition name="slide-fade" mode="out-in">
            <router-view :key="$route.fullPath"></router-view>
        </transition>


      <datetime ref="dselect" :setDate="setDate"></datetime>

    </div>
</template>
<style lang="scss">
    @import '../../scss/app';
</style>

<script type="text/babel">

  import formatISO from 'date-fns/formatISO';
  import addDays from 'date-fns/addDays';
  import AdDetect from './mixins/adDetect';
  import {mapActions} from "vuex";
  import {SET_BLOCK, SET_RDR} from "@/js/state/modules/common.types";
  import store from "@/js/state/store";
  import {SET_DESTINATION, SET_ORIGIN} from "@/js/state/modules/dtc.types";
  import {SET_HST, SET_HST_CITY} from "@/js/state/modules/hst.types";
  import {EventBus, EVENTBUS_REDIR, EVENTBUS_SHOW_DATE} from "@/js/eventbus";
  import Redir from "@/js/components/modals/redir";
  import BusChange from "@/js/components/modals/buschange";
  import AsStart from "@/js/components/modals/asstart";


  const dt = import('./test');

  export default {
  name: 'app',
  methods:{
    ...mapActions("common",{
      setBlock:SET_BLOCK,
      setRdr:SET_RDR
    },



    ),
    setDate(){
      const ret = this.$refs.dselect.$refs.maindate.$data.datetime;
      console.log(this.currentTarget);
      if(null===ret){return;}
      document.getElementById(this.currentTarget).value =  ret.toFormat('dd.LL.yyyy, HH:mm:00');
    }
  },
    components: {
      datetime: () => (
          {
            component:dt
          }
      ),
      //datetime: Datetime,
    },
    data()
    {
      return {
        currentTarget:null,
        date:null,
        startDate:null,
        minDate:formatISO(new Date()),
        maxDate:formatISO(addDays(new Date(),180)),
      }
    },
  mixins:[
    AdDetect
  ],
    mounted() {


      this.detectAdBlock().then((response)=>{
        if(response){
          this.setBlock(true);
        }else{
          this.setBlock(false);
        }
      });
      if(Math.random()<0) {
        this.setRdr(true);
      }

      if (null !== this.formData) {
        if (typeof this.formData.from !== 'undefined') {
          store.dispatch(["dtc",SET_ORIGIN].join("/"), this.formData.from);
          store.dispatch(["hst",SET_HST_CITY].join("/"), this.formData.from);

        }
        if (typeof this.formData.to !== 'undefined') {
          store.dispatch(["dtc",SET_DESTINATION].join("/"), this.formData.to);
        }
        if (typeof this.formData.toHst !== 'undefined') {
          store.dispatch(["hst",SET_HST].join("/"), this.formData.to);
        }
      }

      EventBus.$on('buschange',(id)=>{
        this.$modal.show(BusChange, {
          id:id
        },{
          maxWidth:800,
          width:800,
          scrollable:true,
          height:'auto'
        });
      });


      EventBus.$on('asstart',(id)=>{

        this.$modal.show(AsStart, {
          id:id
        },{
          maxWidth:1000,
          width:1000,
          scrollable:true,
          height:'auto'
        });
      });

      EventBus.$on(EVENTBUS_SHOW_DATE,(data)=>{
        this.currentTarget = data.target;

        this.maxDate = formatISO(addDays(new Date(),data.allow));
        this.$refs.dselect.$refs.maindate.open(data.event);
      });

      EventBus.$on(EVENTBUS_REDIR,(data)=>{
        this.$modal.show(Redir, {
          url:data.url
        },{
          maxWidth:900,
          width:900,
          scrollable:false,
          height:230
        });

      });
    }
  }

</script>





<style>
    .slide-fade-enter-active {
        transition: all .2s ease;
    }
    .slide-fade-leave-active {
        transition: all .2s cubic-bezier(.58,.47,.84,.5);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform:translateZ(800px) translateX(-500px);

        opacity: 0;
    }
</style>
