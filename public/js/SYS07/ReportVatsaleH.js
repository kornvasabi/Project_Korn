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
	
	$('#CONTNO2').select2({
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
});

$('#btnt1search').click(function(){
	search();
});

var reportsearch = null;
function search(){
	
	var order = "";
	if($("#contno").is(":checked")){ 
		order = "CONTNO";
	}else if($("#locat").is(":checked")){
		order = "LOCAT";
	}else if($("#sdate").is(":checked")){
		order = "SDATE";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTNO2 		= (typeof $('#CONTNO2').find(':selected').val() === 'undefined' ? '':$('#CONTNO2').find(':selected').val());
	dataToPost.VATMONTH 	= $('#VATMONTH').val();
	dataToPost.VATYEAR 		= $('#VATYEAR').val();
	dataToPost.order 		= order;
	
	if(dataToPost.LOCAT1 == ""){	
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
			msg: 'กรุณาระบุสาขา'
		});
	}else{
		$('#loadding').show();
		reportsearch = $.ajax({
			url: '../SYS07/ReportVatsaleH/search',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				$('#loadding').hide();	
				Lobibox.window({
					title: 'รายงานภาษีคงเหลือจากการขายผ่อน',
					content: data.html,
					height: $(window).height(),
					width: $(window).width(),
					closeOnEsc: false,
					draggable: false
				});
				
				fn_datatables('table-ReportVatsaleH',1,310);
				
				$('.data-export').prepend('<img id="print-VatsaleH" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#print-VatsaleH").hover(function() {
					document.getElementById("print-VatsaleH").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("print-VatsaleH").style.filter = "contrast(100%)";
				});
				
				$('.data-export').prepend('<img id="table-VatsaleH-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#table-VatsaleH-excel").hover(function() {
					document.getElementById("table-VatsaleH-excel").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("table-VatsaleH-excel").style.filter = "contrast(100%)";
				});
				
				$("#table-VatsaleH-excel").click(function(){ 
					tableToExcel_Export(data.report,"sheet 1","รายงานภาษีคงเหลือจากการขายผ่อน  "+data.reporttoday); 
				});
				
				$('#print-VatsaleH').click(function(){
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
	
	var order = "";
	if($("#contno").is(":checked")){ 
		order = "CONTNO";
	}else if($("#locat").is(":checked")){
		order = "LOCAT";
	}else if($("#sdate").is(":checked")){
		order = "SDATE";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTNO2 		= (typeof $('#CONTNO2').find(':selected').val() === 'undefined' ? '':$('#CONTNO2').find(':selected').val());
	dataToPost.VATMONTH 	= $('#VATMONTH').val();
	dataToPost.VATYEAR 		= $('#VATYEAR').val();
	dataToPost.order 		= order;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS07/ReportVatsaleH/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/ReportVatsaleH/pdf?condpdf='+data[0];
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