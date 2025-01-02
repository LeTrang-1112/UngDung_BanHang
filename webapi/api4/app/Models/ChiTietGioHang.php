<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietGioHang extends Model
{
    use HasFactory;
    protected $table = 'chitietgiohang';
    protected $primaryKey = 'CTGHId';
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'GioHangId',
        'SanPhamId',
        'SoLuong',
        'Gia',
    ];
    // Quan hệ với bảng GioHang (Một chi tiết giỏ hàng thuộc về một giỏ hàng)
    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'GioHangId');
    }

    // Quan hệ với bảng SanPham (Một chi tiết giỏ hàng thuộc về một sản phẩm)
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'SanPhamId');
    }
}
