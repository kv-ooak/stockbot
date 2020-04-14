@extends('layouts.app')

@section('content')
<div class="container col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2> Ticker: {{ $ticker }} </h2>
            <h5><i><a href="{{ url('/ticker/recommend') }}"> <font color="333333"> &#8592; Back to previous page</font></a></i></h5>
            <table id="table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Avg.20 Volume</th>
                        <th>Net Buy</th>
                        <th>Indicator</th>               
                        <th>Signal</th>
                        <th>Strength</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recommends as $date => $recommend)
                    <tr>
                        <td>{{ $date }}</td>
                        <td>
                            {{ number_format(array_values($recommend)[0]['avg_volume_20']) }}
                        </td>

                        <td>
                            {{ array_values($recommend)[0]['net_buy'] }}
                        </td>

                        <td>
                            @foreach ($recommend as $array)
                            {{ $array['indicator'] }} </br>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($recommend as $array)
                            {{ $array['signal'] }} </br>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($recommend as $array)
                            @if ($array['strength'] == 'POS')
                            <font size = "5" color = "green"><b> {{ $array['arrow'] }} </b></font>
                            @endif
                            @if ($array['strength'] == 'NEG')
                            <font size = "5" color = "red"><b> {{ $array['arrow'] }} </b></font>
                            @endif
                            @if ($array['strength'] == 'NEU')
                            <font size = "5" color = "CCCC00"><b> {{ $array['arrow'] }} </b></font>
                            @endif
                            @endforeach 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection