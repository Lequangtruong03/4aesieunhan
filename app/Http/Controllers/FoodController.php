<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    function __construct(){
        $cloud_name = cloud_name();
        view()->share('cloud_name',$cloud_name);
    }
    public function food()
    {
        $food = Food::orderBy('id', 'DESC')->Paginate(10);
        return view('admin.food.list', ['food' => $food]);
    }

    public function postCreate(Request $request)
    {
        if (!$request->hasFile('Image')) {
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng chọn ảnh'
            ]);
        }

        $file = $request->file('Image');

        $upload = Cloudinary::upload($file->getRealPath(),[
            'folder'=>'food'
        ]);

        $image = $upload->getSecurePath();

        $food = new Food();
        $food->name = $request->name;
        $food->image = $image;
        $food->price = $request->price;
        $food->save();

        return response()->json([
            'status' => 1,
            'image' => $image,
            'name' => $food->name,
            'price' => $food->price
        ]);
    }

    public function postEdit(Request $request, $id)
    {
        $food = Food::find($id);

        // validate tên sản phẩm
        $request->validate([
            'name' => 'required'
        ],[
            'name.required' => "Please enter Food's name"
        ]);

        // upload ảnh nếu có
        if ($request->hasFile('Image')) {

            $file = $request->file('Image');
            $img = $request['image'] = $file;

            $upload = Cloudinary::upload($img->getRealPath(), [
                'folder' => 'food',
            ]);

            $food->image = $upload->getSecurePath();
        }

        // cập nhật dữ liệu
        $food['name'] = $request['name'];
        $food['price'] = $request['price'];

        $food->update();

        return redirect('admin/food')->with('success', "Cập nhật thành công!");
    }

    public function delete($id)
    {
        $food = Food::find($id);
        Cloudinary::destroy($food['image']);
        $food->delete();
        return response()->json(['success' => 'Delete Successfully']);
    }
    public function status(Request $request){
        $food = Food::find($request->food_id);
        $food['status'] = $request->active;
        $food->save();
        return response('success',200);
    }
}
