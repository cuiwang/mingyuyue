<?php

namespace App\Http\Controllers;

use App\History;
use App\KXDictionary;
use App\Name;
use App\Srcdata;
use App\Store;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    public $total = 100;

    public $wuge_weight = 0.5;
    public $sancai_weight = 0.5;

    public $ji_weight = 1;
    public $xiaoji_weight = 0.75;
    public $half_weight = 0.5;
    public $xiong_weight = 0.25;

    //五格 50
    public $shuli_ji = array(1, 3, 5, 6, 7, 11, 13, 15, 16, 21, 23, 24,
        29, 31, 32, 33, 35, 37, 41, 45, 47, 48, 52, 57, 61, 63, 65, 67, 68,
        81);

    public $shuli_banji = array(8, 17, 18, 25, 30, 36, 38, 39, 49, 50,
        51, 55, 58, 71, 72, 73, 77);

    public $shuli_xiong = array(2, 4, 9, 10, 12, 14, 19, 20, 22, 26,
        27, 28, 30, 34, 36, 38, 40, 42, 43, 44, 46, 49, 50, 51, 53, 54, 56,
        58, 59, 60, 62, 64, 66, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80);

    //三才 50
    //1金 2木 3水 4火 5土
    public $sancai_ji = array(
        222, 224, 225, 242, 245, 232, 232, 231, 233,
        422, 424, 425, 442, 445, 454, 455, 451,
        542, 544, 545, 554, 555, 551, 515, 511, 513,
        154, 155, 151, 115, 132, 131,
        322, 324, 325, 323, 315, 313, 332, 331);
    public $sancai_xiaoji = array(
        223, 244, 254, 255, 251,
        423, 444, 452, 452,
        522, 524, 541, 552,
        152, 153, 111, 113, 135, 133,
        342, 354, 355, 351, 311, 333);
    public $sancai_xiong = array(
        221, 241, 243, 252, 253, 212, 213, 214, 215, 211, 213, 234, 235,
        421, 441, 443, 412, 414, 411, 413, 432, 434, 434, 431, 433,
        525, 521, 523, 543, 553, 512, 514, 532, 534, 535, 533,
        122, 124, 125, 121, 123, 142, 141, 143, 112, 134,
        321, 344, 345, 341, 343, 352, 353, 312, 314, 334, 335);
    public $sancai_jixiongcanban = array(
        415,
        531,
        144, 145);


    //三才+五格  总分*三才权重+  (总分*五格权重)/5 *(天权重+地权重+人权重+总权重+外权重)
    //三才五格算分
    public function getScoreData($firstname, $secondname)
    {

        //分词
        $firstname_arr = $this->mbStrSplit($firstname);
        $secondname_arr = $this->mbStrSplit($secondname);

        //姓分词
        if (sizeof($firstname_arr) > 1) {
            //两个字
            $firstname1 = KXDictionary::where('jtz', $firstname_arr[0])->first();
            $firstname2 = KXDictionary::where('jtz', $firstname_arr[1])->first();
        } else {
            //单个字
            $firstname1 = KXDictionary::where('jtz', $firstname)->first();
        }
        //名分词
        if (sizeof($secondname_arr) > 1) {
            //两个字
            $secondname1 = KXDictionary::where('jtz', $secondname_arr[0])->first();
            $secondname2 = KXDictionary::where('jtz', $secondname_arr[1])->first();
        } else {
            //单个字
            $secondname1 = KXDictionary::where('jtz', $secondname)->first();
        }

        //算分
        $firstName1Bihua = $firstname1->ftbh;
        $firstName2Bihua = empty($firstname2) ? 0 : $firstname2->ftbh;

        $secondName1Bihua = $secondname1->ftbh;
        $secondName2Bihua = empty($secondname2) ? 0 : $secondname2->ftbh;

        //天格 (复姓，合计姓氏之笔画；单姓，再加假添一数)
        $tian_ge = empty($firstName2Bihua) ? $firstName1Bihua + 1 : $firstName1Bihua + $firstName2Bihua;
        //人格 (其构成是姓氏最下字与名字最上字笔画数之和)
        $ren_ge = empty($firstName2Bihua) ? $firstName1Bihua + $secondName1Bihua : $firstName2Bihua + $secondName1Bihua;
        //地格 ()
        $di_ge = empty($secondName2Bihua) ? $secondName1Bihua + 1 : $secondName1Bihua + $secondName2Bihua;
        //总格 (合计姓与名的总笔画数)
        $zong_ge = $firstName1Bihua + $secondName1Bihua;
        $zong_ge = empty($firstName2Bihua) ? $zong_ge : $firstName2Bihua + $zong_ge;
        $zong_ge = empty($secondName2Bihua) ? $zong_ge : $secondName2Bihua + $zong_ge;
        //外格 ()
        if (empty($firstName2Bihua) && empty($secondName2Bihua)) {
            //单姓单名
            $wai_ge = 2;
        } else if (empty($firstName2Bihua)) {
            //单姓
            $wai_ge = $zong_ge - $ren_ge + 1;
        } else if (empty($secondName2Bihua)) {
            //单姓
            $wai_ge = $zong_ge - $ren_ge + 1;
        } else {
            $wai_ge = $zong_ge - $ren_ge;
        }


        //输出

//        echo '<br>';
//        echo '天格 - ' . $tian_ge . ' | 人格 - ' . $ren_ge . ' | 地格 - ' . $di_ge . ' | 总格 - ' . $zong_ge . ' | 外格 - ' . $wai_ge;
        //三才 天地人
        $tian_cai_num = ($tian_ge % 10);
        $ren_cai_num = ($ren_ge % 10);
        $di_cai_num = ($di_ge % 10);

        $sancan_data = $this->get5xnum($tian_cai_num) . $this->get5xnum($ren_cai_num) . $this->get5xnum($di_cai_num);
//        echo '<br>';
//        echo ''.$sancan_data;

        //计算三才分数
        if (in_array($sancan_data, $this->sancai_ji)) {
            $sancan_score = $this->total * $this->sancai_weight * $this->ji_weight;
        } else if (in_array($sancan_data, $this->sancai_xiaoji)) {
            $sancan_score = $this->total * $this->sancai_weight * $this->xiaoji_weight;
        } else if (in_array($sancan_data, $this->sancai_jixiongcanban)) {
            $sancan_score = $this->total * $this->sancai_weight * $this->half_weight;;
        } else {
            $sancan_score = $this->xiong_weight;
        }

        //计算五格分数
        //对81取余
        $tian_ge = $tian_ge % 81;
        $ren_ge = $ren_ge % 81;
        $di_ge = $di_ge % 81;
        $zong_ge = $zong_ge % 81;
        $wai_ge = $wai_ge % 81;


        //天格权重
        if (in_array($tian_ge, $this->shuli_ji)) {
            $tian_ge_weight = $this->ji_weight;
            $tiange_jx_title = '大吉';
            $tiange_jx_tag = 1;
        } else if (in_array($tian_ge, $this->shuli_banji)) {
            $tian_ge_weight = $this->half_weight;
            $tiange_jx_title = '吉';
            $tiange_jx_tag = 2;
        } else {
            $tian_ge_weight = $this->xiong_weight;
            $tiange_jx_title = '凶';
            $tiange_jx_tag = 3;
        }
        //人格权重
        if (in_array($ren_ge, $this->shuli_ji)) {
            $ren_ge_weight = $this->ji_weight;
            $renge_jx_title = '大吉';
            $renge_jx_tag = 1;
        } else if (in_array($ren_ge, $this->shuli_banji)) {
            $ren_ge_weight = $this->half_weight;
            $renge_jx_title = '吉';
            $renge_jx_tag = 2;
        } else {
            $ren_ge_weight = $this->xiong_weight;
            $renge_jx_title = '凶';
            $renge_jx_tag = 3;
        }
        //地格权重
        if (in_array($di_ge, $this->shuli_ji)) {
            $di_ge_weight = $this->ji_weight;
            $dige_jx_title = '大吉';
            $dige_jx_tag = 1;
        } else if (in_array($di_ge, $this->shuli_banji)) {
            $di_ge_weight = $this->half_weight;
            $dige_jx_title = '吉';
            $dige_jx_tag = 2;
        } else {
            $di_ge_weight = $this->xiong_weight;
            $dige_jx_title = '凶';
            $dige_jx_tag = 3;
        }
        //总格权重
        if (in_array($zong_ge, $this->shuli_ji)) {
            $zong_ge_weight = $this->ji_weight;
            $zongge_jx_title = '大吉';
            $zongge_jx_tag = 1;
        } else if (in_array($zong_ge, $this->shuli_banji)) {
            $zong_ge_weight = $this->half_weight;
            $zongge_jx_title = '吉';
            $zongge_jx_tag = 2;
        } else {
            $zong_ge_weight = $this->xiong_weight;
            $zongge_jx_title = '凶';
            $zongge_jx_tag = 3;
        }
        //外格权重
        if (in_array($wai_ge, $this->shuli_ji)) {
            $wai_ge_weight = $this->ji_weight;
            $waige_jx_title = '大吉';
            $waige_jx_tag = 1;
        } else if (in_array($wai_ge, $this->shuli_banji)) {
            $wai_ge_weight = $this->half_weight;
            $waige_jx_title = '吉';
            $waige_jx_tag = 2;
        } else {
            $wai_ge_weight = $this->xiong_weight;
            $waige_jx_title = '凶';
            $waige_jx_tag = 3;
        }
        $wuge_score = ($this->total * $this->wuge_weight) / 5;
        $wuge_score = $wuge_score * ($tian_ge_weight + $ren_ge_weight + $di_ge_weight + $zong_ge_weight + $wai_ge_weight);

        //三才+五格  总分*三才权重+  (总分*五格权重)/5 *(天权重+地权重+人权重+总权重+外权重)

        return ['success' => true,
            'data' => [
                'tg' => $tiange_jx_title,
                'rg' => $renge_jx_title,
                'dg' => $dige_jx_title,
                'zg' => $zongge_jx_title,
                'wg' => $waige_jx_title,
                'zf' => $sancan_score + $wuge_score,
                'tc' => $this->get5x($tian_cai_num),
                'rc' => $this->get5x($ren_cai_num),
                'dc' => $this->get5x($di_cai_num),
            ]
        ];


    }

    public function get5xnum($num)
    {
        switch ($num) {
            case 0 :
                $wuxin = '3';
                break;
            case 1 :
                $wuxin = '2';
                break;
            case 2 :
                $wuxin = '2';
                break;
            case 3 :
                $wuxin = '4';
                break;
            case 4 :
                $wuxin = '4';
                break;
            case 5 :
                $wuxin = '5';
                break;
            case 6 :
                $wuxin = '5';
                break;
            case 7 :
                $wuxin = '1';
                break;
            case 8 :
                $wuxin = '1';
                break;
            case 9 :
                $wuxin = '3';
                break;
            default :
                break;
        }

        return $wuxin;

    }

    public function get5x($num)
    {
        switch ($num) {
            case 0 :
                $wuxin = '水';
                break;
            case 1 :
                $wuxin = '木';
                break;
            case 2 :
                $wuxin = '木';
                break;
            case 3 :
                $wuxin = '火';
                break;
            case 4 :
                $wuxin = '火';
                break;
            case 5 :
                $wuxin = '土';
                break;
            case 6 :
                $wuxin = '土';
                break;
            case 7 :
                $wuxin = '金';
                break;
            case 8 :
                $wuxin = '金';
                break;
            case 9 :
                $wuxin = '水';
                break;
            default :
                break;
        }

        return $wuxin;

    }


    public function clearTable($id)
    {
        $data = Name::where('from',$id);
        $data->delete();
    }


    /**
     * @param $num
     * @return string
     * 数字转中文汉字
     */
    public function numToWord($num)
    {
        $chiNum = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        $chiUni = array('', '拾', '佰', '仟', '萬', '拾', '佰', '仟', '億');

        $chiStr = '';

        $num_str = (string)$num;

        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字

        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num] . $chiUni[1];
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        } else if ($count > 2) {
            $index = 0;
            for ($i = $count - 1; $i >= 0; $i--) {
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag) {
                        $chiStr = $chiNum[$temp_num] . $chiStr;
                        $last_flag = true;
                    }
                } else {
                    $chiStr = $chiNum[$temp_num] . $chiUni[$index % 9] . $chiStr;

                    $zero_flag = false;
                    $last_flag = false;
                }
                $index++;
            }
        } else {
            $chiStr = $chiNum[$num_str[0]];
        }
        return $chiStr;
    }

    /**
     * 搜索名字
     */
    public function searchName($uid, $keyword, $page = 1)
    {
        $data = Name::where('name', 'like', '%' . $keyword . '%')->forPage($page, 100)->get()->unique('name');
        $theName = new Name;
        $theName->id = $uid;
        $theName->from = 0;
        $theName->type = 3;
        $theName->name = $keyword;
        $theName->description = '';
        $theName->by = '';
        $theName->loves = 0;
        $theName->views = 0;
        $theName->from_name = '';
        $data->prepend($theName);
        return ['success' => true, 'data' => $data->toArray()];
    }

    /**
     * 获取根数据
     * @param int $size
     * @return array
     */
    public function getSrcData($page)
    {
        // $article->created_at->diffForHumans()
        $data = Srcdata::all()->groupBy('from')->sortByDesc('updated_at')->map(function ($item) {
//            dd($item->count());
            $tmp = $item->first();
            $tmp->size = $item->sum('size');
            $tmp->custom = $item->count();
            return $tmp;
        })->flatten()->forPage($page, 10);

//        $data = Srcdata::paginate($size)->groupBy('from');
        /*$data = Srcdata::simplePaginate($size);
        $count = count($data);

        $data = $data->groupBy('from');

        $data->flatten()->transform(function ($item, $key) {
            $d = $item;
            $d->size = $this->numToWord(Name::where('from', $d->id)->count());
            return $d;
        });*/


        if (empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'count'=>$data->count(), 'data' => $data->toArray()];
        }

    }

    /**
     * 获取根数据
     * @param int $size
     * @return array
     */
    public function getSrcDataDetails($title,$page)
    {
        // $article->created_at->diffForHumans()
        $data = Srcdata::where('from',$title)->get()->sortByDesc('created_at')->forPage($page, 10);
//        $data = Srcdata::paginate($size)->groupBy('from');
        /*$data = Srcdata::simplePaginate($size);
        $count = count($data);

        $data = $data->groupBy('from');

        $data->flatten()->transform(function ($item, $key) {
            $d = $item;
            $d->size = $this->numToWord(Name::where('from', $d->id)->count());
            return $d;
        });*/


        if (empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'count'=>$data->count(), 'data' => $data->values()];
        }

    }

    /**
     * 获取一条根数据
     * @param int $size
     * @return array
     */
    public function getOneSrcData($id)
    {
//        $data = Srcdata::paginate($size)->groupBy('from');
        $data = Srcdata::find($id);

        if (empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'data' => $data->toArray()];
        }

    }

    /**
     * 获取名字
     * @param $from
     * @param $type
     * @param int $size
     * @return mixed
     */
    public function getName($from, $page, $type = 2)
    {
        $data = Name::where(['from' => $from, 'type' => $type])->forPage($page, 100)->get()->unique('name');

        if (empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true,'count'=> $data->count(), 'data' => $data->values()];
        }
    }


