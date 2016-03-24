<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "zhangbailong");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";  
       
				if(!empty($keyword))
                {
              		$msgType = "text";       		
              		$contentStr=wechatCallbackapiTest::tulingRebot($keyword,$fromUsername);       	
              		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
              		echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
    
    /**
     * 调用小黄鸡API
     * Enter description here ...
     */
    public function tulingRebot($keyword,$fromUsername)
    {
    	$keyword=urldecode(urldecode($keyword)); //用户输入的关键字
    	$api_key="fa96b752bc518c2cfa6ff6980bf053a4"; //我的图灵机器人API key    	
    	$api_address="http://www.tuling123.com/openapi/api?key=KEY&info=KEYWORD&userid=USERID"; //图灵机器人API地址
    	$api_address=str_replace('KEY',$api_key,$api_address);
    	$api_address=str_replace('KEYWORD',$keyword,$api_address);
    	$api_address=str_replace('USERID',$fromUsername,$api_address);
    	/*
    	$ch = curl_init(); 
		$timeout = 5; curl_setopt ($ch, CURLOPT_URL,$api_address);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
		$file_contents = curl_exec($ch);
		curl_close($ch); 
		*/
        $array=json_decode(file_get_contents($api_address),true);   
    	return $array['text'];	
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>