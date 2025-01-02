<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhongChat extends Model
{
    use HasFactory;
    protected $table = 'phongchat';
    protected $primaryKey = 'PhongChatId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng NguoiDung (Một phòng chat thuộc về một người dùng)
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId');
    }

    // Quan hệ với bảng TinNhan (Một phòng chat có thể có nhiều tin nhắn)
    public function tinNhans()
    {
        return $this->hasMany(TinNhan::class, 'PhongChatId');
    }
}
