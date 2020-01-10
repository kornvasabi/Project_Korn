/********************************************************
             ______@26/11/2019______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#CUSCOD1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCUSTOMER',
			data: function (params){
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
	$('#CONTNO1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCONTNO',
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
	$('#STRNO1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getSIRNO',
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
	$('#REGNO1').select2({
		placeholder: 'เลือก',
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#SPECSCAR_A').hide();
	//$('#statuscar').hide();
	
	
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	function initPage(){
		$('#wizard-financedetail').bootstrapWizard({
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
				switch(index){
					case 0: //tab1
						nextTab(ind2); 
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
					case 5: //tab6	
						nextTab(ind2);
						break;
					case 6: //tab7
						nextTab(ind2);
						break;
					case 7: //tab8
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
});
function cleardata(){
	$('#1_SNAM').val('');
	$('#1_NAME1').val('');
	$('#1_NAME2').val('');
	$('#1_COUNTCONTNO').val('');
	$('#1_SUMTOTPRC').val('');
	$('#1_SUMSMPAY').val('');
	$('#1_SUMBALANCE').val('');
	$('#1_COUNTCUSCOD_ISR').val('');
	$('#1_SUMBALANCE_ISR').val('');
	
	$('#2_CONTNO').val('');
	$('#2_LOCAT').val('');
	$('#2_STRNO').val('');
	$('#2_ENGNO').val('');
	$('#2_NADDCOST').val('');
	$('#2_TYPE').val('');
	$('#2_MODEL').val('');
	$('#2_BAAB').val('');
	$('#2_COLOR').val('');
	$('#2_CC').val('');
	$('#2_STAT').val('');
	$('#2_APNAME').val('');
	$('#2_GDESC').val('');
	$('#2_MANUYR').val('');
	
	$('#2_REGNO').val('');
	$('#2_REGYEAR').val('');
	$('#2_REGEXP').val('');
	$('#2_REGTYP').val('');
	$('#2_GARFRM').val('');
	$('#2_GAREXP').val('');
	$('#2_GARNO3').val('');
	$('#2_GAR3FRM').val('');
	$('#2_GAR3EXP').val('');
	
	$('#2_COUNTSTRNO').val('');
	$('#2_SUMNPRICE').val('');
	
	$('#3_CONTNO').val('');
	$('#3_LOCAT').val('');
	$('#3_SDATE').val('');
	$('#3_TOTPRC').val('');
	$('#3_SMPAY').val('');
	$('#3_REMAIN').val('');
	$('#3_REMAIN').val('');
	$('#3_SMCHQ').val('');
	$('#3_LPAYD').val('');
	$('#3_LPAYA').val('');
	$('#3_TOTDWN').val('');
	$('#3_PAYDWN').val('');
	$('#3_KDWN').val('');
	$('#3_EXP_AMT').val('');
	$('#3_EXP_FRM').val('');
	$('#3_EXP_TO').val('');
	$('#3_EXP_PRD').val('');
	$('#3_CONTSTAT').val('');
	$('#3_PAYTYP').val('');
	$('#3_T_NOPAY').val('');
	$('#3_CALINT').val('');
	$('#3_CALDSC').val('');
	$('#3_DELYRT').val('');
	$('#3_DLDAY').val('');
	$('#3_ADDRNO').val('');
	$('#3_MEMO1').val('');
	$('#3_OTHR').val('');
	
	$('#3_NCSHPRC').val('');
	$('#3_NPRICE').val('');
	$('#3_INTRT').val('');
	$('#3_GRDCOD').val('');
	$('#3_BILLCOLL').val('');
	$('#4_CONTNO').val('');
	$('#4_LOCAT').val('');
	$('#4_COUNTTMBILL').val('');
	$('#4_TOTPRC').val('');
	$('#4_REMAIN').val('');
	$('#4_REMAIN_1').val('');
	$('#4_SMPAY').val('');
	$('#4_SMCHQ').val('');
	
	$('#5_CUSCOD').val('');
	$('#5_GROUP1').val('');
	$('#5_GRADE').val('');
	$('#5_BIRTHDT').val('');
	$('#5_NICKNM').val('');
	$('#5_ISSUBY').val('');
	$('#5_ISSUDT').val('');
	$('#5_EXPDT').val('');
	$('#5_AGE').val('');
	$('#5_OCCUP').val('');
	$('#5_OFFIC').val('');
	$('#5_MAXCRED').val('');
	$('#5_YINCOME').val('');
	$('#5_MREVENU').val('');
	$('#5_YREVENU').val('');
	$('#5_ADDRNO3').val('');
	$('#5_MEMO1').val('');
	
	$('#6_CONTNO').val('');
	$('#6_LOCAT').val('');
	$('#6_GROUP1').val('');
	$('#6_GRADE').val('');
	$('#6_BIRTHDT').val('');
	$('#6_NICKNM').val('');
	$('#6_IDCARD').val('');
	$('#6_IDNO').val('');
	$('#6_ISSUBY').val('');
	$('#6_ISSUDT').val('');
	$('#6_EXPDT').val('');
	$('#6_AGE').val('');
	$('#6_OCCUP').val('');
	$('#6_OFFIC').val('');
	$('#6_YINCOME').val('');
	$('#6_MAXCRED').val('');
	$('#6_YREVENU').val('');
	$('#6_MREVENU').val('');
	$('#6_ADDRNO3').val('');
	$('#6_MEMO1').val('');
	
	//tab7
	$('#7_CONTNO').val('');
	$('#7_LOCAT').val('');
	//tab8
	$('#8_CONTNO').val('');
}
$('#btnsearch').click(function(){
	cleardata();
	search();
});
$('#btnmsg').click(function(){
	message();
});

function search(){
	dataToPost = new Object();
	dataToPost.CUSCOD1 	  = (typeof $('#CUSCOD1').find(':selected').val() === 'undefined' ? '':$('#CUSCOD1').find(':selected').val());
	dataToPost.CONTNO1 	  = (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.STRNO1 	  = (typeof $('#STRNO1').find(':selected').val() === 'undefined' ? '':$('#STRNO1').find(':selected').val());
	dataToPost.tab11C     = [$('#tab11C').is(':checked'),$('#tab11C').val()];
	$('#loadding').show();
	if(dataToPost.CUSCOD1 == '' && dataToPost.CONTNO1 == '' && dataToPost.STRNO1 == ''){
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
			msg: 'กรุณาระบุเงื่อนไขเพื่อสอบถามก่อนครับ'
		});
		$('#loadding').hide();
	}else{
		
		$('#dataTables-cusbuy tbody').html('');
		$('#dataTables-cusbuy tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		
		$('#dataTables-insurance tbody').html('');
		$('#dataTables-insurance tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		
		$.ajax({
			url:'../SYS06/ReportFinance/search',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				
				var TSALE = data.TSALE;
				/*if(TSALE != 'H'){ 
					$('#btncostcar').attr('disabled',true);
					$('#btndiscount').attr('disabled',true);
				}else{
					$('#btncostcar').attr('disabled',false);
					$('#btndiscount').attr('disabled',false);
				}
				*/
				$('#dataTables-cusbuy tr').click(function(e) {
					$('#dataTables-cusbuy tr').removeClass('highlighted');
					$(this).addClass('highlighted');
				});
				
				$('#dataTables-insurance tr').click(function(e) {
					$('#dataTables-insurance tr').removeClass('highlighted');
					$(this).addClass('highlighted');
				});
				
				loaddata(data,TSALE);
				$('#loadding').hide();
				$('.getit').click(function(){
					var	CUSCODS = $(this).attr('CUSCOD');
					var	CONTNOS = $(this).attr('CONTNO');
					var STRNOS	= $(this).attr('STRNO');
					var LOCATS  = $(this).attr('LOCAT');
					var TSALES  = $(this).attr('TSALE');
					if(data.numrow > 1){
						changedata(CUSCODS,CONTNOS,STRNOS,LOCATS,TSALES);
					}
				});
				$('.getstrno').click(function(){
					var STRNOS = $(this).attr('STRNO');
					if(data.numrow1 > 1){
						changedatacar(STRNOS);
					}
				});
				
				$('.getspt').click(function(){
					var CUSCODSPT = $(this).attr('CUSCODSPT');
					if(data.numrow2 > 1){
						chagedataspt(CUSCODSPT);
					}
				});
				if(data.numrow1 < 1){
					$('#SPECSCAR').show();
					$('#SPECSCAR_A').hide();
				}else{
					$('#SPECSCAR').hide();
					$('#SPECSCAR_A').show();
				}
			}
		});
	}
}
function message(){
	$('#loadding').show();
	dataToPost = new Object();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromSaveMessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกข้อความเตือน',
				//width: $(window).width(),
				//height: $(window).height(),
				width: 830,
				height: 600,
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($this){
					
					$('#col2').hide();
					
					$('#btnmsgS').attr('disabled',true);
					$('#btnmsgC').attr('disabled',true);
					$('#btnmsgD').attr('disabled',true);
					$('#N_text').attr('disabled',true);
					$('#Y_text').attr('disabled',true);
					$('#DATESAVE').attr('disabled',true);
					$('#add_contno').attr('disabled',true);
					$('#DATESTART').attr('disabled',true);
					$('#DATEENG').attr('disabled',true);
					$('#MSGMEMO').attr('disabled',true);
					
					$('.glyphicon').click(function(){
						$('#add_contno').val('');
					});
					$('#btnmsgI').click(function(){
						insertmsg();
					});
					$('#btnmsgC').click(function(){
						cancelmsg();
					});
					$('#add_contno').click(function(){
						addcontno();
					});
					$('#btnmsgS').click(function(){
						savemsgalert($this);
					});
					$('#btnmsgQ').click(function(){
						selectmsg();
					});
					$('#btnmsgD').click(function(){
						//alert($('#add_contno').val());
						delmsgalert();
					});
					
					if(_insert == 'T'){
						$('#btnmsgI').attr('disabled',false);	
					}else{
						$('#btnmsgI').attr('disabled',true);	
					}
					
				}
			});
		}
	}); 
}
function insertmsg(){
	dataToPost = new Object();//$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromSaveMessage',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#add_contno').val('');
			$('#MSGMEMO').val('');
			$('#btnmsgD').attr('disabled',true);
			$('#btnmsgI').attr('disabled',true);
			$('#btnmsgQ').attr('disabled',true);
			$('#btnmsgS').attr('disabled',false);
			$('#btnmsgC').attr('disabled',false);
			$('#N_text').attr('disabled',false);
			$('#Y_text').attr('disabled',false);
			$('#DATESAVE').attr('disabled',false);
			$('#add_contno').attr('disabled',false);
			$('#DATESTART').attr('disabled',false);
			$('#DATEENG').attr('disabled',false);
			$('#MSGMEMO').attr('disabled',false);
			
			$('#DATESAVE').val(data.DATESAVE);
			$('#DATESTART').val(data.DATESTART);
			$('#DATEENG').val(data.DATEENG);
			if(data.userid == "แดง"){
				$('#col1').hide();
				$('#col2').show();
			}else{
				$('#col1').show();
				$('#col2').hide();
			}
		}
	});
}
function cancelmsg(){
	$('#add_contno').val('');
	$('#MSGMEMO').val('');
	$('#DATESAVE').val('');
	$('#DATESTART').val('');
	$('#DATEENG').val('');
	
	$('#btnmsgI').attr('disabled',false);
	$('#btnmsgQ').attr('disabled',false);
	$('#btnmsgS').attr('disabled',true);
	$('#btnmsgC').attr('disabled',true);
	$('#btnmsgD').attr('disabled',true);
	$('#N_text').attr('disabled',true);
	$('#Y_text').attr('disabled',true);
	$('#DATESAVE').attr('disabled',true);
	$('#add_contno').attr('disabled',true);
	$('#DATESTART').attr('disabled',true);
	$('#DATEENG').attr('disabled',true);
	$('#MSGMEMO').attr('disabled',true);
}
function addcontno(){
	$('#btnmsgD').attr('disabled',true);
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2K/getfromCONTNO',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'FORM SEARCH',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					var kb_cont_search = null;
					$('#cont_search').click(function(){ fnResultCONTNO(); });
					function fnResultCONTNO(){
						dataToPost = new Object();
						dataToPost.s_contno = $('#s_contno').val();
						dataToPost.s_name1 = $('#s_name1').val();
						dataToPost.s_name2 = $('#s_name2').val();
						
						$('#loadding').fadeIn(200);
						kb_cont_search = $.ajax({
							url:'../Cselect2K/getResultCONTNO',
							data:dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#cont_result').html(data.html);
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});
								$('.getit').unbind('click');
								$('.getit').click(function(){
									cln = new Object();
									cln.contno  = $(this).attr('CONTNO');
									$('#add_contno').val(cln.contno);
									$this.destroy();
								});
								$('#loadding').fadeOut(200);
								kb_cont_search = null;
							},
							beforeSend: function(){
								if(kb_cont_search !== null){ kb_cont_search.abort(); }
							}
						});
					}
				},
				beforeClose : function(){
					
				}
			});
			$('#loadding').fadeOut(200);
		}
	});
}
function savemsgalert($this){
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
			var btnType;
			if (type === 'ok'){
				dataToPost = new Object();
				dataToPost.CONTNO    = $('#add_contno').val();
				dataToPost.DATESAVE  = $('#DATESAVE').val();
				dataToPost.DATESTART = $('#DATESTART').val();
				dataToPost.DATEENG   = $('#DATEENG').val();
				dataToPost.MSGMEMO   = $('#MSGMEMO').val();
				dataToPost.choice 	 = $('.choice[name=edit]:checked').val();
				$.ajax({
					url:'../SYS06/ReportFinance/msgSave',
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
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						else if(data.stat){
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 8000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
								soundExt: '.ogg',
								msg: data.msg
							});
							$this.destroy();
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
								soundExt: '.ogg',
								msg: data.msg
							});
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
					soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					soundExt: '.ogg',
					msg: 'ยังไม่บันทึกรายการ'
				});
			}
		}
	});
}
function delmsgalert(){
	var CONT = $('#add_contno').val();
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: '<span style="color:red;font-size:14pt">คุณต้องการลบข้อความแจ้งเตือน รหัส :  '+CONT+' ?</span>',//"คุณต้องการลบข้อความแจ้งเตือน รหัส : "+CONT+"?",
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
			var btnType;
			if (type === 'ok'){
				dataToPost = new Object();
				dataToPost.CONTNO = $('#add_contno').val();
				$.ajax({
					url:'../SYS06/ReportFinance/msgDelete',
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
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						else if(data.stat){
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 8000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
								soundExt: '.ogg',
								msg: data.msg
							});
							$('#btnmsgD').attr('disabled',true);
							$('#add_contno').val('');
							$('#MSGMEMO').val('');
							$('#DATESAVE').val('');
							$('#DATESTART').val('');
							$('#DATEENG').val('');
							
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
								soundExt: '.ogg',
								msg: data.msg
							});
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
					soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					soundExt: '.ogg',
					msg: 'ยังไม่บันทึกรายการ'
				});
			}
		}
	});
}
function selectmsg(){
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2K/getfromSearchCONTNO',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'FORM SEARCH',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					var kb_cont_select = null;
					$('#Cont_search').click(function(){ fnResultCONTNO(); });
					function fnResultCONTNO(){
						dataToPost = new Object();
						dataToPost.S_contno = $('#S_contno').val();
						dataToPost.S_strno = $('#S_strno').val();
						
						$('#loadding').fadeIn(200);
						kb_cont_select = $.ajax({
							url:'../Cselect2K/getSearchCONTNO',
							data:dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#Cont_result').html(data.html);
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});
								
								$('.getit').unbind('click');
								$('.getit').click(function(){
									dataToPost = new Object();
									dataToPost.userid = $(this).attr('USERID');
									$.ajax({
										url:'../SYS06/ReportFinance/getfromSaveMessage',
										data:dataToPost,
										type:'POST',
										dataType:'json',
										success:function(data){
											if(data.userid == "แดง"){
												$('#col1').hide();
												$('#col2').show();
											}else{
												$('#col1').show();
												$('#col2').hide();
											}
										}
									});
									
									data = new Object();
									data.contno   = $(this).attr('CONTNO');
									data.createdt = $(this).attr('CREATEDT');
									data.startdt  = $(this).attr('STARTDT');
									data.enddt    = $(this).attr('ENDDT');
									data.memo1    = $(this).attr('MEMO1');
									data.userid   = $(this).attr('USERID');
									$('#add_contno').val(data.contno);
									$('#DATESAVE').val(data.createdt);
									$('#DATESTART').val(data.startdt);
									$('#DATEENG').val(data.enddt);
									$('#MSGMEMO').val(data.memo1);
									if(data.userid == 'แดง'){
										document.getElementById("N_text").checked = true;
									}else if(data.userid == 'น้ำเงิน'){
										document.getElementById("Y_text").checked = true;
									}
									$this.destroy();
								});
								
								$('#btnmsgD').attr('disabled',false);
								
								if(_delete == 'T'){
									$('#btnmsgD').attr('disabled',false);	
								}else{
									$('#btnmsgD').attr('disabled',true);	
								}
								
								$('#loadding').fadeOut(200);
								kb_cont_select = null;
							},
							beforeSend: function(){
								if(kb_cont_select !== null){ kb_cont_select.abort(); }
							}
						});
					}
				},
				beforeClose : function(){
					
				}
			});
			$('#loadding').fadeOut(200);
		}
	});
}
function loaddata($data){
	if($data.MSGMEMO != 'none'){
		alertmessage($data.CONTNO,$data.MSGLOCAT,$data.STARTDT,$data.ENDDT,$data.MSGMEMO,$data.USERID);
	}
	//tab 1
	$('#dataTables-cusbuy tbody').empty().append($data.cusbuy);
	document.getElementById("cusbuy").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	$('#1_SNAM').val($data.SNAM);
	$('#1_NAME1').val($data.NAME1);
	$('#1_NAME2').val($data.NAME2);
	$('#1_COUNTCONTNO').val($data.COUNTCONTNO);
	$('#1_SUMTOTPRC').val($data.SUMTOTPRC);
	$('#1_SUMSMPAY').val($data.SUMSMPAY);
	$('#1_SUMBALANCE').val($data.SUMBALANCE);
	
	$('#dataTables-insurance tbody').empty().append($data.insurance);
	$('#1_COUNTCUSCOD_ISR').val($data.COUNTCUSCOD_ISR);
	$('#1_SUMBALANCE_ISR').val($data.SUMBALANCE_ISR);
	
	//tab 2
	$('#2_CONTNO').val($data.CONTNO_2);
	$('#2_LOCAT').val($data.LOCAT_2);
	
	$('#2_STRNO').val($data.STRNO_2);
	$('#2_ENGNO').val($data.ENGNO_2);
	$('#2_NADDCOST').val($data.NADDCOST_2);
	
	$('#2_REGNO').val($data.REGNO_2);
	
	//$('#2_REGPAY').val($data.REGPAY_2);
	$('#2_REGYEAR').val($data.REGYEAR_2);
	$('#2_REGEXP').val($data.REGEXP_2);
	$('#2_REGTYP').val($data.REGTYP_2);
	$('#2_GARFRM').val($data.GARFRM_2);
	$('#2_GAREXP').val($data.GAREXP_2);
	$('#2_GARNO3').val($data.GARNO3_2);
	$('#2_GAR3FRM').val($data.GAR3FRM_2);
	$('#2_GAR3EXP').val($data.GAR3EXP_2);
	
	$('#2_TYPE').val($data.TYPE_2);
	$('#2_MODEL').val($data.MODEL_2);
	$('#2_BAAB').val($data.BAAB_2);
	$('#2_COLOR').val($data.COLOR_2);
	$('#2_CC').val($data.CC_2);
	
	if($data.STAT_2 == 'O'){
		$('#2_STAT').val('เก่า');
	}else if($data.STAT_2 == 'N'){
		$('#2_STAT').val('ใหม่');
	}
	$('#2_APNAME').val($data.APNAME_2);
	$('#2_GDESC').val($data.GDESC_2);	
	$('#2_MANUYR').val($data.MANUYR_2);
	
	document.getElementById("dataTable-fixed-accessory").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	$('#dataTables-accessory tbody').empty().append($data.acce);
	$('#2_COUNTOPTCODE').val($data.COUNTOPTCODE_2);
	$('#2_SUMTOTPRC').val($data.SUMTOTPRC_2);
	
	document.getElementById("dataTable-fixed-listcar").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	$('#dataTables-listcar tbody').empty().append($data.list);
	$('#2_COUNTSTRNO').val($data.COUNTSTRNO_2);
	$('#2_SUMNPRICE').val($data.SUMNPRICE_2);
	
	
	//tab3
	$('#3_CONTNO').val($data.CONTNO_3);
	$('#3_LOCAT').val($data.LOCAT_3);
	$('#3_SDATE').val($data.SDATE_3);
	$('#3_TOTPRC').val($data.TOTPRC_3);
	$('#3_SMPAY').val($data.SMPAY_3);
	$('#3_REMAIN').val($data.REMAIN_3);
	$('#3_SMCHQ').val($data.SMCHQ_3);
	$('#3_LPAYD').val($data.LPAYD_3);
	$('#3_LPAYA').val($data.LPAYA_3);
	$('#3_TOTDWN').val($data.TOTDWN_3);
	$('#3_PAYDWN').val($data.PAYDWN_3);
	$('#3_KDWN').val($data.KDWN_3);
	$('#3_EXP_AMT').val($data.EXP_AMT_3);
	$('#3_EXP_FRM').val($data.EXP_FRM_3);
	$('#3_EXP_TO').val($data.EXP_TO_3);
	$('#3_EXP_PRD').val($data.EXP_PRD_3);
	
	$('#3_CONTSTAT').val($data.CONTSTAT_3);
	$('#3_PAYTYP').val($data.PAYTYP_3);
	$('#3_T_NOPAY').val($data.T_NOPAY_3);
	$('#3_CALINT').val($data.CALINT_3);
	$('#3_CALDSC').val($data.CALDSC_3);
	$('#3_DELYRT').val($data.DELYRT_3);
	$('#3_DLDAY').val($data.DLDAY_3);
	$('#3_ADDRNO').val($data.ADDRNO_3);
	
	$('#3_NCSHPRC').val($data.NCSHPRC_3);
	$('#3_NPRICE').val($data.NPRICE_3);
	$('#3_INTRT').val($data.INTRT_3);
	$('#3_GRDCOD').val($data.GRDCOD_3);
	$('#3_MEMO1').val($data.MEMO1_3);
	
	$('#3_BILLCOLL').val($data.bill);
	$('#3_CHECKER_USE').val($data.check);
	$('#3_CHECKER').val($data.check);
	$('#3_OTHR').val($data.OTHR_3);
	/*
	if($data.FL == '*'){
		$('#statuscar').show();
	}else{
		$('#statuscar').hide();
	}
	*/
	//tab4
	document.getElementById("payment").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});	
	$('#4_CONTNO').val($data.CONTNO_4);
	$('#4_LOCAT').val($data.LOCAT_4);
	$('#dataTables-payment tbody').empty().append($data.payment);
	$('#4_COUNTTMBILL').val($data.COUNTTMBILL);
	
	$('#4_TOTPRC').val($data.TOTPRC_4);
	$('#4_REMAIN').val($data.REMAIN_4);
	$('#4_REMAIN_1').val($data.REMAIN_4);
	$('#4_SMPAY').val($data.SMPAY_4);
	$('#4_SMCHQ').val($data.SMCHQ_4);
	
	//tab5
	
	$('#5_CUSCOD').val($data.CUSCOD_5);
	$('#5_GROUP1').val($data.GROUP1_5);
	$('#5_GRADE').val($data.GRADE_5);
	$('#5_BIRTHDT').val($data.BIRTHDT_5);
	$('#5_NICKNM').val($data.NICKNM_5);
	$('#5_ISSUBY').val($data.ISSUBY_5);
	$('#5_ISSUDT').val($data.ISSUDT_5);
	$('#5_EXPDT').val($data.EXPDT_5);
	$('#5_AGE').val($data.AGE_5);
	$('#5_OCCUP').val($data.OCCUP_5);
	$('#5_OFFIC').val($data.OFFIC_5);
	$('#5_MAXCRED').val($data.MAXCRED_5);
	$('#5_YINCOME').val($data.YINCOME_5);
	$('#5_MREVENU').val($data.MREVENU_5);
	$('#5_YREVENU').val($data.YREVENU_5);
	$('#5_ADDRNO3').val($data.ADDRNO3_5);
	$('#5_MEMO1ADR').val($data.MEMO1ADR);
	$('#dataTables-addrprice tbody').empty().append($data.addrprice);
	
	//tab6
	$('#6_CONTNO').val($data.CONTNO_6);
	$('#6_LOCAT').val($data.LOCAT_6);
	
	$('#dataTables-supporter tbody').empty().append($data.supporter);
	$('#6_GROUP1').val($data.GROUP1_6);
	$('#6_GRADE').val($data.GRADE_6);
	$('#6_BIRTHDT').val($data.BIRTHDT_6);
	$('#6_NICKNM').val($data.NICKNM_6);
	$('#6_IDCARD').val($data.IDCARD_6);
	$('#6_IDNO').val($data.IDNO_6);
	$('#6_ISSUBY').val($data.ISSUBY_6);
	$('#6_ISSUDT').val($data.ISSUDT_6);
	$('#6_EXPDT').val($data.EXPDT_6);
	$('#6_AGE').val($data.AGE_6);
	$('#6_OCCUP').val($data.OCCUP_6);
	$('#6_OFFIC').val($data.OFFIC_6);
	$('#6_MAXCRED').val($data.MAXCRED_6);
	$('#6_YINCOME').val($data.YINCOME_6);
	$('#6_MREVENU').val($data.MREVENU_6);
	$('#6_YREVENU').val($data.YREVENU_6);
	$('#6_ADDRNO3').val($data.ADDRNO3_6);
	$('#6_MEMO1').val($data.MEMO1_6);
	$('#dataTables-addrspt tbody').empty().append($data.addrspt);
	$('#6_MEMO1ADR').val($data.MEMO1ADR_6);
	
	//tab7
	$('#7_CONTNO').val($data.CONTNO_7);
	$('#7_LOCAT').val($data.LOCAT_7);
	document.getElementById("dataTable-fixed-compact").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	$('#dataTables-compact tbody').empty().append($data.compact);
	//tab8
	document.getElementById("dataTable-fixed-debtors").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	$('#8_CONTNO').val($data.CONTNO_8);
	$('#dataTables-debtors tbody').empty().append($data.debtors);
	$('#8_SUMTOTPRC').val($data.SUMTOTPRC_8);
	$('#8_SUMSMPAY').val($data.SUMSMPAY_8);
	$('#8_SUMBALANC').val($data.SUMBALANC_8);
	$('#8_SUMSMCHQ').val($data.SUMSMCHQ_8);
	$('#8_SUMTKANG').val($data.SUMTKANG_8);
}
function changedata($CUSCODS,$CONTNOS,$STRNOS,$LOCATS,$TSALES){
	dataToPost = new Object();
	dataToPost.CUSCODS 	= $CUSCODS;
	dataToPost.CONTNOS 	= $CONTNOS;
	dataToPost.STRNOS	= $STRNOS;
	dataToPost.LOCATS	= $LOCATS;
	dataToPost.TSALES   = $TSALES;
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/changedata',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#loadding').hide();
			/*
			if(data.TSALES != 'H'){
				$('#btncostcar').attr('disabled',true);
				$('#btndiscount').attr('disabled',true);
			}else{
				$('#btncostcar').attr('disabled',false);
				$('#btndiscount').attr('disabled',false);
			}
			*/
			changedatatab(data);
		}
	});
}
function changedatatab($data){
	
	if($data.MSGMEMO != 'none'){
		alertmessage($data.CONTNO,$data.MSGLOCAT,$data.STARTDT,$data.ENDDT,$data.MSGMEMO,$data.USERID);
	}
	$('#1_SNAM').val($data.SNAM);
	$('#1_NAME1').val($data.NAME1);
	$('#1_NAME2').val($data.NAME2);
	
	//tab2
	$('#2_CONTNO').val($data.CONTNO_2);
	$('#2_LOCAT').val($data.LOCAT_2);
	
	$('#2_STRNO').val($data.STRNO_2);
	$('#2_ENGNO').val($data.ENGNO_2);
	$('#2_NADDCOST').val($data.NADDCOST_2);
	$('#2_TYPE').val($data.TYPE_2);
	$('#2_MODEL').val($data.MODEL_2);
	$('#2_BAAB').val($data.BAAB_2);
	$('#2_COLOR').val($data.COLOR_2);
	$('#2_CC').val($data.CC_2);
	$('#2_APNAME').val($data.APNAME_2);
	$('#2_GDESC').val($data.GDESC_2);
	
	if($data.STAT_2 == 'O'){
		$('#2_STAT').val('เก่า');
	}else if($data.STAT_2 == 'N'){
		$('#2_STAT').val('ใหม่');
	}
	$('#2_MANUYR').val($data.MANUYR_2);
	
	$('#dataTables-accessory tbody').empty().append($data.acce);
	$('#2_COUNTOPTCODE').val($data.COUNTOPTCODE_2);
	$('#2_SUMTOTPRC').val($data.SUMTOTPRC_2);
	
	//tab3
	$('#3_CONTNO').val($data.CONTNO_3);
	$('#3_LOCAT').val($data.LOCAT_3);
	
	$('#3_SDATE').val($data.SDATE_3);
	$('#3_TOTPRC').val($data.TOTPRC_3);
	$('#3_SMPAY').val($data.SMPAY_3);
	$('#3_REMAIN').val($data.REMAIN_3);
	$('#3_SMCHQ').val($data.SMCHQ_3);
	$('#3_LPAYD').val($data.LPAYD_3);
	$('#3_LPAYA').val($data.LPAYA_3);
	$('#3_TOTDWN').val($data.TOTDWN_3);
	$('#3_PAYDWN').val($data.PAYDWN_3);
	$('#3_KDWN').val($data.KDWN_3);
	$('#3_EXP_AMT').val($data.EXP_AMT_3);
	$('#3_EXP_FRM').val($data.EXP_FRM_3);
	$('#3_EXP_TO').val($data.EXP_TO_3);
	$('#3_EXP_PRD').val($data.EXP_PRD_3);
	
	$('#3_CONTSTAT').val($data.CONTSTAT_3);
	$('#3_PAYTYP').val($data.PAYTYP_3);
	$('#3_T_NOPAY').val($data.T_NOPAY_3);
	$('#3_CALINT').val($data.CALINT_3);
	$('#3_CALDSC').val($data.CALDSC_3);
	$('#3_DELYRT').val($data.DELYRT_3);
	$('#3_DLDAY').val($data.DLDAY_3);
	$('#3_ADDRNO').val($data.ADDRNO_3);
	
	$('#3_NCSHPRC').val($data.NCSHPRC_3);
	$('#3_NPRICE').val($data.NPRICE_3);
	$('#3_INTRT').val($data.INTRT_3);
	$('#3_GRDCOD').val($data.GRDCOD_3);
	$('#3_MEMO1').val($data.MEMO1_3);
	
	$('#3_BILLCOLL').val($data.bill);
	$('#3_CHECKER_USE').val($data.check);
	$('#3_CHECKER').val($data.check);
	$('#3_OTHR').val($data.OTHR_3);
	
	/*if($data.statuscar == '*'){
		$('#statuscar').show();
	}else{
		$('#statuscar').hide();
	}*/
	//tab4
	document.getElementById("payment").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});	
	$('#4_CONTNO').val($data.CONTNO_4);
	$('#4_LOCAT').val($data.LOCAT_4);
	$('#dataTables-payment tbody').empty().append($data.payment);
	$('#4_COUNTTMBILL').val($data.COUNTTMBILL);
	
	$('#4_TOTPRC').val($data.TOTPRC_4);
	$('#4_REMAIN').val($data.REMAIN_4);
	$('#4_REMAIN_1').val($data.REMAIN_4);
	$('#4_SMPAY').val($data.SMPAY_4);
	$('#4_SMCHQ').val($data.SMCHQ_4);
	//tab5
	$('#5_CUSCOD').val($data.CUSCOD_5);
	$('#5_GROUP1').val($data.GROUP1_5);
	$('#5_GRADE').val($data.GRADE_5);
	$('#5_BIRTHDT').val($data.BIRTHDT_5);
	$('#5_NICKNM').val($data.NICKNM_5);
	$('#5_ISSUBY').val($data.ISSUBY_5);
	$('#5_ISSUDT').val($data.ISSUDT_5);
	$('#5_EXPDT').val($data.EXPDT_5);
	$('#5_AGE').val($data.AGE_5);
	$('#5_OCCUP').val($data.OCCUP_5);
	$('#5_OFFIC').val($data.OFFIC_5);
	$('#5_MAXCRED').val($data.MAXCRED_5);
	$('#5_YINCOME').val($data.YINCOME_5);
	$('#5_MREVENU').val($data.MREVENU_5);
	$('#5_YREVENU').val($data.YREVENU_5);
	$('#5_ADDRNO3').val($data.ADDRNO3_5);
	$('#5_MEMO1ADR').val($data.MEMO1ADR);
	$('#dataTables-addrprice tbody').empty().append($data.addrprice);
	//tab6
	$('#6_CONTNO').val($data.CONTNO_6);
	$('#6_LOCAT').val($data.LOCAT_6);
	
	$('#dataTables-supporter tbody').empty().append($data.supporter);
	$('#6_GROUP1').val($data.GROUP1_6);
	$('#6_GRADE').val($data.GRADE_6);
	$('#6_BIRTHDT').val($data.BIRTHDT_6);
	$('#6_NICKNM').val($data.NICKNM_6);
	$('#6_IDCARD').val($data.IDCARD_6);
	$('#6_IDNO').val($data.IDNO_6);
	$('#6_ISSUBY').val($data.ISSUBY_6);
	$('#6_ISSUDT').val($data.ISSUDT_6);
	$('#6_EXPDT').val($data.EXPDT_6);
	$('#6_AGE').val($data.AGE_6);
	$('#6_OCCUP').val($data.OCCUP_6);
	$('#6_OFFIC').val($data.OFFIC_6);
	$('#6_MAXCRED').val($data.MAXCRED_6);
	$('#6_YINCOME').val($data.YINCOME_6);
	$('#6_MREVENU').val($data.MREVENU_6);
	$('#6_YREVENU').val($data.YREVENU_6);
	$('#6_ADDRNO3').val($data.ADDRNO3_6);
	$('#6_MEMO1').val($data.MEMO1_6);
	$('#dataTables-addrspt tbody').empty().append($data.addrspt);
	$('#6_MEMO1ADR').val($data.MEMO1ADR_6);
	//tab7
	$('#7_CONTNO').val($data.CONTNO_7);
	$('#7_LOCAT').val($data.LOCAT_7);
	$('#dataTables-compact tbody').empty().append($data.compact);
	
	//tab8
	$('#8_CONTNO').val($data.CONTNO_8);
	$('#dataTables-debtors tbody').empty().append($data.debtors);
	$('#8_SUMTOTPRC').val($data.SUMTOTPRC_8);
	$('#8_SUMSMPAY').val($data.SUMSMPAY_8);
	$('#8_SUMBALANC').val($data.SUMBALANC_8);
	$('#8_SUMSMCHQ').val($data.SUMSMCHQ_8);
	$('#8_SUMTKANG').val($data.SUMTKANG_8);
}
function changedatacar($STRNOS){
	dataToPost = new Object();
	dataToPost.STRNOS	= $STRNOS;
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/changedatacar',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			//cleardata();
			$('#loadding').hide();
			changedatastrno(data);
		}
	});
}
function changedatastrno($data){
	//$('#2_CONTNO').val($data.CONTNO_2);
	//$('#2_LOCAT').val($data.LOCAT_2);
	
	$('#2_STRNO').val($data.STRNO_2);
	$('#2_ENGNO').val($data.ENGNO_2);
	$('#2_NADDCOST').val($data.NADDCOST_2);
	
	$('#2_REGNO').val($data.REGNO_2);
	//$('#2_REGPAY').val($data.REGPAY_2);
	$('#2_REGYEAR').val($data.REGYEAR_2);
	$('#2_REGEXP').val($data.REGEXP_2);
	$('#2_REGTYP').val($data.REGTYP_2);
	$('#2_GARFRM').val($data.GARFRM_2);
	$('#2_GAREXP').val($data.GAREXP_2);
	$('#2_GARNO3').val($data.GARNO3_2);
	$('#2_GAR3FRM').val($data.GAR3FRM_2);
	$('#2_GAR3EXP').val($data.GAR3EXP_2);
	
	$('#2_TYPE').val($data.TYPE_2);
	$('#2_MODEL').val($data.MODEL_2);
	$('#2_BAAB').val($data.BAAB_2);
	$('#2_COLOR').val($data.COLOR_2);
	$('#2_CC').val($data.CC_2);
	//$('#2_STAT').val($data.STAT_2);
	$('#2_APNAME').val($data.APNAME_2);
	$('#2_GDESC').val($data.GDESC_2);	
	$('#2_MANUYR').val($data.MANUYR_2);
	if($data.STAT_2 == 'O'){
		$('#2_STAT').val('เก่า');
	}else if($data.STAT_2 == 'N'){
		$('#2_STAT').val('ใหม่');
	}
}

