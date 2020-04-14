/**
 * Controller for login page view
 * 
 * @param {type} $scope
 * @param {type} apiCallService
 * @param {type} $auth
 * @param {type} $state
 * @returns {undefined}
 */
function authLoginCtrl($scope, apiCallService, $auth, $state) {
    if ($auth.getToken()) {
        apiCallService.PostApiCall('/auth/status').success(function (data) {
            if (data.status)
                $state.go('index.main', {});
        });
    }

    $scope.auth = {};
    $scope.login = function () {
        var credentials = $scope.auth;
        // Use Satellizer's $auth service to login
        $auth.login(credentials).then(function (data) {
            // If login is successful, redirect to the main state
            $state.go('index.main', {});
        });
    };
}

/**
 * Controller for logout button
 * 
 * @param {type} $scope
 * @param {type} apiCallService
 * @param {type} $auth
 * @param {type} $state
 * @returns {undefined}
 */
function authLogoutCtrl($scope, apiCallService, $auth, $state) {
    $scope.logout = function () {
        apiCallService.PostApiCall('/auth/logout')
                .success(function () {
                    $auth.logout().then(function (data) {
                        // If logoutis successful, redirect to the login state
                        $state.go('login', {});
                    });
                });
    };
}

angular
        .module('lumine')
        .controller('authLoginCtrl', authLoginCtrl)
        .controller('authLogoutCtrl', authLogoutCtrl);