function tradeService(apiCallService, $modal) {
    // Add fund to account
    this.addFund = function (account_id, $scope) {
        if (account_id > 0)
        {
            $scope.account_id = account_id;
            var modalInstance = $modal.open({
                templateUrl: 'views/trade/addFund.html',
                controller: tradeAddFundCtrl,
                scope: $scope,
                size: 'sm'
            });
        }
    };


    // Add buy item log
    this.buyItem = function (account_id, ticker, price, $scope) {
        apiCallService.GetApiCall('/account/buy?account_id=' + account_id).success(function (data) {
            $scope.formData = data['data'];
            $scope.formData.ticker = typeof ticker !== 'undefined' ? ticker : "";
            $scope.account_id = account_id;
            $scope.price = price;
            var modalInstance = $modal.open({
                templateUrl: 'views/trade/buyItem.html',
                controller: tradeBuyItemCtrl,
                scope: $scope,
                size: 'sm'
            });
        });
    };

    // Add sell item log
    this.sellItem = function (account_id, ticker, price, $scope) {
        if (account_id > 0)
        {
            apiCallService.GetApiCall('/account/sell?account_id=' + account_id).success(function (data) {
                $scope.formData = data['data'];
                $scope.formData.ticker = typeof ticker !== 'undefined' ? ticker : "";
                $scope.account_id = account_id;
                $scope.price = price;
                var modalInstance = $modal.open({
                    templateUrl: 'views/trade/sellItem.html',
                    controller: tradeSellItemCtrl,
                    scope: $scope,
                    size: 'sm'
                });
            });
        }
    };
}

angular
        .module('lumine')
        .service('tradeService', tradeService);


/**
 * Add fund to trade account
 * 
 * @param {type} $scope
 * @param {type} $modalInstance
 * @param {type} apiCallService
 * @returns {undefined}
 */
function tradeAddFundCtrl($scope, $modalInstance, apiCallService) {
    // Init for ng-model
    $scope.model = {};
    $scope.model.account = {'id': $scope.account_id};
    $scope.ok = function () {
        if ($scope.model.amount !== null && $scope.model.amount !== 0)
            apiCallService.PostApiCall('/account/fund', $scope.model)
                    .success(function () {
                        $scope.getAccountList();
                        $modalInstance.close();
                    })
                    .error(function (response) {
                    });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}

/**
 * Buy item form
 * 
 * @param {type} $scope
 * @param {type} $modalInstance
 * @param {type} apiCallService
 * @returns {undefined}
 */
function tradeBuyItemCtrl($scope, $modalInstance, apiCallService) {
    // Init for ng-model
    $scope.model = {};
    $scope.model.account = {'id': $scope.account_id};
    $scope.model.ticker = {'ticker': $scope.formData.ticker};
    $scope.ok = function () {
        if ($scope.model.account.id > 0 && $scope.model.ticker.ticker !== '' && $scope.model.price !== null && $scope.model.amount !== null && $scope.model.price > 0 && $scope.model.amount > 0)
            apiCallService.PostApiCall('/account/buy', $scope.model)
                    .success(function () {
                        try {
                            $scope.getAccountList(); // Reload when on trade manager page
                        } catch (err) {
                        }
                        $modalInstance.close();
                    })
                    .error(function (response) {
                    });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}

/**
 * Sell item form
 * 
 * @param {type} $scope
 * @param {type} $modalInstance
 * @param {type} apiCallService
 * @returns {undefined}
 */
function tradeSellItemCtrl($scope, $modalInstance, apiCallService) {
    // Init for ng-model
    $scope.model = {};
    $scope.model.account = {'id': $scope.account_id};
    $scope.model.ticker = {'ticker': $scope.formData.ticker};
    $scope.ok = function () {
        if ($scope.model.ticker.ticker !== '' && $scope.model.price !== null && $scope.model.amount !== null && $scope.model.price > 0 && $scope.model.amount > 0)
            apiCallService.PostApiCall('/account/sell', $scope.model)
                    .success(function () {
                        try {
                            $scope.getAccountList(); // Reload when on trade manager page
                        } catch (err) {
                        }
                        $modalInstance.close();
                    })
                    .error(function (response) {
                    });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}

angular
        .module('lumine')
        .controller('tradeAddFundCtrl', tradeAddFundCtrl)
        .controller('tradeBuyItemCtrl', tradeBuyItemCtrl)
        .controller('tradeSellItemCtrl', tradeSellItemCtrl);