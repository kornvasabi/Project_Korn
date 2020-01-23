/********************************************************
             ______@14/01/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#btnaddcus').click(function(){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../Cselect2K/getfromCUSCOD',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.window({
					title: 'FORM SEARCH',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($this){
						var kb_btnsearch = null;
						$('#btnsearch').click(function(){ fnResultCUSCOD(); });
						function fnResultCUSCOD(){
							dataToPost = new Object();
							dataToPost.cuscod = $('#cuscod').val();
							dataToPost.name1  = $('#name1').val();
							dataToPost.name2  = $('#name2').val();
							$('#loadding').fadeIn(200);
							kb_btnsearch = $.ajax({
								url:'../Cselect2K/getResultCUSTMAST',
								data:dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#cus_result').html(data.html);
									$('.getit').hover(function(){
										$(this).css({'background-color':'#a9a9f9'});
										$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
									},function(){
										$(this).css({'background-color':''});
										$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
									});
									$('.getit').unbind('click');
									$('.getit').click(function(){
										cln = new Object();
										cln.cuscod  = $(this).attr('CUSCOD');
										cln.cusname = $(this).attr('CUSNAME');
										$('#add_cuscod').val(cln.cuscod);
										$('#cusname').val(cln.cusname);
										$this.destroy();
									});
									$('#loadding').fadeOut(200);
									kb_btnsearch = null;
								},
								beforeSend: function(){
									if(kb_btnsearch !== null){ kb_btnsearch.abort(); }
								}
							});
						}
					},
					beforeClose : function(){
						
					}
				});
				$('#loadding').fadeOut(200);
			}
		});
	});
});
$('#btnreportCC').click(function(){
	printReport();
});
function printReport(){
	var order = "";
	if($('#OR1').is(":checked")){
		order = "TMBILL";
	}else if($('#OR2').is(":checked")){
		order = "CONTNO";
	}else if($('#OR3').is(":checked")){
		order = "TMBILDT"; 
	}else if($('#OR4').is(":checked")){
		order = "CHQDT"; 
	}
	dataToPost = new Object();
	dataToPost.CUSCOD = $('#add_cuscod').val();
	dataToPost.CUSNAME= $('#cusname').val();
	dataToPost.order  = order;
	if(dataToPost.CUSCOD == ""){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
			soundExt: '.ogg',
			icon: true,
			messageHeight: '90vh',
			msg: 'กรุณาระบุรหัสลูกค้าก่อนครับ'
		});
	}else{
		$('#loadding').show();
		$.ajax({
			url: '../SYS06/ReportReceivedCC/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedCC/pdf?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				$('#loadding').hide();
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
}