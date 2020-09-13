<template>
    <v-app>
        <v-container dark fluid tag="section" align="center">
            <v-row dark justify="center" class="pa-md-12">
                <v-col cols="12" md="4">
                    <base-material-card
                        class="v-card-profile"
                        avatar="data:image/png;base64,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"
                    >
                        <v-card-text class="text-center">
                            <h6 class="display-1 mb-1 grey--text">{{$t('welcome_tspay_admin')}}</h6>
                            <h4
                                class="display-2 font-weight-light mb-3 black--text"
                            >{{$t('reset_password')}}</h4>
                            <v-form
                                ref="form"
                                lazy-validation
                                v-model="valid"
                                @keyup.enter.native="validate()"
                            >
                                <v-container class="py-0">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Email"
                                                v-model="admin.email"
                                                class="purple-input"
                                                :rules="rules.email"
                                                required
                                            />
                                        </v-col>

                                        <v-col cols="12">
                                            <v-text-field
                                                label="Old Password"
                                                v-model="admin.oldPassword"
                                                class="purple-input"
                                                type="password"
                                                :rules="[v => !!v || 'Old Password is required']"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Password"
                                                v-model="admin.password"
                                                class="purple-input"
                                                type="password"
                                                :rules="rules.password"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Confirm Password"
                                                v-model="admin.confirmPassword"
                                                class="purple-input"
                                                type="password"
                                                :rules="[v => !!v || 'Confirm Password is required', (admin.password === admin.confirmPassword) || 'Password must match']"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-form>
                            <v-btn color="primary" rounded class="mr-0" @click="validate()">Reset</v-btn>
                        </v-card-text>
                    </base-material-card>
                </v-col>
            </v-row>
        </v-container>
        <!-- Snackbar Start-->
        <Snackbar
            :visible="snackbar.visible"
            :text="snackbar.text"
            :color="snackbar.color"
            v-on:requestClose="closeSnackbar()"
        ></Snackbar>
        <!-- Snackbar End-->
    </v-app>
</template>

<script>
import Snackbar from './components/core/Snackbar'
export default {
    name: 'Login',
    components: {
        Snackbar
    },
    data: () => ({
        admin: {
            email: null,
            password: null,
            confirmPassword: null
        },
        rules: {
            email: [
                v => !!v || "Email is required",
                v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
            ],
            password: [
                v => !!v || "Password is required",
                v =>
                    (v && v.length <= 10) ||
                    "Name must be less than 10 characters"
            ],
        },
        valid: false,
        snackbar: {
            visible: false,
            content: '',
            color: ''
        }
    }),

    methods: {
        validate() {
            this.$refs.form.validate()
            this.$nextTick(() => {
                if (this.valid) {
                    this.login()
                }
            })
        },
        login() {
            const baseURI = `/admin/v1/reset-password`
            this.$http
                .post(baseURI, this.admin)
                .then(result => {
                    this.showSnackbar(result.data.message, `success`)
                    this.$router.push('/login')
                })
                .catch(error => {
                    console.log(error)
                    if (!error.response) {
                        this.showSnackbar(`Network Error`, `error`)
                        return
                    }
                    this.showSnackbar(error.response.data.message, `error`)
                })
        },
        showSnackbar(message, color) {
            this.snackbar.text = message
            this.snackbar.color = color
            this.snackbar.visible = true;
        },
        closeSnackbar() {
            this.snackbar.visible = false;
        }
    },
    created() {
        this.$vuetify.theme.themes[this.isDark ? 'dark' : 'light'].primary = '#225CEC'
    }
}
</script>

