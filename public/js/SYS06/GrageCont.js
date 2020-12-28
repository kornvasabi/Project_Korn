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
});
$('#btn_calc').click(function(){
	var locat   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val()); 
	var contno  = $('#add_contno').val();
	if(locat === "" || contno === ""){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: "กรุณาเลือกสาขาและระบุเลขที่สัญญาก่อนครับ"
		});
	}else{
		fnSave();	
	}
});
function fnSave(){
	dataToPost = new Object();
	dataToPost.locat  = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.contno = $('#add_contno').val();
	dataToPost.debtor = ($('#debtor').is(':checked') ? 'Y':'N'); 
	$('#loadding').fadeIn(200);	
	$.ajax({
		url: '../SYS06/GrageCont/Calculator',
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
				$('#add_contno').val("");
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









