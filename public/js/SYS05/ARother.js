//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');

//หน้าแรก
$(function(){
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCUSTOMERS',
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
	
	$('#LOCATS').select2({
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
	
	$('#TSALE').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPESALE',
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
	
	$('#PAYFORS').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getPAYFOR',
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
$('#bthARother').click(function(){
	//$('#bthARother').attr('disabled',true);
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARother/getfromAROTHER',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกลูกหนี้อื่น',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_AROTHER($this);
				}
	
			});			
		}
	});
});

function Add_AROTHER($thisWindowARothr){
	$('#LOCATS2').select2({
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
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#TSALES').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getTYPESALE',
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
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});	
	
	$('#PAYTYPS').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getPAYFOR',
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
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#CONTNOS').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getCONTNO_AR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCODS').find(':selected').val();
				dataToPost.now2 = $('#TSALES').find(':selected').val();
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

	$('#CUSCODS').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2b/getCUSTOMERS_AR',
			data: function (params) {
				//alert('ลูกค้า');
				dataToPost = new Object();
				dataToPost.now = $('#CONTNOS').find(':selected').val();
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
	
	BEECONTNOSCHANGE = null
	
	$('#CONTNOS').change(function(){ 
		var contno = (typeof $('#CONTNOS').find(":selected").val() === 'undefined' ? '' : $('#CONTNOS').find(":selected").val());
		dataToPost = new Object();
		dataToPost.contno = contno
		BEECONTNOSCHANGE = $.ajax({
			url : "../Cselect2b/getCUSTOMERS_AR11",
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				var newOption = new Option(data.CUSNAME, data.CUSCOD, true, true);
				$('#CUSCODS').empty().append(newOption).trigger('change.select2');			
				BEECONTNOSCHANGE = null;
			},
			beforeSend: function(){
				if(BEECONTNOSCHANGE !== null){
					BEECONTNOSCHANGE.abort();
				}
			}
		});
		$('.BBB').not(this).val($(this).val());
	});
	
	$('#CUSCODS').change(function(){ 
		var customer =  (typeof $('#CUSCODS').find(":selected").val() === 'undefined' ? '' : $('#CUSCODS').find(":selected").val());
		dataToPost = new Object();
		dataToPost.customer = customer
		BEECONTNOSCHANGE = $.ajax({
			url : "../Cselect2b/getCUSTOMERS_AR22",
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				var newOption = new Option(data.CONTNO, data.CONTNO, true, true);
				$('#CONTNOS').empty().append(newOption).trigger('change.select2');			
				BEECONTNOSCHANGE = null;
			},
			beforeSend: function(){
				if(BEECONTNOSCHANGE !== null){
					BEECONTNOSCHANGE.abort();
				}
			}
		});
		$('.BBB').not(this).val($(this).val());
	});
	
	document.getElementById("Products").checked = true;
	$('#btndelete_arother').attr('disabled',true);
	
	$('.UPLOADPIC').hide();
	$('#PAYTYPS').change(function(){ 
		var paytype = $('#PAYTYPS').val();
		if(paytype == '188'){
			$('.UPLOADPIC').show();
		}else{
			$('.UPLOADPIC').hide();
		}
	});
	
	$('#PIC1').click(function(){
		alert('ยังไม่เปิดให้ใช้งาน');
		//loadForm();
	});
	
	/*$('#PIC1').uploadFile({
		//url:base_url+'Jaahe/Report_SaleTarget/upload_SaleTarget'
		url:'../SYS05/ARother/upload_SaleTarget'
		,fileName: 'myfile'
		,multiple: false
		,maxFileSize: 10240*1024 // Allow size 10MB
		,allowedTypes: "xls,xlsx"
		,acceptFiles: 'application/vnd.ms-excel,application'
		,showStatusAfterSuccess: false
		,autoSubmit:true
		,dynamicFormData: function(){
		}
		,dragDropStr: 'เลือกไฟล์'
		,abortStr:'เลือกไฟล์'
		,cancelStr:'ยกเลิก'
		,doneStr:'ผิดพลาด :: doneStr'
		,extErrorStr:'ผิดพลาด :: ต้องเป็นไฟล์ '
		,sizeErrorStr:'ผิดพลาด sizeErrorStr'
		,uploadErrorStr:'ผิดพลาด uploadErrorStr'
		,uploadStr:'เลือกไฟล์'
		,onSuccess:function(files,data,xhr,pd) {	
				
		}
	});*/
	
	$('#PIC2').click(function(){
		alert('ยังไม่เปิดให้ใช้งาน');
		//loadForm();
	});
	
	$('#PIC3').click(function(){
		alert('ยังไม่เปิดให้ใช้งาน');
		//loadForm();
	});
	
	$('#btncancel_arother').click(function(){
		$("#Products").prop("checked", true);
		$('#TSALES').empty().trigger('change');
		$('#CONTNOS').empty().trigger('change');
		$('#CUSCODS').empty().trigger('change');
		$('#PAYTYPS').empty().trigger('change');
		$('#AMOUNT').val("");
		$('#MEMO').val("");
	});
	
	//alert(_insert);
	if(_level == '1'){
		$('#btnsave_arother').attr('disabled',false);
	}else{
		if(_insert == 'T'){
			$('#btnsave_arother').attr('disabled',false);
		}else{
			$('#btnsave_arother').attr('disabled',true);
		}
	}
	
	$('#btnsave_arother').click(function(){
		Save_AROTH($thisWindowARothr);
		$('#resultt_ARother').hide(); 
	});	
}

