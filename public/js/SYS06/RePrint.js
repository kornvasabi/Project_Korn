/********************************************************
             ______@25.07.2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
"use strict";
var _groupType = $('.tab1[name="home"]').attr('groupType');
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
var _dbgroup = $('.tab1[name="home"]').attr('dbgroup'); // ฐานหลัก

$(function(){
	$("#sch_locatrecv").selectpicker();
	if(_groupType != "OFF"){
		$("#sch_locatrecv").empty().append('<option value="'+_locat+'" selected>'+_locat+'</option>');
		$("#sch_locatrecv").attr('disabled',true).selectpicker('refresh');
	}else{
		//$("#LOCAT").empty();
		$("#sch_locatrecv").attr('disabled',false).selectpicker('refresh');
	}
});



var OBJbtnt1search = null;
$('#btnt1search').click(function(){
	var dataToPost = new Object();
	dataToPost.TMBILL = $('#sch_tmbill').val();
	dataToPost.BILLNO = $('#sch_billno').val();
	dataToPost.CONTNO = $('#sch_contno').val();
	dataToPost.LOCAT 	= $('#sch_locatrecv').val();
	dataToPost.STMBILDT = $('#sch_stmbildt').val();
	dataToPost.ETMBILDT = $('#sch_etmbildt').val();
	
	$('#loadding').fadeIn(200);
	OBJbtnt1search = $.ajax({
		url:'../SYS06/RePrint/Search',
		data: dataToPost,
		type:'POST',
		dataType:'json',
		beforeSend: function(){ if(OBJbtnt1search !== null){ OBJbtnt1search.abort(); } },
		success: function(data){
			Lobibox.window({
				title: 'รายการอนุมัติพิมพ์บิลซ้ำ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($thisSearch){
					
					$('#loadding').fadeOut(200);
				}
			});
			OBJbtnt1search = null;
		}
	});
});



var OBJbtnt1newallow = null;
$('#btnt1newallow').click(function(){
	var dataToPost = new Object();
	
	$('#loadding').fadeIn(200);
	OBJbtnt1newallow = $.ajax({
		url:'../SYS06/RePrint/getFormAllow',
		data: dataToPost,
		type:'POST',
		dataType:'json',
		beforeSend: function(){ if(OBJbtnt1newallow !== null){ OBJbtnt1newallow.abort(); } },
		success: function(data){
			Lobibox.window({
				title: 'อนุมัติพิมพ์บิลซ้ำ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($thisFAP){
					fn_als($thisFAP);
					$('#loadding').fadeOut(200);
				}
			});
			OBJbtnt1newallow = null;
		}
	});
});

function fn_als($thisFAP){
	$('#als_locat').selectpicker();
	$('#alf_locat').selectpicker();
	
	var OBJals_search=null;
	$("#als_search").click(function(){
		var dataToPost = new Object();
		dataToPost.tmbill 	= $('#als_tmbill').val();
		dataToPost.billno 	= $('#als_billno').val();
		dataToPost.locat 	= $('#als_locat').val();
		dataToPost.stmbildt = $('#als_stmbildt').val();
		dataToPost.etmbildt = $('#als_etmbildt').val();
		
		$('#loadding').fadeIn(200);
		OBJals_search = $.ajax({
			url:'../SYS06/RePrint/getALSSearch',
			data: dataToPost,
			type:'POST',
			dataType:'json',
			beforeSend: function(){ if(OBJals_search !== null){ OBJals_search.abort(); } },
			success: function(data){
				if(data.error){
					Lobibox.notify('info', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: false,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: 'รับชำระต่างสาขา เปลี่ยนวิธีชำระเงินเป็น 14 รับฝากผ่อนแล้ว'
					});
				}else{
					var r = data.html.length;
					var html = "";
					for(var i=0;i<r;i++){
						html += "<tr style='"+(data.html[i]["FLAG"] == "C" ?"color:red;":"")+"'>";
						html += "	<td><input class='billchoose' type='button' class='btn' value='เลือก' TMBILL='"+data.html[i]["TMBILL"]+"' BILLNO='"+data.html[i]["BILLNO"]+"' LOCATRECV='"+data.html[i]["LOCATRECV"]+"'></td>";
						html += "	<td>"+data.html[i]["TMBILL"]+"</td>";
						html += "	<td>"+data.html[i]["TMBILDT"]+"</td>";
						html += "	<td>"+data.html[i]["BILLNO"]+"</td>";
						html += "	<td>"+data.html[i]["LOCATRECV"]+"</td>";
						html += "	<td>"+data.html[i]["CUSCOD"]+"</td>";
						html += "	<td>"+data.html[i]["CUSNAME"]+"</td>";
						html += "	<td align='right'>"+data.html[i]["CHQAMT"]+"</td>";
						html += "	<td align='right'>"+data.html[i]["NOPRNTB"]+"</td>";
						html += "	<td align='right'>"+data.html[i]["NOPRNBL"]+"</td>";
						html += "</tr>";
					}
					
					var htmlHead = "";
					htmlHead += "<tr>";
					htmlHead += "	<th>#</th>";
					htmlHead += "	<th>ใบรับชั่วคราว</th>";
					htmlHead += "	<th>วันที่รับ</th>";
					htmlHead += "	<th>ใบเสร็จรับเงิน</th>";
					htmlHead += "	<th>สาขา</th>";
					htmlHead += "	<th>รหัสลูกค้า</th>";
					htmlHead += "	<th>ชื่อ-สกุล</th>";
					htmlHead += "	<th>จำนวน</th>";
					htmlHead += "	<th>จำนวนพิมพ์บิลรับ</th>";
					htmlHead += "	<th>จำนวนพิมพ์ใบเสร็จ</th>";
					htmlHead += "</tr>";
					
					html = "<table class='table table-bordered'>"+htmlHead+html+"</table>";
					$('#als_results').html(html);
					
					$('.billchoose').hover(function(){
						$(this).parent().parent().css({
							'cursor':'pointer'
							,'background-color':'yellow'
						});
					},function(){
						$(this).parent().parent().css({
							'cursor':'pointer'
							,'background-color':'#fff'
						});
					});
					
					var OBJbillchoose=null;
					$('.billchoose').click(function(){
						var $billchoose = $(this);
						var dataToPost = new Object();
						dataToPost.TMBILL 	 = $(this).attr('TMBILL');
						dataToPost.BILLNO 	 = $(this).attr('BILLNO');
						dataToPost.LOCATRECV = $(this).attr('LOCATRECV');
						
						$('#loadding').fadeIn(200);
						OBJbillchoose = $.ajax({
							url:'../SYS06/RePrint/getBillLOG',
							data: dataToPost,
							type:'POST',
							dataType:'json',
							beforeSend: function(){ if(OBJbillchoose !== null){ OBJbillchoose.abort(); } },
							success: function(data){
								OBJbillchoose = null;
								
								var r = data.html.length;
								var html = "";
								for(var i=0;i<r;i++){
									html += "<tr>";
									html += "	<!-- td><input class='billchoose' type='button' class='btn' value='เลือก' TMBILL='"+data.html[i]["TMBILL"]+"' BILLNO='"+data.html[i]["BILLNO"]+"' LOCATRECV='"+data.html[i]["LOCATRECV"]+"'></td -->";
									html += "	<td>"+data.html[i]["ID"]+"</td>";
									html += "	<td>"+data.html[i]["TMBILL"]+"</td>";
									html += "	<td>"+data.html[i]["BILLNO"]+"</td>";
									html += "	<td>"+data.html[i]["LOCATRECV"]+"</td>";
									html += "	<td>"+data.html[i]["TOPICName"]+"</td>";
									html += "	<td>"+data.html[i]["MEMO1"]+"</td>";
									html += "	<td align='center'>"+data.html[i]["ALTMB"]+"</td>";
									html += "	<td align='center'>"+data.html[i]["ALBIL"]+"</td>";
									html += "	<td>"+data.html[i]["INSBY"]+"</td>";
									html += "	<td>"+data.html[i]["INSDT"]+"</td>";
									html += "</tr>";
								}
								
								var htmlHead = "";
								htmlHead += "<tr>";
								htmlHead += "	<!-- th>#</th -->";
								htmlHead += "	<th>ลำดับ</th>";
								htmlHead += "	<th>ใบรับชั่วคราว</th>";
								htmlHead += "	<th>ใบเสร็จรับเงิน</th>";
								htmlHead += "	<th>สาขา</th>";
								htmlHead += "	<th>สาเหตุ</th>";
								htmlHead += "	<th>หมายเหตุ</th>";
								htmlHead += "	<th>อนุมัติพิมพ์<br>ใบรับชั่วคราว</th>";
								htmlHead += "	<th>อนุมัติพิมพ์<br>ใบเสร็จรับเงิน</th>";
								htmlHead += "	<th>ผู้ทำรายการ</th>";
								htmlHead += "	<th>วันที่ทำรายการ</th>";
								htmlHead += "</tr>";
								
								if(html == ""){
									html += "<tr><td class='text-red' colspan='10' align='center'>ไม่มีประวัติการอนุมัติพิมพ์ซ้ำ</td></tr>";
								}
								html = "<table class='table table-bordered'>"+htmlHead+html+"</table>";
								$('#allog_results').html(html);
								
								$('#alf_tmbill').val($billchoose.attr('TMBILL'));
								$('#alf_billno').val($billchoose.attr('BILLNO'));
								$('#alf_locat').val($billchoose.attr('LOCATRECV')).trigger('change');
								
								var OBJalf_save=null;
								$('#alf_save').unbind('click');
								$('#alf_save').click(function(){
									Lobibox.confirm({
										title: 'ยืนยันการทำรายการ',
										draggable: true,
										iconClass: false,
										closeOnEsc: false,
										closeButton: false,
										msg: 'ยืนยันการทำรายการ ?',
										buttons: {
											ok : {
												'class': 'btn btn-danger glyphicon glyphicon-ok',
												text: ' ยืนยัน ให้สิทธิ์พิมพ์ซ้ำ',
												closeOnClick: false,
											},
											cancel : {
												'class': 'btn btn-default glyphicon glyphicon-remove',
												text: ' ไว้ทีหลัง',
												closeOnClick: true
											},
										},
										onShow: function(lobibox){ $('body').append(jbackdrop); },
										shown: function($this){ 
										},
										callback: function(lobibox, type){
											var $lobiboxConfirm = lobibox;
											
											if (type === 'ok'){
												var dataToPost = new Object();
												dataToPost.tmbill  = $('#alf_tmbill').val();
												dataToPost.billno  = $('#alf_billno').val();
												dataToPost.locat   = $('#alf_locat').val();
												dataToPost.topic   = $('#alf_topic').val();
												dataToPost.memo1   = $('#alf_memo1').val();
												dataToPost.reprint = $('input[name=alf_reprint]:checked').val();
												
												$('#loadding').fadeIn(200);
												OBJalf_save = $.ajax({
													url:'../SYS06/RePrint/Save',
													data: dataToPost,
													type:'POST',
													dataType:'json',
													beforeSend: function(){ if(OBJalf_save !== null){ OBJalf_save.abort(); } },
													success: function(data){
														if(data.error){
															Lobibox.notify('warning', {
																title: 'แจ้งเตือน',
																size: 'mini',
																closeOnClick: false,
																delay: false,
																pauseDelayOnHover: true,
																continueDelayOnInactiveTab: false,
																icon: true,
																messageHeight: '90vh',
																msg: data.errorMessage
															});
														}else{
															$('#alf_topic').val("nouse");	
															$('#alf_memo1').val("");
															
															Lobibox.notify('success', {
																title: 'แจ้งเตือน',
																size: 'mini',
																closeOnClick: false,
																delay: false,
																pauseDelayOnHover: true,
																continueDelayOnInactiveTab: false,
																icon: true,
																messageHeight: '90vh',
																msg: data.errorMessage
															});
														}
														
														OBJalf_save = null;
														$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
														$lobiboxConfirm.destroy();
														$('#loadding').fadeOut(200);
														
													}
												});
											}else{
												$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
											}
										}
									});
								});
								
								$('#search_tabs a[href="#app_bill_menu2"]').tab('show');
								$('#loadding').fadeOut(200);
							}
						});
						
					});
				}
				
				$('#loadding').fadeOut(200);
				OBJals_search = null;
			}
		});
	});
}
















