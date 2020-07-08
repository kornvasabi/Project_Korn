/********************************************************
             ______@18/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#OPTCODE_F').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOPTCODE',
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
	$('#OPTCODE_T').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOPTCODE',
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
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCATNM',
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
	if(_update == 'T'){
		$('#btnupdate').attr('disabled',false);
	}else{
		$('#btnupdate').attr('disabled',true);
	}
});
$('#btnupdate').click(function(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการปรับปรุงสินค้าคงเหลืออุปกรณ์เสริม ?',
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
				fn_updateproduct();
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
var updateproduct = null;
function fn_updateproduct(){
	dataToPost = new Object();
	dataToPost.OPTCODE_F = (typeof $('#OPTCODE_F').find(':selected').val() === 'undefined' ? '' : $('#OPTCODE_F').find(':selected').val());
	dataToPost.OPTCODE_T = (typeof $('#OPTCODE_T').find(':selected').val() === 'undefined' ? '' : $('#OPTCODE_T').find(':selected').val());
	dataToPost.LOCAT     = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
	$('#loadding').fadeIn(300);
	updateproduct = $.ajax({
		url: '../SYS02/UpdateProductAccessory/UpdateProduct',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(300);
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
				$('#OPTCODE_F').empty();
				$('#OPTCODE_T').empty();
				$('#LOCAT').empty();
			}else{
				Lobibo.notify('error',{
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon:true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
		}
	});
}