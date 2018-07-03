<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            Semantics
        </div>
        <div class="col-md-12">
            <b>Semantic for Measure {{ character_name[0] }}:</b>
            <div v-if="measureArray.length > 0">
                <select style="width: 100%;" v-model="measureSemantic" @change="handleDataFc(0)">
                    <option v-for="item in measureArray" :value="item">{{item}}</option>
                </select>
                <div v-if="measureDetails != null">
                    Label: {{ measureDetails.term }}<br>
                    Matching Score: {{ measureDetails.score }}<br>
                    Direct Superclass: {{ measureDetails.parentTerm }}<br>
                    Object Properties:<br>
                    <div v-if="objectProperty != null" v-for="entry in objectProperty">
                        <div v-for="item in entry.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/BFO_0000051' && item.value !=''">
                            &nbsp;&nbsp;&nbsp;Has Part: {{ item.value }}
                        </div>
                        <div v-for="item in entry.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/BFO_0000050' && item.value !=''">
                            &nbsp;&nbsp;&nbsp;Part Of: {{ item.value }}
                        </div>
                    </div>
                    Annotation Properties:<br>
                    <div v-for="item in measureDetails.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/IAO_0000115' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Definition: {{ item.value }}
                    </div>
                    <div v-for="item in measureDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasExactSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Exact Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in measureDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasNarrowSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Narrow Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in measureDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasRelatedSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Related Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in measureDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasBroaderSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Broader Synonym: {{ item.value }}
                    </div>
                </div>
            </div>
            <div v-if="measureArray.length == 0">
                <input class="child-model" style="width: 100%;" v-model="measureSemantic" v-on:change="handleDataFc(0)"/>
            </div>
        </div>
        <div class="col-md-12">
            <b>Semantic for Entity {{ character_name[1] }}:</b>
            <div v-if="entityArray.length > 0">
                <select style="width: 100%;" v-model="entitySemantic" @change="handleDataFc(1)">
                    <option v-for="item in entityArray" :value="item">{{item}}</option>
                </select>
                <div v-if="entityDetails != null">
                    Label: {{ entityDetails.term }}<br>
                    Matching Score: {{ entityDetails.score }}<br>
                    Direct Superclass Score: {{ entityDetails.parentTerm }}<br>
                    Object Properties:<br>
                    <div v-if="objectProperty != null" v-for="entry in objectProperty">
                        <div v-for="item in entry.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/BFO_0000051' && item.value !=''">
                            &nbsp;&nbsp;&nbsp;Has Part: {{ item.value }}
                        </div>
                        <div v-for="item in entry.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/BFO_0000050' && item.value !=''">
                            &nbsp;&nbsp;&nbsp;Part Of: {{ item.value }}
                        </div>
                    </div>
                    Annotation Properties:<br>
                    <div v-for="item in entityDetails.resultAnnotations" v-if="item.property == 'http://purl.obolibrary.org/obo/IAO_0000115' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Definition: {{ item.value }}
                    </div>
                    <div v-for="item in entityDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasExactSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Exact Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in entityDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasNarrowSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Narrow Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in entityDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasRelatedSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Related Synonym: {{ item.value }}
                    </div>
                    <div v-for="item in entityDetails.resultAnnotations" v-if="item.property == 'http://www.geneontology.org/formats/oboInOwl#hasBroaderSynonym' && item.property.value != ''">
                        &nbsp;&nbsp;&nbsp;Broader Synonym: {{ item.value }}
                    </div>
                </div>
            </div>
            <div v-if="entityArray.length == 0">
                <input class="child-model" style="width: 100%;" v-model="entitySemantic" v-on:change="handleDataFc(1)"/>
            </div>

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
                measureDetailsArray: [],
                entityDetailsArray: [],
                measureDetails: null,
                entityDetails: null,
                character_name: null,
                objectProperty: null
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
                var app = this;
                if (id == 0) {
                    this.childData[0] = this.measureSemantic;
                    for (var i = 0; i < this.measureDetailsArray.length; i++) {
                        for (var j = 0; j < this.measureDetailsArray[i].resultAnnotations.length; j++) {
                            if (this.measureDetailsArray[i].resultAnnotations[j].property = 'http://www.geneontology.org/formats/oboInOwl#id') {
                                if (this.measureDetailsArray[i].resultAnnotations[j].value == this.measureSemantic) {
                                    this.measureDetails = this.measureDetailsArray[i];
                                    console.log("1", this.measureDetails);
                                }
                            }
                        }
                    }
                } else if (id == 1) {
                    this.childData[1] = this.entitySemantic;
                    for (var i = 0; i < this.entityDetailsArray.length; i++) {
                        for (var j = 0; j < this.entityDetailsArray[i].resultAnnotations.length; j++) {
                            if (this.entityDetailsArray[i].resultAnnotations[j].property = 'http://www.geneontology.org/formats/oboInOwl#id') {
                                if (this.entityDetailsArray[i].resultAnnotations[j].value == this.entitySemantic) {
                                    this.entityDetails = this.entityDetailsArray[i];
                                }
                            }
                        }
                    }
                }
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            }
        },
        beforeMount () {
            this.character_name = sessionStorage.getItem("characterName");
            this.character_name = this.character_name.split(' of ');
            this.childData = this.parentData; // save props data to itself's data and deal with it
            var app = this;
            console.log("child Data", this.childData);

            this.measureSemantic = this.childData[0];
            this.entitySemantic = this.childData[1];



            axios.get('http://shark.sbs.arizona.edu:8080/PO/search?term=' + app.character_name[1])
                .then(function (resp) {
                    console.log("po resp", resp);
                    for (var i = 0; i < resp.data.entries.length; i++) {
                        app.entityDetailsArray.push(resp.data.entries[i]);
                        for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                            if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                app.entityArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                if (resp.data.entries[i].resultAnnotations[j].value == app.entitySemantic) {
                                    app.entityDetails = resp.data.entries[i];
                                }
                            }
                        }
                    }
                    axios.get('http://shark.sbs.arizona.edu:8080/CAREX/search?term=' + app.character_name[1])
                        .then(function (resp) {
                            console.log("carex resp", resp);
                            for (var i = 0; i < resp.data.entries.length; i++) {
                                app.entityDetailsArray.push(resp.data.entries[i]);
                                for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                    if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                        app.entityArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                        if (resp.data.entries[i].resultAnnotations[j].value == app.entitySemantic) {
                                            app.entityDetails = resp.data.entries[i];
                                        }
                                    }
                                }
                            }
                            axios.get('http://shark.sbs.arizona.edu:8080/PATO/search?term=' + app.character_name[0])
                                .then(function (resp) {
                                    console.log("pato resp", resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        app.measureDetailsArray.push(resp.data.entries[i]);
                                        for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                            if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                app.measureArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                                console.log("meas")
                                                if (resp.data.entries[i].resultAnnotations[j].value == app.measureSemantic) {
                                                    app.measureDetails = resp.data.entries[i];
                                                }
                                            }
                                        }
                                    }
                                    axios.get('http://shark.sbs.arizona.edu:8080/CAREX/search?term=' + app.character_name[0])
                                        .then(function (resp) {
                                            console.log("carex resp", resp);
                                            for (var i = 0; i < resp.data.entries.length; i++) {
                                                app.measureDetailsArray.push(resp.data.entries[i]);
                                                for (var j = 0; j < resp.data.entries[i].resultAnnotations.length; j++) {
                                                    if (resp.data.entries[i].resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                        app.measureArray.push(resp.data.entries[i].resultAnnotations[j].value);
                                                        if (resp.data.entries[i].resultAnnotations[j].value == app.measureSemantic) {
                                                            app.measureDetails = resp.data.entries[i];
                                                        }
                                                    }
                                                }
                                            }
                                            axios.get('http://shark.sbs.arizona.edu:8080/PO/search?term=embryo proper')
                                                .then(function(resp) {
                                                    console.log('embryo proper resp', resp);
                                                    app.objectProperty = resp;
                                                })
                                                .catch(function(resp) {

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
                })
                .catch(function (resp) {

                });
        }
    }
</script>

