//BEE+
// หน้าแรก  
var _locat  	= $('.b_tab1[name="home"]').attr('locat');
var _insert 	= $('.b_tab1[name="home"]').attr('cin');
var _update 	= $('.b_tab1[name="home"]').attr('cup');
var _delete 	= $('.b_tab1[name="home"]').attr('cdel');
var _level  	= $('.b_tab1[name="home"]').attr('clev');
var _today  	= $('.b_tab1[name="home"]').attr('today');
var _usergroup  = $('.b_tab1[name="home"]').attr('usergroup');

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
		url:'../SYS05/ChangeContstat/getfromChangeContstat',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกเปลี่ยนสถานะสัญญา',
				width: $(window).width(),
				height: $(window).height(),
				//width:'60%',
				//height:'80%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_ChangeContstat($this);
				}
	
			});			
		}
	});
});

function Add_ChangeContstat($thisWindowChange){
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_ChangeContstat',
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
	
	CONTNOCHANGE = null
	$('#CONTNO').change(function(){ 
		var contno =  (typeof $('#CONTNO').find(":selected").val() === 'undefined' ? '' : $('#CONTNO').find(":selected").val());
		dataToPost = new Object();
		dataToPost.contno = contno;
			
		CONTNOCHANGE = $.ajax({
			url : '../SYS05/ChangeContstat/searchCONTNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				if(contno != ''){
					$('#FROMSTAT').val(data.CONTSTAT);
					$('#EXP_PRD').val(data.EXP_PRD);
					$('#EXP_AMT').val(data.EXP_AMT);
					$('#FROMBILL').val(data.USERNAME);
				}else{
					$('#DATECHG').val(_today);
					$('#FROMSTAT').val('');
					$('#EXP_PRD').val('');
					$('#EXP_AMT').val('');
					$('#FROMBILL').val('');
					$('#TOSTAT').empty().trigger('change');
					$('#TOBILL').empty().trigger('change');
					$('#MEMO').val('');
				}
				CONTNOCHANGE = null;
			},
			beforeSend: function(){
				if(CONTNOCHANGE !== null){
					CONTNOCHANGE.abort();
				}
			}
		});
	});

	$('#TOSTAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getTYPCONT_ChangeContstat',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.TYPCONTold = $('#FROMSTAT').val();
				//dataToPost.TYPCONTold = (typeof TYPCONT === 'undefined' ? '' : TYPCONT);
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
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	$('#TOBILL').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.TYPCONTold = (typeof TYPCONT === 'undefined' ? '' : TYPCONT);
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
		dropdownAutoWidth : true,
		width: '100%'
	});

	//_insert = 'T';
	if(_insert == 'T'){
		$('#btnsave_changecontstat').attr('disabled',false);
	}else{
		$('#btnsave_changecontstat').attr('disabled',true);
	}
	
	$('#btnsave_changecontstat').click(function(){
		Save_changecontstat($thisWindowChange);
		$('#resultt_ChangeContstat').hide(); 
	});
	
	$('#btnclr_changecontstat').click(function(){
		$('#CONTNO').empty().trigger('change');
		$('#DATECHG').val(_today);
		$('#FROMSTAT').val('');
		$('#EXP_PRD').val('');
		$('#EXP_AMT').val('');
		$('#FROMBILL').val('');
		$('#TOSTAT').empty().trigger('change');
		$('#TOBILL').empty().trigger('change');
		$('#MEMO').val('');
	});
}


function Save_changecontstat($thisWindowChange){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกเปลี่ยนสถานะสัญญาหรือไม่',
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
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
				dataToPost.TOSTAT = (typeof $('#TOSTAT').find(':selected').val() === 'undefined' ? '':$('#TOSTAT').find(':selected').val());
				dataToPost.TOBILL = (typeof $('#TOBILL').find(':selected').val() === 'undefined' ? '':$('#TOBILL').find(':selected').val());
				dataToPost.DATECHG 	= $('#DATECHG').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				if(dataToPost.TOSTAT == ""){	
					var $msg = "";
					if(dataToPost.TOSTAT == ""){
						$msg = "กรุณาเลือกสถานะสัญญา";
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
						url:'../SYS05/ChangeContstat/Save_changecontstat',
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
						}
					});
				}
			}
		}
	});
}

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	$('#resultt_ChangeContstat').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 = $('#CONTNO1').val();
	dataToPost.FROMDATECHG 	= $('#FROMDATECHG').val();
	dataToPost.TODATECHG 	= $('#TODATECHG').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_ChangeContstat').html('');
	$('#resultt_ChangeContstat').append(spinner);
	
	$.ajax({
		url:'../SYS05/ChangeContstat/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_ChangeContstat').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_ChangeContstat').html(data.html);
			
			$('#table-ChangeContstat').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-ChangeContstat',1,325);
			
			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$('.getit').click(function(){	
					var	CONTNO 		= $(this).attr('CONTNO'); 
					var	CHGDATE 	= $(this).attr('CHGDATE');
					var	FROMSTAT 	= $(this).attr('FROMSTAT');
					var	CONTDESCFRM = $(this).attr('CONTDESCFRM');
					var	TOSTAT 		= $(this).attr('TOSTAT');
					var	CONTDESCTO 	= $(this).attr('CONTDESCTO');
					var	EXP_PRD 	= $(this).attr('EXP_PRD');
					var	EXP_AMT 	= $(this).attr('EXP_AMT');
					var	FRMBILL 	= $(this).attr('FRMBILL');
					var	FRMBILLNAME = $(this).attr('FRMBILLNAME');
					var	TOBILL 		= $(this).attr('TOBILL');
					var	TOBILLNAME 	= $(this).attr('TOBILLNAME');
					var	MEMO1 		= $(this).attr('MEMO1');
					var	LOCAT 		= $(this).attr('LOCAT');
					loadform(CONTNO,CHGDATE,FROMSTAT,CONTDESCFRM,TOSTAT,CONTDESCTO,EXP_PRD,EXP_AMT,FRMBILL,FRMBILLNAME,TOBILL,TOBILLNAME,MEMO1,LOCAT);
				});
			}		
		}
	});
}

