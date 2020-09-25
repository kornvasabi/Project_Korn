/********************************************************
             ______@17/04/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

var kb_btnt1search = null;
$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.contno   = $('#CONTNO').val();
	dataToPost.sdatefrm = $('#SDATEFRM').val();
	dataToPost.sdateto  = $('#SDATETO').val();
	dataToPost.locat    = $('#LOCAT').val();
	dataToPost.strno    = $('#STRNO').val();
	dataToPost.name     = $('#NAME').val();
	$('#loadding').fadeIn(200);
	kb_btnt1search = $.ajax({
		url:'../SYS04/Finance/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			
			$('#searchresult').html(data.html);
			
			$('#table-Finance').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-Finance',1,225);
			
			$('.dataTables_scrollBody').css({'height':'calc(-370px + 100vh)'});
			
			function redraw(){
				$('[data-toggle="tooltip"]').tooltip();
				$('.financeDetails').unbind('click');
				$('.financeDetails').click(function(){
					//loadDetails('edit','');
				});
			}
			
			kb_btnt1search = null;
		},
		beforeSend: function(){
			if(kb_btnt1search !== null){ kb_btnt1search.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});
$('#btnt1finance').click(function(){
	loadFormFinance('add','');
});
var jd_btnt1finance = null;
function loadFormFinance($param,$dataLoad){
	$('#btnt1finance').attr('disabled',true);
	$('#loadding').show();
	jd_btnt1finance = $.ajax({
		url:'../SYS04/Finance/getfromFinance',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการขายส่งไฟแนนซ์',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard($param,$dataLoad,$this);
				},
				beforeClose : function(){
					$('#btnt1finance').attr('disabled',false);
				}
			});
			
			jd_btnt1finance = null;
		},
		beforeSend: function(){
			if(jd_btnt1finance !== null){ jd_btnt1finance.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function wizard($param,$dataLoad,$thisWindowFinance){
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	
	function initPage(){
		$('#wizard-finance').bootstrapWizard({
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
				
				var sdate 		= 'x';
				var cuscod 		= 'x';
				var cuscodaddr 	= 'x';
				var strno 		= 'x';
				var paydue 		= 'x';
				
				switch(index){
					case 0: //tab1
						$msg = "";
						
						if(paydue 		== ''){ $msg = "ไม่พบวิธีชำระค่างวด โปรดระบุวิธีชำระค่างวดก่อนครับ"; }
						if(strno 		== ''){ $msg = "ไม่พบเลขตัวถัง โปรดระบุเลขตัวถังก่อนครับ"; }
						if(cuscodaddr 	== ''){ $msg = "ไม่พบที่อยู่ในการพิมพ์สัญญา โปรดระบุที่อยู่ในการพิมพ์สัญญาก่อนครับ"; }
						if(cuscod 		== ''){ $msg = "ไม่พบรหัสลูกค้า โปรดระบุรหัสลูกค้าก่อนครับ"; }
						if(sdate 		== ''){ $msg = "ไม่พบวันที่ขาย โปรดระบุวันที่ขายก่อนครับ"; }
						
						if($msg != ""){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: $msg
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
	
	$('#btn_addBillDas').attr('disabled',true);
	
	$('#add_contno').val('Auto Genarate');
	$('#add_contno').attr('readonly',true);
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: (_level == 1 ? $("#wizard-finance") : true),
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
				dataToPost = new Object();
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_cuscod').click(function(){
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url:'../Cselect2/getformCUSTOMER',
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
										dtp = new Object();
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
		dropdownParent: $("#wizard-finance"), 
		minimumResultsForSearch: -1,
		width: '100%'
	});
	
	$('#add_addrno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_addrno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = $('#add_cuscod').attr('CUSCOD');
				//dataToPost.cuscod = $('#add_cuscod').find(':selected').val();
				
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_strno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_fincode').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getFINCODE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_fincode').find(':selected').val();
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_salcod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_acticod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: $("#wizard-finance"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_recomcod').click(function(){
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url:'../Cselect2/getformCUSTOMER',
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
										dtp = new Object();
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
	//korn
	$('#add_inopt').click(function(){
		$('#loadding').fadeIn(200);
		$.ajax({
			url: '../SYS04/Finance/getFormInopt',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);	
				Lobibox.window({
					title: 'เพิ่มอุปกรณ์เสริม',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						$('#getvalue_inopt').hide();
						
						$('#op_code').select2({
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2K/getOPTCODE_ACS',
								data: function (params){
									dataToPost = new Object();
									dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
									dataToPost.add_locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? "":$('#add_locat').find(':selected').val());	
									
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
							allowClear: false,
							multiple: false,
							dropdownParent: $(".inoptform"),
							//disabled: true,
							//theme: 'classic',
							width: '100%'
						});
						$('#op_code').change(function(){
							$('#inopt_results').hide();
							$('#getvalue_inopt').hide();
						});
						
						$('#cal_inopt').click(function(){
							dataToPost = new Object();
							dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '' : $('#add_inclvat').find(':selected').val());
							dataToPost.vatrt   = $('#add_vatrt').val();
							dataToPost.opCode  = (typeof $('#op_code').find(':selected').val() === 'undefined' ? '' : $('#op_code').find(':selected').val());
							dataToPost.opText  = (typeof $('#op_code').find(':selected').text() === 'undefined' ? '' : $('#op_code').find(':selected').text());
							dataToPost.uprice  = $('#op_uprice').val();
							dataToPost.cvt     = $('#op_cvt').val();
							dataToPost.qty     = $('#op_qty').val();
							$('#loadding').fadeIn(200);
							$.ajax({
								url: '../SYS04/Finance/calculate_inopt',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){ 
									$('#loadding').fadeOut(200);
									if(data.status){
										$('#inopt_results').show();
										$('#inopt_results').html(data.html);
										$('#getvalue_inopt').show();
										
										$('#getvalue_inopt').attr({
											'opCode': $('#op_code').find(':selected').val()
											,'opText': $('#op_code').find(':selected').text()
											,'qty'   : data["qty"].replace(',','')
											,'uprice': data["uprice"].replace(',','')
											,'price1' : data["1price"].replace(',','')
											,'vat1'  : data["1vat"].replace(',','')
											,'total1': data["1total"].replace(',','')
											,'price2': data["2price"].replace(',','')
											,'vat2'  : data["2vat"].replace(',','')
											,'total2': data["2total"].replace(',','')
										});
									}else{
										$('#inopt_results').show();
										$('#inopt_results').html(data.msg);
									}
								}							
							});
						});
						
						$('#getvalue_inopt').click(function(){
							var opCode = $(this).attr('opCode');
							var opText = $(this).attr('opText');
							var qty	   = $(this).attr('qty');
							var uprice = $(this).attr('uprice');
							var price1 = $(this).attr('price1');
							var vat1   = $(this).attr('vat1');
							var total1 = $(this).attr('total1');
							var price2 = $(this).attr('price2');
							var vat2   = $(this).attr('vat2');
							var total2 = $(this).attr('total2');
							
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
									delay: 5000,
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
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
					}
				});
			}
		});
	});
	
	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกรายการขายส่งไฟแนนซ์หรือไม่ ?',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-cancel',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){ 
					fnSave($thisWindowFinance); 
				}
			},
			beforeClose: function(){
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
}
function inopt_remove(){
	$('.inoptTab2').unbind('click');
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
function fnSave($thisWindow){
	dataToPost = new Object();
	dataToPost.CONTNO    = $('#add_contno').val();
	dataToPost.LOCAT     = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.CONTNO    = $('#add_sdate').val();
	dataToPost.RESVNO    = (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '':$('#add_resvno').find(':selected').val());
	dataToPost.APPROVE   = $('#add_approve').val();
	dataToPost.CUSCOD    = $('#add_cuscod').val();
	dataToPost.INCLVAT   = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '':$('#add_inclvat').find(':selected').val());
	dataToPost.VATRT     = $('#add_vatrt').val();
	dataToPost.ADDRNO    = (typeof $('#add_addrno').find(':selected').val() === 'undefined' ? '':$('#add_addrno').find(':selected').val());
	dataToPost.STRNO     = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '':$('#add_strno').find(':selected').val());
	dataToPost.REGNO     = $('#add_reg').val();
	dataToPost.ACTICOD   = (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '':$('#add_acticod').find(':selected').val());
	var listopt = [];
	$('.add_inopt').each(function(){
		var list = [];
		list.push($(this).attr(''));
	});
}







