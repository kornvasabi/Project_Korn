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
	
	$('#TSALE1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPESALE',
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

$('#btnt1search').click(function(){
	search();
});

var reportsearch = null;
function search(){
	
	var order = "";
	if($("#taxdt").is(":checked")){ 
		order = "TAXDT, TAXNO";
	}else if($("#contno").is(":checked")){
		order = "CONTNO";
	}else if($("#taxno").is(":checked")){
		order = "TAXNO";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.TSALE1 		= (typeof $('#TSALE1').find(':selected').val() === 'undefined' ? '':$('#TSALE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.order 		= order;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS07/ReportOutputtax_more/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานภาษีขาย(ยื่นเพิ่มเติม)',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			fn_datatables('table-ReportOutputtax_more',1,360);
			
			$('.data-export').prepend('<img id="print-Outputtax_more" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-Outputtax_more").hover(function() {
				document.getElementById("print-Outputtax_more").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-Outputtax_more").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-Outputtax_more-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-Outputtax_more-excel").hover(function() {
				document.getElementById("table-Outputtax_more-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-Outputtax_more-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-Outputtax_more-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานภาษีขาย(ยื่นเพิ่มเติม)  "+data.reporttoday); 
			});
			
			$('#print-Outputtax_more').click(function(){
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
	
	var order = "";
	if($("#taxdt").is(":checked")){ 
		order = "TAXDT";
	}else if($("#contno").is(":checked")){
		order = "CONTNO";
	}else if($("#taxno").is(":checked")){
		order = "TAXNO";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.TSALE1 		= (typeof $('#TSALE1').find(':selected').val() === 'undefined' ? '':$('#TSALE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.order 		= order;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS07/ReportOutputtax_more/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/ReportOutputtax_more/pdf?condpdf='+data[0];
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