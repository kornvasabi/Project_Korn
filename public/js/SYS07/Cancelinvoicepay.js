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
	$('#INVNO1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTAXBUY',
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
	
	INVNOCHANGE = null
	$('#INVNO1').change(function(){ 
		var INVNO1 =  (typeof $('#INVNO1').find(":selected").val() === 'undefined' ? '' : $('#INVNO1').find(":selected").val());
		dataToPost = new Object();
		dataToPost.INVNO1 = INVNO1;
		
		if(INVNO1 != ''){
			INVNOCHANGE = $.ajax({
				url : '../SYS07/Cancelinvoicepay/searchINVNO',
				data : dataToPost,
				type : "POST",
				dataType : "json",
				success: function(data){
					$('#VATDATE').val(data.TAXDT);
					$('#LOCAT1').val(data.LOCAT);
					$('#NETAMT1').val(data.NETAMT);
					$('#VAT1').val(data.VATAMT);
					$('#AMOUNT1').val(data.TOTAMT);	
					
					$('#dataTables-taxdata tbody').empty().append(data.taxdata);
					document.getElementById("dataTable-fixed-taxdata").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
						this.querySelector("thead").style.transform = translate;
						this.querySelector("thead").style.zIndex = 100;
					});
					INVNOCHANGE = null;
				},
				beforeSend: function(){
					if(INVNOCHANGE !== null){
						INVNOCHANGE.abort();
					}
				}
			});
		}else{
			$('#VATDATE').val('');
			$('#LOCAT1').val('');
			$('#NETAMT1').val('');
			$('#VAT1').val('');
			$('#AMOUNT1').val('');
			$('#dataTables-taxdata tbody').empty().append('');
		}
	});
});

INVNOCHANGE2 = null
$('#btnsearch').click(function(){
	var INVNO1 =  (typeof $('#INVNO1').find(":selected").val() === 'undefined' ? '' : $('#INVNO1').find(":selected").val());
	dataToPost = new Object();
	dataToPost.INVNO1 = INVNO1;
	
	INVNOCHANGE2 = $.ajax({
		url : '../SYS07/Cancelinvoicepay/searchINVNO2',
		data : dataToPost,
		type : "POST",
		dataType : "json",
		success: function(data){
			$('#INVNO1').empty().trigger('change');
			$('#VATDATE').val('');
			$('#LOCAT1').val('');
			$('#NETAMT1').val('');
			$('#VAT1').val('');
			$('#AMOUNT1').val('');	
			
			$('#dataTables-taxdata tbody').empty().append(data.taxdata2);
			document.getElementById("dataTable-fixed-taxdata").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			INVNOCHANGE2 = null;
		},
		beforeSend: function(){
			if(INVNOCHANGE2 !== null){
				INVNOCHANGE2.abort();
			}
		}
	});
});


if(_level == '1'){
	$('#btncancel').attr('disabled',false);
}else{
	if(_delete == 'T'){
		$('#btncancel').attr('disabled',false);
	}else{
		$('#btncancel').attr('disabled',true);
	}
}


$('#btncancel').click(function(){
	Cancel_invoince();
});

function Cancel_invoince(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการยกเลิกใบกำกับภาษีซื้อ หรือไม่',
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
				dataToPost.INVNO1 	= (typeof $('#INVNO1').find(':selected').val() === 'undefined' ? '':$('#INVNO1').find(':selected').val());
				dataToPost.LOCAT1 	= $('#LOCAT1').val();
				
				if(dataToPost.INVNO1 == ""){
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
						msg: 'กรุณาเลือกใบกำกับ'
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS07/Cancelinvoicepay/Cancel_invoince',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
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
								
								$('#INVNO1').empty().trigger('change');
								$('#VATDATE').val('');
								$('#CONTNO1').val('');
								$('#LOCAT1').val('');
								$('#CUSCOD1').val('');
								$('#CUSNAME1').val('');
								$('#AMOUNT1').val('');
								$('#FPAY').val('');
								$('#LPAY').val('');
								$('#DETAIL').val('');
								
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