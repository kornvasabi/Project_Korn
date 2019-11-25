//BEE+
// หน้าแรก  
var _locat  	= $('.b_tab1[name="home"]').attr('locat');
var _insert 	= $('.b_tab1[name="home"]').attr('cin');
var _update 	= $('.b_tab1[name="home"]').attr('cup');
var _delete 	= $('.b_tab1[name="home"]').attr('cdel');
var _level  	= $('.b_tab1[name="home"]').attr('clev');
var _today  	= $('.b_tab1[name="home"]').attr('today');
var _usergroup  = $('.b_tab1[name="home"]').attr('usergroup');

//หน้าแรก
$(function(){
	$('#LOCAT1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
});
	
//กดเพิ่มข้อมูล	
$('#bth1add').click(function(){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ChangeBillcolector/getfromChangeBillcolector',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกเปลี่ยนแปลงพนักงานเก็บเงิน',
				width: $(window).width(),
				height: $(window).height(),
				//width:'60%',
				//height:'80%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_ChangeBillcolector($this);
				}
	
			});			
		}
	});
});

function Add_ChangeBillcolector($thisWindowChange){
	$('#LOCAT').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	$('#OLD_BILLC').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getOFFICER',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%',
	});	
	
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_ChangeContstat',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%',
	});

	$('#AMPHUR').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getAUMPHUR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#PROVINCE').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	BEECONTNOSCHANGE = null
	$('#AMPHUR').change(function(){ 
		var aumphur = (typeof $('#AMPHUR').find(":selected").val() === 'undefined' ? '' : $('#AMPHUR').find(":selected").val());
		dataToPost = new Object();
		dataToPost.aumphur = aumphur;
		BEECONTNOSCHANGE = $.ajax({
			url : "../Cselect2b/getPROVINCEbyAUMPHUR",
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				var newOption = new Option(data.PROVDES, data.PROVCOD, true, true);
				$('#PROVINCE').empty().append(newOption).trigger('change.select2');			
				BEECONTNOSCHANGE = null;
			},
			beforeSend: function(){
				if(BEECONTNOSCHANGE !== null){
					BEECONTNOSCHANGE.abort();
				}
			}
		});
		$('.AUMP').not(this).val($(this).val());
	});
	
	$('#PROVINCE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getPROVINCE',
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	$('#PROVINCE').change(function(){ 
		$('#AMPHUR').empty().trigger('change.select2');
		$('.AUMP').not(this).val($(this).val());
	});
	

	$('#LOCAT2').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#NEW_BILLC').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getOFFICER',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});	
	
	$('#NEW_BILLC').on('select2:open', function (e) {
	  $(".select2-results__options").height(125);
	});
	
	$("#btnsearch").hover(function() {
		document.getElementById("btnsearch").style.filter = "contrast(70%)";
	}, function(){
		document.getElementById("btnsearch").style.filter = "contrast(100%)";
	});
	
	$('#btnsearch').click(function(){
		SerchContnoDetail();
	});
	
	//_insert = 'T';
	if(_insert == 'T'){
		$('#btnsave_changebillc').attr('disabled',false);
	}else{
		$('#btnsave_changebillc').attr('disabled',true);
	}
	$('#btnsave_changebillc').click(function(){
		Save_changebillc($thisWindowChange);
		$('#resultt_ChangeBillcolector').hide(); 
	});
	
	$('#btnclr_changebillc').click(function(){
		clearVal();
	});
	
	$('#btnclr_changebillc').click(function(){
		clearVal();
	});
}

function clearVal(){
	$('#LOCAT').empty().trigger('change');
	$('#AMPHUR').empty().trigger('change');
	$('#PROVINCE').empty().trigger('change');
	$('#LOCAT2').empty().trigger('change');
	$('#OLD_BILLC').empty().trigger('change');
	$('#CONTNO').empty().trigger('change');
	$('#NEW_BILLC').empty().trigger('change');
	$('#DATECHG').val(_today);
	$('#VILLAGE').val('');
	$('#TAMBON').val('');
	$('#EXP_FRM').val('0');
	$('#EXP_TO').val('62');
	$('#MEMO').val('');
	$("#EXP_X").prop("checked", true);
	$('#resultt_Serch').html('');
}

