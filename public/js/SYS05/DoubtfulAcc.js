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
	
	$('#TYPLOST1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPLOST',
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
		url:'../SYS05/DoubtfulAcc/getfromDoubtfulAcc',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการหนี้สงสัยจะสูญ',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_DoubtfulAcc($this);
				}
	
			});			
		}
	});
});

function Add_DoubtfulAcc($thisWindowarlost){
	
	$('#btndel_arlost').attr('disabled',true);
	
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_DoubtfulAcc',
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
	
	CONTNOCHANGE = null
	$('#CONTNO').change(function(){ 
		var contno =  (typeof $('#CONTNO').find(":selected").val() === 'undefined' ? '' : $('#CONTNO').find(":selected").val());
		dataToPost = new Object();
		dataToPost.contno = contno;
			
		CONTNOCHANGE = $.ajax({
			url : '../SYS05/DoubtfulAcc/searchCONTNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				if(contno != ''){
					$('#LOCAT').val(data.CRLOCAT);
					$('#CUSNAME').val(data.CUSNAME);
					$('#CUSCOD').val(data.CUSCOD);
					$('#REGNO').val(data.REGNO);
					$('#STRNO').val(data.STRNO);
					$('#PRICE').val(data.TOTPRC);
					$('#SMPAY').val(data.SMPAY);
					$('#BALANCE').val(data.BALANCE);
					$('#NETAR').val(data.EXP_AMT);
					$('#BOOKVALUE').val(data.BOOKVALUE);
					$('#SALEVAT').val(data.VATPRC);
					$('#NPROFIT').val(data.NPROF);	
					
				}else{
					$('#LOCAT').val('');
					$('#CUSNAME').val('');
					$('#CUSCOD').val('');
					$('#REGNO').val('');
					$('#STRNO').val('');
					$('#PRICE').val('');
					$('#SMPAY').val('');
					$('#BALANCE').val('');
					$('#NETAR').val('');
					$('#BOOKVALUE').val('');
					$('#SALEVAT').val('');
					$('#NPROFIT').val('');
					$('#DATELOST').val(_today);
					$('#MEMO').val('');
				}
				CONTNOCHANGE = null;
			},
			beforeSend: function(){
				if(CONTNOCHANGE !== null){
					CONTNOCHANGE.abort();
				}
			}
		});
	});
	
	$('#TYPLOST').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getTYPLOST',
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
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	//_insert = 'T';
	if(_level == '1'){
		$('#btnsave_arlost').attr('disabled',false);
	}else{
		if(_insert == 'T'){
			$('#btnsave_arlost').attr('disabled',false);
		}else{
			$('#btnsave_arlost').attr('disabled',true);
		}
	}
	$('#btnsave_arlost').click(function(){
		Save_ARlost($thisWindowarlost);
	});
}


