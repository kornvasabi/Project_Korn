<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Login to YTKMini</title>
        <!--link rel="shortcut icon" href="../public/lobiadmin-master/version/1.0/ajax/img/logo/lobiadmin-logo-16.ico" /-->
		<link rel="shortcut icon" href="../public/images/icon-preview.png?v" />

        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/bootstrap.min.css">
        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/font-awesome.min.css"/>
        
        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/login.css"/>
    </head>
    <body style="background-image: url('../public/images/honda-fireblade-cbr.jpg');width:100vw;height:100vh;">
        <div class="login-wrapper fadeInDown animated">
            <form action class="lobi-form login-form visible">
                <div class="login-header">
					เข้าสู่ระบบ
                </div>
                <div class="login-body no-padding">
                    <fieldset>
                        <div class="form-group">
                            <label>ผู้ใช้งาน</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-user"></span>
                                <input type="text" id="user" name="username" placeholder="">
                                <span class="tooltip tooltip-top-left"><i class="fa fa-user text-cyan-dark"></i> กรุณาระบุผู้ใช้งาน</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-key"></span>
                                <input type="password" id="pass" name="password" placeholder="">
                                <span class="tooltip tooltip-top-left"><i class="fa fa-key text-cyan-dark"></i> กรุณาระบุรหัสผ่าน</span>
                            </label>
                        </div>
						<div class="form-group">
                            <label>ฐานข้อมูล</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-user"></span>
                                <input type="text" id="db" name="db" placeholder="" style="text-transform:uppercase">
                                <span class="tooltip tooltip-top-left"><i class="fa fa-user text-cyan-dark"></i> กรุณาระบุฐานข้อมูล</span>
                            </label>
                        </div>

                        <div class="row">                            
                            <div class="col-xs-12">
                                <button type="button" id="login" class="btn btn-info btn-block"><span class="glyphicon glyphicon-log-in"></span> เข้าใช้งาน</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="login-footer"></div>
            </form>          
        </div>
        

        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lib/jquery.min.js"></script>
        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/bootstrap/bootstrap.min.js"></script>
		
        <script type="text/javascript">
            $('.login-wrapper').ready(function(){
                $('#login').click(function(){ login(); });
            });
			
			$("#user").keypress(function(e){ if(e.keyCode == 13){ $("#pass").focus(); } });
			$("#pass").keypress(function(e){ if(e.keyCode == 13){ $("#db").focus(); } });
			$("#db").keypress(function(e){ if(e.keyCode == 13){ login(); $("#login").focus(); } });
			
			function login(){
				dataToPost = new Object();
				dataToPost.user = $("#user").val();
				dataToPost.pass = $("#pass").val();
				dataToPost.db = $("#db").val();
				
				$.ajax({
					url: '<?php echo base_url("CLogin/loginVertify"); ?>',
					data:dataToPost,
					type:'POST',
					dataType: 'json',
					success: function(data){
						if(data.status) {
							window.open('<?php echo base_url("Welcome/"); ?>','_parent')
						}else{
							$('.login-footer').html(data.msg);
							$('.login-footer').css({'color':'red'})
							
							setTimeout(function(){
								$('.login-footer').html('');
							},5000);
						}
					}
				});
			}
			/*
			
            $('.btn-forgot-password').click(function(ev){
                var $form = $(this).closest('form');
                $form.removeClass('visible');
                $form.parent().find('.pass-forgot-form').addClass('visible');
            });
            $('.btn-sign-in').click(function(){
                var $form = $(this).closest('form');
                $form.removeClass('visible');
                $form.parent().find('.login-form').addClass('visible');
            });
            $('.btn-sign-up').click(function(){
                var $form = $(this).closest('form');
                $form.removeClass('visible');
                $form.parent().find('.signup-form').addClass('visible');
            });
			*/
        </script>
    </body>
</html>
