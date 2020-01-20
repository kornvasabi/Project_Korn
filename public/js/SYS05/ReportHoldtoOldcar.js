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
			url: '../Cselect2b/getCONTNO_ARHOLD',
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
	
	$('#Y_USER1').select2({
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
	search();
});

var reportsearch = null;
function search(){
	dataToPost = new Object();
	var calcul = "";
	if($("#SYD").is(":checked")){ 
		calcul = "SYD";
	}else if($("#STR").is(":checked")){
		calcul = "STR";
	}
	
	var conddate = "";
	if($("#c_sdate").is(":checked")){ 
		conddate = "SDATE";
	}else if($("#c_ydate").is(":checked")){
		conddate = "YDATE";
	}
	
	var orderby = "";
	if($("#strno").is(":checked")){ 
		orderby = "STRNO";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#ydate").is(":checked")){ 
		orderby = "YDATE";
	}else if($("#sdate").is(":checked")){ 
		orderby = "SDATE";
	}
	
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.FROMDATEHOLD = $('#FROMDATEHOLD').val();
	dataToPost.TODATEHOLD 	= $('#TODATEHOLD').val();
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.Y_USER1 		= (typeof $('#Y_USER1').find(':selected').val() === 'undefined' ? '':$('#Y_USER1').find(':selected').val());
	dataToPost.STRNO1 		= $('#STRNO1').val();
	dataToPost.calcul 		= calcul;
	dataToPost.conddate 	= conddate;
	dataToPost.orderby 		= orderby;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS05/ReportHoldtoOldcar/search',
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
			
			fn_datatables('table-ReportHoldtoOldcar',1,260);
			
			$('.data-export').prepend('<img id="print-HoldtoOldcar" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-HoldtoOldcar").hover(function() {
				document.getElementById("print-HoldtoOldcar").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-HoldtoOldcar").style.filter = "contrast(100%)";
			});
			
			$('.data-export').prepend('<img id="table-HoldtoOldcar-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-HoldtoOldcar-excel").hover(function() {
				document.getElementById("table-HoldtoOldcar-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-HoldtoOldcar-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-HoldtoOldcar-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานรถยึดเปลี่ยนเป็นรถเก่า "+data.reporttoday); 
			});
			
			$('#print-HoldtoOldcar').click(function(){
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
	
	var calcul = "";
	if($("#SYD").is(":checked")){ 
		calcul = "SYD";
	}else if($("#STR").is(":checked")){
		calcul = "STR";
	}
	
	var conddate = "";
	if($("#c_sdate").is(":checked")){ 
		conddate = "SDATE";
	}else if($("#c_ydate").is(":checked")){
		conddate = "YDATE";
	}
	
	var orderby = "";
	if($("#strno").is(":checked")){ 
		orderby = "STRNO";
	}else if($("#contno").is(":checked")){
		orderby = "a.CONTNO";
	}else if($("#ydate").is(":checked")){ 
		orderby = "YDATE";
	}else if($("#sdate").is(":checked")){ 
		orderby = "SDATE";
	}
	
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.FROMDATEHOLD = $('#FROMDATEHOLD').val();
	dataToPost.TODATEHOLD 	= $('#TODATEHOLD').val();
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.Y_USER1 		= (typeof $('#Y_USER1').find(':selected').val() === 'undefined' ? '':$('#Y_USER1').find(':selected').val());
	dataToPost.STRNO1 		= $('#STRNO1').val();
	dataToPost.calcul 		= calcul;
	dataToPost.conddate 	= conddate;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportHoldtoOldcar/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportHoldtoOldcar/pdf?condpdf='+data[0];
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