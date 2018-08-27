@extends('layouts.app')

@section('content')

	<h3 class="page-title">FORM GỬI TIN NHẮN</h3>
	@if(@$message)
	<div class="row">
		<div class="alert alert-success">
			{{ $message or "" }}

			@if(preg_match("/^Send MT Error/", $message))
				{{ '' }}
			@else
				{{ '' }}
			@endif
		</div>
	</div>
	@endif
	@if(session('success'))
		<!-- If password successfully show message -->
		<div class="row">
			<div class="alert alert-success">
				{{ session('success') }}
			</div>
		</div>
	@else
		{!! Form::open(['method' => 'PATCH', 'route' => ['admin.send_sms'], 'files'=>'true']) !!}
		<!-- If no success message in flash session show change password form  -->
		
		<div class="col-md-6">
			<div class="panel panel-default">
			
				<div class="panel-heading">
					FIBO SMS API
				</div>

				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 form-group">
							{!! Form::label('phone', 'Số điện thoại gửi tới', ['class' => 'control-label']) !!}
							{!! Form::text('phone', @$data['phone'] ,array('class' => 'form-control', 'placeholder' => '0966882422')) !!}
							<p class="help-block"></p>
							@if($errors->has('phone'))
								<p class="help-block">
									{{ $errors->first('phone') }}
								</p>
							@endif
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 form-group">
							{!! Form::label('sample_file','Import Excel file:') !!}
							{!! Form::file('sample_file', array('class' => 'form-control')) !!}
		
							{!! $errors->first('sample_file', '<p class="alert alert-danger">:message</p>') !!}
							<br/>
							<a class="btn btn-xs bg-green" href="{{ url('/download-file') }}">Mẫu Excel download</a>
							</div>
					</div>

					<div class="row">
						<div class="col-xs-12 form-group">
							{!! Form::label('content', 'Nội dung SMS(*)', ['class' => 'control-label']) !!}
							<span style="color:red">Lưu ý: Nội dung tin nhắn phải có nghĩa, tuyệt đối không có chữ “test” hoặc “kiểm tra”.</span>
							{!! Form::textarea ('content', @$data['content'] ,['class' => 'form-control', 'placeholder' => '%xac nhan so nguoi se tham gia% |  PNS%moi ban den phong van%']) !!}
							<p class="help-block"></p>
							@if($errors->has('content'))
								<p class="help-block">
									{{ $errors->first('content') }}
								</p>
							@endif
						</div>
					</div>

				</div>
			</div>
			{!! Form::submit("Gửi tin nhắn", ['class' => 'btn btn-danger']) !!}
			{!! Form::close() !!}


		</div>

		<div class="col-md-6">
			<div class="panel panel-default">
			
				<div class="panel-heading">
					FIBO SMS API
				</div>
				
				<div class="panel-body">
					<p><b>Url webservice</b>: http://center.fibosms.com/service.asmx/SendMaskedSMS?clientNo=string&clientPass=string&senderNa me=string&phoneNumber=string&smsMessage=string&smsGUID=0&serviceType=0 </p>
					<P><b>Method</b>: GET</P>
					<P><b>Params</b>: <br/>
					- <b>clientNo</b>: Tài khoản đăng nhập của khách hàng ví dụ CL1609110001 <br/>
					-	<b> clientPass</b>: Mật khẩu gửi tin của khách hàng. Lưu ý mật khẩu này được cấp lúc đầu chính là mật khẩu đăng nhập. <br/>
					Nếu khách hàng muốn bảo mật hơn có thể đổi mật khẩu này sang mật khẩu khác. <br/>
					- <b>phoneNumber</b>: Số phone khách hàng muốn gửi tới. <br/>
					- <b>smsMessage</b>: Nội dung tin nhắn khách hàng muốn gửi. <br/>
					- <b>smsGUID</b> : 0 (mặc định). <br/>
					- <b>serviceType</b>: 0 (mặc định). <br/>
					
					</P>
				</div>
			</div>
		</div>
		
	@endif
@stop

