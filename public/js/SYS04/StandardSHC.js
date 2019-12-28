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
	dataToPost.name 	= $("#SNAME").val();
	dataToPost.model 	= $("#SMODEL").val();
	dataToPost.baab 	= $("#SBAAB").val();
	dataToPost.color 	= $("#SCOLOR").val();
	dataToPost.events 	= $("#SEVENTS").val();
	dataToPost.evente 	= $("#SEVENTE").val();
	dataToPost.acticod 	= $("#SACTICOD").val();
	dataToPost.locat 	= $("#Search_LOCAT").val();
	dataToPost.stat 	= $("input[name='s_std_stat']:checked").val();
	
	$('#loadding').show();
	JDbtnt1search = $.ajax({
		url:'../SYS04/Standard/search',
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
					$("#table-fixed-std-detail").hide();
					
					
					$(".JDtooltip").attr({
						'data-toggle':'tooltip',
						'data-placement':'right',
						'data-html':'false',
						'data-original-title':'tooltip',
					});
					$('[data-toggle="tooltip"]').tooltip();
					
					redraw();
					function redraw(){
						var JDstddetail = null;
						$(".editstd").unbind('click');
						$(".editstd").click(function(){
							dataToPost = new Object();
							dataToPost.stdid 	= ($(this).attr('stdid'));
							dataToPost.subid 	= ($(this).attr('subid'));
							dataToPost.event	= "edit";
							
							form_operation(dataToPost);
						});
					}
					
					
					$("#excelstd").click(function(){	
						var d = new Date();
						tableToExcel_Export(data.excel,"ข้อมูล std","Standard_"+(d.getTime())); 
						//tableToExcel_Export(data.html,"ข้อมูล std","Standard_"+(d.getTime())); 
					});
					
					
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




var JDbtnt1createStd = null;
$("#btnt1createStd").click(function(){
	data = new Object();
	data.event = "add";
	form_operation(data);
});

function form_operation(dataToPost){
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
					let h = $(window).height() - 400;
					$("#LOCAT").bootstrapDualListbox({
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
					
					$("#COLOR").bootstrapDualListbox({
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
					
					
					if(dataToPost.event == "add"){
						$('#FMODEL').attr('disabled',false).trigger('change');
						$('#FSTAT').attr('disabled',false).trigger('change');
					}else{
						// มีสิทธิ์แก้ไขหรือไม่
						if(_update == "T"){	 
							$('#FEVENTS').attr('disabled',false);
							$('#FEVENTE').attr('disabled',false);
							$('#FEVENTNAME').attr('disabled',false);
							$('#FDETAIL').attr('disabled',false);
							$(".bootstrap-duallistbox-container").find("*").prop("disabled",false);
							$('#FMODEL').attr('disabled',true).trigger('change');
							$('#FSTAT').attr('disabled',true).trigger('change');
							$('#btnAddPSTD').attr('disabled',false);
							$('.btn_car_old_delete').attr('disabled',false);
							$('#btnAddDwn').attr('disabled',false);
							$('.editDwn').attr('disabled',false);
							$('.deleteDwn').attr('disabled',false);
							$('#btnAddFree').attr('disabled',false);
							$('.editFree').attr('disabled',false);
							$('.deleteFree').attr('disabled',false);
							
							$('#btnSave').attr('disabled',false);
						}else{
							$('#FEVENTS').attr('disabled',true);
							$('#FEVENTE').attr('disabled',true);
							$('#FEVENTNAME').attr('disabled',true);
							$('#FDETAIL').attr('disabled',true);
							$(".bootstrap-duallistbox-container").find("*").prop("disabled",true);
							$('#FMODEL').attr('disabled',true).trigger('change');
							$('#FSTAT').attr('disabled',true).trigger('change');
							$('#btnAddPSTD').attr('disabled',true);
							$('.btn_car_old_delete').attr('disabled',true);
							$('#btnAddDwn').attr('disabled',true);
							$('.editDwn').attr('disabled',true);
							$('.deleteDwn').attr('disabled',true);
							$('#btnAddFree').attr('disabled',true);
							$('.editFree').attr('disabled',true);
							$('.deleteFree').attr('disabled',true);
							
							$('#btnSave').attr('disabled',true);
						}
					}
					
					/*
					fn_delete_price();
					activeDatatables();
					activeDatatablesFree();
					*/
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





























