<style>


</style>


<template>

  <div class="nav bg-primary fixed-top d-flex align-items-center">

    <a @click="$router.go(-1)" class="align-self-baseline">
      <svg class="back" role="img" aria-label="zurück">
        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#chevron"></use>
      </svg>
    </a>
    <div class="text-center flex-grow-1 fw-500 text-white" >{{ title }}</div>
    <a v-if="check" @click="$router.go(-1)" class="align-self-baseline">
      <mdicon name="check" />
    </a>
  </div>


</template>
<script>

export default {
  methods:{

  },
  mounted() {

      let item = document.querySelector('meta[name=theme-color]');
      if(null!==item) {
        item.remove();
      }
      let mod = document.createElement('meta');
      mod.name="theme-color";
      mod.content="#298089";
      document.getElementsByTagName('head')[0].appendChild(mod);
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    check: {
      type:Boolean,
      required:false,
      default: false
    }
  },
}


</script>
