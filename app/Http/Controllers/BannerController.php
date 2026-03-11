<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BannerController extends Controller
{
    function __construct()
    {
        $cloud_name = cloud_name();
        view()->share('cloud_name', $cloud_name);
    }

    // Danh sách banner
    public function banners()
    {
        $banners = Banner::orderBy('id', 'DESC')->paginate(10);
        return view('admin.banners.list', compact('banners'));
    }

    // Tạo banner
    public function postCreate(Request $request)
    {
        if (!$request->hasFile('Image')) {
            return redirect('admin/banners')->with('warning', 'Vui lòng nhập hình ảnh');
        }

        $file = $request->file('Image');

        $upload = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'banner'
        ]);

        $banner = new Banner();
        $banner->image = $upload->getSecurePath(); // lưu URL đầy đủ
        $banner->status = 1;
        $banner->save();

        return redirect('admin/banners')->with('success', 'Add Successfully!');
    }

    // Cập nhật banner
    public function postEdit(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        if ($request->hasFile('Image')) {

            $file = $request->file('Image');

            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'banner'
            ]);

            $banner->image = $upload->getSecurePath();
        }

        $banner->save();

        return redirect('admin/banners')->with('success', 'Updated Successfully!');
    }

    // Xóa banner
    public function delete($id)
    {
        $banner = Banner::findOrFail($id);

        // nếu muốn xóa ảnh cloudinary thì phải lưu public_id riêng
        $banner->delete();

        return response()->json(['success' => 'Delete Successfully']);
    }

    // bật tắt status
    public function status(Request $request)
    {
        $banner = Banner::find($request->banner_id);
        $banner->status = $request->active;
        $banner->save();

        return response('success', 200);
    }
}