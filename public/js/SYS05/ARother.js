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
				closeOnEsc: false,
				shown: function($this){
					Add_AROTHER($this);
				}
	
			});			
		}
	});
});

function Add_AROTHER($thisWindowARothr){
	$('.SHOWPIC').hide();
	
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
	
	$("#PIC1").click(function(){
		var pic = $(this).attr("pic");
		var status = "upload";
		showformupload(pic,status);
	});
	
	$("#PIC2").click(function(){
		var pic = $(this).attr("pic");
		var status = "upload";
		showformupload(pic,status);
	});
	
	$("#PIC3").click(function(){
		var pic = $(this).attr("pic");
		var status = "upload";
		showformupload(pic,status);
	});
	
	$('#btncancel_arother').click(function(){
		$("#Products").prop("checked", true);
		$('#TSALES').empty().trigger('change');
		$('#CONTNOS').empty().trigger('change');
		$('#CUSCODS').empty().trigger('change');
		$('#PAYTYPS').empty().trigger('change');
		$('#AMOUNT').val("");
		$('#MEMO').val("");
		$('.namepic').val("");
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

function showformupload(pic,status){
	dataToPost = new Object();
	$.ajax({
		url:'../SYS05/ARother/formupload'
		,data: dataToPost
		,type:'POST'
		,dataType:'json'
		,success: function(data){
			Lobibox.window({
				title: 'อัพโหลดภาพ'
				,width:'100%'
				,height:'100%'
				,content: data
				,draggable: false
				,closeOnEsc: true
				,shown: function($this){
					uploadForm($this,pic,status);
				}
				,beforeClose : function(){
				}
				,sound: false
			});	
		}
	});
}

function uploadForm($thisupload,$pic,$status){
	var file = $('#showoldupload').uploadFile({
		url:'../SYS05/ARother/showfile'
		,fileName: 'myfile'
		,maxFileCount: 1
		,multiple: false
		,maxFileSize: 10240*1024 // Allow size 10MB
		,showProgress: true
		,allowedTypes: "jpg,jpeg,png"
		,acceptFiles: 'image/*,application/pdf/vnd.ms-excel,application'
		,dynamicFormData: function(){
			var data = { 
				selectpic 	: $pic
			}
			return data;
		}
		,showPreview:true
		,previewHeight: '297px'
		,previewWidth: '210px'
		,dragDropStr: 'เลือกไฟล์'
		,abortStr:'เลือกไฟล์'
		,cancelStr:'ยกเลิก'
		,doneStr:'ผิดพลาด :: doneStr'
		,multiDragErrorStr: 'ผิดพลาด :: ลากวางได้ครั้งละ 1 รูป'
		,extErrorStr:'ผิดพลาด :: ต้องเป็นไฟล์ '
		,sizeErrorStr:'ผิดพลาด sizeErrorStr'
		,uploadErrorStr:'ผิดพลาด uploadErrorStr'
		,maxFileCountErrorStr: 'กรุณายกเลิกไฟล์เดิมก่อน :'
		,uploadStr:'เลือกไฟล์'
		//เปลี่ยนชื่อรูปตอนอัพโหลดเสร็จ
		,onSuccess:function(files,data,xhr,pd) {
			//var name = $('.ajax-file-upload-filename').html(); //1). git.pdf (359.30 KB)
			var json = JSON.parse(data.trim());
			var name_old = json['origin'][0];
			var name_new = json['newname'][0];
			$('.namepic[pic='+$pic+']').val(name_old);
			$('.namepic[pic='+$pic+']').attr('newname',name_new);
		}
		,showStatusAfterSuccess: true
		,autoSubmit:false
	});
	
	if($status == "notupdate"){
		$('#clickup').attr('disabled',true);
	}
	
	$("#clickup").click(function(){
		var delpic = $('.namepic[pic='+$pic+']').val();
		if(delpic != ""){
			deletefile(delpic);
		}
		file.startUpload();
		$thisupload.destroy();
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
				dataToPost.INCFL 	= INCFL;
				dataToPost.LOCAT 	= $('#LOCATS2').val();
				dataToPost.ARDATE 	= $('#cont_date').val();
				dataToPost.ARCONT 	= $('#AROTHRNO').val();
				dataToPost.TSALE 	= (typeof $('#TSALES').find(':selected').val() === 'undefined' ? '':$('#TSALES').find(':selected').val() );
				dataToPost.CONTNO 	= (typeof $('#CONTNOS').find(':selected').val() === 'undefined' ? '':$('#CONTNOS').find(':selected').val() );
				dataToPost.CUSCOD 	= (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
				dataToPost.PAYFOR 	= (typeof $('#PAYTYPS').find(':selected').val() === 'undefined' ? '':$('#PAYTYPS').find(':selected').val() );
				dataToPost.USERID 	= $('#USERID').val();
				dataToPost.PAYAMT 	= $('#AMOUNT').val();
				dataToPost.VATRT 	= $('#RATEVAT').val();
				dataToPost.BALANCE 	= $('#PAYMENTS').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				
				dataToPost.PIC1 	= $('#FILEPIC1').attr("newname");
				dataToPost.PIC2 	= $('#FILEPIC2').attr("newname");
				dataToPost.PIC3 	= $('#FILEPIC3').attr("newname");

				if(dataToPost.TSALE == "" || dataToPost.CUSCOD == "" || dataToPost.PAYFOR == "" || dataToPost.PAYAMT == ""){
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 15000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',
							soundExt: '.ogg',
							icon: true,
							messageHeight: '90vh',
							msg: 'กรอกข้อมูลยังไม่ครบถ้วน'
						});
				}else if(dataToPost.PAYFOR == "188" && (dataToPost.PIC1 == "" || dataToPost.PIC2 == "" || dataToPost.PIC3 == "")){
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 15000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',
							soundExt: '.ogg',
							icon: true,
							messageHeight: '90vh',
							msg: 'แนบรูปภาพไม่ครบถ้วน'
						});
				}else{
					$('#loadding').show();
					$.ajax({
						url:'../SYS05/ARother/SAVE_AROTHER',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							
							//uploadpicture();
							
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
	dataToPost.LOCATS 	= (typeof $('#LOCATS').find(':selected').val() === 'undefined' ? '':$('#LOCATS').find(':selected').val() );
	dataToPost.AROTHR 	= $('#AROTHR').val();
	dataToPost.CUSCOD 	= (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
	dataToPost.CONTNO 	= $('#CONTNO').val();
	dataToPost.TSALE 	= (typeof $('#TSALE').find(':selected').val() === 'undefined' ? '':$('#TSALE').find(':selected').val() );
	dataToPost.PAYFORS 	= (typeof $('#PAYFORS').find(':selected').val() === 'undefined' ? '':$('#PAYFORS').find(':selected').val() );
	
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
					
					dataToPost = new Object();
					dataToPost.arcont  = $(this).attr('ARCONT');
					
					$.ajax({
						url:'../SYS05/ARother/serchpicupload',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							var up_PIC1 	= data.up_PIC1;
							var up_PIC2 	= data.up_PIC2;
							var up_PIC3 	= data.up_PIC3;
							var filePath 	= data.up_filePath
							loadform(INCFL,LOCAT,ARDATE,ARCONT,TSALE,DESC1,CONTNO,CUSCOD,CUSNAME,PAYFOR,FORDESC,USERID,PAYAMT,VATRT,SMPAY,MEMO1,
							up_PIC1,up_PIC2,up_PIC3,filePath);			
						}
					});
				});
			}		
		}
	});
}

