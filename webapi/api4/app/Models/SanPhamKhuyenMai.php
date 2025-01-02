<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPhamKhuyenMai extends Model
{
    use HasFactory;
    protected $table = 'sanphamkhuyenmai';
    protected $primaryKey = 'SanPhamKMId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng SanPham (Một sản phẩm có thể có nhiều khuyến mãi)
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'SanPhamId');
    }

    // Quan hệ với bảng KhuyenMai (Một khuyến mãi có thể áp dụng cho nhiều sản phẩm)
    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'KhuyenMaiId');
    }
}
