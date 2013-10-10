<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<title>史上最成功的app</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
<style type="text/css">
    body{
        background-color: #eaeaea;
    }

    a {
        text-decoration: none;
    }
    .header{
        background-color: #82d2ff;
        height: 8%;
        width: 100%;
        position: absolute;
        top:0px;
        right: 0px;
        left: 0px;
    }

    .section{
        font-family: 'Microsoft YaHei', 微软雅黑, arial, simsun, 宋体;
        position: absolute;
        height: 100%;
        width:100%;
        top: 8%;
        right: 0px;
    }
    .intro {
        background-color: #ffffff;
        position: relative;
        height: auto;
        width: 95%;
        margin-top: 4%;
        margin-left: 2.5%;
    }
/*    .face{
        background-image:url(image/1.jpg);
        background-repeat: no-repeat;
        background-size: 90% 90%;
        background-position: center;
        position: relative;
        height: 85px;
        width: 85px;
        top:3px;
        left: 3px;
       bottom: 3px;
    }   */
    .face{
        position: relative;
        height: 85px;
        width: 85px;
        margin-left: 1.5%;
        margin-top: 1.5%;
    }

    .intro h1{
        font-size: 16px;
        position: relative;
        left:23%;
        margin-top:-85px;
        padding-top: 6px;
        margin-left: 2%;
    }
    .intro h2{
        font-size: 13px;
        position: relative;
        left:23%;
        top:28%;
        margin-left: 2%;
    }
    .intro h3{
        font-size: 13px;
        position: relative;
        left:23%;
        top:50%;
        margin-left: 2%;
    }
    .favorable,.info{
        background-color: #ffffff;
        position: relative;
        height: auto;
        width: 95%;
        margin-top: 7%;
        margin-left: 2.5%;
    }
    .title{
        position: relative;
        font-size: 16px;
        font-weight:bold;
        margin-left: 3%;
    }
    .text{
        margin-left: 3%;
    }
    ul{
        margin-left: 0px;
        margin-right: 0px;
        width: 100%; 
    }
    li{
        float: left;
        overflow: hidden; 
        width:40%;
        display:inline; 
    }
    .download{
        background-color: #7dadec;
        position: relative;
        height: 68px;
        width: 80%;
        border-radius: 15px;
    }
    .download h1{
        font-size: 100%;
        color: #ffffff;
        position: relative;
        width: 100%;
        height: 100%;
        top:40%;      
    }
    .home-page{
        background-color: #e7a22b;
        position: relative;
        height: 68px;
        width: 80%;
        margin-left: 18%;        
        border-radius: 15px;
    }
    .home-page h1{
        font-size: 100%;
        color: #ffffff;
        position: relative;
        width: 100%;
        height: 100%;
        top:40%;   
    }
    
</style>
<script type="text/javascript">
    function downFile() {
                var url = "http://localhost/myapp/Sina_weibo_v4_00.apk";
                location.href = url;
            }
</script>
</head>  
<body>
    <header class="header">
    </header>
    <section class="section">
        <div class="intro">
            <img class="face" src=<?php echo ($face); ?>>
<!--            <div class="face" ></div>   -->
                <h1><?php echo ($shopname); ?></h1>
                <h2><?php echo ($address); ?></h2>
                <h3>电话：<?php echo ($phone_num); ?></h3>
        </div>
        <div class="favorable">
            <div class="title">优惠信息：</div>
            <div class="text">比如极品醉青蟹，就是选用产自浙江东部沿海三门县的青蟹制成的，那里是全世界最有名的青蟹产地，以壳薄肉肥、膏多汁厚而闻名，尤其难得的是毫无其他产地青蟹的那种泥腥气，用上等花雕酒活醉后，满身的大团红膏更显惊艳，入口醉香浓郁、沥沥生津。</div>
        </div>
        <div class="info">
            <div class="title">公告信息：</div>
            <div class="text">比如极品醉青蟹，就是选用产自浙江东部沿海三门县的青蟹制成的，那里是全世界最有名的青蟹产地，以壳薄肉肥、膏多汁厚而闻名，尤其难得的是毫无其他产地青蟹的那种泥腥气，用上等花雕酒活醉后，满身的大团红膏更显惊艳，入口醉香浓郁、沥沥生津。</div>
        </div>
        <ul>
        <li><a href="javascript::" onclick="downFile()"><div class="download"><h1 align="center">安卓立即下载</h1></div></a></li>
        <li><a href="/website/Tpl/home.html"><div class="home-page"><h1 align="center">进入官网</h1></div></a></li>            
        </ul>

    </section>
</body>
</html>