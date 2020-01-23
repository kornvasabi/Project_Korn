/********************************************************
             ______@07/12/2019______
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
	$(".select2-selection--multiple,.select2-search--inline").css({"z-index":100}); //กำหนด z-index ช่องค้นหาของ select2
	
	//alert(_insert);
	if(_insert == "T"){ // สิทธิ์เพิ่มข้อมูล
		$("#btnt1createStd").attr("disabled",false);
	}else{
		$("#btnt1createStd").attr("disabled",true);
	}
	
	$("#SACTICOD").selectpicker();
	$("#Search_LOCAT").selectpicker();
});

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


var JDedit = null;
function edit($btn){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		closeOnEsc: false,
		closeButton: false,
		msg: "คุณต้องการแก้ไข Standard หรือไม่",
		buttons: {
			ok : {
				'class': 'btn btn-primary glyphicon glyphicon-ok',
				text: ' ยืนยัน, แก้ไข',
				closeOnClick: false,
			},
			cancel : {
				'class': 'btn btn-danger glyphicon glyphicon-remove',
				text: ' ยกเลิก, ยังไม่แก้ก่อน',
				closeOnClick: true
			},
		},
		callback: function(lobibox, type){
			if (type === 'ok'){
				//lobibox.destroy();
				dataToPost = new Object();
				dataToPost.stdid 		= $btn.attr("stdid");
				dataToPost.plrank 		= $btn.attr("plrank");
				dataToPost.evente 		= $("#FEVENTE").val();
				dataToPost.eventname 	= $("#FEVENTNAME").val();
				dataToPost.eventdetail 	= $("#FDETAIL").val();
				dataToPost.price1 		= $("#FPRICE").val();
				dataToPost.price2 		= $("#FPRICE2").val();
				
				$('#loadding').show();
				JDedit =  $.ajax({
					url:'../SYS04/Standard/EditSTD',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data){
						if(data.error){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}else{
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
						
						$('#loadding').hide();
						JDedit = null;
						lobibox.destroy();
					},
					beforeSend: function(){
						if(JDedit !== null){
							JDedit.abort();
						}
					},
					error: function (x,c,b){
						Lobibox.notify('error', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: false,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: x.status +' '+ b
						});
						$('#loadding').hide();
						JDedit = null;
						lobibox.destroy();
					}			
				});
			}
		}
	});
}


var JDbtnt1createStd = null;
$("#btnt1createStd").click(function(){
	data = new Object();
	data.event = "add";
	form_operation(data);
});

function form_operation(dataToPost){
	$('#loadding').fadeIn(200);
	JDbtnt1createStd = $.ajax({
		url:'../SYS04/Standard/loadform',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){ 
			Lobibox.window({
				title: 'กำหนด Standard',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					fnload($this);
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
					
					fn_delete_price();
					activeDatatables();
					activeDatatablesFree();
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

function fnload($thisForm){
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js',
	], initPage);
	
	function initPage(){
		$('#wizard-std').bootstrapWizard({
			onTabClick: function(li, ul, ind, ind2, xxx){
				var beforeChanged = 0; 
				var index = 0; //tab ก่อนเปลี่ยน 
				$('.wizard-tabs li').each(function(){
					//ลบ wizard ที่ active อยู่ทั้งหมด
					if($(this).hasClass('active')){
						index = beforeChanged;
					}
					
					beforeChanged = beforeChanged + 1;
				});
				
				switch(index){
					case 0: //tab1
						nextTab(ind2); 
						break;
					case 1: //tab2						
						nextTab(ind2); 
						break;
					case 2: //tab3
						nextTab(ind2); 
						break;
					case 3: //tab4
						nextTab(ind2); 
						break;
					case 4: //tab5
						nextTab(ind2); 
						break;
				}
			}
		});
	}
	
	function nextTab(ind2){
		$('.wizard-tabs li').each(function(){
			//ลบ wizard ที่ active อยู่ทั้งหมด
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(ind2).hasClass('active')){
			// active tab ถัดไป
			$('.wizard-tabs li').eq(ind2).addClass('active');
		}
		
		var $id = $('.wizard-tabs li').eq(ind2).find('a').attr('href').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');	
		
		return true;					
	}
		
	$("#FLOCAT").bootstrapDualListbox({
		bootstrap2Compatible: false,
		filterTextClear: 'แสดงทั้งหมด',
		filterPlaceHolder: 'ค้นหา',
		moveSelectedLabel: 'เลือก',
		moveAllLabel: 'เลือกทั้งหมด',
		removeSelectedLabel: 'นำออก',
		removeAllLabel: 'นำออกทั้งหมด',
		moveOnSelect: false,                                                                 
		preserveSelectionOnMove: true,                                                     
		selectorMinimalHeight: 300,
		showFilterInputs: true,                                                             
		nonSelectedFilter: '',                                                              
		selectedFilter: '',                                                                 
		infoText: 'แสดงทั้งหมด {0}',                                                        
		infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
		infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
		filterOnValues: false                                                               
	});
	
	var FBAAB = $("#FBAAB").bootstrapDualListbox({
		bootstrap2Compatible: false,
		filterTextClear: 'แสดงทั้งหมด',
		filterPlaceHolder: 'ค้นหา',
		moveSelectedLabel: 'เลือก',
		moveAllLabel: 'เลือกทั้งหมด',
		removeSelectedLabel: 'นำออก',
		removeAllLabel: 'นำออกทั้งหมด',
		moveOnSelect: false,                                                                 
		preserveSelectionOnMove: true,                                                     
		selectorMinimalHeight: 200,
		showFilterInputs: true,                                                             
		nonSelectedFilter: '',                                                              
		selectedFilter: '',                                                                 
		infoText: 'แสดงทั้งหมด {0}',                                                        
		infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
		infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
		filterOnValues: false                                                               
	});
	
	var FCOLOR = $("#FCOLOR").bootstrapDualListbox({
		bootstrap2Compatible: false,
		filterTextClear: 'แสดงทั้งหมด',
		filterPlaceHolder: 'ค้นหา',
		moveSelectedLabel: 'เลือก',
		moveAllLabel: 'เลือกทั้งหมด',
		removeSelectedLabel: 'นำออก',
		removeAllLabel: 'นำออกทั้งหมด',
		moveOnSelect: false,                                                                 
		preserveSelectionOnMove: true,                                                     
		selectorMinimalHeight: 200,
		showFilterInputs: true,                                                             
		nonSelectedFilter: '',                                                              
		selectedFilter: '',                                                                 
		infoText: 'แสดงทั้งหมด {0}',                                                        
		infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
		infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
		filterOnValues: false                                                               
	});
	
	var FACTI = $("#FACTI").bootstrapDualListbox({
		bootstrap2Compatible: false,
		filterTextClear: 'แสดงทั้งหมด',
		filterPlaceHolder: 'ค้นหา',
		moveSelectedLabel: 'เลือก',
		moveAllLabel: 'เลือกทั้งหมด',
		removeSelectedLabel: 'นำออก',
		removeAllLabel: 'นำออกทั้งหมด',
		moveOnSelect: false,                                                                 
		preserveSelectionOnMove: true,                                                     
		selectorMinimalHeight: 200,
		showFilterInputs: true,                                                             
		nonSelectedFilter: '',                                                              
		selectedFilter: '',                                                                 
		infoText: 'แสดงทั้งหมด {0}',                                                        
		infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
		infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
		filterOnValues: false                                                               
	});
	
	//$(".bootstrap-duallistbox-container select").css({'max-height':'200px'});
	$(".bootstrap-duallistbox-container").find("*").prop("disabled",false);
		
	$(".bootstrap-duallistbox-container .move ,.moveall ,.remove ,.removeall").attr({
		'data-toggle':'tooltip',
		'data-placement':'top',
		'data-html':'true',
		'data-original-title':'tooltip',
	});
	$('[data-toggle="tooltip"]').tooltip();
	
	$('#FMODEL').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#FMODEL').find(':selected').val() === 'undefined' ? "" : $('#FMODEL').find(':selected').val());
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
		dropdownParent: $('#FMODEL').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#FSTAT').select2({
		placeholder: 'เลือก',
		dropdownParent: $('#FMODEL').parent().parent(), 
		minimumResultsForSearch: -1,
		width: '100%'
	});
	
	$('#FSTAT').on('select2:select',function(){
		if($(this).val() == 'N'){
			$('#tb_car_old thead').empty();
			$('#tb_car_old thead').append('<tr><th>ราคาสด</th><th>ราคาผลัด</th><th>#</th></tr>');
			$('.FPRICEN').show();
			$('.FPRICEO').hide();
		}else{
			$('#tb_car_old thead').empty();
			$('#tb_car_old thead').append('<tr><th>ช่วงราคารถ จาก</th><th>ช่วงราคารถ ถึง</th><th>#</th></tr>');
			$('.FPRICEN').hide();
			$('.FPRICEO').show();
		}
		
		$('#tb_car_old tbody').empty();
	});
	
	var jdbtnAddPSTD=null;
	$('#btnAddPSTD').click(function(){
		dataToPost = new Object();
		dataToPost.FSTAT  = (typeof $('#FSTAT').find(':selected').val() === "undefined" ? "":$('#FSTAT').find(':selected').val());
		dataToPost.FPRICE = $('#F_OLD_PRICE').val();
		dataToPost.TPRICE = $('#F_OLD_PRICE2').val();
		
		NPRICE = new Array();
		$('.btn_car_old_delete').each(function(){
			data = new Object();
			data.FPRICE = $(this).attr('FPRICE');
			data.TPRICE = $(this).attr('TPRICE');
			
			NPRICE.push(data);
		});
		
		dataToPost.NPRICE = (NPRICE.length == 0 ? "":NPRICE);
		
		$('#loadding').fadeIn(200);
		jdbtnAddPSTD = $.ajax({
			url: '../SYS04/Standard/setRankPRICE',
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
					$('#F_OLD_PRICE').val('');	
					$('#F_OLD_PRICE').focus();
					$('#F_OLD_PRICE2').val('');	
					$('#tb_car_old tbody').empty();
					$('#tb_car_old tbody').append(data.html);
					fn_delete_price();
				}
				
				jdbtnAddPSTD = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(jdbtnAddPSTD !== null){ jdbtnAddPSTD.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#FMODEL').on('select2:select',function(){
		dataToPost = new Object();
		dataToPost.now 		= "";
		dataToPost.q 		= "";
		dataToPost.TYPECOD 	= "HONDA";
		dataToPost.MODEL 	= (typeof $('#FMODEL').find(':selected').val() === 'undefined' ? "" : $('#FMODEL').find(':selected').val());
		dataToPost.BAAB 	= "";
		dataToPost.NOTB 	= "YES";
		$.ajax({
			url: '../Cselect2/getBAAB',
			data: dataToPost,
			tyle: 'POST',
			dataType: 'json',
			success: function(data){
				let size = data.length;
				for(let i=0;i<size;i++){
					if(i==0){ FBAAB.empty(); }
					FBAAB.append('<option value="'+data[i].id+'">'+data[i].text+'</option>');
				}				
				FBAAB.bootstrapDualListbox('refresh', true);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
		
		$.ajax({
			url: '../Cselect2/getJDCOLOR',
			data: dataToPost,
			tyle: 'POST',
			dataType: 'json',
			success: function(data){
				let size = data.length;
				for(let i=0;i<size;i++){
					if(i==0){ FCOLOR.empty(); }
					FCOLOR.append('<option value="'+data[i].id+'">'+data[i].text+'</option>');
				}				
				FCOLOR.bootstrapDualListbox('refresh', true);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
		
		$.ajax({
			url:'../SYS04/Standard/getSTDID',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#STDID').val(data);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
		
	});
	
	FBAAB.change(function(){
		dataToPost = new Object();
		dataToPost.now 		= "";
		dataToPost.q 		= "";
		dataToPost.TYPECOD 	= "HONDA";
		dataToPost.MODEL 	= (typeof $('#FMODEL').find(':selected').val() === 'undefined' ? "" : $('#FMODEL').find(':selected').val());
		dataToPost.BAAB 	= $(this).val();
		dataToPost.NOTB 	= "YES";
		
		$.ajax({
			url: '../Cselect2/getJDCOLOR',
			data: dataToPost,
			tyle: 'POST',
			dataType: 'json',
			success: function(data){
				let size = data.length;
				for(let i=0;i<size;i++){
					if(i==0){ FCOLOR.empty(); }
					FCOLOR.append('<option value="'+data[i].id+'">'+data[i].text+'</option>');
				}				
				FCOLOR.bootstrapDualListbox('refresh', true);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	
	//$('#table-stdfa').on('draw.dt',function(){ redraw(); });
	//fn_datatables('table-stdfa',2,"300px","YES");	
	//$('#table-stdfa').DataTable({ "ajax": "../SYS04/Standard/datatablesArr" });
	//fn_datatables('table-stdfree',2,"300px","YES");

	/*
	document.getElementById("table-fixed-stdfa").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});	
	
	document.getElementById("table-fixed-stdfree").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});
	*/	
	
	$("#btnAddDwn").click(function(){
		loadAddDwn(null); 	// เพิ่มข้อมูล  Standard การดาวน์รถ  ใหม่
	});
		
	$("#btnAddFree").click(function(){
		loadAddFree(null); 	// เพิ่มข้อมูล  Standard การดาวน์รถ  ใหม่
	});
	
	$("#btnSave").click(function(){
		dataToPost = new Object();
		
		dataToPost.STDID  	 = $("#STDID").val();
		dataToPost.SUBID  	 = $("#SUBID").val();
		dataToPost.EVENTS 	 = $("#FEVENTS").val();
		dataToPost.EVENTE 	 = $("#FEVENTE").val();
		dataToPost.EVENTNAME = $("#FEVENTNAME").val();
		dataToPost.DETAIL 	 = $("#FDETAIL").val();
		dataToPost.ACTI  	 = $("#FACTI").val();
		dataToPost.MODEL 	 = (typeof $("#FMODEL").find(":selected").val() === 'undefined' ? "":$("#FMODEL").find(":selected").val());
		dataToPost.BAAB  	 = $("#FBAAB").val();
		dataToPost.COLOR 	 = $("#FCOLOR").val();
		dataToPost.STAT 	 = $("#FSTAT").val();
		
		$price = [];
		$(".btn_car_old_delete").each(function(){
			row = new Object();
			row.fprice		= $(this).attr('fprice');
			row.tprice 		= $(this).attr('tprice');
			
			$price.push(row);
		});
		
		dataToPost.PRICE 	 = ($price.length == 0 ? "":$price);
		dataToPost.LOCAT 	 = $("#FLOCAT").val();
		
		$Dwn = [];
		$(".editDwn").each(function(){
			row = new Object();
			row.formpriceFP		= $(this).attr('formpriceFP');
			row.formpriceTP		= $(this).attr('formpriceTP');
			row.formdwns		= $(this).attr('formdwns');
			row.formdwne 		= $(this).attr('formdwne');
			row.forminterest 	= $(this).attr('forminterest');
			row.forminterest2 	= $(this).attr('forminterest2');
			row.forminsurance 	= $(this).attr('forminsurance');
			row.formtrans 		= $(this).attr('formtrans');
			row.formregist 		= $(this).attr('formregist');
			row.formact 		= $(this).attr('formact');
			row.formcoupon 		= $(this).attr('formcoupon');
			row.formapprv 		= $(this).attr('formapprv');
			
			$Dwn.push(row);
		});
		dataToPost.STDDWN 	 = ($Dwn.length == 0 ? "":$Dwn);
		
		$Free = [];
		$(".editFree").each(function(){
			row = new Object();
			row.formpriceFP	= $(this).attr('formpriceFP');
			row.formpriceTP	= $(this).attr('formpriceTP');
			row.formdwns	= $(this).attr('formdwns');
			row.formdwne	= $(this).attr('formdwne');
			row.formtype	= $(this).attr('formtypev');
			row.formnopays	= $(this).attr('formnopays');
			row.formnopaye 	= $(this).attr('formnopaye');
			row.formrate 	= $(this).attr('formrate');
			row.formdetail 	= $(this).attr('formdetail');
			
			$Free.push(row);
		});
		dataToPost.STDFREE 	= ($Free.length == 0 ? "":$Free);
		dataToPost.event 	= $('#btnSave').attr('event');
		
		$('#loadding').show(0);
		JDbtnAddDwn = $.ajax({
			url:'../SYS04/Standard/SaveSTD',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').hide(0);
				
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
					//$thisForm.destroy();					
					Lobibox.notify('success', {
						title: 'สำเร็จ',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}
			},
			beforeSend: function(){
				if(JDbtnAddDwn !== null){
					JDbtnAddDwn.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	var jd_btnUpload = null;
	$('#btnUpload').click(function(){
		$('#btnSave').attr('disabled',true);
		$('#btnUpload').attr('disabled',true);
		$('#btnDownload').attr('disabled',true);
		
		jd_btnUpload = $.ajax({
			url:'../SYS04/Standard/getFormUPLOAD',
			//data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.window({
					title: 'FORM CUSTOMER',
					//width: $(window).width(),
					height: 125,
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($thisFile){
						initupload($thisFile);
					},
					beforeClose: function(){
						$('#btnSave').attr('disabled',false);
						$('#btnUpload').attr('disabled',false);
						$('#btnDownload').attr('disabled',false);
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
	});	
}

function initupload($thisFile){
	$("#fileupload").uploadFile({		
		url:'../SYS04/Standard/getDataINFile',
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
				$('#STDID').val(obj["stdid"]);
				$('#FEVENTNAME').val(obj["eventname"]);
				$('#FDETAIL').val(obj["detail"]);
				var newOption = new Option(obj["model"], obj["model"], true, true);
				$('#FMODEL').empty().append(newOption).trigger('change');
				
				var baab = obj["baab"];
				if(baab != ""){
					baab = baab.split(',');
					for(var i=0;i<baab.length;i++){
						$('#FBAAB option[value="'+baab[i]+'"]').attr('selected','selected');
					}
					$('#FBAAB').bootstrapDualListbox('refresh', true);
				}
				
				var color = obj["color"];
				if(color!= ""){
					color = color.split(',');
					for(var i=0;i<color.length;i++){
						$('#FCOLOR option[value="'+color[i]+'"]').attr('selected','selected');
					}
					$('#FCOLOR').bootstrapDualListbox('refresh', true);
				}
				
				//var newOption = new Option(obj["stat"], obj["stat"], true, true);
				$('#FSTAT').val(obj["stat"]).trigger('change');
				
				var acti = obj["acti"];
				if(acti!= ""){
					acti = acti.split(',');
					for(var i=0;i<acti.length;i++){
						$('#FACTI option[value="'+acti[i]+'"]').attr('selected','selected');
					}
					$('#FACTI').bootstrapDualListbox('refresh', true);
				}
				
				$('#FEVENTS').val(obj["events"]);
				$('#FEVENTE').val(obj["evente"]);
				
				var locat = obj["locat"];
				if(locat != ""){
					locat = locat.split(',');
					for(var i=0;i<locat.length;i++){
						$('#FLOCAT option[value="'+locat[i]+'"]').attr('selected','selected');
					}
					$('#FLOCAT').bootstrapDualListbox('refresh', true);
				}
				
				$('#tb_car_old tbody').empty().append(obj["stdprice"]);
				$('#table-stdfa tbody').empty().append(obj["stddown"]);
				$('#table-stdfree tbody').empty().append(obj["stdfree"]);

				fn_delete_price();
				activeDatatables();
				activeDatatablesFree();
				
				$thisFile.destroy();
			}
			
			$("#loadding").fadeOut(200);
		}
	});
}

// นับขนาดของ object
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

var JDselectPickers = null;	
var JDselectPickers_Cache = null;
$('#SACTICOD').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) { 
	$filter = $("#SACTICOD").parent().find("[aria-label=Search]");
	FN_JD_BSSELECT("SACTICOD",$filter,"getSACTICOD2");
});

$("#SACTICOD").parent().find("[aria-label=Search]").keyup(function(){ 
	FN_JD_BSSELECT("SACTICOD",$(this),"getSACTICOD2");
});

$('#Search_LOCAT').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) { 
	$filter = $("#Search_LOCAT").parent().find("[aria-label=Search]");
	FN_JD_BSSELECT("Search_LOCAT",$filter,"getLOCAT2");
});

$("#Search_LOCAT").parent().find("[aria-label=Search]").keyup(function(){ 
	FN_JD_BSSELECT("Search_LOCAT",$(this),"getLOCAT2");
});

function FN_JD_BSSELECT($id,$thisSelected,$func){
	dataToPost = new Object();
	dataToPost.filter = $thisSelected.val();
	dataToPost.now	  = (typeof $("#"+$id).selectpicker('val') == null ? "":$("#"+$id).selectpicker('val'));
	
	clearTimeout(JDselectPickers);
	JDselectPickers = setTimeout(function(){
		getdata();
	},250);
	
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
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	}
}

function fn_delete_price(){
	$('.btn_car_old_delete').click(function(){
		$this = $(this);
		let hasDwn = 0;
		let hasFre = 0;
		$('.deleteDwn').each(function(){
			if($this.attr('FPRICE') == $(this).attr('FORMPRICEFP') && $this.attr('TPRICE') == $(this).attr('FORMPRICETP')){
				hasDwn++;
			}
		});
		
		$('.deleteFree').each(function(){
			if($this.attr('FPRICE') == $(this).attr('FORMPRICEFP') && $this.attr('TPRICE') == $(this).attr('FORMPRICETP')){
				hasFre++;
			}
		});
		
		if(hasDwn > 0 || hasFre > 0){
			Lobibox.confirm({
				title: 'ยืนยันการทำรายการ',
				iconClass: false,
				msg: "มีข้อมูล <span style='color:blue;'>standard เงินดาวน์</span> หรือ/และ <span style='color:blue;'>standard ของแถม</span> แล้ว <br><span style='color:red;font-size:8pt;'>(*** หมายเหตุ หากยืนยันข้อมูล standard เงินดาวน์ หรือ/และ standard ของแถม จะถูกลบด้วย)</span><br><b>คุณแน่ใจว่าต้องการลบ ?</b>",
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
						$this.parent().parent().remove();
						
						$('.deleteDwn').each(function(){
							$deleteDwn = $(this);
							if($this.attr('FPRICE') == $deleteDwn.attr('FORMPRICEFP') && $this.attr('TPRICE') == $deleteDwn.attr('FORMPRICETP')){
								$deleteDwn.parent().parent().remove();
							}
						});
						
						$('.deleteFree').each(function(){
							$deleteFre = $(this);
							if($this.attr('FPRICE') == $deleteFre.attr('FORMPRICEFP') && $this.attr('TPRICE') == $deleteFre.attr('FORMPRICETP')){
								$deleteFre.parent().parent().remove();
							}
						});
					}
				}
			});
		}else{
			$this.parent().parent().remove();
		}
	});
}

