<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            color: #252525;
        }

        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        td, th {
            padding: 5px;
        }
    </style>
</head>
<body>
<table style="width: 100%;margin: 12px;">
    <tbody>
    <tr>
        <td>گیرنده: {{ $order->address->name }}</td>
        <td>شماره موبایل: {{ $order->address->mobile }}</td>
    </tr>
    <tr>
        <td>استان: {{ $order->address->city->province->name }}</td>
        <td>شهر: {{ $order->address->city->name }}</td>
    </tr>
    <tr>
        <td colspan="2">آدرس: {{ $order->address->address }}</td>
    </tr>
    <tr>
        <td colspan="2">کد پستی: {{ $order->address->postal_code }}</td>
    </tr>
    </tbody>
</table>
@if($order->factor)
    <div class="">ارسال فاکتور به</div>
    <table style="width: 100%;margin: 12px;">
        <tbody>
        <tr>
            <td>گیرنده: {{ $order->factor->name }}</td>
            <td>شماره موبایل: {{ $order->factor->mobile }}</td>
        </tr>
        <tr>
            <td>استان: {{ $order->factor->city->province->name }}</td>
            <td>شهر: {{ $order->factor->city->name }}</td>
        </tr>
        <tr>
            <td colspan="2">آدرس: {{ $order->factor->address }}</td>
        </tr>
        <tr>
            <td colspan="2">کد پستی: {{ $order->factor->postal_code }}</td>
        </tr>
        </tbody>
    </table>
@endif
<table style="width: 100%;margin: 12px;">
    <thead>
    <tr>
        <th>ردیف</th>
        <th>نام محصول</th>
        <th>تعداد</th>
        <th>قیمت</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product['name'] }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>{{ number_format($product->pivot->unit_price) }} تومان</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table style="width: 100%;">
    <tbody>
    <tr>
        <td>شماره سفارش: {{ $order->id }}</td>
        <td>تعداد محصول: {{ count($order->products) }}</td>
    </tr>
    <tr>
        <td>مبلغ پرداختی: {{ number_format($order->amount) }} تومان</td>
        <td>تاریخ: {{ verta($order->created_at)->formatJalaliDatetime() }}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
