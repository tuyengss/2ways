@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">

             

            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/') }}">
                    <i class="fa fa-list"></i>
                    <span class="title">THỐNG KÊ</span>
                </a>
            </li>
            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/send_sms') }}">
                    <i class="fa fa-send"></i>
                    <span class="title">GỬI TIN NHẮN</span>
                </a>
            </li>
            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/inbox_send') }}">
                    <i class="fa fa-inbox"></i>
                    <span class="title">TIN NHẮN GỬI ĐI </span>
                </a>
            </li>
            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/inbox_come') }}">
                    <i class="fa fa-inbox"></i>
                    <span class="title">TIN NHẮN ĐẾN </span>
                </a>
            </li>

            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/keyword') }}">
                    <i class="fa fa-inbox"></i>
                    <span class="title">TỪ KHÓA </span>
                </a>
            </li>

            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <i class="fa fa-key"></i>
                    <span class="title">THAY ĐỔI TÀI KHOẢN</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="fa fa-arrow-left"></i>
                    <span class="title">ĐĂNG XUẤT</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

