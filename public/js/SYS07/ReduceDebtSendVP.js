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
	$('#RESONCD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getRESONCD',
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
$('#RESONCD').change(function(){
	dataToPost = new Object();
	dataToPost.RESONCD = (typeof $('#RESONCD').find(':selected').val() === 'undefined' ? '':$('#RESONCD').find(':selected').val());
	$.ajax({
		url: '../SYS07/ReduceDebtSendVP/getRESNDES',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#RESNDES').val(data.RESNDES);
		}
	});
});
$('#btnshowRD').click(function(){
	fn_QueryDebtPrice();
});
var QueryDebtPrice = null;
function fn_QueryDebtPrice(){
	$('#loadding').fadeIn(200);
	QueryDebtPrice = $.ajax({
		url:'../Cselect2K/getfromREDUCECAR',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			Lobibox.window({
				title: 'FORM SEARCH',
				width: 600,
				height: 900,
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					
					/*
					var VATsearch = null;
					$('#btnsearch').click(function(){ fnResultVB(); });
					function fnResultVB(){
						dataToPost = new Object();
						dataToPost.locat = $('#locat').val();
						dataToPost.taxno = $('#taxno').val();
						dataToPost.refno = $('#refno').val();
						dataToPost.vatprice = "debtshunt"; 
						$('#loadding').fadeIn(200);
						VATsearch = $.ajax({
							url:'../Cselect2K/SearchDebtPrice',
							data:dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								
								$('#loadding').fadeOut(200);
								$('#vat_result').html(data.html);
								
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});
								$('.getit').unbind('click');
								$('.getit').click(function(){
									rdc = new Object();
									rdc.locat   = $(this).attr('LOCAT');
									rdc.refno   = $(this).attr('REFNO');
									rdc.strno   = $(this).attr('STRNO');
									rdc.taxno   = $(this).attr('TAXNO');
									rdc.taxdt   = $(this).attr('TAXDT');
									rdc.refdt   = $(this).attr('REFDT');
									rdc.inpdt   = $(this).attr('INPDT');
									rdc.contno  = $(this).attr('CONTNO');
									rdc.cuscod  = $(this).attr('CUSCOD');
									rdc.snam    = $(this).attr('SNAM');
									rdc.name1   = $(this).attr('NAME1');
									rdc.name2   = $(this).attr('NAME2');
									rdc.tsale   = $(this).attr('TSALE');
									rdc.descp   = $(this).attr('DESCP');
									rdc.netamt  = $(this).attr('NETAMT');
									rdc.vatamt  = $(this).attr('VATAMT');
									rdc.totamt  = $(this).attr('TOTAMT');
									rdc.flag    = $(this).attr('FLAG');
									
									var locatOption = new Option(rdc.locat,rdc.locat, false, false);
									$('#LOCAT').empty().append(locatOption).trigger('click');
									
									var refnoOption = new Option(rdc.refno,rdc.refno, false, false);
									$('#TAXNO').empty().append(refnoOption).trigger('click');
									
									$('#STRNO').val(rdc.strno);
									
									$('#TAXNO2').val(rdc.taxno);
									$('#TAXDT').val(rdc.taxdt);
									$('#REFDT').val(rdc.refdt);
									$('#CONTNO').val(rdc.contno);
									$('#CUSCOD').val(rdc.cuscod);
									$('#SNAM').val(rdc.snam);
									$('#NAME1').val(rdc.name1);
									$('#NAME2').val(rdc.name2);
									$('#TSALE').val(rdc.tsale);
									$('#DESCP').val(rdc.descp);
									
									$('#NETAMT').val(rdc.netamt);
									$('#VATAMT').val(rdc.vatamt);
									$('#TOTAMT').val(rdc.totamt);
									
									
									if(rdc.flag == 'C'){
										$('#stana').show();
									}else{
										$('#stana').hide();
										$('#btndelRD').attr('disabled',false);
									}
									//$('#btndelRD').attr('disabled',false);
									
									$('#LOCAT').attr('disabled',true);
									$('#TAXNO').attr('disabled',true);
									$('#STRNO').attr('disabled',true);
									$('#DEBTNO').attr('disabled',true);
									$('#TAXDT').attr('disabled',true);
									$('#REFDT').attr('disabled',true);
									$('#NETAMT').attr('disabled',true);
									$('#VATAMT').attr('disabled',true);
									$('#TOTAMT').attr('disabled',true);
									$('#RECVNO').attr('disabled',true);
									$('#TAXNO2').attr('disabled',true);
									$('#RESONCD').attr('disabled',true);
									$('#btnclearRD').attr('disabled',true);
									$('#btnsaveRD').attr('disabled',true);
										
									$this.destroy();
								});
								//$('#loadding').fadeOut(200);
								
								if(_delete == 'T'){
									$('#btndelRD').attr('disabled',false);	
								}else{	
									$('#btndelRD').attr('disabled',true);	
								}
								
								VATsearch = null;
							},
							beforeSend: function(){
								if(VATsearch !== null){ VATsearch.abort(); }
							}
						});
						
					}
					*/
				},
				beforeClose : function(){
					
				}
			});
			QueryDebtPrice = null;
		},
		beforeSend:function(){
			if(QueryDebtPrice !== null){QueryDebtPrice.abort();}
		}
	});
}