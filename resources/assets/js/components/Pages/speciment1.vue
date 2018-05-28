<template>
    <layout>
        <div slot="section" class="">
            <div class="tab-pane" id="">
                <form class="row">
                    <div class="col-md-4">
                        New Character
                    </div>
                    <div class="col-md-4">
                        <input style="width: 100%;" v-model="character.text" name="text"/>
                    </div>
                    <div class="col-md-4">
                        <a v-on:click="storeCharacter()" class="btn btn-primary" style="height: 28px; line-height: 28px; font-size: 50px; padding: 0 5px;">+</a>
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        Character List
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        <!--<select style="width: 100%; height: 200px;" multiple>-->
                            <!--<option v-for="specimen in specimenArray" v-bind:value="specimen">-->
                                <!--{{ specimen.text }}-->
                            <!--</option>-->
                        <!--</select>-->
                        <table class="table table-bordered measure-table">
                            <thead>
                                <tr>
                                    <th v-for="header in headers"><input class="th-input" v-bind:value="header.header" /></th>
                                    <th class="actions">
                                        <input class="th-input display-none" v-model="newHeader.header" name="header" autofocus/>
                                        <a class="btn btn-add display-block" v-on:click="addHeader()" style="width: 100%; height:37px; "><span class="glyphicon glyphicon-plus"></span></a>
                                        <a class="btn btn-success btn-save display-none" v-on:click="saveHeader()"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                                        <a class="btn btn-danger btn-cancel display-none" v-on:click="cancelHeader()"><span class="glyphicon glyphicon-remove-circle"></span></a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="eachCharacter in characters">
                                    <td v-for="item in eachCharacter">
                                        <div class="text-center" v-if="item.header_id == 2 || item.header_id == 3">
                                            {{ item.value }}
                                        </div>
                                        <input v-if="item.header_id != 2 && item.header_id !=3" class="td-input" v-model="item.value" v-on:blur="saveItem(item)"/>
                                    </td>
                                    <td class="actions text-center">
                                        <!--<a class="btn btn-success btn-save" v-on:click="saveCharacter(eachCharacter)"><span class="glyphicon glyphicon-floppy-disk"></span></a>-->
                                        <!--<a class="btn" v-on:click="deleteCharacter(eachCharacter[0].character_id)"><span class="glyphicon glyphicon-remove-circle"></span></a>-->
                                        <a class="btn" v-on:click="deleteCharacter(eachCharacter[0].character_id)">Delete</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--<div class="col-md-7">-->
                        <!--<div id='piemenu' data-wheelnav-->
                             <!--data-wheelnav-slicepath='DonutSlice'-->
                             <!--data-wheelnav-spreader data-wheelnav-spreaderpath='PieSpreader'-->
                             <!--data-wheelnav-rotateoff-->
                             <!--data-wheelnav-navangle='270'-->
                             <!--data-wheelnav-cssmode-->
                             <!--data-wheelnav-init>-->
                            <!--<div v-on:click="detailComponent(1)" data-wheelnav-navitemtext='Method'  style="background-color: #1b6d85;"></div>-->
                            <!--<div v-on:click="detailComponent(2)" data-wheelnav-navitemtext='Ontology ids' ></div>-->
                            <!--<div data-wheelnav-navitemtext='Author name' ></div>-->
                            <!--<div data-wheelnav-navitemtext='History' ></div>-->
                            <!--<div data-wheelnav-navitemtext='Specimen No' ></div>-->
                            <!--<div class="wheel-updated" data-wheelnav-navitemtext='Wheel updated' ></div>-->
                            <!--<div data-wheelnav-navitemtext='Future Function' ></div>-->
                            <!--<div data-wheelnav-navitemtext='Future Function' ></div>-->
                            <!--<div data-wheelnav-navitemtext='Future Function' ></div>-->
                        <!--</div>-->
                    <!--</div>-->
                    <!--<div class="col-md-5">-->
                        <!--<div id="toReplace">-->
                            <!--<div :is="currentComponent"></div>-->
                            <!--<div v-show="!currentComponent" v-for="component in componentsArray">-->
                                <!--<button type="button" @click="swapComponent(component)">{{component}}</button>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<button type="button" @click="swapComponent(null)">Close</button>-->
                    <!--</div>-->
                </form>
            </div>
        </div>
    </layout>
