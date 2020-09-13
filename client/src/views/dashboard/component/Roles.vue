<template>
    <!-- Container Start-->
    <v-container id="role" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    dark
                    color="primary"
                    icon="mdi-atom"
                    :title="$t('roles')"
                    class="px-5 py-3"
                >
                    <v-card-text>
                        <v-tooltip bottom>
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
                        </v-tooltip>
                        <v-data-table
                            :headers="headers"
                            :items="roles"
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
                                    <td align="center">{{row.item.title}}</td>
                                    <td align="center">
                                        <v-simple-checkbox v-model="row.item.is_active" disabled></v-simple-checkbox>
                                    </td>
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
                                                    @click="assignPermission(row.item)"
                                                >
                                                    <v-icon dark>mdi-security</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>{{$t('assign_permission')}}</span>
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
                                v-model="role.title"
                                :counter="20"
                                :rules="nameRules"
                                label="Title"
                                required
                            ></v-text-field>

                            <v-checkbox v-model="role.is_active" label="Is Active?"></v-checkbox>
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

        <!-- Add/Update permission dialog Start-->
        <v-dialog v-model="dialogRolePermission" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ $t('assign_permission') }}</span>
                </v-card-title>
                <template>
                    <v-form
                        ref="formRolePermission"
                        v-model="validRolePermission"
                        lazy-validation
                        @keyup.enter.native="validateRolePermission()"
                        @keyup.esc.native="dialog = false"
                    >
                        <v-card-text>
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field v-model="role.title" disabled label="Solo" solo></v-text-field>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        :items="menus"
                                        item-value="id"
                                        item-text="title"
                                        label="Select Menu"
                                        v-model="rolePermission.menu_id"
                                        solo
                                        :rules="[v => !!v || 'Menu is required']"
                                        v-on:change="changeMenu"
                                        required
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <template>
                                        <v-data-table
                                            v-model="selectedPermissions"
                                            :headers="permissionHeaders"
                                            :items="permissions"
                                            item-key="title"
                                            show-select
                                            class="elevation-1"
                                            :hide-default-footer="true"
                                        ></v-data-table>
                                    </template>
                                </v-col>
                            </v-row>
                            <v-spacer></v-spacer>
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
                                        @click="dialogRolePermission = false"
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
                                        @click="validateRolePermission()"
                                    >
                                        <v-icon dark>mdi-content-save</v-icon>
                                    </v-btn>
                                </template>
                                <span>{{$t('save')}}</span>
                            </v-tooltip>
                        </v-card-actions>
                    </v-form>
                </template>
            </v-card>
        </v-dialog>
        <!-- Add/Update permission dialog End-->

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
    name: 'Role',
    components: {
        Snackbar, DialogDelete
    },
    data() {
        return {
            role: {
                title: '',
                is_active: true
            },
            editMode: false,
            dialog: false,
            headers: [
                {
                    align: 'center',
                    text: 'SL #',
                    sortable: true,
                    value: 'index'
                },
                {
                    align: 'center',
                    text: 'Title',
                    sortable: true,
                    value: 'title'
                },
                {
                    align: 'center',
                    text: 'Status',
                    sortable: true,
                    value: 'is_active'
                },
                {
                    align: 'center',
                    text: 'Created On',
                    sortable: true,
                    value: 'created_at'
                },
                {
                    align: 'center',
                    text: 'Actions',
                    value: 'actions',
                    sortable: false
                }
            ],
            roles: [],
            loading: true,
            valid: false,
            nameRules: [
                v => !!v || "Title is required",
                v =>
                    (v && v.length <= 30) ||
                    "Title must be less than 30 characters"
            ],
            validationRules: {
                menu: [
                    v => !!v || "Menu is required",
                    v =>
                        (v && v > 0) ||
                        "Menu must be less than 20 characters"
                ]
            },
            dialogConfirmDelete: false,
            snackbar: {
                visible: false,
                content: '',
                color: ''
            },
            pagination: {},
            sort: '',
            totalItems: 0,
            lastPage: 0,
            dialogRolePermission: false,
            permissions: [],
            selectedPermissions: [],
            permissionHeaders: [
                {
                    text: 'Select Permissions',
                    sortable: true,
                    value: 'title'
                }
            ],
            menuHeaders: [
                {
                    text: 'Select Menus',
                    sortable: true,
                    value: 'title'
                }
            ],
            rolePermission: {},
            roleMenu: {},
            menus: [],
            validRolePermission: false
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? 'Edit Item' : 'New Item'
        }
    },

    mounted() {
        this.fetchItems()
    },

    methods: {
        fetchItems() {
            this.loading = true
            const baseURI = `/admin/v1/roles?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.roles = result.data.data.rows
                    this.setupPagination(result.data.data)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        editItem(item) {
            this.editMode = true
            this.role = Object.assign({}, item)
            this.dialog = true
        },
        deleteItem(item) {
            this.dialogConfirmDelete = true
            this.role = Object.assign({}, item)
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
        validateRolePermission() {
            this.$refs.formRolePermission.validate()
            this.$nextTick(() => {
                if (this.validRolePermission) {
                    this.savePermission()
                }
            })
        },
        create() {
            const baseURI = '/admin/v1/roles'
            this.$http
                .post(baseURI, this.role)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.showSnackbar(error.response, 'error')
                })
        },
        update() {
            const baseURI = `/admin/v1/roles/${this.role.id}`
            this.$http
                .put(baseURI, this.role)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.showSnackbar(error.response, 'error')
                })
        },
        erase() {
            this.dialogConfirmDelete = false
            this.loading = true
            const baseURI = `/admin/v1/roles/${this.role.id}`
            this.$http
                .delete(baseURI)
                .then(result => {
                    this.loading = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.loading = false
                    this.showSnackbar(error.response, 'success')
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
            this.role = {
                title: '',
                is_active: true
            }
        },
        fetchPermission(roleID, menuID) {
            this.loading = true
            const baseURI = `/admin/v1/assign-permissions/${roleID}/${menuID}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.permissions = result.data.data
                    this.permissions.forEach(permission => {
                        if (permission.role_permissions.length > 0) {
                            this.selectedPermissions.push(permission)
                        }
                    })
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchMenu(roleID, menuID) {
            this.loading = true
            const baseURI = '/admin/v1/menus'
            this.$http
                .get(baseURI)
                .then(result => {
                    this.menus = result.data.data
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        assignPermission(item) {
            this.dialogRolePermission = true
            this.role = item
            this.rolePermission.role_id = item.id
            this.rolePermission.menu_id = null
            this.fetchMenu(item.id)
            this.selectedPermissions = []
            this.permissions = []
        },
        savePermission() {
            this.rolePermission.permission_ids = this.selectedPermissions.map(item => item['id'])
            const baseURI = '/admin/v1/role-permissions'
            this.$http
                .post(baseURI, this.rolePermission)
                .then(result => {
                    this.dialogRolePermission = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.showSnackbar(error.response, 'error')
                })
        },
        indexGenerate(item) {
            return (this.pagination.page - 1) * this.pagination.itemsPerPage + (this.roles.indexOf(item) + 1)
        },
        dateFormat(date) {
            return this.moment(date).format('YYYY-MM-DD')
        },
        showSnackbar(message, color) {
            this.snackbar.text = message
            this.snackbar.color = color
            this.snackbar.visible = true;
        },
        closeSnackbar() {
            this.snackbar.visible = false;
        },
        changeMenu(menuID) {
            this.selectedPermissions = []
            this.fetchPermission(this.role.id, menuID)
        }
    },
    watch: {
        dialog() {
            if (this.$refs.form) {
                this.$refs.form.resetValidation()
            }
        },
        dialogRolePermission() {
            if (this.$refs.formRolePermission) {
                this.$refs.formRolePermission.resetValidation()
            }

        }
    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>