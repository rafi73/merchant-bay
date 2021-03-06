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
                    title="Chapter Heading"
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
                            :items="chapterHeadings"
                            :items-per-page="10"
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
                                    <td align="center">
                                        <v-img
                                            max-height="40px"
                                            max-width="40px"
                                            aspect-ratio="1"
                                            :src="imagePath(row.item.image)"
                                        ></v-img>
                                    </td>
                                    <td align="left">{{row.item.title.slice(0, 50)}}</td>
                                    <td
                                        align="center"
                                    >{{row.item.code_category.heading.slice(0, 50)}}</td>
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
                                                    color="deep-purple"
                                                    v-on="on"
                                                    @click="showDetails(row.item)"
                                                >
                                                    <v-icon dark>mdi-eye</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>Show Details</span>
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
        {{chapterHeading}}
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

                            <v-select
                                :items="codeCategories"
                                label="Select Code Category"
                                item-value="id"
                                item-text="heading"
                                v-model="chapterHeading.code_category_id"
                                :rules="[v => !!v || 'Code Category is required']"
                            ></v-select>

                            <div v-if="!chapterHeading.image">
                                <h2>Select an image</h2>
                                <input type="file" @change="onFileChange" />
                            </div>
                            <div v-else>
                                <v-img
                                    aspect-ratio="1"
                                    class="grey lighten-2"
                                    max-height="300"
                                />

                                <button @click="removeImage">Remove image</button>
                            </div>
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

        <v-dialog
            v-model="dialogDetails"
            hide-overlay
            transition="dialog-bottom-transition"
            scrollable
            max-width="1200px"
        >
            <v-card tile>
                <v-toolbar flat dark color="primary">
                    <v-btn icon dark @click="dialogDetails = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Detail statistics</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-menu bottom right offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn dark icon v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                    </v-menu>
                </v-toolbar>
                <v-card-text>
                    <!-- Information -->
                    <v-spacer></v-spacer>
                    <v-col cols="12">
                        <v-card class="mx-auto">
                            <v-card-text>
                                <v-row align="center">
                                    <v-row>
                                        <v-card max-width="375" class="mx-auto">
                                            <v-img
                                                src="https://cdn.vuetifyjs.com/images/lists/ali.png"
                                                height="300px"
                                                dark
                                            ></v-img>
                                        </v-card>
                                    </v-row>
                                    <v-row>
                                        <v-form ref="form">
                                            <h4
                                                class="display-2 font-weight-light mb-3 black--text"
                                            >Heading ID : {{chapterHeading.id}}</h4>
                                            <p
                                                class="font-weight-light black--text"
                                            >Heading Title : {{chapterHeading.title.slice(0, 50)}}</p>
                                            <p
                                                class="font-weight-light black--text"
                                            >Heading Chapter : {{chapterHeading.code_category.chapter.slice(0, 50)}}</p>
                                            <p
                                                class="font-weight-light black--text"
                                            >Heading Section : {{chapterHeading.code_category.section.slice(0, 50)}}</p>
                                        </v-form>
                                    </v-row>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- Chart -->
                    <v-spacer></v-spacer>
                    <v-col cols="12">
                        <v-select
                            :items="countries"
                            label="Select Country"
                            item-value="id"
                            item-text="name"
                            v-model="selectedCountryId"
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

                            <h4
                                class="card-title font-weight-light mt-2 ml-2"
                            >Export scenerio within {{selectedCountryId}} country</h4>

                            <p class="d-inline-flex font-weight-light ml-2 mt-1">
                                This Product is exported to
                                <v-icon color="green" small>mdi-home-export-outline</v-icon>
                                <span class="green--text">{{ (countries.length) -1 }}&nbsp;</span>&nbsp;
                                countries from Bangladeshi Manufacturers
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

                    <!-- Slider -->
                    <v-col cols="12">
                        <base-material-card
                            color="primary"
                            title="You can source this product from these factories"
                        >
                            <v-sheet class="mx-auto" elevation="8">
                                <v-slide-group
                                    v-model="sheet"
                                    class="pa-4"
                                    center-active
                                    show-arrows
                                >
                                    <v-slide-item
                                        v-for="n in suppliers.length"
                                        :key="n"
                                        v-slot:default="{ active, toggle }"
                                    >
                                        <v-card
                                            :color="active ? 'primary' : 'grey lighten-1'"
                                            class="ma-4"
                                            height="400"
                                            width="344"
                                            @click="toggle"
                                        >
                                            <v-card max-width="344" class="mx-auto">
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title
                                                            class="headline"
                                                        >{{suppliers[n].company_name}}</v-list-item-title>
                                                        <v-list-item-subtitle>{{suppliers[n].company_email}}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-img
                                                    :src="images[Math.floor(Math.random()*images.length)]"
                                                    height="194"
                                                ></v-img>

                                                <v-card-text>Visit ten places on our planet that are undergoing the biggest changes today.</v-card-text>

                                                <v-card-actions>
                                                    <v-btn
                                                        text
                                                        color="deep-purple accent-4"
                                                    >Factory visit</v-btn>
                                                    <v-btn
                                                        text
                                                        color="deep-purple accent-4"
                                                        :href="`https://www.merchantbay.com/#rfqFormModal/${suppliers[n].id}`"
                                                    >Send and RFW</v-btn>
                                                    <v-spacer></v-spacer>
                                                </v-card-actions>
                                            </v-card>
                                        </v-card>
                                    </v-slide-item>
                                </v-slide-group>
                            </v-sheet>
                        </base-material-card>
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
import VImageInput from 'vuetify-image-input/a-la-carte'

