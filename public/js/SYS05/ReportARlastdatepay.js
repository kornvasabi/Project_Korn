//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');
//หน้าแรก
$(function(){
	$('#LOCAT1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#CONTNO1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_H',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#BILLCOL1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getOFFICER',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#AMPHUR1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getAUMPHUR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#PROVINCE').find(':selected').val();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	BEECONTNOSCHANGE = null
	$('#AMPHUR1').change(function(){ 
		var aumphur = (typeof $('#AMPHUR1').find(":selected").val() === 'undefined' ? '' : $('#AMPHUR1').find(":selected").val());
		dataToPost = new Object();
		dataToPost.aumphur = aumphur;
		BEECONTNOSCHANGE = $.ajax({
			url : "../Cselect2b/getPROVINCEbyAUMPHUR",
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				var newOption = new Option(data.PROVDES, data.PROVCOD, true, true);
				$('#PROVINCE1').empty().append(newOption).trigger('change.select2');			
				BEECONTNOSCHANGE = null;
			},
			beforeSend: function(){
				if(BEECONTNOSCHANGE !== null){
					BEECONTNOSCHANGE.abort();
				}
			}
		});
		$('.AUMP').not(this).val($(this).val());
	});
	
	$('#PROVINCE1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getPROVINCE',
			data: function (params) {
				dataToPost = new Object();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	$('#PROVINCE1').change(function(){ 
		$('#AMPHUR1').empty().trigger('change.select2');
		$('.AUMP').not(this).val($(this).val());
	});
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	//alert('5555');
	search();
});

var reportsearch = null;
function search(){
	
	var orderby = "";
	if($("#ldate").is(":checked")){ 
		orderby = "LDATE";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}
	
	var report = "";
	if($("#armore0").is(":checked")){ 
		report = "armore0";
	}else if($("#arall").is(":checked")){
		report = "arall";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.AMPHUR1 		= (typeof $('#AMPHUR1').find(':selected').val() === 'undefined' ? '':$('#AMPHUR1').find(':selected').val());
	dataToPost.PROVINCE1 	= (typeof $('#PROVINCE1').find(':selected').val() === 'undefined' ? '':$('#PROVINCE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.TUMBON1 		= $('#TUMBON1').val();
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARlastdatepay/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานลูกหนี้ครบกำหนดสัญญา',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARlastdatepay',1,290);
			
			$('.data-export').prepend('<img id="print-ARlastdatepay" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARlastdatepay").hover(function() {
				document.getElementById("print-ARlastdatepay").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARlastdatepay").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARlastdatepay-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARlastdatepay-excel").hover(function() {
				document.getElementById("table-ARlastdatepay-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARlastdatepay-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARlastdatepay-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้ครบกำหนดสัญญา "+data.reporttoday); 
			});
			
			$('#print-ARlastdatepay').click(function(){
				printReport();
			});

			reportsearch = null;
		},
		beforeSend: function(){
			if(reportsearch !== null){
				reportsearch.abort();
			}
		}
	});
	
}

function printReport(){
	var layout = "";
	if($("#ver").is(":checked")){ 
		layout = "A4";
	}else if($("#hor").is(":checked")){
		layout = "A4-L";
	}
	
	var orderby = "";
	if($("#ldate").is(":checked")){ 
		orderby = "LDATE";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}
	
	var report = "";
	if($("#armore0").is(":checked")){ 
		report = "armore0";
	}else if($("#arall").is(":checked")){
		report = "arall";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.AMPHUR1 		= (typeof $('#AMPHUR1').find(':selected').val() === 'undefined' ? '':$('#AMPHUR1').find(':selected').val());
	dataToPost.PROVINCE1 	= (typeof $('#PROVINCE1').find(':selected').val() === 'undefined' ? '':$('#PROVINCE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.TUMBON1 		= $('#TUMBON1').val();
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARlastdatepay/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARlastdatepay/pdf?condpdf='+data[0];
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
}