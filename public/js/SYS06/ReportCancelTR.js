/********************************************************
             ______@31/01/2020______
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
});
$('#btnreportTR').click(function(){
	printReport();
});
var TR_Report = null;
function printReport(){
	var order = null;
	if($('#OR1').is(":checked")){
		order = "LOCATRECV";
	}else if($('#OR2').is(":checked")){
		order = "INPDT";
	}else if($('#OR3').is(":checked")){
		order = "CHQNO";
	}else if($('#OR4').is(":checked")){
		order = "TMBILDT";
	}
	var cancel = null;
	if($('#C1').is(":checked")){
		cancel = "<>";
	}else if($('#C2').is(":checked")){
		cancel = "=";
	}
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.USERID	 = $('#USERID').val();
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.DATE2     = $('#DATE2').val();
	dataToPost.order	 = order;
	dataToPost.cancel    = cancel;
	TR_Report = $.ajax({
		url:'../SYS06/ReportCancelTR/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/ReportCancelTR/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			TR_Report = null;
		},
		beforeSend:function(){
			if(TR_Report !== null){TR_Report.abort();}
		}
	});
}