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
	if(_insert == 'T'){
		$('#btnt1agent').attr('disabled',false);
	}else{
		$('#btnt1agent').attr('disabled',true);
	}
});

var jd_btnt1search = null;
$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.contno 	= $('#CONTNO').val();
	dataToPost.sdatefrm = $('#SDATEFRM').val();
	dataToPost.sdateto 	= $('#SDATETO').val();
	dataToPost.locat 	= $('#LOCAT').val();
	dataToPost.strno	= $('#STRNO').val();
	dataToPost.cuscod 	= $('#CUSCOD').val();
	
	$('#loadding').fadeIn(200);
	jd_btnt1search = $.ajax({
		url: '../SYS04/Agent/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#result').html(data.html);
			
			$('#table-agent').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-agent',1,275);
			
			jd_btnt1search = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){ if(jd_btnt1search !== null){ jd_btnt1search.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

var jd_loadAgent = null;
function redraw(){
	let jd_agentDetails = null;
	$('.agentDetails').unbind('click');
	$('.agentDetails').click(function(){
		dataToPost = new Object();
		dataToPost.contno = $(this).attr('contno');
		
		jd_agentDetails = $.ajax({
			url:'../SYS04/Agent/loadAgent',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success: function(data){
				loadAgent(data);
			},
			beforeSend: function(){
				
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
}

function loadAgent($param){
	$('#loadding').fadeIn(200);
	jd_loadAgent = $.ajax({
		url:'../SYS04/Agent/getfromAgent',
		type: 'POST',
		dataType: 'json',
		success: function(data){
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
			
			jd_loadAgent = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){  if(jd_loadAgent !== null){ jd_loadAgent.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

var jd_btnt1agent = null;
$('#btnt1agent').click(function(){
	$('#btnt1agent').attr('disabled',true);
	$('#loadding').fadeIn(200);
	jd_btnt1agent = $.ajax({
		url: '../SYS04/Agent/getfromAgent',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'บันทึกรายการขายส่งเอเย่นต์',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard('new','',$this);
				},
				beforeClose : function(){
					$('#btnt1agent').attr('disabled',false);
				}
			});
			
			jd_btnt1agent = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(jd_btnt1agent !== null){ jd_btnt1agent.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function wizard($param,$dataLoad,$thisWindowAgent){	
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
					case 3: //tab4
						nextTab(ind2); 
						break;
					case 4: //tab5
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
	
	
	$('#add_cuscod_removed').click(function(){
		$('#add_cuscod').attr('CUSCOD','');
		$('#add_cuscod').val('');
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
							
							let use = new Object();
							use.recomcod = $('#add_recomcod').attr('CUSCOD');
							data.inuse = use;
							
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
										
										$('#add_cuscod').attr('CUSCOD',dtp.cuscod);
										$('#add_cuscod').val(dtp.cusname);
										
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
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_inclvat').select2({ 
		dropdownParent: $("#wizard-leasing"), 
		minimumResultsForSearch: -1,
		width: '100%'
	});
	
	$('#add_paydue').select2({ 
		placeholder: 'เลือก',
		minimumResultsForSearch: -1,
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				dataToPost = new Object();
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
	
	var jd_add_strno = null;
	$('#add_strno').click(function(){
		$('#add_strno').attr('disabled',true);
		dataToPost = new Object();
		dataToPost.inclvat = $('#add_inclvat').val();
		dataToPost.vatrt = $('#add_vatrt').val();
		
		jd_add_strno = $.ajax({
			url:'../SYS04/Agent/getFormSTRNO',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				Lobibox.window({
					title: 'ค้นหาข้อมูลรถ',
					//width: setwidth,
					//height: '300',
					draggable: true,
					content: data.html,
					closeOnEsc: true,
					shown: function($this){
						fn_formSTRNO($this);
					},
					beforeClose: function(){
						$('#add_strno').attr('disabled',false);
					}
				});
				
				jd_add_strno = null;
			},
			beforeSend : function(){
				if(jd_add_strno !== null){ jd_add_strno.abort(); }
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_recomcod_removed').click(function(){
		$('#add_recomcod').attr('CUSCOD','');
		$('#add_recomcod').val('');
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
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	
	$('#add_save').click(function(){
		$('#add_save').attr('disabled',true);
		$('#add_delete').attr('disabled',true);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกรายการขายส่งเอเย่นต์หรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, บันทึก',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-cancel',
					text: ' ยกเลิก, ไม่บันทึก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){ fnSave($thisWindowAgent); }
				
				$('#add_save').attr('disabled',false);
				$('#add_delete').attr('disabled',false);
			}
		});
	});
	
	var jd_add_delete = null;
	$('#add_delete').click(function(){
		$('#add_save').attr('disabled',true);
		$('#add_delete').attr('disabled',true);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกรายการขายส่งเอเย่นต์หรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, บันทึก',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-cancel',
					text: ' ยกเลิก, ไม่บันทึก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){ 
					//fnSave($thisWindowAgent); 
					dataToPost = new Object();
					dataToPost.contno = $('#add_contno').val();
					
					jd_add_delete = $.ajax({
						url:'../SYS04/Agent/deleteContno',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success: function(data){
							if(data.error){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
									soundExt: '.ogg',
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else{
								Lobibox.notify('success', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
									soundExt: '.ogg',
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
								$thisWindowAgent.destroy();
							}
							
							jd_add_delete = null;
						},
						beforeSend: function(){
							if(jd_add_delete !== null){ jd_add_delete.abort(); }
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}
				
				$('#add_save').attr('disabled',false);
				$('#add_delete').attr('disabled',false);
			}
		});
	});
	
	if($param == 'old'){
		//เอาข้อมูลที่โหลดมาแสดง
		permission($dataLoad,$thisWindowAgent);
		
		$('#btnDocument').attr('disabled',false);
		$('#btnDocumentOption').attr('disabled',false);
		
		fnDocumentAction();
		
	}else if($param == 'new'){
		$('#btnDocument').attr('disabled',true);
		$('#btnDocumentOption').attr('disabled',true);
		
		$('#add_delete').attr('disabled',true);
		$('#add_delete').hide();
	}
}

function fnDocumentAction(){
	$contno = $('#add_contno').val();
	
	$('#btnDOSend').click(function(){ documents('ใบส่งมอบสินค้า');});
	$('#btnDOSendTax').click(function(){ documents('ใบส่งของ / ใบกำกับภาษี'); });
	$('#btnDOPrice').click(function(){ documents('ใบเสร็จรับเงิน'); });
	$('#btnDOPriceTax').click(function(){ documents('ใบเสร็จรับเงิน / ใบกำกับภาษี'); });
	
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
	
	
	$('#btnDO').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});	
	
	$('#btnDOTax').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});	
}

function permission($dataLoad,$thisWindowAgent){
	$('#add_contno').val($dataLoad.CONTNO);
	$('#add_contno').attr('readonly',true);	
	var newOption = new Option($dataLoad.LOCAT, $dataLoad.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_sdate').val($dataLoad.SDATE);
	$('#add_approve').val($dataLoad.APPVNO);
	$('#add_cuscod').attr('cuscod',$dataLoad.CUSCOD);
	$('#add_cuscod').val($dataLoad.CUSNAME);
	$('#add_inclvat').val($dataLoad.INCLVAT).trigger('change');
	$('#add_vatrt').val($dataLoad.VATRT);
	var newOption = new Option($dataLoad.PAYDESC, $dataLoad.PAYTYP, true, true);
	$('#add_paydue').empty().append(newOption).trigger('change');
	$('#add_credtm').val($dataLoad.CREDTM);
	$('#add_duedt').val($dataLoad.DUEDT);
	var newOption = new Option($dataLoad.SALNAME, $dataLoad.SALCOD, true, true);
	$('#add_salcod').empty().append(newOption).trigger('change');
	$('#add_comitn').val($dataLoad.COMITN);
	$('#add_issuno').val($dataLoad.ISSUNO);
	$('#add_issudt').val($dataLoad.ISSUDT);
	

	$('#dataTables-strno tbody').append($dataLoad.strnolist);	
	$('#add_taxno').val($dataLoad.TAXNO);
	$('#add_taxdt').val($dataLoad.TAXDT);
	$('#add_tkeyinall').val($dataLoad.TKEYIN);
	$('#add_nkeyinall').val($dataLoad.NKEYIN);
	$('#add_vkeyinall').val($dataLoad.VKEYIN);
	$('#add_smpay').val($dataLoad.SMPAY);
	
	$('#add_crdamt').val($dataLoad.CRDAMT);
	var newOption = new Option($dataLoad.ACTINAME, $dataLoad.ACTICOD, true, true);
	$('#add_acticod').empty().append(newOption).trigger('change');
	$('#add_recomcod').attr('cuscod',$dataLoad.RECOMCOD);
	$('#add_recomcod').val($dataLoad.RECOMNAME);
	$('#add_memo1').val($dataLoad.MEMO1);
	
	$('#add_sdate').attr('disabled',true);	
	$('#add_approve').attr('disabled',true);
	$('#add_cuscod').attr('disabled',true);
	$('#add_cuscod_removed').attr('disabled',true);
	$('#add_inclvat').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_vatrt').attr('disabled',true);
	$('#add_paydue').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_credtm').attr('disabled',true);
	$('#add_duedt').attr('disabled',true);
	$('#add_salcod').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_comitn').attr('disabled',true);
	$('#add_strno').attr('disabled',true);
	$('#add_crdamt').attr('disabled',true);
	$('#add_issuno').attr('disabled',true);
	$('#add_issudt').attr('disabled',true);
	$('#add_recomcod').attr('disabled',true);
	$('#add_recomcod_removed').attr('disabled',true);
	$('#add_memo1').attr('disabled',true);
	
	if(_update == 'T'){
		$('#add_save').attr('disabled',false);
		
		$('#add_issuno').attr('disabled',false);
		$('#add_issudt').attr('disabled',false);
		$('#add_recomcod').attr('disabled',false);
		$('#add_recomcod_removed').attr('disabled',false);
		$('#add_memo1').attr('disabled',false);
	}else{
		$('#add_save').attr('disabled',true);
		
		$('#add_acticod').select2({ dropdownParent: true,disabled: true,width:'100%' });
	}
	
	if(_delete == 'T'){
		$('#add_delete').attr('disabled',false);
	}else{
		$('#add_delete').attr('disabled',true);
	}
}

var jd_fnSave = null;
function fnSave($thisWindowAgent){
	dataToPost = new Object();
	dataToPost.contno 	= $('#add_contno').val();
	dataToPost.locat 	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.sdate 	= $('#add_sdate').val();
	dataToPost.approve 	= $('#add_approve').val();
	dataToPost.cuscod 	= $('#add_cuscod').attr('CUSCOD');
	dataToPost.inclvat 	= (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '':$('#add_inclvat').find(':selected').val());
	dataToPost.vatrt 	= $('#add_vatrt').val();
	dataToPost.paydue 	= (typeof $('#add_paydue').find(':selected').val() === 'undefined' ? '':$('#add_paydue').find(':selected').val());
	dataToPost.credtm 	= $('#add_credtm').val();
	dataToPost.duedt 	= $('#add_duedt').val();
	dataToPost.salcod 	= (typeof $('#add_salcod').find(':selected').val() === 'undefined' ? '':$('#add_salcod').find(':selected').val());
	dataToPost.comitn 	= $('#add_comitn').val();
	dataToPost.issuno 	= $('#add_issuno').val();
	dataToPost.issudt 	= $('#add_issudt').val();
	
	let list = new Array();
	$('.strnolist').each(function(){
		let strnolist = $(this);
		let data = new Object();
		data.strno 	= (strnolist.attr('strno'));
		data.nkeyin = (strnolist.attr('nkeyin'));
		data.vkeyin = (strnolist.attr('vkeyin'));
		data.tkeyin = (strnolist.attr('tkeyin'));
		data.vatrt  = (strnolist.attr('vatrt'));
		data.memo1	= (strnolist.attr('memo1'));
		data.issuno	= (strnolist.attr('issuno'));
		
		list.push(data);
	});
	dataToPost.strnolist = (list.length == 0 ? 'no record' : list);
	dataToPost.nkeyinall = $('#add_nkeyinall').val();
	dataToPost.tkeyinall = $('#add_tkeyinall').val();
	dataToPost.vkeyinall = $('#add_vkeyinall').val();
	
	dataToPost.crdamt 	 = $('#add_crdamt').val();
	dataToPost.acticod 	 = (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '':$('#add_acticod').find(':selected').val());
	dataToPost.recomcod	 = $('#add_recomcod').attr('CUSCOD');
	dataToPost.memo1 	 = $('#add_memo1').val();
	
	
	jd_fnSave = $.ajax({
		url:'../SYS04/Agent/save',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
					soundExt: '.ogg',
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else{
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
					soundExt: '.ogg',
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				
				$thisWindowAgent.destroy();
			}
			jd_fnSave = null;
		},
		beforeSend:function(){
			if(jd_fnSave !== null){
				jd_fnSave.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function fn_formSTRNO($thisWindow){
	$('#gf_strno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#gf_strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
				let strno = new Array();
				$('.strnolist').each(function(){
					d = new Object();
					d.strno = $(this).attr('strno');
					
					strno.push(d);
				});
				
				dataToPost.strno = (strno.length == 0 ? '':strno);
				
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
		dropdownParent: $("#gfmain"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#gf_strno').on('select2:select',function(){
		let inclvat = $('#add_inclvat').find(':selected').val();
		if(inclvat == 'Y'){
			$('#gf_tkeyin').focus();
		}else if(inclvat == 'N'){
			$('#gf_nkeyin').focus();
		}
	});
	
	$('#gf_nkeyin').keyup(function(e){
		if(e.keyCode === 13){
			fnCalPrice();
		}
	});
	
	$('#gf_tkeyin').keyup(function(e){
		if(e.keyCode === 13){
			fnCalPrice();
		}
	});
	
	$('#btngf_receipt').unbind('click');
	$('#btngf_receipt').click(function(){
		$('#loadding').fadeIn(200);
		fnCalPrice(); // คำนวน
		
		var refreshIntervalId = setInterval(function(){
			if(calprice === null){ 
				clearInterval(refreshIntervalId);
				
				data = new Object();
				data.strno 	= (typeof $('#gf_strno').find(':selected').val() === 'undefined' ? '' : $('#gf_strno').find(':selected').val());
				data.nkeyin 	= $('#gf_nkeyin').val();
				data.vkeyin = $('#gf_vkeyin').val();
				data.tkeyin = $('#gf_tkeyin').val();
				data.vatrt 	= $('#gf_vatrt').val();
				data.memo1 	= $('#gf_memo1').val();
				data.issuno = $('#gf_issuno').val();
				
				$strnoDuplicate = false;
				$('.strnolist').each(function(){
					$strnolist = $(this);
					if($strnolist.attr('strno') == data.strno){ $strnoDuplicate = true; }
				});
				
				if($strnoDuplicate){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 3000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "มีเลขตัวถัง ในรายการแล้วครับ"
					});
				}else if(data.strno == ""){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 3000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "โปรดระบุเลขตัวถังก่อนครับ"
					});
				}else{
					var row = '<tr seq="new">';
					row += "<td align='center'> ";
					row += "	<i class='strnolist btn btn-xs btn-danger glyphicon glyphicon-minus' ";
					row += "		strno='"+data.strno+"' nkeyin='"+(data.nkeyin).replace(',','')+"' ";
					row += "		vkeyin='"+(data.vkeyin).replace(',','')+"' tkeyin='"+(data.tkeyin).replace(',','')+"' ";
					row += "		vatrt='"+(data.vatrt).replace(',','')+"' memo1='"+(data.memo1)+"' ";
					row += "		issuno='"+(data.issuno)+"' ";
					row += "		style='cursor:pointer;'> ลบ   ";
					row += "	</i> ";
					row += "</td> ";
					row += "<td>"+data.strno+"</td>";
					row += "<td>"+data.nkeyin+"</td>";
					row += "<td>"+data.vkeyin+"</td>";
					row += "<td>"+data.tkeyin+"</td>";
					row += "<td>"+data.vatrt+"</td>";
					row += "<td>"+data.memo1+"</td>";
					row += "<td>"+data.issuno+"</td>";
					row += '</tr>';
					
					$('#dataTables-strno tbody').append(row);
					removeStrnoLIST();
					$thisWindow.destroy();
				}
				
				$tt_nkeyin = 0;
				$tt_vkeyin = 0;
				$tt_tkeyin = 0;
				$('.strnolist').each(function(){
					$strnolist = $(this);
					
					$tt_nkeyin += parseFloat($strnolist.attr('nkeyin'));
					$tt_vkeyin += parseFloat($strnolist.attr('vkeyin'));
					$tt_tkeyin += parseFloat($strnolist.attr('tkeyin'));
				});
				
				$('#add_nkeyinall').val($tt_nkeyin.toFixed(2));
				$('#add_vkeyinall').val($tt_vkeyin.toFixed(2));
				$('#add_tkeyinall').val($tt_tkeyin.toFixed(2));
				
				$('#loadding').fadeOut(200);
			}
		},1000);
	});
}

function removeStrnoLIST(){
	$('.strnolist').unbind('click');
	$('.strnolist').click(function(){
		$(this).parent().parent().remove();
		
		$tt_nkeyin = 0;
		$tt_vkeyin = 0;
		$tt_tkeyin = 0;
		$('.strnolist').each(function(){
			$strnolist = $(this);
			
			$tt_nkeyin += parseFloat($strnolist.attr('nkeyin'));
			$tt_vkeyin += parseFloat($strnolist.attr('vkeyin'));
			$tt_tkeyin += parseFloat($strnolist.attr('tkeyin'));
		});
		
		$('#add_nkeyinall').val($tt_nkeyin.toFixed(2));
		$('#add_vkeyinall').val($tt_vkeyin.toFixed(2));
		$('#add_tkeyinall').val($tt_tkeyin.toFixed(2));		
	});
}

var calprice = null;
function fnCalPrice(){
	dataToPost = new Object();
	dataToPost.nkeyin   = $('#gf_nkeyin').val();
	dataToPost.tkeyin  = $('#gf_tkeyin').val();
	dataToPost.inclvat = $('#gf_vatrt').attr('inclvat');
	dataToPost.vatrt   = $('#gf_vatrt').val();
	
	calprice = $.ajax({
		url:'../SYS04/Agent/fnCalPrice',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success: function(data){
			$('#gf_nkeyin').val(data.nkeyin);
			$('#gf_vkeyin').val(data.vatin);
			$('#gf_tkeyin').val(data.tkeyin);
			
			calprice = null;
		},
		beforeSend: function(){
			if(calprice !== null){ calprice.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
























