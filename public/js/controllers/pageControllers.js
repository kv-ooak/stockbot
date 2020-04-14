/**
 * Controller for main page view
 * 
 * @param {type} $scope
 * @param {type} apiCallService
 * @returns {undefined}
 */
function indexPageCtrl($scope, apiCallService) {
    apiCallService.GetApiCall('/index').success(function (data) {
        $scope.helloText = data['helloText'];
        $scope.descriptionText = data['descriptionText'];
    });
}

angular
        .module('lumine')
        .controller('indexPageCtrl', indexPageCtrl);