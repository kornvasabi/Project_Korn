//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');
var _vat  	= $('.b_tab1[name="home"]').attr('vat');

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
		url:'../SYS05/HoldtoOldcar/getfromHoldtoOldcar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกเปลี่ยนรถยึดเป็นรถเก่า (เพื่อขาย)',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_HoldtoOldcar($this);
				}
	
			});			
		}
	});
});

function Add_HoldtoOldcar($thisWindowChange){
	
	$('#btndel_holdtooldcar').attr('disabled',true);
	
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_HOLDTOOLDCAR',
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
			url : '../SYS05/HoldtoOldcar/searchCONTNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				//GCODENEW(data.GCODE);
				if(contno != ''){
					$('#LOCAT').val(data.CRLOCAT);
					$('#CUSNAME').val(data.CUSNAME);
					$('#CUSCOD').val(data.CUSCOD);
					$('#REGNO').val(data.REGNO);
					$('#STRNO').val(data.STRNO);
					$('#PRICE').val(data.TOTPRC);
					$('#SMPAY').val(data.SMPAY);
					$('#BALANCE').val(data.BALANCE);
					$('#NETAR').val(data.EXP_AMT);
					$('#BOOKVALUE').val(data.BOOKVALUE);
					$('#SALEVAT').val(data.VATPRC);
					$('#LOCATR').val(data.CRLOCAT);
					$('#SALENEW').val(data.NEWPRC);	
					
					newOption = new Option('('+data.GCODE+') '+data.GDESC, data.GCODE, false, false);
					$('#GCODENEW').empty();
					$('#GCODENEW').append(newOption).trigger('change'); 
					
					newOption = new Option(data.BILLCOLL+' - '+data.NAME, data.BILLCOLL, false, false);
					$('#Y_USER').empty();
					$('#Y_USER').append(newOption).trigger('change'); 
					
				}else{
					$('#LOCAT').val('');
					$('#CUSNAME').val('');
					$('#CUSCOD').val('');
					$('#REGNO').val('');
					$('#STRNO').val('');
					$('#PRICE').val('');
					$('#SMPAY').val('');
					$('#BALANCE').val('');
					$('#NETAR').val('');
					$('#BOOKVALUE').val('');
					$('#SALEVAT').val('');
					$('#COST').val('');
					$('#COSTVAT').val('');
					$('#DATECHG').val(_today);
					$('#LOCATR').val('');
					$('#SALENEW').val('');
					$('#GCODENEW').empty().trigger('change');
					$('#TYPHOLD').empty().trigger('change');
					$('#Y_USER').empty().trigger('change');
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
	
	$('#GCODENEW').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getGCode_ExchangCar',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
				dataToPost.GCODEold = (typeof $('#GCODENEW').find(':selected').val() === 'undefined' ? '':$('#GCODENEW').find(':selected').val());
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
	
	$('#TYPHOLD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getTYPHOLD',
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
		dropdownAutoWidth : false,
		width: '100%'
	});
	$('#TYPHOLD').on('select2:open', function (e) {
	  $(".select2-results__options").height(135);
	});

	$('#Y_USER').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getOFFICER',
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
		dropdownAutoWidth : false,
		width: '100%'
	});
	$('#Y_USER').on('select2:open', function (e) {
	  $(".select2-results__options").height(135);
	});
	
	//_insert = 'T';
	if(_insert == 'T'){
		$('#btnsave_holdtooldcar').attr('disabled',false);
	}else{
		$('#btnsave_holdtooldcar').attr('disabled',true);
	}
	
	$('#btnsave_holdtooldcar').click(function(){
		Save_holdtooldcar($thisWindowChange);
		$('#resultt_HoldtoOldcar').hide(); 
	});
}


