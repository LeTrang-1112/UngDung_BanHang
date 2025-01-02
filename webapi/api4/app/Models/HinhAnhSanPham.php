<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhAnhSanPham extends Model
{
    use HasFactory;
    protected $table = 'hinhanhsanpham';
    protected $primaryKey = 'HinhAnhId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng SanPham (Một sản phẩm có thể có nhiều hình ảnh)
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'SanPhamId');
    }
}
