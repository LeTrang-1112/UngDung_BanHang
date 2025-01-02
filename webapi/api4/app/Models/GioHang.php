<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;
    protected $table = 'giohang';
    protected $primaryKey = 'GioHangId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng NguoiDung (Một giỏ hàng thuộc về một người dùng)
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId');
    }

    // Quan hệ với bảng Chi tiết giỏ hàng (Một giỏ hàng có thể có nhiều chi tiết giỏ hàng)
    public function chiTietGioHangs()
    {
        return $this->hasMany(ChiTietGioHang::class, 'GioHangId');
    }
}
