<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    use HasFactory;
    protected $table = 'khuyenmai';
    protected $primaryKey = 'KhuyenMaiId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng SanPham (Nhiều khuyến mãi có thể áp dụng cho nhiều sản phẩm)
    public function sanPhams()
    {
        return $this->belongsToMany(SanPham::class, 'SanPhamKhuyenMai', 'KhuyenMaiId', 'SanPhamId');
    }
}
