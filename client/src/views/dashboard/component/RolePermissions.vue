<template>
    <!-- Container Start-->
    <v-container id="rolePermission" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    color="primary"
                    dark
                    icon="mdi-security"
                    :title="$t('role_permissions')"
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
                            :items="rolePermissions"
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
                                    >{{ (pagination.page -1 )* pagination.itemsPerPage + (rolePermissions.indexOf(row.item) +1) }}</td>
                                    <td align="start">{{row.item.role.title}}</td>
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
                    <v-select
                        :items="roles"
                        label="Select Roles"
                        item-text="title"
                        item-value="id"
                        v-model="rolePermission.role_id"
                        v-on:change="changeRole"
                        :disabled="disableSelectRole"
                        solo
                    ></v-select>
                    <template>
                        <v-data-table
                            v-model="selectedPermissions"
                            :headers="permissionHeaders"
                            :items="permissions"
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
        <Snackbar :snackbar="snackbar" :text="text" :color="color" @clickedClose="snackbarClose()"></Snackbar>
        <!-- Snackbar End-->
    </v-container>
    <!-- Container End-->
</template>

<script>
import Snackbar from "./../components/core/Snackbar"
import DialogDelete from "./../components/core/DialogDelete"
export default {
    name: "rolePermission",
    components: {
        Snackbar,
        DialogDelete
    },
    data() {
        return {
            rolePermission: {},
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
                    text: "Title",
                    sortable: true,
                    value: "title"
                },
                {
                    align: "center",
                    text: "Actions",
                    value: "actions",
                    sortable: false
                }
            ],
            rolePermissions: [],
            loading: true,
            dialogConfirmDelete: false,
            color: "",
            mode: "",
            snackbar: false,
            text: "",
            pagination: {},
            totalItems: 0,
            lastPage: 0,
            sort: "",
            permissionHeaders: [
                {
                    text: "Select Permissions",
                    sortable: true,
                    value: "title"
                }
            ],
            roles: [],
            permissions: [],
            selectedPermissions: [],
            selectedRole: {}
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? "Edit Permissions" : "Add Permissions"
        },
        filerPermissionIds() {
            return this.selectedPermissions.map(item => item["id"])
        },
        disableSelectRole() {
            return this.editMode ? true : false
        },
    },

    mounted() {
        this.fetchItems()
        this.fetchRole()
    },

    methods: {
        fetchItems() {
            this.loading = true
            const baseURI = `/admin/v1/role-permissions?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.rolePermissions = result.data.data.rows
                    this.setupPagination(result.data.data)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        addItem() {
            this.resetItem()
            this.dialog = true
            this.editMode = false
            this.fetchRole()
        },
        editItem(item) {
            this.resetItem()
            this.editMode = true
            this.rolePermission.role_id = item.role.id
            this.dialog = true
            this.selectedRole = { title: item.title }
            this.fetchPermission(item.role.id)
        },
        validate() {
            if (this.editMode || this.selectedPermissions.length) {
                this.rolePermission.permission_ids = this.selectedPermissions.map(item => item["id"])
                this.save()
            }
        },
        save() {
            const baseURI = `/admin/v1/role-permissions`
            this.$http
                .post(baseURI, this.rolePermission)
                .then(result => {
                    this.dialog = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, `success`)
                })
                .catch(error => {
                    this.showSnackbar(error.response, `error`)
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
            if (pagination.sortBy.length && pagination.sortDesc.length) {
                this.sort = `${pagination.sortBy[0]} ${
                    pagination.sortDesc[0] == true ? "desc" : "asc"
                    }`
            }
            this.fetchItems()
        },
        snackbarClose() {
            this.snackbar = false
        },
        resetItem() {
            this.selectedPermissions = []
            this.rolePermission = {}
        },
        fetchRole() {
            this.loading = true
            const baseURI = `/admin/v1/roles?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.roles = result.data.data.rows
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchPermission(roleID) {
            this.loading = true
            const baseURI = `/admin/v1/assign-permissions/${roleID}`
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
        changeRole(roleID) {
            this.fetchPermission(roleID)
            this.selectedPermissions = []
            this.selectedRole = {}
        }
    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>