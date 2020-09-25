
$('#LOCAT').selectpicker();
var smpay = 0;
var adjust_size = 0;
var adjust_success = 0;
var objbtnAdjustNOPAY = null;
var objbtnAdjustDeley = null;
var objbtnAdjustHP = null;

$('#btnAdjustNOPAY').click(function(){
	Lobibox.progress({
		title: 'ปรับปรุงงวดที่ขาด',
		label: 'กำลังอัพเดตข้อมูล โปรดรอ ... [<span id="lobi_process_now">0</span>/<span id="lobi_process_total">0</span>][<span id="lobi_process_time">0</span>]',
		closeButton: false, 
		closeOnEsc: false,
		onShow: function ($this) {
			var dataToPost = new Object();
			dataToPost.LOCAT = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
			dataToPost.LOCAT = $('#LOCAT').val();
			dataToPost.ADJDT = $('#ADJDT').val();
			
			objbtnAdjustNOPAY  = $.ajax({
				url:'../SYS06/AdjustCONTNO/getCONTNO',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){ if(objbtnAdjustNOPAY !== null){ objbtnAdjustNOPAY.abort(); } },
				success: function(data){
					if(!data.error){
						smpay = 0;
						adjust_size  = data.html.length;
						adjust_success = 0;
						
						$('#lobi_process_time').attr("st",performance.now());
						$('#lobi_process_total').html(adjust_size);
						
						for(var i=0;i<adjust_size;i++){
							adjust(data.html[i][0],$this);
						}
					}else{
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: false,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.errorMessage
						});
						
						$('#loadding').fadeOut(200);
					}
					
					objbtnAdjustNOPAY = null;
				}
			});
		}
	});		
});


function adjust($LOCAT,$this){
	var data = new Object();
	data.LOCAT = $LOCAT;
	data.ADJDT = $('#ADJDT').val();
	
	$.ajax({
		url:'../SYS06/AdjustCONTNO/adjustCONTNO',
		data: data,
		type: 'POST',
		dataType: 'json',
		cache: true,
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: false,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.errorMessage
				});
				
				$this.destroy();
			}else{
				adjust_success += 1;
				smpay += parseInt(data.html);
				
				$('#lobi_process_now').html(adjust_success);
				var timeout = (performance.now() - $('#lobi_process_time').attr("st")) / 1000;
				$('#lobi_process_time').html(timeout.toFixed(2));
				$this.setProgress(parseFloat(adjust_success / adjust_size)*100);
				if(parseFloat(adjust_success / adjust_size)*100 == 100){
					$this.destroy();
					Lobibox.notify('success', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 3000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: 'ปรับปรุงงวดที่ขาดแล้วครับ'
					});	
				}
			}
		}
	});
}



/**********************************************************************************************************************************************************/
$('#btnAdjustDeley').click(function(){
	Lobibox.progress({
		title: 'ปรับปรุงวันค้างชำระ',
		label: 'กำลังอัพเดตข้อมูล โปรดรอ ... [<span id="lobi_process_now">0</span>/<span id="lobi_process_total">0</span>][<span id="lobi_process_time">0</span>]',
		closeButton: false, 
		closeOnEsc: false,
		onShow: function ($this) {
			var dataToPost = new Object();
			dataToPost.LOCAT = $('#LOCAT').val();
			dataToPost.ADJDT = $('#ADJDT').val();
			dataToPost.SCONTNO = $('#SCONTNO').val();
			dataToPost.ECONTNO = $('#ECONTNO').val();
			
			var x=1;
			var AdjustDeley = setInterval(function(){
				let timeout = (performance.now() - $('#lobi_process_time').attr("st")) / 1000;
				$('#lobi_process_time').html(timeout.toFixed(2));
				x += 1; 
				if(x==100){ 
					clearInterval(AdjustDeley);
					$this.destroy(); 
				}
			},1000);
			
			objbtnAdjustDeley  = $.ajax({
				url:'../SYS06/AdjustCONTNO/getCONTNODelay',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){ if(objbtnAdjustDeley !== null){ objbtnAdjustDeley.abort(); } },
				success: function(data){
					if(!data.error){
						smpay = 0;
						adjust_size  = data.html.length;
						adjust_success = 0;
						
						if(adjust_size == 0){
							$this.destroy();
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: 'ไม่พบข้อมูลที่สามารถปรับปรุงได้'
							});	
						}else{
							$('#lobi_process_time').attr("st",performance.now());
							$('#lobi_process_total').html(adjust_size);
							
							for(var i=0;i<adjust_size;i++){
								adjustDelay(data.html[i][0],$this);
							}
						}
					}else{
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: false,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.errorMessage
						});
						
						$this.destroy(); 
					}
					
					objbtnAdjustDeley = null;
				}
			});
		}
	});
});

