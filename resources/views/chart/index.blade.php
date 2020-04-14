@extends('layouts.app')

@section('head')
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="{{ URL::asset('charting_library/charting_library.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('charting_library/datafeed/udf/datafeed.js') }}"></script>

<script type="text/javascript">
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

TradingView.onready(function ()
{
    var widget = new TradingView.widget({
        fullscreen: true,
        symbol: '',
        interval: 'D',
        container_id: "tv_chart_container",
        //	BEWARE: no trailing slash is expected in feed URL
        //datafeed: new Datafeeds.UDFCompatibleDatafeed("http://demo_feed.tradingview.com"),
        datafeed: new Datafeeds.UDFCompatibleDatafeed('{{ URL::asset('') }}', 60 * 1000),
        library_path: "charting_library/",
        locale: getParameterByName('lang') || "en",
        //	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
        drawings_access: {type: 'black', tools: [{name: "Regression Trend"}]},
        disabled_features: ["use_localstorage_for_settings"],
        enabled_features: ["study_templates"],
        charts_storage_url: 'http://saveload.tradingview.com',
        charts_storage_api_version: "1.1",
        client_id: 'tradingview.com',
        user_id: 'public_user'
    });
});

</script>
@endsection

@section('content')
<div id="tv_chart_container"></div>
@endsection