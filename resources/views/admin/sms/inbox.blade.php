@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Danh sách tin nhận</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            Logs
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped >
                <thead>
                    <tr>
                        <th></th>
                        @can('user_delete')
                            <th style="text-align:center;">Stt</th>
                        @endcan

                        <th>Người gửi</th>
                        <th>Người nhận</th>
                        <th>Nội dung tin nhắn</th>
                        <th>Trạng thái</th>
                        <th>Loại tin nhắn</th>
                        <th>Thời gian thực hiện</th></th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (($sms))

                        @foreach ($sms as  $key => $item)
                            <tr>
                                <td></td>
                                <td field-key='stt'>{{ $key + 1 }}</td>
                                <td field-key='sender'>{{ $item->From }}</td>
                                <td field-key='reciever'>{{ $item->To }}</td>
                                <td field-key='content'>{{ htmlspecialchars($item->MsgContent) }}</td>
                                <td field-key='status'>{{ $item->Type }}</td>
                                <td field-key='status'>{{ $item->StatusName }}</td>
                                <td field-key='status'>{{ $item->Time }}</td>

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
