import {getDtcCode} from "@/js/api/local";
import {mapGetters} from "vuex";
import {IS_BLOCKED} from "@/js/state/modules/common.types";

export default {

    computed:{
      ...mapGetters("common",{
          block:IS_BLOCKED
      })
    },
    methods: {


        loadBlock(target,size){

            console.log('load block');
            const f = (size.includes('_HOR_'))?'omioh.png':'omioq.png';
            target.innerHTML = `<a href='https://omio.sjv.io/c/2548737/568106/7385' target='_blank'><img src='/desktop/images/${f}' alt='Image'></a>`;
        },

        notify(id = null) {

            let target = (id === null)?this.$refs.dtc:document.getElementById(id);
            const size = target.getAttribute('data-load');

            if(this.block){
                this.loadBlock(target,size);
                    return;
            }

            getDtcCode(size,window.md,true).then((data)=>{
                target.innerHTML = data;
               //this.init(data);

            });

        },

        init(data,refresh = true){
            console.log("Triggered");
            let frag = document.createRange().createContextualFragment(data);
            const targetId = frag.firstChild.getAttribute('id');
            if(refresh) {
                if (window.md === 1) {
                    try {
                        googletag.cmd.push(() => {
                            googletag.display(targetId);
                        });
                    } catch (e) {
                        return false;
                    }

                } else {
                    try {
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    } catch (e) {
                        return false;
                    }
                }
            }
        }
    }
}