//----------------2017-03-30 21:24:23 新增 --------------------------
    /**
     * @param $id
     * 已读项目
     */
    public function hasRead($id, $uid)
    {
        echo 'has read ' . $id . ' uid ' . $uid;
    }


    public function updateUserFirstName($user_id, $firstName)
    {
        User::where(['id' => $user_id])->update(['firstName' => $firstName]);

        $user = User::where('id', $user_id)->firstOrFail();

        if (empty($user)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'data' => $user->toArray()];
        }

    }

    public function updateUserNickName($user_id, $nickName)
    {
        User::where(['id' => $user_id])->update(['nickName' => $nickName]);
        $user = User::where('id', $user_id)->firstOrFail();


        if (empty($user)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'data' => $user->toArray()];
        }

//        if ($user) {
//            return  ['success' => true, 'data' => $user->toArray()];
//        } else {
//            return ['success' => false, 'data' => '没有此用户!'];
//        }

    }


    //随机获取一个名字,并存储到数据库
    public function randOneName($uid)
    {
        $first = Name::first();
        $last = Name::orderBy('id', 'DESC')->first();
        do {
            $random = mt_rand($first->id, $last->id);
            $data = Name::where('id', '=', $random)->first();
        } while (empty($data));


        User::where(['id' => $uid])->update(['nickName' => $data['name']]);
        $user = User::where('id', $uid)->firstOrFail();


        if (empty($user) || empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'data' => $data];
        }

