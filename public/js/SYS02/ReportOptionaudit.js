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
	
	$('#APCODE1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getAPMAST',
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
	if($("#rcvdate").is(":checked")){
		orderby = " RECVDT";
	}else if($("#invno").is(":checked")){
		orderby = " INVNO";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.APCODE1 		= (typeof $('#APCODE1').find(':selected').val() === 'undefined' ? '':$('#APCODE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS02/ReportOptionaudit/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานตรวจสอบการรับอุปกรณ์',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportOptionaudit',1,290);
			
			$('.data-export').prepend('<img id="print-Optionaudit" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-Optionaudit").hover(function(){
				document.getElementById("print-Optionaudit").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-Optionaudit").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-Optionaudit-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-Optionaudit-excel").hover(function() {
				document.getElementById("table-Optionaudit-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-Optionaudit-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-Optionaudit-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานตรวจสอบการรับอุปกรณ์ "+data.reporttoday); 
			});
			
			$('#print-Optionaudit').click(function(){
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
	if($("#rcvdate").is(":checked")){
		orderby = " RECVDT";
	}else if($("#invno").is(":checked")){
		orderby = " INVNO";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.APCODE1 		= (typeof $('#APCODE1').find(':selected').val() === 'undefined' ? '':$('#APCODE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS02/ReportOptionaudit/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS02/ReportOptionaudit/pdf?condpdf='+data[0];
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