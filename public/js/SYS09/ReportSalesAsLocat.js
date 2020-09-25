/********************************************************
             ______@29/07/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#CRLOCAT').select2({
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
	$("#STAT").select2({
        placeholder: 'เลือก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#STAT").parent().parent(),
        width: '100%'
    });
	$('#BAAB').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getBAABCOD',
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
	$('#GCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGCODE',
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
	$('#TYPE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTYPECOD',
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
	$('#MODEL').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getMODELCOD',
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
	$('#SALCOD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
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
	$('#COLOR').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCOLORCOD',
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
$('#btnreport').click(function(){
	printReport();
});
var SAL_Report = null 
function printReport(){
	var styleReport = "";
	if($('#R1').is(":checked")){
		styleReport = "R1";
	}else if($('#R2').is(":checked")){
		styleReport = "R2";
	}else if($('#R3').is(":checked")){
		styleReport = "R3";
	}
	dataToPost = new Object();
	dataToPost.CRLOCAT 		= (typeof $('#CRLOCAT').find(':selected').val() === 'undefined' ? '':$('#CRLOCAT').find(':selected').val());
	dataToPost.SDATE_F      = $('#SDATE_F').val();
	dataToPost.SDATE_T      = $('#SDATE_T').val();
	dataToPost.STAT 		= (typeof $('#STAT').find(':selected').val() === 'undefined' ? '':$('#STAT').find(':selected').val());
	dataToPost.BAAB 		= (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
	dataToPost.GCODE 		= (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.TYPE 		= (typeof $('#TYPE').find(':selected').val() === 'undefined' ? '':$('#TYPE').find(':selected').val());
	dataToPost.MODEL 		= (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
	dataToPost.SALCOD 		= (typeof $('#SALCOD').find(':selected').val() === 'undefined' ? '':$('#SALCOD').find(':selected').val());
	dataToPost.COLOR 		= (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '':$('#COLOR').find(':selected').val());
	dataToPost.SReport      = styleReport;
	SAL_Report = $.ajax({
		url: '../SYS09/ReportSalesAsLocat/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS09/ReportSalesAsLocat/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
			SAL_Report = null;
		},
		beforeSend: function(){
			if(SAL_Report !== null){
				SAL_Report.abort();
			}
		}
	});
}