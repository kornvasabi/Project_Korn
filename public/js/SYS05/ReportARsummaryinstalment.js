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
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportARsummaryinstalment/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานสรุปลูกหนี้เช่าซื้อตามสาขา',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportARsummaryinstalment',1,280);
			
			$('.data-export').prepend('<img id="print-ARsummaryinstalment" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-ARsummaryinstalment").hover(function() {
				document.getElementById("print-ARsummaryinstalment").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-ARsummaryinstalment").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-ARsummaryinstalment-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-ARsummaryinstalment-excel").hover(function() {
				document.getElementById("table-ARsummaryinstalment-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-ARsummaryinstalment-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-ARsummaryinstalment-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานสรุปลูกหนี้เช่าซื้อตามสาขา "+data.reporttoday); 
			});
			
			$('#print-ARsummaryinstalment').click(function(){
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
	
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.BILLCOL1 	= (typeof $('#BILLCOL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOL1').find(':selected').val());
	dataToPost.GCOCE1 		= (typeof $('#GCOCE1').find(':selected').val() === 'undefined' ? '':$('#GCOCE1').find(':selected').val());
	dataToPost.ARDATE 		= $('#ARDATE').val();
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARsummaryinstalment/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARsummaryinstalment/pdf?condpdf='+data[0];
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