function Save_holdtooldcar($thisWindowChange){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกรถเปลี่ยนรถยึดเป็นรถเก่า (เพื่อขาย) หรือไม่',
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
				dataToPost.GCODENEW = (typeof $('#GCODENEW').find(':selected').val() === 'undefined' ? '':$('#GCODENEW').find(':selected').val());
				dataToPost.TYPHOLD 	= (typeof $('#TYPHOLD').find(':selected').val() === 'undefined' ? '':$('#TYPHOLD').find(':selected').val());
				dataToPost.Y_USER 	= (typeof $('#Y_USER').find(':selected').val() === 'undefined' ? '':$('#Y_USER').find(':selected').val());
				dataToPost.STRNO 	= $('#STRNO').val();
				dataToPost.BOOKVAL 	= $('#BOOKVALUE').val();
				dataToPost.SALEVAT 	= $('#SALEVAT').val();
				dataToPost.COST 	= $('#COST').val();
				dataToPost.COSTVAT 	= $('#COSTVAT').val();
				dataToPost.DATECHG 	= $('#DATECHG').val();
				dataToPost.SALENEW 	= $('#SALENEW').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				
				if(dataToPost.BOOKVAL == "" || dataToPost.COST == "" || dataToPost.SALENEW == "" || dataToPost.TYPHOLD == ""){	
					var $msg = "";
					if(dataToPost.BOOKVAL == ""){
						$msg = "กรุณาระบุ มูลค่าคงเหลือตามบัญชี";
					}else if(dataToPost.COST == ""){
						$msg = "กรุณาระบุ มูลค่าต้นทุน (ไม่รวม VAT)";
					}else if(dataToPost.SALENEW == ""){
						$msg = "กรุณาระบุ ราคาขายใหม่";
					}else if(dataToPost.TYPHOLD == ""){
						$msg = "กรุณาระบุ เหตุที่บอกเลิกสัญญา";
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
						url:'../SYS05/HoldtoOldcar/Save_holdtooldcar',
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
	$('#resultt_HoldtoOldcar').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.STRNO1 = $('#STRNO1').val();
	dataToPost.FROMDATECHG 	= $('#FROMDATECHG').val();
	dataToPost.TODATECHG 	= $('#TODATECHG').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_HoldtoOldcar').html('');
	$('#resultt_HoldtoOldcar').append(spinner);
	
	$.ajax({
		url:'../SYS05/HoldtoOldcar/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_HoldtoOldcar').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_HoldtoOldcar').html(data.html);
			
			$('#table-holdtooldcar').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-holdtooldcar',1,340);
			
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
					var	LOCAT 	= $(this).attr('CRLOCAT');
					var	CUSCOD 	= $(this).attr('CUSCOD');
					var	CUSNAME = $(this).attr('CUSNAME');
					var	REGNO 	= $(this).attr('REGNO');
					var	STRNO 	= $(this).attr('STRNO');
					var	TOTPRC 	= $(this).attr('TOTPRC');
					var	SMPAY 	= $(this).attr('SMPAY');
					var	TOTBAL 	= $(this).attr('TOTBAL');
					var	EXP_AMT = $(this).attr('EXP_AMT');
					var	BOOKVAL = $(this).attr('BOOKVAL');
					var	BOOKVAT = $(this).attr('BOOKVAT');
					var	COST 	= $(this).attr('COST');
					var	COSTVAT = $(this).attr('COSTVAT');
					var	DATECHG = $(this).attr('YDATE');
					var	RCVLOCAT= $(this).attr('RCVLOCAT');
					var	STDPRC 	= $(this).attr('STDPRC');
					var	N_GCODE = $(this).attr('GCODE');
					var	GDESC 	= $(this).attr('GDESC');
					var	BILLCOLL= $(this).attr('BILLCOLL');
					var	NAME 	= $(this).attr('NAME');
					var	TYPHOLD = $(this).attr('TYPHOLD');
					var	HOLDESC = $(this).attr('HOLDESC');
					var	MEMO1 	= $(this).attr('MEMO1');
					loadform(
						CONTNO,LOCAT,CUSCOD,CUSNAME,REGNO,STRNO,TOTPRC,SMPAY,TOTBAL,EXP_AMT,BOOKVAL,
						BOOKVAT,COST,COSTVAT,DATECHG,RCVLOCAT,STDPRC,N_GCODE,GDESC,BILLCOLL,NAME,TYPHOLD,HOLDESC,MEMO1);
				});
			}		
		}
	});
}

