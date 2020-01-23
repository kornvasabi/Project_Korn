/********************************************************
             ______@17/01/2020______
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
});
$('#btnreportAD').click(function(){
	printReport();
});
function printReport(){
	var report = "";
	if($('#R1').is(":checked")){ 
		report = "R1";
	}else if($('#R2').is(":checked")){
		report = "R2";
	}
	var dat = "";
	if($('#D1').is(":checked")){
		dat = "D1";
	}else if($('#D2')){
		dat = "D2";
	}
	var order = "";
	if($('#OR1').is(':checked')){
		order = "TMBILL";
	}else if($('#OR2').is(':checked')){
		order = "TMBILDT";
	}else if($('#OR3').is(':checked')){
		order = "PAYINDT";
	}else if($('#OR4').is(':checked')){
		order = "BILLNO";
	}else if($('#OR5').is(':checked')){
		order = "CHQDT";
	}else if($('#OR6').is(':checked')){
		order = "LOCATRECV";
	}
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.LOCATPAY  = (typeof $('#LOCATPAY').find(':selected').val() === 'undefined' ? '':$('#LOCATPAY').find(':selected').val());	
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.PAYTYP    = (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
	dataToPost.PAYFOR    = (typeof $('#PAYFOR').find(':selected').val() === 'undefined' ? '':$('#PAYFOR').find(':selected').val());
	dataToPost.USERID  	 = (typeof $('#USERID').find(':selected').val() === 'undefined' ? '':$('#USERID').find(':selected').val());
	dataToPost.report	 = report;
	dataToPost.dat	     = dat;
	dataToPost.order	 = order;
	if(report == "R1"){
		$.ajax({
			url: '../SYS06/ReportReceivedAD/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedAD/pdflistall?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
			}
		});
	}else if(report = "R2"){
		$.ajax({
			url: '../SYS06/ReportReceivedAD/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedAD/pdfpay?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc : false,
					height: $(window).height(),
					width: $(window).width()
				});
			}
		});
	}
}