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
	
	var taxtype = "";
	if($("#normal").is(":checked")){ 
		taxtype = "normal";
	}else if($("#more").is(":checked")){
		taxtype = "more";
	}
	
	var report = "";
	if($("#inputtax").is(":checked")){ 
		report = "inputtax";
	}else if($("#creditttax").is(":checked")){
		report = "creditttax";
	}else if($("#taxothr").is(":checked")){
		report = "taxothr";
	}
	
	var orderby = "";
	if($("#taxdt").is(":checked")){ 
		orderby = "TAXDT";
	}else if($("#apcode").is(":checked")){
		orderby = "APCODE";
	}else if($("#taxno").is(":checked")){
		orderby = "TAXNO";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.APCODE1 		= (typeof $('#APCODE1').find(':selected').val() === 'undefined' ? '':$('#APCODE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.taxtype 		= taxtype;
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS07/ReportInputtax/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานภาษีซื้อ',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			$('.lobibox-body').empty().append(data.html);
			
			document.getElementById("table-fixed-ReportInputtax").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
	
			$('#H_ReportInputtax').prepend('<img id="print-Inputtax" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-Inputtax").hover(function(){
				document.getElementById("print-Inputtax").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-Inputtax").style.filter = "contrast(100%)";
			});
			
			$('#H_ReportInputtax').prepend('<img id="table-Inputtax-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-Inputtax-excel").hover(function() {
				document.getElementById("table-Inputtax-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-Inputtax-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-Inputtax-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานภาษีซื้อ "+data.reporttoday); 
			});
			
			$('#print-Inputtax').click(function(){
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
	
	var taxtype = "";
	if($("#normal").is(":checked")){ 
		taxtype = "normal";
	}else if($("#more").is(":checked")){
		taxtype = "more";
	}
	
	var report = "";
	if($("#inputtax").is(":checked")){ 
		report = "inputtax";
	}else if($("#creditttax").is(":checked")){
		report = "creditttax";
	}else if($("#taxothr").is(":checked")){
		report = "taxothr";
	}
	
	var orderby = "";
	if($("#taxdt").is(":checked")){ 
		orderby = "TAXDT";
	}else if($("#apcode").is(":checked")){
		orderby = "APCODE";
	}else if($("#taxno").is(":checked")){
		orderby = "TAXNO";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.APCODE1 		= (typeof $('#APCODE1').find(':selected').val() === 'undefined' ? '':$('#APCODE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.taxtype 		= taxtype;
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS07/ReportInputtax/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/ReportInputtax/pdf?condpdf='+data[0];
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