function adjustDelay($LOCAT,$this){
	var dataToPost = new Object();
	dataToPost.LOCAT = $LOCAT;
	dataToPost.ADJDT = $('#ADJDT').val();
	dataToPost.SCONTNO = $('#SCONTNO').val();
	dataToPost.ECONTNO = $('#ECONTNO').val();
	
	$.ajax({
		url:'../SYS06/AdjustCONTNO/adjustDelayCONTNO',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		cache: true,
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: false,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.errorMessage
				});
				
				$this.destroy();
			}else{
				adjust_success += 1;
				smpay += parseInt(data.html);
				
				$('#lobi_process_now').html(adjust_success);
				var timeout = (performance.now() - $('#lobi_process_time').attr("st")) / 1000;
				$('#lobi_process_time').html(timeout.toFixed(2));
				$this.setProgress(parseFloat(adjust_success / adjust_size)*100);
				if(parseFloat(adjust_success / adjust_size)*100 == 100){
					$this.destroy();
					Lobibox.notify('success', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 3000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: 'ปรับปรุงวันค้างชำระแล้วครับ'
					});	
				}
			}
		}
	});
}




/**********************************************************************************************************************************************************/
$('#btnAdjustHP').click(function(){
	window.top.close();	
});

$('#btnAdjustHP1').click(function(){
	Lobibox.progress({
		title: 'ปรับปรุงเบี้ยปรับ',
		label: 'กำลังอัพเดตข้อมูล โปรดรอ ... [<span id="lobi_process_now">0</span>/<span id="lobi_process_total">0</span>][<span id="lobi_process_time">0</span>]',
		closeButton: false, 
		closeOnEsc: false,
		onShow: function ($this) {
			var dataToPost = new Object();
			dataToPost.LOCAT = $('#LOCAT').val();
			dataToPost.ADJDT = $('#ADJDT').val();
			dataToPost.SCONTNO = $('#SCONTNO').val();
			dataToPost.ECONTNO = $('#ECONTNO').val();
			
			var x=1;
			var AdjustDeley = setInterval(function(){
				let timeout = (performance.now() - $('#lobi_process_time').attr("st")) / 1000;
				$('#lobi_process_time').html(timeout.toFixed(2));
				x += 1; 
			},1000);
			
			objbtnAdjustHP  = $.ajax({
				url:'../SYS06/AdjustCONTNO/getCONTNOHP',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){ if(objbtnAdjustHP !== null){ objbtnAdjustHP.abort(); } },
				success: function(data){
					if(!data.error){
						smpay = 0;
						adjust_size  = data.html.length;
						adjust_success = 0;
						
						if(adjust_size == 0){
							$this.destroy();
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: 'ไม่พบข้อมูลที่สามารถปรับปรุงได้'
							});	
						}else{
							$('#lobi_process_time').attr("st",performance.now());
							$('#lobi_process_total').html(adjust_size);
							
							for(var i=0;i<adjust_size;i++){
								adjustHP(data.html[i][0],$this,AdjustDeley);
							}
						}
					}else{
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: false,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.errorMessage
						});
						
						$this.destroy(); 
					}
					
					objbtnAdjustHP = null;
				}
			});
		}
	});
});

function adjustHP($LOCAT,$this,AdjustDeley){
	var dataToPost = new Object();
	dataToPost.LOCAT = $LOCAT;
	dataToPost.ADJDT = $('#ADJDT').val();
	dataToPost.SCONTNO = $('#SCONTNO').val();
	dataToPost.ECONTNO = $('#ECONTNO').val();
	
	$.ajax({
		url:'../SYS06/AdjustCONTNO/adjustHPCONTNO',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		cache: true,
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: false,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.errorMessage
				});
				
				$this.destroy();
			}else{
				adjust_success += 1;
				smpay += parseInt(data.html);
				
				$('#lobi_process_now').html(adjust_success);
				var timeout = (performance.now() - $('#lobi_process_time').attr("st")) / 1000;
				$('#lobi_process_time').html(timeout.toFixed(2));
				$this.setProgress(parseFloat(adjust_success / adjust_size)*100);
				if(parseFloat(adjust_success / adjust_size)*100 == 100){
					$this.destroy();
					Lobibox.notify('success', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 3000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: 'ปรับปรุงวันค้างชำระแล้วครับ'
					});	
					clearInterval(AdjustDeley);
				}
			}
		}
	});
}




































