<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WechatUserModel;
use Illuminate\Support\Facades\Redis;
use Session;
use App\Tools\Wechat;

class   LoginController extends Controller
{
    //带参数二维码
    public function Dimension (){
        $status=md5(\uniqid());
        //dd($code);
        $access_token=Wechat::getToken();
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
       //dd($url);
        $postData='{"expire_seconds": 2592000, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$status.'"}}}';
        //dd($postData);
        $res=Wechat::curlPost($url,$postData);
        $res=json_decode($res,true);
        //dd($res);
        $ticket=$res['ticket'];
        //dd($ticket);
        $Dimension="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        // dd($Dimension);
        return view("index/list",['Dimension'=>$Dimension,'status'=>$status]);
    }
    public function  userLogin(Request $request){
        //链接接口
        //    $res=$request->echostr;  
        //    echo $res;die;
        //    $echostr=request()->echostr;
        //    if(!empty($echostr)){ 
        //       echo $echostr;
        //    } 
        $xmlObj=file_get_contents("php://input"); 
        file_put_contents("1.txt",$xmlObj);
        $xmlObj=simplexml_load_string($xmlObj,"SimpleXMLElement",LIBXML_NOCDATA);
           //看用户是否关注
           if($xmlObj->MsgType=="event"&&$xmlObj->Event=="subscribe"){
            //获取openid
            $openId=(string)$xmlObj->FromUserName;
            //dd($openId);
            //获取二维码标识
            $EventKey=(string)$xmlObj->EventKey;
            $status=ltrim($EventKey,'qrscene_');
            if(!empty($status)){
               //带参数关注事件
               \Cache::put($status,$openId,20);
               //回复文本消息
                $msg="正在扫描登录，耐心等待";
               Wechat::responseText($msg,$xmlObj);
            }
      }
      //判断用户关注过
      if($xmlObj->MsgType=="event"&&$xmlObj->Event=="SCAN"){
         //获取openid
         $openId=(string)$xmlObj->FromUserName;
         //获取二维码
         $status=(string)$xmlObj->EventKey;
         if(!empty($status)){
            //带参数关注事件
            //file_put_contents('1.php',$status);
            \Cache::put($status,$openId,20);
            //回复文本消息
            echo $status;
             $msg="已关注扫描登录，耐心等待";
            Wechat::responseText($msg,$xmlObj);
         }
      }
     }

}