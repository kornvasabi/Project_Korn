$(function(){
	$('#locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
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
	
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? '':$('#CUSCOD').find(':selected').val());
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

	$('#REPORT').select2();
});


var jdbtnt1search = null;
$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.locat 	= (typeof $('#locat').find(':selected').val() === 'undefined' ? '':$('#locat').find(':selected').val());
	dataToPost.sRESVDT	= $('#sRESVDT').val();
	dataToPost.eRESVDT	= $('#eRESVDT').val();
	dataToPost.CUSCOD 	= (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? '':$('#CUSCOD').find(':selected').val());
	dataToPost.GCODE 	= (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.MODEL 	= (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
	dataToPost.BAAB 	= (typeof $('#BAAB').find(':selected').val() === 'undefined' ? '':$('#BAAB').find(':selected').val());
	dataToPost.COLOR 	= (typeof $('#COLOR').find(':selected').val() === 'undefined' ? '':$('#COLOR').find(':selected').val());
	dataToPost.REPORT 	= (typeof $('#REPORT').find(':selected').val() === 'undefined' ? '':$('#REPORT').find(':selected').val());
	
	jdbtnt1search = $.ajax({
		url:'../SYS04/ReportReserve/search',
		data: dataToPost,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'บันทึกรายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					//$('#table-RPReserveCar').on('draw.dt',function(){ redraw(); });
					fn_datatables('table-RPReserveCar',1,335);
					$('.data-export').prepend('<img id="table-RPReserveCar-print" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;margin-left:10px;">');
					$('.data-export').prepend('<img id="table-RPReserveCar-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
					$("#table-RPReserveCar-excel").click(function(){ 	
						tableToExcel_Export(data.html,"รายงานการจอง","RPReserveCar"); 
					});
					$("#table-RPReserveCar-print").click(function(){ 	
						var baseUrl = $('body').attr('baseUrl');
						var url 	= baseUrl+'SYS04/ReportReserve/pdf';
						var content = ""
							+"<form id='formpdf' action='"+url+"' method='post' enctype='multipart/form-data' target='my_iframe'>"
							+"		<input type='hidden' name='locat' value='"+dataToPost.locat+"'>"
							+"		<input type='hidden' name='sRESVDT' value='"+dataToPost.sRESVDT+"'>"
							+"		<input type='hidden' name='eRESVDT' value='"+dataToPost.eRESVDT+"'>"
							+"		<input type='hidden' name='CUSCOD' value='"+dataToPost.CUSCOD+"'>"
							+"		<input type='hidden' name='GCODE' value='"+dataToPost.GCODE+"'>"
							+"		<input type='hidden' name='MODEL' value='"+dataToPost.MODEL+"'>"
							+"		<input type='hidden' name='BAAB' value='"+dataToPost.BAAB+"'>"
							+"		<input type='hidden' name='COLOR' value='"+dataToPost.COLOR+"'>"
							+"		<input type='hidden' name='REPORT' value='"+dataToPost.REPORT+"'>"
							+"</form>"
							+"<iframe src='"+baseUrl+"SYS04/ReportReserve/loadding' name='my_iframe' width='420' height='315' style='background-color:white;width:100%;height:100%;border:1px solid #ddd;font-size:10pt;'></iframe>"
							+"";
						Lobibox.window({
							title: 'Window title',
							content: content,
							closeOnEsc: false,
							height: $(window).height(),
							width: $(window).width(),
							shown: function($thisPDF){ document.getElementById("formpdf").submit(); }
						});
					});	
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});			
			
			jdbtnt1search = null;
		},
		beforeSend: function(){ if(jdbtnt1search !== null){ jdbtnt1search.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});
























