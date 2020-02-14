/********************************************************
             ______@28/12/2019______
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
	$('#GCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGCODE',
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
DT_Report = null;
function printReport(){
	var report = "";
	if($('#one').is(":checked")){ 
		report = "one";
	}else if($('#all').is(":checked")){
		report = "all";
	}else if($('#pay').is(":checked")){ 
		report = "pay";
	}
	
	var dt = "";
	if($('#tdt').is(':checked')){
		dt = "tdt";
	}else if($('#pdt').is(":checked")){
		dt = "pdt";
	}
	
	var sort = "";
	if($('#bi1').is(':checked')){
		sort = "bi1";
	}else if($('#d1').is(':checked')){
		sort = "d1";
	}else if($('#cont').is(':checked')){
		sort = "cont";
	}else if($('#bi2').is(':checked')){
		sort = "bi2";
	}else if($('#d2').is(':checked')){
		sort = "d2";
	}else if($('#d3').is(':checked')){
		sort = "d3";
	}else if($('#locat').is(":checked")){
		sort = "locat";
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
	dataToPost.GCODE  	 = (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.CODE  	 = (typeof $('#CODE').find(':selected').val() === 'undefined' ? '':$('#CODE').find(':selected').val());
	dataToPost.report	 = report;
	dataToPost.dt	 	 = dt;
	dataToPost.sort	     = sort;
	if(report == "one"){
		DT_Report = $.ajax({
			url: '../SYS06/ReportReceivedDT/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedDT/pdfone?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				DT_Report = null;
			},
			beforeSend:function(){
				if(DT_Report !== null){
					DT_Report.abort();
				}
			}
		});
	}else if(report == "all"){
		DT_Report = $.ajax({
			url: '../SYS06/ReportReceivedDT/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedDT/pdfall?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				DT_Report = null;
			},
			beforeSend:function(){
				if(DT_Report !== null){
					DT_Report.abort();
				}
			}
		});
	}else if(report == "pay"){
		DT_Report = $.ajax({
			url: '../SYS06/ReportReceivedDT/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedDT/pdfpay?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				DT_Report = null;
			},
			beforeSend:function(){
				if(DT_Report !== null){
					DT_Report.abort();
				}
			}
		});
	}
}