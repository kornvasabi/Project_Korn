/********************************************************
             ______@22/07/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCATNM',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.LOCAT  = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
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
	$('#F_OTPCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOPTCODE',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.LOCAT  = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
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
	$('#T_OTPCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOPTCODE',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.LOCAT  = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
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
});
$('#btnsearch').click(function(){
	fn_Search();
});
var kb_search = null;
function fn_Search(){
	dataToPost = new Object();
	dataToPost.LOCAT     = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
	dataToPost.F_OTPCODE = (typeof $('#F_OTPCODE').find(':selected').val() === 'undefined' ? '' : $('#F_OTPCODE').find(':selected').val());
	dataToPost.T_OTPCODE = (typeof $('#T_OTPCODE').find(':selected').val() === 'undefined' ? '' : $('#T_OTPCODE').find(':selected').val());	
	$('#loadding').fadeIn(200);
	kb_search = $.ajax({
		url: '../SYS02/Accessoryisinstock/Search',
		data: dataToPost,
		type : 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			
			$('#result').html(data.html);
			fn_datatables('table-accessorystock',1,400);
			$('#C_ASC').val(data.C_ASC);
			
			kb_search = null;	
		},
		beforeSend: function(){
			if(kb_search !== null){kb_search.abort();}
		}
	});
}