// บันทึกลูกหนี้อื่น
function Save_AROTH($thisWindowARothr){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการบันทึกลูกหนี้อื่นหรือไม่',
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
				var INCFL = "";
				if($("#Products").is(":checked")){ INCFL = "1";}
				if($("#Services").is(":checked")){ INCFL = "2";}
				dataToPost.INCFL = INCFL;
				dataToPost.LOCAT = $('#LOCATS2').val();
				dataToPost.ARDATE = $('#cont_date').val();
				dataToPost.ARCONT = $('#AROTHRNO').val();
				dataToPost.TSALE =  (typeof $('#TSALES').find(':selected').val() === 'undefined' ? '':$('#TSALES').find(':selected').val() );
				dataToPost.CONTNO = (typeof $('#CONTNOS').find(':selected').val() === 'undefined' ? '':$('#CONTNOS').find(':selected').val() );
				dataToPost.CUSCOD = (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
				dataToPost.PAYFOR = (typeof $('#PAYTYPS').find(':selected').val() === 'undefined' ? '':$('#PAYTYPS').find(':selected').val() );
				dataToPost.USERID = $('#USERID').val();
				dataToPost.PAYAMT = $('#AMOUNT').val();
				dataToPost.VATRT = $('#RATEVAT').val();
				dataToPost.BALANCE = $('#PAYMENTS').val();
				dataToPost.MEMO = $('#MEMO').val();

				if(dataToPost.TSALE == "" || dataToPost.CUSCOD == "" || dataToPost.PAYFOR == "" || dataToPost.PAYAMT == ""){	
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
						msg: 'กรอกข้อมูลยังไม่ครบถ้วน'
					});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS05/ARother/SAVE_AROTHER',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							if(data.status == 'S'){
								$thisWindowARothr.destroy();
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
	$('#resultt_ARother').show(); 
	search();
});

