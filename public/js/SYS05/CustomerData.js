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
		$('#dataTables-HCsale tbody').html('');
		$('#dataTables-AOsale tbody').html('');
		$('#dataTables-ARmgra tbody').html('');
		$('#dataTables-HCsale tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		$('#dataTables-AOsale tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		$('#dataTables-ARmgra tbody').html("<table width='100%' height='100%'><tr><td colspan='8'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		reportsearch = $.ajax({
			url: '../SYS05/CustomerData/search',
			data: dataToPost,
			Type: 'POST',
			dataType:'json',
			success: function(data){	
				$('#dataTables-HCsale tbody').empty().append(data.html1);
				document.getElementById("dataTable-fixed-HCsale").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				
				$('#dataTables-AOsale tbody').empty().append(data.html2);
				document.getElementById("dataTable-fixed-AOsale").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				
				$('#dataTables-ARmgra tbody').empty().append(data.html3);
				document.getElementById("dataTable-fixed-ARmgra").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				
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