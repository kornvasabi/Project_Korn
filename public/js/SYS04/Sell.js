/********************************************************
             ______@17/04/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
"use strict";
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

$(function(){
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_cuscod').find(':selected').val();
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
});

var jd_btnt1search = null;
var jd_sellDetails = null;
var jd_loadSell = null;
$('#btnt1search').click(function(){
	var dataToPost = new Object();
	dataToPost.contno 	= $('#CONTNO').val();
	dataToPost.sdatefrm = $('#SDATEFRM').val();
	dataToPost.sdateto 	= $('#SDATETO').val();
	dataToPost.locat 	= $('#LOCAT').val();
	dataToPost.strno 	= $('#STRNO').val();
	dataToPost.cuscod 	= (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? '' : $('#CUSCOD').find(':selected').val());
	
	$('#jd_result').html('');
	$('#loadding').fadeIn(200);
	jd_btnt1search = $.ajax({
		url:'../SYS04/Sell/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			
			$('#jd_result').html(data.html);
			$('#table-sellCar').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-sellCar',1,250);
			$('.data-export').prepend('<img id="table-sellCar-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-sellCar-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ขายสด","sell"); 
			});
			
			function redraw(){
				$('.sellDetails').click(function(){
					sellDetails($(this).attr('contno'),'search');
				});
			}
			
			jd_btnt1search = null;
		},
		beforeSend: function(){
			if(jd_btnt1search !== null){ jd_btnt1search.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function sellDetails($contno,$event){
	var dataToPost = new Object();
	dataToPost.contno = $contno;
	
	$('#loadding').show();
	jd_sellDetails = $.ajax({
		url:'../SYS04/Sell/loadSell',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			//load form leasing
			loadSell(data);
			jd_sellDetails = null;
		},
		beforeSend: function(){
			if(jd_sellDetails !== null){ jd_sellDetails.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function loadSell($param){
	$('#loadding').show();
	jd_loadSell = $.ajax({
		url:'../SYS04/Sell/getfromSell',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					wizard('old',$param,$this);
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});
			
			jd_loadSell = null;
		},
		beforeSend: function(){
			if(jd_loadSell !== null){ jd_loadSell.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

var jd_btnt1sell = null;
$('#btnt1sell').click(function(){
	$('#btnt1sell').attr('disabled',true);
	$('#loadding').show();
	jd_btnt1sell = $.ajax({
		url:'../SYS04/Sell/getfromSell',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการขายสด',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard('new','',$this);
				},
				beforeClose : function(){
					$('#btnt1sell').attr('disabled',false);
				}
			});	

			jd_btnt1sell = null;	
		},
		beforeSend: function(){
			if(jd_btnt1sell !== null){ jd_btnt1sell.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function wizard($param,$dataLoad,$thisWindowLeasing){
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	
	function initPage(){
		$('#wizard-sell').bootstrapWizard({
			onTabClick: function(li, ul, ind, ind2, xxx){
				var beforeChanged = 0; 
				var index = 0; //tab ก่อนเปลี่ยน 
				$('.wizard-tabs li').each(function(){
					//ลบ wizard ที่ active อยู่ทั้งหมด
					if($(this).hasClass('active')){
						index = beforeChanged;
					}
					
					beforeChanged = beforeChanged + 1;
				});
				
				var sdate 		= $('#add_sdate').val();
				var cuscod		= $('#add_cuscod').attr('CUSCOD');
				var cuscodaddr 	= (typeof $('#add_addrno').find(':selected').val() === 'undefined' ? '' : $('#add_addrno').find(':selected').val());
				var strno 		= (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
				var paydue 		= (typeof $('#add_paydue').find(':selected').val() === 'undefined' ? '' : $('#add_paydue').find(':selected').val());
				
				switch(index){
					case 0: //tab1
						var msg = "";
						
						if(paydue 		== ''){ msg = "ไม่พบวิธีชำระค่างวด โปรดระบุวิธีชำระค่างวดก่อนครับ"; }
						if(strno 		== ''){ msg = "ไม่พบเลขตัวถัง โปรดระบุเลขตัวถังก่อนครับ"; }
						if(cuscodaddr 	== ''){ msg = "ไม่พบที่อยู่ในการพิมพ์สัญญา โปรดระบุที่อยู่ในการพิมพ์สัญญาก่อนครับ"; }
						if(cuscod 		== ''){ msg = "ไม่พบรหัสลูกค้า โปรดระบุรหัสลูกค้าก่อนครับ"; }
						if(sdate 		== ''){ msg = "ไม่พบวันที่ขาย โปรดระบุวันที่ขายก่อนครับ"; }
						
						if(msg != ""){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: msg
							});
							
							return false;
						}else{ 
							nextTab(ind2); 
						}
						
						break;
					case 1: //tab2						
						nextTab(ind2); 
						break;
					case 2: //tab3
						nextTab(ind2); 
						break;					
				}
			}
		});
	}
	
	function nextTab(ind2){
		$('.wizard-tabs li').each(function(){
			//ลบ wizard ที่ active อยู่ทั้งหมด
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(ind2).hasClass('active')){
			// active tab ถัดไป
			$('.wizard-tabs li').eq(ind2).addClass('active');
		}
		
		var $id = $('.wizard-tabs li').eq(ind2).find('a').attr('href').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');	
		
		return true;					
	}
	
	$('#add_contno').val('Auto Genarate');
	$('#add_contno').attr('readonly',true);
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
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
		dropdownParent: (_level == 1 ? $("#wizard-sell") : true),
		disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_resvno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getRESVNO',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_resvno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
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
		allowClear: true,
		multiple: false,
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_cuscod').click(function(){
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url:'../Cselect2/getfromCUSTOMER',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#add_cuscod').attr('disabled',true);
				$('#add_save').attr('disabled',true);
				
				Lobibox.window({
					title: 'FORM CUSTOMER',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($thisCUS){
						var jd_cus_search = null;
						$('#cus_fname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_lname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_idno').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_search').click(function(){ fnResultCUSTOMER(); });
						
						function fnResultCUSTOMER(){
							data = new Object();
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
										
										$('#add_cuscod').attr('CUSCOD',dtp.cuscod);
										$('#add_cuscod').val(dtp.cusname);
										
										var newOption = new Option(dtp.addrdes, dtp.addrno, true, true);
										//$('#add_addrno').attr('disabled',true);
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
						$('#add_cuscod').attr('disabled',false);
						$('#add_save').attr('disabled',false);
					}
				});
				
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_cuscod_removed').click(function(){
		$('#add_cuscod').attr('CUSCOD','');
		$('#add_cuscod').val('');
	});
	
	$('#add_inclvat').select2({ 
		dropdownParent: $("#wizard-sell"), 
		minimumResultsForSearch: -1,
		width: '100%'
	});
	
	$('#add_addrno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_addrno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = $('#add_cuscod').attr('CUSCOD');
				
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_strno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_paydue').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_paydue').find(':selected').val();
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var jd_add_strno = null;
	$('#add_resvno').on('select2:select',function(){ fn_checkStd(); });
	$('#add_strno').on('select2:select',function(){ fn_checkStd(); });
	$('#add_acticod').on('select2:select',function(){ fn_checkStd(); });
	function fn_checkStd(){
		var dataToPost = new Object();
		dataToPost.locat 	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
		dataToPost.resvno 	= (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '' : $('#add_resvno').find(':selected').val());
		dataToPost.strno 	= (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
		dataToPost.sdate 	= $('#add_sdate').val();
		dataToPost.acticod 	= (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '' : $('#add_acticod').find(':selected').val());
		
		jd_add_strno = $.ajax({
			url:'../SYS04/Sell/checkStandart',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if(data.error){
					Lobibox.notify('warning', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: true,
						delay: false,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$('#add_stdprc').val(data.html.PRICE);
					$('#add_inprc').val(data.html.PRICE);
				}
				
				
				jd_add_strno = null;
			},
			beforeSend: function(){ if(jd_add_strno !== null){ jd_add_strno.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
	
	$('#add_resvno').on('select2:select', function (e) {	
		var dataToPost = new Object();
		dataToPost.resvno = (typeof $(this).find(':selected').val() === 'undefined' ? '' : $(this).find(':selected').val());
		dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
		
		$.ajax({
			url:'../SYS04/Leasing/resvnoChanged',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if(data.SMCHQ > 0 || data.msg != ""){
					$('#add_cuscod').attr('CUSCOD','');
					$('#add_cuscod').val('');
					$('#add_addrno').empty().trigger('change');
					
					$('#add_strno').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					$('#add_strno').empty().trigger('change');
					
					Lobibox.notify('error', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: false,
						delay: false,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$('#add_cuscod').attr('CUSCOD',data.CUSCOD);
					$('#add_cuscod').attr('disabled',true);
					$('#add_cuscod').val(data.CUSNAME);
					//$('#add_addrno').empty().trigger('change');
					var newOption = new Option(data.ADDRDES, data.ADDRNO, true, true);
					$('#add_addrno').empty().append(newOption).trigger('change');
					
					$('#add_strno').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					var newOption = new Option(data.STRNO, data.STRNO, true, true);
					$('#add_strno').empty().append(newOption).trigger('change');
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_resvno').on('select2:unselect', function (e) {
		$('#add_cuscod').attr('CUSCOD','');
		$('#add_cuscod').attr('disabled',false);
		$('#add_cuscod').val('');
		$('#add_addrno').empty().trigger('change');
		
		$('#add_strno').select2({ 
			placeholder: 'เลือก',
			ajax: {
				url: '../Cselect2/getSTRNO',
				data: function (params) {
					var dataToPost = new Object();
					dataToPost.now = $('#add_strno').find(':selected').val();
					dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
					dataToPost.locat = $('#add_locat').find(':selected').val();
					
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
			dropdownParent: $("#wizard-sell"),
			disabled: false,
			//theme: 'classic',
			width: '100%'
		});
		$('#add_strno').empty().trigger('change');
	});
	
	document.getElementById("dataTable-fixed-inopt").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	$('#add_inopt').click(function(){
		$('#add_inopt').attr('disabled',true);
		
		$('#loadding').show();
		
		$.ajax({
			url: '../SYS04/Leasing/getFormInopt',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').hide();
				Lobibox.window({
					title: 'เพิ่มอุปกรณ์เสริม',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					shown: function($this){
						//$this.destroy();		
						$('#op_code').select2({ 
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getOPTMAST',
								data: function (params) {
									var dataToPost = new Object();
									dataToPost.now = $('#op_code').find(':selected').val();
									dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
									dataToPost.locat = $('#add_locat').find(':selected').val();
									
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
							dropdownParent: $("#inoptform"),
							//disabled: true,
							//theme: 'classic',
							width: '100%'
						});
						
						$('#receipt_inopt').hide();
						$('#cal_inopt').click(function(){
							var dataToPost = new Object();
							dataToPost.qty 	   = $('#op_qty').val();
							dataToPost.uprice  = $('#op_uprice').val();
							dataToPost.cvt     = $('#op_cvt').val();
							dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '' : $('#add_inclvat').find(':selected').val());
							dataToPost.vatrt   = $('#add_vatrt').val();
							dataToPost.opCode  = (typeof $('#op_code').find(':selected').val() === 'undefined' ? '' : $('#op_code').find(':selected').val());
							dataToPost.opText  = (typeof $('#op_code').find(':selected').text() === 'undefined' ? '' : $('#op_code').find(':selected').text());
							
							$.ajax({
								url: '../SYS04/Leasing/calculate_inopt',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									if(data.status){
										$('#receipt_inopt').attr({
											'price1' : data["1price"].replace(',','')
											,'vat1'  : data["1vat"].replace(',','')
											,'total1': data["1total"].replace(',','')
											,'price2': data["2price"].replace(',','')
											,'vat2'  : data["2vat"].replace(',','')
											,'total2': data["2total"].replace(',','')
											,'opCode': $('#op_code').find(':selected').val()
											,'opText': $('#op_code').find(':selected').text()
											,'qty'  : data["qty"].replace(',','')
											,'uprice': data["uprice"].replace(',','')
										});
										
										$('#inopt_results').html(data.html);
										$('#receipt_inopt').show();
									}else{
										$('#inopt_results').html(data.html);
										$('#receipt_inopt').hide();
									}
								},
								error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
							});
						});
						
						$('#receipt_inopt').click(function(){
							var opCode = $(this).attr('opCode');
							var opText = $(this).attr('opText');
							var price1 = $(this).attr('price1');
							var vat1   = $(this).attr('vat1');
							var total1 = $(this).attr('total1');
							var price2 = $(this).attr('price2');
							var vat2   = $(this).attr('vat2');
							var total2 = $(this).attr('total2');
							var qty	   = $(this).attr('qty');
							var uprice = $(this).attr('uprice');
							
							var stat = true;
							$('.inoptTab2').each(function(){
								if(opCode == $(this).attr('opCode')){
									stat = false;
								}
							});
							
							if(stat){
								var row = '<tr seq="new">';							
								row += "<td align='center'> ";
								row += "	<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
								row += "		opCode='"+opCode+"' total1='"+total1+"' total2='"+total2+"' ";
								row += "		price1='"+price1+"' price2='"+price2+"' vat1='"+vat1+"' ";
								row += "		vat2='"+vat2+"' qty='"+qty+"' uprice='"+uprice+"' ";
								row += "		style='cursor:pointer;'> ลบ   ";
								row += "	</i> ";
								row += "</td> ";
								row += "<td>"+opText+"</td>";
								row += "<td class='text-right'>"+uprice.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+qty.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+price1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+vat1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+total1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+price2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+vat2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+total2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += '</tr>';
								
								$('#dataTables-inopt tbody').append(row);
								
								var sumTotal1 = 0;
								var sumTotal2 = 0;
								$('.inoptTab2').each(function(){
									sumTotal1 += parseFloat($(this).attr('total1'));
									sumTotal2 += parseFloat($(this).attr('total2'));
								});
															
								$('#add2_optsell').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
								$('#add2_optcost').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
								
								inopt_remove();
								$this.destroy();
							}else{
								Lobibox.notify('warning', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: 'ผิดพลาดรหัสอุปกรณ์เสริม '+opCode+' มีอยู่แล้ว ไม่สามารถเพิ่มซ้ำได้ครับ'
								});
							}
						});
					},
					beforeClose : function(){
						$('#add_inopt').attr('disabled',false);
					}
				});
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	
	$('#add_salcod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_salcod').find(':selected').val();
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	/*
	$('#add_recomcod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_recomcod').find(':selected').val();
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	*/
	
	$('#add_recomcod').click(function(){
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url:'../Cselect2/getfromCUSTOMER',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#add_cuscod').attr('disabled',true);
				$('#add_recomcod').attr('disabled',true);
				$('#add_save').attr('disabled',true);
				
				Lobibox.window({
					title: 'FORM CUSTOMER',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($thisCUS){
						var jd_cus_search = null;
						$('#cus_fname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_lname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_idno').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_search').click(function(){ fnResultCUSTOMER(); });
						
						function fnResultCUSTOMER(){
							data = new Object();
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
										
										$('#add_recomcod').attr('CUSCOD',dtp.cuscod);
										$('#add_recomcod').val(dtp.cusname);										
										
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
						$('#add_cuscod').attr('disabled',false);
						$('#add_recomcod').attr('disabled',false);
						$('#add_save').attr('disabled',false);
					}
				});
				
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_recomcod_removed').click(function(){
		$('#add_recomcod').attr('CUSCOD','');
		$('#add_recomcod').val('');
	});
	
	$('#add_acticod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#add_acticod').find(':selected').val();
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
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#btn_addBillDas').click(function(){
		var data = false;
		$('.add_billdas').each(function(){
			if(typeof $(this).find(':selected').val() === 'undefined'){
				data = true;
			}
		});
		
		if(data){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 15000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
				soundExt: '.ogg',
				icon: true,
				messageHeight: '90vh',
				msg: 'ไม่สามารถดึงบิลเพิ่มได้<br>เนื่องจากคุณยังมีช่องที่ไม่ได้เลือกบิลอยู่ครับ'
			});
		}else{
			var rank = 'in'+$('.add_billdas').length;	
			var billdas = "<select class='add_billdas form-control input-sm chosen-select' process='' rank='"+rank+"' data-placeholder='เลขที่บิล'></select>";
			$('#formBillDas').append(billdas);
			
			fn_billdasActive(rank);
		}
	});
	
	var jd_add_save = null;
	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกการขายสดหรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: false,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
					dataToPost.contno 	= $('#add_contno').val();
					dataToPost.locat 	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val() );
					dataToPost.sdate 	= $('#add_sdate').val();
					dataToPost.resvno 	= (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '':$('#add_resvno').find(':selected').val() );
					dataToPost.approve 	= $('#add_approve').val();
					dataToPost.cuscod 	= $('#add_cuscod').attr('CUSCOD');
					dataToPost.inclvat 	= (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '':$('#add_inclvat').find(':selected').val() );
					dataToPost.vatrt  	= $('#add_vatrt').val();
					dataToPost.addrno 	= $('#add_addrno').val();
					dataToPost.strno 	= $('#add_strno').val();
					dataToPost.reg 		= $('#add_reg').val();
					dataToPost.paydue 	= $('#add_paydue').val();
					
					var inopt = [];
					$('.inoptTab2').each(function(){
						var data = [];
						data.push($(this).attr('opCode'));
						data.push($(this).attr('uprice'));
						data.push($(this).attr('qty'));
						data.push($(this).attr('price1'));
						data.push($(this).attr('vat1'));
						data.push($(this).attr('total1'));
						data.push($(this).attr('price2'));
						data.push($(this).attr('vat2'));
						data.push($(this).attr('total2'));
						data.push($('#add2_optcost').val());
						data.push($('#add2_optsell').val());
						inopt.push(data);
					});
					
					dataToPost.inopt = inopt;
					
					dataToPost.stdprc 	= $('#add_stdprc').val();
					dataToPost.inprc 	= $('#add_inprc').val();
					dataToPost.dwninv 	= $('#add_dwninv').val();
					dataToPost.dwninvDt = $('#add_dwninvDt').val();
					dataToPost.credtm 	= $('#add_credtm').val();
					dataToPost.duedt 	= $('#add_duedt').val();
					dataToPost.salcod	= (typeof $('#add_salcod').find(':selected').val() === 'undefined' ? '':$('#add_salcod').find(':selected').val() );
					dataToPost.comitn 	= $('#add_comitn').val();
					dataToPost.issuno 	= $('#add_issuno').val();
					dataToPost.issudt 	= $('#add_issudt').val();
					
					dataToPost.recomcod = $('#add_recomcod').attr('CUSCOD');
					dataToPost.acticod 	= (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '':$('#add_acticod').find(':selected').val() );
					dataToPost.commission = $('#add_commission').val();
					dataToPost.free 	= $('#add_free').val();
					dataToPost.payother = $('#add_payother').val();
					dataToPost.crdtxno 	= $('#add_crdtxno').val();
					dataToPost.crdamt	= $('#add_crdamt').val();
					dataToPost.memo1 	= $('#add_memo1').val();
					
					var billdas = [];
					$('.add_billdas').each(function(){
						billdas.push($(this).find(':selected').val());
					});		
					dataToPost.billdas = billdas;
					
					$('#loadding').fadeIn(200);
					jd_add_save = $.ajax({
						url:'../SYS04/Sell/save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							if(data.error){
								Lobibox.notify('warning', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else{
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
								
								$thisWindowLeasing.destroy();
							}
							
							jd_add_save = null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(jd_add_save !== null){ jd_add_save.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
					
					lobibox.destroy();
				}
			}
		});
	});
	
	if($param == 'old'){
		//เอาข้อมูลที่โหลดมาแสดง
		permission($dataLoad,$thisWindowLeasing);
	}
}

