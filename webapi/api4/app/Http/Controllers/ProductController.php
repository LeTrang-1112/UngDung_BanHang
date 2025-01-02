<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\HinhAnhSanPham;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 1. GET - Hiển thị Sản phẩm
    public function ThongTinSanPham()
{
    // Lấy tất cả sản phẩm kèm theo hình ảnh và khuyến mãi
    $products = SanPham::with(['hinhAnhSanPhams', 'khuyenMais'])->get();

    // Định dạng dữ liệu đầu ra
    $response = $products->map(function ($product) {
        // Tính phần trăm giảm giá nếu có
        $discount = $product->khuyenMais->sum('GiaTriGiam');
        
        // Lấy hình ảnh sản phẩm (lấy ảnh đầu tiên)
        $image = $product->hinhAnhSanPhams->first();
        $imageUrl = $image ? $image->DuongDan : null;

        // Trả về dữ liệu cần thiết
        return [
            'SanPhamId' => $product->SanPhamId,  // Sử dụng tên trường từ cơ sở dữ liệu
            'TenSanPham' => $product->TenSanPham, // Tên sản phẩm
            'Gia' => $product->Gia, // Giá sản phẩm
            'HinhAnh' => $imageUrl, // Lấy đường dẫn ảnh đầu tiên
            'GiaTriGiam' => $discount > 0 ? $discount : null, // Nếu có giảm giá, trả về giá trị
        ];
    });

    return response()->json($response);
}

    // 2. GET - Chi tiết Sản phẩm
    public function ChiTietSanPham($id)
{
    // Lấy sản phẩm chi tiết kèm theo thương hiệu và hình ảnh
    $product = SanPham::with(['thuongHieu', 'hinhAnhSanPhams'])->find($id);

    // Kiểm tra nếu sản phẩm không tồn tại
    if (!$product) {
        return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
    }

    return response()->json([
        'SanPhamId' => $product->SanPhamId, // ID sản phẩm
        'MoTa' => $product->MoTa, // Mô tả sản phẩm
        'KichThuoc' => $product->KichThuoc, // Kích thước
        'ChatLieu' => $product->ChatLieu, // Chất liệu
        'SoLuongTon' => $product->SoLuongTon, // Số lượng tồn kho
        'TenThuongHieu' => $product->thuongHieu->TenThuongHieu, // Thương hiệu
        'HinhAnhs' => $product->hinhAnhSanPhams->map(function ($image) {
            return [
                'id' => $image->HinhAnhId,
                'path' => $image->DuongDan, // Đường dẫn ảnh
                'description' => $image->MoTa, // Mô tả ảnh
            ];
        }),
    ]);
}

}
