<?php
function returnJSONBook($books)
{
    if (!$books) {
        return [];
    }

    if (!isset($books[0])) {
        $books = [$books];
    }

    $response = [];
    foreach ($books as $book) {
        $response[] = [
            "id" => $book['maSach'],
            "name" => $book['tenSach'],
            "numberOfPages" => $book['soTrang'],
            "size" => $book['kichThuoc'],
            "description" => $book['moTa'],
            "authorId" => $book['maTacGia'],
            "genreId" => $book['maTheLoai'],
            "coverTypeId" => $book['maLoaiBia'],
            "publisherId" => $book['maNXB'],
            "publishYear" => $book['namXuatBan'],
            "originalPrice" => $book['giaTran'],
            "sellingPrice" => $book['giaBan'],
            "image" => $book['hinhAnh'],
            "status" => $book['trangThai'],
            "updatedAt" => $book['ngayCapNhat'],
            "discount" => $book['phanTramGiamGia']
        ];
    }
    return $response;
}
?>