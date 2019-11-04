/********************************************************
             ______@06/09/2019______
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
});

var JDbtnt1search = null;
$("#btnt1search").click(function(){
	dataToPost = new Object();
	dataToPost.name = $("#SNAME").val();
	dataToPost.model = $("#SMODEL").val();
	dataToPost.baab = $("#SBAAB").val();
	dataToPost.color = $("#SCOLOR").val();
	dataToPost.events = $("#SEVENTS").val();
	dataToPost.evente = $("#SEVENTE").val();
	dataToPost.acticod = $("#SACTICOD").val();
	
	$('#loadding').show();
	JDbtnt1search = $.ajax({
		url:'../SYS04/Standard/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide(200);
			
			Lobibox.window({
				title: 'รายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$("#table-fixed-std-detail").hide();
					
					redraw();
					function redraw(){
						var JDstddetail = null;
						$(".stddetail").unbind('click');
						$(".stddetail").click(function(){
							dataToPost = new Object();
							dataToPost.stdid 	= ($(this).attr('STDID'));
							dataToPost.stdrank 	= ($(this).attr('STDRank'));
							
							$('#loadding').show();
							JDstddetail = $.ajax({
								url:'../SYS04/Standard/searchDetail',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#loadding').hide(200);
									
									Lobibox.window({
										title: 'รายละเอียด',
										width: $(window).width(),
										height: $(window).height(),
										content: data.html,
										draggable: false,
										closeOnEsc: false,
										shown: function($this){
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
												selectorMinimalHeight: 1000,
												showFilterInputs: true,                                                             
												nonSelectedFilter: '',                                                              
												selectedFilter: '',                                                                 
												infoText: 'แสดงทั้งหมด {0}',                                                        
												infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
												infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
												filterOnValues: false                                                               
											});
											
											$(".bootstrap-duallistbox-container select").css({'max-height':'145px'});
											$(".bootstrap-duallistbox-container").find("*").prop("disabled",true);
											$(".bootstrap-duallistbox-container").find(".filter").prop("disabled",false);
											
											fn_datatables('table-stdfa',2,"200px","YES");
											fn_datatables('table-stdfree',2,"200px","YES");
											
											$("#FMODEL").select2({disabled:true});
											$("#FBAAB").select2({disabled:true});
											$("#FCOLOR").select2({disabled:true});
											$("#FACTI").select2({disabled:true});
											
											$("#btnSave").click(function(){ edit($(this)); });
										}
									});
									
									JDstddetail = null;
								},
								beforeSend : function(){
									if(JDstddetail !== null){ JDstddetail.abort(); }
								}
							});
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
		}
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
	//$('#loadding').show();
	JDbtnt1createStd = $.ajax({
		url:'../SYS04/Standard/loadform',
		//data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){ 
			$('#loadding').hide(200);
			
			Lobibox.window({
				title: 'สร้างราคาขาย',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					fnload($this);
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
			JDbtnt1createStd = null;
		},
		beforeSend: function(){
			if(JDbtnt1createStd !== null){
				JDbtnt1createStd.abort();
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
		}
	});
});

function fnload($thisForm){
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
		selectorMinimalHeight: 1000,
		showFilterInputs: true,                                                             
		nonSelectedFilter: '',                                                              
		selectedFilter: '',                                                                 
		infoText: 'แสดงทั้งหมด {0}',                                                        
		infoTextFiltered: '<span class="label label-warning">ค้นหา</span> {0} จาก {1}', 
		infoTextEmpty: 'ยังไม่ได้เลือก',                                                        
		filterOnValues: false                                                               
	});
	
	$(".bootstrap-duallistbox-container select").css({'max-height':'145px'});
	$(".bootstrap-duallistbox-container").find("*").prop("disabled",false);
	/*
	$(".bootstrap-duallistbox-container .move ,.moveall ,.remove ,.removeall").attr({
		'data-toggle':'tooltip',
		'data-placement':'top',
		'data-html':'true',
		'data-original-title':'tooltip',
	});
	*/
	
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
	
	$('#FBAAB').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#FBAAB').find(':selected').val() === 'undefined' ? "" : $('#FBAAB').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = "HONDA";
				dataToPost.MODEL = (typeof $('#FMODEL').find(':selected').val() === 'undefined' ? "" : $('#FMODEL').find(':selected').val());
				
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
		dropdownParent: $('#FBAAB').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#FCOLOR').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#FCOLOR').find(':selected').val() === 'undefined' ? "" : $('#FCOLOR').find(':selected').val());
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
		dropdownParent: $('#FBAAB').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#FACTI').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#FACTI').find(':selected').val() === 'undefined' ? "" : $('#FACTI').find(':selected').val());
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
		dropdownParent: $('#FACTI').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	//$('#table-stdfa').on('draw.dt',function(){ redraw(); });
	fn_datatables('table-stdfa',2,"200px","YES");
	fn_datatables('table-stdfree',2,"200px","YES");
	
	$("#btnAddDwn").click(function(){
		loadAddDwn(null); 	// เพิ่มข้อมูล  Standard การดาวน์รถ  ใหม่
	});
		
	$("#btnAddFree").click(function(){
		loadAddFree(null); 	// เพิ่มข้อมูล  Standard การดาวน์รถ  ใหม่
	});
	
	$("#btnSave").click(function(){
		dataToPost = new Object();
		dataToPost.MODEL = (typeof $("#FMODEL").find(":selected").val() === 'undefined' ? "":$("#FMODEL").find(":selected").val());
		dataToPost.BAAB  = (typeof $("#FBAAB").find(":selected").val() === 'undefined' ? "ALL":$("#FBAAB").find(":selected").val());
		dataToPost.COLOR = (typeof $("#FCOLOR").find(":selected").val() === 'undefined' ? "ALL":$("#FCOLOR").find(":selected").val());
		dataToPost.ACTI  = (typeof $("#FACTI").find(":selected").val() === 'undefined' ? "ALL":$("#FACTI").find(":selected").val());
		
		dataToPost.EVENTS 	 = $("#FEVENTS").val();
		dataToPost.EVENTE 	 = $("#FEVENTE").val();
		dataToPost.EVENTNAME = $("#FEVENTNAME").val();
		dataToPost.DETAIL 	 = $("#FDETAIL").val();
		dataToPost.FPRICE 	 = $("#FPRICE").val();
		dataToPost.FPRICE2 	 = $("#FPRICE2").val();
		dataToPost.LOCAT 	 = $("#FLOCAT").val();
		
		dataToPost.STDDWN = [];
		dataToPost.STDFREE = [];
		
		$Dwn = [];
		$(".editDwn").each(function(){
			row = new Object();
			row.formdwns		= $(this).attr('formdwns');
			row.formdwne 		= $(this).attr('formdwne');
			row.forminterest 	= $(this).attr('forminterest');
			row.forminterest2 	= $(this).attr('forminterest2');
			row.forminsurance 	= $(this).attr('forminsurance');
			row.formtrans 		= $(this).attr('formtrans');
			row.formregist 		= $(this).attr('formregist');
			row.formact 		= $(this).attr('formact');
			row.formcoupon 		= $(this).attr('formcoupon');
			
			$Dwn.push(row);
		});
		
		$Free = [];
		$(".editFree").each(function(){
			row = new Object();
			row.formnopays	= $(this).attr('formnopays');
			row.formnopaye 	= $(this).attr('formnopaye');
			row.formrate 	= $(this).attr('formrate');
			row.formdetail 	= $(this).attr('formdetail');
			
			$Free.push(row);
		});
		
		dataToPost.STDDWN 	= ($Dwn == "" ? "":$Dwn);
		dataToPost.STDFREE 	= ($Free == "" ? "":$Free);
		
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
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$thisForm.destroy();					
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
			},
			beforeSend: function(){
				if(JDbtnAddDwn !== null){
					JDbtnAddDwn.abort();
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
			}
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
				jd_btnUpload = null;
			}
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
		onSuccess:function(files,data,xhr,pd){
			obj = JSON.parse(data);
			
			$('#FEVENTNAME').val(obj[1]['A']);
			$('#FDETAIL').val(obj[1]['B']);
			var newOption = new Option(obj[1]['C'], obj[1]['C'], true, true);
			$('#FMODEL').empty().append(newOption).trigger('change');
			var newOption = new Option(obj[1]['D'], obj[1]['D'], true, true);
			$('#FBAAB').empty().append(newOption).trigger('change');
			var newOption = new Option(obj[1]['E'], obj[1]['E'], true, true);
			$('#FCOLOR').empty().append(newOption).trigger('change');
			var newOption = new Option(obj[1]['F'], obj[1]['F'], true, true);
			$('#FACTI').empty().append(newOption).trigger('change');
			
			$('#FEVENTS').val(obj[1]['G']);
			$('#FEVENTE').val(obj[1]['H']);
			$('#FPRICE').val(obj[1]['I']);
			$('#FPRICE2').val(obj[1]['J']);
			
			// สาขา
			var locat = obj[1]['K'];
			if(locat != ""){
				locat = locat.split(',');
				for(var i=0;i<locat.length;i++){
					$('#FLOCAT option[value='+locat[i]+']').attr('selected','selected');
				}
				$('#FLOCAT').bootstrapDualListbox('refresh', true);
			}
			
			// ตรวจสอบขนาดของ object
			objsize = Object.size(obj);
			// Standard
			var stdfa = $("#table-stdfa").DataTable();
			stdfa.clear().draw();
			for(var i=1;i<=objsize;i++){
				data = new Object();
				data.formdwns 		= obj[i]['L'];
				data.formdwne 		= obj[i]['M'];
				data.forminterest 	= obj[i]['N'];
				data.forminterest2 	= obj[i]['O'];
				data.forminsurance 	= obj[i]['P'];
				data.formtrans 		= obj[i]['Q'];
				data.formregist 	= obj[i]['R'];
				data.formact 		= obj[i]['S'];
				data.formcoupon		= obj[i]['T'];
				
				if(parseInt(data.formdwns) > -1){
					stdfa.row.add([ 
						data.formdwns+" - "+data.formdwne,
						data.forminterest+(data.forminterest2 == "" ? "":" ("+data.forminterest2+")"),
						data.forminsurance,
						data.formtrans,
						data.formregist,
						data.formact,
						data.formcoupon,
						"<button class='editDwn btn-warning'"+
							"formdwns='"+data.formdwns+"'"+
							"formdwne='"+data.formdwne+"'"+
							"forminterest='"+data.forminterest+"'"+
							"forminterest2='"+data.forminterest2+"'"+
							"forminsurance='"+data.forminsurance+"'"+
							"formtrans='"+data.formtrans+"'"+
							"formregist='"+data.formregist+"'"+
							"formact='"+data.formact+"'"+
							"formcoupon='"+data.formcoupon+"'"+
							"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
						"<button class='deleteDwn btn-danger'"+
							"formdwns='"+data.formdwns+"'"+
							"formdwne='"+data.formdwne+"'"+
							"forminterest='"+data.forminterest+"'"+
							"forminterest2='"+data.forminterest2+"'"+
							"forminsurance='"+data.forminsurance+"'"+
							"formtrans='"+data.formtrans+"'"+
							"formregist='"+data.formregist+"'"+
							"formact='"+data.formact+"'"+
							"formcoupon='"+data.formcoupon+"'"+
							"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
					]).draw();		
					activeDatatables(stdfa); 	// หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ					
				}
			}
			
			// ของแถม
			var stdfree = $("#table-stdfree").DataTable();
			stdfree.clear().draw();
			for(var i=1;i<=objsize;i++){
				data = new Object();
				data.formnopays = obj[i]['U'];
				data.formnopaye = obj[i]['V'];
				data.formrate 	= obj[i]['W'];
				data.formdetail = obj[i]['X'];				
				
				if(parseInt(data.formnopays) > -1){
					stdfree.row.add([ 
						data.formnopays+" - "+data.formnopaye,
						data.formrate,
						data.formdetail,
						"<button class='editFree btn-warning'"+
							"formnopays='"+data.formnopays+"'"+
							"formnopaye='"+data.formnopaye+"'"+
							"formrate='"+data.formrate+"'"+
							"formdetail='"+data.formdetail+"'"+
							"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
						"<button class='deleteFree btn-danger'"+
							"formnopays='"+data.formnopays+"'"+
							"formnopaye='"+data.formnopaye+"'"+
							"formrate='"+data.formrate+"'"+
							"formdetail='"+data.formdetail+"'"+
							"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
					]).draw();
					activeDatatablesFree(stdfree); // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ(stdfree); 	// หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
				}
			}
			
			$thisFile.destroy();
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

var JDselectSACTICOD2 = null;	
var JDselectSACTICOD2_Cache = null;
$('#SACTICOD').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) { 
	FN_JD_BSSELECT("SACTICOD",$(this));
});