function loadform(CONTNO,CHGDATE,FROMSTAT,CONTDESCFRM,TOSTAT,CONTDESCTO,EXP_PRD,EXP_AMT,FRMBILL,FRMBILLNAME,TOBILL,TOBILLNAME,MEMO1,LOCAT){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ChangeContstat/getfromChangeContstat',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกเปลี่ยนสถานะสัญญา',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					$('#TOSTAT').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getTYPCONT_ChangeContstat',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.TYPCONTold = TOSTAT;
								//dataToPost.TYPCONTold = (typeof TYPCONT === 'undefined' ? '' : TYPCONT);
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
						dropdownAutoWidth : true,
						width: '100%'
					});
					
					$('#TOBILL').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getUSERS',
							data: function (params) {
								dataToPost = new Object();
								//dataToPost.TYPCONTold = (typeof TYPCONT === 'undefined' ? '' : TYPCONT);
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
						dropdownAutoWidth : true,
						width: '100%'
					});
					
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNO').empty();
					$('#CONTNO').append(newOption).trigger('change'); 
					newOption = new Option(TOSTAT+' - '+CONTDESCTO, TOSTAT, false, false);
					$('#TOSTAT').empty();
					$('#TOSTAT').append(newOption).trigger('change'); 
					newOption = new Option(TOBILLNAME+' ('+TOBILL+')', TOBILL, false, false);
					$('#TOBILL').empty();
					$('#TOBILL').append(newOption).trigger('change'); 
					
					$('#DATECHG').val(CHGDATE);
					$('#FROMSTAT').val(FROMSTAT+' - '+CONTDESCFRM);
					$('#EXP_PRD').val(EXP_PRD);
					$('#EXP_AMT').val(EXP_AMT);
					$('#FROMBILL').val(FRMBILLNAME+' ('+FRMBILL+')');
					$('#MEMO').val(MEMO1);
					
					$('#CONTNO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#TOSTAT').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#TOBILL').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#DATECHG').attr('disabled',true);
					$('#FROMSTAT').attr('disabled',true);
					$('#EXP_PRD').attr('disabled',true);
					$('#EXP_AMT').attr('disabled',true);
					$('#FROMBILL').attr('disabled',true);
					$('#MEMO').attr('disabled',true);
					
					//var _update = 'T';
					/*if(_level == '1'){
						$('#btnsave_changecontstat').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_changecontstat').attr('disabled',false);
							}else{
								$('#btnsave_changecontstat').attr('disabled',true);
								$('#TOSTAT').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#TOBILL').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#MEMO').attr('disabled',true);
							}
						}else{
							$('#btnsave_changecontstat').attr('disabled',true);
							$('#TOSTAT').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#TOBILL').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#MEMO').attr('disabled',true);
						}
					}
					$('#btnsave_changecontstat').click(function(){ 
						Edit_changecontstat($this,FROMSTAT,TOSTAT,FRMBILL,TOBILL);
					});*/
					$('#btnsave_changecontstat').attr('disabled',true);
					$('#btnclr_changecontstat').attr('disabled',true);
				}
			});			
		}
	});
}

function Edit_changecontstat($thisWindowEdit,FROMSTATold,TOSTATold,FRMBILLold,TOBILLold){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขเปลี่ยนสถานะสัญญาหรือไม่',
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
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
				dataToPost.TOSTAT 	= (typeof $('#TOSTAT').find(':selected').val() === 'undefined' ? '':$('#TOSTAT').find(':selected').val());
				dataToPost.TOBILL 	= (typeof $('#TOBILL').find(':selected').val() === 'undefined' ? '':$('#TOBILL').find(':selected').val());
				dataToPost.MEMO 	= $('#MEMO').val();
				dataToPost.FROMSTATold	= FROMSTATold;
				dataToPost.TOSTATold	= TOSTATold;
				dataToPost.FRMBILLold	= FRMBILLold;
				dataToPost.TOBILLold	= TOBILLold;
				if(dataToPost.TOSTAT == ""){	
					var $msg = "";
					if(dataToPost.TOSTAT == ""){
						$msg = "กรุณาเลือกสถานะสัญญา";
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
						url:'../SYS05/ChangeContstat/Edit_changecontstat',
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
						}
					});
				}
			}
		}
	});
}