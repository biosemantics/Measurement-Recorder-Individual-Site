<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            {{ character_name }} is measure ...
        </div>
        <div class="col-md-12">
            As: <input class="child-model" style="width: 100%;" v-model="childData[2]" v-on:change="handleDataFc()"/>
        </div>
        <div class="col-md-12 text-center">
            Or
        </div>
        <div class="col-md-12">
            From: <input class="child-model" style="width: 100%;" v-model="childData[0]" v-on:change="handleDataFc()"/>
        </div>
        <div class="col-md-12">
            To: <input class="child-model" style="width: 100%;" v-model="childData[1]" v-on:change="handleDataFc()"/>
        </div>
    </div>
</template>

<script>

    export default {
        data: function () {
            return {
                childData: [],
                character_name: null
            }
        },
        props: {
            parentData: {
                type: Array,
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
            this.character_name = sessionStorage.getItem("characterName");
            console.log("parentData", this.parentData);
            this.childData = this.parentData; // save props data to itself's data and deal with it

        },
        mounted() {
        }
    }
</script>

