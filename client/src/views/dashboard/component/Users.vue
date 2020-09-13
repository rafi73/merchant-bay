<template>
    <!-- Container Start-->
    <v-container id="user" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    color="primary"
                    dark
                    icon="mdi-account-multiple"
                    :title="$t('users')"
                    class="px-5 py-3"
                >
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="users"
                            :options.sync="pagination"
                            :loading="loading"
                            :server-items-length="totalItems"
                            :footer-props="{
                                showFirstLastPage: true,
                                firstIcon: 'mdi-arrow-collapse-left',
                                lastIcon: 'mdi-arrow-collapse-right',
                                prevIcon: 'mdi-minus',
                                nextIcon: 'mdi-plus'
                            }"
                            @update:options="updatePagination"
                        >
                            <template v-slot:item="row">
                                <tr>
                                    <td
                                        align="center"
                                    >{{ (pagination.page -1 )* pagination.itemsPerPage + (users.indexOf(row.item) +1) }}</td>
                                    <td align="center">{{row.item.origami_id}}</td>
                                    <td align="center">
                                        <v-simple-checkbox v-model="row.item.is_active" disabled></v-simple-checkbox>
                                    </td>
                                    <td
                                        align="center"
                                    >{{moment(row.item.created_at).format('YYYY-MM-DD')}}</td>
                                    <td align="center">
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn
                                                    class="mx-2"
                                                    fab
                                                    dark
                                                    x-small
                                                    color="primary"
                                                    v-on="on"
                                                    @click="editItem(row.item)"
                                                    v-if="$store.state.edit"
                                                >
                                                    <v-icon dark>mdi-pencil</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>{{$t('edit')}}</span>
                                        </v-tooltip>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </base-material-card>
            </v-col>
        </v-row>
        <!-- Content End-->

        <!-- Add/Update dialog Start-->
        <v-dialog v-model="dialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                    <template>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                            @keyup.enter.native="validate()"
                            @keyup.esc.native="dialog = false"
                        >
                            <v-text-field
                                v-model="user.origami_id"
                                :counter="30"
                                :rules="nameRules"
                                label="Origami ID"
                                required
                                disabled
                            ></v-text-field>
                            <v-checkbox v-model="user.is_active" label="Is Active?"></v-checkbox>
                        </v-form>
                    </template>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                class="mx-2"
                                fab
                                small
                                dark
                                color="red"
                                v-on="on"
                                @click="dialog = false"
                            >
                                <v-icon dark>mdi-close</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('close')}}</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                color="primary"
                                v-on="on"
                                @click="validate()"
                            >
                                <v-icon dark>mdi-content-save</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('save')}}</span>
                    </v-tooltip>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Add/Update dialog End-->

        <!-- Delete confirmatioon dialog Start-->
        <DialogDelete
            :dialogConfirmDelete="dialogConfirmDelete"
            @onClickCancel="dialogConfirmDelete = !dialogConfirmDelete"
            @onClickDelete="erase()"
        ></DialogDelete>
        <!-- Delete confirmatioon dialog End-->

        <!-- Snackbar Start-->
        <Snackbar
            :visible="snackbar.visible"
            :text="snackbar.text"
            :color="snackbar.color"
            v-on:requestClose="closeSnackbar()"
        ></Snackbar>
        <!-- Snackbar End-->
    </v-container>
    <!-- Container End-->
</template>

<script>
import Snackbar from './../components/core/Snackbar'
import DialogDelete from './../components/core/DialogDelete'
export default {
    name: "User",
    components: {
        Snackbar, DialogDelete
    },
    data() {
        return {
            user: {
                title: "",
                is_active: true
            },
            editMode: false,
            dialog: false,
            headers: [
                {
                    align: "center",
                    text: "SL #",
                    sortable: true,
                    value: "id"
                },
                {
                    align: "center",
                    text: "Origami ID",
                    sortable: true,
                    value: "origami_id"
                },
                {
                    align: "center",
                    text: "Status",
                    sortable: true,
                    value: "is_active"
                },
                {
                    align: "center",
                    text: "Created On",
                    sortable: true,
                    value: "created_at"
                },
                {
                    align: "center",
                    text: "Actions",
                    value: "actions",
                    sortable: false
                }
            ],
            users: [],
            loading: true,
            valid: false,
            nameRules: [
                v => !!v || "Title is required",
                v =>
                    (v && v.length <= 30) ||
                    "Title must be less than 30 characters"
            ],
            dialogConfirmDelete: false,
            pagination: {},
            totalItems: 0,
            lastPage: 0,
            sort: "",
            snackbar: {
                visible: false,
                content: '',
                color: ''
            },
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? "Edit Item" : "New Item"
        }
    },

    mounted() {
        this.fetchItems()
    },

    methods: {
        fetchItems() {
            this.loading = true
            const baseURI = `/admin/v1/users?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.users = result.data.data.rows
                    this.setupPagination(result.data.data)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        editItem(item) {
            this.editMode = true
            this.user = Object.assign({}, item)
            this.dialog = true
        },
        deleteItem(item) {
            this.dialogConfirmDelete = true
            this.user = Object.assign({}, item)
        },
        addItem() {
            this.resetItem()
            this.dialog = true
            this.editMode = false
        },
        validate() {
            this.$refs.form.validate()
            this.$nextTick(() => {
                if (this.valid) {
                    if (this.editMode) {
                        this.update()
                        return
                    }
                    this.create()
                }
            })
        },
        create() {
            const baseURI = `/admin/v1/users`
            this.$http
                .post(baseURI, this.user)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.showSnackbar(error.response, `error`)
                })
        },
        update() {
            const baseURI = `/admin/v1/users/${this.user.id}`
            this.$http
                .put(baseURI, this.user)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.showSnackbar(error.response.data.message, `error`)
                })
        },
        erase() {
            this.dialogConfirmDelete = false
            this.loading = true
            const baseURI = `/admin/v1/users/${this.user.id}`
            this.$http
                .delete(baseURI)
                .then(result => {
                    this.loading = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.loading = false
                    this.showSnackbar(error.response, `success`)
                })
        },
        setupPagination(pagination) {
            this.totalItems = pagination.total_rows
        },
        updatePagination(pagination) {
            if (pagination.sortBy.length) {
                this.sort = `${pagination.sortBy[0]} ${pagination.sortDesc[0] == true ? 'desc' : 'asc'}`
            }
            this.fetchItems()
        },
        resetItem() {
            this.user = {
                title: '',
                is_active: true
            }
        },
        showSnackbar(message, color) {
            this.snackbar.text = message
            this.snackbar.color = color
            this.snackbar.visible = true;
        },
        closeSnackbar() {
            this.snackbar.visible = false;
        }
    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>