function Save_ARlost($thisWindowarlost){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกรายการหนี้สงสัยจะสูญหรือไม่',
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
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val());
				dataToPost.TYPLOST 	= (typeof $('#TYPLOST').find(':selected').val() === 'undefined' ? '':$('#TYPLOST').find(':selected').val());
				dataToPost.STRNO 	= $('#STRNO').val();
				dataToPost.BOOKVAL 	= $('#BOOKVALUE').val();
				dataToPost.SALEVAT 	= $('#SALEVAT').val();
				dataToPost.NPROFIT 	= $('#NPROFIT').val();
				dataToPost.DATELOST = $('#DATELOST').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				
				if(dataToPost.BOOKVAL == "" || dataToPost.NPROFIT == ""  || dataToPost.TYPLOST == ""){	
					var $msg = "";
					if(dataToPost.BOOKVAL == ""){
						$msg = "กรุณาระบุ มูลค่าคงเหลือตามบัญชี";
					}else if(dataToPost.NPROFIT == ""){
						$msg = "กรุณาระบุ ดอกผลเช่าซื้อคงเหลือ";
					}else if(dataToPost.TYPLOST == ""){
						$msg = "กรุณาระบุ ประเภทหนี้สูญ";
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
						url:'../SYS05/DoubtfulAcc/Save_ARlost',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowarlost.destroy();
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

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	$('#resultt_DoubtfulAcc').show(); 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.TYPLOST1 = (typeof $('#TYPLOST1').find(':selected').val() === 'undefined' ? '':$('#TYPLOST1').find(':selected').val());
	dataToPost.FROMDATE 	= $('#FROMDATE').val();
	dataToPost.TODATE 	= $('#TODATE').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_DoubtfulAcc').html('');
	$('#resultt_DoubtfulAcc').append(spinner);
	
	$.ajax({
		url:'../SYS05/DoubtfulAcc/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_DoubtfulAcc').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_DoubtfulAcc').html(data.html);
			
			$('#table-ARlost').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-ARlost',1,340);
			
			$('.data-export').prepend('<img id="print-ARlost" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(70%);">');
			$("#print-ARlost").hover(function() {
				document.getElementById("print-ARlost").style.filter = "contrast(100%)";
			}, function() {
				document.getElementById("print-ARlost").style.filter = "contrast(70%)";
			});
			
			$('.data-export').prepend('<img id="table-ARlost-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(70%);">');
			$("#table-ARlost-excel").hover(function() {
				document.getElementById("table-ARlost-excel").style.filter = "contrast(100%)";
			}, function() {
				document.getElementById("table-ARlost-excel").style.filter = "contrast(70%)";
			});
			
			$("#table-ARlost-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","Report_ARlost"); 
			});

			$('#print-ARlost').click(function(){
				printReport();
			});
			
			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$('.getit').click(function(){	
					var	CONTNO 	= $(this).attr('CONTNO'); 
					var	LOCAT 	= $(this).attr('LOCAT');
					var	CUSCOD 	= $(this).attr('CUSCOD');
					var	CUSNAME = $(this).attr('CUSNAME');
					var	REGNO 	= $(this).attr('REGNO');
					var	STRNO 	= $(this).attr('STRNO');
					var	TOTPRC 	= $(this).attr('TOTPRC');
					var	SMPAY 	= $(this).attr('SMPAY');
					var	TOTBAL 	= $(this).attr('TOTBAL');
					var	EXP_AMT = $(this).attr('EXP_AMT');
					var	BOOKVAL = $(this).attr('BOOKVAL');
					var	BOOKVAT = $(this).attr('BOOKVAT');
					var	BALPROF = $(this).attr('BALPROF');
					var	LOSTDTS = $(this).attr('LOSTDTS');
					var	LOSTCOD	= $(this).attr('LOSTCOD');
					var	LOSTESC = $(this).attr('LOSTESC');
					var	MEMO1 	= $(this).attr('MEMO1');
					loadform(
						CONTNO,LOCAT,CUSCOD,CUSNAME,REGNO,STRNO,TOTPRC,SMPAY,TOTBAL,EXP_AMT,BOOKVAL,
						BOOKVAT,BALPROF,LOSTDTS,LOSTCOD,LOSTESC,MEMO1
					);
				});
			}		
		}
	});
}

function printReport(){
	dataToPost = new Object();
	dataToPost.LOCAT1 = (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.TYPLOST1 = (typeof $('#TYPLOST1').find(':selected').val() === 'undefined' ? '':$('#TYPLOST1').find(':selected').val());
	dataToPost.FROMDATE 	= $('#FROMDATE').val();
	dataToPost.TODATE 	= $('#TODATE').val();
	$.ajax({
		url: '../SYS05/DoubtfulAcc/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS05/DoubtfulAcc/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: true,
				height: $(window).height(),
				width: $(window).width()
			});

		}
	});
}

