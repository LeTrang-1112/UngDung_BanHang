<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThuongHieu extends Model
{
    use HasFactory;
    protected $table = 'thuonghieu';
    protected $primaryKey = 'ThuongHieuId';
    protected $keyType = 'int';
    public $timestamps = false;

    // Quan hệ với bảng Sản phẩm (Một thương hiệu có thể có nhiều sản phẩm)
    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'ThuongHieuId');
    }


}
