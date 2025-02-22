const ADMIN_ROUTE = '/admin'
import * as permissions from '../store/permissions'

export const HomeRoutePageName = 'welcome'

export default [
  { path: '/docs', name: 'docs', component: require('~/pages/docs/index').default },
  { path: '/terms-and-conditions', name: 'terms', component: require('~/pages/terms/index').default },
  { path: '/terms-of-service', name: 'terms-service', component: require('~/pages/termsService').default },
 // { path: '/apiquery', name: 'apiquery', component: require('~/pages/docs/apiquery').default },
  // Authenticated routes.
  ...middleware('auth', [
    {
      path: '/data/whatnow/:countryCode',
      name: 'view-whatnow-code',
      component: require('~/pages/view_data/whatnow').default,
      props: true
    },
    {
      path: '/data/whatnow/',
      name: 'view-whatnow',
      component: require('~/pages/view_data/whatnow').default
    },
    {
      path: '/data/alerts/:countryCode',
      name: 'view-alerts-code',
      component: require('~/pages/view_data/cap_alerts').default, props: true
    },
    {
      path: '/data/alerts/',
      name: 'view-alerts',
      component: require('~/pages/view_data/cap_alerts').default,
      props: { countryCode: 'USA' }
    },
    {
      path: '/data/alerts/view/:identifier',
      name: 'preview-cap',
      component: require('~/pages/view_data/cap_preview').default,
      props: true
    },
    {
      path: '/register/info',
      name: 'register-info',
      component: require('~/pages/auth/registerInfo').default
    },
    {
      path: '/home',
      name: 'home',
      component: require('~/pages/home').default
    },
    {
      path: '/new-welcome',
      name: 'new-welcome',
      component: require('~/pages/new-welcome').default
    },
    {
      path: '/user-agreement',
      name: 'user-agreement',
      component: require('~/pages/user_agreement').default
    },
    {
      path: '/settings',
      component: require('~/pages/settings/index').default,
      children: [
        { path: '', redirect: { name: 'settings.profile' }},
        { path: 'profile', name: 'settings.profile', component: require('~/pages/users/edit').default, props: true, meta: { breadcrumb: 'Profile' }}
      ]
    },
    {
      path: '/applications',
      component: require('~/pages/application/index').default,
      meta: { breadcrumb: 'Applications' },
      children: [
        { path: '', name: 'applications.dash', component: require('~/pages/application/apiDash').default },
        { path: 'create', name: 'applications.create', component: require('~/pages/application/create').default }
      ]
    },
    {
      path: '/terms-conditions',
      name: 'api-usage.terms-conditions',
      component: require('~/pages/api_usage/terms_conditions').default,
      meta: {
        breadcrumb: 'Terms and Conditions',
        permission: permissions.TERMS_UPDATE
      }
    },
    ...middleware('admin', [
      {
        path: `${ADMIN_ROUTE}/users`,
        component: require('~/pages/users/index').default,
        meta: { breadcrumb: 'Admin Users' },
        children: [
          { path: '', redirect: { name: 'users.list' }, meta: { breadcrumb: 'Users', permission: permissions.USERS_LIST }},
          { path: 'list', name: 'users.list', component: require('~/pages/users/list').default, meta: { breadcrumb: 'Manage Users' }, props: { apiUsers: false, permission: permissions.USERS_LIST }},
          { path: 'edit/:id', name: 'users.edit', component: require('~/pages/users/edit').default, props: true, meta: { breadcrumb: 'Edit', permission: permissions.USERS_EDIT }},
          { path: 'new', name: 'users.new', component: require('~/pages/users/edit').default, props: { createNew: true }, meta: { breadcrumb: 'Create', permission: permissions.USERS_CREATE }}
        ]
      },
      {
        path: `${ADMIN_ROUTE}/content`,
        component: require('~/pages/content/index').default,
        meta: { breadcrumb: 'Content' },
        children: [
          { path: '', redirect: { name: 'content.whatnow' }},
          { path: 'whatnow/:countryCode/:regionSlug?', name: 'content.whatnow', component: require('~/pages/content/whatnow').default, meta: { breadcrumb: 'What Now Content' }, props: true },
          { path: ':whatnowId/:langCode/:regionSlug?', name: 'content.editWhatnow', component: require('~/pages/content/editWhatnow').default, meta: { breadcrumb: 'Edit' }, props: true },
          { path: ':organisation/create/:langCode/:regionSlug?/:eventTypeToCreate?', name: 'content.create', component: require('~/pages/content/editWhatnow').default, meta: { breadcrumb: 'Create' }, props: true },
          { path: 'bulk-upload', name: 'content.bulk_upload', component: require('~/pages/content/bulkUpload').default, meta: { breadcrumb: 'Bulk Upload' }},
          { path: 'audit-log', name: 'content.audit_log', component: require('~/pages/content/auditLog').default, meta: { breadcrumb: 'Audit Log' }}
        ]
      },
      {
        path: `${ADMIN_ROUTE}/regions`,
        component: require('~/pages/regions/index').default,
        meta: { breadcrumb: 'Regions', permission: permissions.CONTENT_CREATE },
        name: 'content.regions'
      },
      {
        path: `${ADMIN_ROUTE}/api-usage`,
        component: require('~/pages/api_usage/index').default,
        meta: { breadcrumb: 'API Usage' },
        children: [
          {
            path: '',
            redirect: {
              name: 'api_usage.applications'
            },
            meta: {
              breadcrumb: 'API Usage'
            }
          },
          {
            path: 'app-detail',
            name: 'api-usage.app-detail',
            component: require('~/pages/api_usage/app_detail').default,
            meta: {
              breadcrumb: 'App Detail'
            }
          },
          {
            path: 'api-stats',
            name: 'api-usage.api-stats',
            component: require('~/pages/api_stats/index').default,
            meta: {
              breadcrumb: 'API Stats'
            }
          },
          {
            path: 'api-users',
            name: 'api-usage.api-users',
            component: require('~/pages/users/list').default,
            props: {
              apiUsers: true
            },
            meta: {
              breadcrumb: 'API Users',
              permission: permissions.USERS_LIST
            }
          },
          {
            path: 'previous-terms-conditions',
            name: 'api-usage.prev_terms_conditions',
            component: require('~/pages/api_usage/prev_terms_conditions').default,
            meta: {
              breadcrumb: 'All Terms and Conditions',
              permission: permissions.TERMS_UPDATE
            }
          }
        ]
      }
    ])
  ]),

  // Guest routes.
  ...middleware('guest', [
    { path: '/', name: HomeRoutePageName, component: require('~/pages/welcome').default },
    { path: '/login', name: 'login', component: require('~/pages/auth/login').default },
    { path: '/login/confirmed', name: 'login.confirmed', component: require('~/pages/auth/login').default, props: { confirmed: true }},
    { path: '/login/confirm-failed', name: 'login.confirmFailed', component: require('~/pages/auth/login').default, props: { confirmFailed: true }},
    { path: '/register', name: 'register', component: require('~/pages/auth/register').default, props: true },
    { path: '/password/reset', name: 'password.request', component: require('~/pages/auth/password/email').default },
    { path: '/password/reset/:token', name: 'password.reset', component: require('~/pages/auth/password/reset').default,
      props: (route) => ({ token: route.params.token, isReset: true }) },
    { path: '/password/set/:token', name: 'password.set', component: require('~/pages/auth/password/reset').default,
      props: (route) => ({ token: route.params.token, isReset: false }) },
    { path: '/get-started', name: 'get_started', component: require('~/pages/get_started/index').default },
    { path: '/legal/terms', name: 'legal_.terms', component: require('~/pages/legal_terms/index').default }
  ]),

  { path: '*', component: require('~/pages/errors/404.vue').default }
]

/**
 * @param  {String|Function} middleware
 * @param  {Array} routes
 * @return {Array}
 */
function middleware (middleware, routes) {
  routes.forEach(route =>
    (route.middleware || (route.middleware = [])).unshift(middleware)
  )

  return routes
}
