<template>

  <div class="inputWrapper input-big" :class="getClasses">
    <label>{{ label }} {{ (!valid)?' - '+error:''}}</label>
    <div class="input" :class="getClasses">{{ getValue}}</div>
    <div class="icon icon-big" :class="icon"></div>
    <div class="error">{{ error }}</div>
  </div>
</template>
<script type="text/babel">
  export default {
    computed:{
      getValue(){

        return (typeof this.value === 'string')?this.value:this.value.name;
      },
      getClasses(){
        let c = [];
        c.push(!(this.valid)?'is-invalid':'valid');
        if(this.disabled){c.push('disabled');}
        return c;
      }
    },
    props:{
      disabled:{
        type:Boolean,
        default:false
      },
      icon:{
        type:String,
        default:'home'
      },
      value:{
        default:null
      },
      error:{
        type:String,
        default:null
      },
      label:{
        type:String,
        default:null
      },
      valid:{
        type:Boolean,
        default:true
      }
    }
  }
</script>