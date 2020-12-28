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
$(function(){
	if(_insert == "T"){
		$('#btnt1finance').attr("disabled",false);
	}else{
		$('#btnt1finance').attr("disabled",true);
	}
	$('#btnt1search').click(function(){
		fn_searchresult();
	});
});

var kb_btnt1search = null; //fn_searchresult
function fn_searchresult(){
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
					var contno = $(this).attr('contno');
					loadDetails(contno);
				});
			}
			kb_btnt1search = null;
		},
		beforeSend: function(){
			if(kb_btnt1search !== null){ kb_btnt1search.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

var kb_loadDetails = null;
function loadDetails($contno){
	dataToPost = new Object();
	dataToPost.CONTNO  = $contno;
	$('#loadding').fadeIn(200);
	kb_loadDetails = $.ajax({
		url:'../SYS04/Finance/loadDetails',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			loadFormFinance('edit',data);
			
			kb_loadDetails = null;
		},
		beforeSend: function(){
			if(kb_loadDetails !== null){ kb_loadDetails.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
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
				var cuscod 		= $('#add_cuscod').val();
				var cuscodaddr 	= 'x';
				var strno 		= (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '':$('#add_strno').find(':selected').val());
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
						//$('#add_strno').attr('disabled',true);	
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
	$('#add_vatrt').attr('disabled',true);
	$('#btnTax').attr('disabled',true);
	$('#btnSend').attr('disabled',true);
	$('#btnApproveSell').attr('disabled',true);
	
	$('#add_stdprc').attr('disabled',true);
	
	//$('#btn_addBillDas').attr('disabled',true);
	$('#add_dscprc').attr('disabled',true);
	
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
		$('#add_addrno').empty();
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
	if($param == "add"){
		$('#add_resvno').change(function(){
			dataToPost = new Object();
			dataToPost.resvno = (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '':$('#add_resvno').find(':selected').val());
			dataToPost.locat  = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
			$('#loadding').fadeIn(200);
			$.ajax({
				url: '../SYS04/Finance/change_resvno',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').fadeOut(200);
					if(data.error){
						Lobibox.notify('error', {
							title: 'ผิดพลาด',
							size: 'mini',
							closeOnClick: false,
							delay: 15000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.msg
						});
						$('#add_resvno').empty();
					}else if(data.RESVNO == ""){
						$('#add_strno').empty();
						$('#add_addrno').empty();
						$('#add_acticod').empty();
						
						$('#add_cuscod').val("");
						$('#add_cuscod').attr("CUSCOD","");
						$('#add_strno').attr('disabled',false);
						$('#add_acticod').attr('disabled',false);
					}else{
						$('#add_cuscod').attr('CUSCOD',data.CUSCOD);
						$('#add_cuscod').val(data.CUSNAME);
						
						var newOption = new Option(data.ADDRDES, data.ADDRNO, true, true);
						$('#add_addrno').empty().append(newOption).trigger('change');
						
						var newOption = new Option(data.STRNO, data.STRNO, true, true);
						$('#add_strno').attr('disabled',true);
						$('#add_strno').empty().append(newOption).trigger('change');
						
						var newOption = new Option(data.ACTIDES, data.ACTICOD, true, true);
						$('#add_acticod').attr('disabled',true);
						$('#add_acticod').empty().append(newOption).trigger('change');
					}
				}	
				,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
		});
		
		$('#add_strno').change(function(){
			dataToPost = new Object();
			dataToPost.STRNO = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '':$('#add_strno').find(':selected').val());
			
			$('#loadding').fadeIn(200);
			$.ajax({
				url: '../SYS04/Finance/get_strPrice',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').fadeOut(200);
					
					$('#add_stdprc').val(data.STDPRC);
					$('#add_dscprc').val(data.DSCPRC);
					$('#add_stdprc').attr("STDINV",data.STDPRC);
					
					$('#add_stdprc').attr('NCARCST',data.NCARCST);
					$('#add_stdprc').attr('VCARCST',data.VCARCST);
					$('#add_stdprc').attr('TCARCST',data.TCARCST);
					
					$('#add_inprc').val("");
					
				}
				,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
		});	
	}

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
	
	//ดึงเลขที่บิลของแถมจาก DAS
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
											'opCode' : $('#op_code').find(':selected').val()
											,'opText': $('#op_code').find(':selected').text()
											,'qty'   : data["qty"].replace(',','')
											,'uprice': data["uprice"].replace(',','')
											,'price1': data["1price"].replace(',','')
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
								row += "		opCode='"+opCode+"' opText ='"+opText+"' total1='"+total1+"' total2='"+total2+"' ";
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
								
								if($('#add_inclvat').val() == "Y"){
									var listot = parseInt(total1.replace(',',''));
									var dscprc = parseInt($('#add_dscprc').val().replace(',',''));
									
									var std1    = $('#add_stdprc').val();
									var stdprc  = parseInt(std1.replace(',',''));
									
									$('#add_dscprc').val(parseFloat(listot + dscprc).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									$('#add_stdprc').val(parseFloat(listot + stdprc).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									
									var sumTotal1 = 0;
									var sumTotal2 = 0;
									
									$('.inoptTab2').each(function(){
										sumTotal1 += parseFloat($(this).attr('total1').replace(',',''));
										sumTotal2 += parseFloat($(this).attr('total2').replace(',',''));
									});
									
									$('#add2_optptot').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									$('#add2_optctot').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									
									inopt_remove();
								}else{
									var listot = parseInt(total1.replace(',',''));
									
									var sumTotal1 = 0;
									var sumTotal2 = 0;
									
									$('.inoptTab2').each(function(){
										sumTotal1 += parseFloat($(this).attr('total1').replace(',',''));
										sumTotal2 += parseFloat($(this).attr('total2').replace(',',''));
									});
									//ราคาขายรวมอุปกรณ์เสริม add2_optptot
									$('#add2_optptot').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									$('#add2_optctot').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									
									//คำนวณหาราคาขายจริงรวมภาษี
									var inprc   = parseFloat(($('#add_inprc').val() == "" ? 0:$('#add_inprc').val()));
									var stdprc  = parseFloat($('#add_stdprc').val().replace(',',''));
									var vatrt   = parseFloat($('#add_vatrt').val());
									var vatprc  = parseFloat((inprc * vatrt) / 100);
									var inprctot= parseFloat(inprc + vatprc);
									
									$('#add_stdprc').val(parseFloat(stdprc + listot).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									$('#add_dscprc').val(parseFloat((stdprc + listot) - inprctot).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
									
									inopt_remove();
								}
								
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
	$('#add_inprc').keyup(function(){
		if($('#add_inclvat').val() == "Y"){
			var inprc   = parseInt($('#add_inprc').val());
			var optptot = parseInt($('#add2_optptot').val().replace(',',''));
			
			var std1    = $('#add_stdprc').attr('STDINV');
			var stdprc  = parseInt(std1.replace(',',''));
			var dscprc  = parseFloat((stdprc - inprc) + optptot) || 0;
			
			$('#add_dscprc').val(dscprc);
		}else{
			//คำนวณหาราคาขายจริงรวมภาษี
			var inprc   = parseInt(($('#add_inprc').val() == "" ? 0:$('#add_inprc').val()));
			var stdprc  = parseInt($('#add_stdprc').val().replace(',',''));
			var vatrt   = parseInt($('#add_vatrt').val());
			var vatprc  = parseFloat((inprc * vatrt) / 100);
			var inprctot= parseInt(inprc + vatprc);
			
			$('#add_dscprc').val((parseFloat(stdprc - inprctot)).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
		}
	});
	
	if($param == "add"){
		$('#add_delete').attr('disabled',true);
	}else{
		loadDatatoForm($dataLoad);
		$('#btnTax').attr('disabled',false);
		$('#btnSend').attr('disabled',false);
		$('#btnApproveSell').attr('disabled',false);
	}
	
	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกรายการขายส่งไฟแนนซ์หรือไม่ ?',
			closeButton: false,
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
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
	$('#btnTax').click(function(){
		printReport('TAX');
	});
	$('#btnSend').click(function(){
		printReport('SEND');
	});
	$('#btnApproveSell').click(function(){
		printReport("SELL");
	});
	
	$('#add_delete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการลบรายการขายส่งไฟแนนซ์เลขที่สัญญาที่ : '+'<span style="color:red;">'+$('#add_contno').val()+'</span>'+'หรือไม่ ?',
			closeButton: false,
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
					fnDel($thisWindowFinance); 
				}
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
}
function loadDatatoForm($data){
	$('#add_contno').attr("disabled",true);
	$('#add_sdate').attr("disabled",true);
	$('#add_resvno').attr("disabled",true);
	$('#add_appvno').attr("disabled",true);
	$('#add_cuscod').attr("disabled",true);
	$('#add_inclvat').attr("disabled",true); 
	$('#add_vatrt').attr("disabled",true);
	
	//$('#add_addrno').attr("disabled",true);
	
	$('#add_strno').attr("disabled",true);
	
	$('#add_reg').attr("disabled",true);
	$('#add_inprc').attr("disabled",true);
	$('#add_indwn').attr("disabled",true);
	$('#add_stdprc').attr("disabled",true);
	//$('#add_fincom').attr("disabled",true);
	//$('#add_comitn').attr("disabled",true);
	//$('#add_issuno').attr("disabled",true); 
	//$('#add_issudt').attr("disabled",true);
	$('#add_recomcod').attr("disabled",true);
	//$('#add_comext').attr("disabled",true);
	//$('#add_comopt').attr("disabled",true);
	//$('#add_comoth').attr("disabled",true);
	$('#add_crdtxno').attr("disabled",true);
	$('#add_crdamt').attr("disabled",true);
	
	$('#add_contno').val($data.CONTNO);
	var newOption = new Option($data.LOCAT ,$data.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change'); 
	
	$('#add_sdate').val($data.SDATE);
	
	var newOption = new Option($data.RESVNO ,$data.RESVNO, true, true);
	$('#add_resvno').empty().append(newOption).trigger('change'); 
	
	$('#add_appvno').val($data.APPVNO);
	$('#add_cuscod').val($data.CUSNAME);
	$('#add_cuscod').attr("CUSCOD",$data.CUSCOD);
	$('#add_inclvat').val($data.INCLVAT).trigger('change'); 
	$('#add_vatrt').val($data.VATRT);
	var newOption = new Option($data.ADDR ,$data.ADDRNO, true, true);
	$('#add_addrno').empty().append(newOption).trigger('change'); 
	var newOption = new Option($data.STRNO ,$data.STRNO, true, true);
	$('#add_strno').empty().append(newOption).trigger('change'); 
	var newOption = new Option($data.ACTIDES ,$data.ACTICOD, true, true);
	$('#add_acticod').empty().append(newOption).trigger('change');
	$('#add_inprc').val($data.KEYIN);	
	$('#add_indwn').val($data.KEYINDWN);
	$('#add_stdprc').val($data.STDPRC);
	$('#add_dscprc').val($data.DSCPRC);
	var newOption = new Option($data.FINNAME ,$data.FINCOD, true, true);
	$('#add_fincode').empty().append(newOption).trigger('change');	
	$('#add_fincom').val($data.FINCOM);
	var newOption = new Option($data.USERNAME ,$data.SALCOD, true, true);
	$('#add_salcod').empty().append(newOption).trigger('change');	
	$('#add_comitn').val($data.COMITN);
	$('#add_taxno').val($data.TAXNO);
	$('#add_taxdt').val($data.TAXDT);
	$('#add_issuno').val($data.ISSUNO);
	$('#add_issudt').val($data.ISSUDT);
	$('#add_recomcod').val($data.RECOMNAME);
	$('#add_recomcod').attr("CUSCOD",$data.RECOMCOD);
	$('#add_paydwn').val($data.PAYDWN);
	$('#add_payfin').val($data.PAYFIN);
	$('#add_comext').val($data.COMEXT);
	$('#add_comopt').val($data.COMOPT);
	$('#add_comoth').val($data.COMOTH);
	$('#add_crdtxno').val($data.CRDTXNO);
	$('#add_crdamt').val($data.CRDAMT);
	$('#add_memo1').val($data.MEMO1);
	
	$('#dataTables-inopt tbody').append($data.listopt);	
	
	$('#add2_optctot').val($data.OPTCTOT);
	$('#add2_optptot').val($data.OPTPTOT);
	
	if(_update == "T"){
		$('.inoptTab2').attr("disabled",false);
		$('#add_inopt').attr("disabled",false);
		$('#add_save').attr("disabled",false);
	}else{
		$('.inoptTab2').attr("disabled",true);
		$('#add_inopt').attr("disabled",true);
		$('#add_save').attr("disabled",true);
	}
	if(_delete == "T"){
		$('#add_delete').attr("disabled",false);	
	}else{
		$('#add_delete').attr("disabled",true);	
	}
	inopt_remove();
}
function inopt_remove(){
	$('.inoptTab2').unbind('click');
	$('.inoptTab2').click(function(){
		if($('#add_inclvat').val() == "Y"){
			var listot = parseInt($(this).attr('total1').replace(',','')); //มูลค่ารวมภาษีแต่ละรายการ
			var dscprc = parseInt($('#add_dscprc').val().replace(',','')); //ส่วนลด
			
			var std1    = $('#add_stdprc').val(); //ราคาขายหน้าร้าน
			var stdprc  = parseInt(std1.replace(',',''));
			
			$('#add_stdprc').val(parseFloat(stdprc - listot).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
			$('#add_dscprc').val(parseFloat(dscprc - listot).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
			
			$(this).parents().closest('tr').remove(); 
			
			var sumTotal1 = 0;
			var sumTotal2 = 0;
			
			$('.inoptTab2').each(function(){
				sumTotal1 += parseFloat($(this).attr('total1').replace(',',''));
				sumTotal2 += parseFloat($(this).attr('total2').replace(',',''));
			});
			
			$('#add2_optptot').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')); //ต้นทุนรวมคงเหลือ
			$('#add2_optctot').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')); //ราคาขายคงเหลือ	
		}else{
			//อุปกรณ์เสริมเพิ่ม ลบ
			$(this).parents().closest('tr').remove(); 
			
			var sumTotal1 = 0;
			var sumTotal2 = 0;
			
			$('.inoptTab2').each(function(){
				sumTotal1 += parseFloat($(this).attr('total1').replace(',',''));
				sumTotal2 += parseFloat($(this).attr('total2').replace(',',''));
			});
			//ราคาขายรวมอุปกรณ์เสริม add2_optptot
			$('#add2_optptot').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
			$('#add2_optctot').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
			
			var listot = parseFloat($(this).attr('total1').replace(',',''));
			
			//คำนวณหาราคาขายจริงรวมภาษี
			var inprc   = parseFloat(($('#add_inprc').val() == "" ? 0:$('#add_inprc').val()));
			var stdprc  = parseFloat($('#add_stdprc').val().replace(',',''));
			
			$('#add_stdprc').val(parseFloat(stdprc - listot).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
			var stdprc2 = parseFloat($('#add_stdprc').val().replace(',',''));
			
			var vatrt   = parseFloat($('#add_vatrt').val());
			var vatprc  = parseFloat((inprc * vatrt) / 100);
			
			var inprctot= parseFloat(inprc + vatprc);
			var dscprc  = parseFloat(stdprc2 - inprctot);
			
			$('#add_dscprc').val(parseFloat(dscprc).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
		}
	});
}
//ดึงเลขที่บิลของแถมจาก DAS 
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
				
				var customers = new Array();
				if($('#add_cuscod').val() != ""){
					customers.push($('#add_cuscod').attr('cuscod'));
					customers.push($('#add_recomcod').attr('cuscod'));
				}
				dataToPost.customers = (customers.length > 0 ? customers: []);
				
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
	//$('.add_billdas[rank='+rank+']').on("select2:unselecting", function(e) { fn_calbilldas(); });
}
function fn_calbilldas(){
	var saleno = new Array();
	$('.add_billdas').each(function(){
		if(typeof $(this).find(':selected').val() !== 'undefined'){
			saleno.push($(this).find(':selected').val());
		}
	});	
	
	if(saleno.length > 0){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../SYS04/Finance/calbilldas',
			data: {saleno:saleno,locat:(typeof $("#add_locat").find(':selected').val() === 'undefined' ? '' : $("#add_locat").find(':selected').val())},
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#add_comopt').val(data.TotalAmt);
				
				//$('#add_comments_free').val(data.Details);
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		})
	}else{
		$('#add_comopt').val('0.00');
		
		//$('#add_comments_free').val('');
	}
}

function fnSave($thisWindow){
	dataToPost = new Object();
	dataToPost.CONTNO    = $('#add_contno').val();
	dataToPost.LOCAT     = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.SDATE     = $('#add_sdate').val();
	dataToPost.RESVNO    = (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '':$('#add_resvno').find(':selected').val());
	dataToPost.APPVNO    = $('#add_appvno').val();
	dataToPost.CUSCOD    = $('#add_cuscod').attr('CUSCOD');
	dataToPost.INCLVAT   = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '':$('#add_inclvat').find(':selected').val());
	dataToPost.VATRT     = $('#add_vatrt').val();
	dataToPost.ADDRNO    = (typeof $('#add_addrno').find(':selected').val() === 'undefined' ? '':$('#add_addrno').find(':selected').val());
	dataToPost.STRNO     = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '':$('#add_strno').find(':selected').val());
	dataToPost.REGNO     = $('#add_reg').val();
	dataToPost.ACTICOD   = (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '':$('#add_acticod').find(':selected').val());
	var listopt = [];
	var sumPrice1  = 0;
	var sumVat1    = 0;
	var sumPrice2  = 0;
	var sumVat2    = 0;
	
	$('.inoptTab2').each(function(){
		var list = [];
		
		list.push($(this).attr('opCode'));
		list.push($(this).attr('opText'));
		list.push($(this).attr('qty'));
		list.push($(this).attr('uprice'));
		list.push($(this).attr('price1'));
		list.push($(this).attr('vat1'));
		list.push($(this).attr('total1'));
		list.push($(this).attr('total2'));
		list.push($(this).attr('vat2'));
		list.push($(this).attr('price2'));
		listopt.push(list);
		
		//sumPrice1 += parseFloat($(this).attr('price1'));
		//sumVat1   += parseFloat($(this).attr('vat1'));
		
		//sumPrice2 += parseFloat($(this).attr('price2'));
		//sumVat2   += parseFloat($(this).attr('vat2'));
		
	});
	dataToPost.listopt = (listopt == "" ? "noopt":listopt);
	
	dataToPost.OPTCTOT  = $('#add2_optctot').val();
	dataToPost.OPTPTOT  = $('#add2_optptot').val();
	
	//dataToPost.OPTPRC  = $('#add2_optptot').val();
	
	dataToPost.INPRC   = $('#add_inprc').val();
	dataToPost.INDWN   = $('#add_indwn').val();
	
	
	dataToPost.STDPRC  = $('#add_stdprc').val();
	
	dataToPost.NCARCST = $('#add_stdprc').attr('NCARCST');
	dataToPost.VCARCST = $('#add_stdprc').attr('VCARCST');
	dataToPost.TCARCST = $('#add_stdprc').attr('TCARCST');
	
	dataToPost.DSCPRC  = $('#add_dscprc').val();
	dataToPost.FINCOD  = (typeof $('#add_fincode').find(':selected').val() === 'undefined' ? '':$('#add_fincode').find(':selected').val());
	dataToPost.FINCOM  = $('#add_fincom').val();
	dataToPost.SALCOD  = (typeof $('#add_salcod').find(':selected').val() === 'undefined' ? '':$('#add_salcod').find(':selected').val());
	dataToPost.COMITN  = $('#add_comitn').val();
	//dataToPost.TAXNO   = $('#add_taxno').val();
	dataToPost.TAXDT   = $('#add_taxdt').val();
	//dataToPost.FINCODE = $('#add_fincode').val();
	dataToPost.ISSUNO  = $('#add_issuno').val();
	dataToPost.ISSUDT  = $('#add_issudt').val();
	dataToPost.RECOMCODE  = ($('#add_recomcod').attr('CUSCOD') == "" ? "":$('#add_recomcod').attr('CUSCOD'));
	dataToPost.PAYDWN  = $('#add_paydwn').val();
	dataToPost.PAYFIN  = $('#add_payfin').val();
	
	dataToPost.COMEXT  = $('#add_comext').val();
	dataToPost.COMOPT  = $('#add_comopt').val();
	dataToPost.COMOTH  = $('#add_comoth').val();
	
	//dataToPost.CRDTXNO   = $('#add_crdtxno').val();
	//dataToPost.CRDAMT    = $('#add_crdamt').val();
	dataToPost.MEMO1   = $('#add_memo1').val();
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../SYS04/Finance/Save',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				$thisWindow.destroy();
				fn_searchresult();
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
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
		},error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
function fnDel($thisWindow){
	dataToPost = new Object();
	dataToPost.CONTNO    = $('#add_contno').val();
	dataToPost.LOCAT     = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	
	var listopt = [];
	$('.inoptTab2').each(function(){
		var list = [];
		list.push($(this).attr('opCode'));
		list.push($(this).attr('opText'));
		list.push($(this).attr('qty'));
		list.push($(this).attr('uprice'));
		list.push($(this).attr('price1'));
		list.push($(this).attr('vat1'));
		list.push($(this).attr('total1'));
		list.push($(this).attr('total2'));
		list.push($(this).attr('vat2'));
		list.push($(this).attr('price2'));
		listopt.push(list);
	});
	dataToPost.listopt    = (listopt == "" ? []:listopt);
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../SYS04/Finance/fnDel',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				$thisWindow.destroy();
				fn_searchresult();
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
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
		},error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
var sale_report = null;
function printReport($type){
	if($type == "TAX"){
		dataToPost = new Object();
		dataToPost.param   = $type;
		dataToPost.contno  = $('#add_contno').val();
		dataToPost.locat   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
		sale_report = $.ajax({
			url:'../SYS04/Finance/conditiontopdf',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS04/Finance/pdftax?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title:"พิมใบกำกับภาษี",
					content:content,
					closeOnEsc:false,
					height:$(window).height(),
					width:$(window).width()
				});
				sale_report = null;
			},
			beforeSend:function(){
				if(sale_report !== null){sale_report.abort();}
			}
		});	
	}else if($type == "SEND"){
		var html = " \
			<div  class='col-sm-12> \
				<div class='form-group'> \
					<b>หัวข้อ Form</b> \
					<div class='row'> \
						<div class='col-xs-6'> \
							<label class='radio-inline lobiradio lobiradio-primary'> \
								<input type='radio' name='titlefrom' value='ใบส่งมอบสินค้า' checked> \
								<i></i> ใบส่งมอบสินค้า \
							</label> \
						</div> \
						<div class='col-xs-6'> \
							<label class='radio-inline lobiradio lobiradio-primary'> \
								<input type='radio' name='titlefrom' value='ใบเสร็จรับเงิน / ใบกำกับภาษี'> \
								<i></i> ใบเสร็จรับเงิน / ใบกำกับภาษี \
							</label> \
						</div> \
						<div class='col-xs-6'> \
							<label class='radio-inline lobiradio lobiradio-primary'> \
								<input type='radio' name='titlefrom' value='ใบส่งของ / ใบกำกับภาษี'> \
								<i></i> ใบส่งของ / ใบกำกับภาษี \
							</label> \
						</div> \
						<div class='col-xs-6'> \
							<label class='radio-inline lobiradio lobiradio-primary'> \
								<input type='radio' name='titlefrom' value='ใบเสร็จรับเงิน'> \
								<i></i> ใบเสร็จรับเงิน \
							</label> \
						</div> \
					</div> \
					<div class='row'> \
						<div class='col-sm-12'> \
							<b>หมายเหตุ</b> \
							<textarea class='form-control' style='height:200px;text-align:left;' id='memo'  rows='4' cols='50'> \
								ข้าพเจ้าได้ตรวจสอบดูแล้ว เห็นว่ารถคันนี้พร้อมด้วยเครื่องยนต์และอุปกรณ์ต่างๆ อยู่ในสภาพเรียบร้อยทุกประการ ในกรณีที่รถคันนี้เกิดเสียหายด้วยเหตุใดๆก็ตามภายหลังจากการมอบรถไปแล้วข้าพเจ้าขอรับผิดชอบทั้งสิ้น เพื่อเป็นหลักฐานในการนี้ ข้าพเจ้าจึงลงนามไว้แล้ว \
							</textarea> \
						</div> \
						<div class='col-sm-12'> \
							<br> \
							<button id='btnscreenReport' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>screen</b></span></button> \
						</div> \
					</div> \
				</div> \
			</div> \
		";
		Lobibox.window({
			title:"ใบส่งมอบสินค้า",
			content:content,
			closeOnEsc:false,
			height:500,
			width:800,
			content: html,
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			shown: function($this){
				$('#btnscreenReport').click(function(){
					screenReport($type);
					$this.destroy();
				});
			},
			beforeClose : function(){
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	}else{
		dataToPost = new Object();
		dataToPost.param   = $type;
		dataToPost.contno  = $('#add_contno').val();
		dataToPost.locat   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
		sale_report = $.ajax({
			url:'../SYS04/Finance/conditiontopdf',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS04/Finance/pdfsell?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title:"พิมพ์ใบอนุมัติขาย",
					content:content,
					closeOnEsc:false,
					height:$(window).height(),
					width:$(window).width()
				});
				sale_report = null;
			},
			beforeSend:function(){
				if(sale_report !== null){sale_report.abort();}
			}
		});	
	}
}
function screenReport($type){
	dataToPost = new Object();
	dataToPost.param   = $type;
	dataToPost.contno  = $('#add_contno').val();
	dataToPost.locat   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.tfrom   = $('input[type=radio][name=titlefrom]:checked').val();
	dataToPost.memo    = $('#memo').val();
	sale_report = $.ajax({
		url:'../SYS04/Finance/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS04/Finance/pdfsend?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:"พิมพ์ใบส่งมอบสินค้า",
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			sale_report = null;
		},
		beforeSend:function(){
			if(sale_report !== null){sale_report.abort();}
		}
	});
}