</template>
<script>
//    import Vue from 'vue';
//    import raphael from "../../../../../node_modules/raphael/raphael.min.js";
//    import wheelnav from "../../../../../node_modules/wheelnav/js/dist/wheelnav.min.js";
//
//    const raphaelComp = new Vue(raphael);
//    const wheelnavComp = new Vue(wheelnav);

    export default {


        data: function () {
            return {
                character: {
                    text: ''
                },
                actionLog: {
                    user_id: this.userId
                },
                currentComponent: null,
                componentsArray: ['foo', 'bar'],

                headers: [],
                characters: [],
                newHeader: {
                    header: ''
                }
            }
        },
        props: {
            userId: Number
        },
        components: {
            'foo': {
                template: '<h1>Foo component</h1>'
            },
            'bar': {
                template: '<h1>Bar component</h1>'
            }
        },
        methods: {
            addHeader: function() {
//                $('.measure-table > thead > tr > th:last-child').before('<th></th>')
                $('th.actions > .display-none').removeClass('display-none').addClass('display-block');
                $('th.actions > .btn-add.display-block').removeClass('display-block').addClass('display-none');
            },
            saveHeader: function() {
                var app = this;
                axios.post('/api/v1/character/add-header', this.newHeader)
                    .then(function (resp) {
                        console.log("resp", resp);
//                        $('.measure-table thead tr th:last-child').before("<th><input class='th-input' value='" + resp.data.header + "' /></th>");
                        app.headers = resp.data.headers;
                        app.characters = resp.data.characters;
                        $('th.actions > .display-block').removeClass('display-block').addClass('display-none');
                        $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            cancelHeader: function() {
                $('th.actions > .display-block').removeClass('display-block').addClass('display-none');
                $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
            },
            swapComponent: function(component)
            {
                this.currentComponent = component;
            },
            storeCharacter() {
                let tempCharacter = {
                    text: this.character.text
                };

                var app = this;
                axios.post('/api/v1/character/create', this.character)
                    .then(function (resp) {
                        console.log("resp", resp);
                        app.characters = resp.data.characters;
                        app.actionLog.action_type = "create";
                        app.actionLog.model_id = resp.data.value.id;
                        axios.post('/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log character !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });
                        console.log("successful saving character !!!");
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Error Occured !");
                    });
            },
            detailComponent: function(componentId) {
                console.log("componentId", componentId);
            },
            saveItem: function(item) {
                var app = this;
                axios.post('/api/v1/character/update', item)
                    .then(function (resp) {
                        console.log("update", resp.data);
                        var updatedCharacter = resp.data;
                        console.log("headers", app.headers);
                        console.log("characters", app.characters);

                        var totalSum = 0;
                        var characterIndex = 0;
                        var headerCount = 0;
                        var averageValue = 0;

                        for (var i = 0; i < app.characters.length; i++) {
                            for (var j = 0; j < app.characters[i].length; j++){
                                if (app.characters[i][j].character_id == updatedCharacter.character_id) {
                                    characterIndex = i;
                                    if (app.characters[i][j].header_id == updatedCharacter.header_id) {
                                        app.characters[i][j].value = updatedCharacter.value;
                                    }
                                    if (app.characters[i][j].header_id > 3) {
                                        headerCount++;
                                        console.log("value", parseFloat(app.characters[i][j].value));
                                        if (app.characters[i][j].value != '') {
                                            totalSum = totalSum + parseFloat(app.characters[i][j].value);
                                        }
                                    }
                                } else {
                                    break;
                                }
                            }
                        }

                        console.log("headerCount", headerCount);
                        console.log("totalSum", totalSum);

                        if (headerCount > 0) {
                            averageValue = (totalSum / headerCount).toFixed(2);
                            app.characters[characterIndex][1].value = (totalSum / headerCount).toFixed(2);
                        }

                        axios.post('/api/v1/character/update', app.characters[characterIndex][1])
                            .then(function (resp) {
                                console.log('update average', resp);
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });

                        var deviationSum = 0;
                        var deviationValue = 0;

                        if (headerCount > 1) {
                            for (var i = 3; i < (headerCount + 3); i ++) {
                                deviationSum = deviationSum + Math.pow((parseFloat(app.characters[characterIndex][i].value) - averageValue), 2);
                            }
                            deviationValue = Math.pow((deviationSum / (headerCount - 1)), 0.5).toFixed(2);
                        } else if (headerCount == 1) {
                            deviationValue = parseFloat(app.characters[characterIndex][3].value).toFixed(2);
                        }

                        app.characters[characterIndex][2].value = deviationValue;

                        axios.post('/api/v1/character/update', app.characters[characterIndex][2])
                            .then(function (resp) {
                                console.log('update deviation', resp);
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });


//                        app.headers = resp.data.headers;
//                        app.characters = resp.data.characters;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Error Occured !");
                    });
            },
            deleteCharacter: function(character_id) {
                var app = this;
                var tpData = {
                    character_id: character_id
                };
                axios.post('/api/v1/character/delete', tpData)
                    .then(function (resp) {
                        console.log("resp", resp);
                        app.characters = resp.data.characters;
                        app.actionLog.action_type = "delete";
                        app.actionLog.model_id = tpData.character_id;
                        axios.post('/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log character !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Error Occured !");
                    });
            }
        },
        created() {
            var app = this;
            axios.get('/api/v1/character/all')
                .then(function (resp) {
                    console.log(resp);
                    app.headers = resp.data.headers;
                    app.characters = resp.data.characters;
                })
                .catch(function (resp) {
                    console.log(resp);
                });

        },
        mounted() {
//            var piemenu = wheelnavComp('piemenu');
//            piemenu.spreaderInTitle = '+';
//            piemenu.spreaderOutTitle = 'Details';
//            piemenu.spreaderRadius = piemenu.wheelRadius * 0.25;
//            piemenu.clockwise = false;
//            piemenu.wheelRadius = piemenu.wheelRadius * 0.99;
//            piemenu.length = 10;
//            piemenu.createWheel();
        }
    }
</script>