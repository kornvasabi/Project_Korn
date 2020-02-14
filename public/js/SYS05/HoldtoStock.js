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
		url:'../SYS05/HoldtoStock/getfromHoldtoStock',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการรถยึดเข้าสต็อก (รอไถ่ถอน)',
				width: $(window).width(),
				height: $(window).height(),
				//width:'60%',
				//height:'80%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_HoldtoStock($this);
				}
	
			});			
		}
	});
});

function Add_HoldtoStock($thisWindowChange){
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_HoldtoStock',
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
			url : '../SYS05/HoldtoStock/searchCONTNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				if(contno != ''){
					$('#LOCAT').val(data.LOCAT);
					$('#CUSNAME').val(data.CUSNAME);
					$('#CUSCOD').val(data.CUSCOD);
					$('#STRNO').val(data.STRNO);
					$('#TOTPRC').val(data.TOTPRC);
					$('#SMPAY').val(data.SMPAY);
					$('#BALANCE').val(data.BALANCE);
					$('#EXP_AMT').val(data.EXP_AMT);
					$('#DATEHOLD').val(data.YDATE);
					
					newOption = new Option(data.LOCAT, data.LOCAT, false, false);
					$('#RVLOCAT').empty();
					$('#RVLOCAT').append(newOption).trigger('change');
					
					if(data.YSTAT == 'Y'){
						document.getElementById("YSTAT_Y").checked = true;
						$('#DATEHOLD').attr('disabled',true);
					}else{
						document.getElementById("YSTAT_N").checked = true;
						$('#DATEHOLD').attr('disabled',false);
					}
					
					$('#DATEHOLD').change(function(){
						if($("#YSTAT_N").is(":checked")){
							$('#DATEHOLD').val('');
							Lobibox.window({
								title: 'แจ้งเตือน',
								width:'30%',
								height:'20%',
								content: 'กรุณาระบุสถานะรถเป็น รถยึดก่อน',
								draggable: true,
								closeOnEsc: false,
							});	
						}
					});
				}else{
					$('#LOCAT').val('');
					$('#CUSNAME').val('');
					$('#CUSCOD').val('');
					$('#STRNO').val('');
					$('#TOTPRC').val('');
					$('#SMPAY').val('');
					$('#BALANCE').val('');
					$('#EXP_AMT').val('');
					$('#DATEHOLD').val('');
					$('#RVLOCAT').empty().trigger('change');
					document.getElementById("YSTAT_Y").checked = false;
					document.getElementById("YSTAT_N").checked = false;
			
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
	
	$('#RVLOCAT').select2({
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	//_insert = 'T';
	if(_insert == 'T'){
		$('#btnsave_holdtostock').attr('disabled',false);
	}else{
		$('#btnsave_holdtostock').attr('disabled',true);
	}
	
	$('#btnsave_holdtostock').click(function(){
		Save_holdtostock($thisWindowChange);
		$('#resultt_HoldtoStock').hide(); 
	});
	
	$('#btnclr_holdtostock').click(function(){
		$('#CONTNO').empty().trigger('change');
		$('#LOCAT').val('');
		$('#CUSNAME').val('');
		$('#CUSCOD').val('');
		$('#STRNO').val('');
		$('#TOTPRC').val('');
		$('#SMPAY').val('');
		$('#BALANCE').val('');
		$('#EXP_AMT').val('');
		$('#DATEHOLD').val('');
		$('#RVLOCAT').empty().trigger('change');
		document.getElementById("YSTAT_Y").checked = false;
		document.getElementById("YSTAT_N").checked = false;
	});
}

function Save_holdtostock($thisWindowChange){
	dataToPost = new Object();
	var YSTAT = "";
	if($("#YSTAT_Y").is(":checked")){ YSTAT = "Y";}
	if($("#YSTAT_N").is(":checked")){ YSTAT = "N";}
	dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	dataToPost.RVLOCAT 	= (typeof $('#RVLOCAT').find(':selected').val() === 'undefined' ? '':$('#RVLOCAT').find(':selected').val());
	dataToPost.CUSCOD 	= $('#CUSCOD').val();
	dataToPost.STRNO 	= $('#STRNO').val();
	dataToPost.DATEHOLD = $('#DATEHOLD').val();
	dataToPost.YSTAT 	= YSTAT;
	
	var $msgconfirm = "";
	if(YSTAT == 'Y'){
		$msgconfirm = '<center>คุณต้องการบันทึก เลขตัวถัง '+dataToPost.STRNO+'<br>เป็น <font color=red>รถยึด</font> หรือไม่</center>';
	}else if(YSTAT == 'N'){
		$msgconfirm = '<center>คุณต้องการบันทึก เลขตัวถัง '+dataToPost.STRNO+'<br>เป็น <font color=blue>รถปกติ</font> หรือไม่</center>';
	}
	
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: $msgconfirm ,
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
				if(dataToPost.CONTNO == "" || dataToPost.DATEHOLD == "" || dataToPost.RVLOCAT == ""){
					var $msg = "";
					if(dataToPost.CONTNO == ""){
						$msg = "กรุณาเลือกสัญญา";
					}else if(dataToPost.DATEHOLD == ""){
						$msg = "กรุณาระบุวันที่ยึด";
					}else if(dataToPost.RVLOCAT == ""){
						$msg = "กรุณาระบุวสาขาที่เก็บรถ";
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
						url:'../SYS05/HoldtoStock/Save_holdtostock',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowChange.destroy();
								if(data.stat == 'Y'){
									Add_Hold(data.contno);
								}
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

function Add_Hold($contno){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/HoldtoStock/getfromHoldtoOldcar',
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
					Add_HoldtoOldcar($this,$contno);
					
					$('#DATECHG2').change(function(){ 
						Add_HoldtoOldcar($this,$contno);
					});	
				}
			});			
		}
	});
}

