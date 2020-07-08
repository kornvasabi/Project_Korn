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
			fn_datatables('table-PriceSpecial',1,350);
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
					if($(this).attr('STATUS') == "ขายแล้ว"){
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
	});
}
$('#btninsert').click(function(){
	fn_loadformSetPrice($(this),'add');
});
function fn_loadformSetPrice($this,$event){
	dataToPost = new Object();
	dataToPost.STRNO = (typeof $this.attr('STRNO') === 'undefined' ? '':$this.attr('STRNO'));
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
					fn_loadPropoties($this,data.EVENT);
				}
			});
		}
	});
}
function fn_loadPropoties($this,$EVENT){
	if(_insert == "T"){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
	if(_update == "T"){
		$('#btnsave').attr('disabled',false);
	}else{
		$('#btnsave').attr('disabled',true);
	}
	if(_delete == "T"){
		$('#btndelete').attr('disabled',false);
	}else{
		$('#btndelete').attr('disabled',true);
	}
	if($EVENT == 'add'){
		$('#btndelete').hide(0);
		$('#ID').attr('disabled',false);
	}else{
		$('#btnclear').hide(0);
		$('#ID').attr('disabled',true);
	}
	$('#btnclear').click(function(){
		$('#STRNO').val('');
		$('#PRICE').val('');
		$('#ISTYPE').val('');
		$('#INSBY').val('');
	});
	$('#btnsave').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
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
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_save($this)
				}
			}
		});
	});	
	$('#btndelete').click(function(){
		var id = '<span style="color:red;">'+$('#ID').val()+'</span>';
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
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
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_delete($this);
				}
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
