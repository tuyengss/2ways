@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Thông tin trả về</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            Message
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($errCode) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>Number</th>
                        <th>Nội dung</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($errCode) > 0)
                        @foreach ($errCode as $key => $item)
                            <tr>
                                @can('user_delete')
                                    <td></td>
                                @endcan

                                <td field-key='sender'>{{ $key }}</td>
                                <td field-key='status'>{{ $item }}</td>

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
