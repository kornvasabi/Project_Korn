//BEE+
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');


$(function(){
	$('#CUSCOD1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCUSTOMERSALL',
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#CUSCOD2').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCUSTOMERSALL',
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#CONTNO1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNOALL',
			data: function (params) {
				dataToPost = new Object();
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
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
	//tab1
	$('#dataTables-cusdata tbody').empty().append('');
	$('#dataTables-detail tbody').empty().append('');
	$('#1_SUMTOTPRC').val('');
	$('#1_SUMSMPAY').val('');
	$('#1_SUMBALANC').val('');
	$('#1_SUMSMCHQ').val('');
	$('#1_SUMTOTAL').val('');
	$('#1_STRNO').val('');
	$('#1_ENGNO').val('');
	$('#1_REGNO').val('');
	$('#1_TYPE').val('');
	$('#1_MODEL').val('');
	$('#1_BAAB').val('');
	$('#1_COLOR').val('');
	$('#1_CC').val('');
	$('#1_STAT').val('');
	$('#1_SDATE').val('');
	
	//tab2
	$('#dataTables-options tbody').empty().append('');
	$('#2_SUMPRCICE').val('');
	$('#2_SUMQTY').val('');
	$('#2_SUMTOTAL').val('');
	
	//tab3
	$('#3_CONTNO').val('');
	$('#3_CUSTOMER').val('');
	$('#3_STRNO').val('');
	$('#3_SDATE').val('');
	$('#3_TOTPRC').val('');
	$('#3_SMPAY').val('');
	$('#3_ARBALAC').val('');
	$('#3_SMCHQ').val('');
	$('#3_TOTBALANC').val('');
	$('#3_DATELP').val('');
	$('#3_TOTLP').val('');
	$('#3_EXPPRD').val('');
	$('#3_EXPFRM').val('');
	$('#3_EXPTO').val('');
	$('#3_EXPAMT').val('');
	$('#3_DAYLATE').val('');
	$('#3_CANINT').val('');
	$('#3_DATESEARCH').val(_today);
	$('#dataTables-armgar tbody').empty().append('');
	$('#dataTables-optarmgar tbody').empty().append('');
	
	//tab4
	$('#CUSCOD2').empty().trigger('change');
	$('#4_ARCONT').val('');
	$('#dataTables-arothers  tbody').empty().append('');
	$('#4_SUMTOTPRC').val('');
	$('#4_SUMSMPAY').val('');
	$('#4_SUMBALANC').val('');
	$('#4_SUMSMCHQ').val('');
	$('#4_SUMTOTAL').val('');
}

$('#btntsearcharothr').click(function(){ 
	searcharothr();
});

$('#btnt1search').click(function(){
	cleardata();
	search();
	
});

function search(){
	dataToPost = new Object();
	dataToPost.CUSCOD1 	= (typeof $('#CUSCOD1').find(':selected').val() === 'undefined' ? '':$('#CUSCOD1').find(':selected').val());
	dataToPost.CONTNO1 	= (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	
	if(dataToPost.CUSCOD1 == '' && dataToPost.CONTNO1 == ''){
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
			msg: 'กรุณาระบุเงื่อนไขเพื่อสอบถาม'
		});
	}else{
		$('#dataTables-cusdata tbody').html('');
		$('#dataTables-cusdata tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
	
		$.ajax({
			url:'../SYS05/ARdataSearch/search',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
			
				loaddata(data);
				
				$('#dataTables-cusdata tr').click(function(e) {
					$('#dataTables-cusdata tr').removeClass('highlighted');
					$(this).addClass('highlighted');
				});
			
				$('.getit').click(function(){ 
					var	CONTNOS = $(this).attr('CONTNO');
					var	CUSCODS = $(this).attr('CUSCOD');
					var	TSALES 	= $(this).attr('TSALE');
					if(data.numrow > 1){
						changedata(CONTNOS,CUSCODS,TSALES,
						data.CUSNAME,data.arothers,data.sumPAYAMT_4,data.sumSMPAY_4,data.sumBALANCE_4,data.sumSMCHQ_4,data.sumTOTAL_4);
					}
				});
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
}

function loaddata($data){
	
	if($data.MSGMEMO != 'none'){
		alertmessage($data.CONTNO,$data.MSGLOCAT,$data.STARTDT,$data.ENDDT,$data.MSGMEMO,$data.USERID);
	}
	
	//tab1
	$('#dataTables-cusdata tbody').empty().append($data.custdata);
	$('#1_SUMTOTPRC').val($data.sumTOTPRC_1);
	$('#1_SUMSMPAY').val($data.sumSMPAY_1);
	$('#1_SUMBALANC').val($data.sumBALANC_1);
	$('#1_SUMSMCHQ').val($data.sumSMCHQ_1);
	$('#1_SUMTOTAL').val($data.sumTKANG_1);
	document.getElementById("dataTable-fixed-cusdata").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	if($data.TSALE == 'A'){
		$('#CONTDETAIL_A').show();
		$('#CONTDETAIL_N').show();
		$('#CONTDETAIL').hide();
		$('#dataTables-detail tbody').empty().append($data.detail);
		document.getElementById("dataTable-fixed-detail").addEventListener("scroll", function(){
			var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
			this.querySelector("thead").style.transform = translate;
			this.querySelector("thead").style.zIndex = 100;
		});
	}else{
		$('#CONTDETAIL').show();
		$('#CONTDETAIL_A').hide();
		$('#CONTDETAIL_N').hide()
		$('#1_STRNO').val($data.STRNO);
		$('#1_ENGNO').val($data.ENGNO);
		$('#1_REGNO').val($data.REGNO);
		$('#1_TYPE').val($data.TYPE);
		$('#1_MODEL').val($data.MODEL);
		$('#1_BAAB').val($data.BAAB);
		$('#1_COLOR').val($data.COLOR);
		$('#1_CC').val($data.CC);
		$('#1_STAT').val($data.STAT);
		$('#1_SDATE').val($data.SDATE);
	}
	
	//tab2
	$('#dataTables-options tbody').empty().append($data.optmast);
	$('#2_SUMPRCICE').val($data.sumUPRICE_2);
	$('#2_SUMQTY').val($data.sumQTY_2);
	$('#2_SUMTOTAL').val($data.sumTOTPRC_2);
	
	//tab3
	$('#3_CONTNO').val($data.CONTNO);
	$('#3_CUSTOMER').val($data.CUSNAME);
	$('#3_STRNO').val($data.STRNO);
	$('#3_SDATE').val($data.SDATE);
	$('#3_TOTPRC').val($data.TOTPRC);
	$('#3_SMPAY').val($data.SMPAY);
	$('#3_ARBALAC').val($data.BALANC);
	$('#3_SMCHQ').val($data.SMCHQ);
	$('#3_TOTBALANC').val($data.TKANG);
	$('#3_DATELP').val($data.LPAYD);
	$('#3_TOTLP').val($data.LPAYA);
	$('#3_EXPPRD').val($data.EXP_PRD);
	$('#3_EXPFRM').val($data.EXP_FRM);
	$('#3_EXPTO').val($data.EXP_TO);
	$('#3_EXPAMT').val($data.EXP_AMT);
	$('#3_DAYLATE').val($data.DLDAY);
	$('#3_CANINT').val($data.CALINT);
	$('#dataTables-armgar tbody').empty().append($data.armgars);
	$('#dataTables-optarmgar tbody').empty().append($data.optarmgar);
	
	//tab4
	newOption = new Option($data.CUSNAME+' ('+$data.CUSCOD+')', $data.CUSCOD, false, false);
	$('#CUSCOD2').empty();
	$('#CUSCOD2').append(newOption).trigger('change');
	$('#dataTables-arothers  tbody').empty().append($data.arothers);
	$('#4_SUMTOTPRC').val($data.sumPAYAMT_4);
	$('#4_SUMSMPAY').val($data.sumSMPAY_4);
	$('#4_SUMBALANC').val($data.sumBALANCE_4);
	$('#4_SUMSMCHQ').val($data.sumSMCHQ_4);
	$('#4_SUMTOTAL').val($data.sumTOTAL_4);
	document.getElementById("dataTable-fixed-arothers").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	if($data.TSALE != 'H'){
		$('#btnpenalty').attr('disabled',true);
		$('#btndiscount').attr('disabled',true);
	}else{
		$('#btnpenalty').attr('disabled',false);
		$('#btndiscount').attr('disabled',false);
	}
	
	$('#btntsearcharothr').click(function(){ 
		searcharothr();
		$('.wizard-tabs').one( 'click', function() {
			newOption = new Option($data.CUSNAME+' ('+$data.CUSCOD+')', $data.CUSCOD, false, false);
			$('#CUSCOD2').empty();
			$('#CUSCOD2').append(newOption).trigger('change');
			$('#dataTables-arothers  tbody').empty().append($data.arothers);
			$('#4_SUMTOTPRC').val($data.sumPAYAMT_4);
			$('#4_SUMSMPAY').val($data.sumSMPAY_4);
			$('#4_SUMBALANC').val($data.sumBALANCE_4);
			$('#4_SUMSMCHQ').val($data.sumSMCHQ_4);
			$('#4_SUMTOTAL').val($data.sumTOTAL_4);
			document.getElementById("dataTable-fixed-arothers").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
		});
	});
}

function changedata($CONTNOS,$CUSCODS,$TSALES,$CUSNAME,$arothers,$sumPAYAMT_S,$sumSMPAY_S,$sumBALANCE_S,$sumSMCHQ_S,$sumTOTAL_S){
	dataToPost = new Object();
	dataToPost.CONTNOS 	= $CONTNOS;
	dataToPost.CUSCODS 	= $CUSCODS;
	dataToPost.TSALES 	= $TSALES;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARdataSearch/changedata',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#loadding').hide();
			
			if(data.MSGMEMO != 'none'){
				alertmessage(data.CONTNO,data.MSGLOCAT,data.STARTDT,data.ENDDT,data.MSGMEMO,data.USERID);
			}
			
			//tab1
			if($TSALES == 'A'){
				$('#CONTDETAIL_A').show();
				$('#CONTDETAIL_N').show()
				$('#CONTDETAIL').hide();
				$('#dataTables-detail tbody').empty().append(data.detail);
				document.getElementById("dataTable-fixed-detail").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
			}else{
				$('#CONTDETAIL').show();
				$('#CONTDETAIL_A').hide();
				$('#CONTDETAIL_N').hide()
				$('#1_STRNO').val(data.STRNO);
				$('#1_ENGNO').val(data.ENGNO);
				$('#1_REGNO').val(data.REGNO);
				$('#1_TYPE').val(data.TYPE);
				$('#1_MODEL').val(data.MODEL);
				$('#1_BAAB').val(data.BAAB);
				$('#1_COLOR').val(data.COLOR);
				$('#1_CC').val(data.CC);
				$('#1_STAT').val(data.STAT);
				$('#1_SDATE').val(data.SDATE);
			}
			
			//tab2
			$('#dataTables-options tbody').empty().append(data.optmast);
			$('#2_SUMPRCICE').val(data.sumUPRICE_2);
			$('#2_SUMQTY').val(data.sumQTY_2);
			$('#2_SUMTOTAL').val(data.sumTOTPRC_2);
			
			//tab3
			$('#3_CONTNO').val(data.CONTNO);
			$('#3_CUSTOMER').val(data.CUSNAME);
			$('#3_STRNO').val(data.STRNO);
			$('#3_SDATE').val(data.SDATE);
			$('#3_TOTPRC').val(data.TOTPRC);
			$('#3_SMPAY').val(data.SMPAY);
			$('#3_ARBALAC').val(data.BALANC);
			$('#3_SMCHQ').val(data.SMCHQ);
			$('#3_TOTBALANC').val(data.TKANG);
			$('#3_DATELP').val(data.LPAYD);
			$('#3_TOTLP').val(data.LPAYA);
			$('#3_EXPPRD').val(data.EXP_PRD);
			$('#3_EXPFRM').val(data.EXP_FRM);
			$('#3_EXPTO').val(data.EXP_TO);
			$('#3_EXPAMT').val(data.EXP_AMT);
			$('#3_DAYLATE').val(data.DLDAY);
			$('#3_CANINT').val(data.CALINT);
			$('#dataTables-armgar tbody').empty().append(data.armgars);
			$('#dataTables-optarmgar tbody').empty().append(data.optarmgar);
			
			if($TSALES != 'H'){
				$('#btnpenalty').attr('disabled',true);
				$('#btndiscount').attr('disabled',true);
			}else{
				$('#btnpenalty').attr('disabled',false);
				$('#btndiscount').attr('disabled',false);
			}
			
			$('#btntsearcharothr').click(function(){ 
				searcharothr();
				$('.wizard-tabs').one( 'click', function() {
					newOption = new Option($CUSNAME+' ('+$CUSCODS+')', $CUSCODS, false, false);
					$('#CUSCOD2').empty();
					$('#CUSCOD2').append(newOption).trigger('change');
					$('#dataTables-arothers  tbody').empty().append($arothers);
					$('#4_SUMTOTPRC').val($sumPAYAMT_S);
					$('#4_SUMSMPAY').val($sumSMPAY_S);
					$('#4_SUMBALANC').val($sumBALANCE_S);
					$('#4_SUMSMCHQ').val($sumSMCHQ_S);
					$('#4_SUMTOTAL').val($sumTOTAL_S);
					document.getElementById("dataTable-fixed-arothers").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
						this.querySelector("thead").style.transform = translate;
						this.querySelector("thead").style.zIndex = 100;
					});
				});
			});
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
	
}

var reportsearch = null;
function searcharothr(){
	dataToPost = new Object();
	dataToPost.CUSCOD2 = (typeof $('#CUSCOD2').find(':selected').val() === 'undefined' ? '':$('#CUSCOD2').find(':selected').val() );
	dataToPost.AROTHR = $('#4_ARCONT').val();
	
	if(dataToPost.CUSCOD2 == '' && dataToPost.AROTHR == ''){
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
			msg: 'กรุณาระบุเงื่อนไขเพื่อสอบถาม'
		});
	}else{
		$('#dataTables-arothers  tbody').html('');
		$('#dataTables-arothers  tbody').html("<tr><td colspan='9' align='center'><img src='../public/images/loading-icon.gif' style='width:130px;height:130px;'></td></tr>");
		reportsearch = $.ajax({
			url: '../SYS05/ARdataSearch/searcharothr',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				$('#dataTables-arothers  tbody').empty().append(data.sercharoth);
				$('#4_SUMTOTPRC').val(data.sumPAYAMT_S);
				$('#4_SUMSMPAY').val(data.sumSMPAY_S);
				$('#4_SUMBALANC').val(data.sumBALANCE_S);
				$('#4_SUMSMCHQ').val(data.sumSMCHQ_S);
				$('#4_SUMTOTAL').val(data.sumTOTAL_S);
				document.getElementById("dataTable-fixed-arothers").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				reportsearch = null;
			},
			beforeSend: function(){
				if(reportsearch !== null){
					reportsearch.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
}

function alertmessage(CONTNO,MSGLOCAT,STARTDT,ENDDT,MSGMEMO,USERID){
	dataToPost = new Object();
	dataToPost.TYPALERT = USERID;
	$.ajax({
		url:'../SYS05/ARdataSearch/getfromAlertMessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'แสดงข้อความเตือน',
				//width: $(window).width(),
				//height: $(window).height(),
				width:'100%',
				height:'100%',
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
		url:'../SYS05/ARdataSearch/updatemessage',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data) {
			if(data.status == 'E'){
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
		}
	});
}

function updateINTAMT(){
	dataToPost = new Object();
	dataToPost.CONTNO 		= $('#3_CONTNO').val();
	dataToPost.DATESEARCH 	= $('#3_DATESEARCH').val();

	$.ajax({
		url:'../SYS05/ARdataSearch/updateINTAMT',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data) {
			if(data.status == 'E'){
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
		}
	});
}

$('#btnpenalty').click(function(){
	updateINTAMT();
	dataToPost = new Object();
	dataToPost.CONTNO 	= $('#3_CONTNO').val();
	var DATESEARCH 	= $('#3_DATESEARCH').val();
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARdataSearch/searchpenalty',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			penalty(data.payment,data.sumINTAMT,data.PAID,data.DSCINT,data.penalty,DATESEARCH);
			
		}
	}); 
});

function penalty(payment,sumINTAMT,PAID,DSCINT,penalty,P_DATESEARCH){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARdataSearch/getfromPenalty',
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
					$('#dataTables-penalty tbody').empty().append(payment);
					document.getElementById("dataTable-fixed-penalty").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
						this.querySelector("thead").style.transform = translate;
						this.querySelector("thead").style.zIndex = 100;
					});
					
					$('#P_PENALTY').val(sumINTAMT);
					$('#P_SMPAY').val(PAID);
					$('#P_DISCOUNT').val(DSCINT);
					$('#P_BALANC').val(penalty);
					
					$('#btnprint_penalty').click(function(){
						printpenalty(P_DATESEARCH);
					});
				}
			});			
		}
	}); 
}