export default {
    name: 'chapter-heading',
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
                    text: 'Image',
                    sortable: true,
                    value: 'index'
                },
                {
                    align: 'Left',
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
            chapterHeading: {
                title: '',
                image: '',
                code_category: {
                    chapter: '',
                    section: ''
                }
            },
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
                    high: 1500000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                    chartPadding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                    tooltips: {
                        enabled: true
                    }
                },
            },
            sheet: null,
            countries: [{ 'id': null, 'name': 'All' }],
            suppliers: [],
            selectedCountryId: null,
            graphData: [],
            images: [
                'https://cdn.pixabay.com/photo/2018/07/26/07/45/valais-3562988__340.jpg',
                'https://cdn.pixabay.com/photo/2020/06/26/17/16/daisies-5343423__340.jpg',
                'https://cdn.pixabay.com/photo/2020/06/11/20/06/dog-5288071__340.jpg',
                'https://cdn.pixabay.com/photo/2020/06/15/02/03/leaves-5300030__340.jpg',
                'https://cdn.pixabay.com/photo/2020/04/20/09/42/seagulls-5067489__340.jpg',
                'https://cdn.pixabay.com/photo/2020/06/26/00/25/summer-5341326__340.jpg',
                'https://cdn.pixabay.com/photo/2020/08/25/00/48/mountains-5515324__340.jpg',
                'https://cdn.pixabay.com/photo/2017/08/27/00/44/cocktail-2684866__340.jpg',
                'https://cdn.pixabay.com/photo/2020/08/26/16/15/taj-mahal-5519945__340.jpg',
                'https://cdn.pixabay.com/photo/2017/12/08/12/25/berlin-3005717__340.jpg',
                'https://cdn.pixabay.com/photo/2015/05/14/16/02/sandcastle-766949__340.jpg',
                'https://cdn.pixabay.com/photo/2020/08/22/21/58/boat-5509457__340.jpg'

            ],
            selectedImage: null,
            imageData: null
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
        this.fetchSuppliers()
        // this.fetchCounties()
    },

    methods: {
        editItem(item) {
            this.editMode = true
            this.chapterHeading = item
            this.chapterHeading.image = this.imagePath(item.image)
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
            this.chapterHeading = {
                title: '',
                image: '',
                code_category: {
                    chapter: '',
                    section: ''
                }
            }
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
            this.snackbar.visible = true
        },
        closeSnackbar() {
            this.snackbar.visible = false
        },
        changeMenu(menuID) {
            this.selectedPermissions = []
            this.fetchPermission(this.role.id, menuID)
        },
        fetchChapterHeadings() {
            this.loading = true
            const baseURI = `/api/v1/chapter-headings?page=${this.pagination.page}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.chapterHeadings = result.data.data
                    this.totalItems = result.data.meta.total
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
            const baseURI = `/api/v1/exports/${this.chapterHeading.id}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.countries = this.countries.concat(result.data.data.countries)
                    this.dailySalesChart.data.labels = result.data.data.fiscal_years
                    this.dailySalesChart.data.series = result.data.data.usd
                    this.dailySalesChart.options.high = Math.max(...result.data.data.usd[0])
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        fetchExportsByCountry() {
            if (!this.selectedCountryId) {
                this.fetchExports()
                return
            }

            this.loading = true
            const baseURI = `/api/v1/exports-by-country/${this.chapterHeading.id}/${this.selectedCountryId}`
            this.$http
                .get(baseURI)
                .then(result => {
                    this.dailySalesChart.data.labels = result.data.data.fiscal_years
                    this.dailySalesChart.data.series = result.data.data.usd
                    this.dailySalesChart.options.high = Math.max(...result.data.data.usd[0])
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        saveChapterHeading() {
            let formData = new FormData()
            formData.append('image', this.imageData)
            formData.append('title', this.chapterHeading.title)
            formData.append('code_category_id', this.chapterHeading.code_category_id)

            let header = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Access-Control-Allow-Origin': '*'
                }
            }

            const baseURI = '/api/v1/chapter-headings'
            this.$http
                .post(baseURI, formData, header)
                .then(result => {
                    this.dialog = false
                    this.fetchChapterHeadings()
                    this.showSnackbar(result.data.message, 'success')
                })
                .catch(error => {
                    this.showSnackbar(error.response, 'error')
                })
        },
        showDetails(item) {
            this.chapterHeading = item
            this.dialogDetails = true
            this.selectedCountryId = null
            this.countries = [{ 'id': null, 'name': 'All' }]
            this.fetchExports()
        },
        getBase64Image(img) {
            var canvas = document.createElement("canvas")
            canvas.width = img.width
            canvas.height = img.height
            var ctx = canvas.getContext("2d")
            ctx.drawImage(img, 0, 0)
            var dataURL = canvas.toDataURL("image/png")
            return dataURL.replace(/^data:image\/(png|jpg);base64,/, "")
        },
        onFileChange(event) {
            var files = event.target.files || event.dataTransfer.files
            if (!files.length)
                return

            this.imageData = files[0]
            this.chapterHeading.image = URL.createObjectURL(files[0])
        },
        createImage(file) {
            var image = new Image()
            var reader = new FileReader()
            var vm = this

            reader.onload = (e) => {
                vm.image = e.target.result
            }
            reader.readAsDataURL(file)
        },
        removeImage: function (e) {
            this.image = ''
        },
        imagePath(imageName) {
            console.log(require(`@/../../storage/uploads/${imageName}`))
            return require(`@/../../storage/uploads/${imageName}`)
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