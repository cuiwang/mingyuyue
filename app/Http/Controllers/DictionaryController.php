<?php

namespace App\Http\Controllers;

use App\Dicitionary;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    //
    public function getBiHua()
    {
        $data = Dicitionary::all();
        $i = 0;
        foreach ($data as $item) {

//            $item->bs = 0;
//            $item->done = 0;
//            $item->save();

           if ($item->done === 0) {
               $bihua = $item->bihua;
               $i++;
               $pos = strrpos($bihua, '：');
               $pos1 = strrpos($bihua, '笔');

               if ($pos && $pos1) {
                   echo $bihua.'~~~'.$pos.'--'.$pos1;
                   $d = substr($bihua, $pos+1, $pos1 - $pos -3);
                   dd($d);
                   if ($d > 0 && $d < 99) {
                       $item->bs = $d;
                       $item->done = 1;
                       $item->save();
                   }

               }

           }

//                echo ''.$bihua;
//                dd(strstr($bihua, '笔)', TRUE));




            /* if ($item->done === 1) {
                 $bihua = $item->bihua;
 //                echo ''.$bihua;
 //                dd(strstr($bihua, '笔)', TRUE));
                     $item->bs =0;
                     $item->done = 0;
                     $item->save();


             }*/
        }

    }
}
