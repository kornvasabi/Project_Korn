<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>LobiAdmin</title>
	<link rel="shortcut icon" href="img/logo/lobiadmin-logo-16.ico" />
	
	<link rel="stylesheet" href="<?php echo base_url("/public/lobiadmin-master/documentation/css/bootstrap.min.css"); ?>">
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
		<!--form class="navbar-search pull-left">
			<label for="search" class="sr-only">Search...</label>
			<input type="text" class="font-size-lg" name="search" id="search" placeholder="Search...">
			<a class="btn btn-search">
				<span class="glyphicon glyphicon-search"></span>
			</a>
			<a class="btn btn-remove">
				<span class="glyphicon glyphicon-remove"></span>
			</a>
		</form -->
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
						<li><a href="#profile"><span class="glyphicon glyphicon-user"></span> &nbsp;&nbsp;ข้อมูลส่วนตัว</a></li>
						<li class="divider"></li>
						<li><a href="../clogout/lock"><span class="glyphicon glyphicon-lock"></span> &nbsp;&nbsp;Lock screen</a></li>
						<li><a href="../clogout"><span class="glyphicon glyphicon-off"></span> &nbsp;&nbsp; ออกจากระบบ</a></li>
						<li class="divider"></li>
					</ul>
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
				<!-- li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="glyphicon glyphicon-envelope"></span>
						<span class="badge badge-danger badge-xs">3</span>
					</a>
					<div class="dropdown-menu dropdown-notifications notification-messages border-1 animated-fast flipInX">
						<div class="notifications-heading border-bottom-1 bg-white">
							Messages
							<div class="header-actions pull-right">
								<a href="#lobimail" data-action="compose-email"><small><i class="fa fa-edit"></i> Create new</small></a>
							</div>
						</div>
						<ul class="notifications-body max-h-300">
							<li class="unread">
								<a href="#lobimail" data-action="open-email" data-key="1" class="notification">
									<img class="notification-image" src="img/users/1.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">George Darso</h4>
										<h5 class="notification-sub-heading">Happy birthday!!</h5>
										<p class="body-text">Happy birthday. Lorem ipsum dolor sit amor.</p>
									</div>
									<span class="notification-time">5 min. ago</span>
								</a>
							</li>
							<li class="unread">
								<a href="#lobimail" data-action="open-email" data-key="2" class="notification">
									<img class="notification-image" src="img/users/2.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">George Dava</h4>
										<h5 class="notification-sub-heading">Lorem ipsum dolor sit amor.</h5>
										<p class="body-text">Whirling indicating staunch pglaf, leafy siroc clerks paying eager promoting storm aliquet treasures.</p>
									</div>
									<span class="notification-time">15 min. ago</span>
								</a>
							</li>
							<li class="unread">
								<a href="#lobimail" data-action="open-email" data-key="3" class="notification">
									<img class="notification-image" src="img/users/3.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">George Darso</h4>
										<h5 class="notification-sub-heading">Lorem ipsum dolor sit amore</h5>
										<p class="body-text">Region doth chaucer, smiled erhung, recording frail cove design train thy holiday committed. Told how fixed parcae tented whistle doom recording deceivers tribe.</p>
									</div>
									<span class="notification-time">1 hr. ago</span>
								</a>
							</li>
							<li>
								<a href="#lobimail" data-action="open-email" data-key="4" class="notification">
									<img class="notification-image" src="img/users/4.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">Zura Sekhno</h4>
										<h5 class="notification-sub-heading">This is message subject text</h5>
										<p class="body-text">Garden check we, dearer you sharply, martin cable, square bonfires, freely, delphian scorning copying courage. </p>
									</div>
									<span class="notification-time">17 hr. ago</span>
								</a>
							</li>
							<li>
								<a href="#lobimail" data-action="open-email" data-key="5" class="notification">
									<img class="notification-image" src="img/users/5.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">Jane Smth</h4>
										<h5 class="notification-sub-heading">Need support about product</h5>
										<p class="body-text">Essaying unappointed pull disdain, downfall square liberal.</p>
									</div>
									<span class="notification-time">2 days ago.</span>
								</a>
							</li>
							<li>
								<a href="#lobimail" data-action="open-email" data-key="6" class="notification">
									<img class="notification-image" src="img/users/1.jpg" alt="">
									<div class="notification-msg">
										<h4 class="notification-heading">Sam Solly</h4>
										<h5 class="notification-sub-heading">Today's meeting</h5>
										<p class="body-text">Paltering kingfisher patience woven his, aeneas self each square surer semper. Self reaped bonfires, gift rounds dearer garden.</p>
									</div>
									<span class="notification-time">January 15, 2014</span>
								</a>
							</li>
						</ul>
						<div class="notifications-footer text-center border-top-1 bg-white">
							<a href="#lobimail">View all</a>
						</div>
					</div>
				</li -->
			</ul>
		</div>
		<div class="clearfix"></div>
	</nav>
	
		
	
	
	 <div class="menu">
		<div class="menu-heading">
			<div class="menu-header-buttons-wrapper clearfix">
				<button type="button" class="btn btn-info btn-menu-header-collapse">
					<i class="fa fa-cogs"></i>
				</button>
				<!--Put your favourite pages here-->
				<div class="menu-header-buttons">
					<a href="#profile" class="btn btn-info btn-outline" data-title="ข้อมูลส่วนตัว">
						<i class="glyphicon glyphicon-user"></i>
					</a>
					<a href="../CLogout/lock" class="btn btn-info btn-outline" data-title="Lock Screen">
						<i class="glyphicon glyphicon-lock"></i>
					</a>
					<a href="../CLogout/" class="btn btn-info btn-outline" data-title="ออกจากระบบ">
						<i class="glyphicon glyphicon-off"></i>
					</a>
					<!-- a href="#calendar" class="btn btn-info btn-outline" data-title="Calendar">
						<i class="fa fa-calendar"></i>
					</a-->
				</div>
			</div>
		</div>
		<nav>
			<ul>
				<li>
					<a href="#welcome/dashboard">
						<i class="fa fa-home menu-item-icon"></i>
						<span class="inner-text">Dashboard</span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-share-alt menu-item-icon"></i>
						<span class="inner-text">Exclusive plugins</span>
						<!-- span class="badge-wrapper"><span class="badge badge-xs badge-cyan">4</span></span-->
					</a>
					<ul>
						<li>
							<a href="#welcome/testload">
								<span class="inner-text">test</span>
							</a>
						</li>
						<li>
							<a href="#welcome/lock">
								<span class="inner-text">lock</span>
							</a>
						</li>
						<li>
							<a href="#welcome/logout">
								<span class="inner-text">logout</span>
							</a>
						</li>
						<li>
							<a href="#lobilist">
								<span class="inner-text">LobiList</span>
							</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-area-chart menu-item-icon"></i>
						<span class="inner-text">Graphs</span>
					</a>
					<ul>
						<li>
							<a href="#chartjs">
								<span class="inner-text">Chart.js</span>
							</a>
						</li>
						<li>
							<a href="#morrisjs">
								<span class="inner-text">Morris Charts</span>
							</a>
						</li>
						<li>
							<a href="#inline-charts">
								<span class="inner-text">Inline Charts</span>
							</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-table menu-item-icon"></i>
						<span class="inner-text">Tables</span>
					</a>
					<ul>
						<li>
							<a href="#basic-tables">
								<span class="inner-text">Basic Tables</span>
							</a>
						</li>
						<li>
							<a href="#data-tables">
								<span class="inner-text">Data Tables</span>
							</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-list-alt menu-item-icon"></i>
						<span class="inner-text">UI Elements</span>
					</a>
					<ul>
						<li>
							<a href="#default-elements">
								Default Elements
							</a>
						</li>
						<li>
							<a href="#">
								<span class="inner-text">Icons</span>
							</a>
							<ul>
								<li>
									<a href="#glyphicon">
										<i class="glyphicon glyphicon-cloud-download"></i>
										<span class="inner-text">Glyphicon</span>
									</a>
								</li>
								<li>
									<a href="#font-awesome">
										<i class="fa fa-flag menu-item-icon"></i>
										<span class="inner-text">Font Awesome</span>
									</a>
								</li>
								<li>
									<a href="#weather-icons">
										<i class="wi wi-cloudy menu-item-icon"></i>
										<span class="inner-text">Weather icons</span>
									</a>
								</li>
							</ul>

						</li>
						<li>
							<a href="#typography">
								Typography
							</a>
						</li>
						<li>
							<a href="#buttons">
								Buttons
							</a>
						</li>
						<li>
							<a href="#tiles">
								Tiles
							</a>
						</li>
						<li>
							<a href="#discount-labels">
								Discount labels
							</a>
						</li>
						<li>
							<a href="#treeview">
								Treeview
							</a>
						</li>
						<li>
							<a href="#">
								Six Level Menu
							</a>
							<ul>
								<li>
									<a href="#">
										Third level menu
									</a>
									<ul>
										<li>
											<a href="#">
												Fourth level
											</a>
											<ul>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
											</ul>

										</li>
										<li>
											<a href="#">
												Fourth level
											</a>
											<ul>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
											</ul>
										</li>
									</ul>

								</li>
								<li>
									<a href="#">
										Third level menu
									</a>
									<ul>
										<li>
											<a href="#">
												Fourth level
											</a>
											<ul>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
											</ul>
										</li>
										<li>
											<a href="#">
												Fourth level
											</a>
											<ul>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
												<li>
													<a href="#">
														Fifth level
													</a>
													<ul>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
														<li>
															<a href="#">
																Sixth level
															</a>
														</li>
													</ul>
												</li>
											</ul>
										</li>
									</ul>

								</li>
							</ul>

						</li>
					</ul>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-pencil-square-o menu-item-icon"></i>
						<span class="inner-text">Forms</span>
					</a>
					<ul>
						<li>
							<a href="#form-basic-elements">
								Basic Elements
							</a>
						</li>
						<li>
							<a href="#form-custom-elements">
								Custom Elements
							</a>
						</li>
						<li>
							<a href="#form-plugins">
								Form Plugins
							</a>
						</li>
						<li>
							<a href="#form-layouts">
								Form Layouts
							</a>
						</li>
						<li>
							<a href="#form-validation">
								Form Validation
							</a>
						</li>
						<li>
							<a href="#wizard">
								Wizards
							</a>
						</li>
						<li>
							<a href="#editor">
								WYSIWYG Editor
							</a>
						</li>
						<li>
							<a href="#file-upload">
								File Upload
							</a>
						</li>
						<li>
							<a href="#image-cropping">
								Image cropping
							</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#grid">
						<i class="fa fa-laptop menu-item-icon"></i>
						<span class="inner-text">Grid options</span>
					</a>
				</li>
				<li>
					<a href="#mail-templates">
						<i class="fa fa-envelope-o menu-item-icon"></i>
						<span class="inner-text">Mail templates</span>
					</a>
				</li>
				<li>
					<a href="#calendar">
						<i class="fa fa-calendar menu-item-icon"></i>
						<span class="inner-text">Calendar</span>
						<span class="badge-wrapper"><span class="badge badge-xs badge-info">12</span></span>
					</a>
				</li>
				<li>
					<a href="#lobimail">
						<i class="fa fa-envelope menu-item-icon"></i>
						<span class="inner-text">Inbox</span>
						<span class="badge-wrapper"><span class="badge badge-xs badge-danger">7</span></span>
					</a>
				</li>
				<li>
					<a href="#">
						<i class="fa fa-file-o menu-item-icon"></i>
						<span class="inner-text">Extra</span>
					</a>
					<ul>
						<li>
							<a href="#error-404">
								Page 404
							</a>
						</li>
						<li>
							<a href="#error-500">
								Page 500
							</a>
						</li>
						<li>
							<a href="lock.html">
								Lock Screen
							</a>
						</li>
						<li>
							<a href="<?php echo base_url("/Welcome/login"); ?>">
								Login & Register
							</a>
						</li>
						<li>
							<a href="#helper">
								Helper Classes
							</a>
						</li>
						<li>
							<a href="#pricing-tables">
								Pricing tables
							</a>
						</li>
						<li>
							<a href="#timeline">
								Timeline
							</a>
						</li>
						<li>
							<a href="#invoice">
								Invoice
							</a>
						</li>
						<li>
							<a href="#profile">
								Profile
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>
		<div class="menu-collapse-line">
			<!--Menu collapse/expand icon is put and control from LobiAdmin.js file-->
			<div class="menu-toggle-btn" data-action="collapse-expand-sidebar"></div>
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
	
	<!--Make sure that config.js file is loaded before LobiAdmin.js-->
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/config.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/LobiAdmin.min.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/app.js"></script>
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/demo.js"></script>
	
	<script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/plugin/select2/select2.min.js"></script>
	
	<script src="../public/bootstrap-datepicker-thai/js/bootstrap-datepicker.js"></script>
    <script src="../public/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js"></script>
    <script src="../public/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js"></script>
</body>
</html>