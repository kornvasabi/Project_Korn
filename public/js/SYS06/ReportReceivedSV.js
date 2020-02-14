/********************************************************
             ______@09/01/2020______
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
			url: '../Cselect2K/getLOCAT',
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
	$('#LOCATPAY').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
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
	$('#PAYTYP').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getPAYTYP',
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
	$('#PAYFOR').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getPAYFOR',
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
	$('#USERID').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getUSERID',
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
	$('#GROUP1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGROUP1',
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
	$('#CODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
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
$('#btnreport').click(function(){
	printReport();
});
SV_Report = null;
function printReport(){
	var report = "";
	if($('#all').is(":checked")){ 
		report = "all";
	}else if($('#pay').is(":checked")){
		report = "pay";
	}
	var sort = "";
	if($('#set1').is(':checked')){
		sort = "set1";
	}else if($('#set2').is(':checked')){
		sort = "set2";
	}else if($('#set3').is(':checked')){
		sort = "set3";
	}else if($('#set4').is(':checked')){
		sort = "set4";
	}else if($('#set5').is(':checked')){
		sort = "set5";
	}else if($('#set6').is(':checked')){
		sort = "set6";
	}else if($('#set7').is(":checked")){
		sort = "set7";
	}
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.LOCATPAY  = (typeof $('#LOCATPAY').find(':selected').val() === 'undefined' ? '':$('#LOCATPAY').find(':selected').val());	
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.DATE2     = $('#DATE2').val();
	dataToPost.PAYTYP    = (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
	dataToPost.PAYFOR    = (typeof $('#PAYFOR').find(':selected').val() === 'undefined' ? '':$('#PAYFOR').find(':selected').val());
	dataToPost.USERID  	 = (typeof $('#USERID').find(':selected').val() === 'undefined' ? '':$('#USERID').find(':selected').val());
	dataToPost.GROUP1  	 = (typeof $('#GROUP1').find(':selected').val() === 'undefined' ? '':$('#GROUP1').find(':selected').val());
	dataToPost.CODE  	 = (typeof $('#CODE').find(':selected').val() === 'undefined' ? '':$('#CODE').find(':selected').val());
	dataToPost.report	 = report;
	dataToPost.sort	     = sort;
	//alert(report); 
	if(report == "all"){
		SV_Report = $.ajax({
			url: '../SYS06/ReportReceivedSV/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedSV/pdfall?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				SV_Report = null;
			},
			beforeSend:function(){
				if(SV_Report !== null){
					SV_Report.abort();
				}
			}
		});
	}else if(report = "pay"){
		SV_Report = $.ajax({
			url: '../SYS06/ReportReceivedSV/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedSV/pdfpay?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc : false,
					height: $(window).height(),
					width: $(window).width()
				});
				SV_Report = null;
			},
			beforeSend:function(){
				if(SV_Report !== null){
					SV_Report:abort();
				}
			}
		});
	}
}
