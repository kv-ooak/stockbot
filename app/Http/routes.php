<?php

// app/routes.php
// HOME PAGE ===================================  
// we dont need to use Laravel Blade 
// we will return a PHP file that will hold all of our Angular content
// see the "Where to Place Angular Files" below to see ideas on how to structure your app return  
Route::get('/', function() {
    return redirect('index.html');
});

// Debugbar
Route::get('debug', 'PageController@debug');

// API ROUTES ==================================  
Route::group(array('prefix' => 'api'), function() {
    // Angular - Chart view
    Route::get('chart/init', 'TickerChartController@init');
    Route::get('chart/config', 'TickerChartController@getConfig');
    Route::get('chart/history', 'TickerChartController@getHistory');
    Route::get('chart/time', 'TickerChartController@getTime');
    Route::get('chart/symbols', 'TickerChartController@getSymbols');
    Route::get('chart/search', 'TickerChartController@getSearch');

    // Angular - Chart template
    Route::get('chart/charts', 'TickerChartTemplateController@getCharts');
    Route::post('chart/charts', 'TickerChartTemplateController@postCharts');
    Route::delete('chart/charts', 'TickerChartTemplateController@deleteCharts');
    Route::options('chart/charts', 'TickerChartTemplateController@optionsCharts');

    // TODO
    // Get group symbol info. Ex: symbol_info?group=NYSE
    Route::get('chart/symbol_info', 'TickerChartController@getSymbolInfo');
    // Mark. GET /marks?symbol=<ticker_name>&from=<unix_timestamp>&to=<unix_timestamp>&resolution=<resolution>
    Route::get('chart/marks', 'TickerChartController@getMarks');
    // Quotes. GET /quotes?symbols=<ticker_name_1>,<ticker_name_2>,...,<ticker_name_n>
    Route::get('chart/quotes', 'TickerChartController@getQuotes');

    // Angular - Ticker view
    Route::get('ticker', 'TickerController@getTicker');
    Route::get('ticker/list', 'TickerController@getList');
    Route::get('ticker/data/{ticker}', 'TickerController@getData');
    Route::get('ticker/bot/{ticker}', 'TickerController@getBot');
    Route::get('ticker/recommend', 'TickerController@getRecommend');
    Route::get('ticker/recommend/date', 'TickerController@getRecommendDateList');
    Route::get('ticker/recommend/detail/{ticker}', 'TickerController@getRecommendDetail');
    Route::get('ticker/quote', 'TickerController@getQuote');
    Route::get('ticker/quote/list', 'TickerController@getQuoteList');

    // Angular - Admin view
    Route::get('admin', 'AdminController@index');
    Route::get('admin/job', 'AdminController@getJobList');
    Route::delete('admin/job/{job}', 'AdminController@deleteJob');
    Route::post('admin/upload', 'AdminController@postUploadFile');
    Route::post('admin/delete/{filename}', 'AdminController@postDeleteFile');
    Route::post('admin/import/{filename}/{truncate}', 'AdminController@importDataFromFile');
    Route::post('admin/clear/table/{action}', 'AdminController@clearData');
    Route::post('admin/clear/cache', 'AdminController@clearCache');
    Route::post('admin/calculate/{action}/{date}', 'AdminController@postCalculate');

    // Angular - Log view
    Route::get('log/queue', 'LogController@getQueueLog');
    Route::get('log/action', 'LogController@getActionLog');

    // Angular - Authenticate
    Route::post('auth/login', 'AuthenticateController@login');
    Route::post('auth/logout', 'AuthenticateController@logout');
    Route::post('auth/status', 'AuthenticateController@status');
    Route::get('auth/token', 'AuthenticateController@token');

    // Angular - Main view
    Route::get('index', 'PageController@index');

    // Angular - User account
    Route::get('account', 'UserAccountController@getAccount');
    Route::post('account', 'UserAccountController@postAccount');
    Route::delete('account', 'UserAccountController@deleteAccount');

    // Angular - User trade manager
    Route::get('account/summary', 'UserAccountTradeController@getAccountSummary');
    Route::post('account/fund', 'UserAccountTradeController@addFund');
    Route::get('account/buy', 'UserAccountTradeController@buyItemForm');
    Route::post('account/buy', 'UserAccountTradeController@buyItem');
    Route::get('account/sell', 'UserAccountTradeController@sellItemForm');
    Route::post('account/sell', 'UserAccountTradeController@sellItem');
});
