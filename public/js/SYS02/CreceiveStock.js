/********************************************************
             ______@17/04/2019______
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
var _groupType  = $('.tab1[name="home"]').attr('groupType');
var OBJfn_Save=null;

var JDbtnt1search = null;
$('#btnt1search').click(function(){
	dataToPost = new Object();
	
	$('#loadding').fadeIn(200);
	JDbtnt1search = $.ajax({
		url: '../SYS02/CreceiveStock/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#resultt1receiveStock').html(data.html);
			
			$('#table-receiveStock').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-receiveStock',1,275);
			
			JDbtnt1search = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(JDbtnt1search !== null){ JDbtnt1search.abort(); }
		}
	});
});

function redraw(){
	
}

var jdloadReceived = null;
function loadReceived(dataToPost){
	$('#loadding').fadeIn(200);
	jdloadReceived = $.ajax({
		url:'../SYS02/CreceiveStock/getfromReceived',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'รับรถเข้าสต๊อค',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$('#table-newstock').on('draw.dt',function(){ redraw(); });
					fn_datatables('table-newstock',2,275,'YES');
					fn_reactive($this);
					//$this.destroy();
					//wizard('old',$param,$this);
				}
			});
			
			jdloadReceived = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){
			if(jdloadReceived !== null){ jdloadReceived.abort(); }
		}
	});
}

function fn_reactive($thisWindow){
	var jdadd_newcar=null;
	$('#fa_recvno').val('Auto Genarate');
	$('#fa_recvno').attr('readonly',true);
	
	$('#fa_locat').select2({
		disabled: (_groupType == 'OFF' ? true:false),
	});
	/*
	$('#fa_locat').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fa_locat').find(':selected').val() === 'undefined' ? '':$('#fa_locat').find(':selected').val());
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
		disabled: (_level == 1 ? false : true),
		//theme: 'classic',
		width: '100%'
	});
	*/
	
	$('#fa_apmast').select2();
	/*
	$('#fa_apmast').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getAPMAST',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fa_apmast').find(':selected').val() === 'undefined' ? '':$('#fa_apmast').find(':selected').val());
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	*/
	
	$('#add_newcar').click(function(){
		$('#add_newcar').attr('disabled',true);
		$('#loadding').fadeIn(200);
		dataToPost = new Object();
		dataToPost.locat = (typeof $('#fa_locat').find(':selected').val() === "undefined" ? "":$('#fa_locat').find(':selected').val());
		
		jdadd_newcar = $.ajax({
			url:'../SYS02/CreceiveStock/getfromADDSTRNO',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.window({
					title: 'รับรถเพิ่ม',
					width: $(window).width() - 100,
					height: $(window).height() - 100,
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						fn_fromADDSTRNO($this);
					},
					beforeClose: function(){
						$('#add_newcar').attr('disabled',false);
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
					}
				});
				
				$('#loadding').fadeOut(200);
				jdadd_newcar = null;
			},
			beforeSend: function(){
				if(jdadd_newcar !== null){ jdadd_newcar.abort(); }
			},
			error: function(){
				$('#loadding').fadeOut(200);
			}
		});		
	});
	
	$('#add_save').click(function(){
		fn_Save();
	});
}

