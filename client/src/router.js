import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/admin/',
            component: () => import('@/views/dashboard/Index'),
            children: [
                // Dashboard
                {	
                    name: 'Admin',	
                    path: 'chapter-headings',	
                    component: () => import('@/views/dashboard/component/ChapterHeading'),	
                },
            ],
        },
        {
            name: 'Chapter Heading',
            path: '/',
            component: () => import('@/views/dashboard/Landing'),
        },
        {
            path: '/login',
            component: () => import('@/views/dashboard/Login'),
        },
        {
            path: '/reset-password',
            component: () => import('@/views/dashboard/ResetPassword'),
        },
    ],
})
