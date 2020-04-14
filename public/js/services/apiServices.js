/**
 * 
 * @param {type} $http
 * @param {type} config
 * @param {type} $auth
 * @param {type} $state
 * @returns {apiCallService}
 */
function apiCallService($http, config, $auth, $state) {
    var result;

    this.GetApiCall = function (method) {
        result = $http.get(config.apiUrl + method)
                .success(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     $auth.setToken(str[1]);
                     }*/
                    result = data;
                })
                .error(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     $auth.setToken(str[1]);
                     }*/
                    if (status === 401 || status === 400) {
                        $state.go('login', {});
                    }
                });
        return result;
    };

    this.PostApiCall = function (method, obj) {
        result = $http.post(config.apiUrl + method, obj)
                .success(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     //$auth.setToken(str[1]);
                     }*/
                    result = data;
                })
                .error(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     //$auth.setToken(str[1]);
                     }*/
                    if (status === 401 || status === 400) {
                        $state.go('login', {});
                    }
                });
        return result;
    };

    this.DeleteApiCall = function (method) {
        result = $http.delete(config.apiUrl + method)
                .success(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     //$auth.setToken(str[1]);
                     }*/
                    result = data;
                })
                .error(function (data, status, headers) {
                    /*
                     if (headers('Authorization') !== null) {
                     var str = headers('Authorization').split(" ");
                     //$auth.setToken(str[1]);
                     }*/
                    if (status === 401 || status === 400) {
                        $state.go('login', {});
                    }
                });
        return result;
    };
}

angular
        .module('lumine')
        .service('apiCallService', apiCallService);