function loadform(CONTNO,LOCAT,CUSCOD,CUSNAME,REGNO,STRNO,TOTPRC,SMPAY,TOTBAL,EXP_AMT,BOOKVAL,BOOKVAT,COST,COSTVAT,DATECHG,RCVLOCAT,STDPRC,N_GCODE,GDESC,BILLCOLL,NAME,TYPHOLD,HOLDESC,MEMO1){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/HoldtoOldcar/getfromHoldtoOldcar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกเปลี่ยนรถยึดเป็นรถเก่า (เพื่อขาย)',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){	
					$('#GCODENEW').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getGCode_ExchangCar',
							data: function (params) {
								dataToPost = new Object();
								//dataToPost.GCODEold = N_GCODE;
								dataToPost.GCODEold = "";
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
						allowClear: false,
						multiple: false,
						dropdownParent: $(".lobibox-body"),
						//disabled: true,
						//theme: 'classic',
						dropdownAutoWidth : true,
						width: '100%'
					});
					
					$('#TYPHOLD').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getTYPHOLD',
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
						allowClear: false,
						multiple: false,
						dropdownParent: $(".lobibox-body"),
						//disabled: true,
						//theme: 'classic',
						dropdownAutoWidth : false,
						width: '100%'
					});
					$('#TYPHOLD').on('select2:open', function (e) {
					  $(".select2-results__options").height(135);
					});

					$('#Y_USER').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getOFFICER',
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
						dropdownAutoWidth : false,
						width: '100%'
					});
					$('#Y_USER').on('select2:open', function (e) {
					  $(".select2-results__options").height(135);
					});
	
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNO').empty();
					$('#CONTNO').append(newOption).trigger('change'); 
					newOption = new Option('('+N_GCODE+') '+GDESC, N_GCODE, false, false);
					$('#GCODENEW').empty();
					$('#GCODENEW').append(newOption).trigger('change'); 
					newOption = new Option('('+TYPHOLD+') '+HOLDESC, TYPHOLD, false, false);
					$('#TYPHOLD').empty();
					$('#TYPHOLD').append(newOption).trigger('change'); 
					newOption = new Option(NAME+' ('+BILLCOLL+')', BILLCOLL, false, false);
					$('#Y_USER').empty();
					$('#Y_USER').append(newOption).trigger('change'); 
					
					$('#LOCAT').val(LOCAT);
					$('#CUSNAME').val(CUSNAME);
					$('#CUSCOD').val(CUSCOD);
					$('#REGNO').val(REGNO);
					$('#STRNO').val(STRNO);
					$('#PRICE').val(TOTPRC);
					$('#SMPAY').val(SMPAY);
					$('#BALANCE').val(TOTBAL);
					$('#NETAR').val(EXP_AMT);
					$('#BOOKVALUE').val(BOOKVAL);
					$('#SALEVAT').val(BOOKVAT);
					$('#COST').val(COST);
					$('#COSTVAT').val(COSTVAT);
					$('#DATECHG').val(DATECHG);
					$('#LOCATR').val(RCVLOCAT);
					$('#SALENEW').val(STDPRC);
					$('#MEMO').val(MEMO1);
					
					$('#CONTNO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });

					$('#LOCAT').attr('disabled',true);
					$('#CUSNAME').attr('disabled',true);
					$('#CUSCOD').attr('disabled',true);
					$('#REGNO').attr('disabled',true);
					$('#STRNO').attr('disabled',true);
					$('#PRICE').attr('disabled',true);
					$('#SMPAY').attr('disabled',true);
					$('#BALANCE').attr('disabled',true);
					$('#NETAR').attr('disabled',true);
					
					//var _update = 'T';
					if(_level == '1'){
						$('#btnsave_holdtooldcar').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_holdtooldcar').attr('disabled',false);
							}else{
								$('#btnsave_holdtooldcar').attr('disabled',true);
								$('#GCODENEW').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#TYPHOLD').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#Y_USER').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#BOOKVALUE').attr('disabled',true);
								$('#SALEVAT').attr('disabled',true);
								$('#COST').attr('disabled',true);
								$('#COSTVAT').attr('disabled',true);
								$('#DATECHG').attr('disabled',true);
								$('#LOCATR').attr('disabled',true);
								$('#SALENEW').attr('disabled',true);
								$('#MEMO').attr('disabled',true);
							}
						}else{
							$('#btnsave_holdtooldcar').attr('disabled',true);
							$('#GCODENEW').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#TYPHOLD').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#Y_USER').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#BOOKVALUE').attr('disabled',true);
							$('#SALEVAT').attr('disabled',true);
							$('#COST').attr('disabled',true);
							$('#COSTVAT').attr('disabled',true);
							$('#DATECHG').attr('disabled',true);
							$('#LOCATR').attr('disabled',true);
							$('#SALENEW').attr('disabled',true);
							$('#MEMO').attr('disabled',true);
						}
					}
					
					$('#btnsave_holdtooldcar').click(function(){ 
						Edit_holdtooldcar($this);
					});
					
					//var _delete = 'T';
					if(_delete == 'T'){ //มีสิทธิ์แก้ไขไหม
						if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
							$('#btndel_holdtooldcar').attr('disabled',false);
						}else{
							$('#btndel_holdtooldcar').attr('disabled',true);
						}
					}else{
						$('#btndel_holdtooldcar').attr('disabled',true);
					}	
					
					$('#btndel_holdtooldcar').click(function(){ 
						Delete_holdtooldcar($this);
					});
				}
			});			
		}
	});
}

