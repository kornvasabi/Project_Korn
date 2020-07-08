//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');
//หน้าแรก
$(function(){
	CHANGE = null
	
	$(document).ready(function() {
		$('input:radio[name=vat]').change(function() {
			if (this.value == 'more'){
				$('#VATDATE').attr('disabled',false);
				$('#VATDATE').val(_today);
			}else{ 
				$('#VATDATE').val('');
				$('#VATDATE').attr('disabled',true);
			}
		});
	});
	
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
	
	var objLOCAT1 = null;
	$('#LOCAT1').on('select2:select',function(){
		var dataToPost = new Object();
		dataToPost.LOCAT = $(this).find(':selected').val();
		
		$('#loadding').fadeIn(200);
		objLOCAT1 = $.ajax({
			url: '../SYS07/Taxinvoicedue/getLASTRUNTAX',
			data: dataToPost,
			type: "POST",
			dataType: "json",
			beforeSend: function(){ if(objLOCAT1 !== null){ objLOCAT1.abort(); } },
			success: function(data){
				$('#FRMDATE').val(data.LRUNTAX);
				$('#TODATE').val(data.LRUNTAX);
				
				search();
				objLOCAT1 = null;
			}
		});
	});
	
	$('#CONTNO1').select2({
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});

if(_level == '1'){
	$('#btncancel').attr('disabled',false);
}else{
	if(_update == 'T'){
		$('#btncancel').attr('disabled',false);
	}else{
		$('#btncancel').attr('disabled',true);
	}
}

search();

$('#btnt1search').click(function(){
	search();
});


function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 	= (typeof $('#LOCAT1').find(":selected").val() === 'undefined' ? '' : $('#LOCAT1').find(":selected").val());
	dataToPost.FRMDATE 	= $('#FRMDATE').val();
	dataToPost.TODATE 	= $('#TODATE').val();
	
	$('#loadding').fadeIn(200);	
	CHANGE = $.ajax({
		url : '../SYS07/Taxinvoicedue/searchLASTTAXNO',
		data : dataToPost,
		type : "POST",
		dataType : "json",
		success: function(data){
			$('#LRUNDT').val(data.Lrundt);
			$('#LTAXNO').val(data.Ltaxno);
			CHANGE = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(CHANGE !== null){
				CHANGE.abort();
			}
		}
	});
}

$('#btnprint').click(function(){
	printInvoine();
});

function printInvoine(){
	var vat = "";
	if($("#normal").is(":checked")){ 
		vat = "normal";
	}else if($("#more").is(":checked")){
		vat = "more";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(":selected").val() === 'undefined' ? '' : $('#LOCAT1').find(":selected").val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(":selected").val() === 'undefined' ? '' : $('#CONTNO1').find(":selected").val());
	dataToPost.VATDATE 		= $('#VATDATE').val();
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.vat 			= vat;
	
	$.ajax({
		url: '../SYS07/Taxinvoicedue/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/Taxinvoicedue/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
		}
	});
}

$('#btnrunno').click(function(){
	runtexno();
});

function runtexno(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกข้อความเตือน หรือไม่',
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
				dataToPost = new Object();
				dataToPost.LOCAT1 	= (typeof $('#LOCAT1').find(":selected").val() === 'undefined' ? '' : $('#LOCAT1').find(":selected").val());
				dataToPost.CONTNO1 	= (typeof $('#CONTNO1').find(":selected").val() === 'undefined' ? '' : $('#CONTNO1').find(":selected").val());
				var FRMDATE 		= $('#FRMDATE').val();
				var TODATE 			= $('#TODATE').val();
				dataToPost.FRMDATE	= FRMDATE;
				dataToPost.TODATE	= TODATE;

				if(dataToPost.LOCAT1 == ""){
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
						msg: 'กรุณาระบุสาขา'
					});
				}
				else if(FRMDATE.substring(3) != TODATE.substring(3)){
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
						msg: 'Run เลขที่ใบกำกับ ภายในเดือนเท่านั้น'
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS07/Taxinvoicedue/Runtaxno',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								search();
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
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}
			}
		}
	});
}