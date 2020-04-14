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
            <th>Open</th>
            <th>Close</th>
            <th>High</th>
            <th>Low</th>
            <th>Volume</th>
            </thead>

            <tbody>
                @foreach ($tickerData as $data)
                <tr>
                    <td class="table-text">
                        <div>{{ $data->date }}</div>
                    </td>
                    <td><div>{{ $data->open }}</div></td>
                    <td><div>{{ $data->close }}</div></td>
                    <td><div>{{ $data->high }}</div></td>
                    <td><div>{{ $data->low }}</div></td>
                    <td><div>{{ $data->volume }}</div></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection