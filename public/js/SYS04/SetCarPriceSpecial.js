/********************************************************
             ______@22/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#btnsearch').click(function(){
		fn_searchSTDSpecial();
	});
});
var pricespecial = null;
function fn_searchSTDSpecial(){
	dataToPost = new Object();
	dataToPost.ID     = $('#IDS').val();
	dataToPost.STRNO  = $('#STRNOS').val();
	dataToPost.PRICE  = $('#PRICES').val();
	dataToPost.ISTYPE = $('#ISTYPES').val();
	$('#loadding').fadeIn(500);
	pricespecial = $.ajax({
		url:'../SYS04/SetCarPriceSpecial/Search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(100);
			$("#result").html(data.html);
			
			$('#table-PriceSpecial').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-PriceSpecial',1,400);
			/*
			// Export data to Excel
			$('.data-export').prepend('<img id="table-ReserveCar-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-ReserveCar-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ใบจอง","ReserveCar"); 
			});
			*/
			function redraw(){
				$('.IDClick').unbind('click');
				$('.IDClick').click(function(){
					if($(this).attr('STATUS') == "sale"){
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 5000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: "รถคันนี้ถูกขายแล้วครับ"
						});
					}else{
						fn_loadformSetPrice($(this),'edit');
					}
				});
				$('.getit').hover(function(){
					$(this).css({'background-color':'#a9a9f9'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
			}
		}
		,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
$('#btninsert').click(function(){
	fn_loadformSetPrice($(this),'add');
});
function fn_loadformSetPrice($this,$event){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $this.attr('STRNO') === 'undefined' ? '':$this.attr('STRNO'));
	dataToPost.ID    = (typeof $this.attr('IDS') === 'undefined' ? '':$this.attr('IDS'));
	dataToPost.EVENT = $event;
	$('#loadding').fadeIn(250);
	$.ajax({
		url:'../SYS04/SetCarPriceSpecial/getformSetCarPrice',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
			Lobibox.window({
				title: 'เพิ่มรายการกำหนดราคารถ',
				width: 600,
				height: 600,
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					fn_loadPropoties($this,data,$event);
				}
			});
		}
	});
}
function fn_loadPropoties($this,$data,$event){
	$("#ISTYPE").select2({
        placeholder: 'เลือก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#ISTYPE").parent().parent(),
        width: '100%'
    });
	
	if($event == "add"){
		if(_insert == "T"){
			$('#btnsave').attr('disabled',false);
		}else{
			$('#btnsave').attr('disabled',true);
		}
	}else{
		if(_update == "T"){
			$('#btnsave').attr('disabled',false);
		}else{
			$('#btnsave').attr('disabled',true);
		}
	}
	
	if(_delete == "T"){
		$('#btndelete').attr('disabled',false);
	}else{
		$('#btndelete').attr('disabled',true);
	}
	if($data.EVENT == 'add'){
		$('#btndelete').hide(0);
		$('#ID').attr('disabled',false);
	}else{
		$('#StartDT').val($data.StartDT);
		$('#INSBY').val($data.INSBY);
		$('#EndDT').val($data.EndDT);
		$('#btnclear').hide(0);
		$('#ID').attr('disabled',true);
	}
	$('#btnclear').click(function(){
		$('#STRNO').val('');
		$('#PRICE').val('');
		$('#ISTYPE').val('');
		//$('#INSBY').val('');
	});
	$('#btnsave').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: 'คุณต้องการบันทึกรายกำหนดราคาพิเศษรถ ?',
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
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_save($this)
				}
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});	
	$('#btndelete').click(function(){
		var id = '<span style="color:red;">'+$('#ID').val()+'</span>';
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: 'คุณต้องการลบรายกำหนดราคาพิเศษรถ รหัส : '+id+' ?',
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
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_delete($this);
				}
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
}
var SaveCarPrice = null;
function fn_save($this){
	dataToPost = new Object();
	dataToPost.ID     = $('#ID').val();
	dataToPost.STRNO  = $('#STRNO').val();
	dataToPost.PRICE  = $('#PRICE').val();
	dataToPost.ISTYPE = $('#ISTYPE').val();
	dataToPost.StartDT= $('#StartDT').val();
	dataToPost.EndDT  = $('#EndDT').val();
	dataToPost.INSBY  = $('#INSBY').val();
	$('#loadding').fadeIn(250);
	SaveCarPrice = $.ajax({
		url:'../SYS04/SetCarPriceSpecial/SaveSCPS',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
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
			else if(data.status){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					soundExt: '.ogg',
					msg: data.msg
				});
				fn_searchSTDSpecial();
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
					soundExt: '.ogg',
					msg: data.msg
				});
			}
			SaveCarPrice = null;
		},
		beforeSend: function(){
			if(SaveCarPrice !== null){
				SaveCarPrice.abort();
			}
		}
	});
}
var DelCarPrice = null;
function fn_delete($this){
	dataToPost = new Object();
	dataToPost.ID     = $('#ID').val();
	$('#loadding').fadeIn(250);
	DelCarPrice = $.ajax({
		url:'../SYS04/SetCarPriceSpecial/DelSCPS',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
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
			else if(data.status){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					soundExt: '.ogg',
					msg: data.msg
				});
				$this.destroy();
				fn_searchSTDSpecial();
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
					soundExt: '.ogg',
					msg: data.msg
				});
			}
			DelCarPrice = null;
		},
		beforeSend: function(){
			if(DelCarPrice !== null){
				DelCarPrice.abort();
			}
		}
	});
}
/*
var KBbtn1import = null;
$('#btnimport').click(function(){
	KBbtn1import = $.ajax({
		url:'../SYS04/SetCarPriceSpecial/stdFormUPLOAD',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'นำเข้ารายการสแตนดาร์ด',
				//width: $(window).width(),
				height: '200',
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$("#form_import").unbind('click');
					$("#form_import").click(function(){
						window.open("../public/form_upload/std_sell_multiple.xlsx");
					});
				}
			});
			
			KBbtn1import = null;
		},
		beforeSend: function(){ if(KBbtn1import !== null){ KBbtn1import.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});
*/
$('#btn1import').click(function(){
	var html =  "<div class='row'>";
		html += "	<input type='button' id='form_import' class='btn btn-info btn-sm' style='width:100%;' value='ดาวน์โหลดฟอร์มนำเข้า'>";		
		html += "</div><hr>";	
		html += "<div class='row'>";	
		html += "	<div id='form_std'></div>";		
		html += "</div>";
	Lobibox.window({
		title: 'นำเข้ารายการสแตนดาร์ด',
		//width: $(window).width(),
		height: '200',
		content: html,
		draggable: false,
		closeOnEsc: false,
		shown: function($this){
			$("#form_std").uploadFile({		
				url:'../SYS04/SetCarPriceSpecial/import_setprice',
				fileName:'myfile',
				autoSubmit: true,
				acceptFiles: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
				allowedTypes: 'xls,xlsx',
				onSubmit:function(files){			
					$("#loadding").fadeIn(200);
				},
				onSuccess:function(files,data,xhr,pd){
					obj = JSON.parse(data);
					
					if(obj["error"]){
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: false,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: obj["errorMsg"]
						});
					}else{
						Lobibox.window({
							title: 'นำเข้าสแตนดาร์ดกำหนดรถราคาพิเศษ',
							width: $(window).width(),
							height: $(window).height(),
							content: obj["html"],
							draggable: false,
							closeOnEsc: false,
							shown: function($this){
								fn_importdb($this);
							}
						});

						$this.destroy();
					}
					
					$("#loadding").fadeOut(200);
				}
			});
			
			$("#form_import").unbind('click');
			$("#form_import").click(function(){
				window.open("../public/form_upload/CopyofFormUploadSpecial.xlsx");
			});
		}
	});
});

