/********************************************************
             _____15/08/2563______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
var _locat  = $('.tab1[name="home"]').attr('locat');

$('#btnCheck').click(function(){ getinfo(); });
$('#STRNO').keypress(function(e){ if(e.keyCode == 13) getinfo(); });

var OBJgetinfo=null;
var OBJsaveManuyr=null;
function getinfo(){
	var dataToPost = new Object()
	dataToPost.strno = $('#STRNO').val();
	
	$('#loadding').fadeIn(200);	
	OBJgetinfo = $.ajax({
		url:'../SYS02/DealerServices/getInfo',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		beforeSend: function(){ if(OBJgetinfo !== null){ OBJgetinfo.abort(); }},
		success:function(data){
			OBJgetinfo = null;
			$('#result').html(data.html);
			
			if(_update == "T"){
				$('#manuyr').attr('disabled',false);
				$('#saveManuyr').attr('disabled',false);
			}else{
				$('#manuyr').attr('disabled',true);
				$('#saveManuyr').attr('disabled',true);
			}
			
			$('#saveManuyr').unbind('click');
			$('#saveManuyr').click(function(){
				var $saveManuyr = $(this);
				
				Lobibox.confirm({
					title: 'ยืนยันการทำรายการ',
					iconClass: false,
					msg: 'คุณต้องการแก้ไขปีผลิตรถในสต๊อค ? ',
					buttons: {
						ok : {
							'class': 'btn btn-primary glyphicon glyphicon-ok',
							text: ' ยืนยัน, แก้ไขปีผลิต',
							closeOnClick: false,
						},
						cancel : {
							'class': 'btn btn-danger glyphicon glyphicon-ok',
							text: ' ยกเลิก, ไว้ทีหลัง',
							closeOnClick: true
						},
					},
					onShow: function(lobibox){ $('body').append(jbackdrop); },	
					callback: function(lobibox, type){
						if (type === 'ok'){
							var dataToPost = new Object();
							dataToPost.strno 	= $saveManuyr.attr('strno');
							dataToPost.manuyr 	= $('#manuyr').val();
							
							$('#loadding').fadeIn(200);
							OBJsaveManuyr = $.ajax({
								url:'../SYS02/DealerServices/saveYR',
								data:dataToPost,
								type:'POST',
								dataType:'json',
								beforeSend: function(){ if(OBJsaveManuyr !== null){ OBJsaveManuyr.abort(); }},
								success:function(data){
									Lobibox.notify((data.stat?'success':'warning'), {
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
									
									if(data.stat){ getinfo(); }
									
									OBJsaveManuyr=null;
									lobibox.destroy();
									$('#loadding').fadeOut(200);
								}
							});
							
						}else{
							$('#stdCond1').prop('checked',false);
						}
						
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
					}
				});
			});
			
			$('#loadding').fadeOut(200);
		}
	});
}


















