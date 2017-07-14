<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <title>名余曰 - 数据录入</title>

    <!-- Fonts -->

    <!-- Styles -->
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1> 数据录入 <small>请提交数据</small></h1>
    </div>
    <div class="row" style="margin: 30px 250px;">
        <form  action="/storeData" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label for="from">主类</label>
                <input type="text" class="form-control" value="{{session('from')}}" name="from" id="from" placeholder="诗经">
            </div>
            <div class="form-group">
                <label for="by">分类</label>
                <input type="text" class="form-control" value="{{session('by')}}" name="by" id="by" placeholder="国风·周南">
            </div>
            <div class="form-group">
                <label for="name">标题</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="关雎">
            </div>
            <div class="form-group">
                <label for="content">内容</label>
                <textarea class="form-control"  name="content" id="content" rows="10"></textarea>
            </div>
            <button type="submit" class="btn btn-success btn-lg btn-block">提交</button>
            <div class="form-group">
                <label for="time">年代</label>
                <input type="text" class="form-control" value="{{session('time')}}" name="time" id="time" placeholder="先秦">
            </div>
            <div class="form-group">
                <label for="author">作者</label>
                <input type="text" class="form-control" value="{{session('author')}}" name="author" id="author" placeholder="佚名">
            </div>
            <div class="form-group">
                <label for="description">描述</label>
                <textarea class="form-control" id="description" value="{{session('description')}}" name="description" rows="10"></textarea>
            </div>


        </form>
    </div>
</div>

</body>


</html>
