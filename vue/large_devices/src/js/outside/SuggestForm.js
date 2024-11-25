
import {getLocalSuggest} from "@/js/api/local";

export default class SuggestForm{
    constructor(){
        this.baseUrl = 'https://fahrplan-bus-bahn.de/rest/suggest/';
        this.type = 'hst';
        this.currentId = '';
        this.useCid = false;
        this.cid = null;
        this.direction = false;
        this.from = null;
        this.target = null;
    }

    initForForm(id){
        this.currentId = id;
        this.target=document.getElementById(id);


        this.cid = (this.useCid)?this.target.getAttribute('data-cid'):null;

        let fromCheck = this.target.getAttribute('data-from');

        if(typeof fromCheck !=='undefined'){
            this.from = fromCheck;
            this.direction = true;
        }


        this.target.querySelector('.formInput').addEventListener('keyup',this.debounce(this.doSearch,500));
    }


    debounce(func, wait, immediate) {
        let timeout;
        return ()=>{
            let context = this, args = arguments;
            let later = ()=>{
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            let callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow){
                func.apply(context, args);
            }
        };
    }

    static buildLink(from, to){
        let d = new Date();

        let lng = 126;
        d.setTime(d.getTime() + 10 * 60 * 1000); // +10Min.
        let time = d.getHours() + ":" + d.getMinutes();
        let date = d.getDate() + "." + (d.getMonth() + 1) + "." + d.getFullYear();
        let url="https://partners.webmasterplan.com/click.asp?ref=715105&site=2894&type=text&tnb="+lng+"&prd=yes";
        //var url = "http://reiseauskunft.bahn.de/bin/query.exe/dn?";
        url += "S=" + encodeURIComponent(from);
        url += "&Z=" + encodeURIComponent(to);
        url += "&date=" + encodeURIComponent(date);
        url += "&time=" + encodeURIComponent(time);
        url += "&start=1";


        return url;
    }

    doSearch(){

       let input = document.querySelector('#'+this.currentId+' .formInput').value;
        let p = (this.type === 'hst')?{
            search:input,restrict:this.cid,type:this.type
        }:{
            search:input,cid:this.cid,type:this.type
        };

            getLocalSuggest(p).then((res)=>{
                let lgc = document.querySelector('#'+this.currentId+' .list-group-check');
                let content = "";
                res.forEach((i)=>{
                    content=content+"<a href='"+i.url+"' class='list-group-item pointer'>" + i.name + "</a>";
                });
                lgc.innerHTML = content;


            });
    }





}
