﻿<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<!-- Author : @s.anutin -->
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	<title>YTKMini</title>
	<link rel="shortcut icon" href="../public/images/icon-preview.png?v" />
	
	<link rel="stylesheet" href="../public/lobiadmin-master/documentation/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/weather-icons.min.css"/>

	<!--lobiadmin-with-plugins.css contains all LobiAdmin css plus lobiplugins all css files, plus third party plugins-->
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/lobiadmin-with-plugins.css"/>
	
	<style>
		tr th { white-space:nowrap; }
		tr td { white-space:nowrap; }
		tr td.nnr { white-space:normal;text-align:left; }
		
		select:-moz-focusring {
			color: transparent;
			text-shadow: 0 0 0 #000;
		}
		select {
			background: transparent;
		}
		
		.select2-container--open {
			z-index:50000;
		}

		.table > tbody > tr > td
		, .table > tbody > tr > th
		, .table > tfoot > tr > td
		, .table > tfoot > tr > th
		, .table > thead > tr > td
		, .table > thead > tr > th {
			padding: 2px;
			line-height: 1;
			//vertical-align: text-bottom;
			//border-top: 1px solid #ddd;
		}
		
		input[type="number"]:disabled {
			background: #ccc;
		}
		
		.chat {
			padding-left: 10px;
			position:fixed;
			min-width:100px;
			min-height:20px;
			bottom:0px;
			right:10px;
			border-radius: 5px 5px 0px 0px;
			background-color:#ddd;
			box-shadow: -0px -2px 10px #888888;
			z-index:5001;
		}
		
		.chat:hover {
			background-color:#ccc;
			border-radius: 5px 5px 0px 0px;
		}
		
		#chat-title-open {
			cursor:pointer;
		}
	</style>
