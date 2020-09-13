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
                        {{chapterHeadings}}
                        <v-data-table
                            :headers="headers"
                            :items="chapterHeadings"
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
                                    <td align="center">{{ indexGenerate(row.title) }}</td>
                                    <td align="center">{{row.item.title}}</td>
                                    <td
                                        align="center"
                                    >{{ dateFormat(row.item.code_category.heading) }}</td>
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
                                v-model="chapterHeading.title"
                                :counter="20"
                                :rules="nameRules"
                                label="Title"
                                required
                            ></v-text-field>

                            <!-- <v-autocomplete
                                v-model="model"
                                :hint="!isEditing ? 'Click the icon to edit' : 'Click the icon to save'"
                                :items="states"
                                :readonly="!isEditing"
                                :label="`State — ${isEditing ? 'Editable' : 'Readonly'}`"
                                persistent-hint
                                prepend-icon="mdi-city"
                            ></v-autocomplete>-->

                            <v-select
                                :items="codeCategories"
                                label="Select Code Category"
                                item-value="id"
                                item-text="heading"
                                v-model="chapterHeading.code_category_id"
                                :rules="[v => !!v || 'Code Category is required']"
                            ></v-select>

                            <v-image-input
                                v-model="chapterHeading.image"
                                :image-quality="0.85"
                                clearable
                                image-format="jpeg"
                            />
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

        <v-dialog
            v-model="dialogDetails"
            hide-overlay
            transition="dialog-bottom-transition"
            scrollable
            fullscreen
        >
            <v-card tile>
                <v-toolbar flat dark color="primary">
                    <v-btn icon dark @click="dialogDetails = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Settings</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark text @click="dialogDetails = false">Save</v-btn>
                    </v-toolbar-items>
                    <v-menu bottom right offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn dark icon v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item v-for="(item, i) in items" :key="i" @click="() => {}">
                                <v-list-item-title>{{ item.title }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-toolbar>
                <v-card-text>
                    <v-spacer></v-spacer>
                    <v-col cols="12">
                        <base-material-card
                            class="v-card-profile"
                            avatar="https://demos.creative-tim.com/vue-material-dashboard/img/marc.aba54d65.jpg"
                        >
                            <v-card-text class="text-center">
                                <h6 class="display-1 mb-1 grey--text">CEO / CO-FOUNDER</h6>

                                <h4
                                    class="display-2 font-weight-light mb-3 black--text"
                                >Alec Thompson</h4>

                                <p
                                    class="font-weight-light grey--text"
                                >Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...</p>
                            </v-card-text>
                        </base-material-card>
                    </v-col>
                    <v-spacer></v-spacer>
                    <v-col cols="12">
                        <v-select
                            :items="countries"
                            label="Select Country"
                            item-value="id"
                            item-text="name"
                            v-model="selectedCountryId"
                            :rules="[v => !!v || 'Code Category is required']"
                            solo
                            v-on:change="fetchExportsByCountry"
                        ></v-select>

                        <base-material-chart-card
                            :data="dailySalesChart.data"
                            :options="dailySalesChart.options"
                            color="black"
                            hover-reveal
                            type="Line"
                        >
                            <template v-slot:reveal-actions>
                                <v-tooltip bottom>
                                    <template v-slot:activator="{ attrs, on }">
                                        <v-btn v-bind="attrs" color="info" icon v-on="on">
                                            <v-icon color="info">mdi-refresh</v-icon>
                                        </v-btn>
                                    </template>

                                    <span>Refresh</span>
                                </v-tooltip>

                                <v-tooltip bottom>
                                    <template v-slot:activator="{ attrs, on }">
                                        <v-btn v-bind="attrs" light icon v-on="on">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                    </template>

                                    <span>Change Date</span>
                                </v-tooltip>
                            </template>

                            <h4 class="card-title font-weight-light mt-2 ml-2">Export History</h4>

                            <p class="d-inline-flex font-weight-light ml-2 mt-1">
                                <v-icon color="green" small>mdi-arrow-up</v-icon>
                                <span class="green--text">55%</span>&nbsp;
                                increase in today's sales
                            </p>

                            <template v-slot:actions>
                                <v-icon class="mr-1" small>mdi-clock-outline</v-icon>
                                <span
                                    class="caption grey--text font-weight-light"
                                >updated 4 minutes ago</span>
                            </template>
                        </base-material-chart-card>
                    </v-col>
                    <v-spacer></v-spacer>
                    <v-col cols="12">
                        <v-sheet class="mx-auto" elevation="8">
                            <v-slide-group v-model="sheet" class="pa-4" center-active show-arrows>
                                <v-slide-item
                                    v-for="n in suppliers.length"
                                    :key="n"
                                    v-slot:default="{ active, toggle }"
                                >
                                    <v-card
                                        :color="active ? 'primary' : 'grey lighten-1'"
                                        class="ma-4"
                                        height="380"
                                        width="230"
                                        @click="toggle"
                                    >
                                        <base-material-card
                                            class="v-card-profile"
                                            avatar="https://demos.creative-tim.com/vue-material-dashboard/img/marc.aba54d65.jpg"
                                        >
                                            <v-card-text class="text-center">
                                                <h5
                                                    class="display-2 font-weight-light mb-3 black--text"
                                                >{{suppliers[n].company_name}}</h5>
                                                <p
                                                    class="font-weight-light grey--text"
                                                >{{suppliers[n].company_email}}</p>

                                                <p
                                                    class="font-weight-light grey--text"
                                                >Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye...</p>
                                            </v-card-text>
                                        </base-material-card>
                                    </v-card>
                                </v-slide-item>
                            </v-slide-group>
                        </v-sheet>
                    </v-col>
                </v-card-text>

                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>

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
import VImageInput from 'vuetify-image-input'

export default {
    name: 'Role',
    components: {
        Snackbar, DialogDelete, [VImageInput.name]: VImageInput,
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
                    text: 'Heading',
                    sortable: true,
                    value: 'heading'
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
            validRolePermission: false,
            codeCategories: [],
            chapterHeading: {},
            chapterHeadings: [],
            dialogDetails: false,
            dailySalesChart: {
                data: {
                    labels: ["M", "T", "W", "T", "F", "S", "S"],
                    series: [[12, 17, 57, 17, 23, 18, 50, 100]]
                },
                options: {
                    lineSmooth: this.$chartist.Interpolation.cardinal({
                        tension: 0
                    }),
                    low: 0,
                    high: 150, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                    chartPadding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    }
                }
            },
            sheet: null,
            countries: [],
            suppliers: [],
            selectedCountryId: null,
            graphData: []
        }
    },

    computed: {
        formTitle() {
            return this.editMode ? 'Edit Item' : 'New Item'
        }
    },

    mounted() {
        this.fetchCodeCategories()
        this.fetchChapterHeadings()
        //this.fetchSuppliers()
        this.fetchCounties()
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
            this.dialogDetails = true
            this.editMode = false


            this.fetchExports()
        },
        validate() {
            this.$refs.form.validate()
            this.$nextTick(() => {
                if (this.valid) {
                    if (this.editMode) {
                        this.update()
                        return
                    }
                    this.saveChapterHeading()
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
            this.fetchChapterHeadings()
        },
        resetItem() {
            this.codeCategory = {
                title: '',
                code_category_id: 0
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
        },
        fetchChapterHeadings() {
            this.loading = true
            const baseURI = `/api/v1/chapter-headings`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.chapterHeadings = result.data.data
                    this.totalItems = resutl.data.meta.total
                    console.log(this.chapterHeadings)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchCodeCategories() {
            this.loading = true
            const baseURI = `/api/v1/code-categories`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.codeCategories = result.data.data
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchCounties() {
            this.loading = true
            const baseURI = `/api/v1/countries`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.countries = result.data.data
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchSuppliers() {
            this.loading = true
            const baseURI = `/api/v1/suppliers`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.suppliers = result.data.data
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchExports() {
            this.loading = true
            const baseURI = `/api/v1/exports/${3002}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.countries = result.data.data.countries
                    this.dailySalesChart.data.labels = result.data.data.fiscal_years
                    this.dailySalesChart.data.series = result.data.data.usd
                    this.dailySalesChart.options.high = Math.max(...result.data.data.usd[0]) + 5
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchExportsByCountry() {
            this.loading = true
            const baseURI = `/api/v1/exports-by-country/${3002}/${this.selectedCountryId}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.dailySalesChart.data.labels = result.data.data.fiscal_years
                    this.dailySalesChart.data.series = result.data.data.usd
                    this.dailySalesChart.options.high = Math.max(...result.data.data.usd[0]) + 5
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        saveChapterHeading() {
            const baseURI = '/api/v1/chapter-heading'
            this.$http
                .post(baseURI, this.chapterHeading)
                .then(result => {
                    this.dialogRolePermission = false
                    this.fetchItems()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.showSnackbar(error.response, 'error')
                })
        },
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