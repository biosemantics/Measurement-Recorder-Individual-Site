<template>
    <layout>
        <div slot="section" class="">
            <div class="tab-pane" id="">
                <form class="row">
                    <div class="col-md-4">
                        Name
                    </div>
                    <div class="col-md-4">
                        <input style="width: 100%;" v-model="specimen.text" name="text"/>
                    </div>
                    <div class="col-md-4">
                        <a v-on:click="saveCharacter()" class="btn btn-primary" style="height: 28px; line-height: 28px; font-size: 50px; padding: 0 5px;">+</a>
                    </div>
                    <div class="col-md-4" style="margin-top: 10px;">
                        Character List
                    </div>
                    <div class="col-md-8" style="margin-top: 10px;">
                        <select style="width: 100%; height: 200px;" multiple>
                            <option v-for="specimen in specimenArray" v-bind:value="specimen">
                                {{ specimen.text }}
                            </option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </layout>
</template>
<script>
    export default {


        data: function () {
            return {
                specimen: {
                    text: ''
                },
                specimenArray: [

                ],
                actionLog: {
                    user_id: this.userId
                }
            }
        },
        props: {
            userId: Number
        },
        methods: {
            saveCharacter() {
                let tempSpecimen = {
                    text: this.specimen.text
                };
                this.specimenArray.push(tempSpecimen);

                var app = this;
                axios.post('/api/v1/specimen/create', this.specimen)
                    .then(function (resp) {
                        console.log("resp", resp);
                        app.actionLog.action_type = "create";
                        app.actionLog.model_id = resp.data.id;
                        axios.post('/api/v1/log', app.actionLog)
                            .then(function (resp) {
                                console.log("successful log character !!!");
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                alert("Could not create your company");
                            });
                        console.log("successful saving character !!!");
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Could not create your company");
                    });

            }
        },
        mounted() {
            var app = this;
            axios.get('/api/v1/specimen/all')
                .then(function (resp) {
                    console.log("successful loading character !!!", resp);
                    app.specimenArray = resp.data;
                })
                .catch(function (resp) {
                    console.log(resp);
                });
        }
    }
</script>