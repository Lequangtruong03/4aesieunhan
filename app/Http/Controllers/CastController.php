<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CastController extends Controller
{
    function __construct()
    {
        $cloud_name = cloud_name();
        view()->share('cloud_name', $cloud_name);
    }

    // Danh sách diễn viên
    public function cast()
    {
        $cast = Cast::orderBy('id', 'DESC')->paginate(5);
        return view('admin.cast.list', compact('cast'));
    }

    // Tạo cast
    public function postCreate(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Name is required'
        ]);

        if (!$request->hasFile('Image')) {
            return redirect('admin/cast')->with('warning', 'Vui lòng nhập hình ảnh');
        }

        $file = $request->file('Image');

        $upload = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'cast'
        ]);

        $cast = new Cast();
        $cast->name = $request->name;
        $cast->image = $upload->getSecurePath(); // lưu URL ảnh
        $cast->birthday = $request->birthday;
        $cast->national = $request->national;
        $cast->content = $request->contents;

        $cast->save();

        return redirect('admin/cast')->with('success', 'Add Successfully!');
    }

    // Sửa cast
    public function postEdit(Request $request, $id)
    {
        $cast = Cast::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => "Please enter cast's name"
        ]);

        if ($request->hasFile('Image')) {

            $file = $request->file('Image');

            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'cast'
            ]);

            $cast->image = $upload->getSecurePath();
        }

        $cast->name = $request->name;
        $cast->birthday = $request->birthday;
        $cast->national = $request->national;
        $cast->content = $request->contents;

        $cast->save();

        return redirect('admin/cast')->with('success', 'Updated Successfully!');
    }

    // Xóa cast
    public function delete($id)
    {
        $cast = Cast::findOrFail($id);

        $cast->delete();

        return response()->json(['success' => 'Delete Successfully']);
    }
}