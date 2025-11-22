<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../app/config.php';
include_once '../../app/models/DanhGia.php';

$danhgia = new app_models_DanhGia();

if (!empty($_GET['userId']) && !empty($_GET['bookId'])) {
    $userId = $_GET['userId'];
    $bookId = $_GET['bookId'];

    $result = $danhgia->getReviewsByOrderId($userId, $bookId);

    if ($result && isset($result['rating'])) {
        http_response_code(200);
        echo json_encode(array("data" => array("rating" => (int)$result['rating'])));
    } 
    else {
        http_response_code(200);
        echo json_encode(array("data" => array("rating" => null)));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Dữ liệu không hợp lệ. Vui lòng cung cấp userId và bookId."));
}
?>