<?php
/*
	*功能：支付宝主动通知调用的页面（服务器异步通知页面）
	*版本：3.1
	*日期：2010-11-23
	'说明：
	'以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
	'该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

*/
///////////页面功能说明///////////////
//创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
//该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
//该页面调试工具请使用写文本函数log_result，该函数已被默认开启，见alipay_notify.php中的函数notify_verify
//WAIT_BUYER_PAY(表示买家已在支付宝交易管理中产生了交易记录，但没有付款);
//WAIT_SELLER_SEND_GOODS(表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货);
//WAIT_BUYER_CONFIRM_GOODS(表示卖家已经发了货，但买家还没有做确认收货的操作);
//TRADE_FINISHED(表示买家已经确认收货，这笔交易完成););
//该服务器异步通知页面面主要功能是：防止订单未更新。如果没有收到该页面打印的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
/////////////////////////////////////
include '../setting.php';
require_once(APPROOT."alipay/class/alipay_notify.php");
require_once(APPROOT."alipay/alipay_config.php");
require_once APPROOT.'util/DBUtil.php';
$alipay = new alipay_notify($partner,$key,$sign_type,$_input_charset,$transport);    //构造通知函数信息
$verify_result = $alipay->notify_verify();  //计算得出通知验证结果

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $dingdan           = $_POST['out_trade_no'];	//获取支付宝传递过来的订单号
    $total             = $_POST['price'];			//获取支付宝传递过来的总价格

	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
	//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
	  $sql="update uorder set  gmt_create=date_format('".$_POST['gmt_create']."','%Y-%c-%d %H:%i:%s')
			 where out_trade_no='".$_POST['out_trade_no']."'";
			try{
				 $conn=DBUtil::getConnection();
                 @mysql_query($sql,$conn) ;//or die(mysql_error());
			}catch(Exception $e){ }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
	//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		   $sb=$_POST['subject'];
		   $bd=$_POST['body'];
		   $ra=$_POST['receive_address'];
		   $rz=$_POST['receive_zip'];
		   $rm=$_POST['receive_mobile'];
		   $pr=$_POST['price'];
		   $be=$_POST['buyer_email'];
		   $gp=$_POST['gmt_payment'];
	       $sql="update uorder set ispay='1' 
	                               ,subject='$sb'
                                   ,body='$bd'
                                   ,price=$pr
                                   ,buyeremail='$be' 
                                   ,gmt_payment=date_format('$gp','%Y-%c-%d %H:%i:%s')   
			      where out_trade_no='$dingdan'";
//			     ,gmt_payment=date_format($gp,'%Y-%c-%d %H:%i:%s') 
//	                              
//	                             
			                       
//			                      
//			        
//	       			    
			try{
				 $conn=DBUtil::getConnection();
                 @mysql_query($sql,$conn) ;//or die(mysql_error());
			}catch(Exception $e){ }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
	//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	else if($_POST['trade_status'] == 'TRADE_FINISHED') {
	//该判断表示买家已经确认收货，这笔交易完成
	
		//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			$sql="update uorder set receive=1  where out_trade_no='".$_POST['out_trade_no']."'";
			try{
				 $conn=DBUtil::getConnection();
                 @mysql_query($sql,$conn) ;//or die(mysql_error());
			}catch(Exception $e){ }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
    else {
		//其他状态判断
        echo "success";

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>