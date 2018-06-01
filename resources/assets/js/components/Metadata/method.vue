<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            Method
        </div>
        <div class="col-md-12">
            <input class="child-model" style="width: 100%;" v-model="childData" v-on:change="handleDataFc()"/>
        </div>
    </div>
</template>

<script>

    export default {
        data: function () {
            return {
                childData: ''
            }
        },
        props: {
            parentData: {
                type: String,
                default () {
                    return ''
                }
            }
        },
        methods: {
            // maybe onchagne may onclick whatever..
            handleDataFc: function () {
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            }
        },
        beforeMount () {
            this.childData = this.parentData; // save props data to itself's data and deal with it

        },
        mounted() {
        }
    }
</script>

