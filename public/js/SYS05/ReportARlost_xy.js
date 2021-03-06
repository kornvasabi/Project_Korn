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
	
	$('#CHECKER1').select2({
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
	
	$('#GCOCE1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getGCode',
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
	
	$('#ACTICOD1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getACTI',
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
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	//alert('5555');
	search();
});

var reportsearch = null;
function search(){
	
	var orderby = "";
	if($("#contno").is(":checked")){ 
		orderby = "CONTNO";
	}else if($("#cuscod").is(":checked")){
		orderby = "CUSCOD";
	}else if($("#lpayd").is(":checked")){ 
		orderby = "LPAYD";
	}
	
	var ystat = "";
	if($("#y_yes").is(":checked")){ 
		ystat = "YES";
	}else if($("#y_no").is(":checked")){
		ystat = "NO";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.CHECKER1 	= (typeof $('#CHECKER1').find(':selected').val() === 'undefined' ? '':$('#CHECKER1').find(':selected').val());
	dataToPost.AMPHUR1 		= (typeof $('#AMPHUR1').find(':selected').val() === 'undefined' ? '':$('#AMPHUR1').find(':selected').val());
	dataToPost.PROVINCE1 	= (typeof $('#PROVINCE1').find(':selected').val() === 'undefined' ? '':$('#PROVINCE1').find(':selected').val());
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.ACTICOD1 	= (typeof $('#ACTICOD1').find(':selected').val() === 'undefined' ? '':$('#ACTICOD1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.TUMBON1 		= $('#TUMBON1').val();
	dataToPost.LATEFRM1 	= $('#LATEFRM1').val();
	dataToPost.LATETO1 		= $('#LATETO1').val();
	dataToPost.orderby 		= orderby;
	dataToPost.ystat 		= ystat;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARlost_xy/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานลูกหนี้ขาดการติดต่อ x วัน - y วัน',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARlost_xy',1,280);
			
			$('.data-export').prepend('<img id="print-ARlost_xy" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARlost_xy").hover(function() {
				document.getElementById("print-ARlost_xy").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARlost_xy").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARlost_xy-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARlost_xy-excel").hover(function() {
				document.getElementById("table-ARlost_xy-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARlost_xy-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARlost_xy-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้ขาดการติดต่อ x วัน - y วัน "+data.reporttoday); 
			});
			
			$('#print-ARlost_xy').click(function(){
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
	if($("#contno").is(":checked")){ 
		orderby = "CONTNO";
	}else if($("#cuscod").is(":checked")){
		orderby = "CUSCOD";
	}else if($("#lpayd").is(":checked")){ 
		orderby = "LPAYD";
	}
	
	var ystat = "";
	if($("#y_yes").is(":checked")){ 
		ystat = "YES";
	}else if($("#y_no").is(":checked")){
		ystat = "NO";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.CHECKER1 	= (typeof $('#CHECKER1').find(':selected').val() === 'undefined' ? '':$('#CHECKER1').find(':selected').val());
	dataToPost.AMPHUR1 		= (typeof $('#AMPHUR1').find(':selected').val() === 'undefined' ? '':$('#AMPHUR1').find(':selected').val());
	dataToPost.PROVINCE1 	= (typeof $('#PROVINCE1').find(':selected').val() === 'undefined' ? '':$('#PROVINCE1').find(':selected').val());
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.ACTICOD1 	= (typeof $('#ACTICOD1').find(':selected').val() === 'undefined' ? '':$('#ACTICOD1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.TUMBON1 		= $('#TUMBON1').val();
	dataToPost.LATEFRM1 	= $('#LATEFRM1').val();
	dataToPost.LATETO1 		= $('#LATETO1').val();
	dataToPost.orderby 		= orderby;
	dataToPost.ystat 		= ystat;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARlost_xy/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARlost_xy/pdf?condpdf='+data[0];
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