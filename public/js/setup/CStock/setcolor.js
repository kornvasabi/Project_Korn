 /********************************************************
             ______@29/12/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

$(function(){
	if($('.tab1[name="home"]').attr('cin') == 'T'){
		$('#add_baab').attr('disabled',false);	
	}else{
		$('#add_baab').attr('disabled',true);	
	}
});


var jdsearch_color=null;
$('#search_color').click(function(){
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.TYPECOD 	= $('#TYPECOD').val();	
	dataToPost.MODELCOD = $('#MODELCOD').val();	
	dataToPost.BAABCOD 	= $('#BAABCOD').val();	
	dataToPost.COLORCOD = $('#COLORCOD').val();	
	
	$('#loadding').fadeIn(200);
	jdsearch_color = $.ajax({
		url: '../setup/CStock/colorSearch',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setcolorResult').html(data.html);
			afterSearch();
			
			jdsearch_color = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){ if(jdsearch_color !== null){ jdsearch_color.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}


function afterSearch(){
	document.getElementById("tbScroll").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});	
	
	$('.getit').hover(function(){
		$(this).css({'background-color':'yellow'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
	},function(){
		$(this).css({'background-color':''});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
	});
	
	
	var jdgetit = null;
	$('.getit').click(function(){
		dataToPost = new Object();
		dataToPost.TYPECOD 	= $(this).attr('TYPECOD');
		dataToPost.MODELCOD = $(this).attr('MODELCOD');
		dataToPost.BAABCOD 	= $(this).attr('BAABCOD');
		dataToPost.COLORCOD	= $(this).attr('COLORCOD');
		
		$('#loadding').fadeIn(200);
		jdgetit = $.ajax({
			url: '../setup/CStock/colorGetFormAE',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success:function(data){
				$('#tab2_main').html(data.html);
				$('#t2gcode').attr('readonly',true);
				
				$('#tab2save').attr('action','edit');
				if(_update == 'T'){
					$('#tab2save').attr('disabled',false);	
				}else{
					$('#tab2save').attr('disabled',true);	
				}
				
				if(_delete == 'T'){
					$('#tab2del').attr('disabled',false);	
				}else{
					$('#tab2del').attr('disabled',true);	
				}
				afterSelect();
				
				jdgetit = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(jdgetit !== null){ jdgetit.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
}


function afterSelect(){
	$('.tab1').hide();
	$('.tab2').show();
	
	dataToPost = new Object();
	dataToPost.q = '';
	dataToPost.now = (typeof $('#t2TYPECOD').find(':selected').val() === "undefined" ? "":$('#t2TYPECOD').find(':selected').val());
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2/getTYPES',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$t2TYPECOD = $('#t2TYPECOD');
			for($i=0;$i<data.length;$i++){
				if($i==0){ $t2TYPECOD.empty(); } // clear
				$t2TYPECOD.append('<option value="'+data[$i].id+'"  '+(data[$i].id == dataToPost.now ? "selected":"")+'  >'+data[$i].text+'</option>');
			}
			$t2TYPECOD.select2({
				disabled: ($('#tab2save').attr('action') == "add" ? false:true),
				width: '100%'
			});
			$('#loadding').fadeOut(200);
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }		
	});
	
	$('#t2TYPECOD').on("select2:select",function(){
		$('#t2MODEL').val(null).trigger('change');
		$('#t2BAAB').val(null).trigger('change');
	});
	
	
	$t2MODEL = $('#t2MODEL');
	$t2MODEL.select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.q 		= (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.now 		= (typeof $('#t2MODEL').find(':selected').val() === "undefined" ? "":$('#t2MODEL').find(':selected').val());
				dataToPost.TYPECOD 	= (typeof $('#t2TYPECOD').find(':selected').val() === "undefined" ? "":$('#t2TYPECOD').find(':selected').val());
				
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
		dropdownParent: $("body"),
		disabled: ($('#tab2save').attr('action') == "add" ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
		
	$('#t2MODEL').on("select2:select",function(){
		$('#t2BAAB').val(null).trigger('change');
	});
	
	$t2BAAB = $('#t2BAAB');
	$t2BAAB.select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.q 		= (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.now 		= (typeof $('#t2BAAB').find(':selected').val() === "undefined" ? "":$('#t2BAAB').find(':selected').val());
				dataToPost.TYPECOD 	= (typeof $('#t2TYPECOD').find(':selected').val() === "undefined" ? "":$('#t2TYPECOD').find(':selected').val());
				dataToPost.MODEL 	= (typeof $('#t2MODEL').find(':selected').val() === "undefined" ? "":$('#t2MODEL').find(':selected').val());
				
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
		dropdownParent: $("body"),
		disabled: ($('#tab2save').attr('action') == "add" ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	
	
	$('#tab2back').click(function(){
		$('.tab1').show();
		$('.tab2').hide();
	});
	
	var jdtab2save=null;
	$('#tab2save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการบันทึก ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.TYPECOD 	= $('#t2TYPECOD').val();
					dataToPost.MODEL 	= $('#t2MODEL').val();
					dataToPost.BAAB 	= $('#t2BAAB').val();
					dataToPost.COLOR 	= $('#t2COLOR').val();
					dataToPost.TYPECOD_OLD 	= $('#t2TYPECOD').attr('TYPECOD');
					dataToPost.MODEL_OLD 	= $('#t2MODEL').attr('MODELCOD');
					dataToPost.BAAB_OLD 	= $('#t2BAAB').attr('BAABCOD');
					dataToPost.COLOR_OLD 	= $('#t2COLOR').attr('COLORCOD');
					dataToPost.MEMO1 	= $('#t2MEMO1').val();
					dataToPost.action 	= $('#tab2save').attr('action');
					
					$('#loadding').fadeIn(200);
					jdtab2save = $.ajax({
						url:'../setup/CStock/colorSave',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.stat){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
								
								$('.tab1').show();
								$('.tab2').hide();
								
								$('#tab2del').show();
								search();
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: true,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
							}
							
							jdtab2save=null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(jdtab2save !== null){ jdtab2save.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
	
	var jdtab2del= null;
	$('#tab2del').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการลบ ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.TYPECOD 	= $('#t2TYPECOD').val();
					dataToPost.MODEL 	= $('#t2MODEL').val();
					dataToPost.BAAB 	= $('#t2BAAB').val();
					dataToPost.COLOR 	= $('#t2COLOR').val();
					dataToPost.TYPECOD_OLD 	= $('#t2TYPECOD').attr('TYPECOD');
					dataToPost.MODEL_OLD 	= $('#t2MODEL').attr('MODELCOD');
					dataToPost.BAAB_OLD 	= $('#t2BAAB').attr('BAABCOD');
					dataToPost.COLOR_OLD 	= $('#t2COLOR').attr('COLORCOD');
					dataToPost.MEMO1 	= $('#t2MEMO1').val();
					dataToPost.action 	= $('#tab2save').attr('action');
					
					$('#loadding').fadeIn(200);
					jdtab2del = $.ajax({
						url:'../setup/CStock/colorDel',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.stat){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
								
								$('.tab1').show();
								$('.tab2').hide();
								
								search();
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									closeOnClick: true,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
							}
							
							jdtab2del = null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(jdtab2del !== null){ jdtab2del.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
	
	var jd_btnUpload = null;
	$('#tab2Import').click(function(){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'ยังไม่เปิดใช้งาน'
		});
		/*
		jd_btnUpload = $.ajax({
			url:'../setup/CStock/colorFormUPLOAD',
			//data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.window({
					title: 'import color',
					//width: $(window).width(),
					height: 270,
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($thisFile){
						initupload($thisFile);
					},
					beforeClose: function(){
						// $('#btnSave').attr('disabled',false);
						// $('#btnUpload').attr('disabled',false);
						// $('#btnDownload').attr('disabled',false);
					}
				});
				
				jd_btnUpload = null;
			},
			beforeSend: function(){
				if(jd_btnUpload !== null){
					jd_btnUpload.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
		*/
	});	
}


