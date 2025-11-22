<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../app/config.php';
include_once '../../app/models/DanhGia.php';

$danhgia = new app_models_DanhGia();

$data = json_decode(file_get_contents("php://input"), true);

if (
    !empty($data['userId']) &&
    !empty($data['bookId']) &&
    !empty($data['orderId']) &&
    !empty($data['rating'])
) {
    $result = $danhgia->createReview($data['userId'], $data['bookId'], $data['orderId'], $data['rating']);
    if ($result) {
        http_response_code(201);
        echo json_encode(array("message" => "Đánh giá đã được tạo."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Không thể tạo đánh giá."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Không thể tạo đánh giá. Dữ liệu không đầy đủ."));
}
?>