function activeDatatables(){ // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
	// $(".deleteDwn").attr('disabled', true);
	// $(".editDwn").attr('disabled', true);
	$(".deleteDwn").unbind('click');
	$(".deleteDwn").click(function(){
		$deleteDwn = $(this);
		
		let hasFre = 0;
		$('.deleteFree').each(function(){
			if($deleteDwn.attr('formpricefp') == $(this).attr('FORMPRICEFP') 
				&& $deleteDwn.attr('formpricetp') == $(this).attr('FORMPRICETP')
				&& $deleteDwn.attr('formdwns') == $(this).attr('formdwns')
				&& $deleteDwn.attr('formdwne') == $(this).attr('formdwne')
			){
				hasFre++;
			}
		});
		
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: "คุณต้องการลบข้อมูล Standard การดาวน์รถ<br>ในช่วงเงินดาวน์ "+$deleteDwn.attr("formdwns")+" - "+$deleteDwn.attr("formdwne")+" ใช่ป่าววว ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, ลบข้อมูล',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก, ยังไม่ลบก่อน',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					if(hasFre == 0){
						$deleteDwn.parent().parent().remove();
					}else{
						checkDown();
					}
				}
			}
		});
		
		function checkDown(){
			Lobibox.confirm({
				title: 'ยืนยันการทำรายการ',
				iconClass: false,
				closeOnEsc: false,
				closeButton: false,
				msg: "มีข้อมูล <span style='color:blue;'>standard ของแถม</span><br>ในช่วงเงินดาวน์ "+$deleteDwn.attr("formdwns")+" - "+$deleteDwn.attr("formdwne")+" แล้ว <br><span style='color:red;font-size:8pt;'>(*** หมายเหตุ หากยืนยันข้อมูล standard ของแถม จะถูกลบด้วย)</span><br><b style='color:red;'>คุณแน่ใจว่าต้องการลบ ?</b>",
				buttons: {
					ok : {
						'class': 'btn btn-primary glyphicon glyphicon-ok',
						text: ' ยืนยัน, ลบข้อมูล',
						closeOnClick: true,
					},
					cancel : {
						'class': 'btn btn-danger glyphicon glyphicon-remove',
						text: ' ยกเลิก, ยังไม่ลบก่อน',
						closeOnClick: true
					},
				},
				callback: function(lobibox, type){
					if (type === 'ok'){
						$('.deleteFree').each(function(){
							$deleteFre = $(this);
							if($deleteDwn.attr('formpricefp') == $(this).attr('FORMPRICEFP') 
								&& $deleteDwn.attr('formpricetp') == $(this).attr('FORMPRICETP')
								&& $deleteDwn.attr('formdwns') == $(this).attr('formdwns')
								&& $deleteDwn.attr('formdwne') == $(this).attr('formdwne')
							){
								$deleteFre.parent().parent().remove();
							}
						});
						
						$deleteDwn.parent().parent().remove();
					}
				}
			});
		}
	});
	
	$('.editDwn').unbind('click');
	$('.editDwn').click(function(){ 
		$this = $(this);
		$(this).parent().parent().remove();
		setTimeout(function(){ loadAddDwn($this); },250);
	});
}

