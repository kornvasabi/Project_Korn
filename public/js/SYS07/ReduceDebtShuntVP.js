/********************************************************
             ______@03/03/2020______
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
				dataToPost.TAXNO = "ShuntVP";
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
	$('#RESONCD').select2({
		placeholder: 'เลือก',
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
	$('#btnaddRD').click(function(){
		ClearInput();
		$('#LOCAT').empty();
	});
});
function ClearInput(){
	$('#TAXNO').empty();
	$('#STRNO').val('');
	$('#TAXNO2').val('');
	$('#TAXDT').val('');
	$('#REFDT').val('');
	//$('#LOCAT').empty();
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
	$('#TAXNO2').attr('disabled',false);
	$('#RESONCD').attr('disabled',false);
	$('#LOCAT').attr('disabled',false);
	$('#TAXNO').attr('disabled',false);
	$('#DEBTNO').attr('disabled',false);
	$('#TAXDT').attr('disabled',false);
	//$('#REFDT').attr('disabled',false);
	$('#NETAMT').attr('disabled',false);
	$('#VATAMT').attr('disabled',false);
	$('#TOTAMT').attr('disabled',false);
	$('#RECVNO').attr('disabled',false);
	$('#btndelRD').attr('disabled',true);
	$('#btnclearRD').attr('disabled',false);
	$('#btnsaveRD').attr('disabled',false);
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
		url: '../SYS07/ReduceDebtShuntVP/getdetailTAXNO', 
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
			}
			$('#STRNO').val(data.STRNO);
			$('#REFDT').val(data.TAXDT);
			$('#CONTNO').val(data.CONTNO);
			$('#CUSCOD').val(data.CUSCOD);
			$('#SNAM').val(data.SNAM);
			$('#NAME1').val(data.NAME1);
			$('#NAME2').val(data.NAME2);
			$('#TSALE').val(data.TSALE);
			$('#DESCP').val(data.DESCP);
			$('#NETAMT').val(data.NETAMT);
			$('#VATAMT').val(data.VATAMT);
			$('#TOTAMT').val(data.TOTAMT);
			
			taxnodetail = null;
		},
		beforeSend: function(){
			if(taxnodetail !== null){
				taxnodetail.abort();
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
		url: '../SYS07/ReduceDebtShuntVP/getTAXNO', 
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
								/*
								if(_delete == 'T'){
									$('#btndelRD').attr('disabled',false);	
								}else{	
									$('#btndelRD').attr('disabled',true);	
								}
								*/
								VATsearch = null;
							},
							beforeSend: function(){
								if(VATsearch !== null){ VATsearch.abort(); }
							}
						});
						
					}
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
$('#btnsaveRD').click(function(){
	savereduceshunt();
});
var VPreduceshunt = null;
function savereduceshunt(){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.TAXTYP  = (typeof $('#TAXTYP').find(':selected').val() === 'undefined' ? '':$('#TAXTYP').find(':selected').val());
	dataToPost.TAXNO   = (typeof $('#TAXNO').find(':selected').val() === 'undefined' ? '':$('#TAXNO').find(':selected').val());
	dataToPost.STRNO   = $('#STRNO').val();
	dataToPost.TAXNO2  = $('#TAXNO2').val();
	dataToPost.TAXDT   = $('#TAXDT').val();
	dataToPost.REFDT   = $('#REFDT').val();
	
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.CUSCOD  = $('#CUSCOD').val();
	dataToPost.SNAM    = $('#SNAM').val();
	dataToPost.NAME1   = $('#NAME1').val();
	dataToPost.NAME2   = $('#NAME2').val();
	
	dataToPost.TSALE   = $('#TSALE').val();
	dataToPost.DESCP   = $('#DESCP').val();
	
	dataToPost.NETAMT  = $('#NETAMT').val();
	dataToPost.VATAMT  = $('#VATAMT').val();
	dataToPost.TOTAMT  = $('#TOTAMT').val();
	
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องการบันทึก ?",
		buttons: {
			ok : {
				'class': 'btn btn-primary',
				text: 'ยืนยัน',
				closeOnClick: true,
			},
			cancel : {
				'class': 'btn btn-danger',
				text: 'ยกเลิก',
				closeOnClick: true
			},
		},
		callback: function(lobibox, type){
			$('#loadding').show();
			if(type === 'ok'){
				VPreduceshunt = $.ajax({
					url:'../SYS07/ReduceDebtShuntVP/Save_VatPriceShunt',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						$('#loadding').hide();
						if(data.error){
							Lobibox.notify('warning',{
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
						}
						if(data.status == 'Y'){
							Lobibox.notify('success',{
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
							$('#LOCAT').empty();
							ClearInput();
						}else if(data.status == 'N'){
							Lobibox.notify('error',{
								title: 'ผิดพลาด',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						VPreduceshunt = null;
					},
					beforeSend:function(){
						if(VPreduceshunt !== null){VPreduceshunt.abort();}
					}
				});
			}else{
				$('#loadding').hide();
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: 'ยังไม่บันทึกรายการ'
				});
			}	
		}
	});
}
$('#btndelRD').click(function(){
	delreduceshunt();
});
var VPreduceshuntdel = null;
function delreduceshunt(){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.TAXTYP  = (typeof $('#TAXTYP').find(':selected').val() === 'undefined' ? '':$('#TAXTYP').find(':selected').val());
	dataToPost.TAXNO   = (typeof $('#TAXNO').find(':selected').val() === 'undefined' ? '':$('#TAXNO').find(':selected').val());
	dataToPost.STRNO   = $('#STRNO').val();
	dataToPost.TAXNO2  = $('#TAXNO2').val();
	dataToPost.TAXDT   = $('#TAXDT').val();
	dataToPost.REFDT   = $('#REFDT').val();
	dataToPost.TSALE   = $('#TSALE').val();
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.CUSCOD  = $('#CUSCOD').val();
	dataToPost.NETAMT  = $('#NETAMT').val();
	dataToPost.VATAMT  = $('#VATAMT').val();
	dataToPost.TOTAMT  = $('#TOTAMT').val();
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องการลบ ?",
		buttons: {
			ok : {
				'class': 'btn btn-primary',
				text: 'ยืนยัน',
				closeOnClick: true,
			},
			cancel : {
				'class': 'btn btn-danger',
				text: 'ยกเลิก',
				closeOnClick: true
			},
		},
		callback: function(lobibox, type){
			$('#loadding').show();
			if(type === 'ok'){
				VPreduceshuntdel = $.ajax({
					url:'../SYS07/ReduceDebtShuntVP/Del_VatPriceShunt',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						$('#loadding').hide();
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
						}
						if(data.status == 'Y'){
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
							$('#LOCAT').empty();
							ClearInput();
						}else if(data.status == 'N'){
							Lobibox.notify('error', {
								title: 'ผิดพลาด',
								size: 'mini',
								closeOnClick: false,
								delay: 3000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						VPreduceshuntdel = null;
					},
					beforeSend:function(){
						if(VPreduceshuntdel !== null){VPreduceshuntdel.abort();}
					}
				});
			}else{
				$('#loadding').hide();
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: 'ยังไม่บันทึกรายการ'
				});
			}	
		}
	});
}