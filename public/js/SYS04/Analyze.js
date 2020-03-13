/********************************************************
             ______@27/08/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
"use strict";
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
var _ismobile  = $('.tab1[name="home"]').attr('is_mobile');
var JDbtnt1search = null;
var JDselectPickers_Cache = null;

$(function(){
	$("#SANSTAT").select2({minimumResultsForSearch: -1,width: '100%'});
	//fn_search(JDbtnt1search);
	var divcondition = $(".divcondition").height() ;
	$("#result").css({
		//'height':'calc(100vh - '+divcondition+'px)',		
		'width':'100%'
	});
	
	$("#LOCAT").selectpicker();
})

$('#LOCAT').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) { 
	var filter = $("#LOCAT").parent().find("[aria-label=Search]");
	FN_JD_BSSELECT("LOCAT",filter,"getLOCAT2");
});

$("#LOCAT").parent().find("[aria-label=Search]").keyup(function(){ 
	FN_JD_BSSELECT("LOCAT",$(this),"getLOCAT2");
});

function FN_JD_BSSELECT($id,$thisSelected,$func){
	var dataToPost = new Object();
	dataToPost.filter = $thisSelected.val();
	dataToPost.now	  = (typeof $("#"+$id).selectpicker('val') == null ? "":$("#"+$id).selectpicker('val'));
	
	clearTimeout(JDselectPickers);
	var JDselectPickers = setTimeout(function(){ getdata(); },250);
	
	function getdata(){
		//$("#"+$id+" UI.dropdown-menu").html("loadding...");
		JDselectPickers_Cache = $.ajax({
			url: '../SYS04/Standard/'+$func,
			data: dataToPost,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#"+$id).empty().append(data.opt);
				$("#"+$id).selectpicker('refresh');
				
				JDselectPickers_Cache= null;
			},
			beforeSend: function(){ if(JDselectPickers_Cache !== null){ JDselectPickers_Cache.abort(); } },
			//error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
}

$("#btnt1search").click(function(){
	fn_search(JDbtnt1search); 
});

function fn_search(JDbtnt1search){	
	var dataToPost = new Object();
	dataToPost.SSTRNO 		= $("#SSTRNO").val();
	dataToPost.SMODEL 		= $("#SMODEL").val();
	dataToPost.SCREATEDATEF = $("#SCREATEDATEF").val();
	dataToPost.SCREATEDATET = $("#SCREATEDATET").val();
	dataToPost.SAPPROVEF 	= $("#SAPPROVEF").val();
	dataToPost.SAPPROVET 	= $("#SAPPROVET").val();
	dataToPost.SRESVNO 		= $("#SRESVNO").val();
	dataToPost.SCUSNAME 	= $("#SCUSNAME").val();
	dataToPost.SANSTAT 		= (typeof $("#SANSTAT").find(":selected").val() === 'undefined' ? "":$("#SANSTAT").find(":selected").val());
	dataToPost.LOCAT	 	= $("#LOCAT").val();
	
	
	$('#loadding').fadeIn(500);
	JDbtnt1search = $.ajax({
		url:'../SYS04/Analyze/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			$("#result").html(data.html);
			$("#table-fixed-Analyze").show(0);
			$("#table-fixed-Analyze-detail").hide(0);
			
			if(_ismobile == "yes"){
				fn_datatables('table-Analyze',1,0);
			}else{
				$('[data-toggle="tooltip"]').tooltip();
				$('#table-Analyze').on('draw.dt',function(){ redraw(); });
				fn_datatables('table-Analyze',1,235);
			}
			
			// Export data to Excel
			// $('.data-export').prepend('<img id="table-Analyze-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			// $("#table-Analyze-excel").click(function(){ 	
				// tableToExcel_Export(data.html,"ใบวิเคราะห์","table-Analyze"); 
			// });
			
			function redraw(){
				var JDansend = null;
				$(".ansend").unbind('click');
				$(".ansend").click(function(){
					var dataToPost = new Object();
					dataToPost.ANID = $(this).attr('ANID');
								
					Lobibox.confirm({
						title: 'ยืนยันการทำรายการ',
						draggable: true,
						iconClass: false,
						closeOnEsc: false,
						closeButton: false,
						msg: 'ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อเลขที่ '+dataToPost.ANID,
						buttons: {
							ok : {
								'class': 'btn btn-primary glyphicon glyphicon-ok',
								text: ' ยืนยันการทำรายการ',
								closeOnClick: false,
							},
							cancel : {
								'class': 'btn btn-danger glyphicon glyphicon-remove',
								text: ' ยกเลิก',
								closeOnClick: true
							},
						},
						shown: function($this){},
						callback: function(lobibox, type){
							if (type === 'ok'){
								$('#loadding').fadeIn(500);
								JDansend = $.ajax({
									url:'../SYS04/Analyze/Send_Analyze',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data){
										
										if(data.error){
											Lobibox.notify('warning', {
												title: 'แจ้งเตือน',
												size: 'mini',
												closeOnClick: false,
												delay: 5000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: true,
												messageHeight: '90vh',
												msg: data["msg"][0]
											});
										}else{
											Lobibox.notify('success', {
												title: 'แจ้งเตือน',
												size: 'mini',
												closeOnClick: false,
												delay: 5000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: true,
												messageHeight: '90vh',
												msg: data["msg"][0]
											});
											
											fn_search(JDbtnt1search);
										}
										
										JDansend = null;
										$('#loadding').fadeOut(500);
										lobibox.destroy();
									},
									beforeSend:function(){ if(JDansend !== null){ JDansend.abort(); } }
								});
							}
						}
					});	
				});
				
				var JDandetail_edit = null;
				$(".andetail_edit").unbind('click');
				$(".andetail_edit").click(function(){
					var dataToPost = new Object();
					dataToPost.ANID = $(this).attr('ANID');
					
					$('#loadding').fadeIn(200);
					JDandetail_edit = $.ajax({
						url:'../SYS04/Analyze/Edit_Analyze',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							if(data.error){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
								
								$('#loadding').fadeOut(200);
							}else{
								fn_loadFormAnalyze(data["data"]);
								fn_search(JDbtnt1search);
							}
							JDandetail_edit = null;
						},
						beforeSend:function(){ if(JDandetail_edit !== null){ JDandetail_edit.abort(); } }
					});
				});
				
				var JDandetail = null;
				$(".andetail").unbind('click');
				$(".andetail").click(function(){
					var dataToPost = new Object();
					dataToPost.ANID = $(this).attr('ANID');
					
					$('#loadding').fadeIn(500);
					JDandetail = $.ajax({
						url:'../SYS04/Analyze/searchDetail',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(200);
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
									msg: data.html
								});
							}else{
								var WINDOW_DETAILS = null;
								Lobibox.window({
									title: 'รายการวิเคราะห์สินเชื่อ',
									width: $(window).width(),
									height: $(window).height(),
									content: data.html,
									draggable: false,
									closeOnEsc: false,
									closeButton: false,
									shown: function($this){
										WINDOW_DETAILS = $this;
										$("#back").click(function(){
											$.ajax({
												url:'../SYS04/Analyze/changeANSTAT',
												data: dataToPost,
												type: 'POST',
												dataType: 'json',
												success: function(data){
													// update status PP to P
												}
											 });
											 
											WINDOW_DETAILS.destroy();
										});
									}
								});
							}
							
							// ซ่อนปุ่มอนุมัติ
							$("#approve").attr('disabled',(_update == "T" ? false:true));
							if(_locat != $('#locat').find(':selected').val() && _level != 1){ $("#approve").attr('disabled',true); }
							
							$('.cushistory').click(function(){
								dataToPost.cuscod = $(this).attr('cuscod');
								
								$('#loadding').fadeIn(200);
								$.ajax({
									url:'../SYS04/Analyze/getCusHistory',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									beforeSend: function(){
										$("#approve").attr("disabled",true);
										$("#back").attr("disabled",true);
									},
									success: function(data){
										Lobibox.window({
											title: 'ประวัติ',
											width: 700,
											//height: $(window).height(),
											content: data.html,
											draggable: true,
											closeOnEsc: true,
											shown:function(){
												var transaction = data.tableName;
												var insurance = 'ins_'+data.tableName;
												
												fn_datatables(transaction,1,100,'YES');
												setTimeout(function(){ fn_datatables(insurance,1,100,'YES'); },250);
											},
											beforeClose:function(){
												$("#approve").attr("disabled",false);
												$("#back").attr("disabled",false);
											}
										});
										
										$('#loadding').fadeOut(200);
									}
								});
							});
							
							var JDapprove = null;
							$("#approve").click(function(){
								$.ajax({
									url:' ../SYS04/Analyze/formApproved',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									beforeSend: function(){
										$("#approve").attr("disabled",true);
										$("#back").attr("disabled",true);
									},
									success: function(data){
										Lobibox.confirm({
											title: 'ยืนยันการทำรายการ',
											draggable: true,
											iconClass: false,
											closeOnEsc: false,
											closeButton: false,
											msg: data.html,
											buttons: {
												ok : {
													'class': 'btn btn-primary glyphicon glyphicon-ok',
													text: ' ยืนยันการทำรายการ',
													closeOnClick: false,
												},
												cancel : {
													'class': 'btn btn-danger glyphicon glyphicon-remove',
													text: ' ยกเลิก',
													closeOnClick: true
												},
											},
											shown: function($this){
												$(this).css({
													'background': 'rgba(0, 0, 0, 0) url("../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png") repeat scroll 0% 0%'
												});
												
												$("#APPTYPE").select2({ 
													placeholder: 'เลือก',
													width:'100%',
													dropdownParent: $('#APPTYPE').parent().parent(),
													minimumResultsForSearch: -1
												});
												
												$("#APPTYPE").on("select2:select",function(){
													if($(this).find(":selected").val() == "A"){
														$("#APPTYPE ,#APPCOMMENT").css({ 'color':'green' });
													}else{
														$("#APPTYPE ,#APPCOMMENT").css({ 'color':'red' });
													}
												});	
												
												$("#APPOPTMAST").select2({ 
													placeholder: 'เลือก',
													width:'100%',
													dropdownParent: $('#APPOPTMAST').parent().parent(),
													minimumResultsForSearch: -1
												});
											},
											callback: function(lobibox, type){
												var confirm_lobibox = lobibox;
												if (type === 'ok'){
													dataToPost.apptype 	= $("#APPTYPE").find(":selected").val();
													dataToPost.comment 	= $("#APPCOMMENT").val();
													dataToPost.optmast 	= $("#APPOPTMAST").find(":selected").val();
													dataToPost.dwn 		= $("#APPDWN").val();
													dataToPost.nopay 	= $("#APPNOPAY").val();
													dataToPost.inrt 	= $("#APPInRT").val();
													
													$('#loadding').fadeIn(500);
													JDapprove = $.ajax({
														url:'../SYS04/Analyze/approved',
														data: dataToPost,
														type: 'POST',
														dataType: 'json',
														success: function(data){
															JDapprove = null;
															
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
																	msg: data.msg
																});
																$("#approve").attr("disabled",false);
																$("#back").attr("disabled",false);
															}else{
																Lobibox.notify('success', {
																	title: 'แจ้งเตือน',
																	size: 'mini',
																	closeOnClick: false,
																	delay: 5000,
																	pauseDelayOnHover: true,
																	continueDelayOnInactiveTab: false,
																	icon: true,
																	messageHeight: '90vh',
																	msg: data.msg
																});
																
																WINDOW_DETAILS.destroy();
																fn_search(JDbtnt1search);
																confirm_lobibox.destroy();
															}
															
															$('#loadding').fadeOut(200);
														},
														beforeSend: function(){
															if(JDapprove !== null){ JDapprove.abort(); }
														},
														error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
													});
												}else{
													$("#approve").attr("disabled",false);
													$("#back").attr("disabled",false);
												}
											}
										});
									},
									error: function(jqXHR, exception){ 
										$("#approve").attr("disabled",false);
										$("#back").attr("disabled",false);
										fnAjaxERROR(jqXHR,exception); 
									}
								});
							});
							
							JDandetail = null;
						},
						beforeSend: function(){
							if(JDandetail !== null){ JDandetail.abort(); }
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				});
			}
			
			JDbtnt1search = null;
		},
		beforeSend: function(){ if(JDbtnt1search !== null){ JDbtnt1search.abort(); } }
	});
}

$("#btnt1createappr").attr('disabled',(_insert == "T" ? false:true));
var JDbtnt1createappr = null;
$("#btnt1createappr").click(function(){ fn_loadFormAnalyze(); });

function fn_loadFormAnalyze($_data){
	$('#loadding').fadeIn(200);
	JDbtnt1createappr = $.ajax({
		url:'../SYS04/Analyze/loadform',
		//data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'รายการขออนุมัติสินเชื่อบุคคลเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					var set_stat = [{id:1,text:'โสด'},{id:2,text:'สมรส'},{id:3,text:'หม้าย'},{id:4,text:'หย่า'},{id:5,text:'แยกกันอยู่'}];
					$('#idnoStat').select2({ data:set_stat,placeholder: 'เลือก',dropdownParent: $('#idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
					$('#idnoStat').val(null).trigger('change');
					$('#is1_idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#is1_idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
					$('#is1_idnoStat').val(null).trigger('change');
					$('#is2_idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#is2_idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
					$('#is2_idnoStat').val(null).trigger('change');
					$('#is3_idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#is2_idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
					$('#is3_idnoStat').val(null).trigger('change');
					fnload($this);
					
					if (typeof $_data !== 'undefined') {
						$('#anid').val($_data['ID']);
						var newOption = new Option($_data["LOCAT"], $_data["LOCAT"], true, true);
						$('#locat').empty().append(newOption).attr('disabled',true).trigger('change');
						var newOption = new Option($_data["ACTIDES"], $_data["ACTICOD"], true, true);
						$('#acticod').empty().append(newOption).trigger('change');
						$('#createDate').val($_data['CREATEDATE']);
						$('#dwnAmt').val($_data['DWN']);
						$('input:radio[name=insuranceType]').each(function(){
							if($(this).val() == $_data['INSURANCE_TYP']){ $(this).prop("checked", true); }
						});
						$('#insuranceAmt').val($_data['DWN_INSURANCE']);
						$('#nopay').val($_data['NOPAY']);
						
						if($_data["RESVNO"] != null){
							var newOption = new Option($_data["RESVNO"], $_data["RESVNO"], true, true);
							$('#resvno').empty().append(newOption);
							$('#strno').attr('disabled',true);
							$('#cuscod').attr('disabled',true);
							$('#cuscod_removed').attr('disabled',(_level == 1 ? false:true));
							$('#acticod').attr('disabled',true).trigger('change');
						}
						$('#resvno').attr('disabled',true).trigger('change');
						$('#resvAmt').val($_data['RESVAMT']);
						var newOption = new Option($_data["STRNO"], $_data["STRNO"], true, true);
						$('#strno').empty().append(newOption).attr('disabled',true).trigger('change');
						var newOption = new Option($_data["MODEL"], $_data["MODEL"], true, true);
						$('#model').empty().append(newOption).attr('disabled',true).trigger('change');
						var newOption = new Option($_data["BAAB"], $_data["BAAB"], true, true);
						$('#baab').empty().append(newOption).attr('disabled',true).trigger('change');
						var newOption = new Option($_data["COLOR"], $_data["COLOR"], true, true);
						$('#color').empty().append(newOption).attr('disabled',true).trigger('change');
						$('#stat').val($_data['STAT']);
						$('#sdateold').val($_data['SDATE']);
						$('#ydate').val($_data['YDATE']);
						$('#price_add').val($_data['PRICE_ADD']).attr('disabled',true);
						$('#price').val($_data['PRICE']).attr('disabled',true);
						$('#price').attr('stdid',$_data['STDID']);
						$('#price').attr('subid',$_data['SUBID']);
						$('#price').attr('shcid',$_data['SHCID']);
						$('#interatert').val($_data['INTEREST_RT']).attr('disabled',true);
						
						for(var i=0;i<4;i++){
							if (typeof $_data['REF'+i+''] !== 'undefined') {
								var tags = "";
								if($_data['REF'+i+'']["CUSTYPE"] == 1){
									tags = "is1_";
								}else if($_data['REF'+i+'']["CUSTYPE"] == 2){
									tags = "is2_";
								}else if($_data['REF'+i+'']["CUSTYPE"] == 3){
									tags = "is3_";
								}
								$('#'+tags+'cuscod').val($_data['REF'+i+'']["CUSNAME"]);
								$('#'+tags+'cuscod').attr('disabled',true);
								$('#'+tags+'cuscod').attr('cuscod',$_data['REF'+i+'']["CUSCOD"]);
								$('#'+tags+'idno').val($_data['REF'+i+'']["IDNO"]).attr('disabled',true);
								$('#'+tags+'idnoBirth').val($_data['REF'+i+'']["BIRTHDT"]).attr('disabled',true);
								$('#'+tags+'idnoExpire').val($_data['REF'+i+'']["EXPDT"]).attr('disabled',true);
								$('#'+tags+'idnoAge').val($_data['REF'+i+'']["CUSAGE"]).attr('disabled',true);
								$('#'+tags+'idnoStat').val($_data['REF'+i+'']["CUSSTAT"]).trigger('change');
								var newOption = new Option($_data['REF'+i+'']["ADDRNO_Detail"], $_data['REF'+i+'']["ADDRNO"], true, true);
								$('#'+tags+'addr1').empty().append(newOption).trigger('change');
								var newOption = new Option($_data['REF'+i+'']["ADDRDOCNO_Detail"], $_data['REF'+i+'']["ADDRDOCNO"], true, true);
								$('#'+tags+'addr2').empty().append(newOption).trigger('change');
								$('#'+tags+'phoneNumber').val($_data['REF'+i+'']["MOBILENO"]);
								$('#'+tags+'baby').val($_data['REF'+i+'']["CUSBABY"]);
								$('#'+tags+'socialSecurity').val($_data['REF'+i+'']["SOCAILSECURITY"]);
								
								$('#'+tags+'career').val($_data['REF'+i+'']["CAREER"]);
								$('#'+tags+'careerOffice').val($_data['REF'+i+'']["CAREERADDR"]);
								$('#'+tags+'careerPhone').val($_data['REF'+i+'']["CAREERTEL"]);
								$('#'+tags+'income').val($_data['REF'+i+'']["MREVENU"]);
								$('#'+tags+'hostName').val($_data['REF'+i+'']["HOSTNAME"]);
								$('#'+tags+'hostIDNo').val($_data['REF'+i+'']["HOSTIDNO"]);
								$('#'+tags+'hostPhone').val($_data['REF'+i+'']["HOSTTEL"]);
								$('#'+tags+'hostRelation').val($_data['REF'+i+'']["HOSTRELATION"]);
								//EMPRELATION
								$('#'+tags+'reference').val($_data['REF'+i+'']["REFERANT"]);
								
								var widpic = ($('#'+tags+'picture').width());						
								var picture_msg = $_data['REF'+i+'']["filePath"];
								if($_data['REF'+i+'']["filePath"] != "(none)"){
									picture_msg = '<image style="width:'+widpic+'px;height:auto;" src="'+$_data['REF'+i+'']["filePath"]+'"/>';
								}
								$('#'+tags+'picture').val("");
								$('#'+tags+'picture').attr('source','');
								$('#'+tags+'picture').attr('data-original-title',picture_msg);
							}
						}
						
						var newOption = new Option($_data["EMPNAME"], $_data["EMP"], true, true);
						$('#empIDNo').empty().append(newOption).trigger('change');
						$('#empTel').val($_data["EMPTEL"]);
						var newOption = new Option($_data["MNGNAME"], $_data["MNG"], true, true);
						$('#mngIDNo').empty().append(newOption).trigger('change');						
						$('#mngTel').val($_data["MNGTEL"]);
						
						var widpic = ($('#analyze_picture').width());						
						var picture_msg = $_data['EVIDENCE'];
						if($_data['EVIDENCE'] != "(none)"){
							picture_msg = '<image style="width:'+widpic+'px;height:auto;" src="'+$_data['EVIDENCE']+'"/>';
						}
						
						$('#analyze_picture').val("");
						$('#analyze_picture').attr('source','');
						$('#analyze_picture').attr('data-original-title',picture_msg);
					}
					
					if ($('#anid').val() == "Auto Genarate"){
						$('#save').attr('disabled',(_insert == "T" ? false:true));
						$('#deleted').attr('disabled',true);
						if(_locat != $('#locat').find(':selected').val() && _level != 1){ $("#save").attr('disabled',true); }
					}else{
						$('#save').attr('disabled',(_update == "T" ? false:true));
						$('#deleted').attr('disabled',(_delete == "T" ? false:true));
						if(_locat != $('#locat').find(':selected').val() && _level != 1){ $("#save").attr('disabled',true); }
					}
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
			
			JDbtnt1createappr = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(JDbtnt1createappr !== null){
				JDbtnt1createappr.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

var jd_fn_checkstd = null;
function fn_checkstd($this){
	var dataToPost = new Object();
	dataToPost.STRNO 	= (typeof $('#strno').find(':selected').val() === 'undefined' ? "":$('#strno').find(':selected').val());
	dataToPost.MODEL 	= (typeof $('#model').find(':selected').val() === 'undefined' ? "":$('#model').find(':selected').val());
	dataToPost.BAAB  	= (typeof $('#baab').find(':selected').val() === 'undefined' ? "":$('#baab').find(':selected').val());
	dataToPost.COLOR 	= (typeof $('#color').find(':selected').val() === 'undefined' ? "":$('#color').find(':selected').val());
	dataToPost.STAT 	= $('#stat').val();
	dataToPost.ACTICOD 	= (typeof $('#acticod').find(':selected').val() === 'undefined' ? "":$('#acticod').find(':selected').val());
	dataToPost.dwnAmt	= $('#dwnAmt').val();
	dataToPost.LOCAT 	= (typeof $('#locat').find(':selected').val() === 'undefined' ? "":$('#locat').find(':selected').val());
	dataToPost.DT 		= $('#createDate').val();
	dataToPost.RESVNO 	= (typeof $('#resvno').find(':selected').val() === 'undefined' ? "":$('#resvno').find(':selected').val());
	
	//$('#loadding').fadeIn(200);
	jd_fn_checkstd = $.ajax({
		url:'../SYS04/Analyze/fn_checkstd',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if(data.error){
				$('#price_add').val('');
				$('#price').val('');
				$('#price').attr("stdid",'');
				$('#price').attr("subid",'');
				$('#price').attr("shcid",'');
				$('#interatert').val('');
				
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 10000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				
				$this.val(null).trigger('change');
			}else{
				if(data.msg != "CANTSTRNO"){
					if($("#locat").find(':selected').val() == data.html["LOCAT"]){
						var newOption = new Option(data.html["MODEL"], data.html["MODEL"], true, true);
						$('#model').empty().append(newOption).trigger('change');
						var newOption = new Option(data.html["BAAB"], data.html["BAAB"], true, true);
						$('#baab').empty().append(newOption).trigger('change');
						var newOption = new Option(data.html["COLOR"], data.html["COLOR"], true, true);
						$('#color').empty().append(newOption).trigger('change');
						$("#stat").val((typeof data.html["STAT"] === 'undefined' ? "": data.html["STAT"]));
						$("#sdateold").val((typeof data.html["SDATE"] === 'undefined' ? "": data.html["SDATE"]));
						$("#ydate").val((typeof data.html["YDATE"] === 'undefined' ? "": data.html["YDATE"]));
						$('#price_add').val(data.html["PRICE_ADD"]);
						$('#price').val(data.html["PRICE"]);
						$('#price').attr("stdid",data.html["STDID"]);
						$('#price').attr("subid",data.html["SUBID"]);
						$('#price').attr("shcid",data.html["SHCID"]);
						$('#interatert').val(data.html["interest_rate"]);
						
						if(typeof data.html["STRNO"] === 'undefined'){
							$('#model').attr("disabled",false).trigger('change');
							$('#baab').attr("disabled",false).trigger('change');
							$('#color').attr("disabled",false).trigger('change');					
							$('#price_add').attr("disabled",false);
							$('#price').attr("disabled",false);
							$('#price').attr("stdid","");
							$('#price').attr("subid","");
							$('#price').attr("shcid","");
							$('#interatert').attr("disabled",false);
						}else{
							$('#model').attr("disabled",true).trigger('change');
							$('#baab').attr("disabled",true).trigger('change');
							$('#color').attr("disabled",true).trigger('change');
							$('#price_add').attr("disabled",true);
							$('#price').attr("disabled",true);
							$('#interatert').attr("disabled",true);
						}					
					}else{
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 5000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: "ผิดพลาด รถอยู่ที่สาขา ["+data.html["LOCAT"]+"] ไม่สามารถคีย์ขายที่สาขา [" +$("#locat").find(':selected').val()+"] ได้ครับ"
						});
					}
				}else{
					$('#price_add').val('');
					$('#price').val('');
					$('#price').attr("stdid",'');
					$('#price').attr("subid",'');
					$('#price').attr("shcid",'');
					$('#interatert').val('');
				}
			}
			jd_fn_checkstd = null;
			
			//$('#loadding').fadeOut(200);
		},
		beforeSend: function(){ if(jd_fn_checkstd !== null){ jd_fn_checkstd.abort(); } }
	});
}

function fnload($thisForm){
	$("#locat").select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#locat').find(':selected').val();
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
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		disabled: (_level == 1 ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	$("#locat").change(function(){
		$('#resvno').val(null).trigger('change');
		$('#strno').val(null).trigger('change');
		$('#model').val(null).trigger('change');
		$('#baab').val(null).trigger('change');
		$('#color').val(null).trigger('change');
	});
	
	
	$('#acticod').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#acticod').find(':selected').val();
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
		dropdownParent: $('#acticod').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	//$('#acticod').val().trigger('change');
	$('#acticod').on("select2:select",function(){ fn_checkstd($(this)); });
	$('#dwnAmt').keyup(function(){ fn_checkstd($(this)); });
	
	$('#resvno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getRESVNO',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#resvno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $("#locat").find(":selected").val() === "undefined" ? "" : $("#locat").find(":selected").val());
				
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
		dropdownParent: $('#resvno').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDresvno = null;
	$('#resvno').on("select2:select",function(){
		//$('#resvno').val(null).trigger('change');
		var dataToPost = new Object();
		dataToPost.dwnAmt = $('#dwnAmt').val();
		dataToPost.resvno = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
		dataToPost.acticod	= (typeof $("#acticod").find(':selected').val() === "undefined" ? "ALL" : $("#acticod").find(':selected').val());
		
		$('#loadding').fadeIn(200);
		JDresvno = $.ajax({
			url:'../SYS04/Analyze/dataResv',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				
				if(data.error){
					resvnull(); // เคลียร์รายการ
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 10000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$("#resvAmt").val((typeof data.html["RESPAY"] === 'undefined' ? "": data.html["RESPAY"]));
					
					var newOption = new Option(data.html["ACTIDES"], data.html["ACTICOD"], true, true);
					$('#acticod').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["STRNO"], data.html["STRNO"], true, true);
					$('#strno').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["MODEL"], data.html["MODEL"], true, true);
					$('#model').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["BAAB"], data.html["BAAB"], true, true);
					$('#baab').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["COLOR"], data.html["COLOR"], true, true);
					$('#color').empty().append(newOption).trigger('change');
					$("#stat").val((typeof data.html["STAT"] === 'undefined' ? "": data.html["STAT"]));
					$("#sdateold").val((typeof data.html["SDATE"] === 'undefined' ? "": data.html["SDATE"]));
					$("#ydate").val((typeof data.html["YDATE"] === 'undefined' ? "": data.html["YDATE"]));
					
					//var newOption = new Option(data.html["CUSNAME"], data.html["CUSCOD"], true, true);
					$('#cuscod').val(data.html["CUSNAME"]);
					$('#cuscod').attr("CUSCOD",data.html["CUSCOD"]);
					$("#idno").val((typeof data.html["IDNO"] === 'undefined' ? "": data.html["IDNO"]));
					$('#idnoBirth').val(data.html["BIRTHDT"]);
					$('#idnoExpire').val(data.html["EXPDT"]);
					$('#idnoAge').val(data.html["AGE"]);				
					
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#addr1').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#addr2').empty().append(newOption).trigger('change');
					
					$('#phoneNumber').val(data.html["MOBILENO"]);
					$('#income').val(data.html["MREVENU"]);
					$('#price_add').val(data.html["PRICE_ADD"]);
					$('#price').val(data.html["price"]);
					$('#price').attr('stdid',data.html["STDID"]);
					$('#price').attr('subid',data.html["SUBID"]);
					$('#price').attr('shcid',data.html["SHCID"]);
					$('#interatert').val(data.html["interest_rate"]);
					
					/*new in ytk*/
					$('#idnoStat').val(data.html["CUSSTAT"]).trigger('change');
					$('#baby').val(data.html["CUSBABY"]);
					$('#socialSecurity').val(data.html["SOCAILSECURITY"]);
					$('#hostName').val(data.html["HOSTNAME"]);
					$('#hostIDNo').val(data.html["HOSTIDNO"]);
					$('#hostPhone').val(data.html["HOSTTEL"]);
					$('#hostRelation').val(data.html["HOSTRELATION"]);
					var newOption = new Option(data.html["EMPRELATIONNAME"], data.html["EMPRELATION"], true, true);
					$('#empRelation').empty().append(newOption).trigger('change');
					$('#reference').val(data.html["REFERANT"]);
					
					var widpic = ($('#picture').width());
					var picture_msg = data.html["filePath"];
					if(data.html["filePath"] != "(none)"){
						picture_msg = '<image style="width:'+widpic+'px;height:auto;" src="'+data.html["filePath"]+'"/>';
					}
					$('#picture').val("");
					$('#picture').attr('source','');
					$('#picture').attr('data-original-title',picture_msg);
					
					if(typeof data.html["RESVNO"] === 'undefined'){
						$('#acticod').attr("disabled",false).trigger('change');
						$('#strno').attr("disabled",false).trigger('change');
						$('#model').attr("disabled",false).trigger('change');
						$('#baab').attr("disabled",false).trigger('change');
						$('#color').attr("disabled",false).trigger('change');
						
						$('#price_add').attr("disabled",false);				
						$('#price').attr("disabled",false);				
						$('#price').attr('stdid','');
						$('#price').attr('subid','');
						$('#price').attr('shcid','');
						$('#interatert').attr("disabled",false);
						$('#cuscod').attr("disabled",false);
						$('#cuscod_removed').attr('disabled',false);
						$("#idno").attr("disabled",false);
						$('#idnoBirth').attr("disabled",false);
						$('#idnoExpire').attr("disabled",false);
						$('#idnoAge').attr("disabled",false);
					}else{
						//ระบุบิลจองมาด้วย
						$('#acticod').attr("disabled",true).trigger('change');
						$('#strno').attr("disabled",true).trigger('change');
						$('#model').attr("disabled",true).trigger('change');
						$('#baab').attr("disabled",true).trigger('change');
						$('#color').attr("disabled",true).trigger('change');
						
						$('#price_add').attr("disabled",true);
						$('#price').attr("disabled",true);
						$('#interatert').attr("disabled",true);
						$('#cuscod').attr("disabled",true);
						$('#cuscod_removed').attr('disabled',(_level == 1 ? false:true));
						$("#idno").attr("disabled",true);
						$('#idnoBirth').attr("disabled",true);
						$('#idnoExpire').attr("disabled",true);
						$('#idnoAge').attr("disabled",true);
					}
				}
				
				JDresvno = null;
			},
			beforeSend: function(){ if(JDresvno !== null){ JDresvno.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#resvno').on("select2:unselect",function(){ resvnull(); }); // เคลียร์รายการ
	
	function resvnull(){ 
		// เคลียร์รายการ
		$('#resvno').empty().trigger('change');
		$("#resvAmt").val("");
		//$('#acticod').empty().trigger('change');
		$('#strno').empty().trigger('change');
		$('#model').empty().trigger('change');
		$('#baab').empty().trigger('change');
		$('#color').empty().trigger('change');
		$("#stat").val("");
		$("#sdateold").val("");
		$("#ydate").val("");
		$('#price_add').val("");
		$('#price').val("");
		$('#price').attr("stdid","");
		$('#price').attr("subid","");
		$('#interatert').val("");
		
		$('#cuscod').val('');
		$("#idno").val("");
		$('#idnoBirth').val("");
		$('#idnoExpire').val("");
		$('#idnoAge').val("");				
		
		$('#addr1').empty().trigger('change');
		$('#addr2').empty().trigger('change');
		
		$('#phoneNumber').val("");
		$('#income').val("");
		
		$('#acticod').attr("disabled",false).trigger('change');
		$('#strno').attr("disabled",false).trigger('change');
		$('#model').attr("disabled",false).trigger('change');
		$('#baab').attr("disabled",false).trigger('change');
		$('#color').attr("disabled",false).trigger('change');
		
		$('#cuscod').attr("disabled",false);
		$('#cuscod_removed').attr("disabled",false);
		$("#idno").attr("disabled",false);
		$('#idnoBirth').attr("disabled",false);
		$('#idnoExpire').attr("disabled",false);
		$('#idnoAge').attr("disabled",false);
	}
	
	$('#strno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $("#locat").find(":selected").val() === "undefined" ? "" : $("#locat").find(":selected").val());
				
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
		dropdownParent: $('#strno').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDstrno = null;
	$('#strno').on("select2:select",function(){
		var dataToPost = new Object();
		dataToPost.dwnAmt 	  = $('#dwnAmt').val();
		dataToPost.createDate = $('#createDate').val();
		dataToPost.strno 	  = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
		dataToPost.acticod	  = (typeof $("#acticod").find(':selected').val() === "undefined" ? "ALL" : $("#acticod").find(':selected').val());
		
		if(dataToPost.strno != ""){
			$('#loadding').fadeIn(0);
			JDstrno = $.ajax({
				url:'../SYS04/Analyze/dataSTR',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if(data.error){
						resvnull(); // เคลียร์รายการ
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 10000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.msg
						});
					}else{
						if($("#locat").find(':selected').val() == data.html["LOCAT"]){
							var newOption = new Option(data.html["MODEL"], data.html["MODEL"], true, true);
							$('#model').empty().append(newOption).trigger('change');
							var newOption = new Option(data.html["BAAB"], data.html["BAAB"], true, true);
							$('#baab').empty().append(newOption).trigger('change');
							var newOption = new Option(data.html["COLOR"], data.html["COLOR"], true, true);
							$('#color').empty().append(newOption).trigger('change');
							$("#stat").val((typeof data.html["STAT"] === 'undefined' ? "": data.html["STAT"]));
							$("#sdateold").val((typeof data.html["SDATE"] === 'undefined' ? "": data.html["SDATE"]));
							$("#ydate").val((typeof data.html["YDATE"] === 'undefined' ? "": data.html["YDATE"]));
							$('#price_add').val(data.html["PRICE_ADD"]);
							$('#price').val(data.html["PRICE"]);
							$('#price').attr("stdid",data.html["STDID"]);
							$('#price').attr("subid",data.html["SUBID"]);
							$('#price').attr("shcid",data.html["SHCID"]);
							$('#interatert').val(data.html["interest_rate"]);
							
							if(typeof data.html["STRNO"] === 'undefined'){
								$('#model').attr("disabled",false).trigger('change');
								$('#baab').attr("disabled",false).trigger('change');
								$('#color').attr("disabled",false).trigger('change');					
								$('#price_add').attr("disabled",false);
								$('#price').attr("disabled",false);
								$('#price').attr("stdid","");
								$('#price').attr("subid","");
								$('#price').attr("shcid","");
								$('#interatert').attr("disabled",false);
							}else{
								$('#model').attr("disabled",true).trigger('change');
								$('#baab').attr("disabled",true).trigger('change');
								$('#color').attr("disabled",true).trigger('change');
								$('#price_add').attr("disabled",true);
								$('#price').attr("disabled",true);
								$('#interatert').attr("disabled",true);
							}					
						}else{
							$('#resvno').val(null).trigger('change');
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 5000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: "ผิดพลาด รถอยู่ที่สาขา ["+data.html["LOCAT"]+"] ไม่สามารถคีย์ขายที่สาขา [" +$("#locat").find(':selected').val()+"] ได้ครับ"
							});
						}
					}
					
					JDstrno = null;
					$('#loadding').fadeOut(0);
				},
				beforeSend: function(){
					if(JDstrno !== null){
						JDstrno.abort();
					}
				},
				error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
		}
	});
	
	$('#strno').on("select2:unselect",function(){
		$('#model').empty().trigger('change');
		$('#baab').empty().trigger('change');
		$('#color').empty().trigger('change');
		$("#stat").val("");
		$("#sdateold").val("");
		$("#ydate").val("");
		$('#price').val("");
		$('#price').attr("stdid","");
		$('#price').attr("subid","");
		$('#interatert').val("");
		
		$('#model').attr("disabled",false).trigger('change');
		$('#baab').attr("disabled",false).trigger('change');
		$('#color').attr("disabled",false).trigger('change');
		$('#price').attr("disabled",false);
		$('#interatert').attr("disabled",false);
	});	
	
	$('#model').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#model').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = "HONDA";
				
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
		allowClear: false,
		multiple: false,
		dropdownParent: $('#model').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#baab').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#baab').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = "HONDA";
				dataToPost.MODEL = (typeof $("#model").find(":selected").val() === "undefined" ? "" : $("#model").find(":selected").val());
				
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
		allowClear: false,
		multiple: false,
		dropdownParent: $('#baab').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#color').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCOLOR',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#color').find(':selected').val();
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
		allowClear: false,
		multiple: false,
		dropdownParent: $('#color').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$(".toggleData").click(function(){
		var thisc = $(this).attr('thisc');
		
		if($(this).hasClass('glyphicon-minus')){
			$(this).removeClass('glyphicon-minus');
			$(this).addClass('glyphicon-plus');
		}else{
			$(this).addClass('glyphicon-minus');
			$(this).removeClass('glyphicon-plus');			
		}
		
		if($("."+thisc).attr('isshow')==1){
			$("."+thisc).fadeOut(300);	
			$("."+thisc).attr('isshow',0);
		}else{
			$("."+thisc).fadeIn(1000);	
			$("."+thisc).attr('isshow',1);
		}
	});
	
	var JDsave = null;
	$('#save').click(function(){
		$('#save').attr('disabled',true);
		$('#deleted').attr('disabled',true);
		
		var dataToPost = new Object();
		dataToPost.anid 		= $('#anid').val();
		dataToPost.locat 		= (typeof $('#locat').find(':selected').val() === 'undefined' ? '' : $('#locat').find(':selected').val());
		dataToPost.acticod		= (typeof $('#acticod').find(':selected').val() === 'undefined' ? '' : $('#acticod').find(':selected').val());
		dataToPost.resvno 		= (typeof $('#resvno').find(':selected').val() === 'undefined' ? '' : $('#resvno').find(':selected').val());
		dataToPost.resvAmt 		= $('#resvAmt').val();
		dataToPost.dwnAmt 		= $('#dwnAmt').val();
		dataToPost.insuranceType = $('.insuranceType[name=insuranceType]:checked').val();
		dataToPost.insuranceAmt = $('#insuranceAmt').val();
		dataToPost.nopay		= $('#nopay').val();
		dataToPost.strno 		= (typeof $('#strno').find(':selected').val() === 'undefined' ? '' : $('#strno').find(':selected').val());
		dataToPost.model 		= (typeof $('#model').find(':selected').val() === 'undefined' ? '' : $('#model').find(':selected').val());
		dataToPost.baab 		= (typeof $('#baab').find(':selected').val() === 'undefined' ? '' : $('#baab').find(':selected').val());
		dataToPost.color 		= (typeof $('#color').find(':selected').val() === 'undefined' ? '' : $('#color').find(':selected').val());
		dataToPost.stat			= $('#stat').val();
		dataToPost.sdateold		= $('#sdateold').val();
		dataToPost.ydate		= $('#ydate').val();
		dataToPost.price		= $('#price').val();
		dataToPost.stdid		= $('#price').attr('stdid');
		dataToPost.subid		= $('#price').attr('subid');
		dataToPost.shcid		= $('#price').attr('shcid');
		dataToPost.interatert	= $('#interatert').val();
		
		dataToPost.cuscod 		= $('#cuscod').attr('cuscod');
		dataToPost.idno			= $('#idno').val();
		dataToPost.idnoBirth	= $('#idnoBirth').val();
		dataToPost.idnoExpire	= $('#idnoExpire').val();
		dataToPost.idnoAge		= $('#idnoAge').val();
		dataToPost.idnoStat 	= (typeof $('#idnoStat').find(':selected').val() === 'undefined' ? '' : $('#idnoStat').find(':selected').val());
		dataToPost.addr1 		= (typeof $('#addr1').find(':selected').val() === 'undefined' ? '' : $('#addr1').find(':selected').val());
		dataToPost.addr2 		= (typeof $('#addr2').find(':selected').val() === 'undefined' ? '' : $('#addr2').find(':selected').val());
		dataToPost.phoneNumber	= $('#phoneNumber').val();
		dataToPost.socialSecurity	= $('#socialSecurity').val();
		dataToPost.baby			= $('#baby').val();
		dataToPost.career		= $('#career').val();
		dataToPost.careerOffice	= $('#careerOffice').val();
		dataToPost.careerPhone	= $('#careerPhone').val();
		dataToPost.income		= $('#income').val();
		dataToPost.hostName		= $('#hostName').val();
		dataToPost.hostIDNo		= $('#hostIDNo').val();
		dataToPost.hostPhone	= $('#hostPhone').val();
		dataToPost.hostRelation	= $('#hostRelation').val();
		dataToPost.empRelation	= (typeof $('#empRelation').find(':selected').val() === 'undefined' ? '' : $('#empRelation').find(':selected').val());
		dataToPost.reference	= $('#reference').val();
		dataToPost.picture_name	= $('#picture').val();
		dataToPost.picture		= $('#picture').attr('source');
		
		dataToPost.is1_cuscod 		= $('#is1_cuscod').attr('cuscod');
		dataToPost.is1_idno			= $('#is1_idno').val();
		dataToPost.is1_idnoBirth	= $('#is1_idnoBirth').val();
		dataToPost.is1_idnoExpire	= $('#is1_idnoExpire').val();
		dataToPost.is1_idnoAge		= $('#is1_idnoAge').val();
		dataToPost.is1_idnoStat 	= (typeof $('#is1_idnoStat').find(':selected').val() === 'undefined' ? '' : $('#is1_idnoStat').find(':selected').val());
		dataToPost.is1_addr1 		= (typeof $('#is1_addr1').find(':selected').val() === 'undefined' ? '' : $('#is1_addr1').find(':selected').val());
		dataToPost.is1_addr2 		= (typeof $('#is1_addr2').find(':selected').val() === 'undefined' ? '' : $('#is1_addr2').find(':selected').val());
		dataToPost.is1_phoneNumber	= $('#is1_phoneNumber').val();
		dataToPost.is1_socialSecurity	= $('#is1_socialSecurity').val();
		dataToPost.is1_baby			= $('#is1_baby').val();
		dataToPost.is1_career		= $('#is1_career').val();
		dataToPost.is1_careerOffice	= $('#is1_careerOffice').val();
		dataToPost.is1_careerPhone	= $('#is1_careerPhone').val();
		dataToPost.is1_income		= $('#is1_income').val();
		dataToPost.is1_hostName		= $('#is1_hostName').val();
		dataToPost.is1_hostIDNo		= $('#is1_hostIDNo').val();
		dataToPost.is1_hostPhone	= $('#is1_hostPhone').val();
		dataToPost.is1_hostRelation	= $('#is1_hostRelation').val();
		dataToPost.is1_empRelation	= (typeof $('#is1_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is1_empRelation').find(':selected').val());
		dataToPost.is1_reference	= $('#is1_reference').val();
		dataToPost.is1_picture_name	= $('#is1_picture').val();
		dataToPost.is1_picture		= $('#is1_picture').attr('source');
		
		dataToPost.is2_cuscod 		= $('#is2_cuscod').attr('cuscod');
		dataToPost.is2_idno			= $('#is2_idno').val();
		dataToPost.is2_idnoBirth	= $('#is2_idnoBirth').val();
		dataToPost.is2_idnoExpire	= $('#is2_idnoExpire').val();
		dataToPost.is2_idnoAge		= $('#is2_idnoAge').val();
		dataToPost.is2_idnoStat 	= (typeof $('#is2_idnoStat').find(':selected').val() === 'undefined' ? '' : $('#is2_idnoStat').find(':selected').val());
		dataToPost.is2_addr1 		= (typeof $('#is2_addr1').find(':selected').val() === 'undefined' ? '' : $('#is2_addr1').find(':selected').val());
		dataToPost.is2_addr2 		= (typeof $('#is2_addr2').find(':selected').val() === 'undefined' ? '' : $('#is2_addr2').find(':selected').val());
		dataToPost.is2_phoneNumber	= $('#is2_phoneNumber').val();
		dataToPost.is2_baby			= $('#is2_baby').val();
		dataToPost.is2_socialSecurity	= $('#is2_socialSecurity').val();
		dataToPost.is2_career		= $('#is2_career').val();
		dataToPost.is2_careerOffice	= $('#is2_careerOffice').val();
		dataToPost.is2_careerPhone	= $('#is2_careerPhone').val();
		dataToPost.is2_income		= $('#is2_income').val();
		dataToPost.is2_hostName		= $('#is2_hostName').val();
		dataToPost.is2_hostIDNo		= $('#is2_hostIDNo').val();
		dataToPost.is2_hostPhone	= $('#is2_hostPhone').val();
		dataToPost.is2_hostRelation	= $('#is2_hostRelation').val();
		dataToPost.is2_empRelation	= (typeof $('#is2_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is2_empRelation').find(':selected').val());
		dataToPost.is2_reference	= $('#is2_reference').val();
		dataToPost.is2_picture_name	= $('#is2_picture').val();
		dataToPost.is2_picture		= $('#is2_picture').attr('source');
		
		dataToPost.is3_cuscod 		= $('#is3_cuscod').attr('cuscod');
		dataToPost.is3_idno			= $('#is3_idno').val();
		dataToPost.is3_idnoBirth	= $('#is3_idnoBirth').val();
		dataToPost.is3_idnoExpire	= $('#is3_idnoExpire').val();
		dataToPost.is3_idnoAge		= $('#is3_idnoAge').val();
		dataToPost.is3_idnoStat 	= (typeof $('#is3_idnoStat').find(':selected').val() === 'undefined' ? '' : $('#is3_idnoStat').find(':selected').val());
		dataToPost.is3_addr1 		= (typeof $('#is3_addr1').find(':selected').val() === 'undefined' ? '' : $('#is3_addr1').find(':selected').val());
		dataToPost.is3_addr2 		= (typeof $('#is3_addr2').find(':selected').val() === 'undefined' ? '' : $('#is3_addr2').find(':selected').val());
		dataToPost.is3_phoneNumber	= $('#is3_phoneNumber').val();
		dataToPost.is3_baby			= $('#is3_baby').val();
		dataToPost.is3_socialSecurity	= $('#is3_socialSecurity').val();
		dataToPost.is3_career		= $('#is3_career').val();
		dataToPost.is3_careerOffice	= $('#is3_careerOffice').val();
		dataToPost.is3_careerPhone	= $('#is3_careerPhone').val();
		dataToPost.is3_income		= $('#is3_income').val();
		dataToPost.is3_hostName		= $('#is3_hostName').val();
		dataToPost.is3_hostIDNo		= $('#is3_hostIDNo').val();
		dataToPost.is3_hostPhone	= $('#is3_hostPhone').val();
		dataToPost.is3_hostRelation	= $('#is3_hostRelation').val();
		dataToPost.is3_empRelation	= (typeof $('#is3_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is3_empRelation').find(':selected').val());
		dataToPost.is3_reference	= $('#is3_reference').val();
		dataToPost.is3_picture_name	= $('#is3_picture').val();
		dataToPost.is3_picture		= $('#is3_picture').attr('source');
		
		dataToPost.empIDNo	= (typeof $('#empIDNo').find(':selected').val() === 'undefined' ? '' : $('#empIDNo').find(':selected').val());
		dataToPost.empTel	= $('#empTel').val();
		dataToPost.mngIDNo	= (typeof $('#mngIDNo').find(':selected').val() === 'undefined' ? '' : $('#mngIDNo').find(':selected').val());
		dataToPost.mngTel	= $('#mngTel').val();
		
		dataToPost.analyze_picture_name = $('#analyze_picture').val();
		dataToPost.analyze_picture = $('#analyze_picture').attr('source');
		
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeButton: false,
			msg: 'คุณต้องการบันทึกการใบวิเคราะห์หรือไม่ ? <br><span style="color:red;font-size:16pt">*** กรณีที่บันทึกแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก ยืนยันการทำรายการ</span>',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน ,บันทึกใบวิเคราะห์',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ไว้ทีหลัง',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					$('#loadding').fadeIn(500);
					
					JDsave = $.ajax({
						url:'../SYS04/Analyze/save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(200);
							
							if(data.error){
								var msg = data.msg.length;
								var msgDesplay = "";
								for(var i=0;i<msg;i++){
									if(i>0) msgDesplay += "<br>";
									msgDesplay += (i+1)+". "+data.msg[i];
								}
								
								if(msgDesplay != ""){
									msgDesplay += "<br><br><span style='background-color:white;color:red;font-size:16pt;'>ไม่สามารถบันทึกได้ครับ</span>";
								}
								
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: msgDesplay
								});
							}else{
								Lobibox.notify('success', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg[0]
								});
								
								$thisForm.destroy();
							}
							
							JDsave = null;
						},
						beforeSend: function(){
							if(JDsave !== null){
								JDsave.abort();
							}
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}else{
					Lobibox.notify('info', {
						title: '',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "คุณยังไม่ได้บันทึกข้อมูล"
					});
				}
				
				if ($('#anid').val() == "Auto Genarate"){
					$('#save').attr('disabled',(_insert == "T" ? false:true));
					$('#deleted').attr('disabled',true);
				}else{
					$('#save').attr('disabled',(_update == "T" ? false:true));
					$('#deleted').attr('disabled',(_delete == "T" ? false:true));
				}
			}
		});
	});
	
	var JDdeleted = null;
	$('#deleted').click(function(){
		$('#deleted').attr('disabled',true);
		$('#save').attr('disabled',true);
		
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeButton: false,
			msg: 'คุณต้องการ<b><u>ยกเลิก</u></b>ใบวิเคราะห์หรือไม่ ? <br><textarea id="cancel_msg" maxlength="8000" rows="3" class="col-sm-12" placeholder="สาเหตุที่ยกเลิก" style="resize:vertical;"></textarea><br><span style="color:red;font-size:16pt">*** กรณีที่บันทึกแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก ยืนยันการทำรายการ</span>',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน ,ยกเลิกใบวิเคราะห์',
					closeOnClick: false,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ไว้ทีหลัง',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
					dataToPost.anid 	  = $('#anid').val();
					dataToPost.cancel_msg = $('#cancel_msg').val();
					
					JDdeleted = $.ajax({
						url:'../SYS04/Analyze/an_cancel',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							
							if(data.error){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}else{
								Lobibox.notify('success', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
								
								lobibox.destroy();
								$thisForm.destroy();
							}
							
							JDdeleted = null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(JDdeleted !== null){ JDdeleted.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}else{
					Lobibox.notify('info', {
						title: '',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "คุณยังไม่ได้บันทึกข้อมูล"
					});
					
					if ($('#anid').val() == "Auto Genarate"){
						$('#save').attr('disabled',(_insert == "T" ? false:true));
						$('#deleted').attr('disabled',true);
					}else{
						$('#save').attr('disabled',(_update == "T" ? false:true));
						$('#deleted').attr('disabled',(_delete == "T" ? false:true));
					}
				}
			}	
		});	
	});
	
	fn_select2_multiples();
}

