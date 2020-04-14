@extends('layouts.app')

@section('content')
@if(isset($message))
<div class="alert alert-dismissible alert-success">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{ $message }} 
</div>
@endif
@if(isset($job_message))
<div class="alert alert-dismissible alert-success">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{ $job_message }} 
</div>
@endif
<div class="container col-md-8 col-md-offset-2">
    <div class="well well bs-component">
        <form action="{{ url('file/add') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <fieldset>


                <input type="file" class="form-control" id="txt" name="filefield">
                {{ Form::select('upload_file_type', $upload_file_type) }}
                <button type="submit" class="btn btn-primary">Upload File</button>

            </fieldset>
        </form>
        <div class="row">
            <ul>
                @foreach($files as $file)
                <li>{{$file->original_filename . " - Type: " . $import_action_list[$file->data_type]}}
                    <form action="{{ url('file/delete/'.$file->filename) }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="container col-md-8 col-md-offset-2">
    <div class="well well bs-component">
        <fieldset>
            <legend>0. Reset Data</legend>
            <form action="{{ url('/manager/clear/1') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Clear Ticker</button>
                    </div>
                </div>
            </form>
            <form action="{{ url('/manager/clear/2') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Clear Ticker Data</button>
                    </div>
                </div>
            </form>
            <form action="{{ url('/manager/clear/3') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Clear Ticker Quote</button>
                    </div>
                </div>
            </form>
            <form action="{{ url('/manager/clear/4') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Clear Ticker Bot</button>
                    </div>
                </div>
            </form>
            <form action="{{ url('/manager/clear/5') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Clear Ticker Recommend</button>
                    </div>
                </div>
            </form>
        </fieldset>


        <fieldset>
            <legend>1. Import data from csv to database</legend>

            <form action="{{ url('/manager/import/') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                {{ Form::select('file_list', $file_list) }}
                {{ Form::select('import_action_list', $import_action_list) }}
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary" value="ticker">Import</button>
                    </div>
                </div>
            </form>
        </fieldset>

        <fieldset>
            <legend>2. Calculate</legend>
            <form action="{{ url('/manager/calculate/bot') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Bot</button>
                    </div>
                </div>
            </form>
            <form action="{{ url('/manager/calculate/recommend') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-0   ">
                        <button type="submit" class="btn btn-primary">Recommend</button>
                    </div>
                </div>
            </form>
        </fieldset>
    </div>
</div>
@endsection