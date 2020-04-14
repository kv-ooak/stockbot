/**
 * 
 * @param {type} $stateProvider
 * @param {type} $urlRouterProvider
 * @param {type} $ocLazyLoadProvider
 * @param {type} $authProvider
 * @param {type} config
 * @returns {undefined}
 */
function config($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, $authProvider, $locationProvider, config, fcsaNumberConfigProvider) {
    // Satellizer configuration that specifies which API
    // route the JWT should be retrieved from
    $authProvider.loginUrl = config.apiUrl + '/auth/login';
    $authProvider.signupUrl = config.apiUrl + '/auth/signup';
    //$authProvider.storageType = 'sessionStorage';

    // Redirect to login page when empty state
    $urlRouterProvider.when('', '/login');
    // Redirect to the 404 state (page not found) if any other states are requested
    $urlRouterProvider.otherwise('/404');

    $ocLazyLoadProvider.config({
        // Set to true if you want to see what and when is dynamically loaded
        debug: false
    });

    // FCSA Number Default Options
    fcsaNumberConfigProvider.setDefaultOptions({
        preventInvalidInput: true,
        maxDecimals: 2
    });

    // Fix open in new tab
    $locationProvider.html5Mode({enabled: false, requireBase: true});

    $stateProvider
            .state('index', {
                abstract: true,
                url: "/index",
                templateUrl: "views/common/content.html",
            })
            .state('index.main', {
                url: "/main",
                templateUrl: "views/main.html",
                data: {pageTitle: 'Dashboard'}
            })
            .state('login', {
                url: '/login',
                templateUrl: 'views/auth/login.html',
                data: {pageTitle: 'Login', specialClass: 'gray-bg'}
            })
            .state('register', {
                url: '/register',
                templateUrl: 'views/auth/register.html',
                data: {pageTitle: 'Register', specialClass: 'gray-bg'}
            })
            .state('forgot_password', {
                url: '/forgot_password',
                templateUrl: 'views/auth/forgot_password.html',
                data: {pageTitle: 'Forgot Password', specialClass: 'gray-bg'}
            })
            .state('error404', {
                url: "/404",
                templateUrl: "views/error/404.html",
                data: {pageTitle: '404', specialClass: 'gray-bg'}
            })
            .state('error500', {
                url: "/500",
                templateUrl: "views/error/500.html",
                data: {pageTitle: '500', specialClass: 'gray-bg'}
            })
            .state('ticker', {
                abstract: true,
                url: "/ticker",
                templateUrl: "views/common/content.html",
            })
            .state('ticker.index', {
                url: "/index",
                templateUrl: "views/ticker/ticker.html",
                data: {pageTitle: 'Ticker List'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            }
                        ]);
                    }
                }
            })
            .state('ticker.show', {
                url: "/show/:ticker/:tab",
                param: {ticker: '', tab: ''},
                templateUrl: "views/ticker/tickerData.html",
                data: {pageTitle: 'Ticker Detail'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            }
                        ]);
                    }
                }
            })
            .state('ticker.recommend', {
                url: "/recommend",
                templateUrl: "views/ticker/tickerRecommend.html",
                data: {pageTitle: 'Ticker Recommend'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            }
                        ]);
                    }
                }
            })
            .state('ticker.quote', {
                url: "/quote/:ticker",
                param: {ticker: ''},
                templateUrl: "views/ticker/tickerQuote.html",
                data: {pageTitle: 'Ticker Quote'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            }
                        ]);
                    }
                }
            })
            .state('ticker.chart', {
                url: "/chart/:ticker",
                param: {ticker: ''},
                templateUrl: "views/ticker/tickerChart.html",
                data: {pageTitle: 'Chart view'}
            })
            .state('trade', {
                abstract: true,
                url: "/trade",
                templateUrl: "views/common/content.html",
            })
            .state('trade.index', {
                url: "/index",
                templateUrl: "views/trade/index.html",
                data: {pageTitle: 'Trade Manager'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            },
                            {
                                files: ['js/plugins/sweetalert/sweetalert.min.js', 'css/plugins/sweetalert/sweetalert.css']
                            },
                            {
                                name: 'oitozero.ngSweetAlert',
                                files: ['js/plugins/sweetalert/angular-sweetalert.min.js']
                            }
                        ]);
                    }
                }
            })
            .state('admin', {
                abstract: true,
                url: "/admin",
                templateUrl: "views/common/content.html",
            })
            .state('admin.index', {
                url: "/index",
                templateUrl: "views/admin/admin.html",
                data: {pageTitle: 'Administrator Menu'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            },
                            {
                                files: ['css/plugins/dropzone/basic.css', 'css/plugins/dropzone/dropzone.css', 'js/plugins/dropzone/dropzone.js']
                            },
                            {
                                insertBefore: '#loadBefore',
                                name: 'toaster',
                                files: ['js/plugins/toastr/toastr.min.js', 'css/plugins/toastr/toastr.min.css']
                            },
                            {
                                files: ['js/plugins/sweetalert/sweetalert.min.js', 'css/plugins/sweetalert/sweetalert.css']
                            },
                            {
                                name: 'oitozero.ngSweetAlert',
                                files: ['js/plugins/sweetalert/angular-sweetalert.min.js']
                            },
                            {
                                name: 'datePicker',
                                files: ['css/plugins/datapicker/angular-datapicker.css', 'js/plugins/datapicker/angular-datepicker.js']
                            },
                            {
                                serie: true,
                                files: ['js/plugins/moment/moment.min.js', 'js/plugins/daterangepicker/daterangepicker.js', 'css/plugins/daterangepicker/daterangepicker-bs3.css']
                            }
                        ]);
                    }
                }
            })
            .state('admin.log', {
                url: "/log/:tab",
                param: {tab: ''},
                templateUrl: "views/admin/log.html",
                data: {pageTitle: 'Action Log'},
                resolve: {
                    loadPlugin: function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            {
                                serie: true,
                                files: ['js/plugins/dataTables/datatables.min.js', 'css/plugins/dataTables/datatables.min.css']
                            },
                            {
                                serie: true,
                                name: 'datatables',
                                files: ['js/plugins/dataTables/angular-datatables.min.js']
                            },
                            {
                                serie: true,
                                name: 'datatables.buttons',
                                files: ['js/plugins/dataTables/angular-datatables.buttons.min.js']
                            }
                        ]);
                    }
                }
            });
}

angular
        .module('lumine')
        .config(config)
        .run(function ($rootScope, $state) {
            $rootScope.$state = $state;
        });
