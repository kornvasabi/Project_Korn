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
	
	$('#CONTNO1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getCONTNOinTAX',
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
			url: '../Cselect2b/getCUSTOMERS_AR',
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

INVNOCHANGE = null
$('#btnsearch').click(function(){
	var INVNO1 	=  (typeof $('#INVNO1').find(":selected").val() === 'undefined' ? '' : $('#INVNO1').find(":selected").val());
	var CONTNO1 =  (typeof $('#CONTNO1').find(":selected").val() === 'undefined' ? '' : $('#CONTNO1').find(":selected").val());
	var CUSCOD1 =  (typeof $('#CUSCOD1').find(":selected").val() === 'undefined' ? '' : $('#CUSCOD1').find(":selected").val());
	dataToPost = new Object();
	dataToPost.INVNO1 	= INVNO1;
	dataToPost.CONTNO1 	= CONTNO1;
	dataToPost.CUSCOD1 	= CUSCOD1;
	
	if(dataToPost.INVNO1 == '' && dataToPost.CONTNO1 == '' && dataToPost.CUSCOD1 == ''){
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
			msg: 'กรุณาระบุเงื่อนไขในการค้นหา'
		});
	}else{
		$('#dataTables-taxdata tbody').html('');
		$('#dataTables-taxdata tbody').html("<table width='100%' height='100%'><tr><td colspan='7'><img src='../public/images/loading-icon2.gif' style='width:50px;height:25px;'></td></tr></table>");
		
		INVNOCHANGE = $.ajax({
			url : '../SYS07/Formbycontno/searchINVNO',
			data : dataToPost,
			type : "POST",
			dataType : "json",
			success: function(data){
				$('#dataTables-taxdata tbody').empty().append(data.taxdata);
				document.getElementById("dataTable-fixed-taxdata").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				INVNOCHANGE = null;
			},
			beforeSend: function(){
				if(INVNOCHANGE !== null){
					INVNOCHANGE.abort();
				}
			}
		});
	}
});

$('#btnprint').click(function(){
	printInvoine();
});

function printInvoine(){
	
	var printtype = "";
	if($("#form").is(":checked")){ 
		printtype = "form";
	}else if($("#report").is(":checked")){
		printtype = "report";
	}
	
	var address = "";
	if($("#add1").is(":checked")){ 
		address = "";
	}else if($("#add2").is(":checked")){
		address = "";
	}

	dataToPost = new Object();
	dataToPost.INVNO1 		=  (typeof $('#INVNO1').find(":selected").val() === 'undefined' ? '' : $('#INVNO1').find(":selected").val());
	dataToPost.CONTNO1 		=  (typeof $('#CONTNO1').find(":selected").val() === 'undefined' ? '' : $('#CONTNO1').find(":selected").val());
	dataToPost.CUSCOD1 		=  (typeof $('#CUSCOD1').find(":selected").val() === 'undefined' ? '' : $('#CUSCOD1').find(":selected").val());
	dataToPost.FRMINVNO 	= $('#FRMINVNO').val();
	dataToPost.TOINVNO 		= $('#TOINVNO').val();
	dataToPost.printtype 	= printtype;
	dataToPost.address 		= address;
	
	$.ajax({
		url: '../SYS07/Formbycontno/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/Formbycontno/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์ใบกำกับ',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
		}
	});
}