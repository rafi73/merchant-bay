<template>
    <!-- Container Start-->
    <v-container id="merchant" fluid tag="section">
        <!-- Content Start-->
        <v-row>
            <v-col cols="12" md="12">
                <base-material-card
                    color="primary"
                    dark
                    icon="mdi-asterisk"
                    :title="$t('merchants')"
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
                                    v-if="$store.state.create"
                                >
                                    <v-icon dark>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>{{$t('add_item')}}</span>
                        </v-tooltip>
                        <v-data-table
                            :headers="headers"
                            :items="merchants"
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
                                    >{{ (pagination.page -1 )* pagination.itemsPerPage + (merchants.indexOf(row.item) +1) }}</td>
                                    <td align="center">{{row.item.name}}</td>
                                    <td align="center">{{row.item.open_time}}</td>
                                    <td align="center">{{row.item.close_time}}</td>
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
                                                    color="deep-purple"
                                                    v-on="on"
                                                    @click="viewItem(row.item)"
                                                    v-if="$store.state.read"
                                                >
                                                    <v-icon dark>mdi-eye-outline</v-icon>
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
                                                    color="primary"
                                                    v-on="on"
                                                    @click="editItem(row.item)"
                                                    v-if="$store.state.update"
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
                                                    v-if="$store.state.delete"
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
        <v-dialog v-model="dialog" persistent max-width="800px">
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
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.name"
                                        :counter="30"
                                        :rules="validationRules.name"
                                        :label="$t('name')"
                                        required
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        v-model="merchant.description"
                                        :rules="validationRules.description"
                                        :label="$t('description')"
                                        required
                                        :counter="50"
                                        :disabled="viewMode"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.phone_number"
                                        :rules="validationRules.phone_number"
                                        :label="$t('phone_number')"
                                        required
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-dialog
                                        ref="dialogOpenTime"
                                        v-model="modalOpenTime"
                                        :return-value.sync="merchant.open_time"
                                        persistent
                                        width="290px"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="merchant.open_time"
                                                :label="$t('open_time')"
                                                prepend-icon="access_time"
                                                readonly
                                                v-on="on"
                                                :rules="validationRules.open_time"
                                                :disabled="viewMode"
                                            ></v-text-field>
                                        </template>
                                        <v-time-picker
                                            v-if="modalOpenTime"
                                            v-model="merchant.open_time"
                                            full-width
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="modalOpenTime = false"
                                            >{{ $t('cancel') }}</v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.dialogOpenTime.save(merchant.open_time)"
                                            >{{ $t('ok') }}</v-btn>
                                        </v-time-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-dialog
                                        ref="dialogCloseTime"
                                        v-model="modalCloseTime"
                                        :return-value.sync="merchant.close_time"
                                        persistent
                                        width="290px"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="merchant.close_time"
                                                :label="$t('close_time')"
                                                prepend-icon="access_time"
                                                readonly
                                                v-on="on"
                                                :rules="validationRules.close_time"
                                                :disabled="viewMode"
                                            ></v-text-field>
                                        </template>
                                        <v-time-picker
                                            v-if="modalCloseTime"
                                            v-model="merchant.close_time"
                                            full-width
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="modalCloseTime = false"
                                            >{{ $t('cancel') }}</v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.dialogCloseTime.save(merchant.close_time)"
                                            >{{ $t('ok') }}</v-btn>
                                        </v-time-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12" sm="4">
                                    <v-text-field
                                        v-model="merchant.zip_code"
                                        :rules="validationRules.zip_code"
                                        :label="$t('zip_code')"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="4">
                                    <v-text-field
                                        v-model="merchant.state"
                                        :rules="validationRules.state"
                                        :label="$t('state')"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="4">
                                    <v-text-field
                                        v-model="merchant.city"
                                        :rules="validationRules.city"
                                        :label="$t('city')"
                                        required
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.address1"
                                        :rules="validationRules.address1"
                                        :label="$t('address1')"
                                        :counter="50"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.address2"
                                        :rules="validationRules.address2"
                                        :label="$t('address2')"
                                        :counter="50"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="merchant.longitude"
                                        :rules="validationRules.longitude"
                                        :label="$t('longitude')"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="merchant.latitude"
                                        :rules="validationRules.latitude"
                                        :label="$t('latitude')"
                                        :disabled="viewMode"
                                    ></v-text-field>
                                </v-col>
                                <!-- <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.pg_shop_id"
                                        :rules="validationRules.pg_shop_id"
                                        :label="$t('pg_shop_id')"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="merchant.pg_shop_password"
                                        :rules="validationRules.pg_shop_password"
                                        :label="$t('pg_shop_password')"
                                        required
                                    ></v-text-field>
                                </v-col>-->
                                <v-col cols="12">
                                    <v-image-input
                                        v-model="imageData"
                                        :image-quality="0.85"
                                        clearable
                                        image-format="jpeg"
                                    />
                                </v-col>
                            </v-row>
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
                                :v-show="!viewMode"
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
    name: "Merchant",
    components: {
        Snackbar, DialogDelete
    },
    data() {
        return {
            merchant: {},
            editMode: false,
            viewMode: false,
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
                    text: "Name",
                    sortable: true,
                    value: "name"
                },
                {
                    align: "center",
                    text: "Open Time",
                    sortable: true,
                    value: "open_time"
                },
                {
                    align: "center",
                    text: "Close Time",
                    sortable: true,
                    value: "close_time"
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
            merchants: [],
            loading: true,
            valid: false,
            validationRules: {
                name: [
                    v => !!v || "Name is required",
                    v =>
                        (v && v.length <= 20) ||
                        "Name must be less than 20 characters"
                ],
                description: [
                    v => !!v || "Description is required",
                    v =>
                        (v && v.length <= 50) ||
                        "Description must be less than 50 characters"
                ],
                phone_number: [
                    v => !!v || "Phone number is required",
                    v => !v || /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im.test(v) || 'Phone number must be valid'
                ],
                open_time: [
                    v => !!v || "Open time is required",
                    v => !v || /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(v) || 'Open time must be valid'
                ],
                close_time: [
                    v => !!v || "Close time is required",
                    v => !v || /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(v) || 'Close time must be valid'
                ],
                zip_code: [
                    v => !!v || "Zip is required",
                    v => !v || /^\d{5}(-\d{4})?$/.test(v) || 'Zip code must be valid'
                ],
                state: [
                    v => !!v || "State is required",
                    v =>
                        (v && v.length <= 10) ||
                        "State must be less than 10 characters"
                ],
                city: [
                    v => !!v || "City is required",
                    v =>
                        (v && v.length <= 10) ||
                        "City must be less than 10 characters"
                ],
                address1: [
                    v => !!v || "Address1 is required",
                    v =>
                        (v && v.length <= 50) ||
                        "Address1 must be less than 10 characters"
                ],
                address2: [
                    v => !!v || "Address2 is required",
                    v =>
                        (v && v.length <= 50) ||
                        "Address2 must be less than 10 characters"
                ],
                longitude: [
                    v => !!v || "Longitude is required",
                    v => !v || /^[+-]?(([1-8]?[0-9])(\.[0-9]{1,6})?|90(\.0{1,6})?)$/.test(v) || 'Longitude must be valid'
                ],
                latitude: [
                    v => !!v || "Latitude is required",
                    v => !v || /^[+-]?(([1-8]?[0-9])(\.[0-9]{1,6})?|90(\.0{1,6})?)$/.test(v) || 'Latitude must be valid'
                ]
            },
            dialogConfirmDelete: false,
            color: '',
            mode: '',
            snackbar: false,
            text: '',
            pagination: {},
            totalItems: 0,
            lastPage: 0,
            sort: "",
            modalCloseTime: false,
            modalOpenTime: false,
            imageData: null,
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
            const baseURI = `/admin/v1/stores?limit=${this.pagination.itemsPerPage}&page=${this.pagination.page}&sort=${this.sort}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.merchants = result.data.data.rows
                    this.setupPagination(result.data.data)
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        editItem(item) {
            this.editMode = true
            this.merchant = Object.assign({}, item)
            this.dialog = true
        },
        deleteItem(item) {
            this.dialogConfirmDelete = true
            this.merchant = Object.assign({}, item)
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
            this.merchant.pg_shop_id = 1
            this.merchant.pg_shop_password = 111111
            const baseURI = `/admin/v1/stores`
            this.$http
                .post(baseURI, this.merchant)
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
            const baseURI = `/admin/v1/stores/${this.merchant.id}`
            this.$http
                .put(baseURI, this.merchant)
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
            const baseURI = `/admin/v1/stores/${this.merchant.id}`
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
        snackbarClose() {
            this.snackbar = false
        },
        resetItem() {
            this.merchant = {
                title: '',
                is_active: true
            }
        },
        viewItem(item) {
            this.viewMode = true
            this.merchant = Object.assign({}, item)
            this.dialog = true
        },
        showSnackbar(message, color) {
            this.snackbar.text = message
            this.snackbar.color = color
            this.snackbar.visible = true;
        },
        closeSnackbar() {
            this.snackbar.visible = false;
        },

    }
}
</script>

<style lang="css" scoped>
tr:nth-of-type(2n + 1) {
    background-color: rgba(255, 255, 255, 0.05);
}
</style>