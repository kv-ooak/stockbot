/**
 * Controller for chart page view
 * 
 * @param {type} $scope
 * @param {type} config
 * @param {type} apiCallService
 * @param {type} $stateParams
 * @returns {undefined}
 */
function chartCtrl($scope, config, apiCallService, $stateParams) {
    // init chart view
    var init = function (_ticker) {
        $scope.widget = new TradingView.widget({
            fullscreen: true,
            symbol: _ticker === '' ? '^VNINDEX' : _ticker,
            interval: 'D',
            container_id: "tv_chart_container",
            datafeed: new Datafeeds.UDFCompatibleDatafeed(config.apiUrl + '/chart', 60 * 1000),
            library_path: "charting_library/",
            locale: "en",
            drawings_access: {type: 'black', tools: [{name: "Regression Trend"}]},
            disabled_features: ["use_localstorage_for_settings"],
            enabled_features: [], //["study_templates"],
            //charts_storage_url: 'http://saveload.tradingview.com',
            //charts_storage_api_version: "1.1",
            //client_id: 'tradingview.com',
            //user_id: 'public_user',
            charts_storage_url: config.apiUrl,
            charts_storage_api_version: "chart",
            client_id: $scope.client_id,
            user_id: $scope.user_id,
            overrides: {
                "paneProperties.background": "#222222",
                "paneProperties.vertGridProperties.color": "#454545",
                "paneProperties.horzGridProperties.color": "#454545",
                "symbolWatermarkProperties.transparency": 90,
                "scalesProperties.textColor": "#AAA"
            }
        });
    };

    // Get some setting data from server
    apiCallService.GetApiCall('/chart/init').success(function (data) {
        $scope.client_id = data['client_id'];
        $scope.user_id = data['user_id'];
        var ticker = $stateParams.ticker;
        init(ticker);
    });
}

angular
        .module('lumine')
        .controller('chartCtrl', chartCtrl);
