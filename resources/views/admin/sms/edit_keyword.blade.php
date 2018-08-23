@extends('layouts.app')

@section('content')
    <h3 class="page-title">Từ khóa</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.keywords.update', $keyword->id]]) !!}
    <input name="_method" type="hidden" value="PATCH">
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('Thêm mới')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('keyword', trans('Từ khóa').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('keyword', $keyword->keyword, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('keyword'))
                        <p class="help-block">
                            {{ $errors->first('keyword') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('content', trans('Nội dung trả về').'*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('content', $keyword->content, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('content'))
                        <p class="help-block">
                            {{ $errors->first('content') }}
                        </p>
                    @endif
                </div>
            </div>
           
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('active', trans('Trạng thái').'*', ['class' => 'control-label']) !!}
                    <br/>
                    
                    Active {!! Form::radio('active', 1, 'checked', ['class' => ""]) !!}
                    UnActive {!! Form::radio('active', 0, '', ['class' => ""]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('active'))
                        <p class="help-block">
                            {{ $errors->first('active') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

