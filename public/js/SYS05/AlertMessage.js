//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');
//หน้าแรก
$(function(){
	$('#LOCAT1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});
	
//กดเพิ่มข้อมูล	
$('#bth1add').click(function(){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/AlertMessage/getfromAlertMessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกข้อความเตือน',
				//width: $(window).width(),
				//height: $(window).height(),
				width:'100%',
				height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($this){
					Add_HoldtoOldcar($this);
				}
	
			});			
		}
	});
});

function Add_HoldtoOldcar($thisWindowChange){
	
	$('#btndelete_alertmsg').attr('disabled',true);
	
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_AlertMsg',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: true,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%',
	});	
	
	$('#DISCRIPTION').hover(function() {
		document.getElementById("DISCRIPTION").style.filter = "contrast(70%)";
	}, function() {
		document.getElementById("DISCRIPTION").style.filter = "contrast(100%)";
	});
	description();
	
	//_insert = 'T';
	if(_insert == 'T'){
		$('#btnsave_alertmsg').attr('disabled',false);
	}else{
		$('#btnsave_alertmsg').attr('disabled',true);
	}
	
	$('#btnsave_alertmsg').click(function(){
		Save_alertmsg($thisWindowChange);
		$('#resultt_AlertMessage').hide(); 
	});
}

function description(){
	$('#DISCRIPTION').click(function(){
		$content = "1. ข้อความสีแดง สำหรับเตือน ที่ไม่ต้องการให้แก้ไขข้อความ<br>2. ข้อความสีน้ำเงิน สำหรับติดต่อสื่อสารในแต่ละแผนก ซึ่งสามารถเพิ่มเติมข้อความได้<br><br>จะมีผลที่หน้าเมนูต่อไปนี้<br>- ระบบการเงิน หน้ารับชำระเงิน<br>- ระบบการเงิน หน้าสอบถาม<br>- ระบบลูกหนี้ หน้าสอบถาม<br>- ระบบทะเบียน หน้าบันทึกรับจดทะเบียน";
		Lobibox.window({
			title: 'การใช้งาน',
			width:'100%',
			height:'100%',
			content: $content,
			draggable: true,
			closeOnEsc: false,
		});	
	});
}

