<?php

namespace App\Http\Controllers;

use App\Name;
use App\Srcdata;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;


class WxAuthController extends Controller
{
    public function test($a)
    {
//        header("Content-type: application/json");
    }

    //获取微信返回的code
    public function code($code)
    {
        $code = trim($code);
//        $randomKey = str_random(40);
//        echo $randomKey;
//        return;
        //GuzzleHttpClient
//        $client = new Client();
//        $res = $client->request('GET', 'https://api.weixin.qq.com/sns/jscode2session?appid=wx88531a640863d920&secret=ec872f08c59c8867034382cdef140162&grant_type=authorization_code&js_code=' . $code);

        $res = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid=wxb091e8f360880da0&secret=55aaa2623df3c2d97a2e027c51af1224&grant_type=authorization_code&js_code='.$code);
        $data = json_decode($res);

        if (empty($data->errcode)) {
//            $randomKey = str_random(40);
//            Cache::forever($randomKey, $data->session_key . '_' . $data->openid);
            return ['success' => true, 'data' => $data->openid];
        } else {
            echo $data->errmsg;
        }
    }

    //注册
    public function wxRegister(Request $request)
    {
//        $sessionKey = $request->input('sessionKey');
//        $nickName = $request->input('nickName');
//        $avatarUrl = $request->input('avatarUrl');
//        $gender = $request->input('gender');
//        $province = $request->input('province');
//        $city = $request->input('city');
//        $country = $request->input('country');
        $firstname = $request->input('firstname');

        if ($this->ischinese($firstname)) {
            $ndata = [
                'sessionKey' => $request->input('sessionKey'),
                'nickName' => $str = mb_convert_encoding($request->input('nickName'), 'UTF-8'),
                'avatarUrl' => $request->input('avatarUrl'),
                'gender' => $request->input('gender'),
                'province' => $request->input('province'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'firstName' => $request->input('firstname'),
            ];

            $user = User::create($ndata);

            if ($user) {
                return  ['success' => true, 'data' => User::find($user->id)->toArray()];
            } else {

                return ['success' => false, 'data' => '注册失败,请联系管理员!'];

            }
        } else {
            return ['success' => false, 'data' => '请输入汉字!'];
        }


    }


    //获取登录后微信信息
    public function wxInfo(Request $request)
    {

        $sessionKey = $request->input('sessionKey');

        $user = User::where('sessionKey', '=', $sessionKey)->firstOrFail();

        if (!empty($user)) {
            return  ['success' => true, 'data' => $user->toArray()];
        } else {
            return ['success' => false, 'data' => '没有此用户!'];
        }

    }

    public function ischinese($s)
    {

        $allcn = preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $s);  //判断是否是中文
        if ($allcn) {
            return true;
        } else {
            return false;
        }

    }


}
