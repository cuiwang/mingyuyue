# mingyuyue 名余曰
## 古诗词取名后台

 ![名余曰](https://ws3.sinaimg.cn/large/006tKfTcgy1fhjge4gnnij308y08y0tb.jpg)
 
### 支撑微信小程序 [ 名余曰 ] , 通过拆分古文生成名字.

**基于Laravel 5.3,PHP >=5.6.10,MYSQL >=5.5**

##### 任何问题欢迎加入QQ群交流: **287021519** 言无不知,知无不尽
-------
## 程序运行说明
1. 搭建好LAMP或WAMP环境 , 创建好站点 , 复制所有文件到站点根目录**(给好权限)**
2. 复制根目录下env_example.txt 重命名为.env
3. 导入根目录下的mingyuyue.sql文件到你的数据库
4. 修改.env中DB_开头的内容,为你的数据库信息
### 基本功能
1. 后台录入古文
2. 去除古文中特殊字符和常用语气词
3. 单个字拆分或两个字拆分
4. 随机取名
5. 三才五格分析
6. 收藏功能
7. 其他

### 接口说明

```
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
```
### 后台录入操作流程
1. 后台录入古文 ![](https://ws3.sinaimg.cn/large/006tKfTcgy1fhjfx5autej30u00l6wfq.jpg)
2. 调用生成名字接口 name/makename



