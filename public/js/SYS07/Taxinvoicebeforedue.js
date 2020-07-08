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
	$(document).ready(function() {
		$('input:radio[name=vat]').change(function() {
			if (this.value == 'more'){
				$('#VATDATE').attr('disabled',false);
				$('#VATDATE').val(_today);
			}else{ 
				$('#VATDATE').val('');
				$('#VATDATE').attr('disabled',true);
			}
		});
	});
	
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
	
	$('#CONTNO1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_ChangeContstat',
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

CHANGE = null
$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.LOCAT1 	= (typeof $('#LOCAT1').find(":selected").val() === 'undefined' ? '' : $('#LOCAT1').find(":selected").val());
	dataToPost.FRMDATE 	= $('#FRMDATE').val();
	dataToPost.TODATE 	= $('#TODATE').val();
		
	CHANGE = $.ajax({
		url : '../SYS07/Taxinvoicebeforedue/searchLASTTAXNO',
		data : dataToPost,
		type : "POST",
		dataType : "json",
		success: function(data){
			$('#LRUNDT').val(data.Lrundt);
			$('#LTAXNO').val(data.Ltaxno);
			CHANGE = null;
		},
		beforeSend: function(){
			if(CHANGE !== null){
				CHANGE.abort();
			}
		}
	});
});

$('#btnprint').click(function(){
	printInvoine();
});

function printInvoine(){
	var vat = "";
	if($("#normal").is(":checked")){ 
		vat = "normal";
	}else if($("#more").is(":checked")){
		vat = "more";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(":selected").val() === 'undefined' ? '' : $('#LOCAT1').find(":selected").val());
	dataToPost.CONTNO1 		= (typeof $('#CONTNO1').find(":selected").val() === 'undefined' ? '' : $('#CONTNO1').find(":selected").val());
	dataToPost.VATDATE 		= $('#VATDATE').val();
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.vat 			= vat;
	
	$.ajax({
		url: '../SYS07/Taxinvoicebeforedue/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/Taxinvoicebeforedue/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
		}
	});
}