function chagedataspt($CUSCODSPT){
	//alert(CUSCODSPT);
	dataToPost = new Object();
	dataToPost.CUSCODSPT = $CUSCODSPT;
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/changedataspt',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			//cleardata();
			$('#loadding').hide();
			changedatasupporter(data);
		}
	});
}
function changedatasupporter($data){
	$('#6_GROUP1').val($data.GROUP1_6);
	$('#6_GRADE').val($data.GRADE_6);
	$('#6_BIRTHDT').val($data.BIRTHDT_6);
	$('#6_NICKNM').val($data.NICKNM_6);
	$('#6_IDCARD').val($data.IDCARD_6);
	$('#6_IDNO').val($data.IDNO_6);
	$('#6_ISSUBY').val($data.ISSUBY_6);
	$('#6_ISSUDT').val($data.ISSUDT_6);
	$('#6_EXPDT').val($data.EXPDT_6);
	$('#6_AGE').val($data.AGE_6);
	$('#6_OCCUP').val($data.OCCUP_6);
	$('#6_OFFIC').val($data.OFFIC_6);
	$('#6_MAXCRED').val($data.MAXCRED_6);
	$('#6_YINCOME').val($data.YINCOME_6);
	$('#6_MREVENU').val($data.MREVENU_6);
	$('#6_YREVENU').val($data.YREVENU_6);
	$('#6_ADDRNO3').val($data.ADDRNO3_6);
	$('#6_MEMO1').val($data.MEMO1_6);
	$('#dataTables-addrspt tbody').empty().append($data.addrspt);
	$('#6_MEMO1ADR').val($data.MEMO1ADR_6);
}

