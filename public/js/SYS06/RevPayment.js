/********************************************************
             ______@04/03/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
"use strict";
var _groupType  = $('.tab1[name="home"]').attr('groupType');
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

var jdbtnt1search		  = null;
var jdbtnt1revpayment	  = null;
var jd_add_payment		  = null;
var jd_add_cuscod		  = null;
var JDselectPickers_Cache = null;
var OBJadd_btnAlert 	  = null;
var OBJadd_CONTNO_detail  = null;

$(function(){
	$("#sch_locatrecv").attr('disabled',(_level==1?false:true));
	$("#sch_locatrecv").selectpicker();
	if(_groupType != "OFF"){
		$("#sch_locatrecv").empty().append('<option value="'+_locat+'" selected>'+_locat+'</option>');
		$("#sch_locatrecv").attr('disabled',true).selectpicker('refresh');;
	}else{
		//$("#LOCAT").empty();
		$("#sch_locatrecv").attr('disabled',false).selectpicker('refresh');;
	}
});

/*
$('#LOCAT').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) { 
	var filter = $("#LOCAT").parent().find("[aria-label=Search]");
	FN_JD_BSSELECT("LOCAT",filter,"getLOCAT2");
});

$("#LOCAT").parent().find("[aria-label=Search]").keyup(function(){ 
	FN_JD_BSSELECT("LOCAT",$(this),"getLOCAT2");
});
*/

