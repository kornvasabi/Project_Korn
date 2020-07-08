/********************************************************
             ______@30/05/2020______
			 Pasakorn Boonded

********************************************************/
$(function(){
	$('#LOCAT').select2({
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
	$('#GCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGCODE',
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
$('#btnreport').click(function(){
	var CONTNO = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	var LOCAT  = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	var GCODE  = (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	if(CONTNO == "" && LOCAT == ""){
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
			msg: 'กรุณาระบุเงื่อนไขก่อนค้นหารายงานครับ'
		});
	}else{
		printReport();	
	}
});
var PPS_SYD = null;
function printReport(){
	var order = null;
	if($('#OR1').is(':checked')){
		order = "LOCAT";
	}else if($('#OR2').is(':checked')){
		order = "CONTNO";
	}else if($('#OR3').is(':checked')){
		order = "CUSCOD";
	}
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.CONTNO  = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	dataToPost.DATE  = $('#DATE').val();
	dataToPost.GCODE   = (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.order   = order;
	
	PPS_SYD = $.ajax({
		url:'../SYS06/SYDProfitStale/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/SYDProfitStale/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			PPS_SYD = null;
		},
		beforeSend:function(){
			if(PPS_SYD !== null){PPS_SYD.abort();}
		}
	});
}