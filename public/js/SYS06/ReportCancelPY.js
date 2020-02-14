/********************************************************
             ______@30/01/2020______
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
$('#btnreportPY').click(function(){
	printReport();
});
var PY_Report = null;
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
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.USERID	 = $('#USERID').val();
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.DATE2     = $('#DATE2').val();
	dataToPost.order	 = order;
	PY_Report = $.ajax({
		url:'../SYS06/ReportCancelPY/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/ReportCancelPY/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			PY_Report = null;
		},
		beforeSend:function(){
			if(PY_Report !== null){PY_Report.abort();}
		}
	});
}