/********************************************************
             ______@17/03/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
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
	$('#TAXNO1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTAXNO_Reduce',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
				
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
	$('#TAXNO2').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTAXNO_Reduce',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
				
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
	$('#LOCAT').change(function(){
		$('#TAXNO1').empty();
		$('#TAXNO2').empty();
	});
});
$('#btnBateDebt').click(function(){
	printDebtReduce();
});
VP_Report = null;
function printDebtReduce(){
	var snam = "";
	if($('#S1').is(":checked")){ 
		snam = "S1";
	}else if($('#S2').is(":checked")){
		snam = "S2";
	}
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.TAXNO1  = (typeof $('#TAXNO1').find(':selected').val() === 'undefined' ? '':$('#TAXNO1').find(':selected').val());	
	dataToPost.TAXNO2  = (typeof $('#TAXNO2').find(':selected').val() === 'undefined' ? '':$('#TAXNO2').find(':selected').val());
	dataToPost.snam    = snam;
	if(dataToPost.LOCAT == ''){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาเลือกเงื่อนไขสาขาก่อนออกใบลดหนี้ครับ'
		});
	}else if(dataToPost.TAXNO1 == ''){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาเลือกเงื่อนไขจากเลขที่สาขาก่อนออกใบลดหนี้ครับ'
		});
	}else if(dataToPost.TAXNO2 == ''){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาเลือกเงื่อนไขถึงเลขที่สาขาก่อนออกใบลดหนี้ครับ'
		});
	}else{
		VP_Report = $.ajax({
			url: '../SYS07/ReduceDebtFormVP/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS07/ReduceDebtFormVP/pdfDebtReduce?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				VP_Report = null;
			},
			beforeSend:function(){
				if(VP_Report !== null){
					VP_Report.abort();
				}
			}
		});
	}
}