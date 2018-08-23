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
        $this->middleware('auth');
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

        $sms = Sms::all();
        //get errCode
        $errCode = Config::get('constants.ErrCode');

        return view('admin.sms.send', compact('sms','errCode'));
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

        $url = 'http://192.168.1.99:8888/api/v1/logs';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        
        $obj = json_decode($data);
        $sms = $obj->logs;
        
        
        return view('admin.sms.inbox', compact('sms'));
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
        $array = array();

        if($params){    
            $client = new \GuzzleHttp\Client;

            $service = "http://center.fibosms.com/service.asmx/SendMaskedSMS?";
            $query = array(
                'clientNo' => 'CL1808210001', //string
                'clientPass' => 'fnUEURJNm4YEh53A', // string
                'senderName' => '0901800073', //string
                'phoneNumber' => $params['phone'],
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
            $res = $client->request('GET', $url);
            $result = $res->getBody();

            $xml = $this->XMLtoArray($result);
            $array = $xml['STRING']['content'];
            $status = preg_replace('/[^0-9]/', '', $array);
            //get errCode
            $errCode = Config::get('constants.ErrCode');
            
            //save logs
            $data = array(
                'sender' => $query['senderName'],
                'reciever' => $params['phone'],
                'content' => $params['content'],
                'status' => $status
            );

            Sms::create($data);
        }

        return view('admin.sms.index', array('message' => $errCode[$status], 'data' => $params));
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
