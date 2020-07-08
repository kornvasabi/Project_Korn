/********************************************************
             ______@07/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#STRNOOLD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getSTRNO_ST',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.no = "eno";
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
	$('#TSALE').attr('disabled',true);
	$('#CONTNO').attr('disabled',true);
	$('#CUSCOD').attr('disabled',true);
	$('#TAXNO').attr('disabled',true);
	if(_insert == 'T'){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
});
$('#STRNOOLD').change(function(){
	fn_getstrnodetails();
});
var getstrnodetails = null;
function fn_getstrnodetails(){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $('#STRNOOLD').find(':selected').val() === 'undefined' ? '':$('#STRNOOLD').find(':selected').val());
	getstrnodetails = $.ajax({
		url: '../SYS02/EditStrnoNew/getstrnodetails',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#TSALE').val(data.TSALE);
			$('#CONTNO').val(data.CONTNO);
			$('#CUSCOD').val(data.CUSCOD);
			$('#TAXNO').val(data.TAXNO);
			getstrnodetails = null;
		},
		beforSend: function(){
			if(getstrnodetails !== null){getstrnodetails.abort();}
		}
	});
}
$('#btnclear').click(function(){
	ClearInput();
});
function ClearInput(){
	$('#STRNOOLD').empty();
	$('#STRNONEW').val('');
	$('#TSALE').val('');
	$('#CONTNO').val('');
	$('#CUSCOD').val('');
	$('#TAXNO').val('');
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
				fn_savecardetail();
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
function fn_savecardetail(){
	dataToPost = new Object();
	dataToPost.STRNOOLD   = (typeof $('#STRNOOLD').find(':selected').val() === 'undefined' ? '':$('#STRNOOLD').find(':selected').val());
	dataToPost.STRNONEW   = $('#STRNONEW').val();
	dataToPost.TSALE   	  = $('#TSALE').val();
	dataToPost.CONTNO     = $('#CONTNO').val();
	dataToPost.CUSCOD     = $('#CUSCOD').val();
	dataToPost.TAXNO      = $('#TAXNO').val();
	$('#loadding').fadeIn(200);
	savecardetail = $.ajax({
		url: '../SYS02/EditStrnoNew/SaveEditCarDetail',
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