<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\GioHang;
use App\Models\ChiTietGioHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DonHangController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'SanPhamId' => 'required|exists:SanPham,SanPhamId',
                'SoLuong' => 'required|integer|min:1',
                'NguoiDungId' => 'required|exists:GioHang,NguoiDungId',
            ]);
    
            $sanPhamId = $validatedData['SanPhamId'];
            $soLuong = $validatedData['SoLuong'];
            $nguoiDungId = $validatedData['NguoiDungId'];
    
            $sanPham = SanPham::find($sanPhamId);
            if (!$sanPham) {
                return response()->json([
                    'error' => 'Sản phẩm không tồn tại',
                ], 404);
            }
    
            if ($sanPham->SoLuongTon < $soLuong) {
                return response()->json([
                    'error' => 'Số lượng sản phẩm trong kho không đủ',
                ], 400);
            }
    
            $gioHang = GioHang::firstOrCreate(['NguoiDungId' => $nguoiDungId], ['TongTien' => 0]);
    
            $chiTietGioHang = ChiTietGioHang::firstOrNew(
                ['GioHangId' => $gioHang->GioHangId, 'SanPhamId' => $sanPhamId]
            );
    
            $tongSoLuong = $chiTietGioHang->SoLuong + $soLuong;
            if ($sanPham->SoLuongTon < $tongSoLuong) {
                return response()->json([
                    'error' => 'Số lượng sản phẩm trong kho không đủ',
                ], 400);
            }
    
            $chiTietGioHang->SoLuong += $soLuong;
            $chiTietGioHang->Gia = $chiTietGioHang->SoLuong * $sanPham->Gia;
            $chiTietGioHang->save();
    
            $gioHang->TongTien = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)->sum('Gia');
            $gioHang->save();
    
            $sanPham->SoLuongTon -= $soLuong;
            $sanPham->save();
    
            return response()->json([
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
                'cart' => $gioHang,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã xảy ra lỗi, vui lòng thử lại sau',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function showCart(Request $request)
    {
        $nguoiDungId = $request->input('NguoiDungId');

        // Kiểm tra giỏ hàng của người dùng
        $gioHang = GioHang::where('NguoiDungId', $nguoiDungId)->first();

        if (!$gioHang) {
            return response()->json([
                'message' => 'Giỏ hàng của bạn hiện đang trống.',
                'cart' => null,
            ]);
        }

        // Lấy thông tin chi tiết giỏ hàng
        $items = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)
            ->join('SanPham', 'ChiTietGioHang.SanPhamId', '=', 'SanPham.SanPhamId')
            ->leftJoin('HinhAnhSanPham', 'SanPham.SanPhamId', '=', 'HinhAnhSanPham.SanPhamId')
            ->select(
                'ChiTietGioHang.CTGHId as cart_item_id',
                'SanPham.SanPhamId as product_id',
                'SanPham.TenSanPham as name',
                'HinhAnhSanPham.DuongDan as image',
                'SanPham.Gia as price',
                'ChiTietGioHang.SoLuong as quantity'
            )
            ->get();

        return response()->json([
            'cart_id' => $gioHang->GioHangId,
            'total_price' => $gioHang->TongTien,
            'items' => $items,
        ]);
    }
    //XÓA SẢN PHẨM KHỎI GIỎ HÀNG
    public function removeCartItems(Request $request)
    {
        // Lấy ID người dùng và ID mục giỏ hàng từ request
        $nguoiDungId = $request->input('NguoiDungId');
        $cartItemId = $request->input('GioHangId');

        // Kiểm tra giỏ hàng của người dùng
        $gioHang = GioHang::where('NguoiDungId', $nguoiDungId)->first();
        if (!$gioHang) {
            return response()->json([
                'message' => 'Giỏ hàng của bạn hiện đang trống.',
            ], 404);
        }

        // Kiểm tra mục giỏ hàng cụ thể
        $cartItem = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)
            ->where('CTGHId', $cartItemId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Mục giỏ hàng không tồn tại.',
            ], 404);
        }

        // Xóa mục giỏ hàng
        $cartItem->delete();

        return response()->json([
            'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng thành công.',
        ]);
    }
    public function removeCartItem(Request $request)
{
    // Lấy ID người dùng và danh sách ID sản phẩm trong giỏ hàng từ request
    $nguoiDungId = $request->input('NguoiDungId');
    $ctghIds = $request->input('CTGHIds'); // Danh sách ID sản phẩm trong giỏ hàng

    // Kiểm tra nếu danh sách ID sản phẩm trống
    if (empty($ctghIds) || !is_array($ctghIds)) {
        return response()->json([
            'error' => 'Danh sách sản phẩm cần xóa không hợp lệ.',
        ], 400);
    }

    // Kiểm tra giỏ hàng của người dùng
    $gioHang = GioHang::where('NguoiDungId', $nguoiDungId)->first();
    if (!$gioHang) {
        return response()->json([
            'error' => 'Giỏ hàng của bạn hiện đang trống.',
        ], 404);
    }

    // Lọc các sản phẩm trong giỏ hàng cần xóa
    $deletedItems = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)
        ->whereIn('CTGHId', $ctghIds) // Lọc theo danh sách ID sản phẩm
        ->delete();

    // Kiểm tra nếu không xóa được sản phẩm nào
    if ($deletedItems === 0) {
        return response()->json([
            'error' => 'Không có sản phẩm nào được tìm thấy trong giỏ hàng.',
        ], 404);
    }

    return response()->json([
        'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng thành công.',
        'deleted_count' => $deletedItems, // Số lượng sản phẩm đã xóa
    ]);
}

    public function showPaymentInfo(Request $request)
{
    try {
        // Lấy ID người dùng và danh sách sản phẩm
        $nguoiDungId = $request->input('NguoiDungId');
        $sanPhamList = $request->input('sanPhamList');

        if (empty($sanPhamList)) {
            return response()->json([
                'message' => 'Danh sách sản phẩm không được để trống.',
            ], 400);
        }

        // Kiểm tra thông tin người dùng
        $nguoiDung = GioHang::where('GioHang.NguoiDungId', $nguoiDungId)  // Chỉ rõ bảng GioHang
        ->join('NguoiDung', 'GioHang.NguoiDungId', '=', 'NguoiDung.NguoiDungId')
        ->select('NguoiDung.TenNguoiDung', 'NguoiDung.DiaChi', 'NguoiDung.SoDienThoai')
        ->first();
    

        if (!$nguoiDung) {
            return response()->json([
                'message' => 'Thông tin người dùng không tồn tại.',
            ], 404);
        }

        $totalPrice = 0;
        $items = [];

        // Kiểm tra từng sản phẩm trong danh sách
        foreach ($sanPhamList as $sanPhamData) {
            $sanPhamId = $sanPhamData['SanPhamId'];
            $soLuong = $sanPhamData['SoLuong'];

            $sanPham = SanPham::find($sanPhamId);
            if (!$sanPham) {
                return response()->json([
                    'message' => 'Sản phẩm với ID ' . $sanPhamId . ' không tồn tại.',
                ], 404);
            }

            if ($sanPham->SoLuongTon < $soLuong) {
                return response()->json([
                    'message' => 'Sản phẩm ' . $sanPham->TenSanPham . ' không đủ số lượng trong kho.',
                ], 400);
            }

            $gia = $sanPham->Gia * $soLuong;
            $totalPrice += $gia;

            $items[] = [
                'product_id' => $sanPhamId,
                'name' => $sanPham->TenSanPham,
                'price' => $sanPham->Gia,
                'quantity' => $soLuong,
                'total_price' => $gia,
                'description' => $sanPham->MoTa,
                'image' => $sanPham->HinhAnh,
            ];
        }

        return response()->json([
            'user' => [
                'name' => $nguoiDung->TenNguoiDung,
                'address' => $nguoiDung->DiaChi,
                'phone' => $nguoiDung->SoDienThoai,
            ],
            'order' => [
                'total_price' => $totalPrice,
                'items' => $items,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Đã có lỗi xảy ra.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function placeOrder(Request $request)
{
    try {
        // Validate request data
        $validatedData = $request->validate([
            'NguoiDungId' => 'required|integer',
            'sanPhamList' => 'required|array',
            'sanPhamList.*.SanPhamId' => 'required|integer',
            'sanPhamList.*.SoLuong' => 'required|integer',
            'PhuongThucThanhToan' => 'required|string',
            'DiaChiGiaoHang' => 'required|string',
            'GhiChu' => 'nullable|string',
            'SoDienThoai' => 'required|string',

        ]);

        // Lấy thông tin người dùng và danh sách sản phẩm từ request
        $nguoiDungId = $validatedData['NguoiDungId'];
        $sanPhamList = $validatedData['sanPhamList'];
        $phuongThucThanhToan = $validatedData['PhuongThucThanhToan'];
        $diaChiGiaoHang = $validatedData['DiaChiGiaoHang'];
        $ghiChu = $validatedData['GhiChu'];
        $soDienThoai =(string)$validatedData['SoDienThoai'];
        // Kiểm tra thông tin người dùng
        $nguoiDung = GioHang::where('GioHang.NguoiDungId', $nguoiDungId)
            ->join('NguoiDung', 'GioHang.NguoiDungId', '=', 'NguoiDung.NguoiDungId')
            ->select('NguoiDung.TenNguoiDung', 'NguoiDung.DiaChi', 'NguoiDung.SoDienThoai')
            ->first();

        if (!$nguoiDung) {
            return response()->json([
                'message' => 'Thông tin người dùng không tồn tại.',
            ], 404);
        }

        $totalPrice = 0;
        $items = [];

        // Kiểm tra sản phẩm và tính tổng tiền
        foreach ($sanPhamList as $sanPhamData) {
            $sanPhamId = $sanPhamData['SanPhamId'];
            $soLuong = $sanPhamData['SoLuong'];

            $sanPham = SanPham::find($sanPhamId);
            if (!$sanPham) {
                return response()->json([
                    'message' => 'Sản phẩm với ID ' . $sanPhamId . ' không tồn tại.',
                ], 404);
            }

            if ($sanPham->SoLuongTon < $soLuong) {
                return response()->json([
                    'message' => 'Sản phẩm ' . $sanPham->TenSanPham . ' không đủ số lượng trong kho.',
                ], 400);
            }

            $gia = $sanPham->Gia * $soLuong;
            $totalPrice += $gia;

            $items[] = [
                'SanPhamId' => $sanPhamId,
                'SoLuong' => $soLuong,
                'Gia' => $sanPham->Gia,
                'TotalPrice' => $gia,
            ];
        }

        // Tạo đơn hàng mới
        $donHang = DonHang::create([
            'NguoiDungId' => $nguoiDungId,
            'TongTien' => $totalPrice,
            'PhuongThucThanhToan' => $phuongThucThanhToan,
            'DiaChiGiaoHang' => $diaChiGiaoHang,
            'GhiChu' => $ghiChu,
            'SoDienThoai' =>$soDienThoai
        ]);

        // Thêm chi tiết đơn hàng
        foreach ($items as $item) {
            ChiTietDonHang::create([
                'DonHangId' => $donHang->DonHangId,
                'SanPhamId' => $item['SanPhamId'],
                'SoLuong' => $item['SoLuong'],
                'Gia' => $item['Gia'],
            ]);
        }
        // Xóa sản phẩm khỏi giỏ hàng của người dùng
        foreach ($sanPhamList as $sanPhamData) {
            $sanPhamId = $sanPhamData['SanPhamId'];
        
            // Lấy danh sách các GioHangId của người dùng
            $gioHangIds = GioHang::where('NguoiDungId', $nguoiDungId)
                ->pluck('GioHangId'); // Lấy danh sách các ID giỏ hàng của người dùng
        
            // Xóa sản phẩm trong giỏ hàng
            ChiTietGioHang::whereIn('GioHangId', $gioHangIds) // Kiểm tra với danh sách GioHangId
                ->where('SanPhamId', $sanPhamId)
                ->delete();
        }
        

        // Cập nhật lại số lượng tồn kho
        foreach ($items as $item) {
            $sanPham = SanPham::find($item['SanPhamId']);
            $sanPham->SoLuongTon -= $item['SoLuong'];
            $sanPham->save();
        }

        return response()->json([
            'message' => 'Đặt hàng thành công.',
            'order' => [
                'order_id' => $donHang->DonHangId,
                'total_price' => $totalPrice,
                'items' => $items,
            ],
        ]);

    } catch (\Exception $e) {
        
        return response()->json([
            'message' => 'Đã có lỗi xảy ra.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
//Lịch sử đặt hàng
public function getOrdersByUser($nguoiDungId)
{
    try {
        // Lấy danh sách đơn hàng của người dùng
        $donHangs = DonHang::where('NguoiDungId', $nguoiDungId)
            ->with(['chiTietDonHangs', 'chiTietDonHangs.sanPham'])  // Tải các chi tiết đơn hàng và sản phẩm liên quan
            ->get();

        // Kiểm tra xem người dùng có đơn hàng không
        if ($donHangs->isEmpty()) {
            return response()->json([
                'message' => 'Không có đơn hàng nào của người dùng này.',
            ], 404);
        }

        // Trả về thông tin đơn hàng
        return response()->json([
            'orders' => $donHangs,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Đã có lỗi xảy ra.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function cancelOrder($donHangId)
{
    try {
        // Tìm đơn hàng
        $donHang = DonHang::find($donHangId);

        // Kiểm tra đơn hàng có tồn tại không
        if (!$donHang) {
            return response()->json([
                'message' => 'Đơn hàng không tồn tại.',
            ], 404);
        }

        // Kiểm tra thời gian đặt hàng
        $createdAt = Carbon::parse($donHang->ngaydathang); // Thời gian đặt hàng
        $currentTime = Carbon::now(); // Thời gian hiện tại

        // Tính khoảng cách thời gian
        $diffInHours = $createdAt->diffInHours($currentTime);

        if ($diffInHours > 2) {
            return response()->json([
                'message' => 'Bạn chỉ được hủy đơn hàng trong vòng 2 tiếng kể từ khi đặt hàng.',
            ], 400);
        }

        // Lấy tất cả các chi tiết đơn hàng
        $chiTietDonHang = ChiTietDonHang::where('DonHangId', $donHangId)->get();

        // Cập nhật lại số lượng tồn kho cho các sản phẩm
        foreach ($chiTietDonHang as $item) {
            $sanPham = SanPham::find($item->SanPhamId);
            if ($sanPham) {
                // Tăng số lượng sản phẩm trong kho khi hủy đơn hàng
                $sanPham->SoLuongTon += $item->SoLuong;
                $sanPham->save();
            }
        }

        // Xóa các chi tiết đơn hàng
        ChiTietDonHang::where('DonHangId', $donHangId)->delete();

        // Xóa đơn hàng
        $donHang->delete(); // Hoặc có thể cập nhật trạng thái thành "Đã hủy"

        return response()->json([
            'message' => 'Đơn hàng đã được hủy thành công.',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Đã có lỗi xảy ra.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function updateCartItemQuantity(Request $request)
{
    try {
        $validatedData = $request->validate([
            'NguoiDungId' => 'required|integer|exists:GioHang,NguoiDungId',
            'SanPhamId' => 'required|integer|exists:SanPham,SanPhamId',
            'SoLuong' => 'required|integer|min:1',
        ]);

        $nguoiDungId = $validatedData['NguoiDungId'];
        $sanPhamId = $validatedData['SanPhamId'];
        $soLuongMoi = $validatedData['SoLuong'];

        // Kiểm tra giỏ hàng của người dùng
        $gioHang = GioHang::where('NguoiDungId', $nguoiDungId)->first();
        if (!$gioHang) {
            return response()->json([
                'message' => 'Giỏ hàng không tồn tại.',
            ], 404);
        }

        // Kiểm tra mục giỏ hàng
        $cartItem = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)
            ->where('SanPhamId', $sanPhamId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại trong giỏ hàng.',
            ], 404);
        }

        // Kiểm tra sản phẩm trong kho
        $sanPham = SanPham::find($sanPhamId);
        if ($sanPham->SoLuongTon + $cartItem->SoLuong < $soLuongMoi) {
            return response()->json([
                'message' => 'Số lượng sản phẩm trong kho không đủ.',
            ], 400);
        }

        // Cập nhật số lượng tồn kho
        $sanPham->SoLuongTon += $cartItem->SoLuong - $soLuongMoi;
        $sanPham->save();

        // Cập nhật số lượng và giá trong giỏ hàng
        $cartItem->SoLuong = $soLuongMoi;
        $cartItem->Gia = $soLuongMoi * $sanPham->Gia;
        $cartItem->save();

        // Cập nhật tổng tiền trong giỏ hàng
        $gioHang->TongTien = ChiTietGioHang::where('GioHangId', $gioHang->GioHangId)->sum('Gia');
        $gioHang->save();

        return response()->json([
            'message' => 'Cập nhật số lượng sản phẩm thành công.',
            'cart' => $gioHang,
            'updated_item' => $cartItem,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Đã có lỗi xảy ra.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
