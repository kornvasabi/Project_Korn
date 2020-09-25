/********************************************************
             ______@03/02/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	
});
var btn_formadd = null;
$('#btnaddform').click(function(){
	$('#loadding').fadeIn(200);
	btn_formadd = $.ajax({
		url: '../SYS04/Accessory/getformAccessory',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'บันทึกรายการขายอุปกรณ์เสริม',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard('new','',$this);
				},
				beforeClose : function(){
					//$('#btnt1agent').attr('disabled',false);
				}
			});
			btn_formadd = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(btn_formadd !== null){btn_formadd.abort();}
		}
	});
});

$('#btnsearchlist').click(function(){
	Searchlist();
});
var kb_btnsearchlish = null;
function Searchlist(){
	dataToPost = new Object();
	dataToPost.CONTNO    = $('#CONTNO').val();
	dataToPost.SDATEFRM  = $('#SDATEFRM').val();
	dataToPost.SDATETO   = $('#SDATETO').val();
	dataToPost.LOCAT     = $('#LOCAT').val();
	$('#loadding').fadeIn(200);
	kb_btnsearchlish = $.ajax({
		url: '../SYS04/Accessory/Search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			
			$('#result').html(data.html);
			
			$('#table-accessory').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-accessory',1,400);
			
			kb_btnsearchlish = null;	
		},
		beforeSend: function(){
			if(kb_btnsearchlish !== null){kb_btnsearchlish.abort();}
		}
	});
}
var kb_loadACS = null;
function redraw(){
	$('.ACSDetails').unbind('click');
	$('.ACSDetails').click(function(){
		dataToPost = new Object();
		dataToPost.contno  = $(this).attr('contno');
		kb_loadACS = $.ajax({
			url: '../SYS04/Accessory/loadACS',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				loadACS(data);
				kb_loadACS = null;	
			},
			beforeSend: function(){
				if(kb_loadACS !== null){kb_loadACS.abort();}
			}
		});
	});
}

var kb_loadformASC = null;
function loadACS($param){
	$('#loadding').fadeIn(200);
	kb_loadformASC = $.ajax({
		url:'../SYS04/Accessory/getformAccessory',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			Lobibox.window({
				title: 'แก้ไขรายการขายอุปกรณ์เสริม',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard('edit',$param,$this);
					$('#btnDOTax').click(function(){printReport('ใบกำกับภาษีอย่างย่อ');});
					$('#btnDOTaxFull').click(function(){printReport('ใบกำกับภาษีเต็ม');});
					if(_delete == 'T'){
						$('.accslist').attr('disabled',false);
					}else{
						$('.accslist').attr('disabled',true);
					}
				}
			});
			kb_loadformASC = null;
		},
		beforeSend: function(){  if(kb_loadformASC !== null){ kb_loadformASC.abort(); } }
		//,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
} 
var kb_taxReport = null;
function printReport($type){
	if($type == "ใบกำกับภาษีอย่างย่อ"){
		dataToPost = new Object();
		dataToPost.contno  = $('#add_contno').val();
		dataToPost.locat  	 = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
		kb_taxReport = $.ajax({
			url:'../SYS04/Accessory/conditiontopdf',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS04/Accessory/pdftax?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title:$type,
					content:content,
					closeOnEsc:false,
					height:$(window).height(),
					width:$(window).width()
				});
				kb_taxReport = null;
			},
			beforeSend:function(){
				if(kb_taxReport !== null){kb_taxReport.abort();}
			}
		});	
	}
}

function wizard($param,$dataLoad,$thisWindowAcs){
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
				
				var cuscod = $('#add_cuscod').val();
				
				switch(index){
					case 0: //tab1
						var msg = "";
						
						if(cuscod == ''){
							msg = "ไม่พบรหัสลูกค้า กรุณาเลือกรหัสลูกค้าก่อนครับ";
						}
						if(msg != ""){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 9000,
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
	//tab11
	$('#btn_DetailHistrory').attr('disabled',true);
	$('#btn_linkaddcus').attr('disabled',true);
	
	$('#add_contno').val('Auto Genarate');
	$('#add_contno').attr('readonly',true);
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getLOCAT',
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
		dropdownParent: $('#add_locat').parent().parent(),
		disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_cuscod_removed').click(function(){
		$('#add_cuscod').val('');
	});
	$('#btnaddcuscod').click(function(){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../Cselect2K/getformCUSTMAST',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				$('#btn_save').attr('disabled',true);
				Lobibox.window({
					title: 'FORM SEARCH',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						var kb_btnsearch = null;
						$('#btn_search').click(function(){ fnResultCUSCOD(); });
						
						function fnResultCUSCOD(){
							dataToPost = new Object();
							dataToPost.name1  = $('#name1').val();
							dataToPost.name2  = $('#name2').val();
							dataToPost.idno   = $('#idno').val();
							$('#loadding').fadeIn(200);
							kb_btnsearch = $.ajax({
								url:'../Cselect2K/getSearchCUSTMAST',
								data:dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#loadding').fadeOut(200);
									$('#cus_result').html(data.html);
									
									$('.getit').hover(function(){
										$(this).css({'background-color':'#a9a9f9'});
										$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
									},function(){
										$(this).css({'background-color':''});
										$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
									});
									$('.getit').unbind('click');
									$('.getit').click(function(){
										att = new Object();
										att.cuscod  = $(this).attr('CUSCOD');
										att.cusname = $(this).attr('CUSNAME2');
										$('#add_cuscod').attr('CUSCOD',att.cuscod);
										$('#add_cuscod').val(att.cusname);
										$this.destroy();
									});
									kb_btnsearch = null;
								},
								beforeSend: function(){
									if(kb_btnsearch !== null){ kb_btnsearch.abort(); }
								}
							});
						}
					},
					beforeClose : function(){
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
						if(_insert == "T"){
							$('#btn_save').attr('disabled',false);
						}else{
							$('#btn_save').attr('disabled',true);
						}
					}
				});
			}
		});
	});
	$("#add_inclvat").select2({
        placeholder: 'เลิอก',		
        minimumResultsForSearch: -1,
        //dropdownParent: $("#add_inclvat").parent().parent(),
        width: '100%'
    });
	$('#add_salecod').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
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
	
	//tab22	
	$('#get_taxno').val('Auto Genarate');
	var acs_add_optcod = null;
	$('#add_optcod').click(function(){
		$('#add_optcod').attr('disabled',true);
		$('#btn_save').attr('disabled',true);
		$('.accslist').attr('disabled',true);
		dataToPost = new Object();
		dataToPost.inclvat = $('#add_inclvat').val();
		dataToPost.vatrt   = $('#add_vatrt').val();
		acs_add_optcod = $.ajax({
			url:'../SYS04/Accessory/getFormOPTCODE',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				Lobibox.window({
					title: 'เพิ่มอุปกรณ์เสริม',
					width: 600,
					height: 500,
					draggable: true,
					content: data.html,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						fn_formOPTCODE($this);
					},
					beforeClose: function(){
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
						$('#add_optcod').attr('disabled',false);
						$('#btn_save').attr('disabled',false);
						$('.accslist').attr('disabled',false);
					}
				});
				acs_add_optcod = null;
			},
			beforeSend : function(){
				if(acs_add_optcod !== null){ acs_add_optcod.abort(); }
			}
			//,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	$('#btn_save').click(function(){
		//$('#btn_save').attr('disabled',true);
		//$('#btn_delete').attr('disabled',true);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			//closeButton: false,
			msg: 'คุณต้องการบันทึกรายการขายอุปกรณ์เสริมหรือไม่ ?',
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
				if (type === 'ok'){ fnSave($thisWindowAcs); }
				
				//$('#btn_save').attr('disabled',false);
				//$('#btn_delete').attr('disabled',false);
			},
			beforeClose: function(){
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				//$('#btn_save').attr('disabled',false);
				//$('#btn_delete').attr('disabled',false);
			}
		});
	});
	// $param โหลดฟอร์มเพิ่มรายการอุปกรณ์เสริมและ แก้ไขอุปกรณ์เสริม
	if($param == 'edit'){
		if(_update == 'T'){
			$('#btn_save').attr('disabled',false);
			$('#add_optcod').attr('disabled',false);
		}else{
			$('#btn_save').attr('disabled',true);
			$('#add_optcod').attr('disabled',true);
		}
		if(_delete == 'T'){
			$('#btn_delete').attr('disabled',false);
			$('.accslist').attr('disabled',false);
		}else{
			$('#btn_delete').attr('disabled',true);
			$('.accslist').attr('disabled',true);
		}
		Load_Data($dataLoad,$thisWindowAcs);
		$('#btnDOTaxFull').attr('disabled',true);
		$('#btnDOSend').click(function(){
			fn_printreport();
		});
	}else if($param == 'new'){
		$('#btnDocument').attr('disabled',true);
		$('#btnDocumentOption').attr('disabled',true);
		
		$('#btn_delete').attr('disabled',true);
		$('#btnDOSend').attr('disabled',true);
		
		if(_insert == 'T'){
			$('#btn_save').attr('disabled',false);
			$('#add_optcod').attr('disabled',false);
		}else{
			$('#btn_save').attr('disabled',true);
			$('#add_optcod').attr('disabled',true);
		}
	}
	
	if($param == 'edit'){
		$('#btn_delete').click(function(){
			var cont = $('#add_contno').val();
			//$('#btn_save').attr('disabled',true);
			//$('#btn_delete').attr('disabled',true);
			Lobibox.confirm({
				title: 'ยืนยันการทำรายการ',
				iconClass: false,
				//closeButton: false,
				msg: 'คุณต้องการลบรายการขายอุปกรณ์เสริมเลขที่ <span style="color:red;font-size:18pt;">'+cont+'</span>หรือไม่ ?',
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
						fnDelete($thisWindowAcs); 
					}
					
					//$('#btn_save').attr('disabled',false);
					//$('#btn_delete').attr('disabled',false);
				},
				beforeClose: function(){
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
					//$('#btn_save').attr('disabled',false);
					//$('#btn_delete').attr('disabled',false);
				}
			});
		});	
	}
}
function Load_Data($dataLoad,$thisWindowAcs){
	$('#add_sdate').attr('disabled',true);
	$('#add_cuscod').attr('disabled',true);
	
	$('#btnaddcuscod').attr('disabled',true);
	$('#add_cuscod_removed').attr('disabled',true);
	$('#add_inclvat').attr('disabled',true);
	
	$('#add_vatrt').attr('disabled',true);
	$('#add_credtm').attr('disabled',true);
	$('#add_duedt').attr('disabled',true);
	
	$('#add_salecod').attr('disabled',true);
	$('#add_comitn').attr('disabled',true);
	
	var newOption = new Option($dataLoad.LOCAT, $dataLoad.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_contno').val($dataLoad.CONTNO);
	$('#add_sdate').val($dataLoad.SDATE);
	$('#add_cuscod').val($dataLoad.CUSNAME);
	$('#add_cuscod').attr('cuscod',$dataLoad.CUSCOD);
	$('#add_inclvat').val($dataLoad.INCLVAT).trigger('change'); 
	$('#add_vatrt').val($dataLoad.VATRT);
	$('#add_credtm').val($dataLoad.CREDTM);
	$('#add_duedt').val($dataLoad.DUEDT);
	var newOption = new Option($dataLoad.TAXSAL, $dataLoad.SALCOD, true, true);
	$('#add_salecod').empty().append(newOption).trigger('change');
	$('#add_comitn').val($dataLoad.COMITN);
	
	$('#dataTables-acce tbody').append($dataLoad.accslist);	
	
	$('#get_taxno').val($dataLoad.TAXNO);
	$('#get_taxdt').val($dataLoad.TAXDT);
	$('#sum_optptot').val($dataLoad.OPTPTOT);
	$('#sum_optprc').val($dataLoad.OPTPRC);
	$('#sum_vatrt').val($dataLoad.OPTPVT);
	$('#add_memo1').val($dataLoad.MEMO1);
	
	$('.accslist').unbind('click');
	$('.accslist').click(function(){
		var del = $(this);
		removeacslist(del);
	});
}

function fn_formOPTCODE($this){
	$('#fm_optprc').attr('disabled',true);
	$('#fm_vatrt').attr('disabled',true);
	$('#fm_t_optptot').attr('disabled',true);
	
	$('#fm_optcvt').attr('disabled',true);
	$('#fm_optctot').attr('disabled',true);
	$('#fm_optcode').select2({
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fm_optcode').change(function(){
		var ch_optcode = false;
		$('.accslist').each(function(){
			var optcode = $(this).attr('optcode');
			if($('#fm_optcode').val() == optcode){
				ch_optcode = true;	
			}
		});
		if(ch_optcode){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 3000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "มีรหัสอุปกรณ์แล้วครับ"
			});
			$('#fm_optcode').empty();
		}
	});
	
	$('#btn_addlistopt').unbind('click');
	$('#btn_addlistopt').click(function(){
		$('#loadding').fadeIn(200);
		fnCalPrice($this);
	});
}
var CalPrice = null;
function fnCalPrice($this){
	dataToPost = new Object();
	dataToPost.optcode   = $('#fm_optcode').val();
	dataToPost.inclvat   = $('#add_inclvat').val();
	dataToPost.vatrt     = $('#add_vatrt').val();
	dataToPost.optptot   = $('#fm_optptot').val();
	dataToPost.count_acs = $('#fm_count').val();
	dataToPost.capitalvalue = $('#fm_optcst').val();
	CalPrice = $.ajax({
		url:'../SYS04/Accessory/fnCalPrice',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data) {
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
				$('#loadding').fadeOut(200);
			}else{
				$('#fm_optptot').val(data.optptot);
				$('#fm_optcst').val(data.optcst);
				
				$('#fm_optprc').val(data.optprc);
				$('#fm_vatrt').val(data.vatrt);
				$('#fm_t_optptot').val(data.t_optptot);
				
				$('#fm_optcvt').val(data.optcvt);
				$('#fm_optctot').val(data.optctot);
				
				CalPrice = null;
				AddListTable($this);
			}
		},
		beforeSend: function(){
			if(CalPrice !== null){ CalPrice.abort();}
		}
	});
}
function AddListTable($this){
	var clear_Interval = setInterval(function(){
		if(CalPrice === null){
			clearInterval(clear_Interval);
			var optcode   = (typeof $('#fm_optcode').find(':selected').val() === 'undefined' ? "":$('#fm_optcode').find(':selected').val());				
			var optptot   = $('#fm_optptot').val();
			var count_acs = $('#fm_count').val();
			var optprc    = $('#fm_optprc').val();
			var vatrt     = $('#fm_vatrt').val();
			var t_optptot = $('#fm_t_optptot').val();
			var optcst    = $('#fm_optcst').val();
			var optcvt    = $('#fm_optcvt').val();
			var optctot   = $('#fm_optctot').val();
			
			var row = '<tr>';
			row += "<td align='center'>";
			row += "	<i class ='accslist btn btn-xs btn-danger glyphicon glyphicon-minus' ";
			row += "		optcode='"+optcode+"' optptot='"+(optptot).replace(',','')+"' ";
			row += "		count_acs='"+count_acs+"' optprc='"+(optprc).replace(',','')+"' ";
			row += "		vatrt='"+vatrt+"' t_optptot='"+(t_optptot).replace(',','')+"' ";
			row += "		optcst='"+(optcst).replace(',','')+"' optcvt='"+optcvt+"' ";
			row += "		optctot='"+(optctot).replace(',','')+"' style='cursor:pointer;'> ลบ";
			row += "	</i> ";
			row += "</td>";
			row += "<td align='right'>"+optcode+"</td>";
			row += "<td align='right'>"+optptot+"</td>";
			row += "<td align='right'>"+count_acs+"</td>";
			row += "<td align='right'>"+optprc+"</td>";
			row += "<td align='right'>"+vatrt+"</td>";
			row += "<td align='right'>"+t_optptot+"</td>";
			row += "<td align='right'>"+optcst+"</td>";
			row += "<td align='right'>"+optcvt+"</td>";
			row += "<td align='right'>"+optctot+"</td>";
			row += '</tr>';
			
			$('#dataTables-acce tbody').append(row);
			$this.destroy();
			
			$('.accslist').unbind('click');
			$('.accslist').click(function(){
				var del = $(this);
				removeacslist(del);
			});
		}
		var tt_optptot = 0;
		var tt_optprc  = 0;
		var tt_vatrt   = 0;	

		$('.accslist').each(function(){
			var accslist = $(this);
			
			tt_optptot += parseFloat(accslist.attr('t_optptot')); //fn_parseFloat แปลงข้อความให้เป็นตัวเลข int
			tt_optprc  += parseFloat(accslist.attr('optprc'));
			tt_vatrt   += parseFloat(accslist.attr('vatrt'));
		});	
		$('#sum_optptot').val(tt_optptot.toFixed(2)); //toFixed ทศนิยม
		$('#sum_optprc').val(tt_optprc.toFixed(2));
		$('#sum_vatrt').val(tt_vatrt.toFixed(2));
		
		$('#loadding').fadeOut(200);			
	},1000);
}

function removeacslist($del){
	$del.parent().parent().remove();
	var tt_optptot = 0;
	var tt_optprc  = 0;
	var tt_vatrt   = 0;	

	$('.accslist').each(function(){
		var accslist = $(this);
		tt_optptot += parseFloat(accslist.attr('t_optptot')); //fn_parseFloat แปลงข้อความให้เป็นตัวเลข int
		tt_optprc  += parseFloat(accslist.attr('optprc'));
		tt_vatrt   += parseFloat(accslist.attr('vatrt'));
	});	
	$('#sum_optptot').val(tt_optptot.toFixed(2)); //fn_toFixed ทศนิยม
	$('#sum_optprc').val(tt_optprc.toFixed(2));
	$('#sum_vatrt').val(tt_vatrt.toFixed(2));
	/*
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		//closeButton: false,
		msg: 'คุณต้องการลบรายการอุปกรณ์เสริมหรือไม่',
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
		callback: function(lobibox, type){
			if (type === 'ok'){ 
				$del.parent().parent().remove();
				//removeacslist(); 
				var tt_optptot = 0;
				var tt_optprc  = 0;
				var tt_vatrt   = 0;	

				$('.accslist').each(function(){
					var accslist = $(this);
					
					tt_optptot += parseFloat(accslist.attr('t_optptot')); //fn_parseFloat แปลงข้อความให้เป็นตัวเลข int
					tt_optprc  += parseFloat(accslist.attr('optprc'));
					tt_vatrt   += parseFloat(accslist.attr('vatrt'));
				});	
				$('#sum_optptot').val(tt_optptot.toFixed(2)); //fn_toFixed ทศนิยม
				$('#sum_optprc').val(tt_optprc.toFixed(2));
				$('#sum_vatrt').val(tt_vatrt.toFixed(2));
			}
		},
		beforeClose: function(){
			
		}
	});	
	*/
}
var kb_fnSave = null;
function fnSave($thisWindowAcs){
	dataToPost = new Object();
	dataToPost.contno    = $('#add_contno').val();
	dataToPost.locat  	 = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.sdate  	 = $('#add_sdate').val();
	dataToPost.cuscod    = $('#add_cuscod').attr('CUSCOD');
	dataToPost.inclvat   = $('#add_inclvat').val();
	dataToPost.vatrt     = $('#add_vatrt').val();
	dataToPost.credtm    = $('#add_credtm').val();
	dataToPost.duedt     = $('#add_duedt').val();
	dataToPost.salecod   = (typeof $('#add_salecod').find(':selected').val() === 'undefined' ? '':$('#add_salecod').find(':selected').val());
	dataToPost.comitn    = $('#add_comitn').val();
	
	var listacs = [];
	$('.accslist').each(function(){
		var list = [];
		list.push($(this).attr('optcode'));
		list.push($(this).attr('optptot'));
		list.push($(this).attr('count_acs'));
		list.push($(this).attr('optprc'));
		list.push($(this).attr('vatrt'));
		list.push($(this).attr('t_optptot'));
		list.push($(this).attr('optcst'));
		list.push($(this).attr('optcvt'));
		list.push($(this).attr('optctot'));
		listacs.push(list);
	});
	//alert(listacs);
	dataToPost.listacs = (listacs.length == 0 ? 'no listacs':listacs);
	
	dataToPost.taxno       = $('#get_taxno').val();
	dataToPost.tt_optptot  = $('#sum_optptot').val();
	dataToPost.tt_optprc   = $('#sum_optprc').val();
	dataToPost.tt_vatrt    = $('#sum_vatrt').val();
	dataToPost.memo1       = $('#add_memo1').val();
	
	kb_fnSave = $.ajax({
		url:'../SYS04/Accessory/Save',
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
				$thisWindowAcs.destroy();
				Searchlist();
			}
			kb_fnSave = null;
		},
		beforeSend:function(){
			if(kb_fnSave !== null){
				kb_fnSave.abort();
			}
		}
		,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
var kb_fnDel = null;
function fnDelete($thisWindowAcs){
	dataToPost = new Object();
	dataToPost.contno    = $('#add_contno').val();
	dataToPost.locat  	 = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.sdate  	 = $('#add_sdate').val();
	dataToPost.cuscod    = $('#add_cuscod').attr('CUSCOD');
	dataToPost.taxno     = $('#get_taxno').val();
	
	var listacs = [];
	$('.accslist').each(function(){
		var list = [];
		list.push($(this).attr('optcode'));
		list.push($(this).attr('optptot'));
		list.push($(this).attr('count_acs'));
		list.push($(this).attr('optprc'));
		list.push($(this).attr('vatrt'));
		list.push($(this).attr('t_optptot'));
		list.push($(this).attr('optcst'));
		list.push($(this).attr('optcvt'));
		list.push($(this).attr('optctot'));
		listacs.push(list);
	});
	//alert(listacs);
	dataToPost.listacs = listacs;
	kb_fnDel = $.ajax({
		url:'../SYS04/Accessory/DeleteAcs',
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
				$thisWindowAcs.destroy();
				Searchlist();
			}
			kb_fnDel = null;
		},
		beforeSend:function(){
			if(kb_fnDel !== null){
				kb_fnDel.abort();
			}
		}
		//,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

