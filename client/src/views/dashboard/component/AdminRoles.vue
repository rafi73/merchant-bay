<template>
    <!-- Container Start-->
    <v-container id="adminRole" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    color="primary"
                    dark
                    icon="mdi-security"
                    :title="$t('admin_roles')"
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
                                    <td align="start">{{row.item.admin.username}}</td>
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
                        :items="admins"
                        label="Select Admins"
                        item-text="username"
                        item-value="id"
                        v-model="adminRole.admin_id"
                        v-on:change="changeRole"
                        :disabled="editMode"
                        solo
                    ></v-select>
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
    name: "adminRole",
    components: {
        Snackbar,
        DialogDelete
    },
    data() {
        return {
            adminRole: {},
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
            selectedAdmin: {}
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? "Edit Roles" : "Add Roles"
        },
    },

    mounted() {
        this.fetchItems()
        this.fetchAdmin()
    },

    methods: {
        fetchItems() {
            this.loading = true
            const baseURI = `/admin/v1/admin-roles?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
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
            this.fetchAdmin()
        },
        editItem(item) {
            this.resetItem()
            this.editMode = true
            this.adminRole.admin_id = item.admin.id
            this.dialog = true
            this.selectedAdmin = { title: item.username }
            this.fetchRole(item.admin.id)
        },
        validate() {
            if (this.editMode || this.selectedRoles.length) {
                this.adminRole.role_ids = this.selectedRoles.map(item => item["id"])
                this.save()
            }
        },
        save() {
            const baseURI = `/admin/v1/admin-roles`
            this.$http
                .post(baseURI, this.adminRole)
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
                this.sort = `${pagination.sortBy[0]} ${pagination.sortDesc[0] == true ? "desc" : "asc"}`
            }
            this.fetchItems()
        },
        snackbarClose() {
            this.snackbar = false
        },
        resetItem() {
            this.selectedRoles = []
            this.adminRole = {}
        },
        fetchAdmin() {
            this.loading = true
            const baseURI = `/admin/v1/admins?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.admins = result.data.data.rows
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
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
        changeRole(roleID) {
            this.fetchRole(roleID)
            this.selectedRoles = []
            this.selectedAdmin = {}
        },
        deleteItem(item) {
            this.dialogConfirmDelete = true
            this.adminRole.adminID = item.admin.id
            console.log(item)
        },
        erase() {
            this.dialogConfirmDelete = false
            this.loading = true
            const baseURI = `/admin/v1/admin-roles/${this.adminRole.adminID}`
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
    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>