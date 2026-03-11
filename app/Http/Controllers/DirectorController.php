<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class DirectorController extends Controller
{
    function __construct()
    {
        $cloud_name = cloud_name();
        view()->share('cloud_name', $cloud_name);
    }

    // danh sách director
    public function director()
    {
        $director = Director::orderBy('id', 'DESC')->paginate(5);
        return view('admin.director.list', compact('director'));
    }

    // tạo director
    public function postCreate(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Name is required',
        ]);

        if (!$request->hasFile('Image')) {
            return redirect('admin/director')->with('warning', 'Vui lòng nhập hình ảnh');
        }

        $file = $request->file('Image');

        $upload = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'director'
        ]);

        $director = new Director();
        $director->name = $request->name;
        $director->image = $upload->getSecurePath();   // lưu URL ảnh
        $director->birthday = $request->birthday;
        $director->national = $request->national;
        $director->content = $request->contents;

        $director->save();

        return redirect('admin/director')->with('success', 'Add Successfully!');
    }

    // sửa director
    public function postEdit(Request $request, $id)
    {
        $director = Director::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => "Please enter director's name"
        ]);

        if ($request->hasFile('Image')) {

            $file = $request->file('Image');

            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'director'
            ]);

            $director->image = $upload->getSecurePath();
        }

        $director->name = $request->name;
        $director->birthday = $request->birthday;
        $director->national = $request->national;
        $director->content = $request->contents;

        $director->save();

        return redirect('admin/director')->with('success', 'Updated Successfully!');
    }

    // xóa director
    public function delete($id)
    {
        $director = Director::findOrFail($id);

        $director->delete();

        return response()->json(['success' => 'Delete Successfully']);
    }
}