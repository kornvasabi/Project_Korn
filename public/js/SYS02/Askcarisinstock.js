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
	$('#RVLOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCATNM',
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
});
$('#btnsearch').click(function(){
	fn_Search();
});
var kb_search = null;
function fn_Search(){
	dataToPost = new Object();
	dataToPost.TDATE     = $('#TDATE').val();
	dataToPost.TYPE      = (typeof $('#TYPE').find(':selected').val() === 'undefined' ? '' : $('#TYPE').find(':selected').val());
	dataToPost.MODEL     = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '' : $('#MODEL').find(':selected').val());
	dataToPost.BAAB      = (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '' : $('#BAAB').find(':selected').val());
	dataToPost.COLOR  	 = (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '' : $('#COLOR').find(':selected').val());
	dataToPost.RVLOCAT   = (typeof $('#RVLOCAT').find(':selected').val() === 'undefined' ? '' : $('#RVLOCAT').find(':selected').val());
	dataToPost.STAT		 = $('#STAT').val();
	$('#loadding').fadeIn(200);
	kb_search = $.ajax({
		url: '../SYS02/Askcarisinstock/Search',
		data: dataToPost,
		type : 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			$('#result').html(data.html);
			fn_datatables('table-carisstock',1,400);
			$('#C_CAR').val(data.C_CAR);
			
			kb_search = null;	
		},
		beforeSend: function(){
			if(kb_search !== null){kb_search.abort();}
		}
	});
} 