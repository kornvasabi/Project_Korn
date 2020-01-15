/********************************************************
             ______@27/12/2019______
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
 
 
 
 
 
 
var JDbtnt1search = null;
$("#btnt1search").click(function(){
	dataToPost = new Object();
	dataToPost.model 	= $("#search_model").val();
	dataToPost.baab 	= $("#search_baab").val();
	dataToPost.color 	= $("#search_color").val();
	dataToPost.manuyr 	= $("#search_manuyr").val();
	dataToPost.gcode 	= $("#search_gcode").val();
	dataToPost.locat 	= $("#search_locat").val();
		
	$('#loadding').show();
	JDbtnt1search = $.ajax({
		url:'../SYS04/StandardSHC/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide(200);
			
			Lobibox.window({
				title: 'รายการ Standard',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					
					$('#table_stdshc_search').on('draw.dt',function(){ redraw(); });
					fn_datatables('table_stdshc_search',1,180);
					
					function redraw(){
						$('.stdshc_edit').unbind('click');
						$('.stdshc_edit').click(function(){
							dataToPost = new Object();
							dataToPost.ID = $(this).attr('stdid');
							dataToPost.event = "edit";
							
							form_operation(dataToPost);
						});
					}
					JDbtnt1search = null;
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
		},
		beforeSend: function(){
			if(JDbtnt1search !== null){
				JDbtnt1search.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});



var JDbtnt1import = null;
$("#btnt1import").click(function(){
	JDbtnt1import = $.ajax({
		url:'../SYS04/StandardSHC/stdshcFormUPLOAD',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'นำเข้าสแตนดาร์ดรถมือสอง',
				//width: $(window).width(),
				height: '200',
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$("#form_stdshc").uploadFile({		
						url:'../SYS04/StandardSHC/import_stdshc',
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
									title: 'นำเข้าสแตนดาร์ดรถมือสอง',
									width: $(window).width(),
									height: $(window).height(),
									content: obj["errorMsg"],
									draggable: false,
									closeOnEsc: false,
									shown: function($this){
										fn_import();
									}
								});

								$this.destroy();
								
								$("#loadding").fadeOut(200);
							}
							
						}
					});
					
					$("#form_import").unbind('click');
					$("#form_import").click(function(){
						window.open("../public/form_upload/std_shc.xlsx");
					});
				}
			});
			
			JDbtnt1import = null;
		},
		beforeSend: function(){ if(JDbtnt1import !== null){ JDbtnt1import.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function fn_import(){
	var JDmp_save = null;
	$("#mp_save").click(function(){
		$data = new Array();
		$('.mp_stdshc').each(function(){
			in_data = new Object();
			in_data.model  = $(this).attr('MODEL');
			in_data.baab   = $(this).attr('BAAB');
			in_data.year   = $(this).attr('YEAR');
			in_data.gcode  = $(this).attr('GCODE');
			in_data.nprice = $(this).attr('NPRICE');
			in_data.oprice = $(this).attr('OPRICE');
			in_data.color  = $(this).attr('COLOR');
			in_data.locat  = $(this).attr('LOCAT');
			
			$data.push(in_data);
		});
		
		dataToPost = new Object();
		dataToPost.data = ($data.length > 0 ? $data:"");
		
		$('loadding').fadeIn(200);
		JDmp_save = $.ajax({
			url:'../SYS04/StandardSHC/import_save',
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
						msg: data["errorMsg"]
					});
				}else{
					Lobibox.window({
						title: 'นำเข้าสแตนดาร์ดรถมือสอง',
						width: $(window).width() - 100,
						height: $(window).height() - 100,
						content: data.html,
						draggable: true,
						closeOnEsc: false,
						shown: function($this){
							fn_datatables('mp_result',2,400);
						}
					});
				}
				
				$('loadding').fadeOut(200);
			},
			beforeSend: function(){
				if(JDbtnt1search !== null){
					JDbtnt1search.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }	
		});
	});
}


var JDbtnt1createStd = null;
$("#btnt1createStd").click(function(){
	data = new Object();
	data.event = "add";
	form_operation(data);
});

function form_operation(dataToPost){
	$event = dataToPost.event;
	$('#loadding').fadeIn(200);
	JDbtnt1createStd = $.ajax({
		url:'../SYS04/StandardSHC/loadform',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){ 
			Lobibox.window({
				title: 'กำหนด Standard รถมือสอง',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//fnload($this);
					$('#shc_model').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getMODEL',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.now = (typeof $('#shc_model').find(':selected').val() === 'undefined' ? "" : $('#shc_model').find(':selected').val());
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
						allowClear: true,
						multiple: false,
						dropdownParent: $('#shc_model').parent().parent(),
						//disabled: true,
						//theme: 'classic',
						width: '100%'
					});
					
					$('#shc_baab').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getBAAB',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.now = (typeof $('#shc_baab').find(':selected').val() === 'undefined' ? "" : $('#shc_baab').find(':selected').val());
								dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
								dataToPost.TYPECOD = "HONDA";
								dataToPost.MODEL = (typeof $('#shc_model').find(':selected').val() === 'undefined' ? "" : $('#shc_model').find(':selected').val());
								
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
						dropdownParent: $('#shc_baab').parent().parent(),
						//disabled: true,
						//theme: 'classic',
						width: '100%'
					});
					
					$('#shc_baab').on("select2:select",function(){
						dataToPost = new Object();
						dataToPost.now 	 = '';
						dataToPost.q 	 = '';
						dataToPost.MODEL = (typeof $('#shc_model').find(':selected').val() === 'undefined' ? "" : $('#shc_model').find(':selected').val());
						dataToPost.BAAB  = (typeof $('#shc_baab').find(':selected').val() === 'undefined' ? "" : $('#shc_baab').find(':selected').val());
						
						$.ajax({
							url:'../Cselect2/getJDCOLOR',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){ 
								let size = data.length;
								$("#shc_color").empty();
								for($i=0;$i<size;$i++){
									$("#shc_color").append('<option value="'+data[$i]["id"]+'">'+data[$i]["text"]+'</option>');
								}
								
								let h = $(window).height() - 400;
								$("#shc_color").bootstrapDualListbox('refresh', true);
							}
						});
					});
					
					
					$('#shc_gcode').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getGCode',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.now = (typeof $('#shc_baab').find(':selected').val() === 'undefined' ? "" : $('#shc_baab').find(':selected').val());
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
						dropdownParent: $('#shc_baab').parent().parent(),
						//disabled: true,
						//theme: 'classic',
						width: '100%'
					});
					
					let h = $(window).height() - 400;
					$("#shc_locat").bootstrapDualListbox({
						bootstrap2Compatible: false,
						filterTextClear: 'แสดงทั้งหมด',
						filterPlaceHolder: 'ค้นหา',
						moveSelectedLabel: 'เลือก',
						moveAllLabel: 'เลือกทั้งหมด',
						removeSelectedLabel: 'นำออก',
						removeAllLabel: 'นำออกทั้งหมด',
						moveOnSelect: false,                                                                 
						preserveSelectionOnMove: true,                                                     
						selectorMinimalHeight: h,
						showFilterInputs: true,                                                             
						nonSelectedFilter: '',                                                              
						selectedFilter: '',                                                                 
						infoText: 'แสดงทั้งหมด {0}',                                                        
						infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
						infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
						filterOnValues: false                                                               
					});
					
					$("#shc_color").bootstrapDualListbox({
						bootstrap2Compatible: false,
						filterTextClear: 'แสดงทั้งหมด',
						filterPlaceHolder: 'ค้นหา',
						moveSelectedLabel: 'เลือก',
						moveAllLabel: 'เลือกทั้งหมด',
						removeSelectedLabel: 'นำออก',
						removeAllLabel: 'นำออกทั้งหมด',
						moveOnSelect: false,                                                                 
						preserveSelectionOnMove: true,                                                     
						selectorMinimalHeight: h,
						showFilterInputs: true,                                                             
						nonSelectedFilter: '',                                                              
						selectedFilter: '',                                                                 
						infoText: 'แสดงทั้งหมด {0}',                                                        
						infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
						infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
						filterOnValues: false                                                               
					});
					
					
					// มีสิทธิ์แก้ไขหรือไม่
					if($event == "edit"){
						if(_update == "T"){	 
							$('#shc_model').attr('disabled',true);
							$('#shc_baab').attr('disabled',true);
							
							$(".bootstrap-duallistbox-container").find("*").prop("disabled",false);
							$('#shc_manuyr').attr('disabled',true).trigger('change');
							$('#shc_gcode').attr('disabled',true).trigger('change');
							$('#shc_nprice').attr('disabled',false);
							$('#shc_oprice').attr('disabled',false);
							
							$('#btn_save').attr('disabled',false);
						}else{
							$('#shc_model').attr('disabled',true);
							$('#shc_baab').attr('disabled',true);
							$(".bootstrap-duallistbox-container").find("*").prop("disabled",true);
							$('#shc_manuyr').attr('disabled',true).trigger('change');
							$('#shc_gcode').attr('disabled',true).trigger('change');
							$('#shc_nprice').attr('disabled',true);
							$('#shc_oprice').attr('disabled',true);
							
							$('#btn_save').attr('disabled',true);
						}
					}
					
					var jdbtn_save=null;
					$('#btn_save').unbind('click');
					$('#btn_save').click(function(){
						$btn_save = $(this);
						Lobibox.confirm({
							title: 'ยืนยันการทำรายการ',
							iconClass: false,
							msg: "คุณต้องการบันทึกการกำหนดราคารถมือสองหรือไม่ ?",
							buttons: {
								ok : {
									'class': 'btn btn-primary',
									text: 'ยืนยัน ,บันทึกข้อมูล',
									closeOnClick: true,
								},
								cancel : {
									'class': 'btn btn-danger',
									text: 'ยกเลิก ,ยังไม่บันทึกก่อน',
									closeOnClick: true
								},
							},
							callback: function(lobibox, type){
								var btnType;
								if (type === 'ok'){
									dataToPost2 = new Object();
									dataToPost2.stdid 	= $btn_save.attr('stdid');
									dataToPost2.model 	= (typeof $('#shc_model').find(':selected').val() === 'undefined' ? '':$('#shc_model').find(':selected').val());
									dataToPost2.baab  	= (typeof $('#shc_baab').find(':selected').val() === 'undefined' ? '':$('#shc_baab').find(':selected').val());
									dataToPost2.color 	= $('#shc_color').val();
									dataToPost2.locat 	= $('#shc_locat').val();
									dataToPost2.manuyr 	= $('#shc_manuyr').val();
									dataToPost2.gcode 	= $('#shc_gcode').val();
									dataToPost2.nprice 	= $('#shc_nprice').val();
									dataToPost2.oprice	= $('#shc_oprice').val();
									dataToPost2.event 	= $event;
									
									jdbtn_save = $.ajax({
										url:'../SYS04/StandardSHC/SHC_save',
										data: dataToPost2,
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
													msg: data["errorMsg"]
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
													msg: data["errorMsg"]
												});
												
												$this.destroy();
											}
										},
										beforeSend: function(){
											if(JDbtnt1search !== null){
												JDbtnt1search.abort();
											}
										},
										error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
									});
								}
							}
						});
					});
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
			
			JDbtnt1createStd = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(JDbtnt1createStd !== null){
				JDbtnt1createStd.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
	
}






