function alertmessage(CONTNO,MSGLOCAT,STARTDT,ENDDT,MSGMEMO,USERID){
	dataToPost = new Object();
	dataToPost.TYPALERT = USERID;
	$.ajax({
		url:'../SYS06/ReportFinance/getfromAlertMessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'แสดงข้อความเตือน',
				//width: $(window).width(),
				//height: $(window).height(),
				width:630,
				height:420,
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($this){
					$('#MSGMEMO').val(MSGMEMO);
					
					if(USERID != 'XX'){
						document.getElementById("savemsg").checked = false;
						$('#savemsg').attr('disabled',true);
						$('#MSGMEMO').attr('disabled',true);
					}
					$('.btn-close').click(function(){
						if($("#savemsg").is(":checked")){
							updatemessage(CONTNO,MSGLOCAT,STARTDT,ENDDT,MSGMEMO,USERID);
						}
					});
					$('#btnclose').click(function(){
						if($("#savemsg").is(":checked")){
							updatemessage(CONTNO,MSGLOCAT,STARTDT,ENDDT,MSGMEMO,USERID);
						}
						$this.destroy();
					});
					
				}
			});			
		}
	}); 
}
function updatemessage(CONTNO,MSGLOCAT,STARTDT,ENDDT,MSGMEMO,USERID){
	dataToPost = new Object();
	dataToPost.CONTNO = CONTNO;
	dataToPost.MSGLOCAT = MSGLOCAT;
	dataToPost.STARTDT = STARTDT;
	dataToPost.ENDDT = ENDDT;
	dataToPost.USERID = USERID;
	dataToPost.MSGOLD = MSGMEMO;
	dataToPost.MSGNEW = $('#MSGMEMO').val();
	$.ajax({
		url:'../SYS06/ReportFinance/updatemessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data) {
			if(data.status == 'E'){
				Lobibox.notify('error', {
					title: 'ผิดพลาด',
					size: 'mini',
					closeOnClick: false,
					delay: 1000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			/*
			if(data.status == 'S'){
				Lobibox.notify('info',{
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 1500,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			*/
		}
	});
}

$('#btncostcar').click(function(){
	dataToPost = new Object();
	dataToPost.CONTNO 	= $('#3_CONTNO').val();
	dataToPost.LOCAT	= $('#3_LOCAT').val();
	var P_DATESEARCH    = $('#3_DATESEARCH').val();
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromPayment',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//payment(data,DATESEARCH);
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
			}else{
				Lobibox.window({
					title: 'แสดงยอดเบี้ยปรับและยอดชำระ',
					width: $(window).width(),
					height: $(window).height(),
					width:830,
					height:580,
					content: data.html,
					draggable: true,
					closeOnEsc: false,
					shown: function(){
						document.getElementById("dataTable-fixed-listpayment").addEventListener("scroll", function(){
							var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
							this.querySelector("thead").style.transform = translate;
							this.querySelector("thead").style.zIndex = 100;
						});
						$('#btnprint_penalty').click(function(){
							printpenalty(P_DATESEARCH);
						});
					}
				});	
			}
		}
	}); 
});
/*
function payment($data,P_DATESEARCH){
	dataToPost = new Object();
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromPayment',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'แสดงยอดเบี้ยปรับและยอดชำระ',
				//width: $(window).width(),
				//height: $(window).height(),
				width:'100%',
				height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function(){
					document.getElementById("dataTable-fixed-listpayment").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
						this.querySelector("thead").style.transform = translate;
						this.querySelector("thead").style.zIndex = 100;
					});
					$('#dataTables-listpayment tbody').empty().append($data.listpayment);
					$('#P_sumINTAMT').val($data.sumINTAMT);
					$('#P_sumPAID').val($data.sumPAID);
					$('#P_sumDSCINT').val($data.sumDSCINT);
					$('#P_penalty').val($data.penalty);
					
					$('#btnprint_penalty').click(function(){
						printpenalty(P_DATESEARCH);
					});
				}
			});			
		}
	}); 
}
*/
function printpenalty(P_DATESEARCH){
	$('#btnprint_penalty').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS06/ReportFinance/printpenaltypdf?cond='+$("#3_CONTNO").val()+'||'+P_DATESEARCH;
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
}

