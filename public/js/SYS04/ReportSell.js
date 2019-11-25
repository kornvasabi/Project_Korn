$(function(){
	$('#locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#locat').find(':selected').val() === 'undefined' ? '':$('#locat').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#SALCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#SALCOD').find(':selected').val() === 'undefined' ? '':$('#SALCOD').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#PAYTYP').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#ACTICOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#ACTICOD').find(':selected').val() === 'undefined' ? '':$('#ACTICOD').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#STAT').select2();
	
	$('#GCODE').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getGCode',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#MODEL').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = 'HONDA';
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#BAAB').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = 'HONDA';
				dataToPost.MODEL = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#COLOR').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '':$('#COLOR').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.model = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
				dataToPost.baab = (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
				
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#GROUPCUS').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getGROUPCUS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#GROUPCUS').find(':selected').val() === 'undefined' ? '':$('#GROUPCUS').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#AUMPCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getAUMP',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? '':$('#AUMPCOD').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.PROVCOD = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? '':$('#PROVCOD').find(':selected').val());
				
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#PROVCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPROV',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? '':$('#PROVCOD').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
});


var jdbtnt1search = null;
$('#btnt1searchAUD').click(function(){ fnsearch("AUD"); });
$('#btnt1searchACC').click(function(){ fnsearch("ACC"); });

function fnsearch($action){
	dataToPost = new Object();
	dataToPost.locat 	= (typeof $('#locat').find(':selected').val() === 'undefined' ? '':$('#locat').find(':selected').val());
	dataToPost.sSDATE	= $('#sSDATE').val();
	dataToPost.eSDATE	= $('#eSDATE').val();
	dataToPost.SALCOD 	= (typeof $('#SALCOD').find(':selected').val() === 'undefined' ? '':$('#SALCOD').find(':selected').val());
	dataToPost.PAYTYP 	= (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
	dataToPost.ACTICOD 	= (typeof $('#ACTICOD').find(':selected').val() === 'undefined' ? '':$('#ACTICOD').find(':selected').val());
	dataToPost.GCODE 	= (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.MODEL 	= (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
	dataToPost.BAAB 	= (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
	dataToPost.COLOR 	= (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '':$('#COLOR').find(':selected').val());
	dataToPost.STAT 	= (typeof $('#STAT').find(':selected').val() === 'undefined' ? '':$('#STAT').find(':selected').val());
	dataToPost.GROUPCUS = (typeof $('#GROUPCUS').find(':selected').val() === 'undefined' ? '':$('#GROUPCUS').find(':selected').val());
	dataToPost.AUMPCOD 	= (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? '':$('#AUMPCOD').find(':selected').val());
	dataToPost.PROVCOD 	= (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? '':$('#PROVCOD').find(':selected').val());
	dataToPost.REPORT 	= $('input:radio[name=REPORT]:checked').val();
	dataToPost.RPT 		= $('input:radio[name=RPT]:checked').val();
	dataToPost.SORT 	= $('input:radio[name=SORT]:checked').val();
	dataToPost.action 	= $action;
	
	$('#loadding').fadeIn(200);
	jdbtnt1search = $.ajax({
		url:'../SYS04/ReportSell/search',
		data: dataToPost,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'รายงาน',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					//$('#table-RPSellCar').on('draw.dt',function(){ redraw(); });
					let width = 0;
					if($('input:radio[name=REPORT]:checked').val() == 1){
						if($('input:radio[name=RPT]:checked').val() == 1){
							width = ($action == "AUD" ? 365:395);
						}else{
							width = ($action == "AUD" ? 330:395);
						}
					}else if($('input:radio[name=REPORT]:checked').val() == 2){
						if($('input:radio[name=RPT]:checked').val() == 1){
							width = ($action == "AUD" ? 400:460);
						}else{
							width = ($action == "AUD" ? 330:460);
						}
					}else if($('input:radio[name=REPORT]:checked').val() == 3){
						width = ($action == "AUD" ? 290:360);
					}else if($('input:radio[name=REPORT]:checked').val() == 4){
						width = ($action == "AUD" ? 290:315);
					}
					fn_datatables('table-RPSellCar',1,width);
					$('.data-export').prepend('<img id="table-RPSellCar-print" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;margin-left:10px;">');
					$('.data-export').prepend('<img id="table-RPSellCar-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
					$("#table-RPSellCar-excel").click(function(){ 	
						tableToExcel_Export(data.html,$('input:radio[name=REPORT]:checked').text(),"RPSellCar"); 
					});
					$("#table-RPSellCar-print").click(function(){ 	
						/*
						var baseUrl = $('body').attr('baseUrl');
						var url 	= baseUrl+'SYS04/ReportSell/pdf';
						var content = ""
							+"<form id='formpdf' action='"+url+"' method='post' enctype='multipart/form-data' target='my_iframe'>"
							+"		<input type='hidden' name='locat' value='"+dataToPost.locat+"'>"
							+"		<input type='hidden' name='sSDATE' value='"+dataToPost.sSDATE+"'>"
							+"		<input type='hidden' name='eSDATE' value='"+dataToPost.eSDATE+"'>"
							+"		<input type='hidden' name='SALCOD' value='"+dataToPost.SALCOD+"'>"
							+"		<input type='hidden' name='PAYTYP' value='"+dataToPost.PAYTYP+"'>"
							+"		<input type='hidden' name='ACTICOD' value='"+dataToPost.ACTICOD+"'>"
							+"		<input type='hidden' name='GCODE' value='"+dataToPost.GCODE+"'>"
							+"		<input type='hidden' name='MODEL' value='"+dataToPost.MODEL+"'>"
							+"		<input type='hidden' name='BAAB' value='"+dataToPost.BAAB+"'>"
							+"		<input type='hidden' name='COLOR' value='"+dataToPost.COLOR+"'>"
							+"		<input type='hidden' name='STAT' value='"+dataToPost.STAT+"'>"
							+"		<input type='hidden' name='GROUPCUS' value='"+dataToPost.GROUPCUS+"'>"
							+"		<input type='hidden' name='AUMPCOD' value='"+dataToPost.AUMPCOD+"'>"
							+"		<input type='hidden' name='PROVCOD' value='"+dataToPost.PROVCOD+"'>"
							+"		<input type='hidden' name='REPORT' value='"+dataToPost.REPORT+"'>"
							+"		<input type='hidden' name='RPT' value='"+dataToPost.RPT+"'>"
							+"		<input type='hidden' name='SORT' value='"+dataToPost.SORT+"'>"
							+"		<input type='hidden' name='action' value='"+dataToPost.action+"'>"
							+"</form>"
							+"<iframe src='"+baseUrl+"SYS04/ReportSell/loadding' name='my_iframe' width='420' height='315' style='background-color:white;width:100%;height:100%;border:1px solid #ddd;font-size:10pt;'></iframe>"
							+"";
						Lobibox.window({
							title: 'Window title',
							content: content,
							closeOnEsc: false,
							height: $(window).height(),
							width: $(window).width(),
							shown: function($thisPDF){ document.getElementById("formpdf").submit(); }
						});
						*/
						Lobibox.notify('info', {
							title: 'info',
							size: 'mini',
							closeOnClick: false,
							delay: 3000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
						});
					});	
					
					$('#loadding').fadeOut(200);
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});			
			
			jdbtnt1search = null;
		},
		beforeSend: function(){ if(jdbtnt1search !== null){ jdbtnt1search.abort(); } }
	});
}





















