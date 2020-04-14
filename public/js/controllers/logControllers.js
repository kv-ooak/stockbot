/**
 * Controller for log view
 * 
 * @param {type} $scope
 * @param {type} DTColumnBuilder
 * @param {type} DTOptionsBuilder
 * @param {type} $stateParams
 * @param {type} config
 * @param {type} apiCallService
 * @returns {undefined}
 */
function logCtrl($scope, DTColumnBuilder, DTOptionsBuilder, $stateParams, config, apiCallService) {
    var tab = $stateParams.tab;
    $scope.tabs = {
        tabQueue: tab === 'queue' || tab === '',
        tabAction: tab === 'action',
    };

    $scope.queueStatus = [
        '<span class="label">Unknown</span>',
        '<span class="label label-success">Start</span>',
        '<span class="label label-warning">End</span>',
        '<span class="label label-danger">Error</span>'
    ];
    
    $scope.actionStatus = [
        '<span class="label">Unknown</span>',
        '<span class="label label-success">Ok</span>',
        '<span class="label label-danger">Error</span>'
    ];
    
    $scope.actionType = [
        '<span class="label">Unknown</span>',
        '<span class="label label-success">User</span>',
        '<span class="label label-danger">Admin</span>'
    ];

    $scope.queue = {};
    $scope.action = {};

    // Queue log table
    $scope.queue.dtColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("action").withTitle('Action'),
        DTColumnBuilder.newColumn("param").withTitle('Param'),
        DTColumnBuilder.newColumn("status").withTitle('Status').withOption('render', function (data) {
            return $scope.queueStatus[data];
        }),
        DTColumnBuilder.newColumn("comment").withTitle('Comment'),
    ];
    $scope.queue.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.queue.reloadTable();
                    }
                }])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/log/queue',
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_QueueLog', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_QueueLog'));
            });
    $scope.queue.dtInstance = {};
    $scope.queue.reloadTable = function () {
        $scope.queue.dtInstance.reloadData(null, false);
    };

    // Action log table
    $scope.action.dtColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("type").withTitle('Type').withOption('render', function (data) {
            return $scope.actionType[data];
        }),
        DTColumnBuilder.newColumn("user_id").withTitle('User ID'),
        DTColumnBuilder.newColumn("action").withTitle('Action'),
        DTColumnBuilder.newColumn("param").withTitle('Param'),
        DTColumnBuilder.newColumn("status").withTitle('Status').withOption('render', function (data) {
            return $scope.actionStatus[data];
        }),
        DTColumnBuilder.newColumn("comment").withTitle('Comment'),
    ];
    $scope.action.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.action.reloadTable();
                    }
                }])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/log/action',
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_QueueLog', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_QueueLog'));
            });
    $scope.action.dtInstance = {};
    $scope.action.reloadTable = function () {
        $scope.action.dtInstance.reloadData(null, false);
    };
}

angular
        .module('lumine')
        .controller('logCtrl', logCtrl);