<!DOCTYPE html>
<!--Author      : @arboshiki-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>LobiAdmin lock screen</title>
        <link rel="shortcut icon" href="img/logo/lobiadmin-logo-16.ico" />

        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/bootstrap.min.css">
        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/font-awesome.min.css"/>
        
        <link rel="stylesheet" href="../public/lobiadmin-master/version/1.0/ajax/css/lock-screen.css"/>
    </head>
    <body>
        <div class="lock-screen slideInDown animated">
            <div class="lock-form-wrapper">
                <div>
                    <form class="lock-screen-form lobi-form">
                        <div class="row lock-screen-body">
                            <div class="col-xxs-12 col-xs-4">
                                <img src="../public/lobiadmin-master/version/1.0/ajax/img/users/default-user.jpg" class="horizontal-center img-responsive" alt />
                            </div>
                            <div class="col-xxs-12 col-xs-8">
                                <h4 class="fullname"><?php echo isset($user) ? $user:""; ?> <small class="text-gray pull-right"><i class="fa fa-lock"></i> Locked</small></h4>
                                <h6 class="lock-screen-email"><?php echo isset($position) ? $position:""; ?></h6>
                                <div class="form-group margin-bottom-5">
                                    <div class="input-group">
                                        <input type="password" id='pass' class="form-control" placeholder="รหัสผ่าน">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info" id='unlock'><i class="fa fa-key"></i></button>
                                        </span>
                                    </div>
                                </div>
								<div id='result' style='color:red;'>&emsp;</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="carousel-wrapper slideInDown animated">
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <div class="item">
                            <div class="fill" style="background-image:url('../public/lobiadmin-master/version/1.0/ajax/img/demo/1_1920.jpg');">
                                <div class="container">

                                </div>
                            </div>
                        </div>
                        <div class="item active">
                            <div class="fill" style="background-image:url('../public/lobiadmin-master/version/1.0/ajax/img/demo/2_1920.jpg');">
                                <div class="container">

                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="fill" style="background-image:url('../public/lobiadmin-master/version/1.0/ajax/img/demo/3_1920.jpg');">
                                <div class="container">

                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="fill" style="background-image:url('../public/lobiadmin-master/version/1.0/ajax/img/demo/5_1920.jpg');">
                                <div class="container">

                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="fill" style="background-image:url('../public/lobiadmin-master/version/1.0/ajax/img/demo/6_1920.jpg');">
                                <div class="container">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lock-screen-clock">
                        <div class="lock-screen-time"></div>
                        <div class="lock-screen-date"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/lib/jquery.min.js"></script>
        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="../public/lobiadmin-master/version/1.0/ajax/js/config.js"></script>
        <script type="text/javascript">
            $(function(){
                var CONFIG = window.LobiAdminConfig;
                
				$('.lock-screen-form').submit(function(ev){
                    //window.location.href = window.location.href+"";
					//window.location.href = "../welcome/test";
                    return false;
                });
				
				$('#unlock').click(function(){
					dataToPost = new Object();
					dataToPost.pass = $('#pass').val();
					$.ajax({
						url:'../CLogout/unlockVertify',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.status){
								window.open('../welcome/','_parent');
							}else{
								$('#result').html(data.html);
							}
						}
					});
				});                
				//Initialize time on lock screen and timeout for show slideshow
                (function () {
                    var monthNames = CONFIG.monthNames;
                    var weekNames = CONFIG.weekNames;
                    setInterval(function () {
                        var d = new Date();
                        var h = d.getHours();
                        var m = d.getMinutes();
                        $('.lock-screen-time').html((Math.floor(h / 10) === 0 ? "0" : "") + h + ":" + (Math.floor(m / 10) === 0 ? "0" : "") + m);
                        $('.lock-screen-date').html(weekNames[d.getDay()] + ", " + monthNames[d.getMonth()] + " " + d.getDate());
                    }, CONFIG.updateTimeForLockScreen);

                })();
                //Initialize carousel and catch form submit
				var timeout;
                (function () {
                    var $lock = $('.lock-screen');
                    var $car = $lock.find('.carousel');
                    $car.click(function () {
                        $car.parent().addClass('slideOutUp').removeClass('slideInDown');                        
						jd_create_waitkey();
                    });
                    $car.carousel({
                        pause: false,
                        interval: 8000
                    });
                })();
				
				function jd_create_waitkey(){
					timeout = setTimeout(function () {
						$('.lock-screen .carousel-wrapper').removeClass('slideOutUp').addClass('slideInDown');
					}, CONFIG.showLockScreenTimeout);
				}
				
				$('#pass').keypress(function(){
					clearTimeout(timeout);					
					$('.lock-screen .carousel-wrapper').addClass('slideOutUp').removeClass('slideInDown');                        
					jd_create_waitkey();
				});
            });
        </script>
    </body>
</html>
