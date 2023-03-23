export default [
  ...applyRules(['guest'], [{
    path: '',
    component: require('$comp/auth/AuthWrapper').default,
    redirect: {
      name: 'login'
    },
    children: [{
        path: 'login',
        name: 'login',
        component: require('$comp/auth/login/Login').default
      },
    ]
  }, ]),
  ...applyRules(['auth'], [{
    path: '',
    component: require('$comp/main_components/EmployeeWrapper').default,
    children: [
      {
        path: '',
        name: 'index',
        redirect: {
          name: 'dashboard'
        }
      },
      {
        path: '',
        name: 'dashboard',
        component: require('$comp/main_components/Dashboard').default
      },
      {
        path: '/setting',
        component: require('$comp/main_components/order/Index').default,
        children: [{
            path: 'order',
            name: 'order',
            component: require('$comp/main_components/order/Order').default,
          },
        ]
      }
    ],

  }]),
  {
    path: '*',
    redirect: {
      name: 'index'
    }
  }
]

function applyRules(rules, routes) {
  for (let i in routes) {
    routes[i].meta = routes[i].meta || {}

    if (!routes[i].meta.rules) {
      routes[i].meta.rules = []
    }
    routes[i].meta.rules.unshift(...rules)

    if (routes[i].children) {
      routes[i].children = applyRules(rules, routes[i].children)
    }
  }

  return routes
}
