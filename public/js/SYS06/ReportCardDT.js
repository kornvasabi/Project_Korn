/********************************************************
             ______@03/02/2020______
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
						$('#loadding').fadeOut(200);
						var kb_cont_search = null;
						$('#cont_search').click(function(){ fnResultCONTNO(); });
						function fnResultCONTNO(){
							dataToPost = new Object();
							dataToPost.s_contno = $('#s_contno').val();
							dataToPost.s_name1  = $('#s_name1').val();
							dataToPost.s_name2  = $('#s_name2').val();
							$('#loadding').fadeIn(200);
							kb_cont_search = $.ajax({
								url:'../Cselect2K/getResultCONTNO',
								data:dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#loadding').fadeOut(200);
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
										$('#CONTNO').val(cln.contno);
										$('#LOCAT').val(cln.locat);
										$('#CUSNAME').val(cln.cusname);
										
										$this.destroy();
									});
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
			}
		});
	});
});
$('#btnreportCardDT').click(function(){
	printReport();
});
var DT_Report = null;
function printReport(){
	var show1 = null;
	if($('#SYD').is(":checked")){
		show1 = "SYD";
	}else if($('#STR').is(":checked")){
		show1 = "STR";
	}else if($('#EFF').is(":checked")){
		show1 = "EFF";
	}
	
	var show2 = null;
	if($('#YSH').is(":checked")){
		show2 = "Y";
	}else if($('#OR2').is(":checked")){
		show2 = "N";
	}
	dataToPost = new Object();
	dataToPost.CONTNO    = $('#CONTNO').val();
	dataToPost.LOCAT	 = $('#LOCAT').val();
	dataToPost.show1	 = show1;
	dataToPost.show2     = show2;
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
		DT_Report = $.ajax({
			url:'../SYS06/ReportCardDT/conditiontopdf',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'/SYS06/ReportCardDT/pdf?condpdf='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				Lobibox.window({
					title:'พิมพ์รายงาน',
					content:content,
					closeOnEsc:false,
					height:$(window).height(),
					width:$(window).width()
				});
				DT_Report = null;
			},
			beforeSend:function(){
				if(DT_Report !== null){DT_Report.abort();}
			}
		});
	}
}