<template>
  <div  :class="getClasses" class="input-group mb-3">
    <div class="value input varioForm-input" :class="getClasses">{{ getValue }}</div>
    <div class="input-group-append">
                   <span class="input-group-text">
                     <svg role="img"><use v-bind:xlink:href="icon"   xmlns:xlink="http://www.w3.org/1999/xlink"></use></svg>
                   </span>
    </div>
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
        default:'#varioHome'
      },
      value:{
        default:null
      },
      valid:{
        type:Boolean,
        default:true
      }
    }
  }
</script>