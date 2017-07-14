<?php

namespace App\Http\Controllers;

use App\Dicitionary;
use App\Hanzi;
use Illuminate\Http\Request;
use CUtf8_PY;
use Illuminate\Support\Facades\DB;
use QL\QueryList;
use QL\Ext\Lib\Http;

class TestController extends Controller
{
    public function make()
    {

        $datas = Hanzi::all();
//        echo ''.$datas;

        foreach ($datas as $data) {
            $data->done = 0;
            $data->save();
        }

       /* do {
            $datas = Hanzi::where('done','0')->get();
            foreach ($datas as $data) {
                $han =  $data['han'];
                $this->fire($han,$data);
            }
            echo 'sleep(10)';
            sleep(10);
        }while(sizeof($datas));*/




    }



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


    public function getDictionary($title,$datas)
    {
        $url = 'https://www.ankangwang.com/zidian/index.asp?q=' . urlencode($title);
    }
        //
    public function fire($title,$datas)
    {
        $url = 'http://tool.httpcn.com//Zi/So.asp?Tid=1&wd='.urlencode($title);

//        $html = $this->hideip_gethtml($url);
//        $html = file_get_contents($url);
//        echo ''.$html;


//        //HTTP操作扩展
//        $data = QueryList::run('Request',[
//            'target' => 'http://tool.httpcn.com/KangXi/So.asp?Tid=1&wd='.$t,
//            'referrer'=>'http://tool.httpcn.com',
//            'method' => 'GET',
//            'params' => ['var1' => 'testvalue', 'var2' => 'somevalue'],
//            'user_agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0',
//            'cookiePath' => './cookie.txt',
//            'timeout' =>'30'
//        ])->setQuery( array(
//            'base' => array('.text15', 'text', '-script', function ($content) {
//                $content = trim($content);
//                $d1 = explode('　', strstr($content, '拼'));
//                $d2 = explode('　', strstr($content, '部'));
//                $d3 = explode('　', strstr($content, '康'));
//                $pinyin = $d1[0];
//                $bushou = $d2[0];
//                $bihua = $d3[0];
//                $bihua = str_replace(' ','',$bihua);
//                $bihua = str_replace('；','笔',$bihua);
//                return ['pinyin' => $pinyin, 'bushou' => $bushou, 'bihua' => $bihua];
//            }),
//            'wuxin' => array('.text16:eq(1)', 'text', '-span', function ($content) {
//
//                $d = explode('　', trim($content));
//                $wuxin = $d[0];
//
//                return ['wuxin' => strstr($wuxin, '汉')];
//
//            }),
//            'fanti' => array('.text15>.a16fan', 'text', '', function ($content) {
//                return ['fanti' =>trim($content)];
//
//            }),
//            'jieshi' => array('.content16', 'text', '-span -strong', function ($content) {
//
//                return ['jieshi' => trim($content)];
//
//            }),
//        )
//            ,'#div_a1')->data;


        $data = QueryList::Query(
            $url,
            array(
//                'base1' => array('.text15', 'text', '-script br', function ($content) {
//                    $content = trim($content);
//                    $datas = explode('<br>',$content);
//                    return $datas;
//                }),
                'base' => array('.text15', 'text', '-script br', function ($content) {
                    $content = trim($content);
                   $datas = explode('<br>',$content);

                    $d1 = explode('　', strstr($datas[1], '拼'));
                    $d2 = explode('　', strstr($datas[2], '部'));
                    $d3 = explode('　', strstr($datas[2], '总'));
                    $pinyin = $d1[0];
                    $bushou = $d2[0];
                    $bihua = $d3[0];
                    return ['pinyin' => $pinyin, 'bushou' => $bushou, 'bihua' => $bihua.'笔'];
                }),
//                'base' => array('.text15', 'text', '-script br', function ($content) {
//                    $content = trim($content);
//                    $d1 = explode('　', strstr($content, '拼'));
//                    $d2 = explode('　', strstr($content, '部'));
//                    $d3 = explode('　', strstr($content, '总'));
//                    $pinyin = $d1[0];
//                    $bushou = $d2[0];
//                    $bihua = $d3[0];
//                    return ['pinyin' => $pinyin, 'bushou' => $bushou, 'bihua' => $bihua.'笔'];
//                }),
                'wuxin' => array('.text16:eq(1)', 'text', '-span', function ($content) {

                    $d = explode('　', trim($content));
                    $wuxin = $d[0];

                    return ['wuxin' => strstr($wuxin, '汉')];

                }),
                'fanti' => array('.text15>.a16fan', 'text', '', function ($content) {
                    return ['fanti' =>trim($content)];

                }),
                'jieshi' => array('.content16', 'text', '-span -strong', function ($content) {

                    return ['jieshi' => trim($content)];

                }),
            ),
            '#div_a1')->data;

//打印结果


        if (sizeof($data) > 0) {
            $dicitionary = new Dicitionary();

            $dicitionary->name = $title;
            $dicitionary->pinyin = $data[0]['base']['pinyin'];
            $dicitionary->bushou = $data[0]['base']['bushou'];
            $dicitionary->bihua = $data[0]['base']['bihua'];
            $dicitionary->fanti =  $data[0]['fanti']['fanti'];
            $dicitionary->wuxin =  $data[0]['wuxin']['wuxin'];
            $dicitionary->jieshi =  $data[0]['jieshi']['jieshi'];

            $result =  $dicitionary->save();
            $datas->done = '1';
            $datas->save();
            if ($result) {
                echo  $title.' ------------ 生成完毕!';
                echo '<br>';
            }
        }else{
            echo  $title.' - !!!!!!!!!!!!!!!!!!!!!!!!!!生成失败!';
            echo '<br>';
        }

//        dd(strstr($data[0]['bushou'][1],'部'));//部首
//        dd(explode(' ',$data[0]['bushou'][0]));//拼音
    }
}
