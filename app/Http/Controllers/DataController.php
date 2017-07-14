<?php

namespace App\Http\Controllers;

use App\Dicitionary;
use App\Name;
use App\Srcdata;
use Illuminate\Http\Request;



class DataController extends Controller
{


    public function showStoreDataView()
    {
        return view('storeDataView');
    }


    public function storeData(Request $request)
    {
        $input = $request->all();

        $data = Srcdata::firstOrCreate([
            'from' => $input['from'],
            'by' => $input['by'],
            'name' => $input['name'],
            'time' => $input['time'],
            'author' => empty($input['author']) ? '佚名' : $input['author'],
            'description' => $input['description'],
            'content' => $input['content'],
        ]);


        return redirect()->back()->with(["from" => $input['from'],
            "by" => $input['by'],
            "name" => $input['name'],
            "time" => $input['time'],
            "author" => $input['author'],
            "description" => $input['description']]);

        //只需要录入到数据库,以后统一生成
//        $this->makename($data->id);
    }


    public function findData($name)
    {
        $arr = $this->mbStrSplit($name);

        if (sizeof($arr) > 1) {

            $data1 = Dicitionary::where('name', $arr[0])->first();
            $data2 = Dicitionary::where('name', $arr[1])->first();
            return ['success' => true, 'data' => [$data1, $data2]];

        } else {
            $data = Dicitionary::where('name', $name)->get();
            return ['success' => true, 'data' => $data];
        }
//
    }


    //
    /**
     * 处理生成名字
     */
    public function makename($id)
    {

        $data = Srcdata::where('id', '=', $id)->first();
        //$data->content
        //处理content
        //单词
        $dealFirstData = $this->replaceData($data->content);

        $vars = explode("|", $dealFirstData);
        $i = 0;
        foreach ($vars as $items) {
            $dealData = $this->replaceAgainData($items);
            $var = explode("|", $dealData);
            foreach ($var as $item) {
                if (!empty($item)) {
                    $i++;
                    //分词
                    $tmpArr = $this->mbStrSplit($item);
                    //输出单词
                    $this->echoName($tmpArr, count($tmpArr), $data->id, empty($data->by) ? $data->from . '·' . $data->name : $data->from . '·' . $data->by . '·' . $data->name, $items, $data->author);
                    //输出双词
                    $this->echo2Name($tmpArr, count($tmpArr), $data->id, empty($data->by) ? $data->from . '·' . $data->name : $data->from . '·' . $data->by . '·' . $data->name, $items, $data->author);
                }
            }
        }


//            dd($dealData);
        //传统去重
//            $tmpArr = array_unique($this->mbStrSplit($dealData));
        /*// 使用键值互换去重 提速
        $arr = array_flip($this->mbStrSplit($dealData));
        $tmpArr = array_flip($arr);
        //重排
        $tmpArr = array_values($tmpArr);*/


        $size = Name::where('from', $data->id)->count();
        $data->done = 1;
        $data->size = $size;
        $data->save();

        echo $data->name . '___' . 'done';
    }

    public function name()
    {

        //判断是否处理过
        $datas = Srcdata::where('done', '=', 0)->get();
        foreach ($datas as $data) {
            //$data->content
            //处理content
            //单词
            $dealFirstData = $this->replaceData($data->content);
            $vars = explode("|", $dealFirstData);

            foreach ($vars as $items) {
                $dealData = $this->replaceAgainData($items);
                $var = explode("|", $dealData);

                foreach ($var as $item) {
                    if (!empty($item)) {

                        //分词
                        $tmpArr = $this->mbStrSplit($item);
                        //输出单词
                        $this->echoName($tmpArr, count($tmpArr), $data->id, empty($data->by) ? $data->from . '·' . $data->name : $data->from . '·' . $data->by . '·' . $data->name, $items, $data->author);
                        //输出双词
                        $this->echo2Name($tmpArr, count($tmpArr), $data->id, empty($data->by) ? $data->from . '·' . $data->name : $data->from . '·' . $data->by . '·' . $data->name, $items, $data->author);
                    }
                }
            }

            $size = Name::where('from', $data->id)->count();
            $data->done = 1;
            $data->size = $size;
            $data->save();

            echo $data->name . '  -> ' . '处理完成';
            echo '<br>';

        }
    }

    /**
     * 格式化数据,双词需要替换成|
     * @param $data
     * @param $type 1=单 2=双
     * @return string
     */
    public function replaceData($data)
    {
        $duanju = array(" ","　",". ", "?", "？ ", "。", "！ ", "！", "？", "；", "：");
        $fenju = array(
            "
　　", "

", "

", "
　　", "
　　", "\t", "\n", "\r", "《", "》", "”", "“", "、", "」", "「", "【", "】", "‘", "’", "◎");
        $yuqici = array("矣", "也", "乎", "哉", "而", "何", "之", "曰", "者", "耶", "邪", "呜", "呼", "哀", "咦", "嘘", "唏", "兮");
        $dealData = str_replace($duanju, '|', $data);
        $dealData = str_replace($fenju, '', $dealData);
        $dealData = str_replace($yuqici, '', $dealData);

        return trim($dealData);
    }

    public function replaceAgainData($data)
    {
        $qian = array("　"," ", ", ", "，");

        $dealData = str_replace($qian, '|', $data);

        return trim($dealData);
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


    /**
     * 输出单字
     * @param $names
     * @param $len
     * @param $from
     */
    public function echoName($names, $len, $from, $from_name, $src, $author)
    {
        //$names = {"x","x","x"...}
        if ($len > 1) {
            for ($x = 0; $x < $len; $x++) {
//                echo('' . $names[$x] . "-----");


                $Name = new Name();
                $Name->from = $from;
                $Name->from_name = $from_name;
                $Name->type = 1;
                $Name->name = $names[$x];
                $Name->description = $src;
                $Name->by = $author;
//                echo 'echoName --- '.$src;
                //todo
                $Name->save();

                /*if ($x < 5) {
                    if ($len >=5) {
                        $Name->description = $data[0].$data[1].$data[2].$data[3].$data[4];
                    } else{
                        $Name->description = '';
                    }
                } else if ($len - $x >= 5) {
                    $Name->description = $data[$x] . $data[$x+1] . $data[$x+2] . $data[$x+3] . $data[$x+4];
                } else {
                    if ($len >=5) {
                        $Name->description = $data[$len-5].$data[$len-4].$data[$len-3].$data[$len-2].$data[$len-1];
                    }else{
                        $Name->description = '';
                    }
                }*/


            }
        }


    }

    /**
     * 输出双字
     * @param $names
     * @param $len
     * @param $from
     */
    public function echo2Name($names, $len, $from, $from_name, $src, $author)
    {
        //$names = {"x","x","x"...}
        if ($len > 1) {
            for ($x = 1; $x < $len; $x++) {

//                echo (''.$names[0].$names[$x]."-----");

                $Name = new Name();
                $Name->from = $from;
                $Name->from_name = $from_name;
                $Name->type = 2;
                $Name->name = $names[0] . $names[$x];
                $Name->description = $src;
                $Name->by = $author;
//                echo 'echo2Name --- '.$Name;

                $Name->save();

            }
            $dealData = array_slice($names, 1);
            $this->echo2Name($dealData, count($dealData), $from, $from_name, $src, $author);
        }


    }
}
