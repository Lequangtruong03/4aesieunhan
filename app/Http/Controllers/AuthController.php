<?php

namespace App\Http\Controllers;

use App\Models\Info;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(){
        $info = Info::find(1);
        view()->share('info', $info);
    }
    public function signIn(Request $request)
    {
        $request->validate(
            [
                'username' => 'required',
                'password' => 'required'
            ],
            [
                'username.required' => 'Vui lòng nhập email hoặc số điện thoại!',
                'password.required' => 'Vui lòng nhập mật khẩu!'
            ]
        );
        $email = Auth::attempt(['email' => $request['username'], 'password' => $request['password']]);
        $phone = Auth::attempt(['phone' => $request['username'], 'password' => $request['password']]);

        if ($email || $phone) {
            if(Auth::user()->hasRole('admin'))
            {
                Auth::logout();
                return redirect('/')->with('warning','Tài khoản không hợp lệ !');
            }
            if($request->has('rememberme')){
                session(['username_web'=>$request->username]); // $request->session()->put('key','value');
                session(['password_web'=>$request->password]);
            }else{
                session()->forget('username_web');
                session()->forget('password_web');
            }
            return redirect($request->url)->with('success','Chào mừng bạn '.Auth::user()->fullName.' !');
        } else {
            return redirect('/')->with('warning','Sai tài khoản hoặc mật khẩu');
        }
    }

    public function signUp(Request $request)
    {
        $request->validate([
            'fullName' => 'required|min:1',
            'email' => 'nullable|required_without:phone|max:255|unique:users,email',
'phone' => 'nullable|required_without:email|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:12|unique:users,phone',
            'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
            'repassword' => 'required|same:password',
        ], [
            'fullName.required' => 'Vui lòng nhập họ tên',
            'email.required_without' => 'Vui lòng nhập mail hoặc số điện thoại ',
            'email.unique' => 'Email đã tồn tại ',
            'phone.min'=>'Vui lòng nhập tối thiểu 10 số',
            'phone.max'=>'Chỉ được nhập tối đa 12 số',
            'password.regex'=>'Mật khẩu phải có ít nhất 1 chữ hoa,1 chữ thường,1 số và độ dài tối thiểu 6 kí tự',
            'phone.required_without' => 'Vui lòng nhập mail hoặc số điện thoại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'repassword.required' => 'Vui lòng nhập lại mật khẩu',
            'repassword.same' => "Mật khẩu nhập lại không trùng khớp",
        ]);
        $user = new User([
            'fullName'=>$request['fullName'],
            'password'=>bcrypt($request['password']),
            'email'=>$request['email'],
            'phone'=>format_phone_number($request['phone']),
            'code'=>rand(1000000000000000, 9999999999999999),
            'point'=>0,
        ]);
        $user->save();
        $user->syncRoles('user');
        $token = Str::random(20);
        $to_email = $request['email'];
        $link_verify = url('/verify-email?email='.$to_email.'&token='.$token);
        if(isset($request->email)){
            Mail::send('web.pages.verify_account_mail', [
                'to_email' => $to_email,
                'link_verify'=>$link_verify,
            ], function ($email) use ($to_email) {
                $email->subject('Kích hoạt tài khoản: '.$to_email);
                $email->to($to_email);
            });
            return redirect('/')->with('success', 'Đăng ký thành công, vui lòng kiểm tra email để kích hoạt tài khoản !');
        }else{
            return redirect('/')->with('success', 'Đăng ký thành công tài khoản !');
        }
    }

    public function signOut()
    {
        Auth::logout();
        return redirect('/')->with('success','Đăng xuất thành công');
    }

    public function changePassword(Request $request){
        $user = User::find(Auth::user()->id);
        if(Hash::check($request['oldpassword'], $user->password)){
            $request->validate([
                'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'repassword' => 'required|same:password'
            ],[
                'password.regex'=>'Mật khẩu phải có ít nhất 1 chữ hoa,1 chữ thường,1 số và độ dài tối thiểu 6 kí tự',
                'password.required' => 'Vui lòng nhập mật khẩu mới',
                'repassword.required' => 'Vui lòng nhập lại mật khẩu',
                'repassword.same' => "Mật khẩu nhập lại không trùng khớp !"
            ]);
            if($request['password'] == $request['oldpassword']){
                return redirect('/profile')->with('danger',"Mật khẩu mới trùng với mật khẩu cũ !");
            }
            $user['password'] = bcrypt($request['password']);
            $user->save();
        }else{
            return redirect('/profile')->with('warning',"Mật khẩu cũ không đúng !");
        }
        return redirect('/signOut')->with('success','Cập nhật mật khẫu thành công');
    }
    public function forgot_password(Request $request){
        $user_email = User::where('email','=',$request['email'])->first();

        if($user_email){
            $token = Str::random(20);
            $user = User::find($user_email->id);
            $user->remember_token = $token;
            $user->save();

            //send mail
            $to_email = $request['email'];
            $name = $user->fullName;
            $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y');

            $link_reset_password = url('/update-password?email='.$to_email.'&token='.$token);
            if(isset($user_email['email']) && $user_email['email_verified'] == 1)
            {
                Mail::send('web.pages.reset_password_mail', [
                    'name' => $name,
                    'to_email' => $to_email,
                    'now'=>$now,
                    'link_reset_password'=>$link_reset_password,
                ], function ($email) use ($name,  $to_email,$now) {
                    $email->subject('Xác nhận cập nhật lại mật khẩu ngày: '.$now);
                    $email->to($to_email, $name);
                });
                return redirect()->back()->with('success','Vui lòng kiểm tra email để reset mật khẩu !');
            }
            else if(isset($user_email['email']) && $user_email['email_verified'] == 0)
            {
                return redirect()->back()->with('warning','Email chưa được kích hoạt trong hệ thống nên không thể reset mật khẩu !');
            }else{
                return redirect()->back()->with('warning','Email không tồn tại trong hệ thống !');
            }
        }else{
            return redirect()->back()->with('warning','Email chưa được đăng ký trong hệ thống !');
        }
    }
    public function update_password(){
        return view('web.pages.update_password');
    }
    public function Post_update_password(Request $request){
        $token = Str::random(20);
        $user = User::where('email','=',$request['email'])->where('remember_token','=',$request['token'])->first();
        if($user){
            $reset = User::find($user->id);
            $request->validate([
                'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'repassword' => 'required|same:password'
            ],[
                'password.regex'=>'Mật khẩu phải có ít nhất 1 chữ hoa,1 chữ thường,1 số và độ dài tối thiểu 6 kí tự',
                'password.required' => 'Vui lòng nhập mật khẩu mới !',
                'repassword.required' => 'Vui lòng nhập lại mật khẩu !',
                'repassword.same' => "Mật khẩu nhập lại không khớp !"
            ]);
            if(Hash::check($request['password'],$reset['password'])){
                return redirect()->back()->with('danger','Mật khẩu mới trùng với mật khẩu cũ');
            }
            $reset['password'] = bcrypt($request['password']);
            $reset['remember_token'] = $token;
            $reset->save();
            return redirect('/')->with('success','Thay đổi mật khẩu thành công');
        }else{
            return redirect('/')->with('warning','Vui thử lại vì đường dẫn hết thời gian sử dụng');
        }
    }
    public function verify_email(){
        $token = Str::random(20);
        $email = $_GET['email'];
        $user = User::where('email','=',$email)->first();

        if($user){
            $verify = User::find($user->id);
            $verify['email_verified'] = 1;
            $verify['remember_token'] = $token;
            $verify->save();
            if(Auth::check())
            {
                $name = Auth::user()->fullName;
                return redirect('/profile')->with('success','Kích hoạt email cho tài khoản '.$name.' thành công !');
            }
            return redirect('/')->with('success','Kích hoạt email thành công');
        }
        else{
            return redirect('/')->with('warning','Vui lòng thử lại vì đường dẫn hết thời gian sử dụng');
        }
    }
}
