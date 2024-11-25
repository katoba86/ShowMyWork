import {EventBus, EVENTBUS_QUICKJOURNEY, EVENTBUS_REDIR, EVENTBUS_SHOW_DATE} from "@/js/eventbus";
import Sticky from "sticky-js/src/sticky";
import SuggestForm from "@/js/outside/SuggestForm";


export default class Outside{



    loadAds(){
        this.l=true;
        let elements = document.getElementsByClassName('ads_defer_by_google');
        console.log(elements);
        Array.from(elements).forEach((t) =>{

            t.classList.remove('ads_defer_by_google');
            t.classList.add('adsbygoogle');
            (adsbygoogle = window.adsbygoogle || []).push({});

        });
    }

    constructor(v) {
        this.l = false;
        if(window.md === 0) {

            setTimeout(()=>{
                if(!this.l) {
                    this.loadAds();
                }
            },500);

            /*document.addEventListener('scroll', () => {
                if(!this.l) {
                    this.loadAds();
                }
            }, {
                once: true,
                passive: true
            }); */
        }

        if (typeof window.topFormData !== 'undefined') {
            if (typeof window.topFormData.from !== 'undefined') {

                let element = document.getElementById('hstFormBottom');

                if (typeof (element) !== 'undefined' && element != null) {
                    let sf1 = new SuggestForm();
                    sf1.useCid = true;
                    sf1.cid = window.topFormData.from.identifer;
                    sf1.type = 'hst';
                    sf1.initForForm('hstFormBottom');

                }

                let element2 = document.getElementById('linienFormBottom');
                if (typeof (element2) !== 'undefined' && element2 != null) {
                    let sf = new SuggestForm();
                    sf.useCid = true;
                    sf.cid = window.topFormData.from.identifer;
                    sf.type = 'linie';
                    sf.initForForm('linienFormBottom');
                }


            }
        }



        this.isBlocked = false;
        try {
            this.isBlocked = !v.$store.state.common.block
        }catch(e){
            this.isBlocked = false;
        }

        let element =  document.getElementById('map');
        if (typeof(element) != 'undefined' && element != null)
        {
            this.loadLinie().then(()=>{


                let elements = document.getElementsByClassName('action');
                Array.from(elements).forEach((t) =>{
                    t.addEventListener('click', (e)=>{
                        e.preventDefault();
                        const id = t.getAttribute('data-id');
                        const action = t.getAttribute('data-trigger');
                        if(action===null){return true;}

                        EventBus.$emit(action, id);
                        return false;

                    });
                });
            });
        }


    }
    check(){

        let s = new Sticky('.stick');
        setTimeout(()=>{

            if(this.isBlocked) {
                let elems = document.getElementsByClassName('adsbygoogle');
                Array.from(elems).forEach((elem) => {
                    if (elem.clientWidth === 0 && elem.id !== "") {
                        let f = null;
                        switch (elem.id) {
                            case 'V3_Above_Fold/V3_Above_Fold-Desktop':
                                f = "omioh.png";
                                break;
                            case 'V3_Vertical-Desktop':
                                f = "omioq.png";
                                break;
                            default:
                                break;
                        }
                        if (f !== null) {
                            elem.classList.remove('adsbygoogle');
                            elem.innerHTML = `<a href='https://omio.sjv.io/c/2548737/568106/7385' target='_blank'><img src='/desktop/images/${f}' alt='Image'></a>`;
                        }
                    }
                });
            }
            s.update();
        },2000);
    }


    async loadLinie(){
        await import(/* webpackChunkName: "linie" */ './Linie.js');
    }


    initEvents(){

            let elements  = null;

            elements = document.getElementsByClassName('quickJourney');

            Array.from(elements).forEach((t) =>{
                t.addEventListener('click', (e)=>{
                e.preventDefault();

            const origin = t.getAttribute('data-origin');
            const destination = t.getAttribute('data-destination');
            const oT = t.getAttribute('data-origin-type');
            const dT = t.getAttribute('data-destination-type');
            const dTN = t.getAttribute('data-destination-name');
            const oTN = t.getAttribute('data-origin-name');

            let p = {
                action:{"origin":{type:oT,id:origin,identifer:origin,name:oTN},"destination":{type:dT,id:destination,identifer:destination,name:dTN}},
                event:e
            };
            EventBus.$emit(EVENTBUS_QUICKJOURNEY,p);
            return false;
        })});

        if(!document.getElementById('body').classList.contains('linie')) {
            let items = document.querySelectorAll('.action');
            items.forEach(
                (elem) => {
                    elem.addEventListener('click', (e) => {

                        e.preventDefault();
                        let t = e.currentTarget;
                        let id = t.getAttribute('data-id');
                        let action = t.getAttribute('data-trigger');

                        EventBus.$emit(action, id);

                        return false;

                    }, true);
                }
            );
        }

        elements = document.getElementsByClassName('setDate');
        Array.from(elements).forEach((element) =>{
            element.addEventListener('click', (e)=>{
                const p = {
                    target:element.getAttribute('data-field'),
                    allow:element.getAttribute('data-allow'),
                    event:e
                };
                EventBus.$emit(EVENTBUS_SHOW_DATE,p);
                e.preventDefault();
                return false;
            });
        });



        return this;
    }

}
