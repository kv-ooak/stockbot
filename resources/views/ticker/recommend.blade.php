@extends('layouts.app')

@section('content')
<div class="container col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2> Recommendation </h2>
            <h5><i> Date: {{ $lastestDate}} </i></h5>
            <table id="table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No. </th>
                        <th>Ticker</th>
                        <th>Avg.20 Volume</th>
                        <th>Net Buy</th>
                        <th>Indicator</th>               
                        <th>Signal</th>
                        <th>Strength</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recommends as $ticker => $recommend)
                    @if (array_values($recommend)[0]['avg_volume_20'] > 20000)
                    <tr>
                        <td>{{ $countNumber++ }}</td>                               	
                        <td><a href="{{ url('/ticker/recommend/'.$ticker) }}"><font color="333333">{{ $ticker }}</font></a></td>
                        <td>
                            {{ number_format($recommend[0]['avg_volume_20']) }}
                        </td>

                        <td>
                            {{ $recommend[0]['net_buy'] }}
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
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection