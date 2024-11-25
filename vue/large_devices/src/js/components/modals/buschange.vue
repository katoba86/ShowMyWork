<template>
    <div class="fbb-modal-content">

        <div class="modal-body p-4">
          <button class="close" @click="$emit('close')">X</button>
            <div class="pl-1 pt-1">
                <h5>Umsteigemöglichkeiten</h5>
            </div>
            <div class="table-responsive">
            <table class="table table-responsive w-100 d-block d-md-table table-sm table-striped">
                <tbody>
                <tr  v-bind:key="'r'+index" v-for="(linie,index) in data">
                    <td>{{ linie.linie}}</td>
                    <td>{{ linie.ziel}}</td>
                    <td><a  :href="linie.url"><i class="lnr-link"></i> Details</a></td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>


    </div>
</template>
<script type="text/babel">



    import {getInterchange} from "@/js/api/local";

    export default {
        props: {
            id: {
                type: String,
                required: true,
            }
        },
        created() {

            getInterchange(this.id).then((res) => {
                this.data = res.data;
            });
        },

        name: 'buschange',


        methods: {},
        data() {
            return {
                isLoading: true,
                data: [],
            }
        }
    }
</script>