function Edit_holdtooldcar($thisWindowEdit){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขรายการเปลี่ยนรถยึดเป็นรถเก่า (เพื่อขาย) หรือไม่',
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
				dataToPost.GCODENEW = (typeof $('#GCODENEW').find(':selected').val() === 'undefined' ? '':$('#GCODENEW').find(':selected').val());
				dataToPost.TYPHOLD 	= (typeof $('#TYPHOLD').find(':selected').val() === 'undefined' ? '':$('#TYPHOLD').find(':selected').val());
				dataToPost.Y_USER 	= (typeof $('#Y_USER').find(':selected').val() === 'undefined' ? '':$('#Y_USER').find(':selected').val());
				dataToPost.STRNO 	= $('#STRNO').val();
				dataToPost.BOOKVAL 	= $('#BOOKVALUE').val();
				dataToPost.SALEVAT 	= $('#SALEVAT').val();
				dataToPost.COST 	= $('#COST').val();
				dataToPost.COSTVAT 	= $('#COSTVAT').val();
				dataToPost.DATECHG 	= $('#DATECHG').val();
				dataToPost.SALENEW 	= $('#SALENEW').val();
				dataToPost.MEMO 	= $('#MEMO').val();

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/HoldtoOldcar/Edit_holdtooldcar',
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
	});
}

function Delete_holdtooldcar($thisWindowDel){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการลบรายการเปลี่ยนรถยึดเป็นรถเก่า (เพื่อขาย) หรือไม่',
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
				dataToPost.CONTNO = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val() );
				dataToPost.CUSCOD = $('#CUSCOD').val();
				dataToPost.STRNO = $('#STRNO').val();

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/HoldtoOldcar/Delete_holdtooldcar',
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