function FN_JD_BSSELECT($id,$thisSelected,$func){
	var dataToPost = new Object();
	dataToPost.filter = $thisSelected.val();
	dataToPost.now	  = (typeof $("#"+$id).selectpicker('val') == null ? "":$("#"+$id).selectpicker('val'));
	
	clearTimeout(JDselectPickers);
	var JDselectPickers = setTimeout(function(){ getdata(); },250);
	
	function getdata(){
		//$("#"+$id+" UI.dropdown-menu").html("loadding...");
		JDselectPickers_Cache = $.ajax({
			url: '../SYS04/Standard/'+$func,
			data: dataToPost,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#"+$id).empty().append(data.opt);
				$("#"+$id).selectpicker('refresh');
				
				JDselectPickers_Cache= null;
			},
			beforeSend: function(){ if(JDselectPickers_Cache !== null){ JDselectPickers_Cache.abort(); } },
			//error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
}


$('#btnt1search').click(function(){ 
	$('#loadding').fadeIn(200);
	var dataToPost = new Object();
	dataToPost.TMBILL = $('#sch_tmbill').val();
	dataToPost.BILLNO = $('#sch_billno').val();
	dataToPost.LOCATRECV = $('#sch_locatrecv').val();
	dataToPost.CUSCOD = $('#sch_cuscod').attr('cuscod');
	dataToPost.STMBILDT = $('#sch_stmbildt').val();
	dataToPost.ETMBILDT = $('#sch_etmbildt').val();
	
	//fnSearch(dataToPost); 
});

function fnSearch(data){
	$('#loadding').fadeIn(200);
	jdbtnt1search = $.ajax({
		url:'../SYS06/RevPayment/Search',
		data: data,
		type:'POST',
		dataType:'json',
		success: function(data){
			var OBJbillDetails = null;
			Lobibox.window({
				title: 'รายการรับชำระ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($thisPDF){
					
					$('#table-payment').on('draw.dt',function(){ redraw(); });
					$('.data-export').prepend('<span class="text-red">** แสดงข้อมูล 100 ลำดับแรก</span>');
					fn_datatables('table-payment',1,170);
					
					function redraw(){
						$('.billDetails').click(function(){
							var dataToPost = new Object();
							dataToPost.action = 'EDIT';
							dataToPost.TMBILL = $(this).attr('TMBILL');
							fnForm(dataToPost); 
						});
					}
				}
			});
			
			$('#loadding').fadeOut(200);
			jdbtnt1search = null;
		},
		beforeSend: function(){ if(jdbtnt1search !== null){ jdbtnt1search.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

$('#btnt1revpayment').click(function(){ 
	var dataToPost = new Object();
	dataToPost.action = 'new';
	fnForm(dataToPost); 
});

function fnForm(data){
	$('#loadding').fadeIn(200);
	jdbtnt1revpayment = $.ajax({
		url:'../SYS06/RevPayment/get_form_received',
		data: data,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'แบบฟอร์มรับชำระ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//มีสิทธิ์เพิ่มข้อมูลหรือไม่
					$('#add_btnSave').attr('disabled',(_insert != 'T' ? true:false));
					//มีสิทธิ์ยกเลิกบิลหรือไม่ (delete)
					//alert(_delete);
					$('#add_btnCalC').attr('disabled',(_delete != 'T' ? true:false));
					
					
					fnFormPayments($this);
					fnFormPaymentsAction($this);
					
					$('#add_btnAlert').attr('CONTNO',$('.LISTCONTNO').attr('CONTNO'));
					$('#add_btnAlert').attr('LOCAT',$('.LISTCONTNO').attr('LOCAT'));
					$('.LISTCONTNO').click(function(){
						$('#add_btnAlert').attr('CONTNO',$('.LISTCONTNO').attr('CONTNO'));
						$('#add_btnAlert').attr('LOCAT',$('.LISTCONTNO').attr('LOCAT'));
					});
					
					$('#loadding').fadeOut(200);
				}
			});
			
			jdbtnt1revpayment = null;
		},
		beforeSend: function(){ if(jdbtnt1revpayment !== null){ jdbtnt1revpayment.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function fnFormPayments($window){
	$('#add_LOCATRECV').select2({
		placeholder: 'เลือก',
		/*
		ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#add_LOCATRECV').find(':selected').val() === 'undefined' ? '': $('#add_LOCATRECV').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
		},
		*/
		allowClear: false,
		multiple: false,
		dropdownParent: $(".tab1"),
		//disabled: (_level==1?false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	if(_level!=1){
		$('#add_LOCATRECV').attr('disabled',true);
	}
	
	$('#add_PAYTYP').select2({
		placeholder: 'เลือก',
		/*
		ajax: {
			url: '../Cselect2/getPAYTYP',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#add_PAYTYP').find(':selected').val() === 'undefined' ? '': $('#add_PAYTYP').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
		},
		*/
		allowClear: false,
		multiple: false,
		dropdownParent: $(".tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});

	$('#add_CHQBK').select2({
		placeholder: 'เลือก',
		/*
		ajax: {
			url: '../Cselect2/getBKMAST',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#add_CHQBK').find(':selected').val() === 'undefined' ? '': $('#add_CHQBK').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
		},
		*/
		allowClear: false,
		multiple: false,
		dropdownParent: $(".tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	$('#add_CUSCOD').click(function(){
		$('#loadding').fadeIn(200);
		
		jd_add_cuscod = $.ajax({
			url:'../Cselect2/getformCUSTOMER',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#add_CUSCOD').attr('disabled',true);
				$('#add_save').attr('disabled',true);
				
				Lobibox.window({
					title: 'FORM CUSTOMER',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($thisCUS){
						var jd_cus_search = null;
						$('#cus_fname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_lname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_idno').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_search').click(function(){ fnResultCUSTOMER(); });
						
						function fnResultCUSTOMER(){
							var data = new Object();
							data.fname = $('#cus_fname').val();
							data.lname = $('#cus_lname').val();
							data.idno = $('#cus_idno').val();
							
							$('#loadding').fadeIn(200);
							jd_cus_search = $.ajax({
								url:'../Cselect2/getResultCUSTOMER',
								data:data,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#cus_result').html(data.html);
									
									$('.CUSDetails').unbind('click');
									$('.CUSDetails').click(function(){
										var dtp = new Object();
										dtp.cuscod  = $(this).attr('CUSCOD');
										dtp.cusname = $(this).attr('CUSNAMES');
										dtp.addrno  = $(this).attr('ADDRNO');
										dtp.addrdes = $(this).attr('ADDRDES');
										
										$('#add_CUSCOD').attr('CUSCOD',dtp.cuscod);
										$('#add_CUSCOD').attr('disabled',true);
										$('#add_CUSCOD').val(dtp.cusname);
										
										var newOption = new Option(dtp.addrdes, dtp.addrno, true, true);
										$('#add_addrno').empty().append(newOption).trigger('change');	
										
										$thisCUS.destroy();
									});
									
									$('#loadding').fadeOut(200);
									jd_cus_search = null;
								},
								beforeSend: function(){
									if(jd_cus_search !== null){ jd_cus_search.abort(); }
								},
								error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
							});
						}
					},
					beforeClose : function(){
						$('#add_save').attr('disabled',false);
						$('#add_CUSCOD').attr('disabled',($('#add_CUSCOD').attr('CUSCOD') == ""?false:true));
						
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
					}
				});
				
				jd_add_cuscod = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(jd_add_cuscod !== null){ jd_add_cuscod.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_CUSCOD_removed').click(function(){
		$('#add_CUSCOD').val('');
		$('#add_CUSCOD').attr('CUSCOD','');
		$('#add_CUSCOD').attr('disabled',false);
	});
	
	
	$('#add_payment').unbind('click');
	$('#add_payment').click(function(){
		var check006 = false;
		var check007 = false;
		$('.del_payment').each(function(){
			if($(this).attr('opt_payfor') == '006') check006 = true;
			if($(this).attr('opt_payfor') == '007') check007 = true;
		});
		
		if(check006){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "รายการรับชำระค่างวด จะรับชำระรวมกับรายการอื่นไม่ได้ครับ"
			});
		}else if(check007){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "รายการตัดสดชำระรวมกับรายการอื่นไม่ได้ครับ"
			});
		}else{
			jd_add_payment = $.ajax({
				url:'../SYS06/RevPayment/get_form_payment',
				//data: data,
				type:'POST',
				dataType:'json',
				success: function(data){
					$('#add_payment').attr('disabled',true);
					
					Lobibox.window({
						title: 'แบบฟอร์มประเภทการรับชำระ',
						//width: $(window).width(),
						//height: $(window).height(),
						content: data.html,
						draggable: true,
						modal: false,
						closeOnEsc: false,
						onShow: function(lobibox){ $('body').append(jbackdrop); },
						shown: function($this){
							fnFormPaymentsCONTNO($this);
						},
						beforeClose: function(){
							$('#add_payment').attr('disabled',false);
							$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
						}
					});
					
					jd_add_payment = null;
				},
				beforeSend: function(){ if(jd_add_payment !== null){ jd_add_payment.abort(); } },
				error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
		}
	});
}

function fnFormPaymentsCONTNO($thisWindow){
	$('#add_PAYFOR').select2({
		allowClear: false,
		multiple: false,
		dropdownParent: $(".tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var dataToPost = new Object();
	dataToPost.top = '';
	dataToPost.now = '';
	dataToPost.q = '';
	
	$.ajax({
		url: '../Cselect2/getPAYFOR',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			var size = data.length;
			for(var i=0;i<size;i++){
				$('#add_PAYFOR').append('<option value="'+data[i]['id']+'">'+data[i]['text']+'</option>');
			}
		}
	});
	
	/*
	$('#add_PAYFOR').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getPAYFOR',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#add_PAYFOR').find(':selected').val() === 'undefined' ? '': $('#add_PAYFOR').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: false,
		multiple: false,
		dropdownParent: $(".tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	*/
	
	$('#add_CONTNO').click(function(){
		$('#loadding').fadeIn(200);
		
		var CH_blocked 	= new Array("001","002","003","004","005","006","007","008","009","011");
		var CH_Payfor 	= (typeof $('#add_PAYFOR').find(':selected').val() === 'undefined' ? '':$('#add_PAYFOR').find(':selected').val());
		var CH_DATA		= (CH_blocked.indexOf(CH_Payfor) == -1 ? "OTHER":"NORMAL");
		
		if(CH_Payfor != ''){
			jd_add_cuscod = $.ajax({
				url:'../Cselect2/getformCONTNO',
				data: {'data':CH_DATA},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#add_CONTNO').attr('disabled',true);
					$('#btn_DATAPayment').attr('disabled',true);
					
					Lobibox.window({
						title: 'ค้นหา เลขที่สัญญา',
						width: $(window).width(),
						height: $(window).height(),
						content: data.html,
						draggable: false,
						closeOnEsc: false,
						shown: function($thisCONT){
							var jd_cus_search = null;
							var PAYDESC = $('#add_PAYFOR').find(':selected').text();
							var PAYCODE = $('#add_PAYFOR').find(':selected').val();
							var CUSNAME = $('#add_CUSCOD').val();
							var CUSCOD  = $('#add_CUSCOD').attr('CUSCOD');
							
							$('#cont_other').select2();
							$('#cont_other').on('select2:select',function(){
								if($(this).find(':selected').val() == "Y"){
									$('#cont_no').attr('disabled',false);
									$('#cont_no').focus();
								}else{
									$('#cont_no').attr('disabled',true);
								}
							});
							
							$('#cont_cus').click(function(){
								$('#loadding').fadeIn(200);
								
								jd_add_cuscod = $.ajax({
									url:'../Cselect2/getformCUSTOMER',
									type: 'POST',
									dataType: 'json',
									success: function(data){
										$('#cont_cus').attr('disabled',true);
										
										Lobibox.window({
											title: 'FORM CUSTOMER',
											//width: $(window).width(),
											//height: $(window).height(),
											content: data.html,
											draggable: false,
											closeOnEsc: false,
											onShow: function(lobibox){ $('body').append(jbackdrop); },
											shown: function($thisCUS){
												var jd_cus_search = null;
												$('#cus_fname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
												$('#cus_lname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
												$('#cus_idno').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
												$('#cus_search').click(function(){ fnResultCUSTOMER(); });
												
												function fnResultCUSTOMER(){
													var data = new Object();
													data.fname = $('#cus_fname').val();
													data.lname = $('#cus_lname').val();
													data.idno = $('#cus_idno').val();
													
													$('#loadding').fadeIn(200);
													jd_cus_search = $.ajax({
														url:'../Cselect2/getResultCUSTOMER',
														data:data,
														type: 'POST',
														dataType: 'json',
														success: function(data){
															$('#cus_result').html(data.html);
															
															$('.CUSDetails').unbind('click');
															$('.CUSDetails').click(function(){
																var dtp = new Object();
																dtp.cuscod  = $(this).attr('CUSCOD');
																dtp.cusname = $(this).attr('CUSNAMES');
																dtp.addrno  = $(this).attr('ADDRNO');
																dtp.addrdes = $(this).attr('ADDRDES');
																
																$('#cont_cus').attr('CUSCOD',dtp.cuscod);
																$('#cont_cus').attr('disabled',true);
																$('#cont_cus').val(dtp.cusname);
																
																var newOption = new Option(dtp.addrdes, dtp.addrno, true, true);
																$('#add_addrno').empty().append(newOption).trigger('change');	
																
																$thisCUS.destroy();
															});
															
															$('#loadding').fadeOut(200);
															jd_cus_search = null;
														},
														beforeSend: function(){
															if(jd_cus_search !== null){ jd_cus_search.abort(); }
														},
														error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
													});
												}
											},
											beforeClose : function(){
												$('#cont_cus').attr('disabled',($('#cont_cus').attr('CUSCOD') == ""?false:true));
												
												$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
											}
										});
										
										jd_add_cuscod = null;
										$('#loadding').fadeOut(200);
									},
									beforeSend: function(){ if(jd_add_cuscod !== null){ jd_add_cuscod.abort(); } },
									error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
								});
							});
							
							$('#cont_cus_removed').click(function(){
								$('#cont_cus').val('');
								$('#cont_cus').attr('CUSCOD','');
								$('#cont_cus').attr('disabled',false);
							});
							
							$('#cont_payfor').val(PAYDESC);
							$('#cont_payfor').attr('PAYFOR',PAYCODE);
							$('#cont_payfor').attr('disabled',true);
							
							if(CUSCOD != ""){
								$('#cont_cus').val(CUSNAME);
								$('#cont_cus').attr('CUSCOD',CUSCOD);
								$('#cont_cus').attr('disabled',true);
								$('#cont_cus_removed').attr('disabled',true);
							}else{
								$('#cont_cus').val('');
								$('#cont_cus').attr('CUSCOD','');
								$('#cont_cus').attr('disabled',false);
								$('#cont_cus_removed').attr('disabled',false);
							}
							
							$('#cont_no').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
							$('#cont_search').click(function(){ fnResultCUSTOMER(); });
							
							function fnResultCUSTOMER(){
								var data = new Object();
								data.PAYFOR = $('#cont_payfor').attr('PAYFOR');
								data.CUSCOD = $('#cont_cus').attr('CUSCOD');
								data.OTHER  = (typeof $('#cont_other').find(':selected').val() === 'undefined' ? '':$('#cont_other').find(':selected').val());
								data.CONTNO = $('#cont_no').val();
								
								$('#loadding').fadeIn(200);
								jd_cus_search = $.ajax({
									url:'../Cselect2/getResultCONTNO',
									data:data,
									type: 'POST',
									dataType: 'json',
									success: function(data){
										$('#cont_result').html(data.html);
										
										$('.cont_selected').unbind('click');
										$('.cont_selected').click(function(){
											var dtp = new Object();
											dtp.cuscod  = $(this).attr('cuscod');
											dtp.cusname = $(this).attr('cusname');
											dtp.contno  = $(this).attr('contno');
											dtp.locat   = $(this).attr('locat');
											dtp.total   = $(this).attr('total');
											dtp.error   = $(this).attr('error');
											dtp.payfor  = $('#cont_payfor').attr('PAYFOR')
											dtp.tmbildt = $('#add_TMBILDT').val();
											
											$('#loadding').fadeIn(200);											
											$.ajax({
												url:'../SYS06/RevPayment/getREBUILDING',
												data: dtp,
												type: 'POST',
												dataType: 'json',
												success: function(data){
													$('#add_PAYAMT').val(data.PAYAMT);
													$('#add_DISCT').val(data.DISCT);
													$('#add_PAYINT').val(data.PAYINT);
													$('#add_DSCINT').val(data.DSCINT);
													$('#add_NETPAY').val(data.NETPAY);
													
													AlertMessage(dtp.contno,"N");
												}
											});
											
											if(dtp.error == ''){
												$('#add_CUSCOD').val(dtp.cusname);
												$('#add_CUSCOD').attr('cuscod',dtp.cuscod);
												$('#add_CUSCOD').attr('disabled',true);
												$('#add_CONTNO').val(dtp.contno);
												$('#add_CONTNO').attr('locat',dtp.locat);
												$('#add_PAYAMT').val(dtp.total);
												//$('#add_CUSCOD_removed').attr('disabled',true);
												$thisCONT.destroy();
											}else{
												Lobibox.notify('warning', {
													title: 'แจ้งเตือน',
													size: 'mini',
													closeOnClick: false,
													delay: 3000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													msg: dtp.error
												});
											}
										});
										
										$('#loadding').fadeOut(200);
										jd_cus_search = null;
									},
									beforeSend: function(){
										if(jd_cus_search !== null){ jd_cus_search.abort(); }
									},
									error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
								});
							}
						},
						beforeClose : function(){
							var disabled = ($('#add_CONTNO').val() == "" ? false:true);
							$('#add_CONTNO').attr('disabled',disabled);
							$('#btn_DATAPayment').attr('disabled',false);
						}
					});
					
					jd_add_cuscod = null;
					$('#loadding').fadeOut(200);
				},
				beforeSend: function(){ if(jd_add_cuscod !== null){ jd_add_cuscod.abort(); } },
				error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
		}else{
			$('#loadding').fadeOut(200);
			Lobibox.notify('info', {
				title: 'สอบถาม',
				size: 'mini',
				closeOnClick: false,
				delay: 3000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "คุณต้องการชำระค่าอะไรครับ"
			});
		}
	});
	
	$('#add_CONTNO_detail').click(function(){
		var dataToPost = new Object();
		dataToPost.CONTNO = $('#add_CONTNO').val();
		dataToPost.LOCAT  = $('#add_CONTNO').attr('locat');
		
		fn_OutstandingBalance(dataToPost);
	});
	
	$('#add_CONTNO_removed').click(function(){
		$('#add_CONTNO').val('');
		$('#add_CONTNO').attr('CONTNO','');
		$('#add_CONTNO').attr('disabled',false);
		$('#add_PAYAMT').val('');
		$('#add_DISCT').val('');
		$('#add_PAYINT').val('');
		$('#add_DSCINT').val('');
		$('#add_NETPAY').val('');
		
		var table_payments = $('.del_payment');
		if(table_payments.length == 0){
			$('#add_CUSCOD').val('');
			$('#add_CUSCOD').attr('cuscod','');
			$('#add_CUSCOD').attr('disabled',false);
		}
	});
	
	var OBJbtn_DATACalc = null; 
	$('#btn_DATACalc').unbind('click');
	$('#btn_DATACalc').click(function(){
		var dataToPost = new Object();
		dataToPost.PAYAMT = $('#add_PAYAMT').val();
		dataToPost.DISCT  = $('#add_DISCT').val();
		dataToPost.PAYINT = $('#add_PAYINT').val();
		dataToPost.DSCINT = $('#add_DSCINT').val();
		dataToPost.NETPAY = $('#add_NETPAY').val();
		
		OBJbtn_DATACalc = $.ajax({
			url:'../SYS06/RevPayment/getDataCalC',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#add_PAYAMT').val(data.PAYAMT);
				$('#add_DISCT').val(data.DISCT);
				$('#add_PAYINT').val(data.PAYINT);
				$('#add_DSCINT').val(data.DSCINT);
				$('#add_NETPAY').val(data.NETPAY);
				
				if(data.error){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: false,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.errorMessage
					});
					
					if($('.lobibox-notify').length > 1){
						$('.lobibox-notify')[$('.lobibox-notify').length - 1].remove();
					}
					$('#btn_DATAPayment').attr('disabled',true);
					$('#add_PAYAMT').focus();
				}else{
					$('.lobibox-notify').remove();
					$('#btn_DATAPayment').attr('disabled',false);
				}
			}
		});
	});
	
	$('#add_PAYAMT').on('keypress',function(e) {
		if(e.which == 13) {
			$('#add_DISCT').focus();
		}
	});
	$('#add_DISCT').on('keypress',function(e) {
		if(e.which == 13) {
			var attr = $('#add_DSCINT').attr('readonly');
			if (typeof attr !== typeof undefined && attr !== false) {
				$('#btn_DATACalc').trigger('click');
			}else{
				$('#add_DSCINT').focus();
			}
		}
	});
	$('#add_DSCINT').on('keypress',function(e) {
		if(e.which == 13) {
			$('#btn_DATACalc').trigger('click');
		}
	});
	
	$('#btn_DATAPayment').unbind('click');
	$('#btn_DATAPayment').click(function(){
		var PAYFOR 		= (typeof $('#add_PAYFOR').find(':selected').val() === 'undefined' ? '':$('#add_PAYFOR').find(':selected').val());
		var PAYFORtxt 	= (typeof $('#add_PAYFOR').find(':selected').val() === 'undefined' ? '':$('#add_PAYFOR').find(':selected').text());
		var CONTNO 		= $('#add_CONTNO').val();
		var LOCAT 		= $('#add_CONTNO').attr('locat');
		var PAYAMT 		= ($('#add_PAYAMT').val()==''?0:$('#add_PAYAMT').val());
		var DISCT 		= ($('#add_DISCT').val()==''?0:$('#add_DISCT').val());
		var PAYINT 		= ($('#add_PAYINT').val()==''?0:$('#add_PAYINT').val());
		var DSCINT 		= ($('#add_DSCINT').val()==''?0:$('#add_DSCINT').val());
		var NETPAY 		= ($('#add_NETPAY').val()==''?0:$('#add_NETPAY').val());
		
		var WARNING		= '';
		if (PAYAMT + PAYINT == 0){ WARNING += (WARNING == "" ? "":"<br>") + "• จำนวนชำระต้องไม่เท่ากับ 0"; }
		if (PAYFOR == ''){ WARNING += (WARNING == "" ? "":"<br>") + "• คุณยังไม่ได้ระบุชำระค่า"; }
		if (CONTNO == ''){ WARNING += (WARNING == "" ? "":"<br>") + "• คุณยังไม่ได้ระบุเลขที่สัญญา"; }
		//if (NETPAY == '0.00'){ WARNING += (WARNING == "" ? "":"<br>") + "• ยอดรับสุทธิต้องไม่เท่ากับ 0 "; }
		
		if (WARNING != ''){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: WARNING
			});
		}else{
			var table_payments = $('#dataTable_ARMGAR');
			var row = '';
			row += '<td><button class="del_payment btn btn-xs btn-danger glyphicon glyphicon-trash" opt_payfor="'+PAYFOR+'" opt_contno="'+CONTNO+'" opt_payamt="'+PAYAMT+'" opt_disct="'+DISCT+'" opt_payint="'+PAYINT+'" opt_dscint="'+DSCINT+'" opt_netpay="'+NETPAY+'" style="cursor:pointer;"> ลบ </button></td>'
			row += '<td>'+PAYFORtxt+'</td>'
			row += '<td class="LISTCONTNO" CONTNO="'+CONTNO+'" LOCAT="'+LOCAT+'" style="cursor:pointer;">'+CONTNO+'</td>'
			row += '<td align="right">'+PAYAMT+'</td>'
			row += '<td align="right">'+DISCT+'</td>'
			row += '<td class="text-red" align="right">'+PAYINT+'</td>'
			row += '<td align="right">'+DSCINT+'</td>'
			row += '<td align="right">'+NETPAY+'</td>'
			
			var error = false;
			$('.del_payment').unbind('click');
			$('.del_payment').each(function(){
				if(PAYFOR == $('.del_payment').attr('opt_payfor') && CONTNO == $('.del_payment').attr('opt_contno')){
					error = true;
					WARNING = 'ผิดพลาด การรับชำระ '+PAYFORtxt+' และเลขที่สัญญา '+CONTNO+' มีในรายการรับชำระแล้ว ไม่สามารถเพิ่มซ้ำได้ครับ';
					return;
				}
			});
			
			if(error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: WARNING
				});
			}else{
				row = '<tr>'+row+'</tr>';
				table_payments.append(row);
				
				$('.LISTCONTNO').click(function(){
					$('#add_btnAlert').attr('CONTNO',$('.LISTCONTNO').attr('CONTNO'));
					$('#add_btnAlert').attr('LOCAT',$('.LISTCONTNO').attr('LOCAT'));
				});
				
				var total = 0;
				var this_NETPAY = '';
				$('.del_payment').each(function(){
					this_NETPAY = $(this).attr('opt_netpay');
					this_NETPAY = this_NETPAY.replace(',','');
					total += parseFloat(this_NETPAY);
				});
				
				$('#add_CHQTMP').val(addCommas(total));
				$('#add_CHQAMT').val(addCommas(total));
				$('#add_CUSCOD_removed').attr('disabled',true);
				
				$('.del_payment').click(function(){
					var $this_del_payment = $(this);
					
					Lobibox.confirm({
						title: 'ยืนยันการทำรายการ',
						draggable: true,
						iconClass: false,
						closeOnEsc: false,
						closeButton: false,
						msg: 'ยืนยัน การลบรายการรับชำระ',
						buttons: {
							ok : {
								'class': 'btn btn-primary glyphicon glyphicon-ok',
								text: ' ยืนยันการทำรายการ',
								closeOnClick: false,
							},
							cancel : {
								'class': 'btn btn-danger glyphicon glyphicon-remove',
								text: ' ยกเลิก',
								closeOnClick: true
							},
						},
						onShow: function(lobibox){ $('body').append(jbackdrop); },
						//shown: function($this){ $('body').append(jbackdrop); },
						callback: function(lobibox, type){
							var confirm_lobibox = lobibox;
							
							if (type === 'ok'){
								$this_del_payment.parent().parent().remove()
								
								var total = 0;
								var this_NETPAY = '';
								$('.del_payment').each(function(){
									this_NETPAY = $(this).attr('opt_netpay');
									this_NETPAY = this_NETPAY.replace(',','');
									total += parseFloat(this_NETPAY);
								});
								
								
								$('#add_CHQTMP').val(addCommas(total));
								$('#add_CHQAMT').val(addCommas(total));
								//$this_del_payment.parent().parent().draw();
								//$('#dataTable_ARMGAR').draw();
								
								confirm_lobibox.destroy();
							}
							
							$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
						}
					});
				});
			}
			
			$thisWindow.destroy();
		}
	});
}

function fnFormPaymentsAction($window){
	var OBJadd_btnAROther = null;
	var OBJadd_btnARPAY = null;
	var OBJadd_btnListPayment = null;
	var OBJadd_btnCalC = null;
	var OBJadd_btnPrint = null;
	var OBJadd_btnBillFN = null;
	var OBJadd_btnFORMSETAlert = null;
	var OBJadd_btnSave = null;
	var OBJadd_btnCanC = null;
	
	$('#add_btnAROther').click(function(){
		var dataToPost = new Object();
		dataToPost.CUSCOD = $('#add_CUSCOD').attr('CUSCOD');
		dataToPost.CONTNO = $('#add_btnAlert').attr('CONTNO');
		dataToPost.LOCAT  = $('#add_btnAlert').attr('LOCAT');
		
		OBJadd_btnAROther = $.ajax({
			url:'../SYS06/RevPayment/getAROthers',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnAROther !== null){ OBJadd_btnAROther.abort(); }},
			success: function(data){ 
				Lobibox.window({
					title: 'ลูกหนี้อื่น',
					width: '1000',
					//height: '300',
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						OBJadd_btnAROther = null;
					},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
			},
			beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
		});
	});
	
	$('#add_btnARPAY').click(function(){
		var dataToPost = new Object();
		dataToPost.CUSCOD = $('#add_CUSCOD').attr('CUSCOD');
		dataToPost.CONTNO = $('#add_btnAlert').attr('CONTNO');
		dataToPost.LOCAT  = $('#add_btnAlert').attr('LOCAT');
		
		OBJadd_btnARPAY = $.ajax({
			url:'../SYS06/RevPayment/getARPAY',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnARPAY !== null){ OBJadd_btnARPAY.abort(); }},
			success: function(data){ 
				Lobibox.window({
					title: 'ตารางสัญญา',
					width: $(window).width(),
					height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						
						OBJadd_btnARPAY = null;
					},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
			},
			beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
		});
	});
	
	$('#add_btnListPayment').click(function(){
		var dataToPost = new Object();
		dataToPost.CUSCOD = $('#add_CUSCOD').attr('CUSCOD');
		dataToPost.CONTNO = $('#add_btnAlert').attr('CONTNO');
		dataToPost.LOCAT  = $('#add_btnAlert').attr('LOCAT');
		
		OBJadd_btnAROther = $.ajax({
			url:'../SYS06/RevPayment/getListPayments',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnAROther !== null){ OBJadd_btnAROther.abort(); }},
			success: function(data){ 
				Lobibox.window({
					title: 'รายการรับชำระเงิน',
					width: $(window).width(),
					height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						OBJadd_btnAROther = null;
					},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
			},
			beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
		});
	});
	
	$('#add_btnSave').click(function(){
		var dataToPost = new Object();
		dataToPost.TMBILL 	 = $('#add_TMBILL').val();
		dataToPost.TMBILDT 	 = $('#add_TMBILDT').val();
		dataToPost.LOCATRECV = $('#add_LOCATRECV').val();
		dataToPost.PAYTYP	 = $('#add_PAYTYP').val();
		dataToPost.CUSCOD	 = $('#add_CUSCOD').attr('CUSCOD');
		dataToPost.REFNO	 = $('#add_REFNO').val();
		dataToPost.CHQNO	 = $('#add_CHQNO').val();
		dataToPost.CHQDT	 = $('#add_CHQDT').val();
		dataToPost.CHQAMT	 = $('#add_CHQAMT').val();
		dataToPost.CHQBK	 = $('#add_CHQBK').val();
		dataToPost.CHQBR	 = $('#add_CHQBR').val();
		dataToPost.BILLNO	 = $('#add_BILLNO').val();
		dataToPost.BILLDT	 = $('#add_BILLDT').val();
		
		var data_payment = new Array();
		$('.del_payment').each(function(){
			var data_paymentByOne = new Object();
			data_paymentByOne.opt_payfor = ($(this).attr('opt_payfor'));
			data_paymentByOne.opt_contno = ($(this).attr('opt_contno'));
			data_paymentByOne.opt_payamt = ($(this).attr('opt_payamt'));
			data_paymentByOne.opt_disct  = ($(this).attr('opt_disct'));
			data_paymentByOne.opt_payint = ($(this).attr('opt_payint'));
			data_paymentByOne.opt_dscint = ($(this).attr('opt_dscint'));
			data_paymentByOne.opt_netpay = ($(this).attr('opt_netpay'));
			data_payment.push(data_paymentByOne);
		});
		
		dataToPost.data_payment = (data_payment.length == 0 ? new Array():JSON.stringify(data_payment));
		
		$('#loadding').fadeIn(200);
		OBJadd_btnSave = $.ajax({
			url:'../SYS06/RevPayment/SavePayments',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnSave !== null){ OBJadd_btnSave.abort(); }},
			success: function(data){ 
				Lobibox.notify((data.error?"warning":"success"), {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.errorMessage
				});
				
				OBJadd_btnSave = null; 
				$('#loadding').fadeOut(200);
			},
			beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
		});
		//$window.destroy();
	});
	
	var OBJadd_btnCanC = null;
	$('#add_btnCanC').click(function(){
		var $this67 = false;
		$('.del_payment').each(function(){
			var $this = $(this);
			if($this.attr('opt_payfor') == "006" || $this.attr('opt_payfor') == "007"){ $this67 = true; }
		});
		
		if($this67){
			var dataToPost = new Object();
			dataToPost.tmbill = $('#add_TMBILL').val();
			
			$('#loadding').fadeIn(200);
			OBJadd_btnCanC = $.ajax({
				url:'../SYS06/RevPayment/getFormNOPAYCancel',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){ if(OBJadd_btnCanC !== null){ OBJadd_btnCanC.abort(); }},
				success: function(data){
					if(data.error){
						console.log(data.errorMessage);
					}else{
						var _formData = "<div class='col-sm-2 form-group'>เลขที่สัญญา<input type='text' readonly class='form-control' value='"+data.CONTNO+"'></div>";
						_formData += "<div class='col-sm-2 form-group'>รหัสลูกค้า<input type='text' readonly class='form-control' value='"+data.CUSCOD+"'></div>";
						_formData += "<div class='col-sm-3 form-group'>ชื่อลูกค้า<input type='text' readonly class='form-control' value='"+data.CUSNAME+"'></div>";
						_formData += "<div class='col-sm-2 form-group'>สาขาสัญญา<input type='text' readonly class='form-control' value='"+data.LOCAT+"'></div>";
						_formData += "<div class='col-sm-3 form-group'>เลขตัวถัง<input type='text' readonly class='form-control' value='"+data.STRNO+"'></div>";
						
						var _formNopayH = "<tr><th></th><th>เลขที่บิล</th><th>วันที่รับชำระ</th><th>ชำระค่า</th><th>สถานะ</th><th>วันที่ยกเลิก</th><th>ยอดตัดลูกหนี้</th><th>ส่วนลด</th><th>เบี้ยปรับ</th><th>ส่วนลดเบี้ยปรับ</th><th>ยอดสุทธิ</th><th>ชำระโดย</th><th>TAXNO</th><th>F_PAR</th><th>F_PAY</th><th>L_PAR</th><th>L_PAY</th></tr>";
						var _formNopay = "";
						for(var i=0;i<data["BILL"].length;i++){
							_formNopay += "<tr class='"+(data["BILL"][i]["FLAG"]=="C"?"text-red":"")+"'>";
							_formNopay += "<td><input type='radio' class='cc_bill' name='nopay' TMBILL='"+data["BILL"][i]["TMBILL"]+"' "+(data["BILL"][i]["TMBILL"] == data["thisTMBILL"] ? "checked":"")+"></td>";
							_formNopay += "<td>"+data["BILL"][i]["TMBILL"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["TMBILDT"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["PAYFOR"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["TSALE"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["CANDT"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["PAYAMT"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["DISCT"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["PAYINT"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["DSCINT"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["NETPAY"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["PAYTYP"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["TAXNO"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["F_PAR"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["F_PAY"]+"</td>";
							_formNopay += "<td>"+data["BILL"][i]["L_PAR"]+"</td>";
							_formNopay += "<td class='text-right'>"+data["BILL"][i]["L_PAY"]+"</td>";
							_formNopay += "</tr>";							
						}
						
						var _formAction = "<div class='col-sm-2 col-sm-offset-8'><button id='' class='btn-block btn-sm btn-primary'>ตาราง</button></div>";
						_formAction += "<div class='col-sm-2'><button id='add_btnCanCNopay' class='btn-block btn-sm btn-danger'>ยกเลิกการรับชำระ</button></div>";
						var _form = "<div class='col-sm-10 col-sm-offset-1'>"+_formData+"</div>";
						_form += "<div class='col-sm-12' style='height:calc(100vh - 200px);overflow:scroll;'><table class='table table-bordered'>"+_formNopayH+_formNopay+"</table></div>";
						_form += "<div class='col-sm-12'>"+_formAction+"</div>";
						
						Lobibox.window({
							title: 'ยกเลิกการรับชำระค่างวด/ตัดสด',
							width: $(window).width(),
							height: $(window).height(),
							content: _form,
							draggable: false,
							closeOnEsc: false,
							onShow: function(lobibox){ $('body').append(jbackdrop); },
							shown: function($this){
								$('#add_btnCanCNopay').click(function(){
									var dataToPost = new Object();
									dataToPost.TMBILL = $('input[name=nopay]:checked').attr('TMBILL');
									dataToPost.action = 'nopay';
									fn_confirm_cancel_payments(dataToPost);
								});
							},
							beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
						});
					}
					
					OBJadd_btnCanC = null; 
					$('#loadding').fadeOut(200);
				}
			});
		}else{
			var dataToPost = new Object();
			dataToPost.TMBILL 	 = $('#add_TMBILL').val();
			dataToPost.TMBILDT 	 = $('#add_TMBILDT').val();
			dataToPost.LOCATRECV = $('#add_LOCATRECV').val();
			dataToPost.PAYTYP	 = $('#add_PAYTYP').val();
			dataToPost.CUSCOD	 = $('#add_CUSCOD').attr('CUSCOD');
			dataToPost.REFNO	 = $('#add_REFNO').val();
			dataToPost.CHQNO	 = $('#add_CHQNO').val();
			dataToPost.CHQDT	 = $('#add_CHQDT').val();
			dataToPost.CHQAMT	 = $('#add_CHQAMT').val();
			dataToPost.CHQBK	 = $('#add_CHQBK').val();
			dataToPost.CHQBR	 = $('#add_CHQBR').val();
			dataToPost.BILLNO	 = $('#add_BILLNO').val();
			dataToPost.BILLDT	 = $('#add_BILLDT').val();
			dataToPost.action 	 = '';
			
			var data_payment = new Array();
			$('.del_payment').each(function(){
				var data_paymentByOne = new Object();
				data_paymentByOne.opt_payfor = ($(this).attr('opt_payfor'));
				data_paymentByOne.opt_contno = ($(this).attr('opt_contno'));
				data_paymentByOne.opt_payamt = ($(this).attr('opt_payamt'));
				data_paymentByOne.opt_disct  = ($(this).attr('opt_disct'));
				data_paymentByOne.opt_payint = ($(this).attr('opt_payint'));
				data_paymentByOne.opt_dscint = ($(this).attr('opt_dscint'));
				data_paymentByOne.opt_netpay = ($(this).attr('opt_netpay'));
				data_payment.push(data_paymentByOne);
			});
			
			dataToPost.data_payment = (data_payment.length == 0 ? new Array():JSON.stringify(data_payment));	
			fn_confirm_cancel_payments(dataToPost);
		}
	});
	
	function fn_confirm_cancel_payments($dataToPost){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			draggable: true,
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: '<span class="text-red">*** กรณียกเลิกบิลรับชำระแล้ว ไม่สามารถนำกลับมาได้อีก</span><br>ยืนยันการทำรายการ ?',
			buttons: {
				ok : {
					'class': 'btn btn-danger glyphicon glyphicon-ok',
					text: ' ยืนยัน ยกเลิกบิลรับชำระ',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-default glyphicon glyphicon-remove',
					text: ' ไว้ทีหลัง',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			//shown: function($this){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){
					$('#loadding').fadeIn(200);
					OBJadd_btnCanC = $.ajax({
						url:'../SYS06/RevPayment/CanCPayments',
						data: $dataToPost,
						type: 'POST',
						dataType: 'json',
						beforeSend: function(){ if(OBJadd_btnCanC !== null){ OBJadd_btnCanC.abort(); }},
						success: function(data){ 
							Lobibox.notify((data.error?"warning":"success"), {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: false,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.errorMessage
							});
							
							OBJadd_btnCanC = null
							$('#loadding').fadeOut(200);
						}
					});
					
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				}else{
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				}
			}
		});
	}
	
	$('#add_btnPrint').click(function(){
		var dataToPost = new Object();
		dataToPost.TMBILL = $('#add_TMBILL').val();
		dataToPost.BILLNO = $('#add_BILLNO').val();
		
		$('#loadding').fadeIn(200);
		OBJadd_btnPrint = $.ajax({
			url:'../SYS06/RevPayment/tmbillFormPrint',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnPrint !== null){ OBJadd_btnPrint.abort(); }},
			success: function(data){
				var OBJprint_screen = null;
				Lobibox.window({
					title: 'พิมพ์ใบเสร็จรับเงิน',
					//width: $(window).width(),
					height: '300',
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						
						$('#print_screen').click(function(){
							var dataToPost = new Object();
							dataToPost.TMBILL    = $('#print_tmbill').val();
							dataToPost.NOPRNTB   = $('#print_tmbill').attr('NOPRNTB');
							dataToPost.BILLNO    = $('#print_billno').val();
							dataToPost.NOPRNBL   = $('#print_billno').attr('NOPRNBL');
							dataToPost.PRINTFOR  = $('input[name=print_type]:checked').val();
							dataToPost.PRINTADDR = $('input[name=print_addr]:checked').val();
							
							OBJprint_screen = $.ajax({
								url:'../SYS06/RevPayment/tmbillPDF',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								beforeSend: function(){ if(OBJprint_screen !== null){ OBJprint_screen.abort(); }},
								success: function(data){
									var OS = data.OS;
									Lobibox.window({
										title: 'พิมพ์เอกสาร',
										//width: $(window).width(),
										//height: '300',
										content: data.html,
										draggable: false,
										closeOnEsc: false,
										onShow: function(lobibox){ $('body').append(jbackdrop); },
										shown: function($this){
											$('#div_print_tm').css({'background-image': 'url("../public/images/watermark.png")'})
											$('#print_tm').click(function(){
												$('#print_tm').attr('disabled',true);
												$('#div_print_tm').css({'background-image': ''});
												var divToPrint = document.getElementById('div_print_tm'); // เลือก div id ที่เราต้องการพิมพ์
												$('#div_print_tm').css({'background-image': 'url("../public/images/watermark.png")'})
												var html =  '<html>'+
															'<head>'+
																'<!-- link href="../public/css/print.css" rel="stylesheet" type="text/css" -->'+
															'</head>'+
																'<body onload="window.print(); window.close();" style="width:'+(OS == "Windows XP"?76:74)+'mm;height:auto;font-size:10pt;padding:'+(OS == "Windows XP"?0:10)+'px;">' + divToPrint.innerHTML + '</body>'+
																'<style> @page  {margin:'+(OS == "Windows XP"?0:10)+';size:portrait;zoom:100%;MozTransform:scale(1.0);} .borderTB { border-top:0.1px solid black;border-bottom:1px solid black; } </style>'+
															'</html>';
												var popupWin = window.open("","_blank","directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=320,height=20,left=0,top=0");
												popupWin.document.open();
												popupWin.document.write(html); //โหลด print.css ให้ทำงานก่อนสั่งพิมพ์
												popupWin.document.close();
												
												popupWin.onafterprint = function(){
													$.ajax({
														url:'../SYS06/RevPayment/Append2Assessment',
														data:{'code':$('#print_tm').attr('code'),'tmbill':$('#print_tmbill').val(),'print_type':$('input[name=print_type]:checked').val()},
														type:'POST'
													});
												}
											});
										},
										beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
									});	
									
									OBJprint_screen = null;
								}
							});
						});
						
					},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
				
				OBJadd_btnPrint = null;
				$('#loadding').fadeOut(200);
			}
		});
	});
	
	$('#add_btnBillFN').click(function(){
		var dataToPost = new Object();
		dataToPost.TMBILL = $('#add_TMBILL').val();
		dataToPost.BILLNO = $('#add_BILLNO').val();
		
		/*
		var url = window.location.protocol
		url	+= '//'+window.location.hostname
		url	+= ':'+window.location.port
		url	+= '/YTKMini/public/images/tmbill_temp/filename.pdf';
		alert(url);
		*/
		
		Lobibox.window({
			title: 'พิมพ์ใบเสร็จรับเงิน',
			width: $(window).width(),
			height: $(window).height(),
			//content: '<iframe id="anpdfFrame" src="#" style="width:100%;height:100%;"></iframe>',
			content:'<iframe src="http://docs.google.com/gview?url=http://localhost:92/YTKMini/public/images/tmbill_temp/filename.pdf&embedded=true" style="width:600px; height:500px;" frameborder="0"></iframe>',
			draggable: false,
			closeOnEsc: false,
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			shown: function($this){
				//$('#anpdfFrame').attr('src','../SYS06/RevPayment/billPDF?TMBILL='+dataToPost.BILLNO);
			},
			beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
		});
	});
	
	$('#add_btnFORMSETAlert').click(function(){
		var dataToPost = new Object();
		
		$('#loadding').fadeIn(200);
		OBJadd_btnFORMSETAlert = $.ajax({
			url:'../SYS06/RevPayment/getFORMSETAlertData',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){ if(OBJadd_btnFORMSETAlert !== null){ OBJadd_btnFORMSETAlert.abort(); }},
			success: function(data){
				var OBJadd_btnSearchAlert = null;
				var OBJadd_btnSETAlert = null;
				
				Lobibox.window({
					title: 'ฟอร์มข้อความแจ้งเตือน',
					width: $(window).width(),
					height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						//$("#alert_locat").attr('disabled',(_level==1?false:true));
						$("#alert_locat").selectpicker();
						
						$('#alert_btnSearch').click(function(){
							var dataToPost = new Object();
							dataToPost.CONTNO = $('#alert_contno').val();
							dataToPost.LOCAT = $('#alert_locat').val();
							dataToPost.SDATE = $('#alert_sdate').val();
							dataToPost.EDATE = $('#alert_edate').val();
							
							$('#loadding').fadeIn(200);
							OBJadd_btnSearchAlert = $.ajax({
								url:'../SYS06/RevPayment/getSearchAlert',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								beforeSend: function(){ if(OBJadd_btnSearchAlert !== null){ OBJadd_btnSearchAlert.abort(); }},
								success: function(data2){
									$('#result_alert').html(data2.html);
									
									OBJadd_btnSearchAlert = null;
									$('#loadding').fadeOut(200);
								}
							});	
						});
						$('#alert_btnSET').click(function(){
							var dataToPost = new Object();
							
							OBJadd_btnSETAlert = $.ajax({
								url:'../SYS06/RevPayment/getSETAlertData',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								beforeSend: function(){ if(OBJadd_btnSETAlert !== null){ OBJadd_btnSETAlert.abort(); }},
								success: function(data){
									var OBJAlert_ae_save = null;
									
									Lobibox.window({
										title: 'บันทึกข้อความเตือน',
										content: data.html,
										draggable: true,
										closeOnEsc: false,
										onShow: function(lobibox){ $('body').append(jbackdrop); },
										shown: function($thisWIN_AlertSAVE){
											
											$('#alert_ae_save').click(function(){
												var dataToPost = new Object();
												dataToPost.CONTNO = $('#alert_ae_contno').val();
												dataToPost.SDATE  = $('#alert_ae_sdate').val();
												dataToPost.EDATE  = $('#alert_ae_edate').val();
												dataToPost.MEMO1  = $('#alert_ae_memo1').val();
												dataToPost.CLAIM  = $("input[name='radioAlert']:checked").val();
												
												OBJAlert_ae_save = $.ajax({
													url:'../SYS06/RevPayment/getAlertSAVE',
													data: dataToPost,
													type: 'POST',
													dataType: 'json',
													beforeSend: function(){ if(OBJAlert_ae_save !== null){ OBJAlert_ae_save.abort(); }},
													success: function(data){
														Lobibox.notify((data.error ? "warning":"success"), {
															title: 'แจ้งเตือน',
															size: 'mini',
															closeOnClick: false,
															delay: 5000,
															pauseDelayOnHover: true,
															continueDelayOnInactiveTab: false,
															icon: true,
															messageHeight: '90vh',
															msg: data.errorMessage
														});
														
														if(!data.error){ $thisWIN_AlertSAVE.destroy(); }
														OBJAlert_ae_save = null;
													}
												});
											});
											
										},
										beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
									});
									
									OBJadd_btnSETAlert = null;
								}
							});	
						});
					},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
				
				OBJadd_btnFORMSETAlert = null;
				$('#loadding').fadeOut(200);
			}
		});
	});
	
	$('#add_btnAlert').click(function(){
		if($('#add_btnAlert').attr('CONTNO') != ""){
			AlertMessage($('#add_btnAlert').attr('CONTNO'),"Y");
		}else{
			Lobibox.notify("warning", {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: 'ยังไม่เลือกสัญญา โปรดเลือกสัญญาที่ต้องการดูรายการแจ้งเตือนก่อนครับ '
			});
		}
	});
	
	$('#add_btnCalC').click(function(){
		var dataToPost = new Object();
		dataToPost.CONTNO = $('#add_btnAlert').attr('CONTNO');
		dataToPost.LOCAT  = $('#add_btnAlert').attr('LOCAT');
		
		fn_OutstandingBalance(dataToPost);
	});
}

function AlertMessage($CONTNO,$SHOW){
	var dataToPost = new Object();
	dataToPost.CONTNO = $CONTNO;
	
	OBJadd_btnAlert = $.ajax({
		url:'../SYS06/RevPayment/getAlertData',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		beforeSend: function(){ if(OBJadd_btnAlert !== null){ OBJadd_btnAlert.abort(); }},
		success: function(data){
			if(data.SHOW == "NULL"){
				if($SHOW == "Y"){
					Lobibox.window({
						title: 'ข้อความเตือน',
						height: '200px',
						content: data.html,
						draggable: false,
						closeOnEsc: false,
						onShow: function(lobibox){ $('body').append(jbackdrop); },
						shown: function($this){},
						beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
					});
				}
			}else{
				Lobibox.window({
					title: 'ข้อความเตือน',
					height: '200px',
					content: data.html,
					draggable: true,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){},
					beforeClose : function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
				});
			}
			
			OBJadd_btnAlert = null;
			$('#loadding').fadeOut(200);
		}
	});
}

function fn_OutstandingBalance(dataToPost){
	$('#loadding').fadeIn(200);
	OBJadd_CONTNO_detail = $.ajax({
		url:'../SYS06/RevPayment/getOutstandingBalance',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		beforeSend: function(){ if(OBJadd_CONTNO_detail !== null){ OBJadd_CONTNO_detail.abort(); }},
		success: function(data){
			
			Lobibox.window({
				title: 'รายละเอียดการค้างชำระ',
				width: '700',
				//height: $(window).height(),
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				onShow: function(lobibox){ $('body').append(jbackdrop); },
				shown: function($thisCUS){
					$('#loadding').fadeOut(200);
					
					$('#btn_FCD').click(function(){
						dataToPost.DATESEARCH = $('#add_TMBILDT').val();
						$('#loadding').fadeIn(200);
						$.ajax({
							url:'../SYS06/ReportFinance/getfromDiscount',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							beforeSend: function(){ if(OBJadd_CONTNO_detail !== null){ OBJadd_CONTNO_detail.abort(); }},
							success: function(data){
								Lobibox.window({
									title: 'รายละเอียดส่วนลดตัดสด',
									width: '840',
									//height: $(window).height(),
									content: data.html,
									draggable: true,
									closeOnEsc: false,
									onShow: function(lobibox){ $('body').append(jbackdrop); },
									shown: function($this){
										$('#loadding').fadeOut(200);
										
										$('#btnprint_account').click(function(){
											$('#btnprint_account').attr('disabled',true);		
											var baseUrl = $('body').attr('baseUrl');
											var url = baseUrl+'SYS06/ReportFinance/printaccountpdf?cond='+$("#add_CONTNO").val()+'||undefined';
											var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
											Lobibox.window({
												title: 'พิมพ์ใบแจ้งเบี้ยปรับ',
												width: $(window).width(),
												height: $(window).height(),
												content: content,
												draggable: false,
												closeOnEsc: true,			
												beforeClose : function(){
													$('#btnprint_account').attr('disabled',false);
												}
											});
										});
										
										//btnprint_customer--
										$('#btnprint_customer').click(function(){
											$('#btnprint_customer').attr('disabled',true);		
											var baseUrl = $('body').attr('baseUrl');
											var url = baseUrl+'SYS06/ReportFinance/printcustomerpdf?cond='+$("#add_CONTNO").val()+'||undefined';
											var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
											Lobibox.window({
												title: 'พิมพ์ใบแจ้งเบี้ยปรับ',
												width: $(window).width(),
												height: $(window).height(),
												content: content,
												draggable: false,
												closeOnEsc: true,			
												beforeClose : function(){
													$('#btnprint_customer').attr('disabled',false);
												}
											});
										});	
									},
									beforeClose: function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
								});
							}
						});
					});
					
					$('#btn_FinePayment').click(function(){
						dataToPost.DATESEARCH = $('#add_TMBILDT').val();
						$('#loadding').fadeIn(200);
						$.ajax({
							url:'../SYS06/ReportFinance/getfromPayment',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							beforeSend: function(){ if(OBJadd_CONTNO_detail !== null){ OBJadd_CONTNO_detail.abort(); }},
							success: function(data){
								Lobibox.window({
									title: 'แสดงยอดเบี้ยปรับและยอดชำระ',
									width: '840',
									//height: $(window).height(),
									content: (data.error ? data.msg:data.html),
									draggable: true,
									closeOnEsc: false,
									onShow: function(lobibox){ $('body').append(jbackdrop); },
									shown: function($this){
										$('#loadding').fadeOut(200);
										
										$('#btnprint_penalty').click(function(){
											$('#btnprint_penalty').attr('disabled',true);		
											var baseUrl = $('body').attr('baseUrl');
											var url = baseUrl+'SYS06/ReportFinance/printpenaltypdf?cond='+$("#add_CONTNO").val()+'||undefined';
											var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
											Lobibox.window({
												title: 'พิมพ์ใบแจ้งเบี้ยปรับ',
												width: $(window).width(),
												height: $(window).height(),
												content: content,
												draggable: false,
												closeOnEsc: true,			
												beforeClose : function(){
													$('#btnprint_penalty').attr('disabled',false);
												}
											});
										});
									},
									beforeClose: function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
								});
							}
						});
					});
				},
				beforeClose: function(){ $('.jbackdrop')[($('.jbackdrop').length)-1].remove(); }
			});
			
			OBJadd_CONTNO_detail = null;
		}
	});
}





















