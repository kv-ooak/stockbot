/**
 * MainCtrl - controller
 */
function MainCtrl($scope, $state, config, $auth) {

    this.userName = 'Lumine';
    this.userRole = 'Menu';

    // Overwrite ajax setting
    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Bearer " + $auth.getToken());
        },
        error: function (xhr, error, thrown) {
            if (error === "parsererror") {
                // Do nothing
                console.log(error);
            }
        },
        complete: function (xhr, status) {
            if (xhr.status === 401 || xhr.status === 400) {
                $state.go('login', {});
            } else if (xhr.getResponseHeader("Authorization") !== null) {
                var str = xhr.getResponseHeader("Authorization").split(" ");
                $auth.setToken(str[1]);
            }
        }
    });

}

angular
        .module('lumine')
        .controller('MainCtrl', MainCtrl);