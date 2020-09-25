/********************************************************
             ______@30/07/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#LOCATRECV').select2({
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
	$('#LOCATPAY').select2({
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
	$('#PAYTYP').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getPAYTYP',
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
	$('#CODE').select2({
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
});
$('#btnreport').click(function(){
	printReport();
});
var SAL_Report = null 
function printReport(){
	dataToPost = new Object();
	dataToPost.LOCATRECV 	= (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.TMBILDT_F    = $('#TMBILDT_F').val();
	dataToPost.TMBILDT_T    = $('#TMBILDT_T').val();
	dataToPost.LOCATPAY 	= (typeof $('#LOCATPAY').find(':selected').val() === 'undefined' ? '':$('#LOCATPAY').find(':selected').val());
	dataToPost.PAYTYP 		= (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
	dataToPost.CODE 		= (typeof $('#CODE').find(':selected').val() === 'undefined' ? '':$('#CODE').find(':selected').val());
	SAL_Report = $.ajax({
		url: '../SYS09/ReportMoneyReceipt/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS09/ReportMoneyReceipt/pdf?condpdf='+data[0];
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