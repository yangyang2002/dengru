<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h3>{{$status}}</h3>
    <table>
        <tr>
            <td>扫码登入</td>
          
        </tr>
        <tr>
            <td><img  src="{{$Dimension}}" id="img" border="10" style="width:100px"/></td>
        </tr>
    </table>
</body>
</html>
<script src="/admin/js/jquery-3.2.1.min.js"></script>
<script>
var t = setInterval("text();",2000);
var status="{{$status}}";
function text(){
$.ajax({
    url: "{{url('index')}}",
    data: {
        status: status
    },
    dataType: "json",
    success: function (res) {
    //    console.log(res);
         //返回提示
         if(res.ret == 1){
                //关闭定时器
                clearInterval(t);
                //扫码登录成功
                alert(res.msg);
                location.href = "{{url('admin')}}";
            }
    }
});
};
    
</script>