function inopt_remove(){
	$('.inoptTab2').click(function(){
		$(this).parents().closest('tr').remove(); 
		
		var sumTotal1 = 0;
		var sumTotal2 = 0;
		$('.inoptTab2').each(function(){
			sumTotal1 += parseFloat($(this).attr('total1'));
			sumTotal2 += parseFloat($(this).attr('total2'));
		});
									
		$('#add2_optsell').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
		$('#add2_optcost').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
	});
}


function fn_billdasActive(rank){
	$('.add_billdas[rank='+rank+']').select2({ 
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getBILLDAS',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $(this).find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);				
				
				dataToPost.locat = $('#add_locat').find(':selected').val();
				dataToPost.sdate = $('#add_sdate').val();
				
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
		allowClear: true,
		multiple: false,
		dropdownParent: $("#wizard-sell"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:opening', function (e) {
		$(this).attr('process','use');
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:select', function(e){
		var thisData = $(this).find(':selected').val();
		
		if(typeof thisData !== 'undefined' && thisData != ''){
			var status = false;
			
			$('.add_billdas').each(function(){
				var process = $(this).attr('process');
				var this2Data = $(this).find(':selected').val();
				
				if(process == ""){
					if(this2Data == thisData){
						status = true;
					}
				}
			});
			
			if(status){					
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 15000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
					soundExt: '.ogg',
					icon: true,
					messageHeight: '90vh',
					msg: 'ไม่สามารถเลือกบิลซ้ำได้ครับ'
				});					
				$(this).val(null).trigger("change");
			}
		}
		
		$(this).attr('process','');
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:close', function(e){
		if(typeof $(this).find(':selected').val() === 'undefined'){
			//alert('undi');
			var size = 0;
			$('.add_billdas').each(function(){ if(typeof $(this).find(':selected').val() === 'undefined'){ size += 1; } });
		
			$('.add_billdas').each(function(){								
				if(size > 1){
					if(typeof $(this).find(':selected').val() === 'undefined'){ 
						$(this).select2('destroy');
						$(this).remove();
						size -= 1;
					}
				}
			});
		}
		
		fn_calbilldas();
	});
}

