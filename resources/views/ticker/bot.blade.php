@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        {{ $ticker->ticker }}
    </div>

    <div class="panel-body">
        <table id="table" class="table table-striped task-table">
            <thead>
            <th>Date</th>
            <th>avg_volume_20</th>
            <th>EMA9</th>
            <th>MA20</th>
            <th>MA42</th>
            <th>MACD</th>
            <th>MACDSignal</th>
            <th>RSI</th>
            <th>SAR</th>
            <th>UpperBB</th>
            <th>LowerBB</th>
            <th>plusDI</th>
            <th>minusDI</th>
            <th>ADX</th>
            <th>CCI</th>
            </thead>

            <tbody>
                @foreach ($tickerData as $data)
                <tr>
                    <td class="table-text">
                        <div>{{ $data->date }}</div>
                    </td>
                    <td><div>{{ $data->avg_volume_20 }}</div></td>
                    <td><div>{{ $data->EMA9 }}</div></td>
                    <td><div>{{ $data->MA20 }}</div></td>
                    <td><div>{{ $data->MA42 }}</div></td>
                    <td><div>{{ $data->MACD }}</div></td>
                    <td><div>{{ $data->MACDSignal }}</div></td>
                    <td><div>{{ $data->RSI }}</div></td>
                    <td><div>{{ $data->SAR }}</div></td>
                    <td><div>{{ $data->UpperBB }}</div></td>
                    <td><div>{{ $data->LowerBB }}</div></td>
                    <td><div>{{ $data->plusDI }}</div></td>
                    <td><div>{{ $data->minusDI }}</div></td>
                    <td><div>{{ $data->ADX }}</div></td>
                    <td><div>{{ $data->CCI }}</div></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection