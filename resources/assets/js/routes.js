import VueRouter from 'vue-router';

let routes = [
    /* Welcome */
    {
        path: '/',
        component: require('./components/welcome/Welcome.vue')
    },
    {
        path: '/about',
        component: require('./components/welcome/About.vue')
    },
    {
        path: '/login',
        component: require('./components/welcome/Login.vue')
    },
    /* Tests */
    {
        path: '/tests',
        component: require('./components/tests/Tests.vue')
    },
    {
        path: '/test/:id',
        component: require('./components/tests/TestItem.vue')
    },
    {
        path: '/test/:id/:alias/:item/token/:token',
        component: require('./components/tests/TestItem.vue')
    },
    /* Manager */
    {
        path: '/manager',
        component: require('./components/manager/Manager.vue')
    },
    {
        path: '/manager/tests',
        component: require('./components/manager/Tests/Tests.vue')
    },
    {
        path: '/manager/test/:id',
        component: require('./components/manager/Tests/TestElement.vue')
    },
    {
        path: '/manager/directories',
        component: require('./components/manager/Directories.vue')
    },
    {
        path: '/manager/municipalities',
        component: require('./components/manager/Municipalities.vue')
    },
    {
        path: '/manager/schools',
        component: require('./components/manager/Schools.vue')
    },
    {
        path: '/manager/users',
        component: require('./components/manager/Users.vue')
    },
];

const router = new VueRouter({
    routes
});

/**
 * @brief Настройки для маршрутизатора
 */
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
    if (sessionApiId === '') {
        window.location = baseurl;
    } else {
        next();
    }
} else {
    if(to.fullPath.indexOf("manager") != -1 && to.fullPath != '/manager') {
        localStorage['manager_mode'] = to.fullPath;
    }
    next();
}
});

export default router;