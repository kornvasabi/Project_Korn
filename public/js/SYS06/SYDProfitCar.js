/********************************************************
             ______@21/05/2020______
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
	$('#OFFICER').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
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
		url:'../SYS06/SYDProfitCar/getLocat',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var newOption = new Option(data.LOCAT,data.LOCAT, false, false);
			$('#LOCAT').empty().append(newOption).trigger('change');
		}
	});
}
$('#btnreport').click(function(){
	printReport();
});
var PC_SYD = null;
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
	dataToPost.F_DATE  = $('#F_DATE').val();
	dataToPost.T_DATE  = $('#T_DATE').val();
	dataToPost.OFFICER = (typeof $('#OFFICER').find(':selected').val() === 'undefined' ? '':$('#OFFICER').find(':selected').val());
	dataToPost.order   = order;
	
	PC_SYD = $.ajax({
		url:'../SYS06/SYDProfitCar/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/SYDProfitCar/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			PC_SYD = null;
		},
		beforeSend:function(){
			if(PC_SYD !== null){PC_SYD.abort();}
		}
	});		
}