$("#SACTICOD").parent().find("[aria-label=Search]").keyup(function(){ 
	FN_JD_BSSELECT("SACTICOD",$(this));
});

function FN_JD_BSSELECT($id,$thisSelected){
	dataToPost = new Object();
	dataToPost.filter = $thisSelected.val();
	dataToPost.now	  = (typeof $("#"+$id).selectpicker('val') == null ? "":$("#"+$id).selectpicker('val'));
	
	clearTimeout(JDselectSACTICOD2);
	JDselectSACTICOD2 = setTimeout(function(){
		getdata();
	},0);
	
	function getdata(){
		//$("#"+$id+" UI.dropdown-menu").html("loadding...");
		JDselectSACTICOD2_Cache = $.ajax({
			url: '../SYS04/Standard/getSACTICOD2',
			data: dataToPost,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#"+$id).empty().append(data.opt);
				$("#"+$id).selectpicker('refresh');
				
				JDselectSACTICOD2_Cache= null;
			},
			beforeSend: function(){
				if(JDselectSACTICOD2_Cache !== null){
					JDselectSACTICOD2_Cache.abort();
				}
			}
		});
	}
}


function activeDatatables($stdfa){ // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
	// $(".deleteDwn").attr('disabled', true);
	// $(".editDwn").attr('disabled', true);

	$(".deleteDwn").unbind('click');
	$(".deleteDwn").click(function(){
		$deleteDwn = $(this);
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
					$deleteDwn.parent().parent().each(function(){ // tr
						$stdfa.row($(this)).remove().draw( false ); //ลบข้อมูล Standard
					});
				}
			}
		});
	});
	
	$('.editDwn').unbind('click');
	$('.editDwn').click(function(){
		$(this).parent().parent().each(function(){ // tr
			$stdfa.row($(this)).remove().draw( false ); //ลบข้อมูลเดิมออกไปก่อน
		});
		loadAddDwn($(this)); // แก้ข้อมูล  Standard การดาวน์รถ  
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
					$deleteFree.parent().parent().each(function(){ // tr
						$stdfree.row($(this)).remove().draw( false ); //ลบข้อมูล Standard
					});
				}
			}
		});
	});
	
	$('.editFree').unbind('click');
	$('.editFree').click(function(){
		$(this).parent().parent().each(function(){ // tr
			$stdfree.row($(this)).remove().draw( false ); //ลบข้อมูลเดิมออกไปก่อน
		});
		loadAddFree($(this)); // แก้ข้อมูล  Standard การดาวน์รถ  
	});
}

