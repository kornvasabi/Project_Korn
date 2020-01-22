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
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	search();
});

var reportsearch = null;
function search(){
	dataToPost = new Object();
	var orderby = "";
	if($("#strno").is(":checked")){ 
		orderby = "a.STRNO";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#ydate").is(":checked")){ 
		orderby = "a.YDATE";
	}
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.FROMDATECHG 	= $('#FROMDATECHG').val();
	dataToPost.TODATECHG 	= $('#TODATECHG').val();
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportHoldtoStock/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานรถยึด',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportHoldtoStock',1,260);
			
			$('.data-export').prepend('<img id="print-HoldtoStock" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-HoldtoStock").hover(function() {
				document.getElementById("print-HoldtoStock").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-HoldtoStock").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-HoldtoStock-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-HoldtoStock-excel").hover(function() {
				document.getElementById("table-HoldtoStock-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-HoldtoStock-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-HoldtoStock-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานรถยึดรอไถ่ถอน "+data.reporttoday); 
			});
			
			$('#print-HoldtoStock').click(function(){
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
	if($("#strno").is(":checked")){ 
		orderby = "a.STRNO";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#ydate").is(":checked")){ 
		orderby = "a.YDATE";
	}

	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.FROMDATECHG 	= $('#FROMDATECHG').val();
	dataToPost.TODATECHG 	= $('#TODATECHG').val();
	dataToPost.layout 		= layout;
	dataToPost.orderby 		= orderby;
	
	$.ajax({
		url: '../SYS05/ReportHoldtoStock/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportHoldtoStock/pdf?condpdf='+data[0];
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