function fn_Save(){
	dataToPost = new Object();
	dataToPost.recvno = $('#fa_recvno').val();
	dataToPost.recvdt = $('#fa_recvdt').val();
	dataToPost.locat  = (typeof $('#fa_locat').find(':selected').val() === 'undefined' ? '':$('#fa_locat').find(':selected').val());
	dataToPost.apmast = (typeof $('#fa_apmast').find(':selected').val() === 'undefined' ? '':$('#fa_apmast').find(':selected').val());
	dataToPost.invno  = $('#fa_invno').val();
	dataToPost.invdt  = $('#fa_invdt').val();
	dataToPost.taxno  = $('#fa_taxno').val();
	dataToPost.taxdt  = $('#fa_taxdt').val();
	dataToPost.credtm = $('#fa_credtm').val();
	dataToPost.duedt  = $('#fa_duedt').val();
	dataToPost.descp  = $('#fa_descp').val();
	dataToPost.vatrt  = $('#fa_vatrt').val();
	dataToPost.fltax  = ($('#fa_fltax').is(':checked') ? 'Y':'N');
	dataToPost.memo1  = $('#fa_memo1').val();
	
	$dataSTR = [];
	$(".del_newcar").each(function(){
		var row = new Object();
		row.type		= $(this).attr('type');
		row.model		= $(this).attr('model');
		row.baab		= $(this).attr('baab');
		row.color 		= $(this).attr('color');
		row.cc 			= $(this).attr('cc');
		row.strno 		= $(this).attr('strno');
		row.engno 		= $(this).attr('engno');
		row.keyno 		= $(this).attr('keyno');
		row.rvcode 		= $(this).attr('rvcode');
		row.rvcodnam 	= $(this).attr('rvcodnam');
		row.rvlocat 	= $(this).attr('rvlocat');
		row.refno 		= $(this).attr('refno');
		row.milert 		= $(this).attr('milert');
		row.stdprc 		= $(this).attr('stdprc');
		row.crcost 		= $(this).attr('crcost');
		row.disct 		= $(this).attr('disct');
		row.netcost 	= $(this).attr('netcost');
		row.vatrt 		= $(this).attr('vatrt');
		row.crvat 		= $(this).attr('crvat');
		row.totcost 	= $(this).attr('totcost');
		row.gcode 		= $(this).attr('gcode');
		row.gdesc 		= $(this).attr('gdesc');
		row.menuyr 		= $(this).attr('menuyr');
		row.bonus 		= $(this).attr('bonus');
		row.stat 		= $(this).attr('stat');
		row.statname	= $(this).attr('statname');
		row.memo1 		= $(this).attr('memo1');
		
		$dataSTR.push(row);
	});
	dataToPost.dataSTR 	 = ($dataSTR.length == 0 ? "":JSON.stringify($dataSTR));
	
	$('#loadding').fadeIn(200);
	OBJfn_Save = $.ajax({
		url:'../SYS02/CreceiveStock/Save',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		beforeSend: function(){ if(OBJfn_Save !== null){ OBJfn_Save.abort(); }},
		success:function(data){
			
			OBJfn_Save = null;
			$('#loadding').fadeOut(200);
		}
	});
}

