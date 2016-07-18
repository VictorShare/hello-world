/*
 *author：Victor Don
 *date  ：2016-07-18
 *description：此项目是基于yii框架控制器代码，用于处理后台数据
 *associated file：content.js--view文件夹
 */
<?php 
class AdminController extends Controller{
	public function actionLogin(){
		$name = Yii::app()->request->getParam("Phone");
		$pwd  = Yii::app()->request->getParam("Password");
		if(isset($name) && isset($pwd) && $name != '' && $pwd != ''){
			session_start();
			self::adminlogin($name, $pwd);
			$this->redirect("/admin/money");
			exit();
		}
		$this->renderPartial('login');
	}
	
	public function actionIndex(){
		if(self::isLogin()){
			$this->renderPartial("index");
			exit();
		}
		$this->redirect('/admin/login');
	}
	public function actionRequest(){
		$type= Yii::app()->request->getParam("Type");
		
		if(isset($type) && $type != ''){
			if($type == '1'){
				$phone = Yii::app()->request->getParam("Phone");
				if (isset($phone)){
					if(strstr($phone,"@")){
						$usermodel = User::model()->find("mail = :mail",array(":mail"=>$phone));
					}else{
						$usermodel = User::model()->find("phone = :phone",array(":phone"=>$phone));
					}
					
					if($usermodel){
						$vipmodel  = Vip::model()->findAll("user_id = :userid",array(":userid"=>$usermodel["user_id"]));
						$arr["userid"] = $usermodel["user_id"];
						if($vipmodel){
							foreach ($vipmodel as $_v){
								$arr["list"][] = array(
										"id"=>$_v["vip_id"],
										"product"=>$_v["product_flag_id"],
										"isvip"  =>$_v["isvip"],
										"endtime"=>date("Y-m-d",$_v["end_time"])
								);
							}
							Tools::req_hander(1,$arr);
						}else{
							$userid = $arr["userid"];
							Tools::req_hander(2,$userid);
						}
						//Tools::req_hander(1,$arr);//?
					}			
				}
			}
		}
		Tools::req_hander(0);
	}
	//判断是否登录
	public static function isLogin(){
		if(!isset($_SESSION)){
			session_start();
		}
		if(isset($_SESSION["S_CAD_ADMINAPP_NAME"]) && !empty($_SESSION["S_CAD_ADMINAPP_NAME"])
				&& isset($_SESSION["S_CAD_ADMINAPP_PWD"]) && !empty($_SESSION["S_CAD_ADMINAPP_PWD"]) ){
			return true;
		}
		else if(isset($_COOKIE["C_CAD_ADMINAPP_NAME"]) && !empty($_COOKIE["C_CAD_ADMINAPP_NAME"]) &&
				isset($_COOKIE["C_CAD_ADMINAPP_PWD"]) && !empty($_COOKIE["C_CAD_ADMINAPP_PWD"]) ){
			$res=$this->userLogin($_COOKIE["C_CAD_ADMINAPP_NAME"],$_COOKIE["C_CAD_ADMINAPP_PWD"]);
			if($res){
				$_SESSION["S_CAD_ADMINAPP_NAME"]=$_COOKIE["C_CAD_ADMINAPP_NAME"];
				$_SESSION["S_CAD_ADMINAPP_PWD"]=$_COOKIE["C_CAD_ADMINAPP_PWD"];
				return true;
			}
		}
		return false;
	}
	//登录
	public static function adminlogin($name,$password){
		if($name == 'cadadmin' && $password == 'aec188cad'){
			//session_start();
			$_SESSION["S_CAD_ADMINAPP_NAME"]=$name;
			$_SESSION["S_CAD_ADMINAPP_PWD"]=md5($password);
				
			$expire = time() + 3*24*3600;
			setcookie("C_CAD_ADMINAPP_NAME", $name, $expire,"/");
			setcookie("S_CAD_ADMINAPP_PWD",md5($password),$expire,"/");
		}
	}
	public function actionMoney(){
		if(self::isLogin()){
			$this->render('money');
			exit();
		}
		$this->renderPartial('login');
	}
	//后台管理页面
	public static function actionCheck(){
		$type = Yii::app()->request->getParam("Type");
		$dt = Yii::app()->request->getParam("Time");
		$start = date("Y-m-d H:i:s",strtotime($dt));
		$end = date("Y-m-d H:i:s",strtotime("$start +1 day"));
		$money = 0;
		if (!isset($type)||($type == "") ||  !isset($dt)||($dt == "")){
			Tools::req_hander(0);
		}elseif($type == "vip"){
			$rs = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end)",array(":start"=>$start,":end"=>$end));
			foreach ($rs as $cont){
				$money+=$cont['money'];
			}
			$vipCharge = $money;
			Tools::req_hander(1,$vipCharge);
		}else{
			$rs = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>$type));
			foreach ($rs as $cont){
				$money+=$cont['money'];
			}
			$Charge = $money;
			Tools::req_hander(1,$Charge);
		}
	}
	public static function actionChpwd(){//修改密码
		$pwd = Yii::app()->request->getParam("Pwd");
		$userid = Yii::app()->request->getParam("Userid");
		if ($pwd == '' || $userid == ''){
			Tools::req_hander(0);
		}else{
			$rs = User::model()->updateAll(array("password"=>md5($pwd)),"user_id=:userid",array(":userid"=>$userid));
			$tip = $pwd;
			Tools::req_hander(1,$tip);
		}
	}
	public static function actionChtime(){
		$time = Yii::app()->request->getParam("Time");
		$time = strtotime($time);
		$vipid = Yii::app()->request->getParam("Vipid");
		
		if ($time == '' || $vipid == ''){
			Tools::req_hander(0);
		}else{
			$vipmodel = Vip::model()->updateAll(array("end_time"=>$time),"vip_id=:vipid",array(":vipid"=>$vipid));
			if($vipmodel){
				$time = date("Y-m-d",$time);
				Tools::req_hander(1,$time);
			}else {
				Tools::req_hander(0);
			}
			
		}	
	}
	//数据记录默认开始界面
	public function actionShow(){
		$w = date("w",time());
		$d = date("Y-m-d",time());
		switch ($w){
				case "1" :  $d = date("Y-m-d",strtotime("$d - 0 day"));break;
				case "2" :  $d = date("Y-m-d",strtotime("$d - 1 day"));break;
				case "3" :  $d = date("Y-m-d",strtotime("$d - 2 day"));break;
				case "4" :  $d = date("Y-m-d",strtotime("$d - 3 day"));break;
				case "5" :  $d = date("Y-m-d",strtotime("$d - 4 day"));break;
				case "6" :  $d = date("Y-m-d",strtotime("$d - 5 day"));break;
				case "0" :  $d = date("Y-m-d",strtotime("$d - 6 day"));break;
				default: echo "Eeror!";break;
		}
		$type = "1w";
		$this->redirect(array('admin/week','Type'=>$type,'Date'=>$d));
	}
	
	//处理周数据
	public  function actionWeek(){
		$rtype = Yii::app()->request->getParam("Type");//获取查询类型，类型1w查询一周，类型2w查询两周，类型1m查询一个月，类型2m查询两个月，类型all查询所有月份
		$start = Yii::app()->request->getParam("Date");//获取查询开始日期
		if($rtype == "1w"){
			$end = date("Y-m-d",strtotime("$start + 7 day"));
			$type = "0";
		}elseif ($rtype == "2w"){
			$end = date("Y-m-d",strtotime($start));
			$start = date("Y-m-d",strtotime("$start - 14 day"));
			$type = "0";	
		}elseif ($rtype == "1"){
			$start = date("Y-m-d 00:00:00",time());
			$end = date("Y-m-d H:i:s",time());
			//----------------------@begin计算三个软件的总额---------------
			//获取cadsee看图当天的总额
			$cadsee = 0; $cadhome = 0;$homecost = 0;
			$cads = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"CADSEE"));
			if(!isset($cads)){
				$cadsee = 0;
			}else{
				foreach ($cads as $cont){
					$cadsee +=$cont['money'];
				}
			}
			
			//获取cadhome看图当天的总额
			$cadh = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"CADHOME"));
			if(!isset($cadh)){
				$cadhome = 0;
			}else{
				foreach ($cadh as $cont){
					$cadhome +=$cont['money'];
				}
			}
				
			//获取cad看图当天的总额
			$hc = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"HOMECOST"));
			if(!isset($hc)){
				$cadsee = 0;
			}else{
				foreach ($hc as $cont){
					$homecost +=$cont['money'];
				}
			}
			$dsum = $cadhome+$cadsee+$homecost;//查询当天的总额
			$rdata = array("CADSEE"=>$cadsee,"CADHOME"=>$cadhome,"HOMECOST"=>$homecost,"dsum"=>$dsum);
			if(count($rdata) == 0){
				Tools::req_hander(0);
			}else{
				Tools::req_hander(1,$rdata);
			}
			return ;
			//----------------------@end计算结束-------------------------
		}else{
			return array("warning"=>"查询出错！");
		}
		$arr = Sum::findWeek($type, $start, $end);
		$this->renderPartial("week",array("arr"=>$arr));
	}
	
	//处理月数据
	public function actionMonth(){
		$type = Yii::app()->request->getParam("Type");//获取查询模式
		$month = Yii::app()->request->getParam("Month");//查询待查询月份
		$month = date("Y")."-".$month."-"."01";
		if($type == "0"){//查询当前月份
			$startday = date("Y-m-01",time());
			$endday = date("Y-m-d",time());
			$mtype = "0";//月份标记
			$cadsee = 0;
			$cadhome = 0;
			$homecost = 0;
			$dsum = 0;
			//$arr = Sum::findMonth($mtype, $startday, $endday);
			$rs = Sum::model()->findAll("datetype='0' and (date>=:start and date<=:end ) order by date asc",array(":start"=>$startday,":end"=>$endday));
			foreach ($rs as $cont){
				$cadsee+=$cont['CADSEE'];
				$cadhome+=$cont['CADHOME'];
				$homecost+=$cont['HOMECOST'];
				$dsum+=$cont['dsum'];
			}
			
			$arr = array("date"=>$startday,"CADSEE"=>$cadsee,"CADHOME"=>$cadhome,"HOMECOST"=>$homecost,"msum"=>$dsum);
			$this->renderPartial("month",array("arr"=>array("0"=>$arr),"darr"=>$rs));
		}elseif($type == "3"){//查询近期三个月份
			$startday = date("Y-m-01",time());
			$endday = date("Y-m-d",time());
			$mtype = "0";//月份标记
			$cadsee = 0;
			$cadhome = 0;
			$homecost = 0;
			$dsum = 0;
			//$arr = Sum::findMonth($mtype, $startday, $endday);
			$rs = Sum::model()->findAll("datetype='0' and (date>=:start and date<=:end ) order by date asc",array(":start"=>$startday,":end"=>$endday));
			foreach ($rs as $cont){
				$cadsee+=$cont['CADSEE'];
				$cadhome+=$cont['CADHOME'];
				$homecost+=$cont['HOMECOST'];
				$dsum+=$cont['dsum'];
			}	
			$ct = array("date"=>$startday,"CADSEE"=>$cadsee,"CADHOME"=>$cadhome,"HOMECOST"=>$homecost,"msum"=>$dsum);//本月数据数组
			
			$start = date("Y-m-01",time());
			$startday = date("Y-m-01",strtotime("$start - 2 month"));
			$endday = date("Y-m-01",strtotime("$start -1 day"));
			$mtype = "1";
			$arr = Sum::findMonth($mtype, $startday, $endday);
			$this->renderPartial("month",array("arr"=>$arr,"ct"=>$ct));
		}elseif($type == "1"){//查询单个月份
			$startday = $month;
			$endday = $month;
			$mtype = "1";
			$arr = Sum::findMonth($mtype, $startday, $endday);
			if(count($arr) == 0){
				Tools::req_hander(0);
			}else{
				$data = array("date"=>$arr['0']['date'],"CADSEE"=>$arr['0']['CADSEE'],"CADHOME"=>$arr['0']['CADHOME'],"HOMECOST"=>$arr['0']['HOMECOST'],"msum"=>$arr['0']['msum']);
				Tools::req_hander(1,$data);
			}
			return ;
		}
	}
	//更新数据
	public function actionInsdata(){
		/* 	$ch = (time()-strtotime("2016-06-01"))/(3600*24);   *
		 *  $date = "2016-06-01";//全局更新数据，月份区间			*
		 *	for($i = 0; $i <= floor($ch); $i ++){               */
			//echo "<script> if(prompt('Key  Code:')!='123456'){alert('The key code is wrong!');if(confirm('Exit?')){location.href='https://www.baidu.com';}else{location.href='http://www.vipapps.com/admin/insdata';}}</script>";
			$date = Yii::app()->request->getParam("Date");
			$flag = Sum::model()->find("date = :date",array(":date"=>$date));
			if($flag != ""){
				echo "<script>alert('exist~');history.back();</script>";return ;
			}
			$date = date("Y-m-d 00:00:00",strtotime($date));
			$cadsee = 0;//cad看图总和
			$cadhome = 0;//cad家装总和
			$homecost = 0;//预算总和
			$start = date("Y-m-d H:i:s",strtotime($date));
			$end = date("Y-m-d H:i:s",strtotime("$start + 1 day"));
			//获取cadsee看图当天的总额@可以封装
			$cads = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"CADSEE"));
			if(!isset($cads)){
				$cadsee = 0;
			}else{
				foreach ($cads as $cont){
					$cadsee +=$cont['money'];
				}
			}
			
			//获取cadhome看图当天的总额
			$cadh = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"CADHOME"));
			if(!isset($cadh)){
				$cadhome = 0;
			}else{
				foreach ($cadh as $cont){
					$cadhome +=$cont['money'];
				}
			}
			
			//获取cad看图当天的总额
			$hc = Charge::model()->findAll("statu = 1 and (insert_time>:start and insert_time<:end) and product_flag_id = :product_flag_id",array(":start"=>$start,":end"=>$end,":product_flag_id"=>"HOMECOST"));
			if(!isset($hc)){
				$cadsee = 0;
			}else{
				foreach ($hc as $cont){
					$homecost +=$cont['money'];
				}
			}		
			
		    $dsum = $cadhome+$cadsee+$homecost;//查询当天的总额
		    echo "-----------------".$date."-----------------"."<br/>".$cadhome."--".$cadsee."--".$homecost."--".$dsum."<br/>";
			$datetype = "0";//日期种类
			$model = new Sum();
			$model->datetype = $datetype;
			$model->date = $date;
			$model->CADSEE = floor($cadsee);
			$model->CADHOME = floor($cadhome);
			$model->HOMECOST = floor($homecost);
			$model->dsum = floor($dsum);
			$model->save();
			$freeline = $date;//寄存日期

			/*$date = $end;//循环累加，配合全局数据更新*/
			
			$d = date("d",strtotime($freeline));
			echo "<br/>-----------------today:".$d."-----------------<br>";
			if($d >=28){
				$num = date("t",strtotime($freeline));
				echo "<br/>-------------days".$num."-----------------";
				if($d == $num){									//如果当天等于当月的最后一天
					$startm = date("Y-m-01 00:00:00",strtotime($freeline));
					$endm = date("Y:m-d H:i:s",strtotime("$startm + 1 month"));
					echo $startm."		".$endm;
					$type = "CADSEE";
					$mnsum = Sum::sumFb($type, $startm, $endm); //cad迷你看图月总额
					$type = "CADHOME";
					$jzsum = Sum::sumFb($type, $startm, $endm);//cad迷你家装月总额
					$type = "HOMECOST";
					$yssum = Sum::sumFb($type, $startm, $endm);//家装预算月总额
					$sum = $mnsum + $jzsum + $yssum;
					echo "cad迷你看图总额：".$mnsum."cadhome迷你家装总额：".$jzsum."homcost".$yssum;
					//插入月总额数据
					$datetype = "1";
					$model2 = new Sum();
					$model2->datetype = $datetype;
					$model2->date = $startm;
					$model2->CADSEE = ceil($mnsum);
					$model2->CADHOME = ceil($jzsum);
					$model2->HOMECOST = ceil($yssum);
					$model2->msum = ceil($sum);
					if ($model2->save()){
						echo "MSuccess!";
					}else{
						echo "NSuccess!";
					}
				}
			}	
			/*echo "-----------------update success-----------------<br/>";//测试*/
	}
	//获取当月份的详情
	public function actionGetcon(){
		$month = Yii::app()->request->getParam("Month");
		$firstday = "2016-".$month."-01";
		$lastday = date("Y-m-d H:i:s",strtotime("$firstday + 1 month - 1 day"));
		$rs = Sum::model()->findAll("datetype='0' and (date>=:start and date<=:end ) order by date asc",array(":start"=>$firstday,":end"=>$lastday));
		if(count($rs)>0){
			Tools::req_hander(1,array("rs"=>$rs));
		}else{
			Tools::req_hander(0);
		}
	}
}

?>
 
