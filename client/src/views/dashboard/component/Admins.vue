<template>
    <!-- Container Start-->
    <v-container id="admin" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    dark
                    color="primary"
                    icon="mdi-atom"
                    :title="$t('admins')"
                    class="px-5 py-3"
                >
                    <v-card-text>
                        <!-- <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    absolute
                                    fab
                                    top
                                    right
                                    color="primary"
                                    v-on="on"
                                    @click="addItem()"
                                >
                                    <v-icon dark>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>{{$t('add_item')}}</span>
                        </v-tooltip>-->
                        <v-data-table
                            :headers="headers"
                            :items="admins"
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
                            loading-text
                        >
                            <template v-slot:item="row">
                                <tr>
                                    <td align="center">{{ indexGenerate(row.item) }}</td>
                                    <td align="center">{{row.item.username}}</td>
                                    <td align="center">{{row.item.email}}</td>
                                    <!-- <td align="center">
                                        <v-simple-checkbox v-model="row.item.is_active" disabled></v-simple-checkbox>
                                    </td>-->
                                    <td align="center">{{ dateFormat(row.item.created_at) }}</td>
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
                                                >
                                                    <v-icon dark>mdi-pencil</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>{{$t('edit')}}</span>
                                        </v-tooltip>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn
                                                    class="mx-2"
                                                    fab
                                                    dark
                                                    x-small
                                                    color="red"
                                                    v-on="on"
                                                    @click="deleteItem(row.item)"
                                                >
                                                    <v-icon dark>mdi-delete</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>{{$t('delete')}}</span>
                                        </v-tooltip>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on }">
                                                <v-btn
                                                    class="mx-2"
                                                    fab
                                                    dark
                                                    x-small
                                                    color="deep-purple"
                                                    v-on="on"
                                                    @click="assignRole(row.item)"
                                                >
                                                    <v-icon dark>mdi-security</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>{{$t('assign_role')}}</span>
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
                            @keyup.enter.native="validate()"
                            @keyup.esc.native="dialog = false"
                            lazy-validation
                        >
                            <v-text-field
                                v-model="admin.username"
                                :counter="20"
                                :rules="nameRules"
                                label="Username"
                                required
                            ></v-text-field>

                            <v-checkbox v-model="admin.is_active" label="Is Active?"></v-checkbox>
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

        <!-- Add/Update Role dialog Start-->
        <v-dialog v-model="dialogAdminRole" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ $t('assign_role') }}</span>
                </v-card-title>

                <v-card-text>
                    <v-text-field v-model="admin.username" disabled label="Solo" solo></v-text-field>
                    <template>
                        <v-data-table
                            v-model="selectedRoles"
                            :headers="roleHeaders"
                            :items="roles"
                            item-key="title"
                            show-select
                            class="elevation-1"
                        ></v-data-table>
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
                                @click="dialogAdminRole = false"
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
                                @click="saveRole()"
                            >
                                <v-icon dark>mdi-content-save</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('save')}}</span>
                    </v-tooltip>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Add/Update Role dialog End-->

        <!-- Delete confirmatioon dialog Start-->
        <DialogDelete
            :dialogConfirmDelete="dialogConfirmDelete"
            @onClickCancel="dialogConfirmDelete = !dialogConfirmDelete"
            @onClickDelete="erase()"
        ></DialogDelete>
        <!-- Delete confirmatioon dialog End-->

        <!-- Snackbar Start-->
        <Snackbar :snackbar="snackbar" :text="text" :color="color" @clickedClose="snackbarClose()"></Snackbar>
        <!-- Snackbar End-->
    </v-container>
    <!-- Container End-->
</template>

<script>
import Snackbar from './../components/core/Snackbar'
import DialogDelete from './../components/core/DialogDelete'
export default {
    name: `Admin`,
    components: {
        Snackbar, DialogDelete
    },
    data() {
        return {
            admin: {
                title: ``,
                is_active: true
            },
            editMode: false,
            dialog: false,
            headers: [
                {
                    align: `center`,
                    text: `SL #`,
                    sortable: true,
                    value: `index`
                },
                {
                    align: `center`,
                    text: `Title`,
                    sortable: true,
                    value: `title`
                },
                {
                    align: `center`,
                    text: `Status`,
                    sortable: true,
                    value: `is_active`
                },
                {
                    align: `center`,
                    text: `Created On`,
                    sortable: true,
                    value: `created_at`
                },
                {
                    align: `center`,
                    text: `Actions`,
                    value: `actions`,
                    sortable: false
                }
            ],
            admins: [],
            loading: true,
            valid: false,
            nameRules: [
                v => !!v || `Title is required`,
                v =>
                    (v && v.length <= 20) ||
                    `Title must be less than 10 characters`
            ],
            dialogConfirmDelete: false,
            color: '',
            mode: '',
            snackbar: false,
            text: '',
            sort: '',
            pagination: {},
            totalItems: 0,
            lastPage: 0,
            dialogAdminRole: false,
            roleHeaders: [
                {
                    text: "Select Roles",
                    sortable: true,
                    value: "title"
                }
            ],
            admins: [],
            roles: [],
            selectedRoles: [],
            selectedAdmin: {},
            adminRole: {}
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? `Edit Item` : `New Item`
        }
    },

    mounted() {
        this.fetchItems()
    },

    methods: {
        fetchItems() {
            this.loading = true
            const baseURI = `/admin/v1/admins?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.admins = result.data.data.rows
                    this.setupPagination(result.data.data)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        editItem(item) {
            this.editMode = true
            this.admin = Object.assign({}, item)
            this.dialog = true
        },
        deleteItem(item) {
            this.dialogConfirmDelete = true
            this.admin = Object.assign({}, item)
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
            const baseURI = `/admin/v1/admins`
            this.$http
                .post(baseURI, this.admin)
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
            const baseURI = `/admin/v1/admins/${this.admin.id}`
            this.$http
                .put(baseURI, this.admin)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.showSnackbar(error.response, `error`)
                })
        },
        erase() {
            this.dialogConfirmDelete = false
            this.loading = true
            const baseURI = `/admin/v1/admins/${this.admin.id}`
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
        showSnackbar(message, color) {
            this.snackbar = true
            this.text = message
            this.color = color
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
        snackbarClose() {
            this.snackbar = false
        },
        resetItem() {
            this.admin = {
                title: '',
                is_active: true
            }
        },
        fetchRole(roleID) {
            this.loading = true
            const baseURI = `/admin/v1/assign-roles/${roleID}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.roles = result.data.data
                    this.roles.forEach(role => {
                        if (role.admin_roles.length > 0) {
                            this.selectedRoles.push(role)
                        }
                    })
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        assignRole(item) {
            console.log(item)
            this.dialogAdminRole = true
            this.selectedRoles = []
            this.fetchRole(item.id)
            this.adminRole.admin_id = item.id
            this.admin = item
        },
        saveRole() {
            this.adminRole.Role_ids = this.selectedRoles.map(item => item[`id`])
            const baseURI = `/admin/v1/admin-roles`
            this.$http
                .post(baseURI, this.adminRole)
                .then(result => {
                    this.dialogAdminRole = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.showSnackbar(error.response, `error`)
                })
        },
        indexGenerate(item) {
            return (this.pagination.page - 1) * this.pagination.itemsPerPage + (this.admins.indexOf(item) + 1)
        },
        dateFormat(date) {
            return this.moment(date).format('YYYY-MM-DD')
        }
    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>