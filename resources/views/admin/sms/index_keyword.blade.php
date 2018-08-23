@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Thông tin trả về</h3>
    @can('user_create')
    <p>
        <a href="{{ route('admin.create_keyword') }}" class="btn btn-success">Thêm mới</a>
    </p>
    @endcan
    <div class="panel panel-default">
        <div class="panel-heading">
            Message
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($keywords) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>keyword</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($keywords) > 0)
                        @foreach ($keywords as $key => $item)
                            <tr>
                                @can('user_delete')
                                    <td></td>
                                @endcan

                                <td field-key='keyword'>{{ $item->keyword }}</td>
                                <td field-key='content'>{{ $item->content }}</td>
                                <td field-key='active'> <a class="btn btn-xs 
                                    @if ($item->active == 1)
                                        {{ 'btn-success' }}
                                    @else
                                        {{ 'btn-warning' }}
                                    @endif
                                      ">
                                    @if ($item->active == 1)
                                        {{ 'active' }}
                                    @else
                                        {{ 'deactive' }}
                                    @endif
                                    </a>
                                </td>
                                <td>
                                    
                                    @can('user_edit')
                                    <a href="{{ route('admin.keywords.edit',[$item->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('user_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.users.destroy', $item->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop
