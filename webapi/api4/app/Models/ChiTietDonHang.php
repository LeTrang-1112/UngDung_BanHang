<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;
    protected $table = 'chitietdonhang';
    protected $primaryKey = 'CTDHId';
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'DonHangId',
        'SanPhamId',
        'SoLuong',
        'Gia',
    ];
    // Quan hệ với bảng DonHang (Một chi tiết đơn hàng thuộc về một đơn hàng)
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'DonHangId');
    }

    // Quan hệ với bảng SanPham (Một chi tiết đơn hàng thuộc về một sản phẩm)
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'SanPhamId');
    }
}
