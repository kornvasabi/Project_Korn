/********************************************************
             ______@03/02/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#CONTNO').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCONTNO_RP',
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

$('#CONTNO').change(function(){
	getcontno();
});
var contnodetail = null;
function getcontno(){
	dataToPost = new Object();
	dataToPost.CONTNO = $('#CONTNO').val();
	//alert(dataToPost.BIRTHDT);
	contnodetail = $.ajax({
		url: '../SYS06/ReportCardDT/getCONTNO_D', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#LOCAT').val(data.LOCAT);
			$('#CUSNAME').val(data.CUSNAME);
			contnodetail = null;
		},
		beforeSend: function(){
			if(contnodetail !== null){
				contnodetail.abort();
			}
		}
	});
}
$('#btnreportCardDT').click(function(){
	printReport();
});
var DT_Report = null;
function printReport(){
	var show1 = null;
	if($('#SYD').is(":checked")){
		show1 = "SYD";
	}else if($('#STR').is(":checked")){
		show1 = "STR";
	}
	
	var show2 = null;
	if($('#YSH').is(":checked")){
		show2 = "Y";
	}else if($('#OR2').is(":checked")){
		show2 = "N";
	}
	dataToPost = new Object();
	dataToPost.CONTNO = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	dataToPost.LOCAT	 = $('#LOCAT').val();
	dataToPost.show1	 = show1;
	dataToPost.show2     = show2;
	if(dataToPost.CONTNO == ""){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
			soundExt: '.ogg',
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาระบุเลขที่สัญญาก่อนครับ'
		});
	}else{
		DT_Report = $.ajax({
			url:'../SYS06/ReportCardDT/conditiontopdf',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportCardDT/pdf?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title:'พิมพ์รายงาน',
					content:content,
					closeOnEsc:false,
					height:$(window).height(),
					width:$(window).width()
				});
				DT_Report = null;
			},
			beforeSend:function(){
				if(DT_Report !== null){DT_Report.abort();}
			}
		});
	}
}