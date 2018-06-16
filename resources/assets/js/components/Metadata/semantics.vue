<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            Semantics
        </div>
        <!--<div class="col-md-12">-->
            <!--<input style="width: 100%;" v-model="childData" @change="handleDataFc()"/>-->
            <!--{{ childData[0] }}<br>-->
            <!--{{ childData[1] }}<br>-->
        <!--</div>-->
        <div class="col-md-12">
            Semantic for Measure:
            <select style="width: 100%;" v-model="measureSemantic" @change="handleDataFc(0)">
                <option v-for="item in measureArray" :value="item">{{item}}</option>
            </select>
        </div>
        <div class="col-md-12">
            Semantic for Entity:
            <select style="width: 100%;" v-model="entitySemantic" @change="handleDataFc(1)">
                <option v-for="item in entityArray" :value="item">{{item}}</option>
            </select>
        </div>
    </div>
</template>

<script>

    export default {
        data: function () {
            return {
                childData: [],
                measureSemantic: '',
                entitySemantic: '',
                measureArray: [],
                entityArray: [],
                test: ['one', 'two', 'three'],
            }
        },
        props: {
            parentData: {
                type: Array,
                default () {
                    return []
                }
            }
        },
        methods: {
            // maybe onchagne may onclick whatever..
            handleDataFc: function (id) {
                if (id == 0) {
                    this.childData[0] = this.measureSemantic;
                } else if (id == 1) {
                    this.childData[1] = this.entitySemantic;
                }
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            }
        },
        beforeMount () {
            this.childData = this.parentData; // save props data to itself's data and deal with it

            var app = this;
            console.log("child Data", this.childData);

            if (this.childData.length > 3) {
                this.measureSemantic = this.childData[2];
                this.entitySemantic = this.childData[3];
            }

            axios.get('http://shark.sbs.arizona.edu:8080/PO/search?term=' + app.childData[1])
                .then(function (resp) {
                    console.log("po resp", resp);
                    for (var i = 0; i < resp.data.entries.length; i++) {
                        for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                            if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                app.entityArray.push(resp.data.entries[i].resultAnnotations[j].value);
                            }
                        }
                    }
                    axios.get('http://shark.sbs.arizona.edu:8080/CAREX/search?term=' + app.childData[1])
                        .then(function (resp) {
                            console.log("carex resp", resp);
                            for (var i = 0; i < resp.data.entries.length; i++) {
                                for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                    if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                        app.entityArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                    }
                                }
                            }
                            axios.get('http://shark.sbs.arizona.edu:8080/PATO/search?term=' + app.childData[0])
                                .then(function (resp) {
                                    console.log("po resp", resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                            if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                app.measureArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                            }
                                        }
                                    }
                                    axios.get('http://shark.sbs.arizona.edu:8080/CAREX/search?term=' + app.childData[0])
                                        .then(function (resp) {
                                            console.log("carex resp", resp);
                                            for (var i = 0; i < resp.data.entries.length; i++) {
                                                for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                                    if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                        app.measureArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                                    }
                                                }
                                            }
                                        })
                                        .catch(function (resp) {

                                        });
                                })
                                .catch(function (resp) {

                                });
                        })
                        .catch(function (resp) {

                        });
                })
                .catch(function (resp) {

                });
        }
    }
</script>

