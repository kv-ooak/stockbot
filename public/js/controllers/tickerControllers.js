/**
 * Controller for ticker list view
 * 
 * @param {type} $scope
 * @param {type} DTColumnBuilder
 * @param {type} DTOptionsBuilder
 * @param {type} config
 * @param {type} $compile
 * @returns {undefined}
 */
function tickerListDatatablesCtrl($scope, DTColumnBuilder, DTOptionsBuilder, config, $compile) {
    $scope.dtColumns = [
        DTColumnBuilder.newColumn("id").withTitle('ID'),
        DTColumnBuilder.newColumn("ticker").withTitle('Ticker').withOption('render', function (tickerName) {
            return '<a type="button" class="btn btn-primary btn-xs" tooltip-placement="top" tooltip="Chart View" href="#/ticker/chart/' + tickerName + '"><i class="fa fa-bar-chart-o"></i></a>'
                    + '<a type="button" class="btn btn-primary btn-xs m-l-xs" tooltip-placement="top" tooltip="Quote" href="#/ticker/quote/' + tickerName + '"><i class="fa fa-exchange"></i></a>'
                    + '<a class="m-l-sm" href="#/ticker/show/' + tickerName + '/bot">' + tickerName + '</a>';
        }),
        DTColumnBuilder.newColumn("exchange").withTitle('Exchange'),
        DTColumnBuilder.newColumn("outstanding").withTitle('Outstanding'),
        DTColumnBuilder.newColumn("listed").withTitle('Listed'),
        DTColumnBuilder.newColumn("treasury").withTitle('Treasury'),
        DTColumnBuilder.newColumn("foreign_owned").withTitle('Foreign Owned'),
        DTColumnBuilder.newColumn("equity").withTitle('Equity'),
    ];
    $scope.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.reloadTable();
                    }
                }])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/list',
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'asc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_Ticker', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Ticker'));
            })
            .withOption('drawCallback', function (settings) {
                $compile(angular.element('#' + settings.sTableId).contents())($scope); // bind angular after draw table complete
            });
    $scope.dtInstance = {};
    $scope.reloadTable = function () {
        $scope.dtInstance.reloadData(null, false);
    };
}

/**
 * Controller for ticker detail page view
 * 
 * @param {type} $scope
 * @param {type} DTColumnBuilder
 * @param {type} DTOptionsBuilder
 * @param {type} $stateParams
 * @param {type} config
 * @param {type} tradeService
 * @param {type} apiCallService
 * @returns {undefined}
 */
function tickerDataDatablesCtrl($scope, DTColumnBuilder, DTOptionsBuilder, $stateParams, config, tradeService, apiCallService) {
    var ticker = $stateParams.ticker;
    var tab = $stateParams.tab;
    $scope.ticker = ticker;
    $scope.tickerData = {};
    $scope.tabs = {
        tabData: tab === 'data' || tab === '',
        tabBot: tab === 'bot',
        tabRecommend: tab === 'recommend'
    };

    // Raw data table
    $scope.dtDataColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("open").withTitle('Open'),
        DTColumnBuilder.newColumn("high").withTitle('High'),
        DTColumnBuilder.newColumn("low").withTitle('Low'),
        DTColumnBuilder.newColumn("close").withTitle('Close'),
        DTColumnBuilder.newColumn("volume").withTitle('Volume').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
    ];
    $scope.dtDataOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/data/' + ticker,
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_TickerDetail_Data', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_TickerDetail_Data'));
            });

    // Bot data table
    $scope.dtBotColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("avg_volume_20").withTitle('Avg Volume 20').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        //DTColumnBuilder.newColumn("EMA9").withTitle('EMA9'),
        //DTColumnBuilder.newColumn("MA20").withTitle('MA20'),
        DTColumnBuilder.newColumn("MA42").withTitle('MA42'),
        DTColumnBuilder.newColumn("MACD").withTitle('MACD'),
        DTColumnBuilder.newColumn("MACDSignal").withTitle('MACDSignal'),
        //DTColumnBuilder.newColumn("RSI").withTitle('RSI'),
        //DTColumnBuilder.newColumn("SAR").withTitle('SAR'),
        DTColumnBuilder.newColumn("UpperBB").withTitle('UpperBB'),
        DTColumnBuilder.newColumn("LowerBB").withTitle('LowerBB'),
        //DTColumnBuilder.newColumn("plusDI").withTitle('plusDI'),
        //DTColumnBuilder.newColumn("minusDI").withTitle('minusDI'),
        //DTColumnBuilder.newColumn("ADX").withTitle('ADX'),
        DTColumnBuilder.newColumn("CCI").withTitle('CCI'),
    ];
    $scope.dtBotOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([])
            .withOption('searchDelay', 1000)
            //.withOption('scrollX', true)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/bot/' + ticker,
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_TickerDetail_Bot', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_TickerDetail_Bot'));
            });

    // Recommend detail data table
    $scope.dtRecommendColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("avg_volume_20").withTitle('Avg Volume 20').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("price").withTitle('Close Price'),
        DTColumnBuilder.newColumn("net_buy").withTitle('Net Buy').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("net_buy_value").withTitle('Net Buy Value').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("indicator").withTitle('Indicator'),
        DTColumnBuilder.newColumn("signal").withTitle('Signal'),
        DTColumnBuilder.newColumn("strength").withTitle('Strength'),
    ];
    $scope.dtRecommendOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/recommend/detail/' + ticker,
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [0, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_TickerDetail_Recommend', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_TickerDetail_Recommend'));
            });

    // Buy ticker button
    $scope.buyItem = function (ticker) {
        var account_id = 0;
        tradeService.buyItem(account_id, ticker, $scope.tickerData.close, $scope);
    };

    $scope.getTickerData = function () {
        apiCallService.GetApiCall('/ticker?ticker=' + ticker).success(function (data) {
            $scope.tickerData = data['data'][0];
        });
    };
    $scope.getTickerData();
}

