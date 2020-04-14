/**
 * Controller for admin page view
 * 
 * @param {type} $scope
 * @param {type} DTOptionsBuilder
 * @param {type} apiCallService
 * @param {type} toaster
 * @param {type} SweetAlert
 * @param {type} $filter
 * @returns {undefined}
 */
function adminCtrl($scope, DTOptionsBuilder, apiCallService, toaster, SweetAlert, $filter) {
    // Init for ng-model
    $scope.model = {};

    // File list view
    $scope.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.getFileList(true);
                    }
                }])
            .withOption('order', [1, 'asc'])
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_Admin_File', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Admin_File'));
            });

    $scope.fileList = [];
    $scope.jobMessage = "";
    $scope.dataType = [];
    $scope.files = []; // upload file list

    $scope.getFileList = function (display) {
        apiCallService.GetApiCall('/admin').success(function (data) {
            $scope.fileList = data['fileList'];
            $scope.jobMessage = data['jobMessage'];
            $scope.dataType = data['dataType'];
            if (typeof (display) !== 'undefined')
            {
                toaster.success({body: 'File list has been refreshed.'});
            }
        });
    };
    $scope.getFileList();

    // Job list view
    $scope.dtJobOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>Tfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.getJobList(true);
                    }
                }, {
                    text: 'Delete waiting jobs',
                    action: function () {
                        $scope.deleteJob(0);
                    }
                }])
            .withOption('searching', false)
            .withOption('order', [2, 'asc'])
            .withOption('responsive', true)
            .withOption('autoWidth', false);

    $scope.jobs = [];
    $scope.getJobList = function (display) {
        apiCallService.GetApiCall('/admin/job').success(function (data) {
            $scope.jobs = data['jobs'];
            angular.forEach($scope.jobs, function (value, key) {
                var _payload = JSON.parse(value.payload).data;
                value.name = _payload[0].split(":")[1];
                value.option = _payload[1];
                value.status = value.reserved > 0 ? '<span class="label label-success">Running</span>' : '';
            });
            if (typeof (display) !== 'undefined')
            {
                toaster.success({body: 'Job list has been refreshed.'});
            }
        });
    };
    $scope.getJobList();


    // File delete button
    $scope.confirmDelete = function (filename) {
        SweetAlert.swal({
            title: "Are you sure you want to delete this file?",
            text: "You will not be able to recover this file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.PostApiCall('/admin/delete/' + filename)
                        .success(function () {
                            apiCallService.GetApiCall('/admin').success(function (data) {
                                $scope.fileList = data['fileList'];
                                $scope.jobMessage = data['jobMessage'];
                                $scope.dataType = data['dataType'];
                            });
                            toaster.success({body: 'File has been deleted successfully.'});
                            SweetAlert.swal("Success!", "Your file has been deleted.", "success");
                        })
                        .error(function (response) {
                            toaster.error({body: 'Can not delete file.'});
                            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                        });

            } else {
                SweetAlert.swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    };

    // File import button
    $scope.confirmImport = function (filename, truncate) {
        SweetAlert.swal({
            title: "Are you sure you want to import this file to database?",
            text: "You will not be able to rollback this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, import it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.PostApiCall('/admin/import/' + filename + '/' + truncate)
                        .success(function () {
                            toaster.success({body: 'Import job has been added to queue.'});
                            SweetAlert.swal("Success!", "Import job has been added to queue.", "success");
                            $scope.getJobList(true);
                        })
                        .error(function (response) {
                            toaster.error({body: 'Can not add job to queue. Please try again.'});
                            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                        });
            } else {
                SweetAlert.swal("Cancelled", "Your job queue received nothing :)", "error");
            }
        });
    };

    // Database clear button
    $scope.confirmClearTable = function (action) {
        SweetAlert.swal({
            title: "Are you sure you want to clear this table?",
            text: "You will not be able to rollback this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, clear it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.PostApiCall('/admin/clear/table/' + action)
                        .success(function () {
                            toaster.success({body: 'Clear table job has been added to queue.'});
                            SweetAlert.swal("Success!", "Clear table job has been added to queue.", "success");
                            $scope.getJobList(true);
                        })
                        .error(function (response) {
                            toaster.error({body: 'Can not add job to queue. Please try again.'});
                            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                        });
            } else {
                SweetAlert.swal("Cancelled", "Your table is safe :)", "error");
            }
        });
    };

    // Bot start button
    $scope.confirmCalculate = function (action) {
        var date = '';
        if ($scope.model.date)
            date = $scope.model.date.format('YYYY-MM-DD');
        else
            date = $filter('date')(new Date(), 'yyyy-MM-dd');
        SweetAlert.swal({
            title: "Are you sure you want to start calculation?",
            text: "This action might take lots of time!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, do it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.PostApiCall('/admin/calculate/' + action + '/' + date)
                        .success(function () {
                            toaster.success({body: 'Calculate job has been added to queue.'});
                            SweetAlert.swal("Success!", "Calculate job has been added to queue.", "success");
                            $scope.getJobList(true);
                        })
                        .error(function (response) {
                            toaster.error({body: 'Can not add job to queue. Please try again.'});
                            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                        });
            } else {
                SweetAlert.swal("Cancelled", "Your job queue received nothing :)", "error");
            }
        });
    };

    // Clear server cache button
    $scope.confirmClearServerCache = function () {
        SweetAlert.swal({
            title: "Are you sure you want to clear server cache?",
            text: "This action will effect all users, and you will not be able to rollback this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, clear it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                apiCallService.PostApiCall('/admin/clear/cache')
                        .success(function () {
                            toaster.success({body: 'Server cache has been cleared.'});
                            SweetAlert.swal("Success!", "Server cache has been cleared.", "success");
                        })
                        .error(function (response) {
                            toaster.error({body: 'Can not clear server cache. Please try again.'});
                            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
                        });
            } else {
                SweetAlert.swal("Cancelled", "Server cache data is safe :)", "error");
            }
        });
    };

    // Clear local cache button
    $scope.confirmClearLocalCache = function () {
        SweetAlert.swal({
            title: "Are you sure you want to clear local cache?",
            text: "This action only effects YOU, and you will not be able to rollback this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, clear it!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                //lscache.flush();
                toaster.success({body: 'Local cache has been cleared.'});
                SweetAlert.swal("Success!", "Local cache has been cleared.", "success");
            } else {
                SweetAlert.swal("Cancelled", "Your cache data is safe :)", "error");
            }
        });
    };

    // Job delete button
    $scope.deleteJob = function (job_id) {
        apiCallService.DeleteApiCall('/admin/job/' + job_id).success(function (data) {
            $scope.getJobList(true);
            SweetAlert.swal("Deleted!", "Job has been deleted.", "success");
        }).error(function (response) {
            SweetAlert.swal("Error!", "Something happened.\n" + response.message, "error");
        });
    };
}

angular
        .module('lumine')
        .controller('adminCtrl', adminCtrl);