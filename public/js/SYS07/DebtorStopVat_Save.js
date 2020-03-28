/********************************************************
             ______@23/02/2020______
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
		placeholder: '',
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
			url: '../Cselect2K/getCONTNO_V',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? "":$('#LOCAT').find(':selected').val());
				dataToPost.vatstop = "save";
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
	$('#CONTNO2').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCONTNO_V',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? "":$('#LOCAT').find(':selected').val());
				dataToPost.vatstop = "save";
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
	if(_insert == 'T'){
		$('#btnadd').attr('disabled',false);
	}else{
		$('#btnadd').attr('disabled',true);
	}
	if(_insert == 'T'){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
});
$('#btnclear').click(function(){
	fn_Clearinput();
});
function fn_Clearinput(){
	$('#STOPVNO').val('');
	$('#CONTNO1').empty();
	$('#CONTNO2').empty();
	$('#EXP_PRD').val('0');
	$('#data-tbody').empty();
	$('#COUNTCONTNO').val('0');
}
$('#btnadd').click(function(){
	fn_Addinput();
});
function fn_Addinput(){
	$('#STOPDT').attr('disabled',false);
	$('#STOPVNO').attr('disabled',false);
	$('#CONTNO1').attr('disabled',false);
	$('#CONTNO2').attr('disabled',false);
	$('#EXP_PRD').attr('disabled',false);
	$('#btnsearch').attr('disabled',false);
	$('#btnlist').attr('disabled',false);
	$('#btnlistall').attr('disabled',false);
	$('#STOPVNO').val('');
	$('#CONTNO1').empty();
	$('#CONTNO2').empty();
	$('#EXP_PRD').val('0');
	$('#data-tbody').empty();
	$('#COUNTCONTNO').val('0');
	$('#btnsave').attr('disabled',false);
	$('#btnclear').attr('disabled',false);
}
$('#CONTNO1').change(function(){
	dataToPost = new Object();
	dataToPost.LOCAT = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.STOPDT = $('#STOPDT').val();
	dataToPost.CONTNO1 = (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	$.ajax({
		url: '../SYS07/DebtorStopVat_Save/getSTOPVNO',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#STOPVNO').val(data.STOPVNO);
		}
	});
});
$('#btnsearch').click(function(){
	fn_Search();
});
var searchstopvat = null;
function fn_Search(){
	dataToPost = new Object();
	dataToPost.LOCAT = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.STOPDT = $('#STOPDT').val();
	dataToPost.CONTNO1 = (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTNO2 = (typeof $('#CONTNO2').find(':selected').val() === 'undefined' ? '':$('#CONTNO2').find(':selected').val());
	dataToPost.EXP_PRD  = $('#EXP_PRD').val();
	$('#loadding').show();
	
	$('#dataTable-stopvat tbody').html('');
	$('#dataTable-stopvat tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
	
	searchstopvat = $.ajax({
		url: '../SYS07/DebtorStopVat_Save/ResultStopVat',
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
				$('#dataTable-stopvat tbody').empty().append(data.stopvat);
			}
			$('#dataTable-stopvat tr').click(function(e) {
				$('#dataTable-stopvat tr').removeClass('highlighted');
				$(this).addClass('highlighted');
			});
			$('#dataTable-stopvat tbody').empty().append(data.stopvat);
			document.getElementById("dataTable-stop-vat").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			$('#COUNTCONTNO').val(data.countcontno);
			$('#loadding').hide();
			
			$('#btnlistall').click(function(){
				$('.checklist').each(function(){
					this.checked = true;
				}) 
			});
			$('#btnlist').unbind('click');
			$('#btnlist').click(function(){
				$("table input[type='checkbox']:not(:checked)").parents('tr').remove(); 
				var rows = $('.checklist').length;
				$('#COUNTCONTNO').val(rows);
				//alert(rows);
			});
			searchstopvat = null;
		},
		beforeSend: function(){
			if(searchstopvat !== null){ searchstopvat.abort(); }
		}
	});
}
$('#btnshow').click(function(){
	fn_selectstopvat();
});
var selectstopvat = null;
function fn_selectstopvat(){
	$('#loadding').fadeIn(200);
	selectstopvat = $.ajax({
		url:'../Cselect2K/getSearchfromstopvat',
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
					$('#btnSearchResult').click(function(){fnResultSV();});
					var R_stopvat = null;
					function fnResultSV(){
						dataToPost = new Object();
						dataToPost.stopvno = $('#stopvno').val();
						$('#loadding').fadeIn(200);
						R_stopvat = $.ajax({
							url:'../Cselect2K/getResultstopvat',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#loadding').fadeOut(200);
								$('#StopVat_result').html(data.html);
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});
								var result_tr = null;
								$('.getit').unbind('click');
								$('.getit').click(function(){
									$('#STOPDT').val($(this).attr('STOPDT'));
									$('#STOPVNO').val($(this).attr('STOPVNO'));
									var frmcontnoOption = new Option($(this).attr('FRMCONTNO'),$(this).attr('FRMCONTNO'), false, false);
									$('#CONTNO1').empty().append(frmcontnoOption).trigger('click');
									
									var tocontnoOption = new Option($(this).attr('TOCONTNO'),$(this).attr('TOCONTNO'), false, false);
									$('#CONTNO2').empty().append(tocontnoOption).trigger('click');
									
									$('#EXP_PRD').val($(this).attr('EXP_PRD'));
									dataToPost = new Object();
									dataToPost.STOPVTR = $(this).attr('STOPVNO');
									result_tr = $.ajax({
										url:'../Cselect2K/getResultstopvat_TR',
										data: dataToPost,
										type: 'POST',
										dataType: 'json',
										success: function(data){
											$('#dataTable-stopvat tbody').empty().append(data.tr_stopvat);
											$('#COUNTCONTNO').val(data.countrow);
											$this.destroy();
											result_tr = null;
										},
										beforeSend: function(){
											if(result_tr !== null){result_tr.abort();}
										}
									});
									$('#STOPDT').attr('disabled',true);
									$('#STOPVNO').attr('disabled',true);
									$('#CONTNO1').attr('disabled',true);
									$('#CONTNO2').attr('disabled',true);
									$('#EXP_PRD').attr('disabled',true);
									$('#btnsearch').attr('disabled',true);
									$('#btnlist').attr('disabled',true);
									$('#btnlistall').attr('disabled',true);
									$('#btnsave').attr('disabled',true);
									$('#btnclear').attr('disabled',true);
								});
							}
						});
					}
				},
				beforeClose : function(){
					
				}
			});
			selectstopvat = null;
		},
		beforeSend:function(){
			if(selectstopvat !== null){selectstopvat.abort();}
		}
	});
}
$('#btnsave').click(function(){
	var stopvno = $('#STOPVNO').val();
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกลูกหนี้หยุด Vat รหัส :  '+stopvno+'?',
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
				fn_SanveStopvat();
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
	//fn_SanveStopvat();
});
var SanveStopvat = null;
function fn_SanveStopvat(){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.STOPDT  = $('#STOPDT').val();
	dataToPost.STOPVNO = $('#STOPVNO').val();
	dataToPost.CONTNO1 = (typeof $('#CONTNO1').find(':selected').val() === 'undefined' ? '':$('#CONTNO1').find(':selected').val());
	dataToPost.CONTNO2 = (typeof $('#CONTNO2').find(':selected').val() === 'undefined' ? '':$('#CONTNO2').find(':selected').val());
	dataToPost.EXP_PRD = $('#EXP_PRD').val();
	
	var sv = [];
	$("table input[type='checkbox']:checked").each(function(){
		var svs = [];
		svs.push($(this).attr('CONTNO'));
		svs.push($(this).attr('EXP_PRD'));
		svs.push($(this).attr('CUSCOD'));
		sv.push(svs);
		//alert($(this).attr('LOCAT')+$(this).attr('CONTNO'));
	});
	dataToPost.SVAT = sv;
	SanveStopvat = $.ajax({
		url: '../SYS07/DebtorStopVat_Save/SanveStopvat',
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
				fn_Clearinput();
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
			SanveStopvat = null;
		},
		beforeSend: function(){
			if(SanveStopvat !== null){SanveStopvat.abort();}
		}
	});
}