<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DonHangController;

//Hiển thị sản phẩm ở trang chính
Route::get('/products', [ProductController::class, 'ThongTinSanPham']);
//Hiển thị sản phẩm giảm giá
//Hiển thị sản phẩm theo thương hiệu
//Hiển thị thông tin chi tiết sản phẩm
Route::get('/product/{id}', [ProductController::class, 'ChiTietSanPham']);
//Thêm sản phẩm vào giỏ hàng
Route::post('/cart', [DonHangController::class, 'addToCart']);
//Hiển thị thông tin sản phẩm trong giỏ hàng
Route::get('/showCart', [DonHangController::class, 'showCart']);
//Xóa tất cả sản phẩm trong giỏ hàng
Route::delete('/deletecart', [DonHangController::class, 'removeCartItems']);
//Xóa danh sách sản phẩm trong giỏ hàng
Route::delete('/listdeletecart', [DonHangController::class, 'removeCartItem']);
//Cập nhật giỏ hàng
Route::post('/cart/updatequantity', [DonHangController::class, 'updateCartItemQuantity']);
//Chuyển dữ liệu qua trang thanh toán
Route::post('/showPayment', [DonHangController::class, 'showPaymentInfo']);
//Đặt hàng
Route::post('/oder', [DonHangController::class, 'placeOrder']);
//Xem lịch sử mua hàng
Route::get('/orders/{nguoiDungId}', [DonHangController::class, 'getOrdersByUser']);
//Hủy đơn hàng
Route::delete('/orders/{donHangId}/cancel', [DonHangController::class, 'cancelOrder']);

