<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>激活邮件</title>
</head>
<body>
    <p>尊敬的&nbsp;<span style="font-weight: bold;color:red;">{{ $user->user_rname }}</span>&nbsp;员工,您好！</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;管理员已成功在ZER药店销售管理平台中注册了您的员工账号，请于24小时内激活您的账号，过期失效。<a href="http://www.pharmacy.com/active?userid={{ $user->user_id }}&token={{ $user->token }}">激活链接</a></p>
</body>
</html>