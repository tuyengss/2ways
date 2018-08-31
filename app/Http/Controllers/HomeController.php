<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Http\Requests;
use App\Income;
use App\Services\WidgetsInfoService;
use App\Services\WidgetsGraphsService;
use Illuminate\Http\Request;
use App\Mo;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }


     /**
     * Show the application dashboard.
     *
     * @param WidgetsGraphsService $widgetsGraphsService
     * @param WidgetsInfoService $widgetsInfoService
     * @return \Illuminate\Http\Response
     */
    public function index(WidgetsGraphsService $widgetsGraphsService, WidgetsInfoService $widgetsInfoService)
    {
        $expenses = Expense::latest()->limit(10)->get();
        $incomes = Income::latest()->limit(10)->get();
        $graphs = $widgetsGraphsService->getGraphs();
        $infoData = $widgetsInfoService->getData();

        return view('home', compact('expenses', 'incomes', 'graphs', 'infoData'));
    }

    /**
     * Get MO From gateways
     * http://103.48.194.60/WebserivceFibo/MoPartner.asmx 
        STT	Tên trường	Kiểu dữ liệu	Mô tả
        1	Username	varchar(15)	Khách hàng tạo ra username
        2	Password	varchar(10)	Khách hàng tạo ra pass 
        3	PrefixId	varchar(10)	Các đầu số 8x77 6x89
        4	Phonenumber	varchar(160)	Số điện thoại khách hàng nhắn tin lên đầu số
        5	MsgContent	decimal(10)	Nội dung khách hàng nhắn tin lên đầu số
        6	RequestId		Tạo ra ID để fibo gọi sang

        - Giá trị trả về: -1: thất bại, 1: thành công

     * @param $request
     * @return boolean
     */
    static function moGateWay(Request $request){
        //MO recieved    
        $params = $request->all();
        // set post fields
        $post = [
            'Username' => $params['Username'],
            'Password' => $params['Password'],
            'PrefixId'   => $params['PrefixId'],
            'Phonenumber' => $params['Phonenumber'],
            'MsgContent' => $params['MsgContent'],
            'RequestId' => $params['RequestId']
        ];

        //valid MsgContent
        $patent = '/Fibo DK VIP/i';

        if(preg_match($patent, $params['MsgContent'])){
            $message = 'Cam on ban da tham gia goi sms VIP cua Fibo';
        }else{
            $message = 'Cu phap sai, vui long xem lai cu phap';
        }

        $data = array(
            'Username' => $post['Username'],
            'Phonenumber' => $post['Phonenumber'],
            'MsgContent' => $post['MsgContent'],
            'status' => 1,
            'RequestId' => $post['RequestId'],
        );

        //save to logs
        $logs = Mo::create($data);

        return json_encode(array(
            1 => $message
        ));
    }

    /**
     * http://103.48.194.60/WebserivceFibo/MtReceiver.asmx

        •		Đặc tả: Hàm ReceiveMtReplyMo của Web service gồm các trường sau:
        STT	Tên trường	Kiểu dữ liệu	Mô tả
        1	Username	varchar(20)	Bên FIBO sẽ cung cấp cho đối tác một tài khoản để gửi tin.

        2	Password	varchar(20)	Mật khẩu tài khoản để gửi tin.
        3	PhoneNumber
            varchar(15)	Số di động gửi đến (Theo chuẩn quốc tế, bắt đầu bằng 84, không phải là 09xxx hay 01xxx)
        4	RequestId	varchar(1000)	RequestId là số id của MO tin nhắn khách hàng gọi sang fibo
        5	PrefixId	varchar(10)	Số dịch vụ (Các số mà FIBO đang sở hữu: 6x89, 8x77, …).
        6	CommandCode	varchar(10)	Mã của dịch vụ,  mã này sẽ phục vụ cho việc thống kê và quản lý MT phát sinh. Ví dụ : GO, SC …
        7	MoId	decimal(10)	Để mặc định MOID=0
        8	MsgContent	number(1)	Nội dung phản hồi về cho Khách hàn
        9	MsgContentTypeId	number(2)	Để mặc định bằng 1
        10	FeeTypeId	number(2)	Để mặc định bằng 1

     */
    static function sendMt($params){
        // set post fields
        $post = [
            'Username' => 'CL1808210001',
            'Password' => 'fnUEURJNm4YEh53A',
            'PrefixId'   => 6020,
            'Phonenumber' => str_replace(['0'], '84' , $params['Phonenumber']),
            'MsgContent' => 'Chung toi da nhan duoc thong tin, cam on ban',
            'RequestId' => $params['RequestId'],
            'CommandCode' => 'FIBO',
            'MoId' => 0,
            'MsgContentTypeId' => 1,
            'FeeTypeId' => 1
        ];

	 var_dump($post); 

        $ch = curl_init('http://103.48.194.60/WebserivceFibo/MtReceiver.asmx?wsdl');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);

        return $response;
    }
}
