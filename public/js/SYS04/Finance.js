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

var jd_btnt1finance = null;
$('#btnt1finance').click(function(){
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
					wizard('new','',$this);
				},
				beforeClose : function(){
					$('#btnt1finance').attr('disabled',false);
				}
			});
			
			jd_btnt1finance = null;
		},
		beforeSend: function(){
			if(jd_btnt1finance !== null){ jd_btnt1finance.abort(); }
		}
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
		$('#wizard-leasing').bootstrapWizard({
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
		dropdownParent: (_level == 1 ? $("#wizard-leasing") : true),
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
		dropdownParent: $("#wizard-leasing"),
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
								}
							});
						}
						
					},
					beforeClose : function(){
						$('#add_cuscod').attr('disabled',false);
						$('#add_save').attr('disabled',false);
					}
				});
				
				$('#loadding').fadeOut(200);
			}
		});
	});
	
	/*
	$('#add_cuscod').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	*/
	
	$('#add_inclvat').select2({ 
		dropdownParent: $("#wizard-leasing"), 
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
		dropdownParent: $("#wizard-leasing"),
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
		dropdownParent: $("#wizard-leasing"),
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
		dropdownParent: $("#wizard-leasing"),
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
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
								}
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
			}
		});
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
}


function fn_billdasActive(rank){
	$('.add_billdas[rank='+rank+']').select2({ 
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getBILLDAS',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: $("#wizard-leasing"),
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
			$('.add_billdas').each(function(){ 
				if(typeof $(this).find(':selected').val() === 'undefined'){ 
					size += 1; 
				} 
			});
			
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
	$saleno = new Array();
	$('.add_billdas').each(function(){
		if(typeof $(this).find(':selected').val() !== 'undefined'){
			$saleno.push($(this).find(':selected').val());
		}
	});	
	
	if($saleno.length > 0){
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/calbilldas',
			data: {saleno:$saleno,locat:(typeof $("#add_locat").find(':selected').val() === 'undefined' ? '' : $("#add_locat").find(':selected').val())},
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





