/********************************************************
             ______@06/02/2020______
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
			url: '../Cselect2K/getTAXNO',
			data: function (params){
				dataToPost = new Object();
				dataToPost.now = (typeof $('#TAXNO').find(':selected').val() === 'undefined' ? '':$('#TAXNO').find(':selected').val());
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
			url: '../Cselect2K/getSTRNO',
			data: function (params){
				var vatmoney = "vatmoney";
				dataToPost = new Object();
				dataToPost.vat = vatmoney;
				dataToPost.now = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.recvno = $('#RECVNO').val();
				
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
});
$('#btnaddRD').click(function(){
	AddRD();
});
$('#btnclearRD').click(function(){
	AddRD();
});
function AddRD(){
	$('#stana').hide();
	$('#LOCAT').empty();
	$('#TAXNO').empty();
	$('#STRNO').empty();
	$('#DEBTNO').val('');
	$('#TAXDT').val('');;
	$('#REFDT').val('');;
	$('#NETAMT').val('0.00');
	$('#VATAMT').val('0.00');
	$('#TOTAMT').val('0.00');
	$('#RECVNO').val('');
	$('#LOCAT').attr('disabled',false);
	$('#TAXNO').attr('disabled',false);
	$('#STRNO').attr('disabled',false);
	$('#DEBTNO').attr('disabled',false);
	$('#TAXDT').attr('disabled',false);
	$('#REFDT').attr('disabled',false);
	$('#NETAMT').attr('disabled',false);
	$('#VATAMT').attr('disabled',false);
	$('#TOTAMT').attr('disabled',false);
	$('#RECVNO').attr('disabled',false);
	$('#btndelRD').attr('disabled',true);
	$('#btnclearRD').attr('disabled',false);
	$('#btnsaveRD').attr('disabled',false);
	//$('#btnaddRD').attr('disabled',true);
}
$('#TAXNO').change(function(){
	gettaxno();
});
var taxnodetail = null;
function gettaxno(){
	dataToPost = new Object();
	dataToPost.TAXNO = $('#TAXNO').val();
	dataToPost.LOCAT = $('#LOCAT').val();
	//alert(dataToPost.BIRTHDT);
	taxnodetail = $.ajax({
		url: '../SYS07/ReduceDebtMonyVB/getTAXNO', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#RECVNO').val(data.REFNO);
			$('#REFDT').val(data.TAXDT);
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
	getoutdt();
});
var outdtdetail = null;
function getoutdt(){
	dataToPost = new Object();
	dataToPost.TAXDT = $('#TAXDT').val();
	dataToPost.LOCAT = $('#LOCAT').val();
	//alert(dataToPost.BIRTHDT);
	outdtdetail = $.ajax({
		url: '../SYS07/ReduceDebtMonyVB/getTAXDT', 
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
				$('#DEBTNO').val(data.DEBTNO);
			}
			outdtdetail = null;
		},
		beforeSend: function(){
			if(outdtdetail !== null){
				outdtdetail.abort();
			}
		}
	});
}
$('#VATAMT').click(function(){
	dataToPost = new Object();
	dataToPost.NETAMT = $('#NETAMT').val();
	$.ajax({
		url: '../SYS07/ReduceDebtMoneyVB/getVATAMT', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#NETAMT').val(data.netamt);
			$('#VATAMT').val(data.totalvat);
			$('#TOTAMT').val(data.total);
		}
	});
});
$('#TAXNO').change(function(){
	gettaxno();
});
var taxnodetail = null;
function gettaxno(){
	dataToPost = new Object();
	dataToPost.TAXNO = $('#TAXNO').val();
	dataToPost.LOCAT = $('#LOCAT').val();
	taxnodetail = $.ajax({
		url: '../SYS07/ReduceDebtMoneyVB/getTAXNO', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#RECVNO').val(data.REFNO);
			$('#REFDT').val(data.TAXDT);
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
var strnodetail = null;
function getstrno(){
	dataToPost = new Object();
	dataToPost.STRNO = $('#STRNO').val();
	//alert(dataToPost.BIRTHDT);
	strnodetail = $.ajax({
		url: '../SYS07/ReduceDebtMoneyVB/getSTRNO', 
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
				$('#STRNO').empty();
			}
			strnodetail = null;
		},
		beforeSend: function(){
			if(strnodetail !== null){
				strnodetail.abort();
			}
		}
	});
}
$('#TAXDT').change(function(){
	getoutdt();
});
var outdtdetail = null;
function getoutdt(){
	dataToPost = new Object();
	dataToPost.TAXDT = $('#TAXDT').val();
	dataToPost.LOCAT = $('#LOCAT').val();
	outdtdetail = $.ajax({
		url: '../SYS07/ReduceDebtMoneyVB/getTAXDT', 
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
				$('#DEBTNO').val(data.DEBTNO);
			}
			outdtdetail = null;
		},
		beforeSend: function(){
			if(outdtdetail !== null){
				outdtdetail.abort();
			}
		}
	});
}
$('#btnshowRD').click(function(){
	showreducecar();
});
var Show_reducecar = null;
function showreducecar(){
	$('#loadding').fadeIn(200);
	Show_reducecar = $.ajax({
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
						dataToPost.vatprice = "debtmoney"; 
						$('#loadding').fadeIn(200);
						VATsearch = $.ajax({
							url:'../Cselect2K/getsearchREDUCECAR',
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
									rdc.taxno   = $(this).attr('TAXNO');
									rdc.name1   = $(this).attr('NAME1');
									rdc.refno   = $(this).attr('REFNO');
									rdc.strno   = $(this).attr('STRNO');
									rdc.taxdt   = $(this).attr('TAXDT');
									rdc.refdt   = $(this).attr('REFDT');
									rdc.netamt  = $(this).attr('NETAMT');
									rdc.vatamt  = $(this).attr('VATAMT');
									rdc.totamt  = $(this).attr('TOTAMT');
									rdc.flag    = $(this).attr('FLAG');
										var locatOption = new Option(rdc.locat,rdc.locat, false, false);
										$('#LOCAT').empty().append(locatOption).trigger('click');
										var refnoOption = new Option(rdc.refno,rdc.refno, false, false);
										$('#TAXNO').empty().append(refnoOption).trigger('click');
										var strnoOption = new Option(rdc.strno,rdc.strno, false, false);
										$('#STRNO').empty().append(strnoOption).trigger('click');
										
										$('#DEBTNO').val(rdc.taxno);
										$('#TAXDT').val(rdc.taxdt);
										$('#REFDT').val(rdc.refdt);
										$('#NETAMT').val(rdc.netamt);
										
										if(rdc.vatamt == 0){
											$('#VATAMT').val('0.00');
										}else{
											$('#VATAMT').val(rdc.vatamt);
										}
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
										
										$('#btnclearRD').attr('disabled',true);
										$('#btnsaveRD').attr('disabled',true);
										
									$this.destroy();
								});
								$('#loadding').fadeOut(200);
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
			Show_reducecar = null;
		},
		beforeSend:function(){
			if(Show_reducecar !== null){Show_reducecar.abort();}
		}
	});
}
$('#btnsaveRD').click(function(){
	savereducemoney();
});
var VBreducemoney = null;
function savereducemoney(){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.TAXTYP  = (typeof $('#TAXTYP').find(':selected').val() === 'undefined' ? '':$('#TAXTYP').find(':selected').val());
	dataToPost.TAXNO   = (typeof $('#TAXNO').find(':selected').val() === 'undefined' ? '':$('#TAXNO').find(':selected').val());
	dataToPost.STRNO   = (typeof $('#STRNO').find(':selected').val() === 'undefined' ? '':$('#STRNO').find(':selected').val());
	dataToPost.DEBTNO  = $('#DEBTNO').val();
	dataToPost.TAXDT   = $('#TAXDT').val();
	dataToPost.REFDT   = $('#REFDT').val();
	dataToPost.RECVNO  = $('#RECVNO').val();
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
				VBreducemoney = $.ajax({
					url:'../SYS07/ReduceDebtMoneyVB/Save_reducemoney',
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
							ClearRD();
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
						VBreducemoney = null;
					},
					beforeSend:function(){
						if(VBreducemoney !== null){VBreducemoney.abort();}
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
	delreducemoney();
});
var DEL_reducemoney = null;
function delreducemoney(){
	dataToPost = new Object();
	dataToPost.LOCAT   = $('#LOCAT').val();
	dataToPost.REFNO   = $('#TAXNO').val();
	dataToPost.STRNO   = $('#STRNO').val();
	dataToPost.TAXNO   = $('#DEBTNO').val();
	dataToPost.RECVNO  = $('#RECVNO').val();
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
			if(type === 'ok'){
				$('#loadding').fadeIn(200);
				DEL_reducemoney = $.ajax({
					url: '../SYS07/ReduceDebtMoneyVB/Del_reducemoney', 
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data){
						$('#loadding').fadeOut(200);
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
							AddRD();
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
						DEL_reducemoney = null;
					},
					beforeSend: function(){
						if(DEL_reducemoney !== null){
							DEL_reducemoney.abort();
						}
					}
				});
			}else{
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