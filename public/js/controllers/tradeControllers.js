/**
 * Controller for ticker list view
 * 
 * @param {type} $scope
 * @param {type} DTOptionsBuilder
 * @param {type} apiCallService
 * @param {type} tradeService
 * @param {type} SweetAlert
 * @param {type} $modal
 * @returns {undefined}
 */
function tradeAccountViewCtrl($scope, DTOptionsBuilder, apiCallService, tradeService, SweetAlert, $modal) {
    // Init some default values
    $scope.accounts = [];
    $scope.account_id = 0; // parse to account edit, fund view
    $scope.dataView = {}; // parse to account transaction log view
    $scope.dataView.account_name = '';
    $scope.dataView.account_id = 0;
    $scope.dataView.money = 0;
    $scope.logAction = [
        '<span class="label">Unknown</span>',
        '<span class="label label-warning">Fund</span>',
        '<span class="label label-success">Buy</span>',
        '<span class="label label-danger">Sell</span>'
    ];

    // Account list view
    $scope.dtAccountOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgt')
            .withButtons([])
            .withOption('searching', false)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_Trade_Account', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Trade_Account'));
            });

    // Account summary view
    $scope.dtSummaryOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgitp')
            .withButtons([])
            .withOption('searching', false)
            .withOption('responsive', true)
            .withDisplayLength(10) // Page size
            .withOption('order', [1, 'asc']);

    // Transaction log view
    $scope.dtTransactionOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgitp')
            .withButtons([])
            .withOption('searching', false)
            .withOption('responsive', true)
            .withDisplayLength(10) // Page size
            .withOption('order', [0, 'desc']);

    // Account sold history view
    $scope.dtSoldHistoryOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgitp')
            .withButtons([])
            .withOption('searching', false)
            .withOption('responsive', true)
            .withDisplayLength(10) // Page size
            .withOption('order', [0, 'desc']);

    // Get account list
    $scope.getAccountList = function () {
        apiCallService.GetApiCall('/account').success(function (data) {
            $scope.accounts = data['data']['list'];
            if ($scope.dataView.account_id > 0) {
                // Load sellected account
                $scope.getSummaryData();
            } else if ($scope.accounts.length > 0) {
                // Load default account
                $scope.dataView.account_id = $scope.accounts[0].id;
                $scope.dataView.account_name = $scope.accounts[0].account_name;
                $scope.dataView.money = $scope.accounts[0].money;
                $scope.getSummaryData();
            } else {
                // No account
                $scope.summaryData = {};
                $scope.transactionLog = {};
                $scope.soldHistory = {};
            }
        });
    };
    $scope.getAccountList();

    // Get summary data
    $scope.getSummaryData = function () {
        apiCallService.GetApiCall('/account/summary?account_id=' + $scope.dataView.account_id).success(function (data) {
            $scope.summaryData = data['data']['account_item'];
            $scope.transactionLog = data['data']['account_transaction'];
            $scope.soldHistory = data['data']['account_history'];
        });
    };

    // Add account
    $scope.addAccount = function () {
        var modalInstance = $modal.open({
            templateUrl: 'views/trade/addAccount.html',
            controller: tradeAccountAddCtrl,
            scope: $scope
        });
    };

    // Add fund to account
    $scope.addFund = function (account_id) {
        tradeService.addFund(account_id, $scope);
    };

    // Remove account
    $scope.removeAccount = function (account_id) {
        SweetAlert.swal({
            title: "Are you sure you want to delete this account?",
            text: "You will not be able to recover this account!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.DeleteApiCall('/account?account_id=' + account_id).success(function (data) {
                    $scope.getAccountList();
                    SweetAlert.swal("Deleted!", "Your account has been deleted.", "success");
                }).error(function (response) {
                    SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                });
            } else {
                SweetAlert.swal("Cancelled", "Your account is safe :)", "error");
            }
        });
    };

    // Load transaction log for account
    $scope.loadData = function (account_id, name, money) {
        $scope.dataView.account_id = account_id;
        $scope.dataView.account_name = name;
        $scope.dataView.money = money;
        $scope.getSummaryData();
    };

    // Add buy item log
    $scope.buyItem = function (account_id, ticker, price) {
        tradeService.buyItem(account_id, ticker, price, $scope);
    };

    // Add sell item log
    $scope.sellItem = function (account_id, ticker, price) {
        tradeService.sellItem(account_id, ticker, price, $scope);
    };
}

/**
 * Add new trade account
 * 
 * @param {type} $scope
 * @param {type} $modalInstance
 * @param {type} apiCallService
 * @returns {undefined}
 */
function tradeAccountAddCtrl($scope, $modalInstance, apiCallService) {
    // Init for ng-model
    $scope.model = {};
    $scope.ok = function () {
        apiCallService.PostApiCall('/account', $scope.model)
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

angular
        .module('lumine')
        .controller('tradeAccountViewCtrl', tradeAccountViewCtrl);