//แสดงข้อมูล
function search(){
	dataToPost = new Object();
	dataToPost.LOCATS = (typeof $('#LOCATS').find(':selected').val() === 'undefined' ? '':$('#LOCATS').find(':selected').val() );
	dataToPost.AROTHR = $('#AROTHR').val();
	dataToPost.CUSCOD =(typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
	dataToPost.CONTNO = $('#CONTNO').val();
	dataToPost.TSALE = (typeof $('#TSALE').find(':selected').val() === 'undefined' ? '':$('#TSALE').find(':selected').val() );
	dataToPost.PAYFORS = (typeof $('#PAYFORS').find(':selected').val() === 'undefined' ? '':$('#PAYFORS').find(':selected').val() );
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt_ARother').html('');
	$('#resultt_ARother').append(spinner);
	
	$.ajax({
		url:'../SYS05/ARother/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt_ARother').find('.spinner, .spinner-backdrop').remove();
			$('#resultt_ARother').html(data.html);
			
			$('#table-ARother').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-ARother',1,360);
			
			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'#fff769'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$('.getit').click(function(){					
					dataToPost = new Object();
					dataToPost.cup  = _update;
					dataToPost.clev = _level;
					//alert($(this).attr('DESC1'));
					var	INCFL 	= $(this).attr('INCFL');
					var	LOCAT 	= $(this).attr('LOCAT');
					var	ARDATE 	= $(this).attr('ARDATE');
					var	ARCONT	= $(this).attr('ARCONT');
					var	TSALE	= $(this).attr('TSALE'); 
					var	DESC1	= $(this).attr('DESC1'); 
					var	CONTNO	= $(this).attr('CONTNO'); 
					var	CUSCOD 	= $(this).attr('CUSCOD');
					var	CUSNAME = $(this).attr('CUSNAME'); 
					var	PAYFOR	= $(this).attr('PAYFOR');
					var	FORDESC	= $(this).attr('FORDESC'); 
					var	USERID	= $(this).attr('USERID'); 
					var	PAYAMT 	= $(this).attr('PAYAMT'); 
					var	VATRT	= $(this).attr('VATRT'); 
					var	SMPAY	= $(this).attr('SMPAY');
					var	MEMO1	= $(this).attr('MEMO1'); 
					loadform(INCFL,LOCAT,ARDATE,ARCONT,TSALE,DESC1,CONTNO,CUSCOD,CUSNAME,PAYFOR,FORDESC,USERID,PAYAMT,VATRT,SMPAY,MEMO1);
				});
			}		
		}
	});
}

//โหลดในฟอร์ม
function loadform(INCFL,LOCAT,ARDATE,ARCONT,TSALE,DESC1,CONTNO,CUSCOD,CUSNAME,PAYFOR,FORDESC,USERID,PAYAMT,VATRT,SMPAY,MEMO1){
	dataToPost = new Object();
	dataToPost.level = _level;

	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ARother/getfromAROTHER',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกลูกหนี้อื่น',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){	
					$('#PAYTYPS').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2b/getPAYFOR',
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
						allowClear: false,
						multiple: false,
						dropdownParent: $(".lobibox-body"),
						//disabled: true,
						//theme: 'classic',
						width: '100%'
					});
					
					if(PAYFOR == '188'){
						$('.UPLOADPIC').show();
					}else{
						$('.UPLOADPIC').hide();
					}
					
					if(INCFL == '1'){
						document.getElementById("Products").checked = true;
					}else if(INCFL == '2'){
						document.getElementById("Services").checked = true;
					}
					
					newOption = new Option(LOCAT, LOCAT, false, false);
					$('#LOCATS2').empty();
					$('#LOCATS2').append(newOption).trigger('change'); 
					
					newOption = new Option(TSALE+' - '+DESC1, TSALE, false, false);
					$('#TSALES').empty();
					$('#TSALES').append(newOption).trigger('change');
					
					newOption = new Option(CONTNO, CONTNO, false, false);
					$('#CONTNOS').empty();
					$('#CONTNOS').append(newOption).trigger('change'); 
					
					newOption = new Option(CUSNAME+' ('+CUSCOD+')', CUSCOD, false, false);
					$('#CUSCODS').empty();
					$('#CUSCODS').append(newOption).trigger('change'); 
					
					newOption = new Option(PAYFOR+' - '+FORDESC, PAYFOR, false, false);
					$('#PAYTYPS').empty();
					$('#PAYTYPS').append(newOption).trigger('change'); 
					
					$('#cont_date').val(ARDATE);
					$('#AROTHRNO').val(ARCONT);
					$('#USERID').val(USERID);
					$('#AMOUNT').val(PAYAMT);
					$('#RATEVAT').val(VATRT);
					$('#PAYMENTS').val(SMPAY);
					$('#MEMO').val(MEMO1);
					
					$('#LOCATS2').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#CONTNOS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#CUSCODS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#TSALES').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					
					$('#cont_date').attr('disabled',true);
					$('#RATEVAT').attr('disabled',true);
					$('#PAYMENTS').attr('disabled',true);
					$('#Products').attr('disabled',true);
					$('#Services').attr('disabled',true);
					
					//var _update = 'T';
					if(_level == '1'){
						$('#btnsave_arother').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_arother').attr('disabled',false);
							}else{
								$('#btnsave_arother').attr('disabled',true);
								$('#PAYTYPS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#AMOUNT').attr('disabled',true);
								$('#MEMO').attr('disabled',true);
								$('#FILEPIC1').attr('disabled',true);
								$('#FILEPIC2').attr('disabled',true);
								$('#FILEPIC3').attr('disabled',true);
							}
						}else{
							$('#btnsave_arother').attr('disabled',true);
							$('#PAYTYPS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#AMOUNT').attr('disabled',true);
							$('#MEMO').attr('disabled',true);
							$('#FILEPIC1').attr('disabled',true);
							$('#FILEPIC2').attr('disabled',true);
							$('#FILEPIC3').attr('disabled',true);
						}
					}
					$('#btnsave_arother').click(function(){ 
						Edit_AROTH($this);
					});
					
					//var _delete = 'T';
					if(_level == '1'){
						$('#btndelete_arother').attr('disabled',false);
					}else{
						if(_delete == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btndelete_arother').attr('disabled',false);
								$('#btncancel_arother').attr('disabled',true);
							}else{
								$('#btndelete_arother').attr('disabled',true);
								$('#btncancel_arother').attr('disabled',true);
							}
						}else{
							$('#btndelete_arother').attr('disabled',true);;
							$('#btncancel_arother').attr('disabled',true);
						}	
					}
					$('#btndelete_arother').click(function(){ 
						Delete_AROTH($this);
					});
				}
			});			
		}
	});
}

