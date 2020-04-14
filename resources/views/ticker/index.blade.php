@extends('layouts.app')

@section('content')
@if (count($tickers) > 0)
<div class="panel panel-default">
    <div class="panel-heading">
        Current Ticker
    </div>

    <div class="panel-body">
        <table id="table" class="table table-striped task-table">

            <thead>
            <th>Ticker</th>
            <th>&nbsp;</th>
            </thead>

            <tbody>
                @foreach ($tickers as $ticker)
                <tr>
                    <td class="table-text">
                        <div>{{ $ticker->ticker }}</div>
                    </td>

                    <td>
                        <form action="{{ url('ticker/data/'.$ticker->id) }}" method="GET">
                            <button>Raw Data</button>
                        </form>
                        <form action="{{ url('ticker/bot/'.$ticker->id) }}" method="GET">
                            <button>Bot Data</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection