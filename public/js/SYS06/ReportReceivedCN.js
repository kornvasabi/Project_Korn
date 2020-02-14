/********************************************************
             ______@11/01/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#btnaddcont').click(function(){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../Cselect2K/getfromCONTNO',
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
						var kb_cont_search = null;
						$('#cont_search').click(function(){ fnResultCONTNO(); });
						function fnResultCONTNO(){
							var price = "";
							if($('#P1').is(":checked")){
								price = "P1";
							}else if($('#P2').is(":checked")){
								price = "P2";
							}else if($('#P3').is(":checked")){
								price = "P3";
							}else if($('#P4').is(":checked")){
								price = "P4";
							}else if($('#P5').is(":checked")){
								price = "P5";
							}else if($('#P6').is(":checked")){
								price = "P6";
							}
							dataToPost = new Object();
							dataToPost.s_contno = $('#s_contno').val();
							dataToPost.s_name1  = $('#s_name1').val();
							dataToPost.s_name2  = $('#s_name2').val();
							dataToPost.price    = price;
							
							$('#loadding').fadeIn(200);
							kb_cont_search = $.ajax({
								url:'../Cselect2K/getResultCONTNO_R',
								data:dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#cont_result').html(data.html);
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
										cln.contno  = $(this).attr('CONTNO');
										cln.locat   = $(this).attr('LOCAT');
										cln.cusname = $(this).attr('CUSNAME');
										$('#add_contno').val(cln.contno);
										$('#locat').val(cln.locat);
										$('#cusname').val(cln.cusname);
										$this.destroy();
									});
									$('#loadding').fadeOut(200);
									kb_cont_search = null;
								},
								beforeSend: function(){
									if(kb_cont_search !== null){ kb_cont_search.abort(); }
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
	$('#P1').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
	$('#P2').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
	$('#P3').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
	$('#P4').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
	$('#P5').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
	$('#P6').click(function(){
		$('#add_contno').val('');
		$('#locat').val('');
		$('#cusname').val('');
	});
});
$('#btnreportCN').click(function(){
	printReport();
});
CN_Report = null;
function printReport(){
	var order = "";
	if($('#OR1').is(":checked")){
		order = "TMBILL";
	}else if($('#OR2').is(":checked")){
		order = "TMBILDT";
	}else if($('#OR3').is(":checked")){
		order = "CHQNO";
	}else if($('#OR4').is(":checked")){
		order = "CHQDT";
	}
	var price = "";
	if($('#P1').is(":checked")){
		price = "P1";
	}else if($('#P2').is(":checked")){
		price = "P2";
	}else if($('#P3').is(":checked")){
		price = "P3";
	}else if($('#P4').is(":checked")){
		price = "P4";
	}else if($('#P5').is(":checked")){
		price = "P5";
	}else if($('#P6').is(":checked")){
		price = "P6";
	}
	dataToPost = new Object();
	dataToPost.CONTNO = $('#add_contno').val();
	dataToPost.LOCAT  = $('#locat').val();
	dataToPost.CUSNAME= $('#cusname').val();
	dataToPost.order  = order;
	dataToPost.price  = price;
	if(dataToPost.CONTNO == ""){
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
			msg: 'กรุณาระบุเลขที่สัญญาก่อนครับ'
		});
	}else{
		$('#loadding').show();
		CN_Report = $.ajax({
			url: '../SYS06/ReportReceivedCN/conditiontopdf',
			data: dataToPost,
			type:'POST',
			dataType: 'json',
			success: function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportReceivedCN/pdf?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				$('#loadding').hide();
				Lobibox.window({
					title: 'พิมพ์รายงาน',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
				CN_Report = null;
			},
			beforeSend:function(){
				if(CN_Report !== null){CN_Report.abort();}
			}
		});
	}
}