function Edit_AROTH($thisWindowEdit){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการแก้ไขลูกหนี้อื่นหรือไม่',
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
				dataToPost.ARCONT = $('#AROTHRNO').val();
				dataToPost.PAYFOR = (typeof $('#PAYTYPS').find(':selected').val() === 'undefined' ? '':$('#PAYTYPS').find(':selected').val() );
				dataToPost.CONTNO = (typeof $('#CONTNOS').find(':selected').val() === 'undefined' ? '':$('#CONTNOS').find(':selected').val() );
				dataToPost.CUSCOD = (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
				dataToPost.PAYAMT = $('#AMOUNT').val();
				dataToPost.BALANCE = $('#PAYMENTS').val();
				dataToPost.MEMO = $('#MEMO').val();

				if(dataToPost.PAYFOR == "" || dataToPost.PAYAMT == "" || dataToPost.BALANCE >= dataToPost.PAYAMT){	
					$msg = "";
					if(dataToPost.PAYFOR == ""){
						$msg = "กรุณาเลือกค้างชำระ";
					}else if(dataToPost.PAYAMT == ""){
						$msg = "กรุณาระบุจำนวนเงิน";
					}else if(dataToPost.BALANCE >= dataToPost.PAYAMT ){
						$msg = "ไม่สามารถแก้ไขได้ เนื่องจากมีการรับชำระแล้ว";
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
						url:'../SYS05/ARother/Edit_AROTHER',
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

function Delete_AROTH($thisWindowARothrDel){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: 'คุณต้องการลบลูกหนี้อื่นหรือไม่',
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
				dataToPost.ARCONT = $('#AROTHRNO').val();
				dataToPost.LOCAT = $('#LOCATS2').val();
				dataToPost.CONTNO = (typeof $('#CONTNOS').find(':selected').val() === 'undefined' ? '':$('#CONTNOS').find(':selected').val() );
				dataToPost.CUSCOD = (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );

				$('#loadding').show();
				$.ajax({
					url:'../SYS05/ARother/Delete_AROTHER',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if(data.status == 'S'){
							$thisWindowARothrDel.destroy();
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