$('#btndiscount').click(function(){
	dataToPost = new Object();
	dataToPost.CONTNO 	  = $('#3_CONTNO').val();
	dataToPost.LOCAT	  = $('#3_LOCAT').val();
	dataToPost.DATESEARCH = $('#3_DATESEARCH').val();
	var D_DATESEARCH = $('#3_DATESEARCH').val();
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromDiscount',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//discount(data,DATESEARCH);
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
			}else{
				Lobibox.window({
					title: 'แสดงยอดเบี้ยปรับและยอดชำระ',
					width:830,
					height:580,
					content: data.html,
					draggable: true,
					closeOnEsc: false,
					shown: function(){
						$('#btnprint_account').click(function(){
							printaccount(D_DATESEARCH);
						});
						
						$('#btnprint_customer').click(function(){
							printcustomer(D_DATESEARCH);
						});
						
					}
				});
			}
		}
	}); 
});

/*function discount($data,D_DATESEARCH){
	dataToPost = new Object();
	$('#loadding').show();
	$.ajax({
		url:'../SYS06/ReportFinance/getfromDiscount',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'แสดงยอดเบี้ยปรับและยอดชำระ',
				//width: $(window).width(),
				//height: $(window).height(),
				width:'100%',
				height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function(){
					$('#D_TOTAR').val($data.TOTAR);
					$('#D_PERC30').val($data.PERC30);
					$('#D_TOTPAY').val($data.TOTPAY);
					$('#D_INTAMT').val($data.INTAMT);
					$('#D_OPERT').val($data.OPERT);
					$('#D_NETPAY').val($data.NETPAY);
					$('#D_NPROF').val($data.NPROF);
					$('#D_PERC50').val($data.PERC50);
					
					$('#btnprint_account').click(function(){
						//alert(D_DATESEARCH);
						printaccount(D_DATESEARCH);
					});
					
					$('#btnprint_customer').click(function(){
						printcustomer(D_DATESEARCH);
					});
					
				}
			});			
		}
	}); 
}
*/
function printaccount(D_DATESEARCH){
	$('#btnprint_account').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS06/ReportFinance/printaccountpdf?cond='+$("#3_CONTNO").val()+'||'+D_DATESEARCH;
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
}
function printcustomer(D_DATESEARCH){
	$('#btnprint_customer').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS06/ReportFinance/printcustomerpdf?cond='+$("#3_CONTNO").val()+'||'+D_DATESEARCH;
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
}