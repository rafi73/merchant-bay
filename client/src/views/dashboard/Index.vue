<template>
    <v-app>
        <dashboard-core-app-bar />

        <dashboard-core-drawer />

        <dashboard-core-view />

        <!-- <dashboard-core-settings /> -->
    </v-app>
</template>

<script>
import Cookies from 'js-cookie'
export default {
    name: 'DashboardIndex',

    components: {
        DashboardCoreAppBar: () => import('./components/core/AppBar'),
        DashboardCoreDrawer: () => import('./components/core/Drawer'),
        DashboardCoreSettings: () => import('./components/core/Settings'),
        DashboardCoreView: () => import('./components/core/View')
    },

    data: () => ({
        expandOnHover: false,
        authentication: false
    }),
    created() {
        this.$vuetify.theme.themes[this.isDark ? 'dark' : 'light'].primary = '#225CEC'
    },
}
</script>