function loadform(CONTNO,LOCAT,CUSCOD,CUSNAME,REGNO,STRNO,TOTPRC,SMPAY,TOTBAL,EXP_AMT,BOOKVAL,BOOKVAT,BALPROF,LOSTDTS,LOSTCOD,LOSTESC,MEMO1){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/DoubtfulAcc/getfromDoubtfulAcc',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการหนี้สงสัยจะสูญ',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){	
					$('#TYPLOST').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getTYPLOST',
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
						dropdownAutoWidth : true,
						width: '100%'
					});
					
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNO').empty();
					$('#CONTNO').append(newOption).trigger('change'); 
					newOption = new Option(LOSTCOD+' - '+LOSTESC, LOSTCOD, false, false);
					$('#TYPLOST').empty();
					$('#TYPLOST').append(newOption).trigger('change'); 
					
					$('#LOCAT').val(LOCAT);
					$('#CUSNAME').val(CUSNAME);
					$('#CUSCOD').val(CUSCOD);
					$('#REGNO').val(REGNO);
					$('#STRNO').val(STRNO);
					$('#PRICE').val(TOTPRC);
					$('#SMPAY').val(SMPAY);
					$('#BALANCE').val(TOTBAL);
					$('#NETAR').val(EXP_AMT);
					$('#BOOKVALUE').val(BOOKVAL);
					$('#SALEVAT').val(BOOKVAT);
					$('#NPROFIT').val(BALPROF);
					$('#DATELOST').val(LOSTDTS);
					$('#MEMO').val(MEMO1);
					
					$('#CONTNO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });

					$('#LOCAT').attr('disabled',true);
					$('#CUSNAME').attr('disabled',true);
					$('#CUSCOD').attr('disabled',true);
					$('#REGNO').attr('disabled',true);
					$('#STRNO').attr('disabled',true);
					$('#PRICE').attr('disabled',true);
					$('#SMPAY').attr('disabled',true);
					$('#BALANCE').attr('disabled',true);
					$('#NETAR').attr('disabled',true);
					
					//var _update = 'T';
					if(_level == '1'){
						$('#btnsave_arlost').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_arlost').attr('disabled',false);
							}else{
								$('#btnsave_arlost').attr('disabled',true);
								$('#TYPLOST').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#BOOKVALUE').attr('disabled',true);
								$('#SALEVAT').attr('disabled',true);
								$('#NPROFIT').attr('disabled',true);
								$('#DATELOST').attr('disabled',true);
								$('#MEMO').attr('disabled',true);
							}
						}else{
							$('#btnsave_arlost').attr('disabled',true);
							$('#TYPLOST').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#BOOKVALUE').attr('disabled',true);
							$('#SALEVAT').attr('disabled',true);
							$('#NPROFIT').attr('disabled',true);
							$('#DATELOST').attr('disabled',true);
							$('#MEMO').attr('disabled',true);
						}
					}
					$('#btnsave_arlost').click(function(){ 
						Edit_arlost($this);
					});
					
					//var _delete = 'T';
					if(_level == '1'){
						$('#btndel_arlost').attr('disabled',false);
					}else{
						if(_delete == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btndel_arlost').attr('disabled',false);
							}else{
								$('#btndel_arlost').attr('disabled',true);
							}
						}else{
							$('#btndel_arlost').attr('disabled',true);
						}
					}
					$('#btndel_arlost').click(function(){ 
						Delete_arlost($this);
					});
				}
			});			
		}
	});
}

function Edit_arlost($thisWindowEdit){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขรายการตั้งหนี้สงสัยจะสูญหรือไม่',
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
				dataToPost.CONTNO 	= (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val() );
				dataToPost.TYPLOST 	= (typeof $('#TYPLOST').find(':selected').val() === 'undefined' ? '':$('#TYPLOST').find(':selected').val());
				dataToPost.STRNO 	= $('#STRNO').val();
				dataToPost.BOOKVAL 	= $('#BOOKVALUE').val();
				dataToPost.SALEVAT 	= $('#SALEVAT').val();
				dataToPost.NPROFIT 	= $('#NPROFIT').val();
				dataToPost.DATELOST = $('#DATELOST').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				
				if(dataToPost.BOOKVAL == "" || dataToPost.NPROFIT == ""  || dataToPost.TYPLOST == ""){	
					var $msg = "";
					if(dataToPost.BOOKVAL == ""){
						$msg = "กรุณาระบุ มูลค่าคงเหลือตามบัญชี";
					}else if(dataToPost.NPROFIT == ""){
						$msg = "กรุณาระบุ ดอกผลเช่าซื้อคงเหลือ";
					}else if(dataToPost.TYPLOST == ""){
						$msg = "กรุณาระบุ ประเภทหนี้สูญ";
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
						url:'../SYS05/DoubtfulAcc/Edit_arlost',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowEdit.destroy();
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
							search();
						}
					});
				}
			}
		}
	});
}

function Delete_arlost($thisWindowDel){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการลบรายการตั้งหนี้สงสัยจะสูญหรือไม่',
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
				dataToPost.CONTNO = (typeof $('#CONTNO').find(':selected').val() === 'undefined' ? '':$('#CONTNO').find(':selected').val() );
				dataToPost.CUSCOD = $('#CUSCOD').val();
				dataToPost.STRNO = $('#STRNO').val();

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/DoubtfulAcc/Delete_arlost',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if(data.status == 'S'){
							$thisWindowDel.destroy();
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
						search();
					}
				});
			}
		}
	});
}