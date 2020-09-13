import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
    plugins: [createPersistedState({
        storage: window.sessionStorage,
    })],
    state: {
        barColor: 'rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)',
        barImage: 'https://demos.creative-tim.com/material-dashboard/assets/img/sidebar-1.jpg',
        drawer: null,
        admin: null,
        create: false,
        read: false,
        update: false,
        delete: false
    },
    mutations: {
        SET_BAR_IMAGE(state, payload) {
            state.barImage = payload
        },
        SET_DRAWER(state, payload) {
            state.drawer = payload
        },
        SET_ADMIN(state, payload) {
            state.admin = payload
        },
        SET_CREATE(state, payload) {
            state.create = payload
        },
        SET_READ(state, payload) {
            state.read = payload
        },
        SET_UPDATE(state, payload) {
            state.update = payload
        },
        SET_DELETE(state, payload) {
            state.delete = payload
        },
    },
    actions: {

    },
})
