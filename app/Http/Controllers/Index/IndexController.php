<?php
/*
 * @Author: your name
 * @Date: 2020-01-14 20:12:15
 * @LastEditTime : 2020-01-15 15:54:46
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \dengru\app\Http\Controllers\Index\IndexController.php
 */

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WechatUserModel;
use Illuminate\Support\Facades\Redis;
use Session;
use App\Tools\Wechat;

class IndexController extends Controller
{
    
    public function login(){
        return view('index.login');
    }  
    
    public function loginDo(){
        $user_name=request()->input('user_name');
        $user_pwd=request()->input('user_pwd');
        //获取sessionid
        $session_id=Session::getId();
        $error_number='';
        $userData=WechatUserModel::where(['user_name'=>$user_name])->first();
        
        
        $locking_time=$userData['locking_time'];//最后一次错误，锁定时间
        if(!empty($userData)){
            if($userData['user_pwd']!=md5($user_pwd)){ 
                //第一次错误
                if($userData['error_number']==0){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$error_number=1
                    ]);
                    return back()->withErrors(['密码错误,还有2次机会']);
                } 
                //累加
                if($userData['error_number']==1){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$userData['error_number']+1
                    ]);
                    return back()->withErrors(['密码错误,还有1次机会']);
                }
                if($userData['error_number']==2){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$userData['error_number']+1,
                        "locking_time"=>time()+7200 //错误时间+2小时
                    ]);
                    return back()->withErrors(['密码错误,账号被锁定']);
                }    
            }  
        }

        if(time()-$locking_time<7200){
            $mins=ceil(($locking_time-time())/60);
            return back()->withErrors(["账号锁定中".$mins."分钟后进行登录"]);
            // echo "账号锁定中".$mins."分钟后进行登录";exit;
        }

       WechatUserModel::where(['user_name'=>$user_name])->update([
            "error_number"=>0,
            'session_id'=>$session_id,
            "locking_time"=>0,
            "log_time"=>time()+300  //登录时间
        ]);
        
        session(['userInfo'=>$userData]);
        return redirect('list');
    }


    public function list(){
       return view("index/list");
    }
    public function index(){
        $status=request()->input("status");
        //dd($status);
        //缓存里有 登录成功 
        $openid = \Cache::get($status);
        if(!$openid){
            //抛错
            return json_encode(['ret'=>0,'msg'=>'用户未扫码']);
        }
        //查询当前openid是否是新用户
        return json_encode(['ret'=>1,'msg'=>'用户已扫码']);
    
    }

}
