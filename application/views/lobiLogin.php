<!DOCTYPE html>
<!--Author     : @arboshiki-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Login to LobiAdmin</title>
        <link rel="shortcut icon" href="../public/lobiadmin-master/version/1.0/ajax/img/logo/lobiadmin-logo-16.ico" />

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
                            <!--button type="button" class="btn-link btn-forgot-password">Forgot your password?</button-->
                        </div>

                        <div class="row">
                            <!-- div class="col-xs-8">
                                <!--label class="checkbox lobicheck lobicheck-info lobicheck-inversed lobicheck-lg">
                                    <input type="checkbox" name="remember_me" value="0"> 
                                    <i></i> จำรหัส
                                </label>
                            </div -->
                            <div class="col-xs-12">
                                <button type="button" id="login" class="btn btn-info btn-block"><span class="glyphicon glyphicon-log-in"></span> เข้าใช้งาน</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="login-footer">
                    <!-- New user? <button type="button" class="btn btn-xs btn-info btn-sign-up pull-right">Sign up</button-->
                </div>
            </form>
            <!--Sign up form-->
            <!-- form action class="lobi-form signup-form">
                <div class="login-header">
                    New user? Sign up.
                </div>
                <div class="login-body no-padding">
                    <fieldset>
                        <div class="row">
                            <div class="col-xxs-12 col-xs-6">
                                <label>First name</label>
                                <label class="input">
                                    <span class="input-icon input-icon-prepend fa fa-user"></span>
                                    <input type="text" name="firstname" placeholder="Firstname">
                                </label>
                            </div>
                            <div class="col-xxs-12 col-xs-6">
                                <label>Last name</label>
                                <label class="input">
                                    <span class="input-icon input-icon-prepend fa fa-user"></span>
                                    <input type="text" name="lastname" placeholder="Lastname">
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-user"></span>
                                <input type="text" name="username" placeholder="Username">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-envelope"></span>
                                <input type="text" name="email" placeholder="Email address">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-key"></span>
                                <input type="password" name="password" placeholder="Password">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Confirm password</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-key"></span>
                                <input type="password" name="confirm_password" placeholder="Confirm password">
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-xs-offset-8">
                                <button type="submit" class="btn btn-info btn-block">Register</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="login-footer">
                    Do you already have an account? <button type="button" class="btn btn-xs btn-info btn-sign-in pull-right">Sign in</button>
                </div>
            </form -->
            <!--Forgot password form-->
            <!-- form action class="lobi-form pass-forgot-form">
                <div class="login-header">
                    Forgot your password?
                </div>
                <div class="login-body">
                    <fieldset>
                        <div class="form-group">
                            <label>Email or username</label>
                            <label class="input">
                                <span class="input-icon input-icon-prepend fa fa-envelope"></span>
                                <span class="input-icon input-icon-append fa fa-user"></span>
                                <input type="text" name="username" placeholder="Email or username">
                                <span class="tooltip tooltip-bottom-right">Type the email or username used for registration</span>
                            </label>
                            <button type="button" class="btn-link btn-sign-in">Remember your password?</button>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <button type="submit" class="btn btn-info btn-block"><i class="fa fa-refresh"></i> Restore password</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="login-footer">
                    New user? <button type="button" class="btn btn-xs btn-info btn-sign-up pull-right">Sign up</button>
                </div>
            </form -->
        </div>
        

        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lib/jquery.min.js"></script>
        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/bootstrap/bootstrap.min.js"></script>
		
        <script type="text/javascript">
            $('.login-wrapper').ready(function(){
                $('#login').click(function(){ login(); });
                
				/*
				$('.signup-form').submit(function(){
                    return false;
                });
                $('.pass-forgot-form').submit(function(){
                    return false;
                });
				*/
            });
			
			$("#user").keypress(function(e){ if(e.keyCode == 13){ $("#pass").focus(); } });
			$("#pass").keypress(function(e){ if(e.keyCode == 13){ login(); $("#login").focus(); } });
			
			function login(){
				dataToPost = new Object();
				dataToPost.user = $("#user").val();
				dataToPost.pass = $("#pass").val();
				
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