var JDbtnAddDwn = null;	
function loadAddDwn($edit){	
	dataToPost = new Object();
	dataToPost.formdwns 	 = ($edit === null ? "":$edit.attr('formdwns'));
	dataToPost.formdwne 	 = ($edit === null ? "":$edit.attr('formdwne'));
	dataToPost.forminterest  = ($edit === null ? "":$edit.attr('forminterest'));
	dataToPost.forminterest2 = ($edit === null ? "":$edit.attr('forminterest2'));
	dataToPost.forminsurance = ($edit === null ? "":$edit.attr('forminsurance'));
	dataToPost.formtrans 	 = ($edit === null ? "":$edit.attr('formtrans'));
	dataToPost.formregist  	 = ($edit === null ? "":$edit.attr('formregist'));
	dataToPost.formact 		 = ($edit === null ? "":$edit.attr('formact'));
	dataToPost.formcoupon 	 = ($edit === null ? "":$edit.attr('formcoupon'));
	dataToPost.formevent 	 = ($edit === null ? "add":"edit");
	
	$('#btnAddDwn').attr('disabled',true);
	$('#btnAddFree').attr('disabled',true);
	$(".deleteDwn").attr('disabled',true);
	$(".editDwn").attr('disabled',true);
	$(".deleteFree").attr('disabled',true);
	$(".editFree").attr('disabled',true);
	
	$('#loadding').show(0);
	JDbtnAddDwn = $.ajax({
		url:'../SYS04/Standard/formStdDWN',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide(0);
			
			Lobibox.window({
				title: 'Standard การดาวน์รถ',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: true,
				closeOnEsc: true,
				shown: function($this){
					var stdfa = $("#table-stdfa").DataTable();
					
					$("#btnSWNADD").click(function(){
						data = new Object();
						data.formdwns 		= $("#formdwns").val();
						data.formdwne 		= $("#formdwne").val();
						data.forminterest 	= $("#forminterest").val();
						data.forminterest2 	= $("#forminterest2").val();
						data.forminsurance 	= $("#forminsurance").val();
						data.formtrans 		= $("#formtrans").val();
						data.formregist 	= $("#formregist").val();
						data.formact 		= $("#formact").val();
						data.formcoupon 	= $("#formcoupon").val();
						data.formcomment 	= $("#formcomment").val();
						
						$msg = "";
						$(".editDwn").each(function(){
							$nowdwns = $(this).attr("formdwns");
							$nowdwne = $(this).attr("formdwne");
							
							//จำนวนเงินดาวน์เริ่มต้น อยู่ในช่วงที่ระบุไว้แล้ว
							if(parseInt(data.formdwns) >= $nowdwns  && parseInt(data.formdwns) <= $nowdwne){
								$msg = data.formdwns +" this between "+$nowdwns+" - "+$nowdwne;
							}
							//จำนวนเงินดาวน์เริ่มต้น อยู่ในช่วงที่ระบุไว้แล้ว
							if(parseInt(data.formdwne) >= $nowdwns  && parseInt(data.formdwne) <= $nowdwne){
								$msg = data.formdwns +" this between "+$nowdwns+" - "+$nowdwne;
							}
						});
						
						if(parseInt(data.formdwns) > parseInt(data.formdwne)){
							$msg = "ช่วงเงินดาวน์ ไม่ถูกต้อง โปรดตรวจสอบข้อมูลใหม่อีกครั้ง"
						} 
						
						if($msg == ""){
							// เพิ่มข้อมูลใหม่
							stdfa.row.add([ 
								data.formdwns+" - "+data.formdwne,
								data.forminterest+(data.forminterest2 == "" ? "":" ("+data.forminterest2+")"),
								data.forminsurance,
								data.formtrans,
								data.formregist,
								data.formact,
								data.formcoupon,
								"<button class='editDwn btn-warning'"+
									"formdwns='"+data.formdwns+"'"+
									"formdwne='"+data.formdwne+"'"+
									"forminterest='"+data.forminterest+"'"+
									"forminterest2='"+data.forminterest2+"'"+
									"forminsurance='"+data.forminsurance+"'"+
									"formtrans='"+data.formtrans+"'"+
									"formregist='"+data.formregist+"'"+
									"formact='"+data.formact+"'"+
									"formcoupon='"+data.formcoupon+"'"+
									"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
								"<button class='deleteDwn btn-danger'"+
									"formdwns='"+data.formdwns+"'"+
									"formdwne='"+data.formdwne+"'"+
									"forminterest='"+data.forminterest+"'"+
									"forminterest2='"+data.forminterest2+"'"+
									"forminsurance='"+data.forminsurance+"'"+
									"formtrans='"+data.formtrans+"'"+
									"formregist='"+data.formregist+"'"+
									"formact='"+data.formact+"'"+
									"formcoupon='"+data.formcoupon+"'"+
									"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
							]).draw();
							
							activeDatatables(stdfa); 	// หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
							$edit = null; 				//เมื่อมีการเพิ่มข้อมูลใหม่ไปแล้ว กำหนดให้ $edit เป็น NULL เพื่อตอนปิดหน้าต่างจะได้ไม่เพิ่มข้อมูลเดิมกลับเข้าไปอีก
							$this.destroy();			//ทำลาย Lobi window;
						}else{
							Lobibox.notify('error', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: $msg
							});
						}
						
					});
					
					JDbtnAddDwn = null;
				},
				beforeClose : function(){
					if($edit !== null){
						var stdfa = $("#table-stdfa").DataTable();
						
						// กรณีแก้ไข แล้วกดปิดหน้าต่างแก้ไข ให้นำข้อมูลเดิมกลับมา
						stdfa.row.add([
							dataToPost.formdwns+" - "+dataToPost.formdwne,
							dataToPost.forminterest+(dataToPost.forminterest2 == "" ? "":" ("+dataToPost.forminterest2+")"),
							dataToPost.forminsurance,
							dataToPost.formtrans,
							dataToPost.formregist,
							dataToPost.formact,
							dataToPost.formcoupon,
							"<button class='editDwn btn-warning'"+
								"formdwns='"+dataToPost.formdwns+"'"+
								"formdwne='"+dataToPost.formdwne+"'"+
								"forminterest='"+dataToPost.forminterest+"'"+
								"forminterest2='"+dataToPost.forminterest2+"'"+
								"forminsurance='"+dataToPost.forminsurance+"'"+
								"formtrans='"+dataToPost.formtrans+"'"+
								"formregist='"+dataToPost.formregist+"'"+
								"formact='"+dataToPost.formact+"'"+
								"formcoupon='"+dataToPost.formcoupon+"'"+
								"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
							"<button class='deleteDwn btn-danger'"+
								"formdwns='"+dataToPost.formdwns+"'"+
								"formdwne='"+dataToPost.formdwne+"'"+
								"forminterest='"+dataToPost.forminterest+"'"+
								"forminterest2='"+dataToPost.forminterest2+"'"+
								"forminsurance='"+dataToPost.forminsurance+"'"+
								"formtrans='"+dataToPost.formtrans+"'"+
								"formregist='"+dataToPost.formregist+"'"+
								"formact='"+dataToPost.formact+"'"+
								"formcoupon='"+dataToPost.formcoupon+"'"+
								
								"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
						]).draw();
						activeDatatables(stdfa); // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
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
		}
	});
}

function loadAddFree($edit){
	dataToPost = new Object();
	dataToPost.formnopays 	= ($edit === null ? "":$edit.attr('formnopays'));
	dataToPost.formnopaye 	= ($edit === null ? "":$edit.attr('formnopaye'));
	dataToPost.formrate 	= ($edit === null ? "":$edit.attr('formrate'));
	dataToPost.formdetail 	= ($edit === null ? "":$edit.attr('formdetail'));
	dataToPost.formevent 	= ($edit === null ? "add":"edit");
	
	$('#btnAddDwn').attr('disabled',true);
	$('#btnAddFree').attr('disabled',true);
	$(".deleteDwn").attr('disabled',true);
	$(".editDwn").attr('disabled',true);
	$(".deleteFree").attr('disabled',true);
	$(".editFree").attr('disabled',true);
	
	$('#loadding').show(0);
	JDbtnAddDwn = $.ajax({
		url:'../SYS04/Standard/formStdFREE',
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
				shown: function($this){
					var stdfree = $("#table-stdfree").DataTable();
					
					$("#btnSWNADD").click(function(){
						data = new Object();
						data.formnopays = $("#formnopays").val();
						data.formnopaye = $("#formnopaye").val();
						data.formrate 	= $("#formrate").val();
						data.formdetail = $("#formdetail").val();
						
						$msg = "";
						/*
						$(".editFree").each(function(){
							$nownopays = parseInt($(this).attr("formnopays"));
							$nownopaye = parseInt($(this).attr("formnopaye"));
							
							//จำนวนเงินดาวน์เริ่มต้น อยู่ในช่วงที่ระบุไว้แล้ว
							if(parseInt(data.formnopays) >= $nownopays  && parseInt(data.formnopays) <= $nownopaye){
								$msg = data.formnopays +" this between "+$nownopays+" - "+$nownopaye;
							}
							//จำนวนเงินดาวน์เริ่มต้น อยู่ในช่วงที่ระบุไว้แล้ว
							if(parseInt(data.formnopaye) >= $nownopays  && parseInt(data.formnopaye) <= $nownopaye){
								$msg = data.formnopaye +" this between "+$nownopays+" - "+$nownopaye;
							}
						});
						*/
						
						if(parseInt(data.formnopays) > parseInt(data.formnopaye)){
							$msg = "ระบุงวดไม่ถูกต้อง โปรดตรวจสอบข้อมูลใหม่อีกครั้ง"
						} 
						
						if($msg == ""){
							// เพิ่มข้อมูลใหม่
							stdfree.row.add([ 
								data.formnopays+" - "+data.formnopaye,
								data.formrate,
								data.formdetail,
								"<button class='editFree btn-warning'"+
									"formnopays='"+data.formnopays+"'"+
									"formnopaye='"+data.formnopaye+"'"+
									"formrate='"+data.formrate+"'"+
									"formdetail='"+data.formdetail+"'"+
									"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
								"<button class='deleteFree btn-danger'"+
									"formnopays='"+data.formnopays+"'"+
									"formnopaye='"+data.formnopaye+"'"+
									"formrate='"+data.formrate+"'"+
									"formdetail='"+data.formdetail+"'"+
									"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
							]).draw();
							
							activeDatatablesFree(stdfree); 	// หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
							$edit = null; 				//เมื่อมีการเพิ่มข้อมูลใหม่ไปแล้ว กำหนดให้ $edit เป็น NULL เพื่อตอนปิดหน้าต่างจะได้ไม่เพิ่มข้อมูลเดิมกลับเข้าไปอีก
							$this.destroy();			//ทำลาย Lobi window;
						}else{
							Lobibox.notify('error', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: $msg
							});
						}
						
					});
					
					JDbtnAddDwn = null;
				},
				beforeClose : function(){
					if($edit !== null){
						var stdfree = $("#table-stdfree").DataTable();
						
						// กรณีแก้ไข แล้วกดปิดหน้าต่างแก้ไข ให้นำข้อมูลเดิมกลับมา
						stdfree.row.add([
							dataToPost.formnopays+" - "+dataToPost.formnopaye,
							dataToPost.formrate,
							dataToPost.formdetail,
							"<button class='editFree btn-warning'"+
								"formnopays='"+dataToPost.formnopays+"'"+
								"formnopaye='"+dataToPost.formnopaye+"'"+
								"formrate='"+dataToPost.formrate+"'"+
								"formdetail='"+dataToPost.formdetail+"'"+
								"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>"+
							"<button class='deleteFree btn-danger'"+
								"formnopays='"+dataToPost.formnopays+"'"+
								"formnopaye='"+dataToPost.formnopaye+"'"+
								"formrate='"+dataToPost.formrate+"'"+
								"formdetail='"+dataToPost.formdetail+"'"+
								"><span class='glyphicon glyphicon-trash'> ลบ</span></button>"
						]).draw();
						activeDatatablesFree(stdfree); // หลังจากเพิ่มข้อมูลใน datatables ให้ทำอะไรต่อ
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
		}
	});
}













