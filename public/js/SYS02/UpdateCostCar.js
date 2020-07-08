/********************************************************
             ______@08/04/2020______
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
	$('#CONTNO').attr('disabled',true);
	$('#TSALE').attr('disabled',true);
	$('#NETCOST').attr('disabled',true);
	$('#CRVAT').attr('disabled',true);
	$('#TOTCOST').attr('disabled',true);
	$('#VATRT').attr('disabled',true);

	$('#btnsave').attr('disabled',true);
	$('#btnedit').attr('disabled',true);
});
$('#btnclear').click(function(){
	ClearInput();
});
function ClearInput(){
	$('#CONTNO').attr('disabled',true);
	$('#TSALE').attr('disabled',true);
	$('#NETCOST').attr('disabled',true);
	$('#CRVAT').attr('disabled',true);
	$('#TOTCOST').attr('disabled',true);
	$('#VATRT').attr('disabled',true);

	$('#STRNO').empty();
	$('#CONTNO').val('');
	$('#TSALE').val('');
	$('#NETCOST').val('');
	$('#CRVAT').val('');
	$('#TOTCOST').val('');
	$('#VATRT').val('');
	$('#btnedit').attr('disabled',true);
	$('#btnsave').attr('disabled',true);
}
$('#STRNO').change(function(){
	fn_getcostcar();
});
var costcar = null;
function fn_getcostcar(){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	costcar = $.ajax({
		url: '../SYS02/UpdateCostCar/getCostCar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
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
				$('#STRNO').empty();
			}else{
				$('#CONTNO').val(data.CONTNO);
				$('#TSALE').val(data.TSALE);
				$('#NETCOST').val(data.NETCOST);
				$('#CRVAT').val(data.CRVAT);
				$('#TOTCOST').val(data.TOTCOST);
				$('#VATRT').val(data.VATRT);

				$('#btnedit').attr('disabled',false);
			}
			costcar = null;
		},
		beforeSend: function(){
			if(costcar !== null){costcar.abort();}
		}
	});
}
$('#btnedit').click(function(){
	$('#NETCOST').attr('disabled',false);
	$('#CRVAT').attr('disabled',false);
	$('#TOTCOST').attr('disabled',false);
	$('#VATRT').attr('disabled',false);
	
	if(_update == 'T'){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
	//$('#btnsave').attr('disabled',false);
});

$('#CRVAT').click(function(){
	getcalculation();
});
$('#TOTCOST').click(function(){
	getcalculation();
});
function getcalculation(){
	dataToPost = new Object();
	dataToPost.NETCOST = $('#NETCOST').val();
	dataToPost.VATRT   = $('#VATRT').val();
	$.ajax({
		url: '../SYS02/UpdateCostCar/getcalculation',
		data: dataToPost,
		tpye: 'POST',
		dataType: 'json',
		success: function(data){
			$('#CRVAT').val(data.CRVAT);
			$('#TOTCOST').val(data.TOTCOST);
		}
	});
}
$('#btnsave').click(function(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกรายการปรับปรุงราคาต้นทุนรถ ?',
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
var savecostcar = null;
function fn_save(){
	dataToPost = new Object();
	dataToPost.STRNO   = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.NETCOST = $('#NETCOST').val();
	dataToPost.CRVAT   = $('#CRVAT').val();
	dataToPost.VATRT   = $('#VATRT').val();
	dataToPost.TOTCOST = $('#TOTCOST').val();
	savecostcar = $.ajax({
		url: '../SYS02/UpdateCostCar/Savecostcar',
		data: dataToPost,
		tpyp: 'POST',
		dataType: 'json',
		success: function(data){
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
			savecostcar = null;
		},
		beforeSend: function(){
			if(savecostcar !== null){savecostcar.abort();}
		}
	});
}

