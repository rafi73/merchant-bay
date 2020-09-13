<template>
    <v-app-bar id="app-bar" absolute app color="transparent" flat height="75">
        <v-btn class="mr-3" elevation="1" fab small @click="setDrawer(!drawer)">
            <v-icon v-if="value">mdi-view-quilt</v-icon>

            <v-icon v-else>mdi-dots-vertical</v-icon>
        </v-btn>

        <v-icon>{{message.icon}}</v-icon>
        <v-toolbar-title
            class="hidden-sm-and-down font-weight-light"
            v-text="`${message.greeting}!`"
        />

        <v-spacer />

        <v-text-field
            :label="$t('search')"
            color="secondary"
            hide-details
            style="max-width: 165px;"
        >
            <template v-if="$vuetify.breakpoint.mdAndUp" v-slot:append-outer>
                <v-btn class="mt-n2" elevation="1" fab small>
                    <v-icon>mdi-magnify</v-icon>
                </v-btn>
            </template>
        </v-text-field>

        <div class="mx-3" />

        <v-btn class="ml-2" min-width="0" text to="/">
            <v-icon>mdi-view-dashboard</v-icon>
        </v-btn>

        <v-menu bottom left offset-y origin="top right" transition="scale-transition">
            <template v-slot:activator="{ attrs, on }">
                <v-btn class="ml-2" min-width="0" text v-bind="attrs" v-on="on">
                    <v-badge color="red" overlap bordered>
                        <template v-slot:badge>
                            <span>5</span>
                        </template>

                        <v-icon>mdi-bell</v-icon>
                    </v-badge>
                </v-btn>
            </template>

            <v-list :tile="false" nav>
                <div>
                    <app-bar-item v-for="(n, i) in notifications" :key="`item-${i}`">
                        <v-list-item-title v-text="n" />
                    </app-bar-item>
                </div>
            </v-list>
        </v-menu>

        <v-btn class="ml-2" min-width="0" text to="/pages/user">
            <v-icon>mdi-account</v-icon>
        </v-btn>

        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-btn class="ml-2" min-width="0" text v-on="on" @click="confirmDialog = true">
                    <v-icon>mdi-arrow-right-bold</v-icon>
                </v-btn>
            </template>
            <span>{{$t('logout')}}</span>
        </v-tooltip>

        <v-dialog v-model="confirmDialog" max-width="500">
            <v-card>
                <v-card-title class="headline">{{ $t('confirm_logout') }}</v-card-title>
                <v-card-text>{{ $t('logout_confirm_text') }}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <!-- <v-btn color="primary" class="mr-1" @click="confirmDialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>

                    <v-btn color="red" class="mr-1" @click="logout()">
                        <v-icon>mdi-arrow-right-bold</v-icon>
                    </v-btn>-->

                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                small
                                color="primary"
                                v-on="on"
                                @click="confirmDialog = false"
                            >
                                <v-icon dark>mdi-close</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('edit')}}</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn class="mx-2" fab dark color="red" v-on="on" @click="logout()">
                                <v-icon dark>mdi-arrow-right-bold</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('delete')}}</span>
                    </v-tooltip>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app-bar>
</template>

<script>
// Components
import { VHover, VListItem } from 'vuetify/lib'
import Cookies from 'js-cookie'
// Utilities
import { mapState, mapMutations } from 'vuex'

export default {
    name: 'DashboardCoreAppBar',

    components: {
        AppBarItem: {
            render(h) {
                return h(VHover, {
                    scopedSlots: {
                        default: ({ hover }) => {
                            return h(VListItem, {
                                attrs: this.$attrs,
                                class: {
                                    'black--text': !hover,
                                    'white--text secondary elevation-12': hover,
                                },
                                props: {
                                    activeClass: '',
                                    dark: hover,
                                    link: true,
                                    ...this.$attrs,
                                },
                            }, this.$slots.default)
                        },
                    },
                })
            },
        },
    },

    props: {
        value: {
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        notifications: [
            'Mike John Responded to your email',
            'You have 5 new tasks',
            'You\'re now friends with Andrew',
            'Another Notification',
            'Another one',
        ],
        confirmDialog: false,
        message: {
            greeting: '',
            icon: ''
        },
        username: ''
    }),

    computed: {
        ...mapState(['drawer']),
    },
    mounted() {
        this.getWelcomeMessage()
        // if (this.$store.state.admin.data) {
        //     this.username = this.$store.state.admin.data.username
        // }
    },
    methods: {
        ...mapMutations({
            setDrawer: 'SET_DRAWER',
        }),
        logout() {
            Cookies.remove('token')
            sessionStorage.clear()
            localStorage.clear()
            this.$store.commit('SET_ADMIN', null)
            this.$store.commit('SET_CRETAE', false)
            this.$store.commit('SET_READ', false)
            this.$store.commit('SET_UPDATE', false)
            this.$store.commit('SET_DELETE', false)
            this.$router.push('/login')
        },
        getWelcomeMessage() {
            let today = new Date()
            let hourNow = today.getHours()

            if (hourNow < 12) {
                this.message.greeting = "Good Morning"
                this.message.icon = "mdi-coffee-outline"
            }
            else if (hourNow < 20) {
                this.message.greeting = 'Good afternoon!'
                this.message.icon = "mdi-weather-sunny"
            }
            else if (hourNow < 24) {
                this.message.greeting = "Good evening"
                this.message.icon = "mdi-weather-night"
            }
            else {
                this.message.greeting = "Welcome"
            }
        }
    },
}
</script>