/**
 * Controller for ticker recommend page view
 * 
 * @param {type} $scope
 * @param {type} DTColumnBuilder
 * @param {type} DTOptionsBuilder
 * @param {type} DTColumnDefBuilder
 * @param {type} apiCallService
 * @param {type} tradeService
 * @param {type} config
 * @param {type} $compile
 * @returns {undefined}
 */
function tickerRecommendDatatablesCtrl($scope, DTColumnBuilder, DTOptionsBuilder, DTColumnDefBuilder, apiCallService, tradeService, config, $compile) {
    // Init for ng-model
    $scope.model = {};
    $scope.dtColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("ticker").withTitle('Ticker').withOption('render', function (tickerName) {
            return '<a type="button" class="btn btn-primary btn-xs" tooltip-placement="top" tooltip="Chart View" href="#/ticker/chart/' + tickerName + '"><i class="fa fa-bar-chart-o"></i></a>'
                    + '<a type="button" class="btn btn-primary btn-xs m-l-xs" tooltip-placement="top" tooltip="Quote" href="#/ticker/quote/' + tickerName + '"><i class="fa fa-exchange"></i></a>'
                    + '<a class="m-l-sm" href="#/ticker/show/' + tickerName + '/recommend">' + tickerName + '</a>';
        }), //tooltip-placement="top" tooltip="Chart View"
        DTColumnBuilder.newColumn("avg_volume_20").withTitle('Avg Volume 20').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("price").withTitle('Close Price'),
        DTColumnBuilder.newColumn("net_buy").withTitle('Net Buy').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("net_buy_value").withTitle('Net Buy Value').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("indicator").withTitle('Indicator'),
        DTColumnBuilder.newColumn("signal").withTitle('Signal'),
        DTColumnBuilder.newColumn("strength").withTitle('Strength'),
        DTColumnBuilder.newColumn("ticker").withTitle('Action')
    ];
    $scope.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.reloadTable();
                    }
                }])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/recommend',
                data: $scope.model,
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [2, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_Ticker_Recommend', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Ticker_Recommend'));
            })
            .withOption('drawCallback', function (settings) {
                $compile(angular.element('#' + settings.sTableId).contents())($scope); // bind angular after draw table complete
            });
    $scope.dtColumnDefs = [DTColumnDefBuilder.newColumnDef(9).withOption('render', function (data, type, full) {
            return '<button type="button" class="btn btn-primary btn-xs" ng-click=buyItem("' + full.ticker + "-" + full.price + '")><i class="fa fa-shopping-cart"></i> Buy</button>';
        })];
    $scope.dtInstance = {};
    $scope.reloadTable = function () {
        $scope.dtInstance.reloadData(null, false);
    };
    $scope.dateList = {};
    $scope.getDateList = function () {
        apiCallService.GetApiCall('/ticker/recommend/date').success(function (data) {
            $scope.dateList = data['data'];
        });
    };
    $scope.getDateList();

    // Buy ticker button
    $scope.buyItem = function (data) {
        var account_id = 0;
        var ticker = data.split("-")[0];
        var price = Number(data.split("-")[1]);
        tradeService.buyItem(account_id, ticker, price, $scope);
    };
}

/**
 * Controller for ticker quote page view
 * 
 * @param {type} $scope
 * @param {type} DTColumnBuilder
 * @param {type} DTOptionsBuilder
 * @param {type} DTColumnDefBuilder
 * @param {type} apiCallService
 * @param {type} tradeService
 * @param {type} config
 * @param {type} $compile
 * @returns {undefined}
 */
