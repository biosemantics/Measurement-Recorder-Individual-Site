<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 15px;">
            Method: Please explain how to measure the item. e.g. from top to the bottom.
            <br/>
        </div>
        <div class="col-md-12" v-if="methodEntry == null">
            The images will be displayed soon.
            Please wait until the images are displayed.
            It will take more than few seconds...
        </div>
        <div v-if="methodEntry != null">
            <div v-for="(each, index) in methodEntry.resultAnnotations" v-if="noneMethod == false && each.property == 'http://purl.oblibrary.org/obo/IAO_0000600'" class="col-md-6">
                <img class="img-method" v-on:click="clickedMethod(index, each.value.substring(1, each.value.length - 1))" v-bind:id="'img-method-' + index" style="width: 100%;" v-bind:src="'https://drive.google.com/uc?id=' + each.value.split('id=')[1].substring(0, each.value.split('id=')[1].length - 1)"/>
                <!--<img class="img-method" v-on:click="clickedMethod(index, each.value.substring(1, each.value.length - 1))" v-bind:id="'img-method-' + index" style="width: 100%;" v-bind:src="'/images/' + each.value.split('id=')[1].substring(0, each.value.split('id=')[1].length - 1) + '.png'"/>-->
            </div>
            <div v-if="noneMethod == false && methodArray.length > 0" class="col-md-12 text-right">
                <a class="btn btn-primary" v-on:click="noneOfAbove()">None of above</a>
            </div>
            <div v-if="noneMethod == true || methodArray.length == 0">
                <div class="col-md-12">
                    <a v-if="methodArray.length > 0" class="btn btn-primary" v-on:click="displayImageSection()">Open Image Segment</a>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">From:</label>
                    <input class="col-md-8" v-model="methodFrom"/> <p v-if="fromId != null" style="color: green;">&#10004;</p>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">To:</label>
                    <input class="col-md-8" v-model="methodTo"/> <p v-if="toId != null" style="color: green;">&#10004;</p>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">Include:</label>
                    <input class="col-md-8" v-model="methodInclude"/> <p v-if="includeId != null" style="color: green;">&#10004;</p>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">Exclude:</label>
                    <input class="col-md-8" v-model="methodExclude"/> <p v-if="excludeId != null" style="color: green;">&#10004;</p>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">At:</label>
                    <input class="col-md-8" v-model="methodAt"/> <p v-if="atId != null" style="color: green;">&#10004;</p>
                </div>
                <div class="col-md-12 text-right">
                    <a class="btn btn-primary" v-on:click="checkDictionary()">Check</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        data: function () {
            return {
                childData: [],
                character_name: null,
                viewFlag: false,
                methodEntry: null,
                noneMethod: false,
                methodFrom: null,
                methodTo: null,
                methodInclude: null,
                methodExclude: null,
                methodAt: null,
                fromId: null,
                fromTerm: null,
                toId: null,
                toTerm: null,
                includeTerm: null,
                includeId: null,
                excludeTerm: null,
                excludeId: null,
                atTerm: null,
                atId: null,
                methodArray: [],
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
            handleDataFc: function () {
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            },
            clickedMethod(index, value) {
                var app = this;
                $('.img-method').css('border', 'none');
                $('#img-method-' + index).css('border', 'solid 1px green');
                console.log('value', value);
                app.childData[0] = value;
                app.childData[1] = app.methodEntry.term;
                app.childData[2] = app.methodEntry.resultAnnotations.filter(function(e) {
                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                })[0].value;
                app.childData[3] = app.methodFrom;
                app.childData[4] = app.methodTo;
                app.childData[5] = app.methodInclude;
                app.childData[6] = app.methodExclude;
                app.childData[7] = app.methodAt;

                console.log('childData', app.childData);



                app.$emit('interface', app.childData);

            },
            noneOfAbove() {
                var app = this;
                app.noneMethod = true;
            },
            displayImageSection() {
                var app = this;
                app.noneMethod = false;
            },
            checkDictionary() {
                var app = this;
                app.childData[0] = '';
                app.childData[1] = '';
                app.childData[2] = '';
                app.childData[4] = app.methodFrom;
                app.childData[5] = app.methodTo;
                app.childData[6] = app.methodInclude;
                app.childData[7] = app.methodExclude;
                app.childData[8] = app.methodAt;

                this.$emit('interface', this.childData); // handle data and give it back to parent by interface

                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.character_name)
                    .then(function(resp) {
                        console.log('search resp', resp);
                        var tempFlag = false;
                        for (var i = 0; i < resp.data.entries.length; i++) {
                            if (resp.data.entries[i].term == app.character_name) {
                                tempFlag = true;
                            }
                        }
                        if (tempFlag == false) {
                            axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodFrom)
                                .then(function(resp) {
                                    console.log('search resp', resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        if (resp.data.entries[i].score == 1) {
                                            app.fromTerm = resp.data.entries[i].term;
                                            app.fromId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                            })[0].value;
                                            console.log('fromId', app.fromId);
                                        }
                                    }
                                    axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodTo)
                                        .then(function(resp) {
                                            console.log('search resp', resp);
                                            for (var i = 0; i < resp.data.entries.length; i++) {
                                                if (resp.data.entries[i].score == 1) {
                                                    app.toTerm = resp.data.entries[i].term;
                                                    app.toId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                        return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                    })[0].value;
                                                    console.log('toId', app.toId);
                                                }
                                            }
                                            var jsonClass = {
                                                "user": '',
                                                "ontology": 'exp',
                                                "term": app.character_name,
                                                "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#measurement",
                                                "definition": "from [" + app.methodFrom + "] to [" + app.methodTo + "]",
                                                "createdBy": app.childData[3].name,
                                                "creationDate": new Date(),
                                                "definitionSrc": "tba",
                                                "examples": "tba",
                                                "logicDefinition": "measured_from some [" + app.methodFrom +"] and measured_to some [" + app.methodTo + "]"
                                            };
                                            if (app.character_name.split(' ')[0] == 'distance') {
                                                jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#distance"
                                            } else if (app.character_name.split(' ')[0] == 'length') {
                                                jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#length"

                                            } else if (app.character_name.split(' ')[0] == 'width') {
                                                jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#width"
                                            }
                                            axios.post('http://shark.sbs.arizona.edu:8080/class', jsonClass)
                                                .then(function(resp) {
                                                    console.log('class resp', resp);
                                                    axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": '', "ontology": 'exp'})
                                                        .then(function(resp) {
                                                            console.log('save resp', resp);
                                                        });
                                                })
                                                .catch(function(resp) {
                                                    console.log('class error resp', resp);
                                                });

                                        });
                                });
                            axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodInclude)
                                .then(function (resp) {
                                    console.log('search methodInclude resp', resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        if (resp.data.entries[i].score == 1) {
                                            app.includeTerm = resp.data.entries[i].term;
                                            app.includeId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                            })[0].value;
                                            console.log('includeId', app.includeId);
                                        }
                                    }

                                });
                            axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodExclude)
                                .then(function (resp) {
                                    console.log('search methodExclude resp', resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        if (resp.data.entries[i].score == 1) {
                                            app.excludeTerm = resp.data.entries[i].term;
                                            app.excludeId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                            })[0].value;
                                            console.log('excludeId', app.excludeId);
                                        }
                                    }

                                });
                            axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodAt)
                                .then(function (resp) {
                                    console.log('search methodAt resp', resp);
                                    for (var i = 0; i < resp.data.entries.length; i++) {
                                        if (resp.data.entries[i].score == 1) {
                                            app.atTerm = resp.data.entries[i].term;
                                            app.atId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                            })[0].value;
                                            console.log('atId', app.atId);
                                        }
                                    }

                                });
                        }
                    })
                    .catch(function(resp) {

                    });
            }
        },
        beforeMount () {
            var app = this;
            this.character_name = sessionStorage.getItem("characterName");
            this.viewFlag = (sessionStorage.getItem('viewFlag') == 'true');
            this.childData = this.parentData; // save props data to itself's data and deal with it
            console.log('parentData', app.parentData);
            app.methodFrom = app.childData[4];
            app.methodTo = app.childData[5];
            app.methodInclude = app.childData[6];
            app.methodExclude = app.childData[7];
            app.methodAt = app.childData[8];
            axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.character_name)
                .then(function(resp) {
                    console.log('exp search resp', resp);
                    if (resp.data.entries.length > 0) {
                        app.methodEntry = resp.data.entries[0];
                        app.methodArray = resp.data.entries[0].resultAnnotations.filter(function(e) {
                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000600';
                        });
                    } else {
                        app.methodEntry = true;
                        app.methodArray = resp.data.entries;
                        console.log('methodArray', app.methodArray);
                    }

                })
                .catch(function(resp) {
                    console.log('exp search resp error', resp);
                });
//            var jsonPost = {
//                user: '',
//                ontologies: 'exp'
//            };
//            axios.post('http://shark.sbs.arizona.edu:8080/createUserOntology', jsonPost)
//                .then(function(resp) {
//
//                })
//                .catch(function(resp) {
//                    console.log('createUserOntology error resp', resp);
//                });
        },
        mounted() {
        }
    }
</script>