function fn_importdb($thiswindows){
	var KB_importdb = null; 
	$('#std_import').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: "คุณต้องการนำเข้าข้อมูลสแตนดาร์ดหรือไม่",
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: 'นำเข้า',
					closeOnClick: false,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
					
					var list = [];
					$(".listSpecial").each(function(){
						var datalist = [];
						datalist.push($(this).attr('STRNO'));
						datalist.push($(this).attr('PRICE'));
						datalist.push($(this).attr('STARTDT'));
						datalist.push($(this).attr('MODEL'));
						datalist.push($(this).attr('IDKEY'));
						
						list.push(datalist);
					});
					//alert(list);
					dataToPost.LISTPS = list;
					$('#loadding').fadeIn(200);
					KB_importdb = $.ajax({
						url:'../SYS04/SetCarPriceSpecial/import_save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							if(data.error){
								var msg = data.msg2.length;
								var msgNum = "";
								for(i=0;i<msg;i++){
									if(i>0){ msgNum +="<br >";}
									msgNum += (i+1)+". "+data.msg2[i];
								}
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg1+="<br >"+msgNum
								});
							}else if(data.status){
								Lobibox.notify('success', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
							
							KB_importdb = null;
							lobibox.destroy();
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(KB_importdb !== null){ KB_importdb.abort(); } }
						
						,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}
				
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
}