function Add_HoldtoOldcar($thisWindowChange2,$contno){
	$('#btndel_holdtooldcar').attr('disabled',true);
	var contno =  $contno;
	var dateY =  $('#DATECHG2').val();
	dataToPost = new Object();
	dataToPost.contno = contno;
	dataToPost.dateY = dateY;
	$.ajax({
		url : '../SYS05/HoldtoStock/searchCONTNOtoOldcar',
		data : dataToPost,
		type : "POST",
		dataType : "json",
		success: function(data){
			$('#CONTNO2').select2({
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
			
			$('#GCODENEW2').select2({
				placeholder: 'เลือก',
				ajax: {
					url: '../Cselect2b/getGCode_ExchangCar',
					data: function (params) {
						dataToPost = new Object();
						//dataToPost.now = $('#add_cuscod').find(':selected').val();
						dataToPost.GCODEold = (typeof $('#GCODENEW2').find(':selected').val() === 'undefined' ? '':$('#GCODENEW2').find(':selected').val());
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
			$('#GCODENEW2').on('select2:open', function (e) {
			  $(".select2-results__options").height(100);
			});
			
			$('#TYPHOLD2').select2({
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
			$('#TYPHOLD2').on('select2:open', function (e) {
			  $(".select2-results__options").height(100);
			});

			$('#Y_USER2').select2({
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
			$('#Y_USER2').on('select2:open', function (e) {
			  $(".select2-results__options").height(100);
			});

			$('#LOCAT2').val(data.CRLOCAT);
			$('#CUSNAME2').val(data.CUSNAME);
			$('#CUSCOD2').val(data.CUSCOD);
			$('#REGNO2').val(data.REGNO);
			$('#STRNO2').val(data.STRNO);
			$('#PRICE2').val(data.TOTPRC);
			$('#SMPAY2').val(data.SMPAY);
			$('#BALANCE2').val(data.BALANCE);
			$('#NETAR2').val(data.EXP_AMT);
			$('#BOOKVALUE2').val(data.BOOKVALUE);
			$('#SALEVAT2').val(data.VATPRC);
			$('#LOCATR2').val(data.CRLOCAT);
			$('#SALENEW2').val(data.NEWPRC);	
			$('#SDATE2').val(data.SDATE);	
			$('#DATEDIFF2').val(data.DAYDAIFF);
			
			newOption = new Option(data.CONTNO, data.CONTNO, false, false);
			$('#CONTNO2').empty();
			$('#CONTNO2').append(newOption).trigger('change'); 
			$('#CONTNO2').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			
			newOption = new Option('('+data.GCODE+') '+data.GDESC, data.GCODE, false, false);
			$('#GCODENEW2').empty();
			$('#GCODENEW2').append(newOption).trigger('change'); 
			
			newOption = new Option(data.BILLCOLL+' - '+data.NAME, data.BILLCOLL, false, false);
			$('#Y_USER2').empty();
			$('#Y_USER2').append(newOption).trigger('change'); 
			
			//_insert = 'T';
			if(_insert == 'T'){
				$('#btnsave_holdtooldcar').attr('disabled',false);
			}else{
				$('#btnsave_holdtooldcar').attr('disabled',true);
			}
			
			$('#btnsave_holdtooldcar').click(function(){
				Save_holdtooldcar($thisWindowChange2);
			});
		}
	});
}

function Save_holdtooldcar($thisWindowChange2){
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
				dataToPost.CONTNO 	= (typeof $('#CONTNO2').find(':selected').val() === 'undefined' ? '':$('#CONTNO2').find(':selected').val());
				dataToPost.GCODENEW = (typeof $('#GCODENEW2').find(':selected').val() === 'undefined' ? '':$('#GCODENEW2').find(':selected').val());
				dataToPost.TYPHOLD 	= (typeof $('#TYPHOLD2').find(':selected').val() === 'undefined' ? '':$('#TYPHOLD2').find(':selected').val());
				dataToPost.Y_USER 	= (typeof $('#Y_USER2').find(':selected').val() === 'undefined' ? '':$('#Y_USER2').find(':selected').val());
				dataToPost.STRNO 	= $('#STRNO2').val();
				dataToPost.BOOKVAL 	= $('#BOOKVALUE2').val();
				dataToPost.SALEVAT 	= $('#SALEVAT2').val();
				dataToPost.COST 	= $('#COST2').val();
				dataToPost.COSTVAT 	= $('#COSTVAT2').val();
				dataToPost.DATECHG 	= $('#DATECHG2').val();
				dataToPost.SALENEW 	= $('#SALENEW2').val();
				dataToPost.MEMO 	= $('#MEMO2').val();
				
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
						url:'../SYS05/HoldtoStock/Save_holdtooldcar',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowChange2.destroy();
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
	$('#resultt_HoldtoStock').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.STRNO1 		= $('#STRNO1').val();
	dataToPost.FROMDATEHOLD = $('#FROMDATEHOLD').val();
	dataToPost.TODATEHOLD 	= $('#TODATEHOLD').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_HoldtoStock').html('');
	$('#resultt_HoldtoStock').append(spinner);
	
	$.ajax({
		url:'../SYS05/HoldtoStock/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_HoldtoStock').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_HoldtoStock').html(data.html);
			
			$('#table-changecontstat').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-changecontstat',1,320);
			
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
					var	YDATE 	= $(this).attr('YDATE');
					var	LOCAT 	= $(this).attr('LOCAT');
					var	CUSNAME = $(this).attr('CUSNAME');
					var	CUSCOD 	= $(this).attr('CUSCOD');
					var	STRNO 	= $(this).attr('STRNO');
					var	TOTPRC 	= $(this).attr('TOTPRC');
					var	SMPAY 	= $(this).attr('SMPAY');
					var	BALANCE = $(this).attr('BALANCE');
					var	EXP_AMT = $(this).attr('EXP_AMT');
					var	YSTAT 	= $(this).attr('YSTAT');
					var	RVLOCAT = $(this).attr('RVLOCAT');
					loadform(CONTNO,YDATE,LOCAT,CUSNAME,CUSCOD,STRNO,TOTPRC,SMPAY,BALANCE,EXP_AMT,YSTAT,RVLOCAT);
				});
			}		
		}
	});
}

function loadform(CONTNO,YDATE,LOCAT,CUSNAME,CUSCOD,STRNO,TOTPRC,SMPAY,BALANCE,EXP_AMT,YSTAT,RVLOCAT){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/HoldtoStock/getfromHoldtoStock',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการรถยึดเข้าสต็อก (รอไถ่ถอน)',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){	
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNO').empty();
					$('#CONTNO').append(newOption).trigger('change'); 
					newOption = new Option(RVLOCAT, RVLOCAT, false, false);
					$('#RVLOCAT').empty();
					$('#RVLOCAT').append(newOption).trigger('change'); 

					$('#DATEHOLD').val(YDATE);
					$('#LOCAT').val(LOCAT);
					$('#CUSNAME').val(CUSNAME);
					$('#CUSCOD').val(CUSCOD);
					$('#STRNO').val(STRNO);
					$('#TOTPRC').val(TOTPRC);
					$('#SMPAY').val(SMPAY);
					$('#BALANCE').val(BALANCE);
					$('#EXP_AMT').val(EXP_AMT);
					$('#DATEHOLD').val(YDATE);
					
					if(YSTAT == 'Y'){
						document.getElementById("YSTAT_Y").checked = true;
					}else if(YSTAT == 'N'){
						document.getElementById("YSTAT_N").checked = true;
					}
					
					$('#CONTNO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#RVLOCAT').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#DATEHOLD').attr('disabled',true);
					
					$('#btnclr_holdtostock').attr('disabled',true);
					
					//var _update = 'T';
					if(_level == '1'){
						$('#btnsave_holdtostock').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_holdtostock').attr('disabled',false);
							}else{
								$('#btnsave_holdtostock').attr('disabled',true);
								$('#YSTAT_Y').attr('disabled',true);
								$('#YSTAT_N').attr('disabled',true);
							}
						}else{
							$('#btnsave_holdtostock').attr('disabled',true);
							$('#YSTAT_Y').attr('disabled',true);
							$('#YSTAT_N').attr('disabled',true);
						}
					}
					
					$('#btnsave_holdtostock').click(function(){ 
						Edit_holdtostock($this);
					});
					
				}
			});			
		}
	});
}

function Edit_holdtostock($thisWindowChange3){
	dataToPost = new Object();
	var YSTAT = "";
	if($("#YSTAT_Y").is(":checked")){ YSTAT = "Y";}
	if($("#YSTAT_N").is(":checked")){ YSTAT = "N";}
	dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	dataToPost.RVLOCAT 	= (typeof $('#RVLOCAT').find(':selected').val() === 'undefined' ? '':$('#RVLOCAT').find(':selected').val());
	dataToPost.CUSCOD 	= $('#CUSCOD').val();
	dataToPost.STRNO 	= $('#STRNO').val();
	dataToPost.YSTAT 	= YSTAT;

	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขรถยึดเข้าสต็อก (รอไถ่ถอน) หรือไม่' ,
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
				if(dataToPost.YSTAT == "Y"){
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
						msg: 'สัญญานี้อยู่ในสถานะ รถยึดเข้าสต็อก (รอไถ่ถอน) แล้ว'
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS05/HoldtoStock/Edit_holdtostock',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowChange3.destroy();
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