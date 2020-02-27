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
			url: '../Cselect2b/getTAXNO',
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
			
		INVNOCHANGE = $.ajax({
			url : '../SYS07/Cancelinvoicedue/searchINVNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				if(INVNO1 != ''){
					$('#VATDATE').val(data.TAXDT);
					$('#CONTNO1').val(data.CONTNO);
					$('#LOCAT1').val(data.LOCAT);
					$('#CUSCOD1').val(data.CUSCOD);
					$('#CUSNAME1').val(data.CUSNAME);
					$('#AMOUNT1').val(data.TOTAMT);
					$('#FPAY').val(data.FPAY);
					$('#LPAY').val(data.LPAY);
					$('#DETAIL').val(data.DESCP);	

					if(data.FLAG == 'C'){
						$('#INVSTATUS').show();
						$('#btncancel').attr('disabled',true);
					}else{
						$('#INVSTATUS').hide();
						$('#btncancel').attr('disabled',false);
					}
				}else{
					$('#VATDATE').val('');
					$('#CONTNO1').val('');
					$('#LOCAT1').val('');
					$('#CUSCOD1').val('');
					$('#CUSNAME1').val('');
					$('#AMOUNT1').val('');
					$('#FPAY').val('');
					$('#LPAY').val('');
					$('#DETAIL').val('');

					$('#INVSTATUS').hide();
					$('#btncancel').attr('disabled',false);
				}
				INVNOCHANGE = null;
			},
			beforeSend: function(){
				if(INVNOCHANGE !== null){
					INVNOCHANGE.abort();
				}
			}
		});
	});
});

//กดแสดงข้อมูล
$('#btncancel').click(function(){
	alert('ยังไม่เปิดใช้งานเมนูนี้ค่ะ');
	//search();
});