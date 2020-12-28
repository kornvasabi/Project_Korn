/********************************************************
             ______@27/10/2020______
            pasakorn boonded
********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
$(function(){
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
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
		width: '100%'
	});
	$('#add_contno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getCONTNO_RP',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
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
		width: '100%'
	});
});
$('#btn_upstr').click(function(){
	fnUpdate('str');
});
$('#btn_upsyd').click(function(){
	fnUpdate('syd');
});
function fnUpdate(params){
	dataToPost = new Object();
	dataToPost.params = params;
	dataToPost.locat  = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.contno = (typeof $('#add_contno').find(':selected').val() === 'undefined' ? '':$('#add_contno').find(':selected').val());
	dataToPost.fdate  = $('#fdate').val();
	dataToPost.tdate  = $('#tdate').val();
	dataToPost.condup = $("#condup input[type='radio']:checked").val();
	$('#loadding').fadeIn(200);	
	$.ajax({
		url: '../SYS06/UpdateProfitNopay/Update',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				
				$('#add_locat').empty();
				$('#add_locatnm').val("");
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
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
		},error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}








