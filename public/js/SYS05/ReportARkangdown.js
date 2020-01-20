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
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.orderby 		= orderby;
	dataToPost.ystat 		= ystat;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARkangdown/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานลูกหนี้ค้างชำระเงินดาวน์',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARkangdown',1,280);
			
			$('.data-export').prepend('<img id="print-ARkangdown" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARkangdown").hover(function() {
				document.getElementById("print-ARkangdown").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARkangdown").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARkangdown-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARkangdown-excel").hover(function() {
				document.getElementById("table-ARkangdown-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARkangdown-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARkangdown-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้ค้างชำระเงินดาวน์ "+data.reporttoday); 
			});
			
			$('#print-ARkangdown').click(function(){
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
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.orderby 		= orderby;
	dataToPost.ystat 		= ystat;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARkangdown/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARkangdown/pdf?condpdf='+data[0];
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