function permission($dataLoad,$thisWindowLeasing){
	$('#add_resvno').unbind('change'); //เพื่อไม่ให้ชื่อลูกค้า กับเลขที่สัญญาเปลี่ยน
	$('#add_cuscod').unbind('change'); //เพื่อแสดงข้อมูลการจองของลูกค้าคนนี้ กรณี ไม่ได้ใช้ใบจอง
	
	/*tab1*/
	$('#add_contno').val($dataLoad.CONTNO);
	var newOption = new Option($dataLoad.LOCAT, $dataLoad.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_sdate').val($dataLoad.SDATE);
	var newOption = new Option($dataLoad.RESVNO, $dataLoad.RESVNO, true, true);
	$('#add_resvno').empty().append(newOption).trigger('change');
	$('#add_approve').val($dataLoad.APPVNO);
	//var newOption = new Option($dataLoad.CUSNAME, $dataLoad.CUSCOD, true, true);
	//$('#add_cuscod').empty().append(newOption).trigger('change');
	$('#add_cuscod').attr('CUSCOD',$dataLoad.CUSCOD);
	$('#add_cuscod').attr('disabled',true);
	$('#add_cuscod').val($dataLoad.CUSNAME);
					
	$('#add_inclvat').val($dataLoad.INCLVAT).trigger('change');
	$('#add_vatrt').val($dataLoad.VATRT);
	var newOption = new Option($dataLoad.ADDRDetail, $dataLoad.ADDRNO, true, true);
	$('#add_addrno').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.STRNO, $dataLoad.STRNO, true, true);
	$('#add_strno').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.PAYDESC, $dataLoad.PAYTYP, true, true);
	$('#add_paydue').empty().append(newOption).trigger('change');
	/*tab2*/
	$('#add_inopt').attr('disabled',true);
	$('#dataTables-inopt tbody').empty().append($dataLoad.option);
	$('#add2_optcost').val($dataLoad.OPTCTOT);
	$('#add2_optsell').val($dataLoad.OPTPTOT);
	$('#add_stdprc').val($dataLoad.STDPRC);
	$('#add_inprc').val($dataLoad.KEYIN);
	$('#add_dwninv').val($dataLoad.TAXNO);
	$('#add_dwninvDt').val($dataLoad.TAXDT);
	$('#add_credtm').val($dataLoad.CREDTM);
	$('#add_duedt').val($dataLoad.DUEDT);
	var newOption = new Option($dataLoad.SALNAME, $dataLoad.SALCOD, true, true);
	$('#add_salcod').empty().append(newOption).trigger('change');
	$('#add_comitn').val($dataLoad.COMITN);
	$('#add_issuno').val($dataLoad.ISSUNO);
	$('#add_issudt').val($dataLoad.ISSUDT);
	
	/*tab3*/
	var newOption = new Option($dataLoad.RECOMNAME, $dataLoad.RECOMCOD, true, true);
	$('#add_recomcod').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.ACTINAME, $dataLoad.ACTICOD, true, true);
	$('#add_acticod').empty().append(newOption).trigger('change');
	$('#add_commission').val($dataLoad.COMEXT);
	$('#add_free').val($dataLoad.COMOPT);
	$('#add_payother').val($dataLoad.COMOTH);
	$('#add_crdtxno').val($dataLoad.CRDTXNO);
	$('#add_crdamt').val($dataLoad.CRDAMT);
	
	var billDas = (typeof $dataLoad.billDAS === 'undefined' ? [] : $dataLoad.billDAS);
	for($i=0;$i<billDas.length;$i++){
		var billdas = "<select class='add_billdas form-control input-sm chosen-select' process='' rank='"+$i+"' data-placeholder='เลขที่บิล'><option value='"+billDas[$i]+"'>"+billDas[$i]+"</option></select>";
		$('#formBillDas').append(billdas);
		
		fn_billdasActive($i);
	}
	$('#add_memo1').val($dataLoad.MEMO1);
	
	
	/******************************************************************
		Enabled,Disabled  or Other 
	*******************************************************************/
	$('#add_contno').attr('readonly',true);
	$('#add_contno').css({'color':'red'});
	$('#add_locat').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_sdate').attr('disabled',true);
	$('#add_resvno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_approve').attr('disabled',true);
	
	$('#add_cuscod').attr('disabled',true);
	$('#add_cuscod_removed').attr('disabled',true);
	$('#add_inclvat').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_vatrt').attr('disabled',true);
	$('#add_addrno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_strno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_reg').attr('disabled',true);
	
	$('#add_stdprc').attr('disabled',true);
	$('#add_inprc').attr('disabled',true);
	//$('#add_inprcCal').unbind('click');
	$('#add_dwninv').attr('disabled',true);
	$('#add_dwninvDt').attr('disabled',true);
	$('#add_credtm').attr('disabled',true);
	$('#add_duedt').attr('disabled',true);
	
	$('#add_crdtxno').attr('disabled',true);
	$('#add_crdamt').attr('disabled',true);
	
	
	if(_update == 'T'){
		$('#add_save').attr('disabled',false);
	}else{
		$('#add_paydue').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_salcod').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_comitn').attr('disabled',true);
		$('#add_issuno').attr('disabled',true);
		$('#add_issudt').attr('disabled',true);
		$('#add_recomcod').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_acticod').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_commission').attr('disabled',true);
		$('#add_free').attr('disabled',true);
		$('#add_payother').attr('disabled',true);
		$('#add_memo1').attr('disabled',true);
		$('#btn_addBillDas').attr('disabled',true);
		$('.add_billdas').attr('disabled',true);
		$('#add_save').attr('disabled',true);
	}
	
	if(_delete == 'T'){
		$('#add_delete').attr('disabled',false);
	}else{
		$('#add_delete').attr('disabled',true);
	}
	
	$('#btnTax').attr('disabled',false);
	$('#btnSend').attr('disabled',false);
	$('#btnApproveSell').attr('disabled',false);
	
	__decss(); //load script css in VIEW disabled and enabled.
	
	btnOther($thisWindowLeasing);
}

