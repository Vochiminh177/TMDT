<?php

class app_models_DanhGia extends app_libs_DBConnection
{
    protected $table_name = 'ratings';

    // Lấy đánh giá của sản phẩm theo mã đơn hàng
    public function getReviewsByOrderId($userId, $bookId)
    {
        return $this->building_queryParam([
            'where' => 'user_id = ? && book_id = ?',
            'params' => [$userId, $bookId]
        ])->select_one();
    }

    /**
     * Thêm đánh giá mới vào cơ sở dữ liệu.
     * Đổi tên hàm thành createReview và nhận các tham số riêng lẻ để khớp với lời gọi trong API.
     */
    public function createReview($userId, $bookId, $orderId, $rating)
    {
        $data = [
            'user_id' => $userId,
            'order_id' => $orderId,
            'book_id' => $bookId,
            'rating' => $rating
        ];

        return $this->building_queryParam([
            'field' => $data
        ])->insert();
    }
}