function activeDatatablesFree($stdfree){ // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
	// $(".deleteFree").attr('disabled', true);
	// $(".editFree").attr('disabled', true);

	$(".deleteFree").unbind('click');
	$(".deleteFree").click(function(){
		$deleteFree = $(this);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: "คุณต้องการลบข้อมูล  Standard ของแถม <br>ในช่วงเงินดาวน์ "+$deleteFree.attr("formnopays")+" - "+$deleteFree.attr("formnopaye")+" ใช่ป่าววว ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, ลบข้อมูล',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก, ยังไม่ลบก่อน',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					$deleteFree.parent().parent().remove();
				}
			}
		});
	});
	
	$(".deleteFree").unbind('click');
	$(".deleteFree").click(function(){
		$deleteFree = $(this);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: "คุณต้องการลบข้อมูล Standard ของแถม<br>ในงวดที่ "+$deleteFree.attr("formnopays")+" - "+$deleteFree.attr("formnopaye")+" ใช่ป่าววว ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, ลบข้อมูล',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก, ยังไม่ลบก่อน',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					$deleteFree.parent().parent().remove();
				}
			}
		});
	});
	
	$('.editFree').unbind('click');
	$('.editFree').click(function(){ 
		$this = $(this);
		$(this).parent().parent().remove();
		setTimeout(function(){ loadAddFree($this); },250);
	});
}

