@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Danh sách SMS</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            Logs
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($sms) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        <th></th>
                        @can('user_delete')
                            <th style="text-align:center;">Id</th>
                        @endcan

                        <th>UserName</th>
                        <th>PhoneNumber</th>
                        <th>Nội dung tin nhắn</th>
                        <th>Trạng thái</th>
                        <th>MO/MT</th>
                        <th>Thời gian thực hiện</th></th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($sms) > 0)

                        @foreach ($sms as  $key => $item)
                            <tr>
                                <td></td>
                                <td field-key='stt'>{{ $item->id }}</td>
                                <td field-key='sender'>{{ $item->Username }}</td>
                                <td field-key='reciever'>{{ $item->Phonenumber }}</td>
                                <td field-key='content'>{{ ($item->MsgContent) }}</td>
                                <td field-key='status'><a class="btn btn-xs btn-success ">Thành công</a></td>
                                <td><a class="btn">MO</a></td>
                                <td field-key='status'>{{ $item->created_at }}</td>
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
