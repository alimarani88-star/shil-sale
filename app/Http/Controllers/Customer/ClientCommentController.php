<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientCommentController extends Controller
{
    public function create_comment(Request $request)
    {
        if(Auth::check()){
            $request->validate([
                'post_id' => 'required|exists:posts,id',
                'reply'  =>  'nullable|exists:comments,id',
                'comment' => 'required|string|min:3',
            ]);

            try {
                if (Comment::where('user_id', Auth::id())
                    ->where('process_id', $request->input('post_id'))
                    ->where('status', 'pending')
                    ->whereDate('created_at', now()->format('Y-m-d'))
                    ->exists()) {
                    return redirect()->back()->with('error', 'شما روز جاری نظری ارسال کرده‌اید و در حال بررسی است.');
                }
                $comment = Comment::create([
                    'module'=> 'post',
                    'process_id'=> $request->input('post_id'),
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'content' => strip_tags($request->input('comment')),
                    'status' => 'pending',
                    'reply' => $request->input('reply')
                ]);

                if($comment){
                    return redirect()->back()->with('success' , 'دیدگاه شما ثبت شد و منتظر تایید ادمین است.');
                }else{
                    return redirect()->back()->with('error' , 'مشکلی در ثبت دیدگاه بوجود آمد.');
                }
            }catch (\Exception $exception){

                return redirect()->back()->with('error', 'خطای غیر منتظره لطفا دوباره تلاش کنید.');
            }
        }

    }
}
