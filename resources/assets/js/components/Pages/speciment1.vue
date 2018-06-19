<template>
    <layout>
        <div slot="section" class="">
            <div class="tab-pane" id="">
                <form class="row">
                    <div class="col-md-4">
                        New Character
                    </div>
                    <div class="col-md-4">
                        <input style="width: 100%;" v-model="character.name" name="text"/>
                    </div>
                    <div class="col-md-4">
                        <a v-on:click="storeCharacter()" class="btn btn-primary" style="height: 28px; line-height: 28px; font-size: 50px; padding: 0 5px;">+</a>
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        Character List
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
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
                                        <div class="text-center" v-if="item.header_id == 1">
                                            {{ item.value }} ({{ item.unit }})
                                        </div>
                                        <div class="text-center" v-if="item.header_id > 1 && item.header_id < 4">
                                            {{ item.value }}
                                        </div>
                                        <input v-if="item.header_id >= 4" class="td-input" v-model="item.value" v-on:blur="saveItem(item)"/>
                                    </td>
                                    <td class="actions text-center">
                                        <a class="btn" v-on:click="editCharacter(eachCharacter[0])"><span class="glyphicon glyphicon-edit"></span></a>
                                        <a class="btn" v-on:click="deleteCharacter(eachCharacter[0].character_id)"><span class="glyphicon glyphicon-trash"></span></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="detailsFlag == true">
                        <!--<div class="col-md-7 radial-menu">-->
                            <!--<ul style="margin-left: auto; margin-right: auto;">-->
                                <!--<li><a v-on:click="showDetails('method')">Method</a></li>-->
                                <!--<li><a v-on:click="showDetails('unit')">Unit</a></li>-->
                                <!--<li><a v-on:click="showDetails('semantics')">Semantics</a></li>-->
                                <!--<li><a v-on:click="showDetails('creator')">Creator</a></li>-->
                                <!--<li><a v-on:click="showDetails('usage')">Usage</a></li>-->
                                <!--<li><a v-on:click="showDetails('history')">History</a></li>-->
                                <!--<li><a v-on:click="showDetails('')">Future<br>Function</a></li>-->
                                <!--<li><a v-on:click="showDetails('')">Future<br>Function</a></li>-->
                                <!--<li><a v-on:click="showDetails('')">Future<br>Function</a></li>-->
                            <!--</ul>-->
                            <!--<div class="center">-->
                                <!--<a>Details</a>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="col-md-5">-->
                            <!--<div id="metadataPlace">-->
                                <!--<div :is="currentMetadata" :parentData="parentData"-->
                                     <!--@interface="handleFcAfterDateBack">-->

                                <!--</div>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="col-md-12 text-right">-->
                            <!--<a v-on:click="saveCharacter()" class="btn btn-primary">Save</a>-->
                            <!--<a v-on:click="cancelCharacter()" class="btn btn-danger">Cancel</a>-->
                        <!--</div>-->
                    </div>
                    <div v-if="detailsFlag" @close="detailsFlag = false">
                        <transition name="modal">
                            <div class="modal-mask">
                                <div class="modal-wrapper">
                                    <div class="modal-container">

                                        <div class="modal-header">
                                            <h3>Metadata Details</h3>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-7 radial-menu">
                                                    <ul style="margin-left: auto; margin-right: auto;">
                                                        <li class="method"><a v-on:click="showDetails('method')">Method</a></li>
                                                        <li class="unit"><a v-on:click="showDetails('unit')">Unit</a></li>
                                                        <li class="semantics"><a v-on:click="showDetails('semantics')">Semantics</a></li>
                                                        <li class="creator"><a v-on:click="showDetails('creator')">Creator</a></li>
                                                        <li><a v-on:click="showDetails('usage')">Usage</a></li>
                                                        <li><a v-on:click="showDetails('history')">History</a></li>
                                                        <li><a v-on:click="showDetails('')">Future<br>Function</a></li>
                                                        <li><a v-on:click="showDetails('')">Future<br>Function</a></li>
                                                        <li><a v-on:click="showDetails('')">Future<br>Function</a></li>
                                                    </ul>
                                                    <div class="center">
                                                        <a>Details</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div id="metadataPlace">
                                                        <div :is="currentMetadata" :parentData="parentData"
                                                             @interface="handleFcAfterDateBack">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <a v-on:click="saveCharacter()" class="btn btn-primary">Save</a>
                                                    <a v-on:click="cancelCharacter()" class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                            <!--<slot name="footer">-->
                                                <!--default footer-->
                                                <!--<button class="modal-default-button" @click="$emit('close')">-->
                                                    <!--OK-->
                                                <!--</button>-->
                                            <!--</slot>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>

                </form>
            </div>
        </div>

    </layout>
</template>