function btnOther($thisWindowLeasing){
	$('#add_delete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการ<span style="color:red;">ลบเลขที่สัญญา</span> '+$('#add_contno').val()+' หรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ลบ',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
					dataToPost.contno = $('#add_contno').val();
					
					$('#loadding').show();
					
					$.ajax({
						url:'../SYS04/Sell/deleteContno',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							$('#loadding').hide();
							
							if(data.status == 'S'){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
								
								$thisWindowLeasing.destroy();
							}else{
								Lobibox.notify('error', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
						},
						error: function(){
							$('#loadding').hide();
						}
					});
				}
			}
		});
	});
	
	$contno = $('#add_contno').val();
	$('#btnDOSend').click(function(){
		documents('ใบส่งมอบสินค้า');
	});
	$('#btnDOSendTax').click(function(){
		documents('ใบส่งของ / ใบกำกับภาษี');
	});
	
	function documents($type){
		var baseUrl = $('body').attr('baseUrl');
		var url = baseUrl+'SYS04/Agent/sendpdf?contno='+$contno+'&document='+$type;
		var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
		
		Lobibox.window({
			title: $type,
			width: $(window).width(),
			height: $(window).height(),
			content: content,
			draggable: false,
			closeOnEsc: true,			
			beforeClose : function(){
				$('#btnApproveSell').attr('disabled',false);
			}
		});
	}
	
	
	$('#btnApproveSell').click(function(){
		$('#btnApproveSell').attr('disabled',true);		
		
		var baseUrl = $('body').attr('baseUrl');
		var url = baseUrl+'SYS04/Sell/approvepdf?contno='+$("#add_contno").val();
		var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
		
		Lobibox.window({
			title: 'ใบอนุมัติขาย',
			width: $(window).width(),
			height: $(window).height(),
			content: content,
			draggable: false,
			closeOnEsc: true,			
			beforeClose : function(){
				$('#btnApproveSell').attr('disabled',false);
			}
		});
	});
}

function fn_calbilldas(){
	var saleno = new Array();
	$('.add_billdas').each(function(){
		if(typeof $(this).find(':selected').val() !== 'undefined'){
			saleno.push($(this).find(':selected').val());
		}
	});	
	
	if(saleno.length > 0){
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/calbilldas',
			data: {saleno:saleno,locat:(typeof $("#add_locat").find(':selected').val() === 'undefined' ? '' : $("#add_locat").find(':selected').val())},
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#add_free').val(data.TotalAmt);
				
				var comment = $('#add_memo1').val().split("\n");
				$('#add_memo1').val(data.Details+"\n"+(typeof comment[1] === 'undefined' ? '' : comment[1]));
				$('#loadding').hide();
			}
		})
	}else{
		$('#add_free').val('0.00');
		var comment = $('#add_memo1').val().split("\n");
		$('#add_memo1').val((typeof comment[1] === 'undefined' ? '' : "\n"+comment[1]));
	}
}





















