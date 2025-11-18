<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy cấu hình cổng
$port = $_SERVER['SERVER_PORT'];


$vnp_TmnCode = "2MUICAVM"; //Website ID in VNPAY System
$vnp_HashSecret = "S60FTLLXRG51LZ8SRSQGYJE9ZMJCC6WQ"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost:$port/vnpay_php/vnpay_return.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
