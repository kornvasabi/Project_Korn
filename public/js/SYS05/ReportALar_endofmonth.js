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
	
	$('#BILLCOLL1').select2({
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
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	//alert('5555');
	search();
});

var reportsearch = null;
function search(){
	
	var ystat = "";
	if($("#y_yes").is(":checked")){ 
		ystat = "YES";
	}else if($("#y_no").is(":checked")){
		ystat = "NO";
	}
	
	var orderby = "";
	if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}else if($("#locat").is(":checked")){
		orderby = "LOCAT";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.BILLCOLL1 	= (typeof $('#BILLCOLL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.ystat 		= ystat;
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportALar_endofmonth/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportALbillcollwork',1,310);
			
			$('.data-export').prepend('<img id="print-ALbillcollwork" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ALbillcollwork").hover(function() {
				document.getElementById("print-ALbillcollwork").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ALbillcollwork").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ALbillcollwork-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ALbillcollwork-excel").hover(function() {
				document.getElementById("table-ALbillcollwork-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ALbillcollwork-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ALbillcollwork-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานวิเคราะห์สภาพลูกหนี้ ณ สิ้นเดือน "+data.reporttoday); 
			});
			
			$('#print-ALbillcollwork').click(function(){
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
	
	var ystat = "";
	if($("#y_yes").is(":checked")){ 
		ystat = "YES";
	}else if($("#y_no").is(":checked")){
		ystat = "NO";
	}
	
	var orderby = "";
	if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}else if($("#locat").is(":checked")){
		orderby = "LOCAT";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.BILLCOLL1 	= (typeof $('#BILLCOLL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.ystat 		= ystat;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportALar_endofmonth/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportALar_endofmonth/pdf?condpdf='+data[0];
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