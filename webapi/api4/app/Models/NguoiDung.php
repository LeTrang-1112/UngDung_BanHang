<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    use HasFactory;
    protected $table = 'nguoidung';
    protected $primaryKey = 'NguoiDungId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng Giỏ hàng (Một người dùng có nhiều giỏ hàng)
    public function gioHangs()
    {
        return $this->hasMany(GioHang::class, 'NguoiDungId');
    }

    // Quan hệ với bảng Đơn hàng (Một người dùng có nhiều đơn hàng)
    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'NguoiDungId');
    }

    // Quan hệ với bảng Phòng chat (Một người dùng có thể có nhiều phòng chat)
    public function phongChats()
    {
        return $this->hasMany(PhongChat::class, 'NguoiDungId');
    }
}
