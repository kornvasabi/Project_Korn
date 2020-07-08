/********************************************************
             ______@16/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#TABLENM').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTableclearnull',
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
	//$('#selectTB').hide();
});
$('#TABLENM').change(function(){
	dataToPost = new Object();
	dataToPost.TABLENM = (typeof $('#TABLENM').find(':selected').val() === 'undefined' ? '' : $('#TABLENM').find(':selected').val());
	$.ajax({
		url: '../SYS02/ClearNullFromUpgrade/CountFields',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//alert(data.count);
			$('#COUNTFIEDLS').val(data.count);
		}
	});
});
$('#btnclear').click(function(){
	fn_Clearnull();
});
var clearnull = null;
function fn_Clearnull(){
	dataToPost = new Object();
	dataToPost.TABLENM = (typeof $('#TABLENM').find(':selected').val() === 'undefined' ? '' : $('#TABLENM').find(':selected').val());
	$('#loadding').fadeIn(200);
	clearnull = $.ajax({
		url: '../SYS02/ClearNullFromUpgrade/ClearNullTable',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
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
			}else{
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
		}
	});
}