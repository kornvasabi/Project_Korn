/********************************************************
             ______@05/03/2020______
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
	$('#TAXTYP').select2({
		placeholder: '',
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#TAXNO').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getTAXNO_VP',
			data: function (params){
				dataToPost = new Object();
				dataToPost.TAXNO = "SendVP";
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
				
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
	$('#STRNO').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getSTRNO_VP',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
				dataToPost.contno = $('#CONTNO').val();
				
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
	$('#btndelRD').attr('disabled',true);
	$('#stana').hide();
	
	$('#LOCAT').change(function(){
		ClearInput();
	});
});
function ClearInput(){
	$('#TAXNO').empty();
	$('#STRNO').empty();
	$('#TAXNO2').val('');
	$('#TAXDT').val('');
	$('#INPDT').val('');
	$('#CONTNO').val('');
	$('#CUSCOD').val('');
	$('#SNAM').val('');
	$('#NAME1').val('');
	$('#NAME2').val('');
	$('#TSALE').val('');
	$('#DESCP').val('');
	$('#RESONCD').val('');
	$('#RESNDES').val('');
	$('#NETAMT').val('0.00');
	$('#VATAMT').val('0.00');
	$('#TOTAMT').val('0.00');
}
$('#TAXNO').change(function(){
	gettaxnodetail();
});
var taxnodetail = null;
function gettaxnodetail(){
	dataToPost = new Object();
	dataToPost.TAXNO = (typeof $('#TAXNO').find(':selected').val() === 'undefined' ? '':$('#TAXNO').find(':selected').val());
	dataToPost.LOCAT = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	//alert(dataToPost.BIRTHDT);
	taxnodetail = $.ajax({
		url: '../SYS07/ReduceDebtSendVP/getdetailTAXNO', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#STRNO').val(data.STRNO);
			$('#INPDT').val(data.INPDT);
			$('#CONTNO').val(data.CONTNO);
			$('#CUSCOD').val(data.CUSCOD);
			$('#SNAM').val(data.SNAM);
			$('#NAME1').val(data.NAME1);
			$('#NAME2').val(data.NAME2);
			$('#TSALE').val(data.TSALE);
			$('#DESCP').val(data.DESCP);
			
			taxnodetail = null;
		},
		beforeSend: function(){
			if(taxnodetail !== null){
				taxnodetail.abort();
			}
		}
	});
}
$('#STRNO').change(function(){
	getstrno();
});
var detailstrno = null;
function getstrno(){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	//alert(dataToPost.BIRTHDT);
	detailstrno = $.ajax({
		url: '../SYS07/ReduceDebtSendVP/getdetailSTRNO', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#NETAMT').val(data.NPRICE);
			$('#VATAMT').val(data.VATPRC);
			$('#TOTAMT').val(data.TOTPRC);
			
			detailstrno = null;
		},
		beforeSend: function(){
			if(detailstrno !== null){
				detailstrno.abort();
			}
		}
	});
}
$('#TAXDT').change(function(){
	gettexno();
});
var gettaxno = null;
function gettexno(){
	dataToPost = new Object();
	dataToPost.TAXDT = $('#TAXDT').val();
	dataToPost.LOCAT = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	gettaxno = $.ajax({
		url: '../SYS07/ReduceDebtSendVP/getTAXNO', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				$('#TAXDT').val('');
			}else{
				$('#TAXNO2').val(data.TAXNO);
			}
			gettaxno = null;
		},
		beforeSend: function(){
			if(gettaxno !== null){
				gettaxno.abort();
			}
		}
	});
}