</head>
<!-- style='background: rgba(0, 0, 0, 0) url("../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png") repeat scroll 0% 0%;' -->
<body class='header-fixed menu-fixed ribbon-fixed' baseUrl='<?php echo $baseUrl; ?>'>
	<nav class="navbar navbar-default navbar-header header">
		<!-- div class="chat col-sm-2">
			<div class="chat-title">
				<div style="float:left;width:calc(100% - 50px);"><b>แจ้งปัญหา</b></div>
				<div id="chat-title-open" class="glyphicon glyphicon-chevron-up" style="float:left;width:50px;" align="right"></div>
			</div>			
			<div class="chat-body" style="cursor:default;" hidden>
				<div class="chat-details" lastLoad="" style="max-height:300px;height:300px;width:100%;background-color:#fff;overflow-y:scroll;">
					<div class="col-sm-12" align="left">
						<div align=left style="word-break:break-all;max-width:90%;width:auto;padding-left:5px;border:0.1px solid #ddd;background-color:#ccc;border-radius: 0px 10px 10px 0px;">
							สวัสดีครับ ต้องการสอบถามข้อมูลด้านไหนครับ
						</div>
					</div>
				</div>
				<textarea id="textchat" rows="1" class="input-sm" style="width:80%;resize:none;float:left;"></textarea>
				<button id="chatSendMSG" style="width:calc(20%);height:50px;float:left;">ส่ง</button>
			</div>
		</div -->
				
		<a class="navbar-brand" href="#">
			<div class="navbar-brand-img"></div>
			<!--<img src="img/logo/lobiadmin-logo-text-white-32.png" class="hidden-xs" alt="" />-->
		</a>
		<!--Menu show/hide toggle button-->
		
		<ul class="nav navbar-nav pull-left show-hide-menu">
			<li>
				<a href="#" class="border-radius-0 btn font-size-lg" data-action="show-hide-sidebar">
					<i class="fa fa-bars"></i>
				</a>
			</li>
		</ul>	
		
		<div class="navbar-items">
			<!--User avatar dropdown-->
			<ul class="nav navbar-nav navbar-right user-actions">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<!--img class="user-avatar" src="../public/lobiadmin-master/version/1.0/ajax/img/users/me-160.jpg" alt="..."/-->
						<div class='glyphicon glyphicon-user'></div>
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<!-- li><a href="#profile"><span class="glyphicon glyphicon-user"></span> &nbsp;&nbsp;ข้อมูลส่วนตัว</a></li>
						<li class="divider"></li -->
						<!-- สาขาเผลอไปกด แล้วไม่เข้าใจ เลยปิดในส่วนของ LockScreen -->
						<!-- li><a href="../clogout/lock"><span class="glyphicon glyphicon-lock"></span> &nbsp;&nbsp;Lock screen</a></li -->
						<li><a href="../clogout"><span class="glyphicon glyphicon-off"></span> &nbsp;&nbsp; ออกจากระบบ</a></li>
						<li class="divider"></li>
					</ul>
				</li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right user-actions">
				<li class="dropdown">
					<a id='LOCATCHANGE' class="dropdown-toggle" data-toggle="dropdown">						
						<div style='font-size:8pt;'><?php echo $branch; ?></div>						
					</a>
					<!--ul class="dropdown-menu">
						<li><a href="#">Fบน</a></li>
					</ul -->
				</li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right user-actions">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">						
						<div style='font-size:8pt;'><?php echo $db; ?></div>						
					</a>
				</li>
			</ul>
			
			<!-- แจ้งเตือน -->
			<ul class="nav navbar-nav navbar-right user-actions">
				<li class="dropdown">
					<!-- a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="glyphicon glyphicon-globe"></span>
						<span id="notify_cnt" class="badge badge-danger badge-xs">5</span>
					</a>
					
					<div class="dropdown-menu dropdown-notifications notification-news border-1 animated-fast flipInX">
						<div class="notifications-heading border-bottom-1 bg-white">
							Notifications
						</div>
						<ul class="notifications-body max-h-300">
							<li class="read">
								<div class="notification">
									TEST<br>TEST<br>TEST<br>TEST<br>
									<p class="body-text"><i class="fa fa-clock-o"></i> 5 นาทีที่แล้ว</p>
									<a href="http://localhost:92/YTKMini/Welcome/#SYS04/Analyze" class="link-action">ตรวจสอบ</a>
								</div>
							</li>
							<li class="unread">
								<div class="notification friend-request">
									<img class="notification-image" src="img/users/4.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading"><a href="#">John Doe</a></h4>
										<h5 class="notification-sub-heading text-nowrap">Friend request 
											<a href="#" class="btn btn-info btn-xs btn-response">Accept</a>
											<a href="#" class="btn btn-danger btn-xs btn-response">Decline</a>
										</h5>
										<p class="body-text"><i class="fa fa-user"></i> 12 hr. ago</p>
									</div>
								</div>
							</li>
							<li class="unread">
								<div class="notification">
									<img class="notification-image" src="img/users/2.jpg" alt="">
									<div class="notification-msg">
										<h5 class="notification-sub-heading"><a href="#">George Darso</a> invited you to join the conference</h5>
										<p class="body-text"><i class="fa fa-clock-o"></i> yesterday</p>
									</div>
									<a href="#" class="link-action">View invitation</a>
								</div>
							</li>
							<li>
								<div class="notification">
									<img class="notification-image" src="img/users/1.jpg" alt="">
									<div class="notification-msg">
										<h5 class="notification-sub-heading">
											<a href="#">Guga Grigo</a> likes your photo 
										</h5>
										<p class="body-text"><i class="fa fa-thumbs-up"></i> Tuesday</p>
									</div>
									<a href="#"><img src="img/users/me-160.jpg" alt="" class="liked-photo"></a>
								</div>
							</li>
							<li>
								<div class="notification">
									<div class="notification-icon"><i class="glyphicon glyphicon-user"></i></div>
									<div class="notification-msg">
										<h5 class="notification-sub-heading"><a href="#">Poll Hunely</a> has accepted your invitation.</h5>
										<p class="body-text"><i class="fa fa-check"></i> yesterday</p>
									</div>
									<a href="#" class="link-action"><i class="fa fa-calendar"></i> Add to calendar</a>
								</div>
							</li>
							<li>
								<div class="notification">
									<div class="notification-icon"><i class="fa fa-users"></i></div>
									<div class="notification-msg">
										<h5 class="notification-sub-heading"><a href="#" data-toggle="tooltip" data-html="true" data-title="Mari Abdu<br>Guga Grigo<br>David Sula" data-original-title="" title="">3 new users</a> has just signed up</h5>
										<p class="body-text"><i class="fa fa-user"></i> Sunday</p>
									</div>
								</div>
							</li>
						</ul>
						<div class="notifications-footer border-top-1 bg-white text-center">
							<input type='checkbox'><a href="#">เฉพาะที่อ่านแล้ว</a>&emsp;
							<a href="#">ยังไม่อ่าน</a>&emsp;
							<a href="#">ทั้งหมด</a>
						</div>
					</div -->
				</li>
			</ul>
		</div>
		
		
		
		<!-- div class="clearfix-xxs"></div>
		<div class="navbar-items-2">
			<ul class="nav navbar-nav navbar-actions">
				<li class="visible-lg">
					<a href="#" data-action="fullscreen">
						<span class="glyphicon glyphicon-fullscreen"></span>
					</a>
				</li>
			</ul>
		</div>
		<div class="clearfix"></div -->
	</nav>
	
		
	
	
	 <div class="menu">		
		<nav>
			<ul>
				<!-- li>
					<a href="#welcome/dashboard">
						<i class="fa fa-home menu-item-icon"></i>
						<span class="inner-text">Dashboard</span>
					</a>
				</li>
				
				<li class="opened">
					<a href="#">
						<i class="fa fa-folder-o" aria-hidden="true"></i>
						<span class="inner-text">MENU</span>
						<i class="menu-item-toggle-icon fa fa-chevron-circle-down"></i>
						<ul style='display:block;'>
							<li>
								<a href="#CHomenew/TypeCar">
									<i class="fa fa-tags" aria-hidden="true"></i>
									<span class="inner-text">ฟอร์มเปลี่ยนกลุ่มรถ</span>
								</a>
							</li>	
						</ul>
					</a>
				</li>
				
				<li>
					<a href="#">
						<i class="fa fa-folder-o" aria-hidden="true"></i>
						<span class="inner-text">MENU RT</span>
					
						<ul>
							<li>
								<a href="#CHomenew/TypeCarxxx">
									<i class="fa fa-tags" aria-hidden="true"></i>
									<span class="inner-text">ฟอร์มเปลี่ยนกลุ่มรถ</span>
								</a>
							</li>	
							
							<li>
								<a href="#">
									<i class="fa fa-folder-o" aria-hidden="true"></i>
									<span class="inner-text">MENU RT</span>
								
									<ul>
										<li>
											<a href="#CHomenew/TypeCarxxxx">
												<i class="fa fa-tags" aria-hidden="true"></i>
												<span class="inner-text">ฟอร์มเปลี่ยนกลุ่มรถ</span>
											</a>
										</li>	
										
										<li>
											<a href="#">
												<i class="fa fa-folder-o" aria-hidden="true"></i>
												<span class="inner-text">MENU RT</span>
											
												<ul>
													<li>
														<a href="#CHomenew/TypeCarxxxxx">
															<i class="fa fa-tags" aria-hidden="true"></i>
															<span class="inner-text">ฟอร์มเปลี่ยนกลุ่มรถ</span>
														</a>
													</li>	
												</ul>
											</a>
										</li>
									</ul>
								</a>
							</li>
						</ul>
					</a>
				</li -->
				<?php echo $menu; ?>
			</ul>
		</nav>
		<div class="menu-collapse-line">
			<!--Menu collapse/expand icon is put and control from LobiAdmin.js file-->
			<div class="menu-toggle-btn" data-action="collapse-expand-sidebar"></div>
		</div>
		<div class="menu-heading">
			<div class="menu-header-buttons-wrapper clearfix">
				<button type="button" class="btn btn-info btn-menu-header-collapse">
					<!-- i class="fa fa-cogs"></i -->
					<div class='glyphicon glyphicon-user'></div>
				</button>
				<!--Put your favourite pages here-->
				<div class="menu-header-buttons">
					<span style='font-size:10pt;' title='<?php echo $name; ?>'><?php echo $name; ?></span>
				</div>
			</div>
		</div>
	</div>
	
	
	<div id="main">
		<!-- style="background-image: url('../public/images/KOI FISH.gif'); background-position: center;" -->
		<div id="ribbon" class="hidden-print">
			<a href="#welcome/dashboard" class="btn-ribbon" data-container="#main" data-toggle="tooltip" data-title="Show dashboard"><i class="fa fa-home"></i></a>
			<span class="vertical-devider">&nbsp;</span>
			<button class="btn-ribbon" data-container="#main" data-action="reload" data-toggle="tooltip" data-title="Reload content by ajax"><i class="fa fa-refresh"></i></button>
			<ol class="breadcrumb">
			</ol>
		</div>
		<div id="content">
		
		</div>
	</div>
		
	<!--Loading indicator for ajax page loading-->
	<!-- div class="spinner spinner-horizontal hide">
		<span class="spinner-text">Loading...</span>
		<div class="bounce1"></div>
		<div class="bounce2"></div>
		<div class="bounce3"></div>
	</div -->
	
	<div id="loadding" hidden style="
		background-image: url('../public/images/KOI FISH.gif');
		background-repeat: no-repeat;
		background-attachment: fixed;
		background-position: center; 
		background-size: cover;
		width:100vw;height:100vh;color:white;background-color:hsla(40, 14%, 21%, 0.59);position:fixed;top:0;left:0;z-index:10000;">
		<div class="spinner spinner-horizontal">
			<span class="spinner-text">Loading...</span>
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>

	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lib/jquery.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lib/jquery-ui.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lobi-plugins/lobibox.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lobi-plugins/lobipanel.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	
	<link href="../public/select2/select2.min.css" rel="stylesheet"/>
	<script src="../public/select2/select2.min.js"></script>
	
	<!--Make sure that config.js file is loaded before LobiAdmin.js-->
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/config.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/LobiAdmin.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/app.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/demo.js"></script>
	
	<!-- script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/select2/select2.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/dataTables.responsive.min.js"></script>
	<!-- script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-datepicker/bootstrap-datepicker.js"></script -->
	
	
	<script src="../public/bootstrap-datepicker-thai/js/bootstrap-datepicker.js"></script>
    <script src="../public/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js"></script>
    <script src="../public/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js"></script>
	
	<script src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/jquery.dataTables.min.js"></script>
	<script src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
	<script src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/datatables/dataTables.responsive.min.js"></script>
	
	<script src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/duallistbox/jquery.bootstrap-duallistbox.js"></script>
	
	<link rel="stylesheet" href="../vendor/snapappointments/bootstrap-select/dist/css/bootstrap-select.css"/>
	<script src="../vendor/snapappointments/bootstrap-select/dist/js/bootstrap-select.js"></script>
	
	<link rel='stylesheet' type='text/css' media='screen' href='../public/upload/uploadfile.css' />
	<script type='text/javascript' src='../public/upload/jquery.uploadfile.min.js'></script>
	
	<!-- show image colorbox 20201019 -->
	<link rel='stylesheet' type='text/css' media='screen' href='../public/colorbox-master/example3/colorbox.css' />
	<script type='text/javascript' src='../public/colorbox-master/jquery.colorbox-min.js'></script>
	
	<script type='text/javascript' src='../node_modules/signature_pad/dist/signature_pad.umd.js'></script>
