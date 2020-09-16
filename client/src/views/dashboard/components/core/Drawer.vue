<template>
    <v-navigation-drawer
        id="core-navigation-drawer"
        v-model="drawer"
        :dark="barColor !== 'rgba(228, 226, 226, 1), rgba(255, 255, 255, 0.7)'"
        :expand-on-hover="expandOnHover"
        :right="$vuetify.rtl"
        :src="barImage"
        mobile-break-point="960"
        app
        width="260"
        v-bind="$attrs"
    >
        <template v-slot:img="props">
            <v-img :gradient="`to bottom, ${barColor}`" v-bind="props" />
        </template>
        <v-divider class="mb-1" />

        <v-list dense nav>
            <v-list-item>
                <v-list-item-avatar class="align-self-center" color="white" contain>
                    <v-img
                        src="data:image/png;base64,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"
                        max-height="30"
                    />
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title class="display-1" v-text="profile.title" />
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <v-divider class="mb-2" />

        <v-list expand nav>
            <!-- Style cascading bug  -->
            <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
            <div />

            <template v-for="(item, i) in computedItems">
                <base-item-group v-if="item.children" :key="`group-${i}`" :item="item">
                    <!--  -->
                </base-item-group>

                <base-item v-else :key="`item-${i}`" :item="item" />
            </template>

            <!-- Style cascading bug  -->
            <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
            <div />
        </v-list>
        <template v-slot:append>
            <base-item
                :item="{
                    title: $t('made_in_Tokyo_by_Omise'),
                    icon: 'mdi-heart',
                    to: '',
                }"
            />
        </template>
    </v-navigation-drawer>
</template>

<script>
// Utilities
import {
    mapState,
} from 'vuex'

export default {
    name: 'DashboardCoreDrawer',

    props: {
        expandOnHover: {
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        items: [
            // {
            //     icon: 'mdi-view-dashboard',
            //     title: 'dashboard',
            //     to: '/',
            // },
            // {
            //     title: 'roles',
            //     icon: 'mdi-atom',
            //     to: '/roles',
            // },
            // {
            //     title: 'admins',
            //     icon: 'mdi-flash',
            //     to: '/admins',
            // },
            // {
            //     title: 'merchants',
            //     icon: 'mdi-store-outline',
            //     to: '/merchants',
            // },
            // {
            //     title: 'users',
            //     icon: 'mdi-account-multiple',
            //     to: '/users',
            // },
            {
                title: 'Chapter Heading',
                icon: 'mdi-atom',
                to: '/admin/chapter-headings',
            },
        ],
    }),

    computed: {
        ...mapState(['barColor', 'barImage']),
        drawer: {
            get() {
                return this.$store.state.drawer
            },
            set(val) {
                this.$store.commit('SET_DRAWER', val)
            },
        },
        computedItems() {
            return this.items.map(this.mapItem)
        },
        profile() {
            return {
                avatar: true,
                title: this.$t('avatar'),
            }
        },
    },

    methods: {
        mapItem(item) {
            return {
                ...item,
                children: item.children ? item.children.map(this.mapItem) : undefined,
                title: this.$t(item.title),
            }
        },
    },

    mounted() {
        //console.log(this.$store.state.admin.data)
        // if (this.$store.state.admin.data.menu != null) {
        //     this.items = this.$store.state.admin.data.menu
        // }
    }
}
</script>

<style lang="sass">
@import '~vuetify/src/styles/tools/_rtl.sass'

#core-navigation-drawer
    .v-list-group__header.v-list-item--active:before
        opacity: .24

    .v-list-item
    &__icon--text,
    &__icon:first-child
        justify-content: center
        text-align: center
        width: 20px

        +ltr()
        margin-right: 24px
        margin-left: 12px !important

        +rtl()
        margin-left: 24px
        margin-right: 12px !important

    .v-list--dense
    .v-list-item
        &__icon--text,
        &__icon:first-child
            margin-top: 10px

    .v-list-group--sub-group
    .v-list-item
        +ltr()
        padding-left: 8px

        +rtl()
        padding-right: 8px

    .v-list-group__header
        +ltr()
        padding-right: 0

        +rtl()
        padding-right: 0

        .v-list-item__icon--text
            margin-top: 19px
            order: 0

        .v-list-group__header__prepend-icon
            order: 2

            +ltr()
            margin-right: 8px

            +rtl()
            margin-left: 8px
</style>