//        $this->updateUserNickName($uid,$data['name']);
    }


    //查询名字信息
    public function findNameInfo($uid, $name)
    {
        $data = Name::where('name', $name)->first();

        $stored = Store::where(['name' => $name, 'user_id' => $uid])->first();
        $data->stored = empty($stored) ? 0 : 1;

        if (empty($data)) {
            return ['success' => false, 'data' => null];
        } else {
            return ['success' => true, 'data' => $data->toArray()];
        }
    }


    //======================================

    /**
     * post
     * @param Request $request
     * @internal param $id 历史记录* 历史记录
     */
    public function addHistory(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $name_id = $request->input('name_id');
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $description = $request->input('description');

        $history = new History();

        $history->name_id = $name_id;
        $history->user_id = $user_id;
        $history->name = $name;
        $history->description = $description;

        $history->save();

    }

    public function delHistory(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $history = History::where(['id' => $id, 'user_id' => $user_id, 'name' => $name])->delete();

        //$history 0/1
        echo $history . '-' . $id . '-' . $user_id . '-' . $name;
    }

    public function getHistory(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $user_id = $request->input('user_id');
        $data = History::where(['user_id' => $user_id])->paginate(20);

    }

    /**
     * post
     * @param Request $request
     * @internal param $id 收藏记录* 收藏记录
     * @return array
     */
    public function addStore(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $name_id = $request->input('name_id');
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $description = $request->input('description');
        $first_name = $request->input('first_name');

        $score_data = $this->getScoreData($first_name, $name);


        $store = Store::firstOrNew(['name_id' => $name_id]);
        $store->user_id = $user_id;
        $store->name = $name;
        $store->description = $description;
        $store->first_name = $first_name;
        $store->score = $score_data['data']['zf'];

        $store->save();

        return ['success' => true, 'data' => 'ok'];

    }


    public function delStore(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $data = Store::find($id);
        if ($user_id == $data->user_id) {
            $data->delete();
            return ['success' => true, 'data' => 'delete ok'];
        } else {
            return ['success' => false, 'data' => 'delete false'];
        }
    }

    public function updateStore(Request $request)
    {
        //$name_id,$user_id,$name,$description
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $star = $request->input('star');

        Store::where(['id' => $id, 'user_id' => $user_id, 'name' => $name])->update(['star' => $star]);
    }

    //https://idoapi.com/getStore/23?page=1
    public function getStore($user_id, $page = 1)
    {
        //$name_id,$user_id,$name,$description
        $data = Store::all()->where('user_id' , $user_id)->sortByDesc('created_at')->flatten()->forPage($page, 100);

        return ['success' => true, 'data' => $data->toArray()];

    }

    public function getSimpleStore($user_id)
    {
        //$name_id,$user_id,$name,$description
        $data = Store::all()->where('user_id', $user_id)->sortByDesc('created_at')->flatten()->take(10);
        $title = '';
        foreach ($data as $item) {
            $title = $title . $item->first_name . $item->name . ',';
        }
        $title = rtrim($title, ',');

        $size = Store::all()->count();

        if ($size > 10) {
            $title = $title . '...';
        }

        $data_size = $data->count();

        $time = $data_size > 0 ? $data->first()->created_at->diffForHumans() : "无";

        return ['success' => true, 'data' => array('title' => $title, 'size' => $size
        , 'time' => $time)];

    }


    /**
     * 汉字分词
     * @param $string
     * @param int $len
     * @return array
     */
    public function mbStrSplit($string, $len = 1)
    {
        $start = 0;
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string = mb_substr($string, $len, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
        return $array;
    }


}
