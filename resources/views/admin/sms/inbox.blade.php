@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">Danh sách tin gửi đi</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            Logs
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($sms) > 0 ? 'datatable' : '' }} @can('user_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>Người gửi</th>
                        <th>Người nhận</th>
                        <th>Nội dung tin nhắn</th>
                        <th>Trạng thái</th>
                        <th>Thời gian thực hiện</th></th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($sms) > 0)
                        @foreach ($sms as $item)
                            <tr data-entry-id="{{ $item->id }}">
                                @can('user_delete')
                                    <td></td>
                                @endcan

                                <td field-key='sender'>{{ $item->sender }}</td>
                                <td field-key='reciever'>{{ $item->reciever }}</td>
                                <td field-key='content'>{{ $item->content }}</td>
                                <td field-key='status'><a class="btn btn-xs {{ $item->status == 1 || $item->status == 200 ? 'btn-success':'btn-warning' }} ">{{ $errCode[$item->status] }}</a></td>
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

@section('javascript') 
    <script>
        @can('user_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}';
        @endcan

    </script>
@endsection