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
			url: '../Cselect2b/getCONTNO_C',
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
	
	$('#CONTSTAT1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPCONT',
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
	
	$('#GCODE1').select2({
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
	
	$('#TYPE1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPECOD',
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
	
	$('#MODEL1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getMODELS',
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
	search();
});

var reportsearch = null;
function search(){
	dataToPost = new Object();
	var orderby = "";
	if($("#sdate").is(":checked")){ 
		orderby = "SDATE";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "CUSCOD";
	}else if($("#type").is(":checked")){ 
		orderby = "TYPE";
	}
	
	var vat = "";
	if($("#showvat").is(":checked")){ 
		vat = "showvat";
	}else if($("#contno").is(":checked")){
		vat = "sumvat";
	}
	var stat = "";
	if($("#NEW").is(":checked")){ 
		stat = "N";
	}else if($("#OLD").is(":checked")){
		stat = "O";
	}else if($("#ALL").is(":checked")){
		stat = "";
	}
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	ataToPost.BILLCOL1 		= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.TYPE1 		= (typeof $('#TYPE1').find(':selected').val() === 'undefined' ? '':$('#TYPE1').find(':selected').val());
	dataToPost.MODEL1 		= (typeof $('#MODEL1').find(':selected').val() === 'undefined' ? '':$('#MODEL1').find(':selected').val());
	dataToPost.CONTSTAT1 	= (typeof $('#CONTSTAT1').find(':selected').val() === 'undefined' ? '':$('#CONTSTAT1').find(':selected').val());
	dataToPost.GCODE1 		= (typeof $('#GCODE1').find(':selected').val() === 'undefined' ? '':$('#GCODE1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	dataToPost.orderby 		= orderby;
	dataToPost.vat 			= vat;
	dataToPost.stat 		= stat;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARfromsaleinstalment/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานลูกหนี้คงเหลือจากการขายผ่อน',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARfromsalecash',1,350);
			//$('.dataTables_info').hide();
			
			$('.data-export').prepend('<img id="print-ARfromsalecash" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARfromsalecash").hover(function() {
				document.getElementById("print-ARfromsalecash").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARfromsalecash").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARfromsalecash-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARfromsalecash-excel").hover(function() {
				document.getElementById("table-ARfromsalecash-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARfromsalecash-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARfromsalecash-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้คงเหลือจากการขายสด "+data.reporttoday); 
			});
			
			$('#print-ARfromsalecash').click(function(){
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
	dataToPost = new Object();
	var layout = "";
	if($("#ver").is(":checked")){ 
		layout = "A4";
	}else if($("#hor").is(":checked")){
		layout = "A4-L";
	}
	
	var orderby = "";
	if($("#sdate").is(":checked")){ 
		orderby = "A.SDATE";
	}else if($("#contno").is(":checked")){
		orderby = "A.CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "A.CUSCOD";
	}
	var vat = "";
	if($("#showvat").is(":checked")){ 
		vat = "showvat";
	}else if($("#contno").is(":checked")){
		vat = "sumvat";
	}
	var stat = "";
	if($("#NEW").is(":checked")){ 
		stat = "N";
	}else if($("#OLD").is(":checked")){
		stat = "O";
	}else if($("#ALL").is(":checked")){
		stat = "";
	}
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.GCODE1 		= (typeof $('#GCODE1').find(':selected').val() === 'undefined' ? '':$('#GCODE1').find(':selected').val());
	dataToPost.TYPE1 		= (typeof $('#TYPE1').find(':selected').val() === 'undefined' ? '':$('#TYPE1').find(':selected').val());
	dataToPost.MODEL1 		= (typeof $('#MODEL1').find(':selected').val() === 'undefined' ? '':$('#MODEL1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	dataToPost.orderby 		= orderby;
	dataToPost.vat 			= vat;
	dataToPost.stat 		= stat;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARfromsaleinstalment/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARfromsaleinstalment/pdf?condpdf='+data[0];
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