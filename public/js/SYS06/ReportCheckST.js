/********************************************************
             ______@01/02/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#LOCATRECV').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#BKCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getBKCODE',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});
$('#btnreportST').click(function(){
	printReport();
});
var ST_Report = null;
function printReport(){
	var datareport = null;
	if($('#DR1').is(":checked")){
		datareport = "H";
	}else if($('#DR2').is(":checked")){
		datareport = "P";
	}else if($('#DR3').is(":checked")){
		datareport = "";
	}else if($('#DR4').is(":checked")){
		datareport = "B";
	}else if($('#DR5').is(":checked")){
		datareport = "R";
	}
	var order = null;
	if($('#D1').is(":checked")){
		order = "TMBILDT";
	}else if($('#D2').is(":checked")){
		order = "CHQDT";
	}
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.BKCODE    = (typeof $('#BKCODE').find(':selected').val() === 'undefined' ? '':$('#BKCODE').find(':selected').val());
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.DATE2     = $('#DATE2').val();
	dataToPost.USERID	 = $('#USERID').val();
	dataToPost.datareport= datareport;
	dataToPost.order	 = order;
	ST_Report = $.ajax({
		url:'../SYS06/ReportCheckST/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/ReportCheckST/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			ST_Report = null;
		},
		beforeSend:function(){
			if(ST_Report !== null){ST_Report.abort();}
		}
	});
}