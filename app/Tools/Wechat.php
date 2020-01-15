<?php
namespace App\Tools;
use App\Index\Advice;
use App\Index\Media;
class Wechat{
    //回复文本消息
  const addID="wxf059eac379aa6da3";
  const appsecret="4d62def70824aff841248b84239b8919";
   public static function responseText($msg,$postObj){
    echo "<xml>
    <ToUserName><![CDATA[".$postObj->FromUserName."]]></ToUserName>
    <FromUserName><![CDATA[".$postObj->ToUserName."]]></FromUserName>
    <CreateTime>".time()."</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[".$msg."]]></Content>
  </xml>";
   }
public static function getToken(){
  //缓存里有数据之间读取
  //$access_token=\Cache::get("access_token");
 // if(empty($access_token)){
  //缓存里没有数据调用存取
  $array="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".Self::addID."&secret=".Self::appsecret;
  $data=file_get_contents($array);
  $data=json_decode($data,true);
  $access_token=$data['access_token'];
  //获取到的id发的危机里
  \Cache::put("access_token",$access_token,7200);
//}
      return $access_token;
}
//curl get
public static function curlGet($url)
{
	//初始化： curl_init
	$ch = curl_init();
	//设置	curl_setopt
	curl_setopt($ch, CURLOPT_URL, $url);  //请求地址
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //返回数据格式
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
	//执行  curl_exec
	$result = curl_exec($ch);
	//关闭（释放）  curl_close
	curl_close($ch);
	return $result;
}
//curl post
public static function curlPost($url,$postData)
{
	//初始化： curl_init
	$ch = curl_init();
	//设置	curl_setopt
	curl_setopt($ch, CURLOPT_URL, $url);  //请求地址
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //返回数据格式
	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
   	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
   	//访问https网站 关闭ssl验证
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
	//执行  curl_exec
	$result = curl_exec($ch);
	//关闭（释放）  curl_close
	curl_close($ch);
	return $result;
}
//图片
	public static function img($media_id,$postObj){

		echo"<xml>
        <ToUserName><![CDATA[".$postObj->FromUserName."]]></ToUserName>
        <FromUserName><![CDATA[".$postObj->ToUserName."]]></FromUserName>
        <CreateTime>".time()."</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        <Image>
          <MediaId><![CDATA[$media_id]]></MediaId>
        </Image>
      </xml>";
	}
	//获取用户基本信息
	public static function getUser($openid){
		 //接受Token
		 $access_token=self::getToken();
		 $usr="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
		  $userInfo=file_get_contents($usr);
		  $userInfo=json_decode($userInfo,true);

		  return $userInfo;
	}
	//获取一周天气
	public static function getWeather($city){
		$arr="http://api.k780.com/?app=weather.future&weaid={$city}&&appkey=46451&sign=d26a5578e433c580c247e78984bdf656&format=json";
        $data=file_get_contents($arr);
       $data=json_decode($data,true);
      $msg="";
      foreach($data['result'] as $key => $value){
        $msg .= $value['days']." ".$value['citynm']." ".$value['week']." ".$value['temperature']." ".$value['weather']."\r\n";
	}
		return $msg;
	} 
	//素材添加
	public static function uploadMedia($logo,$media_format){
		$access_token=self::getToken();
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=".$media_format;
       ; 
        $postData['media']=new \CURLFile($logo);
        // dd($postData)
        $res=Wechat::curlPost($url,$postData);
		$res=\json_decode($res,true);
		$wechat_media_id=$res['media_id'];
       return $wechat_media_id;
	}

	//微信绑定账号
/**
     * 网页授权获取用户openid
     * @return [type] [description]
     */
    public static function getOpenid()
    {
        //先去session里取openid 
        $openid = session('openid');
        //var_dump($openid);die;
        if(!empty($openid)){
            return $openid;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::addID."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::addID."&secret=".self::appsecret."&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            //获取到openid之后  存储到session当中
            session(['openid'=>$openid]);
            return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
        }   
    }

    /**
     * 网页授权获取用户基本信息
     * @return [type] [description]
     */
    public static function getOpenidByUserInfo()
    {
        //先去session里取openid 
        $userInfo = session('userInfo');
        //var_dump($openid);die;
        if(!empty($userInfo)){
            return $userInfo;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::addID."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::addID."&secret=".self::appsecret."&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            $access_token = $data['access_token'];
            //获取到openid之后  存储到session当中
            //session(['openid'=>$openid]);
            //return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
            $userInfo = file_get_contents($url);
            $userInfo = json_decode($userInfo,true);
            //返回用户信息
            session(['userInfo'=>$userInfo]);
            return $userInfo;
        }   
    }






}