Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'command-runner',
            path: '/command-runner',
            component: require('./components/Tool'),
        },
    ])
})
