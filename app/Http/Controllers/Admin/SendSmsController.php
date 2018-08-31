<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Hash;
use Validator;
use App\Sms;
use Config;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Excel;
use Illuminate\Support\Facades\URL;
use App\Mo;
use App\keyword;

class SendSmsController extends Controller
{
    
    /**
     * Where to redirect users after password is changed.
     *
     * @var string $redirectTo
     */
    protected $redirectTo = '/inbox';
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function errCodes()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $errCode = Config::get('constants.ErrCode');
        
        return view('admin.sms.errors', compact('errCode'));
    }

    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $user = Auth::getUser();
        
        return view('admin.sms.index', compact('user'));
    }

     /**
     * Display a listing of MT.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllSms()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $sms = Sms::all()->sortByDesc('id');
        //get errCode
        $errCode = Config::get('constants.ErrCode');

        return view('admin.sms.send', compact('sms','errCode'));
    }

     /**
     * Display a listing of MT.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllGateWay()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $sms = Mo::all()->where('type',0)->sortByDesc('id');

        return view('admin.sms.gateway', compact('sms'));
    }


     /**
     * Display a listing of message MO.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllSmsCome()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

	    $sms = Mo::all()->where('type',1)->sortByDesc('id');

        return view('admin.sms.gateway', compact('sms'));
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
    public function moGateWay(Request $request){
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
        $keywords = keyword::all()->toArray();
	
        if($keywords){
            foreach($keywords as $keyword){
                if(preg_match('/'.strtolower($keyword['keyword']).'/', $params['MsgContent'])){
                    //send sms
            	    $status = $this->invoke_sms(
                        array(
                            'phone'=> str_replace('84', '0', $params['Phonenumber']),
                            'content' => $params['MsgContent']
                        ), $request
                    );		        
                }else{
		            //not fit		
                }
            }
        }

        $data = array(
            'Username' => $post['Username'],
            'Phonenumber' => $post['Phonenumber'],
            'MsgContent' => $post['MsgContent'],
            'status' => 1,
            'type' => 1, //2ways 0//getway
            'RequestId' => $post['RequestId'],
        );

        //save to logs
        $logs = Mo::create($data);

        return true;
    }

    /**
     * Change password.
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function sendSms(Request $request)
    {
        $this->validator($request->all())->validate();
        $params = $request->all();
        
        $message = $this->invoke_sms($params, $request);
        return view('admin.sms.index', array('message' => $message, 'data' => $params));
    }

    /**
     * invoke sms
     * @params 
     * @return 
     */
    public function invoke_sms($params, $request){
         //get errCode
        $errCode = Config::get('constants.ErrCode');
        $excel = $this->importFile($request);
        if($excel){
            $lists = $excel;  
            $flag = true;          
        }else{
            $lists = array('only' => $params['phone']);
            $flag = false;
        }
        
        if($params){    
            $service = "http://center.fibosms.com/service.asmx/SendMaskedSMS?";

            if($lists){
                foreach($lists as $key => $item){
                    $query = array(
                        'clientNo' => 'CL1808210001', //string
                        'clientPass' => 'fnUEURJNm4YEh53A', // string
                        'senderName' => '0901800073', //string
                        'phoneNumber' => ($flag == true) ? "0".$item['phone']: $item,
                        'smsMessage' => $params['content'],
                        'smsGUID' => 0,
                        'serviceType' => 0
                    );
        
                    $builder = "";
                    $i = 0;
                    foreach($query as $key => $param){
                        if($i == 0) $builder .= $key."=".$param;
                        else $builder .= "&".$key."=".$param;
                        $i++;
                    }
        
                    $url = $service.$builder;
                    $status = $this->send_multi($url, $query, $params);
                }
            }
            
        }

        return $errCode[$status];
    }

   /**
     * Send multi sms
     * @param $url
     * @return $array
     */
    public function send_multi($url, $query, $params){
        $array = array();
        $client = new \GuzzleHttp\Client;
        $res = $client->request('GET', $url);
        $result = $res->getBody();

        $xml = $this->XMLtoArray($result);
        $array = $xml['STRING']['content'];
        $status = preg_replace('/[^0-9]/', '', $array);
        
        if($status == 1 || $status == 200){
            //save logs
            $data = array(
                'sender' => $query['senderName'],
                'reciever' => $query['phoneNumber'],
                'content' => $params['content'],
                'status' => $status
            );

            Sms::create($data);
        }

        return $status;
    }

    /**

     * Create a new controller instance.

     *

     * @return void

     */
    public function importFile($request){

        if($request->hasFile('sample_file')){

            $path = $request->file('sample_file')->getRealPath();

            $data = \Excel::load($path)->get();

            if($data->count()){
                
                foreach ($data as $key => $value) {

                    $arr[] = ['phone' => $value->so_dien_thoai];
                }

                if(!empty($arr)){
                    return $arr;
                }

            }

        }
    } 

    /**
     * Dowload excel file
     * @return $file
     */
    public function downloadFile(){
        $file = 'Maudulieu.xls';
        $file = public_path('upload/'.$file);

        if(!file_exists($file)){ // file does not exist
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$file");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");

            // read the file from disk
            readfile($file);
            return $file;
        }
    }

    /**
     * parse xml.
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function XMLtoArray($XML)
    {
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $XML, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp='';
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_level!=1 && $x_type == 'close') {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level]=1;
                else
                    $multi_key[$x_tag][$x_level]=0;
            }
            if ($x_level!=1 && $x_type == 'complete') {
                if ($_tmp==$x_tag)
                    $multi_key[$x_tag][$x_level]=1;
                $_tmp=$x_tag;
            }
        }
        // jedziemy po tablicy
        foreach ($vals as $xml_elem) {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level) {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                $start_level++;
            }
            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level]=0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
                if ($x_type == 'open')
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes', $xml_elem)) {
                if (isset($xml_elem['value'])) {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value) {
                    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }
        return $xml_array;
    }

    /**
     * Get a validator for an incoming change password request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'phone' => 'required',
            'content' => 'required|min:6',
        ]);
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Sms::whereIn('id', $request->input('ids'))->get();  

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
