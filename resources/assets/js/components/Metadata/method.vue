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
                <div class="col-md-12 text-right">
                    <a v-if="methodArray.length > 0" class="btn btn-primary" v-on:click="displayImageSection()" style="padding: 3px 8px;">
                        Open Image Segment
                    </a>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label class="col-md-3 text-right">From:</label>
                    <input class="col-md-8" v-model="methodFrom"/> <p v-if="fromId != null || greenTick.from == true" style="color: green;">&#10004;</p>
                    <a v-if="fromNeedMore == true" class="red col-md-12" v-on:click="needMore('from')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.from == true">
                        <div class="col-md-12">
                            {{ methodFrom }}: is a synonym of

                        </div>
                        <div class="col-md-12" v-if="fromSynonyms.length > 0" >
                            <a class="btn btn-basic" v-on:click="addSynonym('from', each)" v-tooltip="each.tooltip" v-for="each in fromSynonyms">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('from')">None of above, add the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.from == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <label> {{ methodFrom }} : </label>
                            <input v-model="definition.from" style="margin-left: 10px;"/>
                            <a class="btn btn-primary" v-on:click="addUserSynonym('from', definition.from)">Add</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label class="col-md-3 text-right">To:</label>
                    <input class="col-md-8" v-model="methodTo"/> <p v-if="toId != null || greenTick.to == true" style="color: green;">&#10004;</p>
                    <a v-if="toNeedMore == true" class="red col-md-12" v-on:click="needMore('to')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.to == true">
                        {{ methodTo }}: is a synonym of
                        <div class="col-md-12" v-if="toSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('to', each)" v-tooltip="each.tooltip" v-for="each in toSynonyms">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('to')">None of above, add the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.to == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <label> {{ methodTo }} : </label>
                            <input v-model="definition.to" style="margin-left: 10px;"/>
                            <a class="btn btn-primary" v-on:click="addUserSynonym('to', definition.to)">Add</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label class="col-md-3 text-right">Include:</label>
                    <input class="col-md-8" v-model="methodInclude"/> <p v-if="includeId != null || greenTick.include == true" style="color: green;">&#10004;</p>
                    <a v-if="includeNeedMore == true" class="red col-md-12" v-on:click="needMore('include')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.include == true">
                        {{ methodInclude }}: is a synonym of
                        <div class="col-md-12" v-if="includeSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('include', each)" v-tooltip="each.tooltip" v-for="each in includeSynonyms">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('include')">None of above, add the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.include == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <label> {{ methodInclude }} : </label>
                            <input v-model="definition.include" style="margin-left: 10px;"/>
                            <a class="btn btn-primary" v-on:click="addUserSynonym('include', definition.include)">Add</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label class="col-md-3 text-right">Exclude:</label>
                    <input class="col-md-8" v-model="methodExclude"/> <p v-if="excludeId != null || greenTick.exclude == true" style="color: green;">&#10004;</p>
                    <a v-if="excludeNeedMore == true" class="red col-md-12" v-on:click="needMore('exclude')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.exclude == true">
                        {{ methodExclude }}: is a synonym of
                        <div class="col-md-12" v-if="excludeSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('exclude', each)" v-tooltip="each.tooltip" v-for="each in excludeSynonyms">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('exclude')">None of above, add the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.exclude == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <label> {{ methodExclude }} : </label>
                            <input v-model="definition.exclude" style="margin-left: 10px;"/>
                            <a class="btn btn-primary" v-on:click="addUserSynonym('exclude', definition.exclude)">Add</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <label class="col-md-3 text-right">At:</label>
                    <input class="col-md-8" v-model="methodAt"/> <p v-if="atId != null || greenTick.at == true" style="color: green;">&#10004;</p>
                    <a v-if="atNeedMore == true" class="red col-md-12" v-on:click="needMore('at')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.at == true">
                        {{ methodAt }}: is a synonym of
                        <div class="col-md-12" v-if="atSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('at', each)" v-tooltip="each.tooltip" v-for="each in atSynonyms">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('at')">None of above, add the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.at == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <label> {{ methodExclude }} : </label>
                            <input v-model="definition.at" style="margin-left: 10px;"/>
                            <a class="btn btn-primary" v-on:click="addUserSynonym('at', definition.at)">Add</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <a class="btn btn-primary" v-on:click="checkDictionary()">Check</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Vue from 'vue';
    import VTooltip from 'v-tooltip'

    Vue.use(VTooltip)
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
                fromNeedMore: false,
                toNeedMore: false,
                includeNeedMore: false,
                excludeNeedMore: false,
                atNeedMore: false,
                fromSynonyms: [],
                toSynonyms: [],
                includeSynonyms: [],
                excludeSynonyms: [],
                atSynonyms: [],
                formViewFlag: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    at: false
                },
                greenTick: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    at: false
                },
                definition: {
                    from: null,
                    to: null,
                    include: null,
                    exclude: null,
                    at: null
                },
                noneSynonymFlag: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    at: false
                },
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
            noneSynonym: function(setting) {
                var app = this;
                app.noneSynonymFlag[setting] = true;
            },
            needMore: function (setting) {
                var app = this;
                app.formViewFlag[setting] = !(app.formViewFlag[setting]);
            },
            addUserSynonym: function(setting, definition) {
                var app = this;

                var jsonRequest = {
                    user: '',
                    ontology: 'exp',
                    term: '',
                    superclassIRI: "http://biosemantics.arizona.edu/ontology/exp#physical_entity",
                    definition: definition,
                    elucidation: '',
                    createdBy: app.childData[3].name + ' via MR',
                    examples: app.character_name,
                    creationDate: new Date(),
                    definitionSrc: app.childData[3].name,
                    logicDefinition: ''
                };

                switch (setting) {
                    case 'from':
                        jsonRequest.term = app.methodFrom;
                        break;
                    case 'to':
                        jsonRequest.term = app.methodTo;
                        break;
                    case 'include':
                        jsonRequest.term = app.methodInclude;
                        break;
                    case 'exclude':
                        jsonRequest.term = app.methodExclude;
                        break;
                    case 'at':
                        jsonRequest.term = app.methodExclude;
                        break;
                    default:
                        break;
                }
                axios.post('http://shark.sbs.arizona.edu:8080/class', jsonRequest)
                    .then(function(resp) {
                        console.log('class resp', resp);
                        app.greenTick[setting] = true;
                        app.formViewFlag[setting] = false;
                        axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": '', "ontology": 'exp'})
                            .then(function(resp) {
                                console.log('save resp', resp);
                            });
                    });
            },
            addSynonym: function (setting, value) {
                var app = this;
                console.log('value', value);

                var jsonRequest = {
                    user: '',
                    ontology: 'exp',
                };
                var temp = value.resultAnnotations.filter(function(e) {
                    return e.property == "http://www.geneontology.org/formats/oboInOwl#id";
                });

                switch (setting) {
                    case 'from':
                        jsonRequest.term = app.methodFrom;
                        break;
                    case 'to':
                        jsonRequest.term = app.methodTo;
                        break;
                    case 'include':
                        jsonRequest.term = app.methodInclude;
                        break;
                    case 'exclude':
                        jsonRequest.term = app.methodExclude;
                        break;
                    case 'at':
                        jsonRequest.term = app.methodExclude;
                        break;
                    default:
                        break;
                }
                var splitValue = value.term.split('(');
                console.log('splitValue', splitValue);
                if (splitValue.length > 1) {
                    console.log('***', splitValue[1].substring(0, 7));
                    if (splitValue[1].substring(0, 7) == 'part_of') {
                        jsonRequest.superclassIRI = "http://biosemantics.arizona.edu/ontology/exp#physical_entity";
                        jsonRequest.definition = '';
                        jsonRequest.elucidation = '';
                        jsonRequest.createdBy = app.childData[3].name + ' via MR';
                        jsonRequest.examples = app.character_name;
                        jsonRequest.creationDate = new Date();
                        jsonRequest.definitionSrc = app.childData[3].name;
                        jsonRequest.logicDefinition = value.term;
                        axios.post('http://shark.sbs.arizona.edu:8080/class', jsonRequest)
                            .then(function(resp) {
                                console.log('class resp', resp);
                                app.greenTick[setting] = true;
                                app.formViewFlag[setting] = false;
                                axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": '', "ontology": 'exp'})
                                    .then(function(resp) {
                                        console.log('save resp', resp);
                                    });
                            });
                    }
                } else {
                    if (temp.length > 0) {
                        jsonRequest.classIRI = temp[0].value;
                    }
                    axios.post('http://shark.sbs.arizona.edu:8080/esynonym', jsonRequest)
                        .then(function(resp) {
                            console.log('esynonym resp', resp);
                            if (resp.data == 'SUCCESSFULLY') {
                                app.greenTick[setting] = true;
                                app.formViewFlag[setting] = false;
                            }
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": '', "ontology": 'exp'})
                                .then(function(resp) {
                                    console.log('save resp', resp);
                                });
                        });
                }
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
                app.childData[4] = app.methodFrom;
                app.childData[5] = app.methodTo;
                app.childData[6] = app.methodInclude;
                app.childData[7] = app.methodExclude;
                app.childData[8] = app.methodAt;

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
                            if (app.methodFrom != null) {
                                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodFrom)
                                    .then(function(resp) {
                                        console.log('search from resp', resp);
                                        for (var i = 0; i < resp.data.entries.length; i++) {
                                            if (resp.data.entries[i].score == 1) {
                                                app.fromTerm = resp.data.entries[i].term;
                                                app.fromId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                })[0].value;
                                                console.log('fromId', app.fromId);
                                            }
                                        }
                                        if (app.fromId == null) {
                                            app.fromNeedMore = true;
                                            app.fromSynonyms = resp.data.entries;
                                            if (app.fromSynonyms.length == 0) {
                                                app.noneSynonymFlag.from = true;
                                            }
                                            for (var i = 0; i < app.fromSynonyms.length; i++) {
                                                app.fromSynonyms[i].tooltip = '';
                                                var temp = app.fromSynonyms[i].resultAnnotations.filter(function(e){
                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                                });
                                                if (temp.length > 0) {
                                                    app.fromSynonyms[i].tooltip = temp[0].value;
                                                }
                                            }
                                        }
                                    });
                            }
                            if (app.methodTo != null) {
                                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodTo)
                                    .then(function(resp) {
                                        console.log('search to resp', resp);
                                        for (var i = 0; i < resp.data.entries.length; i++) {
                                            if (resp.data.entries[i].score == 1) {
                                                app.toTerm = resp.data.entries[i].term;
                                                app.toId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                })[0].value;
                                                console.log('toId', app.toId);
                                            }
                                        }
                                        if (app.toId == null) {
                                            app.toNeedMore = true;
                                            app.toSynonyms = resp.data.entries;
                                            if (app.toSynonyms.length == 0) {
                                                app.noneSynonymFlag.to = true;
                                            }
                                            for (var i = 0; i < app.toSynonyms.length; i++) {
                                                app.toSynonyms[i].tooltip = '';
                                                var temp = app.toSynonyms[i].resultAnnotations.filter(function(e){
                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                                });
                                                if (temp.length > 0) {
                                                    app.toSynonyms[i].tooltip = temp[0].value;
                                                }
                                            }
                                        }
                                    });
                            }
                            if (app.methodInclude != null) {
                                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodInclude)
                                    .then(function (resp) {
                                        console.log('search include resp', resp);
                                        for (var i = 0; i < resp.data.entries.length; i++) {
                                            if (resp.data.entries[i].score == 1) {
                                                app.includeTerm = resp.data.entries[i].term;
                                                app.includeId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                })[0].value;
                                                console.log('includeId', app.includeId);
                                            }
                                        }
                                        if (app.includeId == null) {
                                            app.includeNeedMore = true;
                                            app.includeSynonyms = resp.data.entries;
                                            if (app.includeSynonyms.length == 0) {
                                                app.noneSynonymFlag.include = true;
                                            }
                                            for (var i = 0; i < app.includeSynonyms.length; i++) {
                                                app.includeSynonyms[i].tooltip = '';
                                                var temp = app.includeSynonyms[i].resultAnnotations.filter(function(e){
                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                                });
                                                if (temp.length > 0) {
                                                    app.includeSynonyms[i].tooltip = temp[0].value;
                                                }
                                            }
                                        }
                                    });
                            }
                            if (app.methodExclude != null) {
                                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodExclude)
                                    .then(function (resp) {
                                        console.log('search exclude resp', resp);
                                        for (var i = 0; i < resp.data.entries.length; i++) {
                                            if (resp.data.entries[i].score == 1) {
                                                app.excludeTerm = resp.data.entries[i].term;
                                                app.excludeId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                })[0].value;
                                                console.log('excludeId', app.excludeId);
                                            }
                                        }
                                        if (app.excludeId == null) {
                                            app.excludeNeedMore = true;
                                            app.excludeSynonyms = resp.data.entries;
                                            if (app.excludeSynonyms.length == 0) {
                                                app.noneSynonymFlag.exclude = true;
                                            }
                                            for (var i = 0; i < app.excludeSynonyms.length; i++) {
                                                app.excludeSynonyms[i].tooltip = '';
                                                var temp = app.excludeSynonyms[i].resultAnnotations.filter(function(e){
                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                                });
                                                if (temp.length > 0) {
                                                    app.excludeSynonyms[i].tooltip = temp[0].value;
                                                }
                                            }
                                        }
                                    });
                            }
                            if (app.methodAt != null) {
                                axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.methodAt)
                                    .then(function (resp) {
                                        console.log('search at resp', resp);
                                        for (var i = 0; i < resp.data.entries.length; i++) {
                                            if (resp.data.entries[i].score == 1) {
                                                app.atTerm = resp.data.entries[i].term;
                                                app.atId = resp.data.entries[i].resultAnnotations.filter(function(e) {
                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                                })[0].value;
                                                console.log('atId', app.atId);
                                            }
                                        }
                                        if (app.atId == null) {
                                            app.atNeedMore = true;
                                            app.atSynonyms = resp.data.entries;
                                            if (app.atSynonyms.length == 0) {
                                                app.noneSynonymFlag.at = true;
                                            }
                                            for (var i = 0; i < app.atSynonyms.length; i++) {
                                                app.atSynonyms[i].tooltip = '';
                                                var temp = app.atSynonyms[i].resultAnnotations.filter(function(e){
                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                                });
                                                if (temp.length > 0) {
                                                    app.atSynonyms[i].tooltip = temp[0].value;
                                                }
                                            }
                                        }
                                    });
                            }

                            var jsonClass = {
                                "user": '',
                                "ontology": 'exp',
                                "term": app.character_name,
                                "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#measurement",
                                "definition": '',
                                "createdBy": app.childData[3].name,
                                "creationDate": new Date(),
                                "definitionSrc": "tba",
                                "examples": "tba",
                                "logicDefinition": "measured_from some [" + app.methodFrom +"] and measured_to some [" + app.methodTo + "]"
                            };
                            if (app.methodFrom != null) {
                                jsonClass.definition = jsonClass.definition + 'from [' + app.methodFrom + ']';
                            }
                            if (app.methodTo != null) {
                                jsonClass.definition = jsonClass.definition + ' to [' + app.methodTo + ']';
                            }
                            if (app.methodInclude != null) {
                                jsonClass.definition = jsonClass.definition + ' include [' + app.methodInclude + ']';
                            }
                            if (app.methodExclude != null) {
                                jsonClass.definition = jsonClass.definition + ' exclude [' + app.methodExclude + ']';
                            }
                            if (app.methodAt != null) {
                                jsonClass.definition = jsonClass.definition + ' at [' + app.methodAt + ']';
                            }
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
        },
        mounted() {
        }
    }
</script>