function fn_fromADDSTRNO($thisASTRNO){	
	$('#fc_type').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTYPES',
			data: function (params) {
				dataToPost 		= new Object();
				dataToPost.now  = (typeof $('#fc_type').find(':selected').val() === 'undefined' ? '':$('#fc_type').find(':selected').val());
				dataToPost.q 	= (typeof params.term === 'undefined' ? '' : params.term);
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_model').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 		= (typeof $('#fc_model').find(':selected').val() === 'undefined' ? '':$('#fc_model').find(':selected').val());
				dataToPost.q 		= (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD 	= (typeof $('#fc_type').find(':selected').val() === 'undefined' ? '':$('#fc_type').find(':selected').val());
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_type').on('select2:select',function(){
		$('#fc_model').empty().trigger('changed');
		$('#fc_baab').empty().trigger('changed');
		$('#fc_color').empty().trigger('changed');
		$('#fc_cc').empty().trigger('changed');
	});
	
	$('#fc_model').on('select2:select',function(){
		$('#fc_baab').empty().trigger('changed');
		$('#fc_color').empty().trigger('changed');
		$('#fc_cc').empty().trigger('changed');
	});
	
	$('#fc_baab').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 		= (typeof $('#fc_baab').find(':selected').val() === 'undefined' ? '':$('#fc_baab').find(':selected').val());
				dataToPost.q 		= (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD 	= (typeof $('#fc_type').find(':selected').val() === 'undefined' ? '':$('#fc_type').find(':selected').val());
				dataToPost.MODEL 	= (typeof $('#fc_model').find(':selected').val() === 'undefined' ? '':$('#fc_model').find(':selected').val());
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_color').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getJDCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fc_color').find(':selected').val() === 'undefined' ? '':$('#fc_color').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.BAAB  = (typeof $('#fc_baab').find(':selected').val() === 'undefined' ? '':$('#fc_baab').find(':selected').val());
				dataToPost.MODEL = (typeof $('#fc_model').find(':selected').val() === 'undefined' ? '':$('#fc_model').find(':selected').val());
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_cc').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCC',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fc_cc').find(':selected').val() === 'undefined' ? '':$('#fc_cc').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_rvcode').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fc_rvcode').find(':selected').val() === 'undefined' ? '':$('#fc_rvcode').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_rvlocat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fc_rvlocat').find(':selected').val() === 'undefined' ? '':$('#fc_rvlocat').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
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
		disabled: (_groupType == "OFF" ? true:false),
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_gcode').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getGCODE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fc_gcode').find(':selected').val() === 'undefined' ? '':$('#fc_gcode').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fc_stat').select2();
	
	var jdfc_add=null;
	$('#fc_add').click(function(){
		dataToPost = new Object();
		dataToPost.type 	= (typeof $('#fc_type').find(':selected').val() === 'undefined'?'':$('#fc_type').find(':selected').val());
		dataToPost.model 	= (typeof $('#fc_model').find(':selected').val() === 'undefined'?'':$('#fc_model').find(':selected').val());
		dataToPost.baab 	= (typeof $('#fc_baab').find(':selected').val() === 'undefined'?'':$('#fc_baab').find(':selected').val());
		dataToPost.color 	= (typeof $('#fc_color').find(':selected').val() === 'undefined'?'':$('#fc_color').find(':selected').val());
		dataToPost.cc 		= (typeof $('#fc_cc').find(':selected').val() === 'undefined'?'':$('#fc_cc').find(':selected').val());
		dataToPost.strno 	= $('#fc_strno').val();
		dataToPost.engno 	= $('#fc_engno').val();
		dataToPost.keyno 	= $('#fc_keyno').val();
		dataToPost.rvcode 	= (typeof $('#fc_rvcode').find(':selected').val() === 'undefined'?'':$('#fc_rvcode').find(':selected').val());
		dataToPost.rvcodnam	= (typeof $('#fc_rvcode').find(':selected').val() === 'undefined'?'':$('#fc_rvcode').find(':selected').text());
		dataToPost.rvlocat 	= (typeof $('#fc_rvlocat').find(':selected').val() === 'undefined'?'':$('#fc_rvlocat').find(':selected').val());
		dataToPost.refno 	= $('#fc_refno').val();
		dataToPost.milert 	= $('#fc_milert').val();
		dataToPost.stdprc 	= $('#fc_stdprc').val();
		dataToPost.crcost 	= $('#fc_crcost').val();
		dataToPost.disct 	= $('#fc_disct').val();
		dataToPost.netcost 	= $('#fc_netcost').val();
		dataToPost.vatrt 	= $('#fc_vatrt').val();
		dataToPost.crvat 	= $('#fc_crvat').val();
		dataToPost.totcost 	= $('#fc_totcost').val();
		dataToPost.gcode 	= (typeof $('#fc_gcode').find(':selected').val() === 'undefined'?'':$('#fc_gcode').find(':selected').val());
		dataToPost.gdesc 	= (typeof $('#fc_gcode').find(':selected').val() === 'undefined'?'':$('#fc_gcode').find(':selected').text());
		dataToPost.menuyr 	= $('#fc_menuyr').val();
		dataToPost.bonus 	= $('#fc_bonus').val();
		dataToPost.stat 	= (typeof $('#fc_stat').find(':selected').val() === 'undefined'?'':$('#fc_stat').find(':selected').val());
		dataToPost.statname = (typeof $('#fc_stat').find(':selected').val() === 'undefined'?'':$('#fc_stat').find(':selected').text());
		dataToPost.memo1 	= $('#fc_memo1').val();
		
		$error = 0;
		$('.del_newcar').each(function(){
			if($(this).attr('strno') == dataToPost.strno){
				$error = 1;
			}
		});
		
		if($error == 0){
			jdfc_add = $.ajax({
				url: '../SYS02/CreceiveStock/dataSTRNO',
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
							msg: data.html
						});
					}else{
						var tbnewstock = $("#table-newstock").DataTable();
						tbnewstock.row.add(data.html).draw();
						
						$thisASTRNO.destroy();
					}
					
					jdfc_add = null;
				},
				beforeSend: function(){
					if(jdfc_add !== null){ jdfc_add.abort(); }
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
				msg: 'ผิดพลาด มีเลขตัวถังนี้อยู่แล้ว'
			});
		}
	});
}

$('#btnt1receiveStock').click(function(){
	dataToPost = new Object();
	dataToPost.action = "manual";
	dataToPost.obj = "";
	loadReceived(dataToPost);
});

var OBJbtnt1UploadStock=null;
$('#btnt1UploadStock').click(function(){
	OBJbtnt1UploadStock = $.ajax({
		url:'../SYS02/CreceiveStock/FormUPLOAD',
		type: 'POST',
		dataType: 'json',
		beforeSend: function(){ if(OBJbtnt1UploadStock !== null){ OBJbtnt1UploadStock.abort(); } },
		success: function(data){
			Lobibox.window({
				title: 'รับรถเข้าสต๊อค (Upload)',
				//width: $(window).width(),
				height: '200',
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					$("#form_std").uploadFile({		
						url:'../SYS02/CreceiveStock/checkFileUpload',
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
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: obj["errorMassage"]
								});
							}else{
								dataToPost = new Object();
								dataToPost.action = "autoupload";
								dataToPost.obj = JSON.stringify(obj["data"]);
								loadReceived(dataToPost);

								$this.destroy();
							}
							
							$("#loadding").fadeOut(200);
						}
					});
					
					$("#form_import").unbind('click');
					$("#form_import").click(function(){
						window.open("../public/form_upload/upload_cycle_multiple.xlsx");
					});
				}
			});		
			
			OBJbtnt1UploadStock = null;
		}
	});			
});