function Save_alertmsg($thisWindowChange){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกข้อความเตือน หรือไม่',
		buttons: {
			ok : {
				'class': 'btn btn-primary',
				text: 'ยืนยัน',
				closeOnClick: true,
			},
			cancel : {
				'class': 'btn btn-danger',
				text: 'ยกเลิก',
				closeOnClick: true
			},
		},
		
		callback: function(lobibox, type){
			if (type === 'ok'){
				dataToPost = new Object();
				var useredit = "";
				if($("#notedit").is(":checked")){ useredit = "notedit";}
				if($("#edit").is(":checked")){ useredit = "edit";}
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
				dataToPost.CREATEDT = $('#CREATEDT').val();
				dataToPost.STARTDT 	= $('#STARTDT').val();
				dataToPost.ENDDT 	= $('#ENDDT').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				dataToPost.useredit = useredit;
				
				if(dataToPost.CONTNO == "" || dataToPost.CREATEDT == "" || dataToPost.STARTDT == "" || dataToPost.ENDDT == "" || dataToPost.MEMO == ""){
					$msg = "";
					if(dataToPost.MEMO == ""){
						$msg = "กรุณาระบุข้อความเตือน";
					}else{
						$msg = "ระบุข้อมูลไม่ครบถ้วน";
					}
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
						soundExt: '.ogg',
						icon: true,
						messageHeight: '90vh',
						msg: $msg
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS05/AlertMessage/Save_alertmsg',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowChange.destroy();
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else if(data.status == 'W'){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else if(data.status == 'E'){
								Lobibox.notify('error', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}
			}
		}
	});
}

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	$('#resultt_AlertMessage').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 	= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 	= $('#CONTNO1').val();
	dataToPost.FROMDATE = $('#FROMDATE').val();
	dataToPost.TODATE 	= $('#TODATE').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_AlertMessage').html('');
	$('#resultt_AlertMessage').append(spinner);
	
	$.ajax({
		url:'../SYS05/AlertMessage/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_AlertMessage').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_AlertMessage').html(data.html);
			
			$('#table-alertmsg').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-alertmsg',1,340);
			
			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$('.getit').click(function(){	
					var	CONTNO 	= $(this).attr('CONTNO'); 
					var	LOCAT 	= $(this).attr('LOCAT');
					var	CREATEDT= $(this).attr('CREATEDT');
					var	STARTDT = $(this).attr('STARTDT');
					var	ENDDT 	= $(this).attr('ENDDT');
					var	USERID 	= $(this).attr('USERID');
					var	MEMO1 	= $(this).attr('MEMO1');
					loadform(CONTNO,LOCAT,CREATEDT,STARTDT,ENDDT,USERID,MEMO1);
				});
			}		
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function loadform(CONTNO,LOCAT,CREATEDT,STARTDT,ENDDT,USERID,MEMO1){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/AlertMessage/getfromAlertMessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกข้อความเตือน',
				//width: $(window).width(),
				//height: $(window).height(),
				width:'100%',
				height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($this){	
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNO').empty();
					$('#CONTNO').append(newOption).trigger('change'); 
					
					$('#CREATEDT').val(CREATEDT);
					$('#STARTDT').val(STARTDT);
					$('#ENDDT').val(ENDDT);
					$('#MEMO').val(MEMO1);
					
					$('#CONTNO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });

					$('#CREATEDT').attr('disabled',true);
					$('#STARTDT').attr('disabled',true);
					$('#ENDDT').attr('disabled',true);
					$('#edit').attr('disabled',true);
					$('#notedit').attr('disabled',true);
					$('#btncancel_alertmsg').attr('disabled',true);
					
					if(USERID == 'XX'){
						document.getElementById("edit").checked = true;
					}else{
						document.getElementById("notedit").checked = true;
					}
					
					description();

					if(_delete == 'T'){ //มีสิทธิ์ลบหม
						if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
							$('#btndelete_alertmsg').attr('disabled',false);
						}else{
							$('#btndelete_alertmsg').attr('disabled',true);
						}
					}else{
						$('#btndelete_alertmsg').attr('disabled',true);
					}
					
					$('#btndelete_alertmsg').click(function(){ 
						Delete_alertmsg($this,MEMO1);
					});

					if(_level == '1'){
						$('#btnsave_alertmsg').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_alertmsg').attr('disabled',false);
							}else{
								$('#btnsave_alertmsg').attr('disabled',true);
								$('#MEMO').attr('disabled',true);
							}
						}else{
							$('#btnsave_alertmsg').attr('disabled',true);
							$('#MEMO').attr('disabled',true);
						}
					}
					$('#btnsave_alertmsg').click(function(){ 
						Edit_alertmsg($this,MEMO1);
					});
					
					
				}
			});			
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function Delete_alertmsg($thisWindowDel, MEMOold){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการลบข้อความแจ้งเตือน หรือไม่',
		buttons: {
			ok : {
				'class': 'btn btn-primary',
				text: 'ยืนยัน',
				closeOnClick: true,
			},
			cancel : {
				'class': 'btn btn-danger',
				text: 'ยกเลิก',
				closeOnClick: true
			},
		},
		
		callback: function(lobibox, type){
			if (type === 'ok'){
				dataToPost = new Object();
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val() );
				dataToPost.STARTDT 	= $('#STARTDT').val();
				dataToPost.ENDDT 	= $('#ENDDT').val();
				dataToPost.MEMOold 	= MEMOold;

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/AlertMessage/Delete_alertmsg',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if(data.status == 'S'){
							$thisWindowDel.destroy();
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}else if(data.status == 'W'){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}else if(data.status == 'E'){
							Lobibox.notify('error', {
								title: 'ผิดพลาด',
								size: 'mini',
								closeOnClick: false,
								delay: false,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						search();
					}
				});
			}
		}
	});
}

function Edit_alertmsg($thisWindowEdit, MEMOold){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขข้อความแจ้งเตือน หรือไม่',
		buttons: {
			ok : {
				'class': 'btn btn-primary',
				text: 'ยืนยัน',
				closeOnClick: true,
			},
			cancel : {
				'class': 'btn btn-danger',
				text: 'ยกเลิก',
				closeOnClick: true
			},
		},
		
		callback: function(lobibox, type){
			if (type === 'ok'){
				dataToPost = new Object();
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val() );
				dataToPost.STARTDT 	= $('#STARTDT').val();
				dataToPost.ENDDT 	= $('#ENDDT').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				dataToPost.MEMOold 	= MEMOold;

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/AlertMessage/Edit_alertmsg',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if(data.status == 'S'){
							$thisWindowEdit.destroy();
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}else if(data.status == 'W'){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}else if(data.status == 'E'){
							Lobibox.notify('error', {
								title: 'ผิดพลาด',
								size: 'mini',
								closeOnClick: false,
								delay: false,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						search();
					},
					error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
				});
			}
		}
	});
}