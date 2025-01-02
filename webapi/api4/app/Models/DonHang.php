<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;
    protected $table = 'donhang';
    protected $primaryKey = 'DonHangId';
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'NguoiDungId',
        'TongTien',
        'PhuongThucThanhToan',
        'TrangThai',
        'DiaChiGiaoHang',
        'GhiChu',
        'NgayDatHang',
        'SoDienThoai',
    ];

    // Quan hệ với bảng NguoiDung (Một đơn hàng thuộc về một người dùng)
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId');
    }

    // Quan hệ với bảng Chi tiết đơn hàng (Một đơn hàng có thể có nhiều chi tiết đơn hàng)
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'DonHangId');
    }
}