function initupload($thisFile){
	$("#fileupload").uploadFile({		
		url:'../setup/CStock/colorImport',
		fileName:'myfile',
		autoSubmit: true,
		acceptFiles: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
		allowedTypes: 'xls,xlsx',
		onSubmit:function(files){			
			$("#loadding").fadeIn(200);
		},
		onSuccess:function(files,data,xhr,pd){
			obj = JSON.parse(data);
			
			if(obj["error"]){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: obj["errorMsg"]
				});
			}else{
				Lobibox.window({
					title: 'import color',
					width: $(window).width(),
					height: $(window).height(),
					content: obj["html"],
					draggable: false,
					closeOnEsc: true,
					shown: function($thisFile){
						$('.btn_remove_color_upload').click(function(){
							$btn_rcu = $(this);
							
							Lobibox.confirm({
								title: 'ยืนยันการทำรายการ',
								iconClass: false,
								msg: "คุณต้องการลบข้อมูล <br><span style='color:blue;'>ชนิด "+$btn_rcu.attr('TYPECOD')+"  <br>รุ่น "+$btn_rcu.attr('MODELCOD')+" <br>แบบ "+$btn_rcu.attr('BAABCOD')+" <br>สี "+$btn_rcu.attr('COLORCOD')+"</span> <br><b style='color:Red;'>คุณแน่ใจว่าต้องการลบ ?</b>",
								buttons: {
									ok : {
										'class': 'btn btn-primary',
										text: 'ยืนยัน ,ลบไปเลยครับ',
										closeOnClick: true,
									},
									cancel : {
										'class': 'btn btn-danger',
										text: 'ยกเลิก ,ยังไม่ลบก่อนครับ',
										closeOnClick: true
									},
								},
								callback: function(lobibox, type){
									var btnType;
									if (type === 'ok'){
										data = new Object();
										data.type  = $btn_rcu.attr('TYPECOD');
										data.model = $btn_rcu.attr('MODELCOD');
										data.baab  = $btn_rcu.attr('BAABCOD');
										data.color = $btn_rcu.attr('COLORCOD');
										
										$btn_rcu.parent().parent().remove();
										let nowall = $('.nowall').text(); 
										$('.nowall').text(nowall-1);
										let nowadd = $('.nowadd').text(); 
										$('.nowadd').text(nowadd-1);
										
										let x = $('.item[item='+$btn_rcu.attr('item')+']').attr('SEQ');
										let seq = x-1;
										$('.item[item='+$btn_rcu.attr('item')+']').attr('SEQ',seq);
										if(seq == 1){
											$('.item[item='+$btn_rcu.attr('item')+']').parent().parent().css({"background-color":"white"});
										}
									}
								}
							});
						});
						
						$('#btn_save_upload').click(function(){
							if($('.nowadd').text() > 0){
								Lobibox.confirm({
									title: 'ยืนยันการทำรายการ',
									iconClass: false,
									msg: "<span style='color:blue;'>คุณต้องการบันทึก "+$('.nowadd').text()+" รายการ<br><b style='color:Red;'>คุณแน่ใจว่าต้องการบันทึก ?</b>",
									buttons: {
										ok : {
											'class': 'btn btn-primary',
											text: 'ยืนยัน ,บันทึก',
											closeOnClick: true,
										},
										cancel : {
											'class': 'btn btn-danger',
											text: 'ยกเลิก ,ไม่บันทึก',
											closeOnClick: true,
										},
									},
									callback: function(lobibox, type){
										var btnType;
										if (type === 'ok'){
											dataToPost = new Object();
											
											let arrs = new Array();
											let multi = 0;
											$('.btn_remove_color_upload').each(function(){
												data = new Object();
												data.type 	= $(this).attr('TYPECOD');
												data.model	= $(this).attr('MODELCOD');
												data.baab 	= $(this).attr('BAABCOD');
												data.color 	= $(this).attr('COLORCOD');
												data.colorTH= $(this).attr('COLORTH');
												
												if($(this).attr('SEQ')>1){
													multi++;
												}
												
												arrs.push(data);
											});
											
											dataToPost.data = (arrs.length == 0 ? "":arrs);
											
											if(multi == 0){
												$('#loadding').fadeIn(200);
												
												$.ajax({
													url: '../setup/CStock/color_save_import',
													data: dataToPost,
													type: 'POST',
													dataType: 'json',
													success:function(data){
														if(data.stat){
															Lobibox.notify('success', {
																title: 'สำเร็จ',
																size: 'mini',
																closeOnClick: false,
																delay: 8000,
																pauseDelayOnHover: true,
																continueDelayOnInactiveTab: false,
																icon: true,
																messageHeight: '90vh',
																soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
																soundExt: '.ogg',
																msg: data.msg
															});
															
															$('.tab1').show();
															$('.tab2').hide();
															
															$('#tab2del').show();
															search();
														}else{
															Lobibox.notify('error', {
																title: 'แจ้งเตือน',
																size: 'mini',
																closeOnClick: true,
																delay: 5000,
																pauseDelayOnHover: true,
																continueDelayOnInactiveTab: false,
																icon: true,
																messageHeight: '90vh',
																soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
																soundExt: '.ogg',
																msg: data.msg
															});
														}
														
														$('#loadding').fadeOut(200);
													}
												});
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
													msg: "ผิดพลาด มีข้อมูลซ้ำซ้อน"
												});
											}
											
											
											
										}
										
										// บันทึกไฟล์ UPLOAD 
									}
								});								
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
									msg: "ผิดพลาด ไม่มีข้อมูลสีรถที่สามารถเพิ่มได้"
								});
							}
						});
						
						//$thisFile.destroy();
					}
				});
				
			}
			
			$("#loadding").fadeOut(200);
		}
	});
} 

var jdadd_color=null;
$('#add_color').click(function(){
	dataToPost = new Object();
	dataToPost.TYPECOD = '';
	dataToPost.MODELCOD = '';
	dataToPost.BAABCOD = '';
	dataToPost.COLORCOD = '';
	
	$('#loadding').fadeIn(200);
	jdadd_color = $.ajax({
		url: '../setup/CStock/colorGetFormAE',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success:function(data){
			$('#tab2_main').html(data.html);
			$('#tab2save').attr('action','add');
			if(_insert == 'T'){
				$('#tab2save').attr('disabled',false);	
			}else{
				$('#tab2save').attr('disabled',true);	
			}
			$('#tab2del').attr('disabled',true);
			
			afterSelect();
			jdadd_color = null;
		},
		beforeSend: function(){ if(jdadd_color !== null){ jdadd_color.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});














