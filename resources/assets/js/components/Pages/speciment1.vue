<template>
    <layout>
        <div slot="section" class="">
            <div class="tab-pane" id="">
                <form class="row" autocomplete="off">
                    <div class="col-md-4">
                        New Character
                    </div>
                    <div class="col-md-4">
                        <input placeholder="Measure of Entity (e.g: length of leaf)" style="width: 100%;" v-model="character.name" name="text"/>
                    </div>
                    <div class="col-md-4">
                        <a v-on:click="storeCharacter()" class="btn btn-primary"
                           style="height: 28px; line-height: 28px; font-size: 50px; padding: 0 5px;">+</a>
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        Character List
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        <div style="overflow-x: auto; max-height: 850px;">
                            <table class="table table-bordered table-responsive measure-table">
                                <thead>
                                <tr v-if="headers.length > 0">
                                    <th style="min-width: 190px;"><input class="th-input" value="Character" /></th>
                                    <th style="min-width: 150px;"><input class="th-input" value="Average" /></th>
                                    <th style="min-width: 150px;"><input class="th-input" value="Deviation" /></th>
                                    <th v-if="header.id > 3" v-for="header in headers" style="min-width: 200px;">
                                        <input class="th-input" v-bind:value="header.header" />
                                        <a class="btn btn-add display-block" v-on:click="deleteHeader(header.id)"><span class="glyphicon glyphicon-remove"></span></a>
                                    </th>
                                    <!--<th v-for="header in headers" style="min-width: 150px;"><input class="th-input" v-bind:value="header.header" /></th>-->
                                    <th class="actions" style="min-width: 150px;">
                                        <input class="th-input display-none" v-model="newHeader.header" name="header" autofocus/>
                                        <a class="btn btn-add display-block" v-on:click="addHeader()" style="width: 100%; height:37px; "><span class="glyphicon glyphicon-plus"></span></a>
                                        <a class="btn btn-success btn-save display-none" v-on:click="saveHeader()"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                                        <a class="btn btn-danger btn-cancel display-none" v-on:click="cancelHeader()"><span class="glyphicon glyphicon-remove-circle"></span></a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="eachCharacter in characters">

                                    <td class="text-center" v-if="item.header_id == 1" v-for="item in eachCharacter">
                                        <div>
                                            {{ item.value }} ({{ item.unit }})
                                            <a class="btn" v-on:click="editCharacter(eachCharacter[eachCharacter.length - 1])"><span class="glyphicon glyphicon-edit"></span></a>
                                        </div>
                                    </td>
                                    <td class="text-center" v-if="item.header_id == 2" v-for="item in eachCharacter">
                                        {{ item.value }}
                                    </td>
                                    <td class="text-center" v-if="item.header_id == 3" v-for="item in eachCharacter">
                                        {{ item.value }}
                                    </td>
                                    <td v-if="item.header_id > 3" v-for="item in eachCharacter">
                                        <input v-if="item.header_id >= 4" class="td-input" v-model="item.value" v-on:blur="saveItem(item)"/>
                                    </td>
                                    <td class="actions text-center">
                                        <a class="btn" v-on:click="deleteCharacter(eachCharacter[0].character_id)"><span class="glyphicon glyphicon-trash"></span></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div v-if="detailsFlag" @close="detailsFlag = false">
                        <transition name="modal">
                            <div class="modal-mask">
                                <div class="modal-wrapper">
                                    <div class="modal-container">

                                        <div class="modal-header">
                                            <h3>Meta Details for {{ character.name }}</h3>
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
                                                        <a>{{ character.name }}</a>
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
                                            <div class="row">
                                                <div class="col-md-12 text-right" style="margin-top: 15px;">
                                                    <a :disabled="saveDisabled == true" v-on:click="saveCharacter()" class="btn btn-primary">Save</a>
                                                    <a v-on:click="cancelCharacter()" class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">

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
                saveDisabled: true,
                character: {
                    name: null,
                    method_from: null,
                    method_to: null,
                    method_as: null,
                    unit: null,
                    measure_semantic: null,
                    entity_semantic: null,
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
                editFlag: false,
                methodUpdateFlag: false,
                unitUpdateFlag: false,
                semanticsUpdateFlag: false,
                creatorUpdateFlag: false,

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
                        this.character.method_as = event[2];
                        this.parentData = event;
                        this.methodUpdateFlag = true;
                        console.log("method return", event);
                        break;
                    case 'unit':
                        this.character.unit = event;
                        this.parentData = event;
                        this.unitUpdateFlag = true;
                        break;
                    case 'semantics':
                        this.character.measure_semantic = event[0];
                        this.character.entity_semantic = event[1];
                        this.parentData = event;
                        this.semanticsUpdateFlag = true;
                        break;
                    case 'creator':
                        this.character.creator = event;
                        this.parentData = event;
                        this.creatorUpdateFlag = true;
                        break;
                    default:
                        break;
                }
                if ((((this.character.method_from != null && this.character.method_from != '') &&
                    (this.character.method_to != null && this.character.method_to != '')) ||
                    (this.character.method_as != null && this.character.method_as != '')) &&
                    ((this.character.measure_semantic != null && this.character.measure_semantic != '') &&
                    (this.character.entity_semantic != null && this.character.entity_semantic != '')) &&
                    (this.character.unit != null && this.character.unit != '')) {
                    this.saveDisabled = false;
                    console.log("enabled");
                } else {
                    this.saveDisabled = true;
                    console.log("disabled");
                }
                console.log('data after child handle: ', event); // get the data after child dealing
            },
            editCharacter (character) {
                this.saveDisabled = false;
                this.updatedFlag = false;
                this.methodUpdateFlag = false;
                this.unitUpdateFlag = false;
                this.semanticsUpdateFlag = false;
                this.creatorUpdateFlag = false;
                this.editFlag = true;
                console.log("character", character);
                sessionStorage.setItem("characterName", character.value);
                var app = this;
                axios.get("/mr/shared/public/api/v1/character/" + character.character_id)
                    .then(function (resp) {
                        console.log("get Character", resp);
                        app.metadataFlag = 'method';
                        app.character = resp.data;
                        app.parentData = [];
                        app.parentData.push(app.character.method_from);
                        app.parentData.push(app.character.method_to);
                        app.parentData[2] = app.character.method_as;
                        app.currentMetadata = method;
                        app.detailsFlag = true;
                        axios.get("/mr/shared/public/api/v1/meta-log/" + character.character_id)
                            .then(function (resp) {
                                console.log("history resp", resp);
                                app.character.history = [];
                                for (var i = 0; i < resp.data.metaHistory.length; i++) {
                                    app.character.history.push(resp.data.metaHistory[i]);
                                }
                                console.log("history", app.character.history);

                                axios.get("/mr/shared/public/api/v1/character/usage/" + app.character.id)
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
                        this.parentData[2] = this.character.method_as;
                        this.currentMetadata = method;
                        break;
                    case 'unit':
                        this.parentData = this.character.unit;
                        this.currentMetadata = unit;
                        break;
                    case 'semantics':
                        this.parentData = [];
                        this.character.semantics = [];
                        if (this.editFlag) {
                            this.character.semantics.push(this.character.measure_semantic);
                            this.character.semantics.push(this.character.entity_semantic);
                        }
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
                console.log('edit Flag', this.editFlag);


                var checkFields = true;

                for (var key in this.character) {
                    if (key != 'confirmed' && key != 'method_as' && key != 'method_from' && key != 'method_to' && key != 'measure_semantic' && key != 'entity_semantic' && key != 'usage' && key != 'history' && (this.character[key] == null || this.character[key] == '')) {
                        console.log(key);
                        checkFields = false;
                    }
                }
                if ((this.character['method_as'] == null || this.character['method_as'] == '') &&
                    ((this.character['method_from'] == null || this.character['method_from'] == '') ||
                    (this.character['method_to'] == null || this.character['method_to'] == ''))) {
                    checkFields = false;
                }
                var app = this;

                if (checkFields) {
                    this.detailsFlag = false;
                    this.updatedFlag = false;
                    axios.get('/mr/shared/public/api/v1/character/name')
                        .then(function (resp) {
                            console.log('get name resp', resp);
                            var checkName = true;

                            for (var i = 0; i < resp.data.length; i++) {
                                if (app.character.name == resp.data[i].name) {
                                    checkName = false;
                                }
                            }

                            console.log("app.editFlag", app.editFlag);

                            if (!app.editFlag && app.character.id) {
                                delete app.character['id'];
                            }

                            if (checkName || app.editFlag) {
                                axios.post('/mr/shared/public/api/v1/character/create', app.character)
                                    .then(function (resp) {
                                        console.log("resp", resp);
                                        app.characters = resp.data.characters;
                                        app.character = resp.data.character;
                                        for (var i = 0; i < app.characters.length; i++) {
                                            app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
                                        }

                                        if (app.editFlag) {
                                            if (app.methodUpdateFlag) {
                                                var jsonRequest = {

                                                };
                                                jsonRequest.character_id = app.character.id;
                                                jsonRequest.username = app.user.name;
                                                jsonRequest.description = 'updated method';
                                                axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                    .then(function(resp) {
                                                        console.log("update metalog", resp);
                                                        if (app.unitUpdateFlag) {
                                                            var jsonRequest = {

                                                            };
                                                            jsonRequest.character_id = app.character.id;
                                                            jsonRequest.username = app.user.name;
                                                            jsonRequest.description = 'updated unit';
                                                            axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                .then(function(resp) {
                                                                    console.log("update metalog", resp);
                                                                    if (app.semanticsUpdateFlag) {
                                                                        var jsonRequest = {

                                                                        };
                                                                        jsonRequest.character_id = app.character.id;
                                                                        jsonRequest.username = app.user.name;
                                                                        jsonRequest.description = 'updated semantics';
                                                                        axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                            .then(function(resp) {
                                                                                console.log("update metalog", resp);
                                                                                if (app.creatorUpdateFlag) {
                                                                                    var jsonRequest = {

                                                                                    };
                                                                                    jsonRequest.character_id = app.character.id;
                                                                                    jsonRequest.username = app.user.name;
                                                                                    jsonRequest.description = 'updated creator';
                                                                                    axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                                        .then(function(resp) {
                                                                                            console.log("update metalog", resp);
                                                                                        })
                                                                                        .catch(function(resp) {
                                                                                            console.log('error', resp);
                                                                                            alert('Error Occurred while meta logging.')
                                                                                        });
                                                                                }
                                                                            })
                                                                            .catch(function(resp) {
                                                                                console.log('error', resp);
                                                                                alert('Error Occurred while meta logging.')
                                                                            });
                                                                    }
                                                                })
                                                                .catch(function(resp) {
                                                                    console.log('error', resp);
                                                                    alert('Error Occurred while meta logging.')
                                                                });
                                                        }
                                                    })
                                                    .catch(function(resp) {
                                                        console.log('error', resp);
                                                        alert('Error Occurred while meta logging.')
                                                    });
                                            } else if (app.unitUpdateFlag) {
                                                var jsonRequest = {

                                                };
                                                jsonRequest.character_id = app.character.id;
                                                jsonRequest.username = app.user.name;
                                                jsonRequest.description = 'updated unit';
                                                axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                    .then(function(resp) {
                                                        console.log("update metalog", resp);
                                                        if (app.semanticsUpdateFlag) {
                                                            var jsonRequest = {

                                                            };
                                                            jsonRequest.character_id = app.character.id;
                                                            jsonRequest.username = app.user.name;
                                                            jsonRequest.description = 'updated semantics';
                                                            axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                .then(function(resp) {
                                                                    console.log("update metalog", resp);
                                                                    if (app.creatorUpdateFlag) {
                                                                        var jsonRequest = {

                                                                        };
                                                                        jsonRequest.character_id = app.character.id;
                                                                        jsonRequest.username = app.user.name;
                                                                        jsonRequest.description = 'updated creator';
                                                                        axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                            .then(function(resp) {
                                                                                console.log("update metalog", resp);
                                                                            })
                                                                            .catch(function(resp) {
                                                                                console.log('error', resp);
                                                                                alert('Error Occurred while meta logging.')
                                                                            });
                                                                    }
                                                                })
                                                                .catch(function(resp) {
                                                                    console.log('error', resp);
                                                                    alert('Error Occurred while meta logging.')
                                                                });
                                                        }
                                                    })
                                                    .catch(function(resp) {
                                                        console.log('error', resp);
                                                        alert('Error Occurred while meta logging.')
                                                    });
                                            } else if (app.semanticsUpdateFlag) {
                                                var jsonRequest = {

                                                };
                                                jsonRequest.character_id = app.character.id;
                                                jsonRequest.username = app.user.name;
                                                jsonRequest.description = 'updated semantics';
                                                axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                    .then(function(resp) {
                                                        console.log("update metalog", resp);
                                                        if (app.creatorUpdateFlag) {
                                                            var jsonRequest = {

                                                            };
                                                            jsonRequest.character_id = app.character.id;
                                                            jsonRequest.username = app.user.name;
                                                            jsonRequest.description = 'updated creator';
                                                            axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                                .then(function(resp) {
                                                                    console.log("update metalog", resp);
                                                                })
                                                                .catch(function(resp) {
                                                                    console.log('error', resp);
                                                                    alert('Error Occurred while meta logging.')
                                                                });
                                                        }
                                                    })
                                                    .catch(function(resp) {
                                                        console.log('error', resp);
                                                        alert('Error Occurred while meta logging.')
                                                    });
                                            } else if (app.creatorUpdateFlag) {
                                                var jsonRequest = {

                                                };
                                                jsonRequest.character_id = app.character.id;
                                                jsonRequest.username = app.user.name;
                                                jsonRequest.description = 'updated creator';
                                                axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                    .then(function(resp) {
                                                        console.log("update metalog", resp);
                                                    })
                                                    .catch(function(resp) {
                                                        console.log('error', resp);
                                                        alert('Error Occurred while meta logging.')
                                                    });
                                            }
                                        } else {
                                            var jsonRequest = {

                                            };
                                            jsonRequest.character_id = app.character.id;
                                            jsonRequest.username = app.user.name;
                                            jsonRequest.description = 'created';
                                            axios.post('/mr/shared/public/api/v1/meta-log', jsonRequest)
                                                .then(function(resp) {
                                                    console.log("create metalog", resp);
                                                })
                                                .catch(function(resp) {
                                                    console.log('error', resp);
                                                    alert('Error Occurred while meta logging.')
                                                });

                                        }
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
                axios.get('/mr/shared/public/api/v1/character/all')
                    .then(function(resp) {
                        var headerData = resp.data.headers;
                        var tpFlag = true;
                        for (var i = 0; i < headerData.length; i ++) {
                            if (app.newHeader.header == headerData[i].header) {
                                tpFlag = false;
                            }
                        }

                        if (tpFlag) {
                            axios.post('/mr/shared/public/api/v1/character/add-header', app.newHeader)
                                .then(function (resp) {
                                    console.log("createHeader resp", resp);
//                        $('.measure-table thead tr th:last-child').before("<th><input class='th-input' value='" + resp.data.header + "' /></th>");
                                    app.headers = resp.data.headers;
                                    app.characters = resp.data.characters;
                                    for (var i = 0; i < app.characters.length; i++) {
                                        app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
                                    }
                                    $('th.actions > .display-block').removeClass('display-block').addClass('display-none');
                                    $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
                                    app.actionLog.action_type = "create_header";
                                    app.actionLog.model_id = resp.data.characters[0][resp.data.characters[0].length - 1].header_id;
                                    app.actionLog.model_name = "header";
                                    axios.post('/mr/shared/public/api/v1/log', app.actionLog)
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
                        } else {
                            alert("The header is already exist. Please check the header name.");
                        }
                    })
                    .catch(function(resp) {
                        console.log("resp", resp);
                    });

            },
            deleteHeader: function(headerId) {
                var app = this;
                console.log("headerId", headerId);
                axios.post('/mr/shared/public/api/v1/character/delete-header/' + headerId)
                    .then(function(resp) {
                        console.log('deleteHeader resp', resp);
                        app.headers = resp.data.headers;
                        app.characters = resp.data.characters;
                        for (var i = 0; i < app.characters.length; i++) {
                            app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
                        }
                        app.actionLog.action_type = "delete_header";
                        app.actionLog.model_id = resp.data.characters[0][resp.data.characters[0].length - 1].header_id;
                        app.actionLog.model_name = "header";
                        axios.post('/mr/shared/public/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log header !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });
                        var totalSum = [];
                        var headerCount = [];
                        var averageValue = [];

                        var deviationSum = [];
                        var deviationValue = [];

                        for (var i = 0; i < app.characters.length; i++) {
                            totalSum[i] = 0;
                            headerCount[i] = 0;
                            averageValue[i] = 0;
                            deviationSum[i] = 0;
                            deviationValue[i] = 0;
                            for (var j = 0; j < app.characters[i].length - 3; j++){
                                if (app.characters[i][j].header_id > 3) {
                                    if (isNaN(parseFloat(app.characters[i][j].value)) == false) {
                                        headerCount[i]++;
                                        totalSum[i] = totalSum[i] + parseFloat(app.characters[i][j].value);
                                    }
                                }
                            }

                            if (headerCount[i] > 0) {
                                averageValue[i] = (totalSum[i] / headerCount[i]).toFixed(2);
                                app.characters[i][app.characters[i].length - 2].value = (totalSum[i] / headerCount[i]).toFixed(2);
                                axios.post('/mr/shared/public/api/v1/character/update', app.characters[i][app.characters[i].length - 2])
                                    .then(function (resp) {
                                        console.log('update average', resp);
                                    })
                                    .catch(function (resp) {
                                        console.log(resp);
                                        alert("Error Occured !");
                                    });

                                console.log("headerCount ", headerCount[i]);
                                if (headerCount[i] > 1) {
                                    for (var j = 0; j < (app.characters[i].length - 3); j++) {
                                        console.log('check for deviation', isNaN(parseFloat(app.characters[i][j].value)));
                                        if (isNaN(parseFloat(app.characters[i][j].value)) == false) {
                                            deviationSum[i] = deviationSum[i] + Math.pow((parseFloat(app.characters[i][j].value) - averageValue[i]), 2);
                                        }
                                    }
                                    deviationValue[i] = Math.pow((deviationSum[i] / (headerCount[i] - 1)), 0.5).toFixed(2);
                                } else if (headerCount[i] == 1) {
                                    for (var j = 0; j < (app.characters[i].length - 3); j++) {
                                        if (isNaN(parseFloat(app.characters[i][j].value)) == false) {
                                            deviationValue[i] = parseFloat(app.characters[i][j].value).toFixed(2);
                                        }
                                    }
                                }
                                app.characters[i][app.characters[i].length - 3].value = deviationValue[i];
                                axios.post('/mr/shared/public/api/v1/character/update', app.characters[i][app.characters[i].length - 3])
                                    .then(function (resp) {
                                        console.log('update deviation', resp);
                                    })
                                    .catch(function (resp) {
                                        console.log(resp);
                                        alert("Error Occured !");
                                    });

                            }
                        }
                    })
                    .catch(function(resp) {
                        console.log('error deleteHeader', resp);
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
                var app = this;
                var tpArray = this.character.name.split(' ');
                this.character.method_from = null;
                this.character.method_to = null;
                this.character.method_as = null;
                this.character.unit = null;
                this.character.measure_semantic = null;
                this.character.entity_semantic = null;
                this.character.creator = this.user.name;
                this.character.usage = [];
                this.character.history = [];

                this.methodUpdateFlag = false;
                this.unitUpdateFlag = false;
                this.semanticsUpdateFlag = false;
                this.creatorUpdateFlag = false;


                var tpFlag = false;
                for (var i = 0; i < tpArray.length; i++) {
                    if (tpArray[i] == 'of') {
                        tpFlag = true;
                    }
                }
                if (tpFlag) {
                    sessionStorage.setItem("characterName", this.character.name);
                    console.log("detailsFlag", this.detailsFlag);
                    app.parentData = [];
                    app.parentData[0] = "";
                    app.parentData[1] = "";
                    app.currentMetadata = method;
                    app.detailsFlag = true;
                    app.editFlag = false;
                    app.metadataFlag = "method";
                } else {
                    alert("The header name should contain 'of' word.");
                }

            },
            detailComponent: function(componentId) {
                console.log("componentId", componentId);
            },
            saveItem: function(item) {
                var app = this;
                if (item.value < 0) {
                    alert("Value should be only positive value.");
                    item.value = '';
                } else {
                    axios.post('/mr/shared/public/api/v1/character/update', item)
                        .then(function (resp) {
                            console.log("update item", resp.data);
                            var updatedCharacter = resp.data;

                            app.actionLog.action_type = "update";
                            app.actionLog.model_id = resp.data.character_id;
                            app.actionLog.model_name = "value";
                            axios.post('/mr/shared/public/api/v1/log', app.actionLog)
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
                                app.characters[characterIndex][app.characters[characterIndex].length - 2].value = (totalSum / headerCount).toFixed(2);
                            }

                            axios.post('/mr/shared/public/api/v1/character/update', app.characters[characterIndex][app.characters[characterIndex].length - 2])
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
                                for (var i = 0; i < (app.characters[characterIndex].length - 3); i++) {
                                    console.log("check value for deviation", app.characters[characterIndex][i].value);
                                    console.log("check of parse for deviation", isNaN(parseFloat(app.characters[characterIndex][i].value)));
                                    if (isNaN(parseFloat(app.characters[characterIndex][i].value)) == false) {
                                        deviationSum = deviationSum + Math.pow((parseFloat(app.characters[characterIndex][i].value) - averageValue), 2);
                                    }
                                }
                                deviationValue = Math.pow((deviationSum / (headerCount - 1)), 0.5).toFixed(2);
                            } else if (headerCount == 1) {
                                for (var i = 0; i < (app.characters[characterIndex].length - 3); i++) {
                                    if (isNaN(parseFloat(app.characters[characterIndex][i].value)) == false) {
                                        deviationValue = parseFloat(app.characters[characterIndex][i].value).toFixed(2);
                                    }
                                }
                            }

                            app.characters[characterIndex][app.characters[characterIndex].length - 3].value = deviationValue;

                            axios.post('/mr/shared/public/api/v1/character/update', app.characters[characterIndex][app.characters[characterIndex].length - 3])
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
                }
            },
            deleteCharacter: function(character_id) {
                var app = this;
                var tpData = {
                    character_id: character_id
                };
                axios.post('/mr/shared/public/api/v1/character/delete', tpData)
                    .then(function (resp) {
                        console.log("resp", resp);
                        app.characters = resp.data.characters;
                        for (var i = 0; i < app.characters.length; i++) {
                            app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
                        }
                        app.actionLog.action_type = "delete";
                        app.actionLog.model_id = tpData.character_id;
                        app.actionLog.model_name = "character";
                        axios.post('/mr/shared/public/api/v1/log', app.actionLog)
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
            axios.get('/mr/shared/public/api/v1/character/all')
                .then(function (resp) {
                    console.log(resp);
                    app.headers = resp.data.headers;
                    app.characters = resp.data.characters;

                    for (var i = 0; i < app.characters.length; i++) {
                        app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
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