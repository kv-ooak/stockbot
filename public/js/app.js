/**
 * 
 * @returns {undefined}
 */
(function () {
    angular.module('lumine', [
        'ui.router', // Routing
        'oc.lazyLoad', // ocLazyLoad
        'ui.bootstrap', // Ui Bootstrap
        'satellizer', // Satellizer for JWT
        'ngSanitize', // ngSanitize
        'fcsa-number', // FCSA Number: format number for input form
    ]).constant('config', {
        appName: 'Stock Bot',
        appVersion: 1.0,
        apiUrl: 'http://stockbot.info/api',
        localCacheEnable: true,
    });
})();