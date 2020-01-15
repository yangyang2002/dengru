<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{url('logDo')}}" method='post'>
        <p style="color:red">
            @if(!empty($errors->first()))
                {{$errors->first()}}
            @endif
        </p>
        <table >
            <tr>
                <td>用户名</td>
                <td><input type="text" name="user_name"></td>
            </tr>
            <tr>
                <td>密码</td>
                <td><input type="password" name="user_pwd"></td>
            </tr>
            <tr>
                <td><button>登录</button></td>
                <td></td>
            </tr>
        </table>
    </form>
    <a href="{{url('Dimension')}}">扫码登入</a>
    
</body>
</html>
<script src="/admin/js/jquery-3.2.1.min.js"></script>
<script>
    $("document").on("click","#but",function(){
        $("#img").show();
    });
</script>