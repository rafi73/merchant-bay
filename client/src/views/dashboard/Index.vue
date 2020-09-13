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
        // if (this.$store.state.admin == null) {
        //     this.$router.push('/login')
        // } else {
        //     this.$http.defaults.headers.common.Authorization = Cookies.get('token')
        // }
    },
    // watch: {
    //     '$route': function (to, from) {
    //         if (this.$store.state.admin.data.menu == null) {
    //             return
    //         }

    //         if (this.$store.state.admin.data.is_superuser) {
    //             return
    //         }

    //         let result = this.$store.state.admin.data.menu.find(menu => menu.to === to.fullPath)
    //         if (result != null) {
    //             result.permissions.forEach(permission => {
    //                 switch (permission) {
    //                     case "Create":
    //                         this.$store.commit('SET_CREATE', true)
    //                         break
    //                     case "Read":
    //                         this.$store.commit('SET_READ', true)
    //                         break
    //                     case "Update":
    //                         this.$store.commit('SET_UPDATE', true)
    //                         break
    //                     case "Delete":
    //                         this.$store.commit('SET_DELETE', true)
    //                         break
    //                 }
    //             })
    //         }
    //         else {
    //             this.$router.push('/')
    //         }
    //     }
    // }
}
</script>