function fn_select2_multiples(){
	$('[data-toggle="tooltip"]').tooltip();
	/*20200203*/
	$('.select2_addrno').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				var tags = $(this).attr('data-jd-tags');
				
				var dataToPost = new Object();
				dataToPost.now 		= (typeof $('#'+tags+'addr1').find(':selected').val() === 'undefined' ? '' : $('#'+tags+'addr1').find(':selected').val());
				dataToPost.q	 	= (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod 	= $('#'+tags+'cuscod').attr('cuscod');
				
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
		allowClear: false,
		multiple: false,
		//dropdownParent: $('.lobibox-body'),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('.select2_empRelation').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				var tags = $(this).attr('data-jd-tags');
				
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#'+tags+'empRelation').find(':selected').val() === 'undefined' ? '' : $('#'+tags+'empRelation').find(':selected').val());
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
		//dropdownParent: $('#empRelation').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#empIDNo').select2({
		placeholder: 'เลือก',
		tags: true,
		//tokenSeparators: [","],
		createTag: function (params) {
			var term = $.trim(params.term);
			if (term === '') { return null; }
			return {id: term,text: term + ' (พนักงานใหม่)'};
		},
		ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#empIDNo').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				$('#loadding').fadeIn(200);
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				$('#loadding').fadeOut(200);
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: true,
		multiple: false,
		//dropdownParent: $('#empIDNo').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#mngIDNo').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = $('#mngIDNo').find(':selected').val();
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
		tags: true,
		createTag: function (params) {
			var term = $.trim(params.term);
			if (term === '') { return null; }
			return {id: term,text: term + ' (พนักงานใหม่)'};
		},
		allowClear: true,
		multiple: false,
		//dropdownParent: $('#mngIDNo').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JD_CUSCOD = null;
	$('#cuscod, #is1_cuscod, #is2_cuscod, #is3_cuscod').click(function(){
		var tags = $(this).attr('tags');		
		
		$('#loadding').fadeIn(200);		
		JD_CUSCOD = $.ajax({
			url:'../Cselect2/getfromCUSTOMER',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#'+tags+'cuscod').attr('disabled',true);
				
				Lobibox.window({
					title: 'FORM CUSTOMER',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($thisCUS){
						var jd_cus_search = null;
						$('#cus_fname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_lname').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_idno').keyup(function(e){ if(e.keyCode === 13){ fnResultCUSTOMER(); } });
						$('#cus_search').click(function(){ fnResultCUSTOMER(); });
						
						function fnResultCUSTOMER(){
							data = new Object();
							data.fname 		= $('#cus_fname').val();
							data.lname 		= $('#cus_lname').val();
							data.idno 		= $('#cus_idno').val();
							data.cuscod 	= $('#cuscod').attr('cuscod');
							data.is1_cuscod = $('#is1_cuscod').attr('cuscod');
							data.is2_cuscod = $('#is2_cuscod').attr('cuscod');
							data.is3_cuscod = $('#is3_cuscod').attr('cuscod');
							
							$('#loadding').fadeIn(200);
							jd_cus_search = $.ajax({
								url:'../Cselect2/getResultCUSTOMER',
								data:data,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#cus_result').html(data.html);
									
									$('.CUSDetails').unbind('click');
									var JDfn_getdata_customers = null;
									$('.CUSDetails').click(function(){
										var dtp = new Object();
										dtp.cuscod  = $(this).attr('CUSCOD');
										dtp.cusname = $(this).attr('CUSNAMES');
										
										$('#'+tags+'cuscod').attr('CUSCOD',dtp.cuscod);
										$('#'+tags+'cuscod').val(dtp.cusname);
										
										var dataToPost = new Object();
										dataToPost.cuscod = dtp.cuscod;
										$('#loadding').fadeIn(200);	
										JDfn_getdata_customers = $.ajax({
											url:'../SYS04/Analyze/dataCUS',
											data: dataToPost,
											type: 'POST',
											dataType: 'json',
											success: function(data){
												$('#loadding').fadeOut(200);			
												JDfn_getdata_customers = null;
												
												$('#'+tags+'idno').val(data.html["IDNO"]);
												$('#'+tags+'idnoBirth').val(data.html["BIRTHDT"]);
												$('#'+tags+'idnoExpire').val(data.html["EXPDT"]);
												$('#'+tags+'idnoAge').val(data.html["AGE"]);
												
												var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
												$('#'+tags+'addr1').empty().append(newOption).trigger('change');
												var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
												$('#'+tags+'addr2').empty().append(newOption).trigger('change');
												
												
												$('#'+tags+'career').val(data.html["OCCUP"]);
												$('#'+tags+'careerOffice').val(data.html["OFFIC"]);
												
												$('#'+tags+'phoneNumber').val(data.html["MOBILENO"]);
												$('#'+tags+'income').val(data.html["MREVENU"]);
												
												if(typeof data.html["CUSCOD"] === 'undefined'){
													$('#'+tags+'idno').attr("disabled",false);
													$('#'+tags+'idnoBirth').attr("disabled",false);
													$('#'+tags+'idnoExpire').attr("disabled",false);
													$('#'+tags+'idnoAge').attr("disabled",false);
												}else{
													$('#'+tags+'idno').attr("disabled",true);
													$('#'+tags+'idnoBirth').attr("disabled",true);
													$('#'+tags+'idnoExpire').attr("disabled",true);
													$('#'+tags+'idnoAge').attr("disabled",true);
												}
												
												/*new in ytk*/
												$('#'+tags+'idnoStat').val(data.html["CUSSTAT"]).trigger('change');
												$('#'+tags+'baby').val(data.html["CUSBABY"]);
												$('#'+tags+'socialSecurity').val(data.html["SOCAILSECURITY"]);
												$('#'+tags+'hostName').val(data.html["HOSTNAME"]);
												$('#'+tags+'hostIDNo').val(data.html["HOSTIDNO"]);
												$('#'+tags+'hostPhone').val(data.html["HOSTTEL"]);
												$('#'+tags+'hostRelation').val(data.html["HOSTRELATION"]);
												var newOption = new Option(data.html["EMPRELATIONNAME"], data.html["EMPRELATION"], true, true);
												$('#'+tags+'empRelation').empty().append(newOption).trigger('change');
												$('#'+tags+'reference').val(data.html["REFERANT"]);
												
												var widpic = ($('#'+tags+'picture').width());
												
												var picture_msg = data.html["filePath"];
												if(data.html["filePath"] != "(none)"){
													picture_msg = '<image style="width:'+widpic+'px;height:auto;" src="'+data.html["filePath"]+'"/>';
												}
												$('#'+tags+'picture').val("");
												$('#'+tags+'picture').attr('source','');
												$('#'+tags+'picture').attr('data-original-title',picture_msg);
											
											},
											beforeSend: function(){ if(JDfn_getdata_customers !== null){ JDfn_getdata_customers.abort(); } },
											error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
										});
										
										$thisCUS.destroy();
									});
									
									$('#loadding').fadeOut(200);
									jd_cus_search = null;
								},
								beforeSend: function(){
									if(jd_cus_search !== null){ jd_cus_search.abort(); }
								},
								error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
							});
						}
					},
					beforeClose : function(){ 
						if($('#'+tags+'cuscod').val() == ""){
							$('#'+tags+'cuscod').attr('disabled',false);
						}else{
							$('#'+tags+'cuscod').attr('disabled',true);
						}
					}
				});
				
				JD_CUSCOD = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(JD_CUSCOD!==null){ JD_CUSCOD.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#cuscod_removed,#is1_cuscod_removed,#is2_cuscod_removed,#is3_cuscod_removed').click(function(){
		var tags = $(this).attr('tags');
		$('#'+tags+'cuscod').attr('CUSCOD','');
		$('#'+tags+'cuscod').val('');
		$('#'+tags+'cuscod').attr('disabled',false);
		
		$('#'+tags+'idno').val("");
		$('#'+tags+'idnoBirth').val("");
		$('#'+tags+'idnoExpire').val("");
		$('#'+tags+'idnoAge').val("");
		$('#'+tags+'idnoStat').val(null).trigger('change');
		$('#'+tags+'addr1').val(null).trigger('change');
		$('#'+tags+'addr2').val(null).trigger('change');
		$('#'+tags+'phoneNumber').val("");
		$('#'+tags+'baby').val("");
		$('#'+tags+'socialSecurity').val("");
		$('#'+tags+'career').val("");
		$('#'+tags+'careerOffice').val("");
		$('#'+tags+'careerPhone').val("");
		$('#'+tags+'income').val("");
		$('#'+tags+'hostName').val("");
		$('#'+tags+'hostIDNo').val("");
		$('#'+tags+'hostPhone').val("");
		$('#'+tags+'hostRelation').val("");
		$('#'+tags+'empRelation').val(null).trigger('change');
		$('#'+tags+'reference').val("");
		
		$('#'+tags+'idno').attr("disabled",false);
		$('#'+tags+'idnoBirth').attr("disabled",false);
		$('#'+tags+'idnoExpire').attr("disabled",false);
		$('#'+tags+'idnoAge').attr("disabled",false);
		
		$('#'+tags+'picture').val();
		$('#'+tags+'picture').attr('source','');
		$('#'+tags+'picture').attr('data-original-title','(none)');
	});
	
	
	$('.jd-upload-an').click(function(){
		//$('[data-toggle="tooltip"]').tooltip();
		var tags = $(this).attr('data-tags');
		var widpic = ($('#'+tags+'picture').width());
		if($('#'+tags+'cuscod').attr('cuscod') == ""){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "คุณยังไม่ได้ระบุรหัสลูกค้า"
			});
		}else{
			Lobibox.window({
				title: 'form upload',
				//width: $(window).width(),
				//height: $(window).height(),
				content: '<div id="upload_file"></div>',
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$('#upload_file').uploadFile({
						url:'../SYS04/Analyze/picture_receipt'
						,fileName: 'myfile'
						,maxFileCount: 1
						,multiple: false
						,maxFileSize: 10240*1024 // Allow size 10MB
						,showProgress: true
						,allowedTypes: "jpg,jpeg,png"
						,acceptFiles: 'image/jpg,image/jpeg,image/png'
						,dynamicFormData: function(){
							var data = { 
								IDNO 	: $('#'+tags+'idno').val()
							}
							return data;
						}
						,showPreview:true
						,previewHeight: '150px'
						,previewWidth: '150px'
						,dragDropStr: 'เลือกไฟล์'
						,abortStr:'เลือกไฟล์'
						,cancelStr:'ยกเลิก'
						,doneStr:'ผิดพลาด :: doneStr'
						,multiDragErrorStr: 'ผิดพลาด :: ลากวางได้ครั้งละ 1 รูป'
						,extErrorStr:'ผิดพลาด :: ต้องเป็นไฟล์ '
						,sizeErrorStr:'ผิดพลาด sizeErrorStr'
						,uploadErrorStr:'ผิดพลาด uploadErrorStr'
						,maxFileCountErrorStr: 'กรุณายกเลิกไฟล์เดิมก่อน ไม่อนุญาติให้เพิ่มไฟล์ อนุญาติให้อัพโหลดไฟล์ได้ :'
						,uploadStr:'เลือกไฟล์'					
						,onSuccess:function(files,data,xhr,pd) {
							var json = JSON.parse(data.trim());
							$('#'+tags+'picture').val(json["name"]);
							$('#'+tags+'picture').attr('source',json["source"]);
							$('#'+tags+'picture').attr('data-original-title','<image style="width:'+widpic+'px;height:auto;" src="'+json["source"]+'"/>');
							
							$this.destroy();
						}
						,showStatusAfterSuccess: true
						,autoSubmit:true
					});
				}
			});
		}
	});
	/*20200203*/	
}



















