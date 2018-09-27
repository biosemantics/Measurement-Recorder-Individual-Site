<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            {{ character_name }} is measure ...
        </div>
        <div class="col-md-12" v-if="methodEntry == null">
            The images will be displayed soon.
            Please wait until the images are displayed.
            It will take less than few seconds...
        </div>
        <div v-if="methodEntry != null">
            <!--<div class="col-md-12">-->
                <!--As: <input v-if="viewFlag == false" class="child-model" style="width: 100%;" v-model="childData[2]" v-on:change="handleDataFc()"/>-->
                <!--<input v-if="viewFlag == true" disabled class="child-model" style="width: 100%;" v-model="childData[2]" v-on:change="handleDataFc()"/>-->
            <!--</div>-->
            <!--<div class="col-md-12 text-center">-->
                <!--Or-->
            <!--</div>-->
            <!--<div class="col-md-12">-->
                <!--From: <input v-if="viewFlag == false" class="child-model" style="width: 100%;" v-model="childData[0]" v-on:change="handleDataFc()"/>-->
                <!--<input v-if="viewFlag == true" disabled class="child-model" style="width: 100%;" v-model="childData[0]" v-on:change="handleDataFc()"/>-->
            <!--</div>-->
            <!--<div class="col-md-12">-->
                <!--To: <input v-if="viewFlag == false" class="child-model" style="width: 100%;" v-model="childData[1]" v-on:change="handleDataFc()"/>-->
                <!--<input v-if="viewFlag == true" disabled class="child-model" style="width: 100%;" v-model="childData[1]" v-on:change="handleDataFc()"/>-->
            <!--</div>-->

            <div v-for="(each, index) in methodEntry.resultAnnotations" v-if="noneMethod == false && each.property == 'http://purl.oblibrary.org/obo/IAO_0000600'" class="col-md-6">
                <img class="img-method" v-on:click="clickedMethod(index, each.value.substring(1, each.value.length - 1))" v-bind:id="'img-method-' + index" style="width: 100%;" v-bind:src="'https://drive.google.com/uc?id=' + each.value.split('id=')[1].substring(0, each.value.split('id=')[1].length - 1)"/>
            </div>
            <div v-if="noneMethod == false" class="col-md-12 text-right">
                <a class="btn btn-primary" v-on:click="noneOfAbove()">None of above</a>
            </div>
            <div v-if="noneMethod == true">
                <div class="col-md-12">
                    <a class="btn btn-primary" v-on:click="displayImageSection()">Open Image Segment</a>
                </div>
                <div class="col-md-12">
                    Method: Please explain how to measure the item. e.g. from top to the bottom.
                    <br/>
                    (Choose the one(s) fits your method best.)
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">From:</label>
                    <input class="col-md-9" v-model="methodFrom"/>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">To:</label>
                    <input class="col-md-9" v-model="methodTo"/>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">Include:</label>
                    <input class="col-md-9" v-model="methodInclude"/>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">Exclude:</label>
                    <input class="col-md-9" v-model="methodExclude"/>
                </div>
                <div class="col-md-12">
                    <label class="col-md-3 text-right">At:</label>
                    <input class="col-md-9" v-model="methodAt"/>
                </div>
                <div class="col-md-12 text-right">
                    <a class="btn btn-primary" v-on:click="checkDictionary()">Check for the dictionary</a>
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
        },
        beforeMount () {
            var app = this;
            this.character_name = sessionStorage.getItem("characterName");
            this.viewFlag = (sessionStorage.getItem('viewFlag') == 'true');
            this.childData = this.parentData; // save props data to itself's data and deal with it
            console.log('parentData', app.parentData);
            var jsonPost = {
                user: '',
                ontologies: 'exp'
            };
            axios.post('http://shark.sbs.arizona.edu:8080/createUserOntology', jsonPost)
                .then(function(resp) {
                    axios.get('http://shark.sbs.arizona.edu:8080/exp/search?term=' + app.character_name)
                        .then(function(resp) {
                            console.log('exp search resp', resp);
                            app.methodEntry = resp.data.entries[0];
                        })
                        .catch(function(resp) {
                            console.log('exp search resp error', resp);
                        });
                })
                .catch(function(resp) {
                    console.log('createUserOntology error resp', resp);
                });
        },
        mounted() {
        }
    }
</script>

