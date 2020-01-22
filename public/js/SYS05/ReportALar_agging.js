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
	
	DATECHANGE = null
	$('#FRMDATE').change(function(){ 
		var frmdate = $('#FRMDATE').val();
		dataToPost = new Object();
		dataToPost.frmdate = frmdate
		DATECHANGE = $.ajax({
			url : '../Cselect2b/dateofendmonth',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				$('#TODATE').val(data.dateofendmonth);			
				DATECHANGE = null;
			},
			beforeSend: function(){
				if(DATECHANGE !== null){
					DATECHANGE.abort();
				}
			}
		});
	});
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	//alert('5555');
	search();
});

var reportsearch = null;
function search(){
	var vat = "";
	if($("#sumvat").is(":checked")){ 
		vat = "sumvat";
	}else if($("#notvat").is(":checked")){
		vat = "notvat";
	}
	
	var orderby = "";
	if($("#ldate").is(":checked")){ 
		orderby = "LDATE";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOLL1 	= (typeof $('#BILLCOLL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.vat 			= vat;
	dataToPost.orderby 		= orderby;
	
	if(dataToPost.LOCAT1 == "" && dataToPost.BILLCOLL1 == ""){	
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
			msg: 'กรุณาระบุ สาขา หรือ รหัสพนักงานเก็บเงิน'
		});
	}else{
		$('#loadding').show();
		reportsearch = $.ajax({
			url: '../SYS05/ReportALar_agging/search',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				$('#loadding').hide();	
				Lobibox.window({
					title: 'รายงานรายละเอียดอายุหนี้',
					content: data.html,
					height: $(window).height(),
					width: $(window).width(),
					closeOnEsc: false,
					draggable: false
				});
				
				fn_datatables('table-ReportALar_agging',1,305);
				
				$('.data-export').prepend('<img id="print-ALar_agging" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#print-ALar_agging").hover(function() {
					document.getElementById("print-ALar_agging").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("print-ALar_agging").style.filter = "contrast(100%)";
				});
				
				$('.data-export').prepend('<img id="table-ALar_agging-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
				$("#table-ALar_agging-excel").hover(function() {
					document.getElementById("table-ALar_agging-excel").style.filter = "contrast(70%)";
				}, function() {
					document.getElementById("table-ALar_agging-excel").style.filter = "contrast(100%)";
				});
				
				$("#table-ALar_agging-excel").click(function(){ 
					tableToExcel_Export(data.report,"sheet 1","รายงานรายละเอียดอายุหนี้ "+data.reporttoday); 
				});
				
				$('#print-ALar_agging').click(function(){
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
	
	var vat = "";
	if($("#sumvat").is(":checked")){ 
		vat = "sumvat";
	}else if($("#notvat").is(":checked")){
		vat = "notvat";
	}
	
	var orderby = "";
	if($("#ldate").is(":checked")){ 
		orderby = "LDATE";
	}else if($("#contno").is(":checked")){
		orderby = "CONTNO";
	}else if($("#billcoll").is(":checked")){ 
		orderby = "BILLCOLL";
	}
	
	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.BILLCOLL1 	= (typeof $('#BILLCOLL1').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.vat 			= vat;
	dataToPost.orderby 		= orderby;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS05/ReportALar_agging/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/ReportALar_agging/pdf?condpdf='+data[0];
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