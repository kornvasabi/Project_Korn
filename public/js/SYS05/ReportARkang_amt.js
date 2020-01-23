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
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	//alert('5555');
	search();
});

var reportsearch = null;
function search(){
	
	var report = "";
	if($("#ar0").is(":checked")){ 
		report = "ar0";
	}else if($("#armore0").is(":checked")){
		report = "armore0";
	}else if($("#arall").is(":checked")){ 
		report = "arall";
	}

	var orderby = "";
	if($("#locat").is(":checked")){ 
		orderby = "LOCAT";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "CUSCOD";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTSTAT1 	= (typeof $('#CONTSTAT1').find(':selected').val() === 'undefined' ? '':$('#CONTSTAT1').find(':selected').val());
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	
	if(report == "armore0" || report == "arall"){	
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 15000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
			soundExt: '.ogg',
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาเลือกสาขา'
		});
	}else{
		$('#loadding').show();
		reportsearch = $.ajax({
			url: '../SYS05/ReportARkang_amt/search',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				$('#loadding').hide();	
				Lobibox.window({
					title: 'รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ',
					content: data.html,
					height: $(window).height(),
					width: $(window).width(),
					closeOnEsc: false,
					draggable: false
				});
				
				fn_datatables('table-ReportARkang_amt',1,280);
				
				$('.data-export').prepend('<img id="print-ARkang_amt" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#print-ARkang_amt").hover(function() {
					document.getElementById("print-ARkang_amt").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("print-ARkang_amt").style.filter = "contrast(100%)";
				});
				
				$('.data-export').prepend('<img id="table-ARkang_amt-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#table-ARkang_amt-excel").hover(function() {
					document.getElementById("table-ARkang_amt-excel").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("table-ARkang_amt-excel").style.filter = "contrast(100%)";
				});
				
				$("#table-ARkang_amt-excel").click(function(){ 
					tableToExcel_Export(data.report,"sheet 1","รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ "+data.reporttoday); 
				});
				
				$('#print-ARkang_amt').click(function(){
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
}

function printReport(){
	var layout = "";
	if($("#ver").is(":checked")){ 
		layout = "A4";
	}else if($("#hor").is(":checked")){
		layout = "A4-L";
	}
	
	var report = "";
	if($("#ar0").is(":checked")){ 
		report = "ar0";
	}else if($("#armore0").is(":checked")){
		report = "armore0";
	}else if($("#arall").is(":checked")){ 
		report = "arall";
	}

	var orderby = "";
	if($("#locat").is(":checked")){ 
		orderby = "LOCAT";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#cuscod").is(":checked")){ 
		orderby = "CUSCOD";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTSTAT1 	= (typeof $('#CONTSTAT1').find(':selected').val() === 'undefined' ? '':$('#CONTSTAT1').find(':selected').val());
	dataToPost.report 		= report;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportARkang_amt/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportARkang_amt/pdf?condpdf='+data[0];
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