function tickerQuoteDatatablesCtrl($scope, DTColumnBuilder, DTOptionsBuilder, DTColumnDefBuilder, apiCallService, tradeService, config, $compile, $stateParams) {
    var ticker = $stateParams.ticker;

    // Init for ng-model
    $scope.model = {};
    $scope.model.ticker = {'ticker': ticker === '' ? '^VNINDEX' : ticker};

    $scope.dateList = {};
    $scope.tickerList = {};
    $scope.getDataList = function () {
        apiCallService.GetApiCall('/ticker/quote/list').success(function (data) {
            $scope.dateList = data['data']['date'];
            $scope.tickerList = data['data']['ticker'];
        });
    };
    $scope.getDataList();

    $scope.dtColumns = [
        DTColumnBuilder.newColumn("date").withTitle('Date'),
        DTColumnBuilder.newColumn("hour").withTitle('Hour'),
        DTColumnBuilder.newColumn("ticker").withTitle('Ticker').withOption('render', function (tickerName) {
            return '<a type="button" class="btn btn-primary btn-xs" tooltip-placement="top" tooltip="Chart View" href="#/ticker/chart/' + tickerName + '"><i class="fa fa-bar-chart-o"></i></a>'
                    + '<a class="m-l-sm" href="#/ticker/show/' + tickerName + '/recommend">' + tickerName + '</a>';
        }), //tooltip-placement="top" tooltip="Chart View"
        DTColumnBuilder.newColumn("total_volume").withTitle('Total Volume').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("volume").withTitle('Volume').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("bid").withTitle('Bid'),
        DTColumnBuilder.newColumn("price").withTitle('Price'),
        DTColumnBuilder.newColumn("ask").withTitle('Ask'),
        DTColumnBuilder.newColumn("value").withTitle('Value').withOption('render', function (data) {
            data = Math.round(data);
            while (/(\d+)(\d{3})/.test(data.toString())) {
                data = data.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
            }
            return data;
        }),
        DTColumnBuilder.newColumn("status").withTitle('Status'),
        DTColumnBuilder.newColumn("ticker").withTitle('Action')
    ];
    $scope.dtOptions = DTOptionsBuilder.newOptions()
            .withDOM('<"html5buttons"B>lTfgitp')
            .withButtons([{
                    text: 'Refresh',
                    action: function () {
                        $scope.reloadTable();
                    }
                }])
            .withOption('searchDelay', 1000)
            .withOption('responsive', true)
            .withOption('autoWidth', false)
            .withOption('ajax', {
                dataSrc: function (json) {
                    return json.data;
                },
                url: config.apiUrl + '/ticker/quote',
                data: $scope.model,
                type: "GET"
            })
            .withOption('processing', true) //for show progress bar
            .withOption('serverSide', true) // for server side processing
            .withPaginationType('full_numbers') // for get full pagination options // first / last / prev / next and page numbers
            .withDisplayLength(10) // Page size
            .withOption('aaSorting', [1, 'desc'])
            .withOption('stateSave', true) // save table state
            .withOption('stateSaveCallback', function (settings, data) {
                localStorage.setItem('DataTables_Ticker_Quote', JSON.stringify(data));
            })
            .withOption('stateLoadCallback', function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Ticker_Quote'));
            })
            .withOption('drawCallback', function (settings) {
                $compile(angular.element('#' + settings.sTableId).contents())($scope); // bind angular after draw table complete
            });
    $scope.dtColumnDefs = [DTColumnDefBuilder.newColumnDef(10).withOption('render', function (data, type, full) {
            return '<button type="button" class="btn btn-primary btn-xs" ng-click=buyItem("' + full.ticker + "-" + full.price + '")><i class="fa fa-shopping-cart"></i> Buy</button>';
        })];
    $scope.dtInstance = {};
    $scope.reloadTable = function () {
        $scope.dtInstance.reloadData(null, false);
    };

    // Buy ticker button
    $scope.buyItem = function (data) {
        var account_id = 0;
        var ticker = data.split("-")[0];
        var price = Number(data.split("-")[1]);
        tradeService.buyItem(account_id, ticker, price, $scope);
    };
}


angular
        .module('lumine')
        .controller('tickerListDatatablesCtrl', tickerListDatatablesCtrl)
        .controller('tickerDataDatablesCtrl', tickerDataDatablesCtrl)
        .controller('tickerRecommendDatatablesCtrl', tickerRecommendDatatablesCtrl)
        .controller('tickerQuoteDatatablesCtrl', tickerQuoteDatatablesCtrl);