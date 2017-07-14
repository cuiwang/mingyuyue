<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//
Route::get('/', function () {
    return view('welcome');
});

//==================后台录入古文======================
//显示网页录入
Route::get('/showStoreDataView', 'DataController@showStoreDataView');
//post 提交网页存储srcdata
Route::post('/storeData','DataController@storeData');
//==================================================

//====================录入后调用生成名字=========================
//根据srcdata批量生成名字库
Route::get('/name', 'DataController@name');
//根据srcdata id指定生成名字库
Route::get('/makename/{id}', 'DataController@makename');
//===========================================================

//==================微信小程序接口======================
//是否中文
Route::get('/ischinese/{s}', 'WxAuthController@ischinese');
//获取微信返回的session code
Route::get('/wxCode/{code}', 'WxAuthController@code');
//获取登录后微信信息
Route::post('/wxInfo', 'WxAuthController@wxInfo');
//用微信数据注册到本系统
Route::post('/wxRegister', 'WxAuthController@wxRegister');
//获取名字列表
Route::get('/getName/{id}/{page}/{type?}', 'IndexController@getName');
//获取srcdata列表
Route::get('/getSrcData/{size?}', 'IndexController@getSrcData');
Route::get('/getSrcDataDetails/{title}/{page}', 'IndexController@getSrcDataDetails');
//获取一条scrdata
Route::get('/getOneSrcData/{id}', 'IndexController@getOneSrcData');
//注入已读
Route::get('/hasRead/{id}/{uid}', 'IndexController@hasRead');
//获取三才五格分数等
Route::get('/getScoreData/{firstname}/{secondname}', 'IndexController@getScoreData');
//修改用户姓
Route::get('/updateUserFirstName/{id}/{name}', 'IndexController@updateUserFirstName');
//随机获取一个名字
Route::get('/randOneName/{uid}', 'IndexController@randOneName');
//查找名字来源
Route::get('/findNameInfo/{uid}/{name}', 'IndexController@findNameInfo');
//添加到历史记录
Route::post('/addHistory', 'IndexController@addHistory');
//添加到收藏
Route::post('/addStore', 'IndexController@addStore');
//删除历史
Route::post('/delHistory', 'IndexController@delHistory');
//删除收藏
Route::post('/delStore', 'IndexController@delStore');
//更新收藏
Route::post('/updateStore', 'IndexController@updateStore');
//获取历史记录列表
Route::post('/getHistory', 'IndexController@getHistory');
//获取收藏列表
Route::get('/getStore/{uid}/{page?}', 'IndexController@getStore');
//获取精简收藏列表
Route::get('/getSimpleStore/{uid}', 'IndexController@getSimpleStore');
//搜索名字
Route::get('/searchName/{uid}/{keyword}/{page?}', 'IndexController@searchName');
//康熙字典查找名字
Route::get('/findData/{name}', 'DataController@findData');

//===========================================================
//[危险操作]清空指定表
Route::get('/clearTable/{id}','IndexController@clearTable');
//===========================================================


