<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body{
            color: #252525;
        }
        .pdf-head{
            font-size: 16px;
            text-align: center;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .overflow-hidden{
            overflow: hidden;
        }
        .float-right{
            float: right;
        }
        .float-left{
            float: left;
        }
        .w-70{
            width: 70%;
        }
        .w-30{
            width: 30%;
        }
    </style>
</head>
<body>
<div class="pdf-head">فاکتور فروش</div>
<div class="overflow-hidden">
    <div class="float-right w-70">
        <div class="">خریدار: علی کشتکار</div>
        <div class="">آدرس: رشت، خیابان امام</div>
    </div>
    <div class="float-left w-30" dir="ltr">
        <div>شماره سفارش: {{ enToFa(12345) }}</div>
        <div>تاریخ: {{ enToFa(verta()->formatJalaliDate()) }}</div>
    </div>
</div>
</body>
</html>
