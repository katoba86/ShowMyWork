
import {getDtcCode} from "@/js/common/api/local";


export default {




    methods: {



        rand(){
            // eslint-disable-next-line no-unused-vars
            return [...Array(5)].map(i => (~~(Math.random() * 36)).toString(36)).join('');
        },

        loadBlock(target,size){


            const f = (size.includes('_HOR_'))?'sh.jpg':'sq.gif';
            target.innerHTML = `<a href='https://amzn.to/3lkPcLp' target='_blank'><img src='/desktop/images/${f}' alt='Image'></a>`;
        },

        notify(id = null) {


            let target = (id === null)?this.$refs.dtc:document.getElementById(id);
            const size = target.getAttribute('data-load');


            getDtcCode(size,window.md,true).then((data)=>{
                target.innerHTML = data;
               //this.init(data);

            });

        },

        init(data){
            let frag = document.createRange().createContextualFragment(data);
            const targetId = frag.firstChild.getAttribute('id');

            if(window.md===1) {
                try {
                    // eslint-disable-next-line no-undef
                    googletag.cmd.push(() => {
                        // eslint-disable-next-line no-undef
                        googletag.display(targetId);
                    });
                }catch(e){
                    return false;
                }

            }else{
                try {

                    // eslint-disable-next-line no-undef
                    (adsbygoogle = window.adsbygoogle || []).push({});
                }catch(e){
                    console.error(e);
                    return false;
                }
            }

        }
    }
}
