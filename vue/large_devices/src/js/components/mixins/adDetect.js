export default {
    methods: {
        detectAdBlock(){

            return new Promise((resolve) => {
                const element = document.createElement("div");
                element.classList.add('adsbyyahoo');
                element.style.cssText = 'height: 1; width: 1; background-color: transparent';
                element.innerText="";
                document.body.appendChild(element);
                window.setTimeout(()=>{
                    if(document.querySelector('.adsbyyahoo').clientHeight === 0){
                        resolve(true)
                    }else{
                        resolve(false)
                    }
                    //document.body.removeChild(element);
                }, 20)
            });
        }
    }
};
