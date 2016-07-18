/*
 *Author:Vicotor Don  
 *Date  :2016-07-18
 *Description:JavaScript文件，用于处理网页数据和ajax数据交互
 */
/**
 * author：victor
 * date：20160711
 * for what：Describe the details of each month.
 */
$(function(){
	$(".btn.col-sm-8").click(function(){
		var store = $(this).attr("id");
		var sid = "#"+store;
		var clt = "#m"+store+"t";//清空表格
		$(clt).remove();
		$.ajax({
			type:"post",
			dataType:"json",
			async:true,
			url:"/admin/getcon",
			data:{
				Month:store,
			},
			success:function(data){
				if(data.code == "1"){
					//alert(JSON.stringify(data));
					//数据暂存器@begin
					var cadsee = 0;
					var cadhome = 0;
					var homecost = 0;
					var dsum = 0 ;
					var count = 0;
					//数据暂存器@end
					var today = new Array('周日','周一','周二','周三','周四','周五','周六');
					var list = data.msg["rs"];
					var tr = "<table class='tab' id='m"+store+"t'><tr><th>日&nbsp;&nbsp;期</th><th>CAD迷你看图</th><th>CAD迷你家装</th><th>家装预算</th><th>总&nbsp;&nbsp;计</th></tr>";
					$.each(list,function(index,array){
						//需要正则转换的则 此处为 ： var day = new Date(Date.parse(date.replace(/-/g, '/')));
						var day = new Date(Date.parse(array['date']));     
						var mk = day.getDay();
						//当计算数据日期为周日，计算总和与平均值
						if(mk == "0"){
							cadsee += parseInt(array['CADSEE']);
							cadhome += parseInt(array['CADHOME']);
							homecost += parseInt(array['HOMECOST']);
							dsum += parseInt(array['dsum']);
							//alert(homecost);
							count++;
							if(parseInt(count%7) != 0){
								count = parseInt(count%7);
							}
							var avesee = (cadsee/count).toFixed(2);
							var avehome = (cadhome/count).toFixed(2);
							var avecost = (homecost/count).toFixed(2);
							var avedsum = (dsum/count).toFixed(2);
							tr+="<tr><td>"+array['date']+"<span style='color:red;'>|</span>"+today[day.getDay()]+"</td><td>"+array['CADSEE']+"</td><td>"+array['CADHOME']+"</td><td>"+array['HOMECOST']+"</td><td>"+array['dsum']+"</td></tr>";
							//总和
							tr +="<tr><td>周统计</td><td>总和："+cadsee+"<hr style='color:blue; width:100%;'>平均："+avehome+"</td><td>总和："+cadhome+"<hr style='color:#c0c0c0;width:100%;'>平均："+avehome+"</td><td>总和："+homecost+"<hr style='color:blue; width:100%;'>平均："+avecost+"</td><td>总和："+dsum+"<hr style='color:#c0c0c0;width:100%;'>平均："+avedsum+"</td></tr>";
							tr += "<tr><td colspan='5'></td></tr>"
							//计算结束后，所有数据暂存器进行清零处理，重新进行记录数据
							cadsee = 0;
							cadhome = 0;
							homecost = 0;
							dsum = 0 ;
							count = 0;
							//alert(tr);
						}else{//数据累加运算
							cadsee += parseInt(array['CADSEE']);
							cadhome += parseInt(array['CADHOME']);
							homecost += parseInt(array['HOMECOST']);
							dsum += parseInt(array['dsum']);
							count++;
							tr+="<tr><td>"+array['date']+"<span style='color:red;'>|</span>"+today[day.getDay()]+"</td><td>"+array['CADSEE']+"</td><td>"+array['CADHOME']+"</td><td>"+array['HOMECOST']+"</td><td>"+array['dsum']+"</td></tr>";
						}
					});
					tr+="</table><tr></tr>";
					$(sid).parent().append(tr);
				}
			},
			error:function(){
				alert("Error!");
			},
		});
		//$(this).attr();
	});
	$(".btn.btn-warning").click(function(){
		var stid = $(this).attr("id");
		var stid = "#" + stid + "t";
		$(stid).fadeToggle("fast");
	});
	//select-JS，获取数值并对数据进行处理
	$("#month").change(function(){
		var month=$(this).children('option:selected').val();
		$("#tb").empty();
		$.ajax({
				type:"post",
				dataType:"json",
				async:true,
				url:"month",
				data:{
					Month:month,
					Type:"1",
				},
				success:function(data){
					if(data.code == "1"){
							var day = new Date(Date.parse(data.msg['date']));
							var m = day.getMonth() + 1;
							tr = "<tr><th>月&nbsp;&nbsp;份</th><th>CAD迷你看图</th><th>CAD迷你家装</th><th>家装预算</th><th>总&nbsp;&nbsp;计</th></tr>";
							tr += "<tr><td>"+m+"</td><td>"+data.msg['CADSEE']+"</td><td>"+data.msg['CADHOME']+"</td><td>"+data.msg['HOMECOST']+"</td><td>"+data.msg['msum']+"</td></tr>";
							//tr += "<tr><td colspan='5'><button class='btn col-sm-8' id='0"+m+"'style='height:38px;'><span style='color:green;'>点击此处查看当月详情v</span></button><button class='btn btn-warning col-sm-4' id='m0"+m+"' style='height:38px;'><span style='color:red;'>收起|展开^</span></button></td></tr>";
							$("#hid_tb").removeAttr("hidden");
							$(".btn.col-sm-8").attr("id","0"+m);
							$(".btn.btn-warning").attr("id","m0"+m);
							$("#tb").prepend(tr);
					}else if(data.code == "0"){
						tr = "<tr><td><span style='color:red;'>暂无该月份的数据~</span></td></tr>"
						$("#tb").append(tr);
					}									
				},					
			});
	});
});
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