function SerchContnoDetail(){
	dataToPost = new Object();
	var EXP = "";
	if($("#EXP_1").is(":checked")){ EXP = "1";}
	if($("#EXP_X").is(":checked")){ EXP = "X";}
	//dataToPost.LOCAT 	= (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.AMPHUR	= (typeof $('#AMPHUR').find(':selected').val() === 'undefined' ? '':$('#AMPHUR').find(':selected').val());
	dataToPost.PROVINCE	= (typeof $('#PROVINCE').find(':selected').val() === 'undefined' ? '':$('#PROVINCE').find(':selected').val());
	dataToPost.LOCAT2	= (typeof $('#LOCAT2').find(':selected').val() === 'undefined' ? '':$('#LOCAT2').find(':selected').val());
	dataToPost.OLD_BILLC= (typeof $('#OLD_BILLC').find(':selected').val() === 'undefined' ? '':$('#OLD_BILLC').find(':selected').val());
	dataToPost.CONTNO	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
	//dataToPost.NEW_BILLC= (typeof $('#NEW_BILLC').find(':selected').val() === 'undefined' ? '':$('#NEW_BILLC').find(':selected').val());
	//dataToPost.DATECHG 	= $('#DATECHG').val();
	dataToPost.VILLAGE	= $('#VILLAGE').val();
	dataToPost.TAMBON	= $('#TAMBON').val();
	dataToPost.EXP_FRM	= $('#EXP_FRM').val();
	dataToPost.EXP_TO	= $('#EXP_TO').val();
	dataToPost.EXP		= EXP;
	//dataToPost.MEMO		= $('#MEMO').val();
	
	if(dataToPost.AMPHUR == "" && dataToPost.PROVINCE == "" && dataToPost.LOCAT2 == "" && dataToPost.OLD_BILLC == "" && dataToPost.CONTNO == "" && dataToPost.VILLAGE == "" && dataToPost.TAMBON == ""){
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
			msg: 'กรุณาระบุเงื่อนไขการค้นหาสัญญา'
		});
	}else{
		$('#resultt_Serch').html("<table width='100%' height='100%'><tr><td align='center'><img src='../public/images/loading-icon.gif' style='width:100px;height:100px;'></td></tr></table>");
		$.ajax({
			url:'../SYS05/ChangeBillcolector/SerchContnoDetail',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				$('#resultt_Serch').html(data.html);
				
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$(document).ready(function () {
					$('#selectall').click(function () {
						if ($(this).prop('checked')) {
							$('.ckecklist').prop('checked', true);
							$("#SerchContnoDetail .ckecklist").attr("checktosave","T");
						}
						else {
							$('.ckecklist').prop('checked', false);
							$("#SerchContnoDetail .ckecklist").attr("checktosave","F");
						}
					});
				});
				
				$(".ckecklist").unbind("blur");
				$(".ckecklist").blur(function(){
					var x = $(this).attr("CONTNO");
					if($(this).is(':checked')){
						//var xx = "0";
						$("#SerchContnoDetail .ckecklist[CONTNO="+x+"]").attr("checktosave","T");
					}else{
						//var xx = "0";
						$("#SerchContnoDetail .ckecklist[CONTNO="+x+"]").attr("checktosave","F");
					}
				});
			}
		});
	}
}

function Save_changebillc($thisWindowChange){	
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกรายการเปลี่ยนแปลงพนักงานเก็บเงินหรือไม่',
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
			if (type === 'ok'){
					var AdjuststockAll = [];
					$("#SerchContnoDetail .ckecklist").each(function(){
						var data = 	$(this).attr("CONTNO")+'<###>'+
									$(this).attr("CUSCOD")+'<###>'+
									$(this).attr("CUSTNAME")+'<###>'+
									$(this).attr("SDATE")+'<###>'+
									$(this).attr("LOCAT")+'<###>'+
									$(this).attr("OLD_BILLC")+'<###>'+
									$(this).attr("EXP_PRD")+'<###>'+
									$(this).attr("MOOBAN")+'<###>'+
									$(this).attr("TUMB")+'<###>'+
									$(this).attr("AUMPDES")+'<###>'+
									$(this).attr("PROVDES")+'<###>'+
									$(this).attr("checktosave");
									AdjuststockAll.push(data);
								
					});
					
					dataToPost = new Object();
					dataToPost.LOCAT 	= (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
					dataToPost.AMPHUR	= (typeof $('#AMPHUR').find(':selected').val() === 'undefined' ? '':$('#AMPHUR').find(':selected').val());
					dataToPost.PROVINCE	= (typeof $('#PROVINCE').find(':selected').val() === 'undefined' ? '':$('#PROVINCE').find(':selected').val());
					dataToPost.LOCAT2	= (typeof $('#LOCAT2').find(':selected').val() === 'undefined' ? '':$('#LOCAT2').find(':selected').val());
					dataToPost.OLD_BILLC= (typeof $('#OLD_BILLC').find(':selected').val() === 'undefined' ? '':$('#OLD_BILLC').find(':selected').val());
					dataToPost.CONTNO	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
					dataToPost.NEW_BILLC= (typeof $('#NEW_BILLC').find(':selected').val() === 'undefined' ? '':$('#NEW_BILLC').find(':selected').val());
					dataToPost.DATECHG 	= $('#DATECHG').val();
					dataToPost.VILLAGE	= $('#VILLAGE').val();
					dataToPost.TAMBON	= $('#TAMBON').val();
					dataToPost.EXP_FRM	= $('#EXP_FRM').val();
					dataToPost.MEMO		= $('#MEMO').val();
					dataToPost.AdjuststockAll = AdjuststockAll;
				
				if(dataToPost.LOCAT == "" || dataToPost.NEW_BILLC == "" || dataToPost.DATECHG == "" || dataToPost.AdjuststockAll == ""){	
					var $msg = "";
					if(dataToPost.LOCAT == ""){
						$msg = "กรุณาระบุ สาขาที่เปลี่ยน";
					}else if(dataToPost.NEW_BILLC == ""){
						$msg = "กรุณาระบุ เปลี่ยนเป็นพนักงานเก็บเงิน";
					}else if(dataToPost.DATECHG == ""){
						$msg = "กรุณาระบุ วันที่เปลี่ยน";
					}else if(dataToPost.AdjuststockAll == ""){
						$msg = "กรุณาเลือกเงื่อนไขและค้นหารายการสัญญาก่อน";
					}
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
						msg: $msg
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS05/ChangeBillcolector/Save_changebillc',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowChange.destroy();
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else if(data.status == 'W'){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else if(data.status == 'E'){
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
			}
		}
	});
}

$('#btnt1search').click(function(){
	$('#resultt_ChangeBillcolector').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.CHGNO1 = $('#CHGNO1').val();
	dataToPost.FROMDATECHG 	= $('#FROMDATECHG').val();
	dataToPost.TODATECHG 	= $('#TODATECHG').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_ChangeBillcolector').html('');
	$('#resultt_ChangeBillcolector').append(spinner);
	
	$.ajax({
		url:'../SYS05/ChangeBillcolector/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_ChangeBillcolector').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_ChangeBillcolector').html(data.html);
			
			$('#table-CHGBILLC').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-CHGBILLC',1,345);

			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
			}		
		}
	});
}

