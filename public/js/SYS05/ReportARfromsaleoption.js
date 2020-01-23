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
			url: '../Cselect2b/getCONTNO_OP',
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
	
	$('#CUSCOD1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCUSTOMERS',
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
	dataToPost = new Object();
	
	var orderby = "";
	if($("#sdate").is(":checked")){ 
		orderby = "SDATE";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "CUSCOD";
	}
	
	var vat = "";
	var $height = 0;
	if($("#showvat").is(":checked")){ 
		vat = "showvat";
		$height = 290;
	}else if($("#sumvat").is(":checked")){
		vat = "sumvat";
		$height = 280;
	}
	
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CUSCOD1 		= (typeof $('#CUSCOD1').find(':selected').val() === 'undefined' ? '':$('#CUSCOD1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	dataToPost.orderby 		= orderby;
	dataToPost.vat 			= vat;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARfromsaleoption/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานลูกหนี้คงเหลือจากการขายอุปกรณ์',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARfromsaleoption',1,$height);
			
			$('.data-export').prepend('<img id="print-ARfromsaleoption" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARfromsaleoption").hover(function() {
				document.getElementById("print-ARfromsaleoption").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARfromsaleoption").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARfromsaleoption-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARfromsaleoption-excel").hover(function() {
				document.getElementById("table-ARfromsaleoption-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARfromsaleoption-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARfromsaleoption-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้คงเหลือจากการขายอุปกรณ์ "+data.reporttoday); 
			});
			
			$('#print-ARfromsaleoption').click(function(){
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
		orderby = "SDATE";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "CUSCOD";
	}
	
	var vat = "";
	var $height = 0;
	if($("#showvat").is(":checked")){ 
		vat = "showvat";
		$height = 350;
	}else if($("#sumvat").is(":checked")){
		vat = "sumvat";
		$height = 330;
	}
	
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CUSCOD1 		= (typeof $('#CUSCOD1').find(':selected').val() === 'undefined' ? '':$('#CUSCOD1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	dataToPost.orderby 		= orderby;
	dataToPost.vat 			= vat;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARfromsaleoption/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARfromsaleoption/pdf?condpdf='+data[0];
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