var JDbtnAddDwn = null;	
function loadAddDwn($edit){	
	dataToPost = new Object();
	dataToPost.formpriceFP 	 = ($edit === null ? "":$edit.attr('formpriceFP'));
	dataToPost.formpriceTP 	 = ($edit === null ? "":$edit.attr('formpriceTP'));
	dataToPost.formdwns 	 = ($edit === null ? "0":$edit.attr('formdwns'));
	dataToPost.formdwne 	 = ($edit === null ? "5000":$edit.attr('formdwne'));
	dataToPost.forminterest  = ($edit === null ? "1.6":$edit.attr('forminterest'));
	dataToPost.forminterest2 = ($edit === null ? "1.5":$edit.attr('forminterest2'));
	dataToPost.forminsurance = ($edit === null ? "2300":$edit.attr('forminsurance'));
	dataToPost.formtrans 	 = ($edit === null ? "750":$edit.attr('formtrans'));
	dataToPost.formregist  	 = ($edit === null ? "550":$edit.attr('formregist'));
	dataToPost.formact 		 = ($edit === null ? "800":$edit.attr('formact'));
	dataToPost.formcoupon 	 = ($edit === null ? "500":$edit.attr('formcoupon'));
	dataToPost.formapprv 	 = ($edit === null ? "N":$edit.attr('formapprv'));
	dataToPost.formevent 	 = ($edit === null ? "add":"edit");
	dataToPost.fstat	 	 = (typeof $('#FSTAT').find(':selected').val() === 'undefined' ? '' : $('#FSTAT').find(':selected').val());
	
	editDwn = new Array();
	$('.editDwn').each(function(){
		data = new Object();
		data.formpriceFP 	= $(this).attr('formpriceFP');
		data.formpriceTP 	= $(this).attr('formpriceTP');
		data.formdwns 		= $(this).attr('formdwns');
		data.formdwne 		= $(this).attr('formdwne');
		data.forminterest 	= $(this).attr('forminterest');
		data.forminterest2 	= $(this).attr('forminterest2');
		data.forminsurance 	= $(this).attr('forminsurance');
		data.formtrans 		= $(this).attr('formtrans');
		data.formregist 	= $(this).attr('formregist');
		data.formact 		= $(this).attr('formact');
		data.formcoupon 	= $(this).attr('formcoupon');
		data.formapprv 		= $(this).attr('formapprv');
		
		editDwn.push(data);
	});
	
	dataToPost.editDwn = (editDwn.length == 0 ? "":editDwn);
	
	data = new Array();
	$('.btn_car_old_delete').each(function(){
		data_r = new Object();
		data_r.FPRICE = $(this).attr('FPRICE');
		data_r.TPRICE = $(this).attr('TPRICE');
		
		data.push(data_r);
	});
	dataToPost.price = (data.length == 0 ? "":data);
	
	$('#btnAddDwn').attr('disabled',true);
	$('#btnAddFree').attr('disabled',true);
	$(".deleteDwn").attr('disabled',true);
	$(".editDwn").attr('disabled',true);
	$(".deleteFree").attr('disabled',true);
	$(".editFree").attr('disabled',true);
	
	$('#loadding').fadeIn(200);
	JDbtnAddDwn = $.ajax({
		url:'../SYS04/Standard/JDFormStdDWN',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'Standard การดาวน์รถ',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: true,
				closeOnEsc: false,
				shown: function($windowSTD){	
					var jdbtnSWNADD = null;
					$("#btnSWNADD").unbind('click');
					$("#btnSWNADD").click(function(){
						dataToPost = new Object();
						dataToPost.formprice 		= $("#formprice").find(':selected').text();
						dataToPost.formpriceFP 		= $("#formprice").find('option:selected').attr("FPRICE");
						dataToPost.formpriceTP 		= $("#formprice").find('option:selected').attr("TPRICE");
						dataToPost.formdwns 		= $("#formdwns").val();
						dataToPost.formdwne 		= $("#formdwne").val();
						dataToPost.forminterest 	= $("#forminterest").val();
						dataToPost.forminterest2 	= $("#forminterest2").val();
						dataToPost.forminsurance 	= $("#forminsurance").val();
						dataToPost.formtrans 		= $("#formtrans").val();
						dataToPost.formregist 		= $("#formregist").val();
						dataToPost.formact 			= $("#formact").val();
						dataToPost.formcoupon 		= $("#formcoupon").val();
						dataToPost.formapprv 		= ($("#formapprv").is(":checked") ? "Y":"N");
						dataToPost.action 			= "add";
						
						editDwn = new Array();
						$('.editDwn').each(function(){
							data = new Object();
							data.formpriceFP 	= $(this).attr('formpriceFP');
							data.formpriceTP 	= $(this).attr('formpriceTP');
							data.formdwns 		= $(this).attr('formdwns');
							data.formdwne 		= $(this).attr('formdwne');
							data.forminterest 	= $(this).attr('forminterest');
							data.forminterest2 	= $(this).attr('forminterest2');
							data.forminsurance 	= $(this).attr('forminsurance');
							data.formtrans 		= $(this).attr('formtrans');
							data.formregist 	= $(this).attr('formregist');
							data.formact 		= $(this).attr('formact');
							data.formcoupon 	= $(this).attr('formcoupon');
							data.formapprv 		= $(this).attr('formapprv');
							
							editDwn.push(data);
						});
						
						dataToPost.editDwn = (editDwn.length == 0 ? "":editDwn);
						
						$('#loadding').fadeIn(200);
						jdbtnSWNADD = $.ajax({
							url:'../SYS04/Standard/getStdDWN',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							beforeSend: function(){ 
								if(jdbtnSWNADD !== null){ jdbtnSWNADD.abort(); } 
							},
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
									$('#table-stdfa tbody').empty().append(data.html);
									$edit = null;
									activeDatatables();
									$windowSTD.destroy();
								}
								
								jdbtnSWNADD = null;
								$('#loadding').fadeOut(200);
							},
							error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
						});
					});
				},
				beforeClose : function(){
					if($edit !== null){
						dataToPost.action = "cancel edit";	
						$('#loadding').fadeIn(200);
						
						$.ajax({
							url:'../SYS04/Standard/getStdDWN',
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
									$('#table-stdfa tbody').empty().append(data.html);
									activeDatatables();
								}
								
								$('#loadding').fadeOut(200);
							},
							error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
						});
					}
					
					$('#btnAddDwn').attr('disabled',false);
					$('#btnAddFree').attr('disabled',false);
					$(".deleteDwn").attr('disabled',false);
					$(".editDwn").attr('disabled',false);
					$(".deleteFree").attr('disabled',false);
					$(".editFree").attr('disabled',false);
					
					$edit = null;
				}
			});
			
			JDbtnAddDwn = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){ if(JDbtnAddDwn !== null){ JDbtnAddDwn.abort(); } },
		error: function(jqXHR, exception){ 
			fnAjaxERROR(jqXHR,exception); 
			
			$('#btnAddDwn').attr('disabled',false);
			$('#btnAddFree').attr('disabled',false);
			$(".deleteDwn").attr('disabled',false);
			$(".editDwn").attr('disabled',false);
			$(".deleteFree").attr('disabled',false);
			$(".editFree").attr('disabled',false);
		}
	});
}