//โหลดในฟอร์ม
function loadform(INCFL,LOCAT,ARDATE,ARCONT,TSALE,DESC1,CONTNO,CUSCOD,CUSNAME,PAYFOR,FORDESC,USERID,PAYAMT,VATRT,SMPAY,MEMO1,up_PIC1,up_PIC2,up_PIC3,filePath){
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
				closeOnEsc: false,
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
					$('#FILEPIC1').val(up_PIC1);
					$('#FILEPIC2').val(up_PIC2);
					$('#FILEPIC3').val(up_PIC3);
					
					$('#LOCATS2').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#CONTNOS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#CUSCODS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					$('#TSALES').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
					
					$('#cont_date').attr('disabled',true);
					$('#RATEVAT').attr('disabled',true);
					$('#PAYMENTS').attr('disabled',true);
					$('#Products').attr('disabled',true);
					$('#Services').attr('disabled',true);
					$('#AMOUNT').attr('disabled',true);
					
					$('#SHOWP1').click(function() {
						show_img(ARCONT,filePath,up_PIC1);
					});
					$('#SHOWP2').click(function() {
						show_img(ARCONT,filePath,up_PIC2);
					});
					$('#SHOWP3').click(function() {
						show_img(ARCONT,filePath,up_PIC3);
					});
					
					//var _update = 'N';
					if(_level == '1'){
						$('#btnsave_arother').attr('disabled',false);
					}else{
						if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
							if(_locat == LOCAT){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
								$('#btnsave_arother').attr('disabled',false);
							}else{
								$('#btnsave_arother').attr('disabled',true);
								$('#PAYTYPS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
								$('#MEMO').attr('disabled',true);
								var status = "notupdate";
							}
						}else{
							$('#btnsave_arother').attr('disabled',true);
							$('#PAYTYPS').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
							$('#MEMO').attr('disabled',true);
							var status = "notupdate";
						}
					}
					$('#btnsave_arother').click(function(){ 
						Edit_AROTH($this);
					});
					
					$("#PIC1").click(function(){
						var pic = $(this).attr("pic");
						showformupload(pic,status);
					});
					
					$("#PIC2").click(function(){
						var pic = $(this).attr("pic");
						showformupload(pic,status);
					});
					
					$("#PIC3").click(function(){
						var pic = $(this).attr("pic");
						showformupload(pic,status);
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

function show_img(ARCONT,filePath,up_PIC){
	dataToPost = new Object();
	dataToPost.ARCONT = ARCONT;
	dataToPost.url = filePath;
	dataToPost.pic = up_PIC;
	$.ajax({
		url:'../SYS05/ARother/getfromshowimg',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'เลขที่สัญญาลูกหนี้อื่น '+ARCONT,
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($this){
					$('#btnclose').click(function(){ 
						$this.destroy();
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
				dataToPost.ARCONT 	= $('#AROTHRNO').val();
				dataToPost.PAYFOR 	= (typeof $('#PAYTYPS').find(':selected').val() === 'undefined' ? '':$('#PAYTYPS').find(':selected').val() );
				dataToPost.CONTNO 	= (typeof $('#CONTNOS').find(':selected').val() === 'undefined' ? '':$('#CONTNOS').find(':selected').val() );
				dataToPost.CUSCOD 	= (typeof $('#CUSCODS').find(':selected').val() === 'undefined' ? '':$('#CUSCODS').find(':selected').val() );
				dataToPost.PAYAMT 	= $('#AMOUNT').val();
				dataToPost.BALANCE 	= $('#PAYMENTS').val();
				dataToPost.MEMO 	= $('#MEMO').val();
				
				dataToPost.PIC1 	= $('#FILEPIC1').attr("newname");
				dataToPost.PIC2 	= $('#FILEPIC2').attr("newname");
				dataToPost.PIC3 	= $('#FILEPIC3').attr("newname");

				if(dataToPost.PAYFOR == "" || dataToPost.BALANCE >= dataToPost.PAYAMT){	
					$msg = "";
					if(dataToPost.PAYFOR == ""){
						$msg = "กรุณาเลือกค้างชำระ";
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
				
				var  delpic1	= $('#FILEPIC1').val();
				var  delpic2 	= $('#FILEPIC2').val();
				var  delpic3	= $('#FILEPIC3').val();
				
				$('#loadding').show();
				$.ajax({
					url:'../SYS05/ARother/Delete_AROTHER',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if(data.status == 'S'){
							deletefile(delpic1);
							deletefile(delpic2);
							deletefile(delpic3);
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

function deletefile(delpic){
	dataToPost = new Object();
	dataToPost.delpic = delpic;
	$.ajax({
		url:'../SYS05/ARother/deletefile',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data) {
			//alert('delete');
		}
	});
}


