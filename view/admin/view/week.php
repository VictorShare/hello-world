<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>设计组管理页面</title>
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_URL;?>admin.css" />
	<link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css">
	<script type="text/javascript" src="<?php echo JS_URL;?>jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_URL;?>content.js"></script>
	<script type="text/javascript">
		$(function(){
			$("#bt").click(function(){
				$.ajax({
					type:"post",
					dataType:"json",
					url:"/admin/week",
					async:true,
					data:{
						Type:"1",
					},
					success:function(data){
						if(data.code == "1"){
							//alert(JSON.stringify(data));
							$("#sp1").html(data.msg["CADSEE"]);
							$("#sp2").html(data.msg["CADHOME"]);
							$("#sp3").html(data.msg["HOMECOST"]);
							$("#sp4").html(data.msg["dsum"]);
						}else{
							$("#sp1").html("暂无数据~");
						}
					},	
				});
			});
		});
	</script>
</head>
<body>
	<div class="dtb"><?php 
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
		}?>
		
		<div class="hd" align="center">
			<div class="title2"><a href="<?php echo SITE_URL;?>admin/week?Type=1w&Date=<?php echo $d;?>"><span class="sp">一&nbsp;&nbsp;周</span></a></div>
			<div class="title2"><a href="<?php echo SITE_URL;?>admin/week?Type=2w&Date=<?php echo $d;?>"><span class="sp">两&nbsp;&nbsp;周</span></a></div>
			<div class="title2"><a href="<?php echo SITE_URL;?>admin/month?Type=0"><span class="sp">一个月</span></a></div>
			<div class="title2"><a href="<?php echo SITE_URL;?>admin/month?Type=3"><span class="sp"><span class="sp">三个月</span></a></div>
			<div style="float: left;margin-top:15px;">
			<span style="color:red;font-size:1em;">查询：</span>
			<select id="month" style="width:80px; height:28px;">
				<option value="1">一月份</option>
				<option value="2">二月份</option>
				<option value="3">三月份</option>
				<option value="4">四月份</option>
				<option value="5">五月份</option>
				<option value="6">六月份</option>
				<option value="7">七月份</option>
				<option value="8">八月份</option>
				<option value="9">九月份</option>
				<option value="10">十月份</option>
				<option value="11">十一月份</option>
				<option value="12">十二月份</option>
			</select></div>
		</div>
		<table class="tab" cellspacing="0" id="tb" align="center">
			<tr>
				<th>日&nbsp;&nbsp;期</th><th>CAD迷你看图</th><th>CAD迷你家装</th><th>家装预算</th><th>总&nbsp;&nbsp;计</th>
			</tr>
		<?php $sumcost = 0; $sumhome = 0; $sumsee = 0; $count = 0; $arrnum = count($arr); $storesee = 0;$storehome = 0; $storecost = 0;?>
		<?php foreach ($arr as $v){?>
			<tr>
				<td><?php echo $v["date"]; ?>&nbsp;<span style="color:red;">|</span>&nbsp;周&nbsp;&nbsp;
				<?php 
				$d = date("w",strtotime($v["date"]));
				switch ($d){
					case "1" : echo "一";break;
					case "2" : echo "二";break;
					case "3" : echo "三";break;
					case "4" : echo "四";break;
					case "5" : echo "五";break;
					case "6" : echo "六";break;
					case "0" : echo "日";break;
					default: echo "Eeror!";break;
				}
				?>
				</td>
				<td><?php $cadsee = $v["CADSEE"]; echo $cadsee; $sumsee += $cadsee;?></td>
				<td><?php $cadhome = $v["CADHOME"]; echo $cadhome; $sumhome += $cadhome;?></td>
				<td><?php $homecost = $v["HOMECOST"]; echo $homecost; $sumcost += $homecost;?></td>
				<td><?php echo $v["dsum"]; $count++;?></td>
			</tr>
			<?php 
				if ($count == "7"){
			?>
			<tr>
				<td>周统计</td><td>
				总和：<?php echo $sumsee; $storesee = $sumsee;?>
				<hr style="color:#c0c0c0; width:100%;">
				平均：<?php if($count != "0"){echo round($sumsee/$count,2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php echo $sumhome; $storehome = $sumhome;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($sumhome/$count,2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php echo $sumcost;$storecost = $sumcost;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($sumcost/$count,2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $wsum = $sumcost+$sumhome+$sumsee; echo $wsum;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php echo round($wsum/7,2)?></td>
			</tr>
			<tr></tr>
			<?php if($v != end($arr)){?>
			<tr>
				<th>日&nbsp;&nbsp;期</th><th>CAD迷你看图</th><th>CAD迷你家装</th><th>家装预算</th><th>总&nbsp;&nbsp;计</th>
			</tr>
			<?php }?>
			<?php }elseif(($count < 7) && ($v == end($arr))){?>
				<tr>
					<td><span style="width: 82px;"><button id="bt" class="btn btn-default">实时数据</button></span><span style="color:red;">|</span>&nbsp;&nbsp;<?php echo date("m-d",time());?></td><td><span id="sp1" style="color:blue;"></span></td><td><span style="color:blue;" id="sp2"></span></td><td><span style="color:blue;" id="sp3"></span></td><td><span style="color:blue;" id="sp4"></span></td>
				</tr>
				<tr>
				<td>周统计</td><td>
				总和：<?php $cad = $sumsee-$storesee;echo $cad;?>
				<hr style="color:#c0c0c0; width:100%;">
				平均：<?php if($count != "0"){echo round($cad/($count),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $home = $sumhome-$storehome; echo $home;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($home/($count),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $cost = $sumcost-$storecost; echo $cost;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($cost/($count),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $ssum = $cad+$home+$cost; echo $ssum;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php echo round($ssum/($count),2)?></td>
			</tr>
			<?php }else if(($count > 7) && ($v == end($arr))){?>
			<tr>
				<td>周统计</td><td>
				总和：<?php $cad = $sumsee-$storesee;echo $cad;?>
				<hr style="color:#c0c0c0; width:100%;">
				平均：<?php if($count != "0"){echo round($cad/($count - 7),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $home = $sumhome-$storehome; echo $home;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($home/($count - 7),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $cost = $sumcost-$storecost; echo $cost;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php if($count != "0"){echo round($cost/($count - 7),2);}else{echo "wrong data!";}?>
				</td><td>
				总和：<?php $ssum = $cad+$home+$cost; echo $ssum;?>
				<hr style="color:#c0c0c0;width:100%;">
				平均：<?php echo round($ssum/($count - 7),2)?></td>
			</tr>
			<?php }?>
			<?php }?>
			<!-- 如果今天是周一 -->
			<?php 
				if (date("w",time()) == "1"){
			?>
				<tr>
					<td><span style="width: 82px;"><button id="bt" class="btn btn-default">实时数据</button></span><span style="color:red;">|</span>&nbsp;&nbsp;<?php echo date("m-d",time());?></td><td><span id="sp1" style="color:blue;"></span></td><td><span style="color:blue;" id="sp2"></span></td><td><span style="color:blue;" id="sp3"></span></td><td><span style="color:blue;" id="sp4"></span></td>
				</tr>
			<?php }?>
		</table>
		<table id="hid_tb" class="hidtb" cellspacing="0" hidden>
			<tr><td colspan="5"><button class="btn col-sm-8" id="tb_btn1" style="height:38px;"><span style="color:green;">点击此处查看当月详情v</span></button><button class='btn btn-warning col-sm-4' id="tb_btn2" style='height:38px;'><span style='color:red;'>收起|展开^</span></button></td></tr>
		</table>
		<div class="hd" align="center"><a href="/admin/insdata?Date=<?php $date0 = date("Y-m-d",time()); $t = date("Y-m-d",strtotime("$date0 - 1 day")); echo $t;?>" class="btn btn-default pull-right">点击添加昨天的数据</a>
	</div>
</body>
</html>