function printpenalty(P_DATESEARCH){
	$('#btnprint_penalty').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS05/ARdataSearch/printpenaltypdf?cond='+$("#3_CONTNO").val()+'||'+P_DATESEARCH;
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
	updateINTAMT();
	dataToPost = new Object();
	dataToPost.CONTNO 	= $('#3_CONTNO').val();
	var DATESEARCH 	= $('#3_DATESEARCH').val();
	dataToPost.DATESEARCH = DATESEARCH;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARdataSearch/searchdiscount',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			discount(data.BALANC,data.DISCOUNT,data.NDAMT,data.NINTAMT,data.OPERT,data.TOTAL,data.NPROF,data.PRENPROF,DATESEARCH);
		}
	}); 
});

function discount(BALANC,DISCOUNT,NDAMT,NINTAMT,OPERT,TOTAL,NPROF,PRENPROF,D_DATESEARCH){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARdataSearch/getfromDiscount',
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
					$('#D_PAYMENT').val(BALANC);
					$('#D_DISCOUNT').val(DISCOUNT);
					$('#D_BALANC').val(NDAMT);
					$('#D_PENALTY').val(NINTAMT);
					$('#D_OPERATE').val(OPERT);
					$('#D_TOTAL').val(TOTAL);
					$('#D_NPROFIT').val(NPROF);
					$('#D_PPROFI').val(PRENPROF);
					
					$('#btnprint_account').click(function(){
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

function printaccount(D_DATESEARCH){
	$('#btnprint_account').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS05/ARdataSearch/printaccountpdf?cond='+$("#3_CONTNO").val()+'||'+D_DATESEARCH;
	var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
	Lobibox.window({
		title: 'พิมพ์ใบตัดสด',
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
	$('#btnprint_account').attr('disabled',true);		
	var baseUrl = $('body').attr('baseUrl');
	var url = baseUrl+'SYS05/ARdataSearch/printcustomerpdf?cond='+$("#3_CONTNO").val()+'||'+D_DATESEARCH;
	var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
	Lobibox.window({
		title: 'พิมพ์ใบตัดสด',
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


