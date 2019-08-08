//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');

//หน้าแรก
$(function(){
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
			url: '../Cselect2b/getCONTNO_ExchangCar',
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
});
	
//กดเพิ่มข้อมูล	
$('#bth1add').click(function(){
	dataToPost = new Object();
	dataToPost.level = _level;
	$('#loadding').show();
	$.ajax({
		url:'../SYS05/ExchangeCar/getfromExchangeCar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรถแลกเปลี่ยน',
				width: $(window).width(),
				height: $(window).height(),
				//width:'100%',
				//height:'100%',
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					Add_ExchangCar($this);
				}
	
			});			
		}
	});
});

function Add_ExchangCar(){
	$('#CONTNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNO_ExchangCar',
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%',
	});	
	
	$('#GCODENEW').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getGCode',
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		dropdownAutoWidth : true,
		width: '100%'
	});	
	
	CONTNOCHANGE = null
	$('#CONTNO').change(function(){ 
		var contno =  (typeof $('#CONTNO').find(":selected").val() === 'undefined' ? '' : $('#CONTNO').find(":selected").val());
		dataToPost = new Object();
		dataToPost.contno = contno;
		CONTNOCHANGE = $.ajax({
			url : '../SYS05/ExchangeCar/searchCONTNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){	
				if(contno != ''){
					$('#CUSNAME').val(data.CUSNAME);
					$('#CUSCOD').val(data.CUSCOD);
					$('#REGNO').val(data.REGNO);
					$('#STRNO').val(data.STRNO);
				}else{
					$('#CUSNAME').val('');
					$('#CUSCOD').val('');
					$('#REGNO').val('');
					$('#STRNO').val('');
				}
				CONTNOCHANGE = null;
			},
			beforeSend: function(){
				if(CONTNOCHANGE !== null){
					CONTNOCHANGE.abort();
				}
			}
		});
	});
}
