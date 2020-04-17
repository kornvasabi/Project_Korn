/********************************************************
             ______@04/11/2019______
			 Pasakorn

********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
$(function(){
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../select2_test/CUSCOD',
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
		//dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});
$('#btnReportVat').click(function(){
	dataToPost = new Object();
	dataToPost.CUSCOD = $('#CUSCOD').val();
	$.ajax({
		url: '../SYS13/Korn/testloop',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if(data.status == 'Y'){
				alert('สำเร็จ');
			}else{
				alert('ไม่สำเร็จ');
			}
		}
	});
});