<script>
    import Vue from 'vue';
    import method from '../../components/Metadata/method.vue';
    import unit from '../../components/Metadata/unit.vue';
    import semantics from '../../components/Metadata/semantics.vue';
    import creator from '../../components/Metadata/creator.vue';
    import usage from '../../components/Metadata/usage.vue';
    import history from '../../components/Metadata/history.vue';
    import modal from '../../components/Pages/modal.vue';

    Vue.component('modal', modal);

    export default {
        props: [
            'user'
        ],
        data: function () {
            return {
                character: {
                    name: null,
                    method_from: null,
                    method_to: null,
                    unit: null,
                    semantics: null,
                    creator: this.user.name,
                    usage: [],
                    history: []
                },
                actionLog: {
                    user_id: this.user.id
                },
                currentComponent: null,
                currentMetadata: null,

                headers: [],
                characters: [],
                newHeader: {
                    header: ''
                },
                parentData: '',
                metadataFlag: '',
                detailsFlag: false,
                updatedFlag: false,
                showModal: false,
                editFlag: false
            }
        },

        methods: {
            handleFcAfterDateBack (event) {
                this.updatedFlag = true;
                $('.center').addClass('back-yellow');
                $('.' + this.metadataFlag).addClass('back-median-green');
                switch (this.metadataFlag) {
                    case 'method':
                        this.character.method_from = event[0];
                        this.character.method_to = event[1];
                        this.parentData = event;
                        console.log("method return", event);
                        break;
                    case 'unit':
                        this.character.unit = event;
                        this.parentData = event;
                        break;
                    case 'semantics':
                        this.character.semantics = event;
                        this.parentData = event;
                        break;
                    case 'creator':
                        this.character.creator = event;
                        this.parentData = event;
                        break;
                    default:
                        break;
                }
                console.log('data after child handle: ', event); // get the data after child dealing
            },
            editCharacter (character) {
                this.updatedFlag = false;
                this.editFlag = true;
                console.log("character", character);
                var app = this;
                axios.get("/api/v1/character/" + character.character_id)
                    .then(function (resp) {
                        console.log("get Character", resp);
                        app.metadataFlag = 'method';
                        app.character = resp.data;
                        app.parentData = [];
                        app.parentData.push(app.character.method_from);
                        app.parentData.push(app.character.method_to);
                        app.currentMetadata = method;
                        app.detailsFlag = true;
                        axios.get("/api/v1/character/history/" + character.character_id)
                            .then(function (resp) {
                                console.log("history resp", resp);
                                app.character.history = [];
                                for (var i = 0; i < resp.data.length; i++) {
                                    app.character.history.push(resp.data[i].created_at);
                                }
                                console.log("history", app.character.history);

                                axios.get("/api/v1/character/usage/" + app.character.id)
                                    .then(function (resp) {
                                        console.log("usage resp", resp);
                                        app.character.usage = [];
                                        for (var j = 0; j < resp.data.length; j++) {
                                            app.character.usage.push(resp.data[j].header);
                                        }
                                        console.log("usage", app.character.usage);
                                    })
                                    .catch(function (resp) {
                                        console.log(resp);
                                    });
                            })
                            .catch(function (resp) {
                                console.log(resp);
                            });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });

            },
            showDetails (metadata) {
                console.log("metadata", metadata);
                console.log("character", this.character);
                this.metadataFlag = metadata;
                switch (metadata) {
                    case 'method':
                        this.parentData = [];
                        this.parentData.push(this.character.method_from);
                        this.parentData.push(this.character.method_to);
                        this.currentMetadata = method;
                        break;
                    case 'unit':
                        this.parentData = this.character.unit;
                        this.currentMetadata = unit;
                        break;
                    case 'semantics':
                        console.log("semantics");
                        this.parentData = this.character.semantics;
                        this.currentMetadata = semantics;
                        break;
                    case 'creator':
                        this.parentData = this.character.creator;
                        this.currentMetadata = creator;
                        break;
                    case 'usage':
                        this.parentData = this.character.usage;
                        this.currentMetadata = usage;
                        break;
                    case 'history':
                        this.parentData = this.character.history;
                        this.currentMetadata = history;
                        break;
                    default:
                        break;
                }
            },
            saveCharacter () {
                console.log('save character', this.character);


                var checkFields = true;

                for (var key in this.character) {
                    if (key != 'semantics' && key != 'usage' && key != 'history' && (this.character[key] == null || this.character[key] == '')) {
                        checkFields = false;
                    }
                }
                var app = this;

                if (checkFields) {
                    this.detailsFlag = false;
                    this.updatedFlag = false;
                    axios.get('/api/v1/character/name')
                        .then(function (resp) {
                            console.log('get name resp', resp);
                            var checkName = true;

                            for (var i = 0; i < resp.data.length; i ++) {
                                if (app.character.name == resp.data[i].name) {
                                    checkName = false;
                                }
                            }

                            if (checkName || app.editFlag) {
                                axios.post('/api/v1/character/create', app.character)
                                    .then(function (resp) {
                                        console.log("resp", resp);
                                        app.characters = resp.data.characters;
                                        if (app.character.id) {
                                            app.actionLog.action_type = "update";
                                            app.actionLog.model_id = app.character.id;
                                        } else {
                                            app.actionLog.action_type = "create";
                                            app.actionLog.model_id = resp.data.characters[resp.data.characters.length - 1][0].character_id;
                                        }
                                        app.actionLog.model_name = "character";
                                        axios.post('/api/v1/log', app.actionLog)
                                            .then(function (resp) {
                                                console.log("successful log character !!!");
                                                axios.get('/api/v1/character/all')
                                                    .then(function (resp) {
                                                        console.log(resp);
                                                        app.headers = resp.data.headers;
                                                        app.characters = resp.data.characters;

                                                        for (var i = 0; i < app.characters.length; i++) {
                                                            app.characters[i][0].unit = resp.data.arrayCharacters[i].unit;
                                                        }
                                                    })
                                                    .catch(function (resp) {
                                                        console.log(resp);
                                                    });
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
                            } else {
                                alert("You can not create new character with the same name of existing character !");
                            }
                        })
                        .catch(function (resp) {
                            console.log(resp);
                        });
//

                } else {
                    alert("You need to fill the all metadata !");
                }


            },
            cancelCharacter () {
                this.detailsFlag = false;
                this.updatedFlag = false;
                this.parentData = null;
                this.character.name = '';
            },
            addHeader: function() {
//                $('.measure-table > thead > tr > th:last-child').before('<th></th>')
                $('th.actions > .display-none').removeClass('display-none').addClass('display-block');
                $('th.actions > .btn-add.display-block').removeClass('display-block').addClass('display-none');
            },
            saveHeader: function() {
                var app = this;
                axios.post('/api/v1/character/add-header', this.newHeader)
                    .then(function (resp) {
                        console.log("createHeader resp", resp);
//                        $('.measure-table thead tr th:last-child').before("<th><input class='th-input' value='" + resp.data.header + "' /></th>");
                        app.headers = resp.data.headers;
                        app.characters = resp.data.characters;
                        for (var i = 0; i < app.characters.length; i++) {
                            app.characters[i][0].unit = resp.data.arrayCharacters[i].unit;
                        }
                        $('th.actions > .display-block').removeClass('display-block').addClass('display-none');
                        $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
                        app.actionLog.action_type = "create_header";
                        app.actionLog.model_id = resp.data.characters[0][resp.data.characters[0].length - 1].header_id;
                        app.actionLog.model_name = "header";
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
                console.log("detailsFlag", this.detailsFlag);
                this.parentData = [];
                this.parentData[0] = "";
                this.parentData[1] = "";
                this.currentMetadata = method;
                this.detailsFlag = true;
                this.editFlag = false;
            },
            detailComponent: function(componentId) {
                console.log("componentId", componentId);
            },
            saveItem: function(item) {
                var app = this;
                axios.post('/api/v1/character/update', item)
                    .then(function (resp) {
                        console.log("update item", resp.data);
                        var updatedCharacter = resp.data;

                        app.actionLog.action_type = "update";
                        app.actionLog.model_id = resp.data.character_id;
                        app.actionLog.model_name = "value";
                        axios.post('/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log character !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });

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
                                        console.log("value", parseFloat(app.characters[i][j].value));
                                        console.log("check if NaN", isNaN(parseFloat(app.characters[i][j].value)));
                                        if (isNaN(parseFloat(app.characters[i][j].value)) == false) {
                                            headerCount++;
                                            totalSum = totalSum + parseFloat(app.characters[i][j].value);
                                        }
                                    }
                                } else {
                                    break;
                                }
                            }
                        }

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
                                if (isNaN(parseFloat(app.characters[characterIndex][i].value)) == false) {
                                    deviationSum = deviationSum + Math.pow((parseFloat(app.characters[characterIndex][i].value) - averageValue), 2);
                                }
                            }
                            deviationValue = Math.pow((deviationSum / (headerCount - 1)), 0.5).toFixed(2);
                        } else if (headerCount == 1) {
                            for (var i = 3; i < (app.characters[characterIndex].length); i ++) {
                                if (isNaN(parseFloat(app.characters[characterIndex][i].value)) == false) {
                                    deviationValue = parseFloat(app.characters[characterIndex][i].value).toFixed(2);
                                }
                            }
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
                        app.actionLog.model_name = "character";
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

                    for (var i = 0; i < app.characters.length; i++) {
                        app.characters[i][0].unit = resp.data.arrayCharacters[i].unit;
                    }
                })
                .catch(function (resp) {
                    console.log(resp);
                });

        },
        mounted() {
        }
    }
</script>