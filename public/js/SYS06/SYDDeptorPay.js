/********************************************************
             ______@20/05/2020______
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
});
$('#CONTNO').change(function(){
	fn_getlocat();
});
function fn_getlocat(){
	dataToPost = new Object();
	dataToPost.CONTNO  = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	$.ajax({
		url:'../SYS06/SYDDeptorPay/getLocat',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var newOption = new Option(data.LOCAT,data.LOCAT, false, false);
			$('#LOCAT').empty().append(newOption).trigger('change');
			
			$('#CUSNAME').val(data.CUSNAME);
		}
	});
}
$('#btnreport').click(function(){
	var CONTNO = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	if(CONTNO == ""){
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
		printReport();	
	}
});
var DP_SYD = null;
function printReport(){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.CONTNO  = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());	
	DP_SYD = $.ajax({
		url:'../SYS06/SYDDeptorPay/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/SYDDeptorPay/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			DP_SYD = null;
		},
		beforeSend:function(){
			if(DP_SYD !== null){DP_SYD.abort();}
		}
	});	
}
