/********************************************************
             ______@26/10/2020______
            pasakorn boonded
********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
$(function(){
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
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
		width: '100%'
	});
	$('#add_locat').change(function(){
		dataToPost = new Object();
		dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
		$('#loadding').fadeIn(200);	
		$.ajax({
			url: '../Cselect2K/changeLOCAT',
			data:dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);

				$('#add_locatnm').val(data.locatnm);
			}
		});
	});
});
$('#btn_save').click(function(){
	var locat   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val()); 
	var closedt = $('#add_closedt').val();
	if(locat === "" || closedt === ""){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: "กรุณาเลือกสาขาและวันที่ปิดบัญชีก่อนครับ"
		});
	}else{
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึก ?',
			closeButton: false,
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-cancel',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){ 
					fnSave(); 
				}
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});	
	}
});
function fnSave(){
	dataToPost = new Object();
	dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.closedt = $('#add_closedt').val();
	$('#loadding').fadeIn(200);	
	$.ajax({
		url: '../SYS06/CloseAccountLocat/Save',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				
				$('#add_locat').empty();
				$('#add_locatnm').val("");
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
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
		},error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}








