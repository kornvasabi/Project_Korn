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
	$('#CUSCOD1').select2({
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
	
	CUSCHANGE = null
	
	$('#CUSCOD1').change(function(){ 
		var cuscod = (typeof $('#CUSCOD1').find(":selected").val() === 'undefined' ? '' : $('#CUSCOD1').find(":selected").val());
		dataToPost = new Object();
		dataToPost.cuscod = cuscod
		CUSCHANGE = $.ajax({
			url : "../SYS05/CustomerData/Customerdetail",
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				$('#DESCRIPTION').val(data.CUSTADD);		
				CUSCHANGE = null;
			},
			beforeSend: function(){
				if(CUSCHANGE !== null){
					CUSCHANGE.abort();
				}
			}
		});
	});
});

//กดแสดงข้อมูล
$('#btnt1search').click(function(){
	search();
});

var reportsearch = null;
function search(){
	dataToPost = new Object();
	dataToPost.CUSCOD1 = (typeof $('#CUSCOD1').find(':selected').val() === 'undefined' ? '':$('#CUSCOD1').find(':selected').val());
	
	if(dataToPost.CUSCOD1 == ''){
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
			msg: 'กรุณาระบุลูกค้า'
		});
	}else{
		$('#resultt_HCsale').html('');
		$('#resultt_AOsale').html('');
		$('#resultt_ARmgra').html('');
		$('#resultt_HCsale').html("<table width='100%' height='100%'><tr><td align='center'><img src='../public/images/loading-icon.gif' style='width:130px;height:130px;'></td></tr></table>");
		$('#resultt_ARmgra').html("<table width='100%' height='100%'><tr><td align='center'><img src='../public/images/loading-icon.gif' style='width:130px;height:130px;'></td></tr></table>");
		reportsearch = $.ajax({
			url: '../SYS05/CustomerData/search',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				
				$('#resultt_HCsale').html(data.html1);
				$('#resultt_AOsale').html(data.html2);
				$('#resultt_ARmgra').html(data.html3);
				
				fn_datatables('table-HCsale',3,680);
				fn_datatables('table-AOsale',3,680,);
				fn_datatables('table-ARmgra',3,680,);

				$('.dataTables_paginate').hide();
				$('.dataTables_info').hide();
				reportsearch = null;
			},
			beforeSend: function(){
				if(reportsearch !== null){
					reportsearch.abort();
				}
			}
		});
	}
}