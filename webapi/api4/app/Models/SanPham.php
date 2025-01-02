<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;
    protected $table = 'SanPham';
    protected $primaryKey = 'SanPhamId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng Hình ảnh sản phẩm (Một sản phẩm có thể có nhiều hình ảnh)
    public function hinhAnhSanPhams()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'SanPhamId');
    }

    // Quan hệ với bảng Chi tiết giỏ hàng (Một sản phẩm có thể có nhiều chi tiết giỏ hàng)
    public function chiTietGioHangs()
    {
        return $this->hasMany(ChiTietGioHang::class, 'SanPhamId');
    }

    // Quan hệ với bảng Chi tiết đơn hàng (Một sản phẩm có thể có nhiều chi tiết đơn hàng)
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'SanPhamId');
    }

    // Quan hệ với bảng Khuyến mãi (Nhiều sản phẩm có thể áp dụng nhiều khuyến mãi)
    public function khuyenMais()
    {
        return $this->belongsToMany(KhuyenMai::class, 'SanPhamKhuyenMai', 'SanPhamId', 'KhuyenMaiId');
    }
    public function thuongHieu()
{
    return $this->belongsTo(ThuongHieu::class, 'ThuongHieuId', 'ThuongHieuId');
}

}
