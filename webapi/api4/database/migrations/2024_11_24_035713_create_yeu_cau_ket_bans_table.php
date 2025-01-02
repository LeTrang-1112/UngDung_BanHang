<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('YeuCauKetBan', function (Blueprint $table) {
        $table->id();
        $table->integer('maNguoiGui');
        $table->integer('maNguoiNhan');
        $table->enum('trangThai', ['choXuLy', 'chapNhan', 'tuChoi'])->default('choXuLy');
        $table->timestamps();

        $table->foreign('maNguoiGui')->references('id')->on('NguoiDung');
        $table->foreign('maNguoiNhan')->references('id')->on('NguoiDung');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_ket_bans');
    }
};
