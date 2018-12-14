<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>YTKMini</title>
	<link rel="shortcut icon" href="../public/lobiadmin-master/version/1.0/ajax/img/logo/lobiadmin-logo-48.png" />
	
	<link rel="stylesheet" href="../public/lobiadmin-master/documentation/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/weather-icons.min.css"/>

	<!--lobiadmin-with-plugins.css contains all LobiAdmin css plus lobiplugins all css files, plus third party plugins-->
	<link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/lobiadmin-with-plugins.css"/>
	
	
</head>
<body class='menu-fixed'>
	<nav class="navbar navbar-default navbar-header header">
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
						<li><a href="../clogout/lock"><span class="glyphicon glyphicon-lock"></span> &nbsp;&nbsp;Lock screen</a></li>
						<li><a href="../clogout"><span class="glyphicon glyphicon-off"></span> &nbsp;&nbsp; ออกจากระบบ</a></li>
						<li class="divider"></li>
					</ul>
				</li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right user-actions">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">						
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
		</div>
		
		
		
		<div class="clearfix-xxs"></div>
		<div class="navbar-items-2">
			<ul class="nav navbar-nav navbar-actions">
				<li class="visible-lg">
					<a href="#" data-action="fullscreen">
						<span class="glyphicon glyphicon-fullscreen"></span>
					</a>
				</li>
			</ul>
		</div>
		<div class="clearfix"></div>
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
	<div class="spinner spinner-horizontal hide">
		<span class="spinner-text">Loading...</span>
		<div class="bounce1"></div>
		<div class="bounce2"></div>
		<div class="bounce3"></div>
		
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
</body>
<script>
	/*
		setInterval(function(){
			alert('x');
		},60000);
	*/
	$('.xccc').datepicker({ autoclose: true });
	$('#content').css({'background-color':'whiteSmoke'});
	
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
</script>
</html>