function loadAddFree($edit){
	dataToPost = new Object();
	dataToPost.formpriceFP 	= ($edit === null ? "":$edit.attr('formpriceFP'));
	dataToPost.formpriceTP 	= ($edit === null ? "":$edit.attr('formpriceTP'));
	dataToPost.formdwns 	= ($edit === null ? "":$edit.attr('formdwns'));
	dataToPost.formdwne 	= ($edit === null ? "":$edit.attr('formdwne'));
	dataToPost.formtypeT 	= ($edit === null ? "":$edit.attr('formtypeT'));
	dataToPost.formtypeV 	= ($edit === null ? "":$edit.attr('formtypeV'));
	dataToPost.formnopays 	= ($edit === null ? "":$edit.attr('formnopays'));
	dataToPost.formnopaye 	= ($edit === null ? "":$edit.attr('formnopaye'));
	dataToPost.formrate 	= ($edit === null ? "":$edit.attr('formrate'));
	dataToPost.formdetail 	= ($edit === null ? "":$edit.attr('formdetail'));
	dataToPost.formevent 	= ($edit === null ? "add":"edit");
	
	data = new Array();
	$('.btn_car_old_delete').each(function(){
		data_r = new Object();
		data_r.FPRICE = $(this).attr('FPRICE');
		data_r.TPRICE = $(this).attr('TPRICE');
		
		data.push(data_r);
	});
	dataToPost.price = (data.length == 0 ? "":data);
	
	editDwn = new Array();
	$('.editDwn').each(function(){
		data = new Object();
		data.formpriceFP 	= $(this).attr('formpriceFP');
		data.formpriceTP 	= $(this).attr('formpriceTP');
		data.formdwns 		= $(this).attr('formdwns');
		data.formdwne 		= $(this).attr('formdwne');
		
		editDwn.push(data);
	});
	dataToPost.editDwn = (editDwn.length == 0 ? "":editDwn);
	
	editFree = new Array();
	$('.editFree').each(function(){
		data = new Object();
		data.formpriceFP 	= $(this).attr('formpriceFP');
		data.formpriceTP 	= $(this).attr('formpriceTP');
		data.formdwns 		= $(this).attr('formdwns');
		data.formdwne 		= $(this).attr('formdwne');
		data.formtypeT 		= $(this).attr('formtypeT');
		data.formtypeV 		= $(this).attr('formtypeV');
		data.formnopays 	= $(this).attr('formnopays');
		data.formnopaye 	= $(this).attr('formnopaye');
		data.formrate 		= $(this).attr('formrate');
		data.formdetail 	= $(this).attr('formdetail');
		
		editFree.push(data);
	});
	
	dataToPost.editFree = (editFree.length == 0 ? "":editFree);
	
	
	
	$('#btnAddDwn').attr('disabled',true);
	$('#btnAddFree').attr('disabled',true);
	$(".deleteDwn").attr('disabled',true);
	$(".editDwn").attr('disabled',true);
	$(".deleteFree").attr('disabled',true);
	$(".editFree").attr('disabled',true);
	
	$('#loadding').fadeIn(200);
	JDbtnAddDwn = $.ajax({
		url:'../SYS04/Standard/JDFormStdFREE',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide(0);
			
			Lobibox.window({
				title: 'Standard ของแถม',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($windowFree){
					$('#formprice ,#formDwn ,#formtype').select2({ 
						dropdownParent: $("#main_form_free"), 
						minimumResultsForSearch: -1,
						width: '100%'
					});
					
					
					$('#formprice').on('select2:select',function(){
						$prices = $('#formprice').find(':selected').attr('fprice');
						$pricee = $('#formprice').find(':selected').attr('tprice');
						$('#formDwn option').each(function(){
							$this = $(this);
							if($prices == $this.attr('formpriceFP') && $pricee == $this.attr('formpriceTP')){
								$this.removeAttr('disabled');
							}else{
								$this.attr('disabled',true);
							}
						});
						
						$('#formDwn').val(null).select2("destroy").select2({ 
							placeholder: 'เลือก',
							dropdownParent: $("#main_form_free"), 
							minimumResultsForSearch: -1,
							width: '100%'
						});
					});
					
					var jdbtnSWNADD = null;
					$("#btnSWNADD").unbind('click');
					$("#btnSWNADD").click(function(){
						dataToPost = new Object();
						dataToPost.formprice 	= $("#formprice").find(':selected').text();
						dataToPost.formpriceFP 	= $("#formDwn").find('option:selected').attr("formpriceFP");
						dataToPost.formpriceTP 	= $("#formDwn").find('option:selected').attr("formpriceTP");
						dataToPost.formdwns 	= $("#formDwn").find('option:selected').attr("formdwns");
						dataToPost.formdwne 	= $("#formDwn").find('option:selected').attr("formdwne");
						dataToPost.formtypeT 	= $("#formtype").find(':selected').text();
						dataToPost.formtypeV 	= $("#formtype").find(':selected').val();
						dataToPost.formrate 	= $("#formrate").val();
						dataToPost.formnopays   = $("#formnopays").val();
						dataToPost.formnopaye 	= $("#formnopaye").val();
						dataToPost.formdetail 	= $("#formdetail").val();
						
						editFree = new Array();
						$('.editFree').each(function(){
							data = new Object();
							data.formpriceFP 	= $(this).attr('formpriceFP');
							data.formpriceTP 	= $(this).attr('formpriceTP');
							data.formdwns 		= $(this).attr('formdwns');
							data.formdwne 		= $(this).attr('formdwne');
							data.formtypeT 		= $(this).attr('formtypeT');
							data.formtypeV 		= $(this).attr('formtypeV');
							data.formrate 		= $(this).attr('formrate');
							data.formnopays 	= $(this).attr('formnopays');
							data.formnopaye 	= $(this).attr('formnopaye');
							data.formdetail 	= $(this).attr('formdetail');
							
							editFree.push(data);
						});
						dataToPost.editFree = (editFree.length == 0 ? "":editFree);
						
						$('#loadding').fadeIn(200);
						jdbtnSWNADD = $.ajax({
							url:'../SYS04/Standard/getStdFree',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							beforeSend: function(){ 
								if(jdbtnSWNADD !== null){ jdbtnSWNADD.abort(); } 
							},
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
									$('#table-stdfree tbody').empty().append(data.html);
									$edit = null;
									activeDatatablesFree();
									$windowFree.destroy();
								}
								
								jdbtnSWNADD = null;
								$('#loadding').fadeOut(200);
							},
							error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
						});
					});
					
					JDbtnAddDwn = null;
				},
				beforeClose : function(){
					if($edit !== null){
						dataToPost.action = "cancel edit";	
						$('#loadding').fadeIn(200);
						
						$.ajax({
							url:'../SYS04/Standard/getStdFree',
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
									$('#table-stdfree tbody').empty().append(data.html);
									activeDatatablesFree();
								}
								
								$('#loadding').fadeOut(200);
							},
							error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
						});
					}
					
					$('#btnAddDwn').attr('disabled',false);
					$('#btnAddFree').attr('disabled',false);
					$(".deleteDwn").attr('disabled',false);
					$(".editDwn").attr('disabled',false);
					$(".deleteFree").attr('disabled',false);
					$(".editFree").attr('disabled',false);
				}
			});
		},
		beforeSend: function(){
			if(JDbtnAddDwn !== null){
				JDbtnAddDwn.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}







var JDbtnt1import = null;
$("#btnt1import").click(function(){
	JDbtnt1import = $.ajax({
		url:'../SYS04/Standard/stdFormUPLOAD',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'นำเข้ารายการสแตนดาร์ด',
				//width: $(window).width(),
				height: '200',
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$("#form_std").uploadFile({		
						url:'../SYS04/Standard/import_std',
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
									content: obj["html"],
									draggable: false,
									closeOnEsc: false,
									shown: function($this){
										//fn_import();
									}
								});

								$this.destroy();
								
								$("#loadding").fadeOut(200);
								
								fn_afterimport();
							}
							
						}
					});
					
					$("#form_import").unbind('click');
					$("#form_import").click(function(){
						window.open("../public/form_upload/std_sell_multiple.xlsx");
					});
				}
			});
			
			JDbtnt1import = null;
		},
		beforeSend: function(){ if(JDbtnt1import !== null){ JDbtnt1import.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function fn_afterimport(){
	var JDstd_import = null;
	$('#std_import').click(function(){
		dataToPost = new Object();
		dataToPost.dt = '';
		
		$('#loadding').fadeIn(200);
		JDstd_import = $.ajax({
			url:'../SYS04/Standard/import_save',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.errorMsg
				});
				
				JDstd_import = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(JDstd_import !== null){ JDstd_import.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
}





























