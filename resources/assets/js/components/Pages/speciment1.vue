<template>
    <layout>
        <div slot="section" class="">
            <div class="tab-pane" id="">
                <form class="row" autocomplete="off">
                    <div class="col-md-4">
                        Find or create character<br>Include 'of' or 'between' in character name, e.g., 'diameter of ball'.
                    </div>
                    <div class="col-md-4">
                        <!--<input placeholder="Measure of Entity (e.g: length of leaf)" style="width: 100%;" v-model="character.name" name="text"/>-->
                        <!--<list-select :list="arraySearch"-->
                                     <!--option-value="code"-->
                                     <!--option-text="name"-->
                                     <!--:selected-item="item"-->
                                     <!--placeholder="select item"-->
                                     <!--@select="onSelect">-->
                        <!--</list-select>-->
                        <model-select :options="arraySearch"
                                      v-model="item"
                                      placeholder="Type in here to find or create a character"
                                      @searchchange="printSearchText"
                                      @select="onSelect">
                        </model-select>
                    </div>
                    <div class="col-md-4 text-center">
                        <a v-on:click="addHeader()" class="btn btn-primary"
                           style="height: 28px; line-height: 28px; font-size: 50px; padding: 0 5px;">+</a>
                        <br/>
                        Add a column <br/> for measurements
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        Character List
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        <div style="overflow-x: auto; max-height: 850px;">
                            <table class="table table-bordered table-responsive measure-table">
                                <thead>
                                <tr v-if="headers.length > 0">
                                    <th class="actions" style="min-width: 250px; text-align:center; line-height: 45px;">
                                        Character
                                        <!--<a class="btn btn-add display-block" v-on:click="addHeader()"><span class="glyphicon glyphicon-plus"></span></a>-->
                                    </th>
                                    <th style="min-width: 150px; text-align: center; line-height: 45px;">
                                        <p style="margin-bottom:0px">Value Range</p>
                                        <p style="margin-top:-20px;margin-bottom:-8px">(auto computed)</p>
                                    </th>
                                    <!-- Average and Deviation column start -->
                                    <!--<th style="min-width: 150px;"><input class="th-input" value="Average" /></th>-->
                                    <!--<th style="min-width: 150px;"><input class="th-input" value="Deviation" /></th>-->
                                    <!-- Average and Deviation column end -->

                                    <th v-if="header.id >= 4" v-for="header in headers" style="min-width: 200px;">
                                        <input class="th-input" v-bind:value="header.header" /> <!-- v-on:blur="saveItem($event, header)" -->
                                        <a class="btn btn-add display-block" v-on:click="deleteHeader(header.id, header.header)"><span class="glyphicon glyphicon-remove"></span></a>
                                    </th>
                                    <th class="actions display-none" style="min-width: 250px;">
                                        <input style="width: 50%;" id="new-header" class="th-input" v-model="newHeader.header" name="header" autofocus/>
                                        <a class="btn btn-success btn-save" v-on:click="saveHeader()"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                                        <a class="btn btn-danger btn-cancel" v-on:click="cancelHeader()"><span class="glyphicon glyphicon-remove-circle"></span></a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="eachCharacter in characters" v-if="getCharacterUsingId(eachCharacter[0].character_id).show_flag == true">
                                    <!-- && getCharacterUsingId(eachCharacter[0].character_id).username == user.name -->
                                    <td class="text-center" v-if="item.header_id == 1" v-for="item in eachCharacter">
                                        <div>
                                            {{ item.value }} ({{ item.username }})<br> ({{ item.unit }})
                                            <a class="btn" v-on:click="editCharacter(eachCharacter[eachCharacter.length - 2])"><span class="glyphicon glyphicon-edit"></span></a>
                                            <a class="btn" v-on:click="deleteCharacter(eachCharacter[0].character_id)"><span class="glyphicon glyphicon-trash"></span></a>

                                        </div>
                                    </td>
                                    <td class="text-center" v-if="item.header_id==0" v-for="item in eachCharacter" style="line-height: 45px;">
                                        {{ item.value }}
                                    </td>

                                    <td v-if="item.header_id >= 4" v-for="item in eachCharacter">
                                        <input v-if="item.header_id >= 4" class="td-input" v-model="item.value" v-on:blur="saveItem($event, item)"/>
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div v-if="newCharacterFlag" @close="newCharacterFlag = false">
                        <transition name="modal">
                            <div class="modal-mask">
                                <div class="modal-wrapper">
                                    <div class="modal-container">
                                        <div class="modal-header">
                                            Input the character name in the input box and click OK.
                                        </div>
                                        <div class="modal-body">
                                            Character name:
                                            <input autofocus v-model="character.name" />
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-primary" v-on:click="storeCharacter()">OK</a>
                                            <a v-on:click="cancelNewCharacter()" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <div v-if="detailsFlag" @close="detailsFlag = false">
                        <transition name="modal">
                            <div class="modal-mask">
                                <div class="modal-wrapper">
                                    <div class="modal-container">

                                        <div class="modal-header">
                                            <h3>Information about "{{ character.name }}" by {{ character.username }}</h3>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 radial-menu">
                                                    <ul style="margin-left: auto; margin-right: auto;">
                                                        <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                        <li class="method"><a v-on:click="showDetails('method', metadataFlag)">1. Method<br><span class="glyphicon glyphicon-edit"></span></a></li>
                                                        <li class="unit"><a v-on:click="showDetails('unit', metadataFlag)">2. Unit<br><span class="glyphicon glyphicon-edit"></span></a></li>
                                                        <li class="creator"><a v-on:click="showDetails('creator', metadataFlag)">Creator</a></li>
                                                        <li><a v-on:click="showDetails('usage', metadataFlag)">Usage</a></li>
                                                        <li><a v-on:click="showDetails('history', metadataFlag)">History</a></li>
                                                        <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                        <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                        <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                    </ul>
                                                    <div class="center">
                                                        <a>{{ character.name }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="metadataPlace">
                                                        <div :is="currentMetadata" :parentData="parentData"
                                                             @interface="handleFcAfterDateBack">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-right" style="margin-top: 15px;">
                                                    <a v-if="viewFlag == false" :disabled="saveDisabled == true" v-on:click="saveCharacter(metadataFlag)" class="btn btn-primary">Save</a>
                                                    <a v-if="viewFlag == true" v-on:click="use(item)" class="btn btn-primary">Use this</a>
                                                    <a v-if="viewFlag == true" v-on:click="enhance(item)" class="btn btn-primary">Clone and enhance</a>
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

    /* import { ModelSelect } from 'vue-search-select' */
    import { ListSelect } from '../vue-search-select-lib'
    import { ModelSelect } from '../vue-search-select-lib'
    Vue.use({ListSelect});
    Vue.use({ModelSelect});
    /* import vSelect from 'vue-select';
    Vue.component('v-select', vSelect); */

    export default {
        props: [
            'user'
        ],
        data: function () {
            return {
                arraySearch: [

                ],
                newCharacterFlag: false,
                item: {
                    value: '',
                    text: ''
                },
                viewFlag: false,
                searchText: '',
                cloneFlag: false,
                saveDisabled: true,
                character: {
                    name: null,
                    method_from: null,
                    method_to: null,
                    method_as: null,
                    method_include: null,
                    method_exclude: null,
                    method_at: null,
                    unit: null,
                    measure_semantic: null,
                    entity_semantic: null,
                    semantics: null,
                    creator: this.user.name + ' via MR',
                    usage: [],
                    history: [],
                    username: this.user.name,
                    usage_count: 0,
                    show_flag: true,
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
                metadataFlag: null,
                detailsFlag: false,
                updatedFlag: false,
                showModal: false,
                editFlag: false,
                methodUpdateFlag: false,
                unitUpdateFlag: false,
                semanticsUpdateFlag: false,
                creatorUpdateFlag: false,
                term: null,
                termValue: null,
                arrayCharacters: [],
            }
        },

        methods: {
            handleFcAfterDateBack (event) {
                console.log('hadleFcAfterDateBack function inited');
                var app = this;
                this.updatedFlag = true;
                $('.center').addClass('back-yellow');
                $('.' + this.metadataFlag).addClass('back-median-green');
                switch (this.metadataFlag) {
                    case 'method':
                        /* this.character.method_from = event[0];
                        this.character.method_to = event[1]; */
                        this.character.method_as = event[0];
                        app.term = event[1];
                        app.termValue = event[2];
                        this.character.method_from = event[4];
                        this.character.method_to = event[5];
                        this.character.method_include = event[6];
                        this.character.method_exclude = event[7];
                        this.character.method_at = event[8];
                        this.parentData = event;
                        this.methodUpdateFlag = true;
                        console.log("method return", event);
                        /* var jsonRequest = {
                            'user_id': app.user.id,
                            'action': 'clicked method image for "' + app.character.name + '"',
                            'type': 'Measurement Recorder',
                            'action_detail': app.character.method_as
                        };
                        axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                            .then(function(resp) {
                                console.log("userLog resp", resp);
                            }); */
                        break;
                    case 'unit':
                        this.character.unit = event;
                        this.parentData = event;
                        this.unitUpdateFlag = true;
                        var jsonRequest = {
                            'user_id': app.user.id,
                            'action': 'set the unit',
                            'type': 'Measurement Recorder',
                            'action_detail': 'unit=' + app.character.unit
                        };
                        axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                            .then(function(resp) {
                                console.log("userLog resp", resp);
                            });
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
                app.setSaveDisabled();
                console.log('data after child handle: ', event); // get the data after child dealing
            },
            getCharacterUsingId(id) {
                var app = this;
                for (var i = 0; i < app.arrayCharacters.length; i++) {
                    if (app.arrayCharacters[i].id == id) {
                        return app.arrayCharacters[i];
                    }
                }

            },
            editCharacter (character, metadataFlag = '', editFlag = true) {
                var app = this;
                console.log('metadataFlag', metadataFlag);
                this.saveDisabled = false;
                this.updatedFlag = false;
                this.methodUpdateFlag = false;
                this.unitUpdateFlag = false;
                this.semanticsUpdateFlag = false;
                this.creatorUpdateFlag = false;
                this.editFlag = editFlag;
                console.log("character log in edit character", character);
                sessionStorage.setItem("characterName", character.value);
                axios.get("/mr/individual/public/api/v1/character/" + character.character_id)
                    .then(function (resp) {
                        console.log("get Character", resp);
                        /* app.metadataFlag = 'method'; */
                        app.metadataFlag = metadataFlag;
                        app.character = resp.data;
                        if (metadataFlag != '') {
                            app.character.creator = app.user.name + ' via MR';
                            app.character.username = app.user.name;
                        }
                        if (app.character.username.indexOf(app.user.name) < 0) {
                            sessionStorage.setItem('edit_created_other', true);
                        } else {
                            sessionStorage.setItem('edit_created_other', false);
                        }

                        /* app.parentData = [];
                        app.parentData.push(app.character.method_from);
                        app.parentData.push(app.character.method_to);
                        app.parentData[2] = app.character.method_as; */
                        switch (metadataFlag) {
                            case 'method':
                                app.parentData = [];
                                /* app.parentData.push(app.character.method_from);
                                app.parentData.push(app.character.method_to); */
                                app.parentData.push(app.character.method_as);
                                app.parentData[3] = app.user;
                                app.parentData[4] = app.character.method_from;
                                app.parentData[5] = app.character.method_to;
                                app.parentData[6] = app.character.method_include;
                                app.parentData[7] = app.character.method_exclude;
                                app.parentData[8] = app.character.method_at;
                                app.currentMetadata = method;
                                break;
                            case 'unit':
                                app.parentData = app.character.unit;
                                app.currentMetadata = unit;
                                break;
                            case 'semantics':
                                app.parentData = [];
                                app.character.semantics = [];
                                if (app.editFlag || app.cloneFlag) {
                                    app.character.semantics.push(app.character.measure_semantic);
                                    app.character.semantics.push(app.character.entity_semantic);
                                }
                                app.parentData = app.character.semantics;
                                app.currentMetadata = semantics;
                                break;
                            case 'creator':
                                app.parentData = app.character.creator;
                                app.currentMetadata = creator;
                                break;
                            case 'usage':
                                app.parentData = app.character.usage;
                                app.currentMetadata = usage;
                                break;
                            case 'history':
                                app.parentData = app.character.history;
                                app.currentMetadata = history;
                                break;
                            default:
                                app.parentData = [];
                                /* app.parentData.push(app.character.method_from);
                                app.parentData.push(app.character.method_to); */
                                app.parentData.push(app.character.method_as);
                                app.parentData[3] = app.user;
                                app.parentData[4] = app.character.method_from;
                                app.parentData[5] = app.character.method_to;
                                app.parentData[6] = app.character.method_include;
                                app.parentData[7] = app.character.method_exclude;
                                app.parentData[8] = app.character.method_at;
                                app.showDetails('method', null);
                                app.currentMetadata = method;
                                break;
                        }
                        /* app.currentMetadata = metadataFlag;
                        app.currentMetadata = null; */
                        app.detailsFlag = true;
                        localStorage.clear();
                        var jsonRequest = {
                            'user_id': app.user.id,
                            'action': 'clicked on "Edit Icon" on "' + character.value + '"',
                            'type': 'Measurement Recorder'
                        };
                        axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                            .then(function(resp) {
                                console.log('userLog resp', resp);
                            })
                            .catch(function(resp) {
                                console.log('userLog error', resp);
                            });
                        axios.get("/mr/individual/public/api/v1/meta-log/" + character.character_id)
                            .then(function (resp) {
                                console.log("history resp", resp);
                                app.character.history = [];
                                for (var i = 0; i < resp.data.metaHistory.length; i++) {
                                    app.character.history.push(resp.data.metaHistory[i]);
                                }
                                console.log("history", app.character.history);

                                axios.get("/mr/individual/public/api/v1/character/usage/" + app.character.id)
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
            showDetails(metadata, previousMetadata = null) {
                var app = this;
                const prev_methodFrom = localStorage.getItem('methodFrom');
                const prev_methodTo = localStorage.getItem('methodTo');
                const prev_methodInclude = localStorage.getItem('methodInclude');
                const prev_methodExclude = localStorage.getItem('methodExclude');
                const prev_methodAt = localStorage.getItem('methodAt');
                if (metadata !== 'method' && !app.methodUpdateFlag && 
                    (!!prev_methodFrom || !!prev_methodTo ||
                    !!prev_methodInclude || !!prev_methodExclude || !!prev_methodAt)) {
                    alert('Please check input by pressing CHECK button');
                    return;
                }
                console.log("metadata", metadata);
                console.log("this.character=", this.character);
                this.metadataFlag = metadata;
                switch (metadata) {
                    case 'method':
                        this.parentData = [];
                        /* this.parentData.push(this.character.method_from);
                        this.parentData.push(this.character.method_to); */
                        if (!app.character.method_from && !!prev_methodFrom) app.character.method_from = prev_methodFrom;
                        if (!app.character.method_to && !!prev_methodTo) app.character.method_to = prev_methodTo;
                        if (!app.character.method_include && !!prev_methodInclude) app.character.method_include = prev_methodInclude;
                        if (!app.character.method_exclude && !!prev_methodExclude) app.character.method_exclude = prev_methodExclude;
                        if (!app.character.method_at && !!prev_methodAt) app.character.method_at = prev_methodAt;

                        this.parentData[0] = this.character.method_as;
                        app.parentData[3] = app.user;
                        app.parentData[4] = app.character.method_from;
                        app.parentData[5] = app.character.method_to;
                        app.parentData[6] = app.character.method_include;
                        app.parentData[7] = app.character.method_exclude;
                        app.parentData[8] = app.character.method_at;
                        app.setSaveDisabled();
                        this.currentMetadata = method;
                        break;
                    case 'unit':
                        this.parentData = this.character.unit;
                        this.currentMetadata = unit;
                        break;
                    case 'semantics':
                        this.parentData = [];
                        this.character.semantics = [];
                        if (this.editFlag || this.cloneFlag) {
                            this.character.semantics.push(this.character.measure_semantic);
                            this.character.semantics.push(this.character.entity_semantic);
                        }
                        this.parentData = this.character.semantics;
                        this.currentMetadata = semantics;
                        break;
                    case 'creator':
                        this.parentData = this.character.username + ' via MR';//this.character.creator;
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

             /*    if (previousMetadata != null) {
                    var jsonRequest = {
                        'user_id': app.user.id,
                        'type': 'Measurement Recorder',
                        'action': 'entered ' + previousMetadata + ' for "' + app.character.name + '"',
                        'action_detail': ' '
                    };
                    console.log('previousMetadata', previousMetadata);

                    switch(previousMetadata) {
                        case '':
                            break;
                        case 'method':
                            if (app.character.method_as != null) {
                                jsonRequest.action_detail = jsonRequest.action_detail + 'As: ' + app.character.method_as + '; ';
                            }
                            if (app.character.method_from != null) {
                                jsonRequest.action_detail = jsonRequest.action_detail + 'From: ' + app.character.method_from + '; ';
                            }
                            if (app.character.method_to != null) {
                                jsonRequest.action_detail = jsonRequest.action_detail + 'To: ' + app.character.method_to + '; ';
                            }
                            break;
                        case 'unit':
                            jsonRequest.action_detail = app.character.unit;
                            break;
                        default:
                            break;
                    }
                    axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                        .then(function(resp) {
                            console.log('userLog resp', resp);
                        })
                        .catch(function(resp) {
                            console.log('userLog error', resp);
                        });
                } */

                if (metadata != '') {
                    var jsonRequest = {
                        'user_id': app.user.id,
                        'action': 'clicked on ' + metadata,
                        'type': 'Measurement Recorder'
                    };
                    axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                        .then(function(resp) {
                            console.log('userLog resp', resp);
                        })
                        .catch(function(resp) {
                            console.log("userLog error", resp);
                        });
                }

            },
            use(characterId) {
                console.log('characterId', characterId);
                var app = this;
                let used_character_name = '';
                sessionStorage.setItem('viewFlag', false);
                for (var i = 0; i < app.arrayCharacters.length; i++) {
                    if (app.arrayCharacters[i].id == characterId) {
                        /* var tempFlag = true;
                        for (var j = 0; j < app.arrayCharacters.length; j++) {
                            if (app.arrayCharacters[j].name == app.arrayCharacters[i].name && app.arrayCharacters[j].show_flag == true) {
                                tempFlag = false;
                            }
                        } */
                        if (app.arrayCharacters[i].show_flag) {
                            alert('The character is already exist!');
                            app.log('/mr/individual/public/api/v1/user-log', {
                                'user_id': app.user.id,
                                'action': 'failed to use character',
                                'action_detail': '',
                                'type': 'Measurement Recorder',
                                'abnormal_system_response': 'Character already exist',
                            });
                        } else {
                            app.arrayCharacters[i].show_flag = true;
                            axios.post('/mr/individual/public/api/v1/character/set-charashow', {
                                character_id: app.arrayCharacters[i].id,
                                user_id: app.user.id,
                                show_flag: 1
                            });
                            /* axios.post('/mr/individual/public/api/v1/character/undelete', {
                                    character_id: app.arrayCharacters[i].id
                                }); */
                                /* if (app.user.name == app.arrayCharacters[i].username)
                                // app.arrayCharacters[i].username = app.user.name; */
                                /* var tempObj = {
                                    name: app.arrayCharacters[i].name,
                                    method_from: app.arrayCharacters[i].method_from,
                                    method_to: app.arrayCharacters[i].method_to,
                                    method_as: app.arrayCharacters[i].method_as,
                                    method_include: app.arrayCharacters[i].method_include,
                                    method_exclude: app.arrayCharacters[i].method_exclude,
                                    method_at: app.arrayCharacters[i].method_at,
                                    unit: app.arrayCharacters[i].unit,
                                    measure_semantic: app.arrayCharacters[i].measure_semantic,
                                    entity_semantic: app.arrayCharacters[i].entity_semantic,
                                    semantics: app.arrayCharacters[i].semantics,
                                    creator: app.arrayCharacters[i].creator,
                                    usage: [],
                                    history: [],
                                    username: app.arrayCharacters[i].username,
                                    usage_count: 0,
                                    show_flag: app.arrayCharacters[i].show_flag,
                                };
                                axios.post('/mr/individual/public/api/v1/character/create', tempObj)
                                    .then(function(resp) {
                                        console.log('use resp', resp);
                                        app.characters = resp.data.characters;
                                        app.arrayCharacters = resp.data.arrayCharacters;
                                        for (var i = 0; i < app.characters.length; i++) {
                                            app.characters[i][app.characters[i].length - 1].unit = resp.data.arrayCharacters[i].unit;
                                            app.characters[i][app.characters[i].length - 1].username = resp.data.arrayCharacters[i].username;
                                        }
                                        app.arraySearch = [];
                                        for (var i = 0; i < resp.data.arrayCharacters.length; i++) {
                                            var temp = {

                                            };
                                            temp.text = resp.data.arrayCharacters[i].name + ' by ' + resp.data.arrayCharacters[i].username + ' (' + resp.data.arrayCharacters[i].usage_count + ')';
                                            temp.value = resp.data.arrayCharacters[i].id;
                                            app.arraySearch.push(temp);
                                        }
                                        //app.addLastItemToDropdown();
                                    }); */
                        }
                        used_character_name = app.arrayCharacters[i].name;
                    }
                }
                app.log('/mr/individual/public/api/v1/user-log', {
                    'user_id': app.user.id,
                    'action': 'clicked the "Use This" button',
                    'action_detail': used_character_name,
                    'type': 'Measurement Recorder'
                });
                app.viewFlag = false;
                app.editFlag = false;
                app.detailsFlag = false;

                /* axios.get('/mr/individual/public/api/v1/character/' + characterId)
                    .then(function(resp) {
                        console.log('getCharacter resp', resp);
                        var newCharacter = resp.data;
                        newCharacter.username = app.user.name;
                        app.viewFlag = false;
                        app.editFlag = false;
                        app.character = newCharacter;
                        app.saveCharacter();

                    })
                    .catch(function(resp) {
                        console.log("getCharacter error", resp);
                    }); */
            },
            enhance(characterId) {
                var app = this;
                sessionStorage.setItem('viewFlag', false);
                axios.get('/mr/individual/public/api/v1/character/' + characterId)
                    .then(function(resp) {
                        console.log('getCharacter resp', resp);
                        var newCharacter = resp.data;
                        newCharacter.username = app.user.name;
                        newCharacter.creator = app.user.name + ' via MR';
                        newCharacter.show_flag = true;
                        app.detailsFlag = false;
                        app.viewFlag = false;
                        app.editFlag = false;
                        app.cloneFlag = true;
                        app.character = newCharacter;
                        for (var i = 0; i < app.characters.length; i++) {
                            for (var j = 0; j < app.characters[i].length; j++) {
                                if (app.characters[i][j].header_id == 1 && app.characters[i][j].character_id == newCharacter.id) {
                                    app.editCharacter(app.characters[i][j], app.metadataFlag, false);
                                }
                            }
                        }
                        /* app.editCharacter(app.character); */
                    })
                    .catch(function(resp) {
                        console.log('getCharacter error', resp);
                    });
                app.log('/mr/individual/public/api/v1/user-log', {
                    'user_id': app.user.id,
                    'action': 'clicked the "Clone and Enhance" button',
                    'action_detail': '',
                    'type': 'Measurement Recorder'
                });
            },
            saveCharacter (currentMetadata = null) {
                var app = this;
                console.log('save character', this.character);
                console.log('edit Flag', this.editFlag);
                app.show_flag = true;

                var checkFields = true;
                if ((this.character['method_as'] == null || this.character['method_as'] == '') &&
                    (this.character['method_from'] == null || this.character['method_from'] == '') &&
                    (this.character['method_to'] == null || this.character['method_to'] == '') &&
                    (this.character['method_include'] == null || this.character['method_include'] == '') &&
                    (this.character['method_exclude'] == null || this.character['method_exclude'] == '') &&
                    (this.character['method_at'] == null || this.character['method_at'] == '')) {
                    checkFields = false;
                }

                if (!app.character['unit']) {
                    checkFields = false;
                }

                if (checkFields) {
                    this.detailsFlag = false;
                    this.updatedFlag = false;
                    axios.get('/mr/individual/public/api/v1/character/all')
                        .then(function (resp) {
                            console.log('get name resp', resp);
                            var checkName = true;

                            for (var i = 0; i < resp.data.arrayCharacters.length; i++) {
                                const character_creators = resp.data.arrayCharacters[i].username.split(';');
                                if ((app.character.name == resp.data.arrayCharacters[i].name) && (character_creators.indexOf(app.character.username)>-1)) {
                                    console.log('character username', app.character.username);
                                    console.log('resp username', resp.data.arrayCharacters[i].username);
                                    checkName = false;
                                }
                            }

                            console.log("app.editFlag", app.editFlag);

                            if (!app.editFlag && app.character.id) {
                                delete app.character['id'];
                            }
                            if (checkName || app.editFlag) {
                                var jsonUserLog = {
                                    'user_id': app.user.id,
                                    'type': 'Measurement Recorder',
                                    'action': 'entered method for "' + app.character.name + '"',
                                    'action_detail': ''
                                };
                                switch (currentMetadata) {
                                    case 'method':
                                        if (app.character.method_as != null) {
                                            jsonUserLog.action_detail = jsonUserLog.action_detail + 'As: ' + app.character.method_as + '; ';
                                        }
                                        break;
                                    case 'unit':
                                        jsonUserLog.action_detail = app.character.unit;
                                        break;
                                    case 'semantics':
                                        jsonUserLog.action_detail = 'Measure: ' + app.character.measure_semantic + '; Entity: ' + app.character.entity_semantic;
                                        break;
                                    default:
                                        break;
                                }
                                app.log('/mr/individual/public/api/v1/user-log', jsonUserLog);
                                console.log('app.term', app.term);

                                if (app.term == app.character.name) {
                                    axios.post('/mr/individual/public/api/v1/character/create', app.character)
                                        .then(function (resp) {
                                            console.log("resp", resp);
                                            app.characters = resp.data.characters;
                                            app.character = resp.data.character;
                                            app.arrayCharacters = resp.data.arrayCharacters;
                                            app.updateArraySearch();

                                            if (app.editFlag) {
                                                if (app.methodUpdateFlag) {
                                                    var jsonRequest = {

                                                    };
                                                    jsonRequest.character_id = app.character.id;
                                                    jsonRequest.username = app.user.name;
                                                    jsonRequest.description = 'updated method';
                                                    axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                        .then(function(resp) {
                                                            console.log("update metalog", resp);
                                                            if (app.unitUpdateFlag) {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = app.user.name;
                                                                jsonRequest.description = 'updated unit';
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("update metalog", resp);
                                                                        if (app.semanticsUpdateFlag) {
                                                                            var jsonRequest = {

                                                                            };
                                                                            jsonRequest.character_id = app.character.id;
                                                                            jsonRequest.username = app.user.name;
                                                                            jsonRequest.description = 'updated semantics';
                                                                            axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                                .then(function(resp) {
                                                                                    console.log("update metalog", resp);
                                                                                    if (app.creatorUpdateFlag) {
                                                                                        var jsonRequest = {

                                                                                        };
                                                                                        jsonRequest.character_id = app.character.id;
                                                                                        jsonRequest.username = app.user.name;
                                                                                        jsonRequest.description = 'updated creator';
                                                                                        axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
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
                                                    axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                        .then(function(resp) {
                                                            console.log("update metalog", resp);
                                                            if (app.semanticsUpdateFlag) {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = app.user.name;
                                                                jsonRequest.description = 'updated semantics';
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("update metalog", resp);
                                                                        if (app.creatorUpdateFlag) {
                                                                            var jsonRequest = {

                                                                            };
                                                                            jsonRequest.character_id = app.character.id;
                                                                            jsonRequest.username = app.user.name;
                                                                            jsonRequest.description = 'updated creator';
                                                                            axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
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
                                                } else if (app.creatorUpdateFlag) {
                                                    var jsonRequest = {

                                                    };
                                                    jsonRequest.character_id = app.character.id;
                                                    jsonRequest.username = app.user.name;
                                                    jsonRequest.description = 'updated creator';
                                                    axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                        .then(function(resp) {
                                                            console.log("update metalog", resp);
                                                        })
                                                        .catch(function(resp) {
                                                            console.log('error', resp);
                                                            alert('Error Occurred while meta logging.')
                                                        });
                                                }
                                            } else {
                                                if (app.cloneFlag == true) {
                                                    axios.get('/mr/individual/public/api/v1/character/' + app.item)
                                                        .then(function(resp) {
                                                            var jsonRequest = {

                                                            };
                                                            jsonRequest.character_id = app.character.id;
                                                            jsonRequest.username = '';
                                                            jsonRequest.description = 'cloned by ' + resp.data.username;
                                                            axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                .then(function(resp) {
                                                                    console.log("create metalog", resp);
                                                                })
                                                                .catch(function(resp) {
                                                                    console.log('error', resp);
                                                                    alert('Error Occurred while meta logging.')
                                                                });
                                                        })
                                                        .catch(function(resp) {
                                                            console.log('getCharacter error', resp);
                                                        });
                                                }  else {
                                                    var jsonRequest = {

                                                    };
                                                    jsonRequest.character_id = app.character.id;
                                                    jsonRequest.username = app.user.name;
                                                    jsonRequest.description = 'created';
                                                    axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                        .then(function(resp) {
                                                            console.log("create metalog", resp);
                                                        })
                                                        .catch(function(resp) {
                                                            console.log('error', resp);
                                                            alert('Error Occurred while meta logging.')
                                                        });
                                                }

                                            }
                                        })
                                        .catch(function (resp) {
                                            console.log(resp);
                                            alert("Error Occured !");
                                        });
                                } else {
                                    if (app.term == null || app.term == '') {
                                        if (app.cloneFlag && !!app.item) {
                                            app.character.clone_id = app.item;
                                        }
                                        axios.post('/mr/individual/public/api/v1/character/create', app.character)
                                            .then(function (resp) {
                                                console.log("resp", resp);
                                                app.characters = resp.data.characters;
                                                app.character = resp.data.character;
                                                app.arrayCharacters = resp.data.arrayCharacters;
                                                app.updateArraySearch();

                                                if (app.editFlag) {
                                                    if (app.methodUpdateFlag) {
                                                        var jsonRequest = {};
                                                        jsonRequest.character_id = app.character.id;
                                                        jsonRequest.username = app.user.name;
                                                        jsonRequest.description = 'updated method';
                                                        axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                            .then(function(resp) {
                                                                console.log("update metalog", resp);
                                                                if (app.unitUpdateFlag) {
                                                                    var jsonRequest = {

                                                                    };
                                                                    jsonRequest.character_id = app.character.id;
                                                                    jsonRequest.username = app.user.name;
                                                                    jsonRequest.description = 'updated unit';
                                                                    axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
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
                                                    } else if (app.unitUpdateFlag) {
                                                        var jsonRequest = {

                                                        };
                                                        jsonRequest.character_id = app.character.id;
                                                        jsonRequest.username = app.user.name;
                                                        jsonRequest.description = 'updated unit';
                                                        axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                            .then(function(resp) {
                                                                console.log("update metalog", resp);
                                                            })
                                                            .catch(function(resp) {
                                                                console.log('error', resp);
                                                                alert('Error Occurred while meta logging.')
                                                            });
                                                    }
                                                } else {
                                                    if (app.cloneFlag == true) {
                                                        axios.get('/mr/individual/public/api/v1/character/' + app.item)
                                                            .then(function(resp) {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = '';
                                                                jsonRequest.description = 'cloned by ' + resp.data.username;
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("create metalog", resp);
                                                                    })
                                                                    .catch(function(resp) {
                                                                        console.log('error', resp);
                                                                        alert('Error Occurred while meta logging.')
                                                                    });
                                                            })
                                                            .catch(function(resp) {
                                                                console.log('getCharacter error', resp);
                                                            });
                                                    }  else {
                                                        var jsonRequest = {};
                                                        jsonRequest.character_id = app.character.id;
                                                        jsonRequest.username = app.user.name;
                                                        jsonRequest.description = 'created';
                                                        axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                            .then(function(resp) {
                                                                console.log("create metalog", resp);
                                                            })
                                                            .catch(function(resp) {
                                                                console.log('error', resp);
                                                                alert('Error Occurred while meta logging.')
                                                            });
                                                    }
                                                }
                                            })
                                            .catch(function (resp) {
                                                console.log(resp);
                                                alert("Error Occured !");
                                            });
                                            
                                    } else {
                                        var jsonEsynonym = {
                                            user: app.user.name,
                                            ontology: 'exp',
                                            term: app.term,
                                            classIRI: app.termValue
                                        };
                                        axios.post('http://shark.sbs.arizona.edu:8080/esynonym', jsonEsynonym)
                                            .then(function(resp) {
                                                console.log('esynonym resp', resp);
                                                axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": app.user.name, "ontology": 'exp'})
                                                    .then(function(resp) {
                                                        console.log('save resp', resp);
                                                    })
                                                    .catch(function(resp) {

                                                    });
                                                axios.post('/mr/individual/public/api/v1/character/create', app.character)
                                                    .then(function (resp) {
                                                        console.log("resp", resp);
                                                        app.characters = resp.data.characters;
                                                        app.character = resp.data.character;
                                                        app.arrayCharacters = resp.data.arrayCharacters;
                                                        app.updateArraySearch();

                                                        if (app.editFlag) {
                                                            if (app.methodUpdateFlag) {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = app.user.name;
                                                                jsonRequest.description = 'updated method';
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("update metalog", resp);
                                                                        if (app.unitUpdateFlag) {
                                                                            var jsonRequest = {

                                                                            };
                                                                            jsonRequest.character_id = app.character.id;
                                                                            jsonRequest.username = app.user.name;
                                                                            jsonRequest.description = 'updated unit';
                                                                            axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
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
                                                            } else if (app.unitUpdateFlag) {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = app.user.name;
                                                                jsonRequest.description = 'updated unit';
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("update metalog", resp);
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
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("update metalog", resp);
                                                                    })
                                                                    .catch(function(resp) {
                                                                        console.log('error', resp);
                                                                        alert('Error Occurred while meta logging.')
                                                                    });
                                                            }
                                                        } else {
                                                            if (app.cloneFlag == true) {
                                                                axios.get('/mr/individual/public/api/v1/character/' + app.item)
                                                                    .then(function(resp) {
                                                                        var jsonRequest = {

                                                                        };
                                                                        jsonRequest.character_id = app.character.id;
                                                                        jsonRequest.username = '';
                                                                        jsonRequest.description = 'cloned by ' + resp.data.username;
                                                                        axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                            .then(function(resp) {
                                                                                console.log("create metalog", resp);
                                                                            })
                                                                            .catch(function(resp) {
                                                                                console.log('error', resp);
                                                                                alert('Error Occurred while meta logging.')
                                                                            });
                                                                    })
                                                                    .catch(function(resp) {
                                                                        console.log('getCharacter error', resp);
                                                                    });
                                                            }  else {
                                                                var jsonRequest = {

                                                                };
                                                                jsonRequest.character_id = app.character.id;
                                                                jsonRequest.username = app.user.name;
                                                                jsonRequest.description = 'created';
                                                                axios.post('/mr/individual/public/api/v1/meta-log', jsonRequest)
                                                                    .then(function(resp) {
                                                                        console.log("create metalog", resp);
                                                                    })
                                                                    .catch(function(resp) {
                                                                        console.log('error', resp);
                                                                        alert('Error Occurred while meta logging.')
                                                                    });
                                                            }

                                                        }
                                                    })
                                                    .catch(function (resp) {
                                                        console.log(resp);
                                                        alert("Error Occured !");
                                                    });
                                            })
                                            .catch(function(resp) {
                                                console.log('esysnoym error resp', resp);
                                            });
                                    }

                                }

                                app.log('/mr/individual/public/api/v1/user-log', {
                                    'user_id': app.user.id,
                                    'action': 'clicked on Save for "' + app.character.name + '"',
                                    'action_detail': app.character.name,
                                    'type': 'Measurement Recorder'
                                });
                            } else {
                                alert("You can not create new character with the same name of existing character !");
                            }
                        })
                        .catch(function (resp) {
                            console.log(resp);
                        });
                } else {
                    app.log('/mr/individual/public/api/v1/user-log', {
                        'user_id': app.user.id,
                        'action': 'Failed to save character',
                        'action_detail': '',
                        'abnormal_system_response': 'need to fill Method and Unit sections',
                        'type': 'Measurement Recorder',
                    });
                    alert("You need to fill Method and Unit sections!");
                }
            },
            cancelCharacter () {
                var app = this;
                this.detailsFlag = false;
                this.updatedFlag = false;
                this.parentData = null;
                this.viewFlag = false;
                this.cloneFlag = false;
                sessionStorage.setItem('viewFlag', false);
                var jsonRequest = {
                    'user_id': app.user.id,
                    'action': 'clicked on Cancel for "' + app.character.name + '"',
                    'type': 'Measurement Recorder'
                };
                axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                    .then(function(resp) {
                        console.log('userLog resp', resp);
                    })
                    .catch(function(resp) {
                        console.log('userLog error', resp);
                    });
                this.character.name = '';
            },
            addHeader: function() {
                /* $('.measure-table > thead > tr > th:last-child').before('<th></th>') */
                $('th.actions.display-none').removeClass('display-none').addClass('display-block');
                $('th.actions > .btn-add.display-block').removeClass('display-block').addClass('display-none');
                $('#new-header').focus();
                var app = this;
                app.newHeader.header = 'measurements';
            },
            saveHeader: function() {
                var app = this;
                axios.get('/mr/individual/public/api/v1/character/all')
                    .then(function(resp) {
                        var headerData = resp.data.headers;
                        var tpFlag = true;
                        for (var i = 0; i < headerData.length; i ++) {
                            if (app.newHeader.header == headerData[i].header) {
                                tpFlag = false;
                            }
                        }

                        if (tpFlag) {
                            axios.post('/mr/individual/public/api/v1/character/add-header', app.newHeader)
                                .then(function (resp) {
                                    console.log("createHeader resp", resp);
                                    /* $('.measure-table thead tr th:last-child').before("<th><input class='th-input' value='" + resp.data.header + "' /></th>"); */
                                    app.headers = resp.data.headers;
                                    app.characters = resp.data.characters;
                                    app.arrayCharacters = resp.data.arrayCharacters;
                                    app.updateArraySearch();

                                    $('th.actions.display-block').removeClass('display-block').addClass('display-none');
                                    $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
                                    app.actionLog.action_type = "create_header";
                                    app.actionLog.model_id = resp.data.characters[0][resp.data.characters[0].length - 1].header_id;
                                    app.actionLog.model_name = "header";
                                    axios.post('/mr/individual/public/api/v1/log', app.actionLog)
                                        .then(function (resp) {
                                            console.log("successful log character !!!");
                                        })
                                        .catch(function (resp) {
                                            console.log(resp);
                                            alert("Error Occured !");
                                        });
                                    var jsonRequest = {
                                        'user_id': app.user.id,
                                        'action': 'added a new column',
                                        'action_detail': app.newHeader.header,
                                        'type': 'Measurement Recorder'
                                    };
                                    axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                                        .then(function (resp) {
                                            console.log('userLog resp', resp);
                                        })
                                        .catch(function(resp) {
                                            console.log('userLog error', resp);
                                        });
                                    app.newHeader.header = '';
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
            deleteHeader: function(headerId, headerName) {
                var app = this;
                console.log("headerId", headerId);
                axios.post('/mr/individual/public/api/v1/character/delete-header/' + headerId)
                    .then(function(resp) {
                        console.log('deleteHeader resp', resp);
                        app.headers = resp.data.headers;
                        app.characters = resp.data.characters;
                        app.arrayCharacters = resp.data.arrayCharacters;
                        app.updateArraySearch();

                        app.actionLog.action_type = "delete_header";
                        app.actionLog.model_id = resp.data.characters[0][resp.data.characters[0].length - 1].header_id;
                        app.actionLog.model_name = "header";
                        axios.post('/mr/individual/public/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log header !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Error Occured !");
                            });
                        var jsonRequest = {
                            'user_id': app.user.id,
                            'action': 'removed a column "' + headerName + '"',
                            'type': 'Measurement Recorder'
                        };
                        axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                            .then(function(resp) {
                                console.log('userLog resp', resp);
                            })
                            .catch(function(resp) {
                                console.log('userLog error', resp);
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
                        }
                    })
                    .catch(function(resp) {
                        console.log('error deleteHeader', resp);
                    });
            },
            cancelHeader: function() {
                $('th.actions.display-block').removeClass('display-block').addClass('display-none');
                $('th.actions > .btn-add.display-none').removeClass('display-none').addClass('display-block');
            },
            swapComponent: function(component) {
                this.currentComponent = component;
            },
            storeCharacter() {
                var app = this;
                if (!this.character.name) return;
                var tpArray = this.character.name.split(' ');
                this.character.method_from = null;
                this.character.method_to = null;
                this.character.method_include = null;
                this.character.method_exclude = null;
                this.character.method_at = null;
                this.character.method_as = null;
                this.character.unit = null;
                this.character.measure_semantic = null;
                this.character.entity_semantic = null;
                this.character.creator = this.user.name + ' via MR';
                this.character.usage = [];
                this.character.history = [];

                this.methodUpdateFlag = false;
                this.unitUpdateFlag = false;
                this.semanticsUpdateFlag = false;
                this.creatorUpdateFlag = false;
                this.newCharacterFlag = false;

                var tpFlag = false;
                for (var i = 0; i < tpArray.length; i++) {
                    if (tpArray[i] == 'of' || tpArray[i] == 'between') {
                        tpFlag = true;
                    }
                }
                if (tpFlag) {
                    var jsonRequest = {
                        'user_id': app.user.id,
                        'action': 'new character',
                        'action_detail': app.character.name,
                        'type': 'Measurement Recorder'
                    };
                    axios.post('/mr/individual/public/api/v1/user-log', jsonRequest)
                        .then(function(resp) {
                            console.log('userLog resp', resp);
                        })
                        .catch(function (resp) {
                            console.log("userLog error", resp);
                        });
                    sessionStorage.setItem("characterName", this.character.name);
                    console.log("detailsFlag", this.detailsFlag);
                    app.parentData = [];
                    app.parentData.push(app.character.method_as);
                    app.parentData[3] = app.user;
                    app.parentData[4] = app.character.method_from;
                    app.parentData[5] = app.character.method_to;
                    app.parentData[6] = app.character.method_include;
                    app.parentData[7] = app.character.method_exclude;
                    app.parentData[8] = app.character.method_at;
                    app.currentMetadata = method;
                    app.detailsFlag = true;
                    localStorage.clear();
                    app.editFlag = false;
                    app.metadataFlag = "method";
                } else {
                    alert("The header name should contain 'of' or 'between' word.");
                    app.log('/mr/individual/public/api/v1/user-log', {
                        'user_id': app.user.id,
                        'action': 'new character',
                        'type': 'Measurement Recorder',
                        'abnormal_system_response': 'error: character name must contain "of"'
                    });
                }

            },
            detailComponent: function(componentId) {
                console.log("componentId", componentId);
            },
            saveItem: function(event, item) {
                console.log('updated item', item);
                var app = this;
                if (item.value < 0) {
                    alert("Value should be only positive value.");
                    item.value = '';
                } else {
                    axios.post('/mr/individual/public/api/v1/character/update', item)
                        .then(function (resp) {
                            if (resp.data.error_input == 1) {
                                event.target.style.color = 'red';
                            } else {
                                event.target.style.color = 'black';
                                app.characters = resp.data.characters;
                                app.arrayCharacters = resp.data.arrayCharacters;
                                app.updateArraySearch();
                                var updatedCharacter = resp.data.updatedCharacter;

                                for (var i = 0; i < app.characters.length; i++) {
                                    if (app.characters[i][app.characters[i].length - 1].character_id == updatedCharacter.character_id) {
                                        for (var j = 0; j < app.headers.length; j++) {
                                            if (app.headers[j].id == updatedCharacter.header_id) {
                                                if (!!item.value) {
                                                    app.log('/mr/individual/public/api/v1/user-log', {
                                                        'user_id': app.user.id,
                                                        'action': 'added "' + updatedCharacter.value + '" value for "' + app.headers[j].header + '"',
                                                        'action_detail': 'value=' + updatedCharacter.value,
                                                        'type': 'Measurement Recorder'
                                                    });
                                                } else {
                                                    app.log('/mr/individual/public/api/v1/user-log', {
                                                        'user_id': app.user.id,
                                                        'action': 'removed value for "' + app.headers[j].header + '"',
                                                        'action_detail': 'value=' + updatedCharacter.value,
                                                        'type': 'Measurement Recorder'
                                                    });
                                                }
                                            }
                                        }
                                    }
                                }
                                app.actionLog.action_type = "update";
                                app.actionLog.model_id = updatedCharacter.character_id;
                                app.actionLog.model_name = "value";
                                app.log('/mr/individual/public/api/v1/log', app.actionLog);
                            }
                        })
                        .catch(function (resp) {
                            console.log(resp);
                            alert("Error Occured in update character!");
                        });
                }
            },
            deleteCharacter: function(character_id) {
                var app = this;
                console.log('character id to be deleted --->', character_id);
                var tpData = {
                    character_id: character_id
                };
                axios.post('/mr/individual/public/api/v1/character/delete', tpData)
                    .then(function (resp) {
                        console.log("resp", resp);
                        console.log("app.characters", app.characters);
                        app.arrayCharacters = resp.data.arrayCharacters;
                        app.characters = resp.data.characters;
                        app.updateArraySearch();

                        const deleted_character = app.arrayCharacters.find(c => {
                            return c.id == character_id
                        });
                        app.log('/mr/individual/public/api/v1/user-log', {
                                'user_id': app.user.id,
                                'action': 'Trashed "' + deleted_character.name + '"',
                                'action_detail': deleted_character.name, 
                                'type': 'Measurement Recorder'
                            });

                        app.actionLog.action_type = "delete";
                        app.actionLog.model_id = tpData.character_id;
                        app.actionLog.model_name = "character";
                        axios.post('/mr/individual/public/api/v1/log', app.actionLog)
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
            },
            selectItem(val) {
                console.log("selectedItem", val);
            },
            onSelect (item) {
                console.log('Selected Dropdown Item:: ', item);
                if (!item) {
                    sessionStorage.setItem('edit_created_other', false);
                }
                var app = this;
                var tempFlag = false;
                app.item = item;
                for (var i = 0; i < app.characters.length; i++) {
                    for (var j = 0; j < app.characters[i].length; j++) {
                        if (app.characters[i][j].header_id == 1 && app.characters[i][j].character_id == item) {
                            app.viewFlag = true;
                            sessionStorage.setItem('viewFlag', true);
                            console.log('onSelect', app.characters[i][j]);
                            tempFlag = true;
                            app.editCharacter(app.characters[i][j]);
                        }
                    }
                }

                if (tempFlag == false) {
                    app.character.name = item;
                    app.newCharacterFlag= true;
                }
                if (item === null) app.item = '';
            },
            updateArraySearch() {
                var app = this;
                app.arraySearch = [];
                //app.addLastItemToDropdown();
                for (var i = 0; i < app.arrayCharacters.length; i++) {
                    var temp = {};
                    temp.text = app.arrayCharacters[i].name + ' by ' + app.arrayCharacters[i].username + ' (' + app.arrayCharacters[i].usage_count + ')';
                    temp.value = app.arrayCharacters[i].id;
                    app.arraySearch.push(temp);
                }
            },
            /* addLastItemToDropdown() {
                var app = this;
                if (app.arraySearch.length == 0) {
                    app.arraySearch.push({
                        value: null,
                        text: 'Click here to create a new character'
                    });
                }
            }, */
            log(url, logdata) {
                axios.post(url, logdata)
                    .then(function (resp) {
                        console.log("successful log character !!!");
                    })
                    .catch(function (resp) {
                        console.log('Logging Error'+url+': ', resp);
                    });
            },
            printSearchText (searchText) {
                this.searchText = searchText
            },
            cancelNewCharacter() {
                this.newCharacterFlag = false;
            },
            beforeunloadHandler() {
                this.log('/mr/individual/public/api/v1/user-log', {
                        'user_id': app.user.id,
                        'action': 'close the page',
                        'action_detail': '', 
                        'type': 'Measurement Recorder'
                    });
            },
            setSaveDisabled() {
                if (((this.character.method_from != null && this.character.method_from != '') ||
                    (this.character.method_to != null && this.character.method_to != '') ||
                    (this.character.method_as != null && this.character.method_as != '') ||
                    (this.character.method_include != null && this.character.method_include != '') ||
                    (this.character.method_exclude != null && this.character.method_exclude != '') ||
                    (this.character.method_at != null && this.character.method_at != '')) &&
                    (this.character.unit != null && this.character.unit != '')) {
                    this.saveDisabled = false;
                    console.log("enabled");
                } else {
                    this.saveDisabled = true;
                    console.log("disabled");
                }
            }
        },
        created() {
            var app = this;

            var jsonPost = {
                user: '',
                ontologies: 'exp'
            };

            axios.get('/mr/individual/public/api/v1/character/all')
                .then(function (resp) {
                    console.log('all resp', resp);
                    app.headers = resp.data.headers;
                    app.characters = resp.data.characters;
                    app.arrayCharacters = resp.data.arrayCharacters;

                    app.arraySearch = [];
                    //app.addLastItemToDropdown();
                    for (var i = 0; i < resp.data.arrayCharacters.length; i++) {
                        var temp = {};
                        temp.text = resp.data.arrayCharacters[i].name + ' by ' + resp.data.arrayCharacters[i].username + ' (' + resp.data.arrayCharacters[i].usage_count + ')';
                        temp.value = resp.data.arrayCharacters[i].id;
                        app.arraySearch.push(temp);
                    }
                })
                .catch(function (resp) {
                    console.log(resp);
                });

            document.addEventListener('beforeunload', this.beforeunloadHandler);
        },
        mounted() {
        },
        components: {
            ListSelect,
            ModelSelect
        }
    }

</script>