</body>
<script>
	var jbackdrop = '<div class="jbackdrop" style="position:fixed;z-index:4001;left:0;top:0;width:100%;height:100%;background-color:rgba(0,0,0,.5)"></div>';
	setInterval(function(){
		__decss();
	},250);
	
	function __decss(){
		$("input[type='text']:enabled").css({'background-color':'white','color':'black','cursor':'default'});
		$("input[type='text']:disabled").css({'background-color':'#ccc','color':'black','cursor':'not-allowed'});
		$(".select2-hidden-accessible:enabled").each(function(){
			$("#select2-"+this.id+"-container").css({'background-color':'white','color':'black','cursor':'pointer','height':'28px'});
		});
		$(".select2-hidden-accessible:disabled").each(function(){
			$("#select2-"+this.id+"-container").css({'background-color':'#ccc','color':'black','cursor':'not-allowed','height':'28px'});
		});
		$(".select2-selection--single").css({'height':'30px'});
		
		/* - @อนุญาติให้คีย์ ตัวเลข เท่านั้น */
		$(".jzAllowNumber").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A, Command+A
				(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
				 // Allow: home, end, left, right, down, up
				(e.keyCode >= 35 && e.keyCode <= 40)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});	
	}

	$(".jzAllowNumber").keyup(function (e) {
		var adjust = ($(this).val()).toString().replace(',','');
		adjust = addCommas(adjust);
		$(this).val(adjust);
	});

	function addCommas(str){
	   var arr,int,dec;
	   str += '';

	   arr = str.split('.');
	   int = arr[0] + '';
	   dec = arr.length>1?'.'+arr[1]:'';

	   return int.replace(/(\d)(?=(\d{3})+$)/g,"$1,") + dec;
	}
	
	//const numberWithCommas = (x) => {
	const numberWithCommas = function(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	function fnAjaxERROR(jqXHR,exception){
		var msg = '';
		var delay = 5000;
		var notify = 'error';
        if (jqXHR.status === 0) {
			delay = 3000;
			notify = 'warning';
            msg = 'Not connect.\n Verify Network.<br>โปรดตรวจสอบระบบ Internet ว่าเชื่อมต่อสมบูรณ์หรือไม่';
        } else if (jqXHR.status == 404) {
            delay = false;
			msg = 'Requested page not found. [404]<br>ผิดพลาด ไม่พบหน้าที่คุณร้องขอ';
        } else if (jqXHR.status == 500) {
            delay = false;
			msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
			delay = 3000;
			notify = 'warning';
			msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            delay = 3000;
			notify = 'warning';
			msg = 'Time out error.';
        } else if (exception === 'abort') {
            delay = 3000;
			notify = 'warning';
			msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
		
		Lobibox.notify(notify, {
			title: 'ผิดพลาด',
			size: 'mini',
			closeOnClick: false,
			delay: delay,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: msg
		});
		
		$('#loadding').fadeOut(200);
	}
	
	var setwidth = $(window).width();
	var setheight = $(window).height();
	if(setwidth > 1000){
		setwidth = 1000;
	}else{
		setwidth = setwidth - 50;
	}

	if(setheight > 800){
		setheight = 800;
	}else{
		setheight = setheight - 50;
	}
	
	//$('#content').css({'background-color':'whiteSmoke'});
	
	$("#LOCATCHANGE").click(function(){
		$.ajax({
			url: '../CHomenew/LocatChangeView',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if(data.html['LOCATClaim'] > 0){
					Lobibox.window({
						title: 'สิทธิ์การเข้าถึงสาขา',
						content: data.html['data'],
						shown: function($this){
							document.getElementById("table-fixed-changelocat").addEventListener("scroll", function(){
								var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
								this.querySelector("thead").style.transform = translate;						
							});	
							
							$('.ChangeLOCAT').click(function(){
								dataToPost = new Object();
								dataToPost.LOCAT = $(this).attr('LOCAT');
								$('#loadding').show();
								
								$.ajax({
									url: '../CHomenew/LocatChange',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data){
										if(data.status){
											window.open('<?php echo base_url("welcome/"); ?>','_parent');
											
											$("#LOCATCHANGE").html(dataToPost.LOCAT);
											
											Lobibox.notify('info', {
												title: 'ข้อมูล',
												closeOnClick: true,
												delay: 10000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: false,
												messageHeight: '90vh',
												soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
												soundExt: '.ogg',
												msg: data.msg
											});
											
											$this.destroy();
										}else{
											Lobibox.notify('info', {
												title: 'ข้อมูล',
												closeOnClick: true,
												delay: 10000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: false,
												messageHeight: '90vh',
												soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
												soundExt: '.ogg',
												msg: data.msg
											});
										}
									}
								});	
							});
						}
						//height: $(window).height(),
						//width: $(window).width()
					});
					
					ChangeLOCAT();
				}
			}
		});
	});
	
	function ChangeLOCAT(){
		
	}
	
	$('#loadding').click(function(e){
		if ( e.keyCode === 13 ) { // ESC
			$('#loadding').hide();
		}
	});
	
	/*
	var tableToExcel = (function() {
		var uri = 'data:application/vnd.ms-excel;base64,'
		  , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
		  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
		  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
		return function(table, name) {
		  if (!table.nodeType) table = document.getElementById(table)
		  var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
		  window.location.href = uri + base64(format(template, ctx))
		}
	})()
	*/
	var tableToExcel = (function() {
		var uri = 'data:application/vnd.ms-excel;base64,'
			, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
			, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
			, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
		return function(table, name, fileName) {
			if (!table.nodeType) table = document.getElementById(table)
			var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
			
			var link = document.createElement("A");
			link.href = uri + base64(format(template, ctx));
			link.download = fileName || 'Workbook.xls';
			link.target = '_blank';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	})();
	
	var tableToExcel_Export = (function() {
		var uri = 'data:application/vnd.ms-excel;base64,'
			, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>{table}</body></html>'
			, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
			, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
		return function(table, name, fileName) {
			var ctx = {worksheet: name || 'Worksheet', table: table}

			var link = document.createElement("A");
			link.href = uri + base64(format(template, ctx));
			link.download = fileName || 'Workbook.xls';
			link.target = '_blank';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	})();
	
	var fontsize=10;
	$('body').keyup(function(e){
		if ( e.keyCode === 27 ) { // ESC
			$('#loadding').hide();
		}else if(e.keyCode === 112){ //F1
			dataToPost = new Object();
			dataToPost.url = document.URL;
			
			$('#loadding').show();
			
			$.ajax({
				url: '../CHomenew/Help',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').hide();
					
					var content = "<iframe src='"+encodeURI(data.url)+"' style='width:100%;height:100%;'></iframe>";
					window.open(encodeURI(data.url),'_blank');
					/*
					Lobibox.window({
						title: 'Help',
						content: content,
						height: $(window).height(),
						width: $(window).width()
					});
					*/
				}
			});
		}else if(e.keyCode === 113){ //F2
			dataToPost = new Object();
			dataToPost.url = document.URL;
			
			$('#loadding').show();
			
			$.ajax({
				url: '../CHomenew/Rating',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').hide();
					if(data.status){
						Lobibox.window({
							title: 'Rating',
							content: data.html,
							height: 500,
							shown: function($this){
								$('.checkstar').click(function(){
									var level = $(this).attr('level');
									var dataname = $(this).attr('dataname');
									
									$('.starinput'+level+'[name='+dataname+']').prop("checked", true);
								});
								
								
								$('#sendRating').click(function(){
									dataToPost = new Object();
									dataToPost.menuid   = $('.thismenu').attr('menuid');
									dataToPost.comments = $('#comments').val();
									
									for(var i=1;i<6;i++){
										if($('.starinput'+i+'[name=correct]').is(':checked')){
											dataToPost.correct = $('.starinput'+i+'[name=correct]:checked').val();
										}
									}
									for(var i=1;i<6;i++){
										if($('.starinput'+i+'[name=easy]').is(':checked')){
											dataToPost.easy = $('.starinput'+i+'[name=easy]:checked').val();
										}
									}
									for(var i=1;i<6;i++){
										if($('.starinput'+i+'[name=fast]').is(':checked')){
											dataToPost.fast = $('.starinput'+i+'[name=fast]:checked').val();
										}
									}
									
									$.ajax({
										url: '../CHomenew/saveRating',
										data: dataToPost,
										type: 'POST',
										dataType: 'json',
										success: function(data){
											if(data.status){
												Lobibox.notify('success', {
													title: 'สำเร็จ',
													size: 'mini',
													closeOnClick: false,
													delay: 5000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													msg: data.msg
												});
												
												$this.destroy();
											}else{
												Lobibox.notify('error', {
													title: 'ผิดพลาด',
													size: 'mini',
													closeOnClick: false,
													delay: 5000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													msg: data.msg
												});
											}
										}
									});
									
								});
							}
						});
					}else{
						Lobibox.notify('error', {
							title: 'ผิดพลาด',
							size: 'mini',
							closeOnClick: false,
							delay: 5000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.html
						});
					}
					
				}
			});
		}else if(e.keyCode === 119){ //F8
			fontsize += 2;
			if(fontsize == 14){ fontsize = 10 }
			$('body').css({'font-size':fontsize+'pt'});
			$('body , .form-group,table > thead,tbody,tfoot > tr > th,td').css({'font-size':fontsize+'pt'});
		}
	});
	
	var LobiboxNotify = null;
	$('.notifyMenu').hover(function(event){
		LobiboxNotify = Lobibox.notify('warning', {
			size: 'mini',
			delay: false,
			icon: false, 
			width: 200,
			sound: false, 
			position: {
				//top:(event.pageY - 50),
				top:(event.clientY - 50),
				left: 220//(event.pageX + 30)
			},
			messageHeight: '90vh',
			msg: $(this).attr('menuname')
		});
		$('.lobibox-notify').css({'z-index':'99999','border-radius':'50px'});
		$('.lobibox-close').fadeOut(0);
	},function(){
		$('.lobibox-notify').css({'z-index':'99999','border-radius':'0px'});
		$('.lobibox-close').fadeIn(0);
		LobiboxNotify.remove();
	});
	
	function fn_datatables($tbname,$numbers,$usageHeight,$overHeight="NO"){
		$dom = "";
		$iDisplayLength = 100;
		$ordering = true;
		switch($numbers){
			case 1: 
				$dom = "<'row'<'col-sm-6 data-export'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"; 
				break; 
			case 11: 
				$iDisplayLength = -1;
				$ordering = false;
				$dom = "<'row'<'col-sm-6 data-export'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"; 
				break; 	
			case 2: 
				$dom = "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"; 
				break; 
			case 3: 
				$iDisplayLength = -1;
				$dom = "<'row'<'col-sm-12'tr>>"; 
				break; 
			default: 
				$iDisplayLength = 100;
				$dom = "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"; 
				break;
		}
		
		if($overHeight=="NO"){
			$scrollY = 'calc(100vh - '+$usageHeight+'px)';			
		}else{
			$scrollY = $usageHeight;
		}
		
		var tables = $('#'+$tbname).DataTable({
			//scrollY: ($(window).height() - 375),
			scrollY: $scrollY,
			scrollX: true,
			autoWidth: true,
			responsive: false,
			ordering: $ordering,
			iDisplayLength: $iDisplayLength,
			lengthChange: false,
			aLengthMenu: [ 50, 100, 500, 1000 ],
			dom: $dom,
			language: {
				"decimal":        "",
				"emptyTable":     "ไม่พบข้อมูล ตามเงื่อนไข..",
				"info":           "ข้อมูล รายการที่ _START_ ถึง  _END_ จากทั้งหมด _TOTAL_ รายการ",
				"infoEmpty":      "ข้อมูล รายการที่ 0 ถึง 0 จากทั้งหมด 0 รายการ",
				"infoFiltered":   "(ค้นหาจากข้อมูล _MAX_ รายการ)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "แสดง _MENU_ รายการ",
				"loadingRecords": "Loading...",
				"processing":     "Processing...",
				"search":         "ค้นหา :",
				"zeroRecords":    "ไม่พบข้อมูลที่ค้นหา..",
				"paginate": {
					"first":      "หน้าแรก",
					"last":       "หน้าสุดท้าย",
					"next":       "ถัดไป",
					"previous":   "ย้อนกลับ"
				},
				"aria": {
					"sortAscending":  ": activate to sort column ascending",
					"sortDescending": ": activate to sort column descending"
				}
			},
			buttons: ['excel']
		});
		
		//setInterval(function(){ tables.columns.adjust().draw(); },250);
	}
	
	
	/*************************************************************************************************************
													 CHAT	SOLUTION
	**************************************************************************************************************/
	var ObjloadChat = null;
	//var loadChat = setInterval(function(){ fnLoadChat(); },3000);
	
	function fnLoadChat(){
		var dataToPost = new Object();
		dataToPost.lastLoad = $('.chat-details').attr('lastLoad');
		
		ObjloadChat = $.ajax({
			url: '../CHomenew/chatLoad?d='+new Date(),
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(ObjloadChat !== null){ ObjloadChat.abort(); } },
			success: function(data){
				if(data.html != ''){
					$('.chat-details').append(data.html);
					$('.chat-details').attr('lastLoad',data.lastLoad);
					
					var objDiv = document.getElementsByClassName("chat-details")[0];
					objDiv.scrollTop = objDiv.scrollHeight;
					ObjloadChat = null;
				} 
				
				$('.chat-body #textchat').focus();
			}
		});
	}
	
	$('#chat-title-open').unbind('click');
	$('#chat-title-open').click(function(){
		if($(this).hasClass('glyphicon glyphicon-chevron-up')){
			var objDiv = document.getElementsByClassName("chat-details")[0];
			objDiv.scrollTop = objDiv.scrollHeight;
			
			$('#chat-title-open').removeClass('glyphicon glyphicon-chevron-up');
			$('#chat-title-open').addClass('glyphicon glyphicon-chevron-down');
			if($('.chat-body').is(":hidden")){
				$('.chat-body').show(300);
				$('.chat-body #textchat').focus();
				
				$('.chat-body #textchat').unbind('keyup');
				$('.chat-body #textchat').keyup(function(e){
					var $this = $(this);
					if(e.keyCode === 13){
						if($this.val() != ''){						
							if (e.shiftKey){ fnChatSendMSG(); }
						}
					}
				});
				
				/*
					$('#textchat').focusout(function(){
						$(function(e){
							var clickedClass = e.target.className;
							var clickedID = e.target.id;
							
							alert(clickedClass);					
						});
						
						$('.chat-body').hide(300);
						$('#chat-title-open').removeClass('glyphicon glyphicon-chevron-down');
						$('#chat-title-open').addClass('glyphicon glyphicon-chevron-up');
					});
				*/
			}else{
				$('.chat-body').hide(300);
			}
		}else{
			$('.chat-body').hide(300);
			$('#chat-title-open').removeClass('glyphicon glyphicon-chevron-down');
			$('#chat-title-open').addClass('glyphicon glyphicon-chevron-up');
		}
		
		$('#chatSendMSG').unbind('click');
		$('#chatSendMSG').click(function(){ fnChatSendMSG(); });
		
		function fnChatSendMSG(){
			var dataToPost = new Object();
			dataToPost.text = $('.chat-body #textchat').val();
			dataToPost.lastLoad = $('.chat-details').attr('lastLoad');
			
			$('.chat-body #textchat').val('');
			$.ajax({
				url: '../CHomenew/chatSend',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if(data.html != ''){
						$('.chat-details').append(data.html);
						$('.chat-details').attr('lastLoad',data.lastLoad);
						
						var objDiv = document.getElementsByClassName("chat-details")[0];
						objDiv.scrollTop = objDiv.scrollHeight;
						ObjloadChat = null;
					}
					
					$('.chat-body #textchat').focus();
				}
			});
		}
	});
	
</script>
</html>