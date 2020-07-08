/********************************************************
             ______@04/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#STRNO').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getSTRNO_ST',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.no = "sno";
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#TYPE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTYPECOD',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#MODEL').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getMODELCOD',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#BAAB').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getBAABCOD',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#COLOR').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCOLORCOD',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#CC').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCCCOD',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#btnedit').attr('disabled',true);
	$('#btnsave').attr('disabled',true);
	ClearInput();
});
$('#btnclear').click(function(){
	ClearInput();
});
function ClearInput(){
	$('#btnsave').attr('disabled',true);
	$('#btnedit').attr('disabled',true);
	$('#STRNO').attr('disabled',false);
	$('#ENGNO').attr('disabled',true);
	$('#TYPE').attr('disabled',true);
	$('#MODEL').attr('disabled',true);
	$('#BAAB').attr('disabled',true);
	$('#COLOR').attr('disabled',true);
	$('#CC').attr('disabled',true);
	$('#CONTNO').attr('disabled',true);
	$('#GCODE').attr('disabled',true);
	$('#STAT').attr('disabled',true);
	$('#TSALE').attr('disabled',true);
	$('#MILERT').attr('disabled',true);

	$('#STRNO').empty();
	$('#ENGNO').val('');
	$('#TYPE').empty();
	$('#MODEL').empty();
	$('#BAAB').empty();
	$('#COLOR').empty();
	$('#CC').empty();
	$('#CONTNO').val('');
	$('#GCODE').val('');
	$('#STAT').val('');
	$('#TSALE').val('');
	$('#MILERT').val('');
}
$('#btnedit').click(function(){
	if(_insert == 'T'){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
	//$('#btnsave').attr('disabled',false);
	var strno = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	if(strno !== ''){
		$('#STRNO').attr('disabled',true);

		$('#ENGNO').attr('disabled',false);
		$('#TYPE').attr('disabled',false);
		$('#MODEL').attr('disabled',false);
		$('#BAAB').attr('disabled',false);
		$('#COLOR').attr('disabled',false);
		$('#CC').attr('disabled',false);
		$('#CONTNO').attr('disabled',true);
		$('#GCODE').attr('disabled',false);
		$('#STAT').attr('disabled',false);
		$('#TSALE').attr('disabled',true);
		$('#MILERT').attr('disabled',false);
	}else{
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาเลือกเลขตัวถังรถก่อนครับ'
		});
		$('.lobibox-notify').css({'z-index':'99999','border-radius':'50px'});
	}
});
$('#STRNO').change(function(){
	ChangeDetailCar();
});
var Detailcar = null;
function ChangeDetailCar(){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	Detailcar = $.ajax({
		url: '../SYS02/EditStrno/DetailCar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#btnedit').attr('disabled',false);
			$('#ENGNO').val(data.ENGNO);
			var tocontnoOption = new Option(data.TYPE,data.TYPE, false, false);
			$('#TYPE').empty().append(tocontnoOption).trigger('change');
			var tocontnoOption = new Option(data.MODEL,data.MODEL, false, false);
			$('#MODEL').empty().append(tocontnoOption).trigger('change');
			var tocontnoOption = new Option(data.BAAB,data.BAAB, false, false);
			$('#BAAB').empty().append(tocontnoOption).trigger('change');
			var tocontnoOption = new Option(data.COLOR,data.COLOR, false, false);
			$('#COLOR').empty().append(tocontnoOption).trigger('change');
			var tocontnoOption = new Option(data.CC,data.CC, false, false);
			$('#CC').empty().append(tocontnoOption).trigger('change');
			$('#CONTNO').val(data.CONTNO);
			$('#GCODE').val(data.GCODE);
			$('#STAT').val(data.STAT);
			$('#TSALE').val(data.TSALE);
			$('#MILERT').val(data.MILERT);
			
			//$('#STRNO').attr('disabled',true);
			//$('#CONTNO').attr('disabled',true);
			//$('#TSALE').attr('disabled',true);
			Detailcar = null;
		},
		beforeSend: function(){
			if(Detailcar !== null){ Detailcar.abort(); }
		}
	});
}
$('#btnsave').click(function(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกการเปลี่ยนแปลงรายละเอียดรถนี้ ?',
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
			var btnType;
			if (type === 'ok'){
				fn_save();
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: 'ยังไม่บันทึกรายการ'
				});
				$('.lobibox-notify').css({'z-index':'99999','border-radius':'50px'});
			}
		}
	});
});
var savecardetail = null; 
function fn_save(){
	dataToPost = new Object();
	dataToPost.STRNO  = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	dataToPost.ENGNO  = $('#ENGNO').val();
	dataToPost.TYPE   = (typeof $('#TYPE').find(':selected').val() === 'undefined' ? '':$('#TYPE').find(':selected').val());
	dataToPost.MODEL  = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
	dataToPost.BAAB   = (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
	dataToPost.COLOR  = (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '':$('#COLOR').find(':selected').val());
	dataToPost.CC     = (typeof $('#CC').find(':selected').val() === 'undefined' ?'':$('#CC').find(':selected').val());
	dataToPost.GCODE  = $('#GCODE').val();
	dataToPost.STAT   = $('#STAT').val();
	dataToPost.MILERT = $('#MILERT').val();
	$('#loadding').fadeIn(200);
	savecardetail = $.ajax({
		url: '../SYS02/EditStrno/SaveEditCarDetail',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			if(data.status == 'Y'){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				ClearInput();
			}else if(data.status == 'N'){
				Lobibox.notify('error', {
					title: 'ผิดพลาด',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			$('.lobibox-notify').css({'z-index':'99999','border-radius':'50px'});
			savecardetail = null;
		},
		beforeSend: function(){
			if(savecardetail !== null){savecardetail.abort();}
		}
	});
}