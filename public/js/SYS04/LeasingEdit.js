/********************************************************
             ______@23/10/2019______
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

$("#LOCAT").select2({
	placeholder: 'เลือก',
	ajax: {
		url: '../Cselect2/getLOCAT',
		data: function (params) {
			dataToPost = new Object();
			dataToPost.now = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
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
	dropdownParent: $(".tab1"),
	//disabled: true,
	//theme: 'classic',
	width: '100%'
});

$("#RESVNO").select2({
	placeholder: 'เลือก',
	ajax: {
		url: '../Cselect2/getRESVNO',
		data: function (params) {
			dataToPost = new Object();
			dataToPost.now = (typeof $('#RESVNO').find(':selected').val() === 'undefined' ? '' : $('#RESVNO').find(':selected').val());
			dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
			dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '' : $('#LOCAT').find(':selected').val());
			
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
	dropdownParent: $(".tab1"),
	//disabled: true,
	//theme: 'classic',
	width: '100%'
});

var jd_btnt1search=null;
$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.CUSCOD  = $('#CUSCOD').val();
	dataToPost.CUSNAME = $('#CUSNAME').val();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.STRNO   = $('#STRNO').val();
	dataToPost.RESVNO  = (typeof $('#RESVNO').find(':selected').val() === 'undefined' ? '':$('#RESVNO').find(':selected').val());
	
	$('#loadding').fadeIn(200);
	jd_btnt1search = $.ajax({
		url:'../SYS04/Leasing/searchEdit',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#jd_result').html(data.html);
			
			$('#table-LE').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-LE',1,250);			
			
			$('#loadding').fadeOut(200);
			jd_btnt1search = null;
		},
		beforeSend: function(){
			if(jd_btnt1search !== null){
				jd_btnt1search.abort();
			}
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function redraw(){
	var jd_leasingEdit = null;
	$('.leasingEdit').click(function(){
		$('.leasingEdit').attr('disabled',true);
		
		dataToPost = new Object();
		dataToPost.contno = ($(this).attr('contno'));
		
		$('#loadding').fadeIn(200);
		jd_leasingEdit = $.ajax({
			url:'../SYS04/Leasing/getFormEdit',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				Lobibox.window({
					title: 'บันทึกเปลี่ยนรหัสลูกค้าหรือเปลี่ยนสถานะสัญญาเช่าซื้อ',
					width: $(window).width(),
					height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,
					shown: function($this){
						fn_afterchoose($this);
					},
					beforeClose : function(){
						$('.leasingEdit').attr('disabled',false);
					}
				});
				
				$('#loadding').fadeOut(200);
				jd_leasingEdit = null;
			},
			beforeSend: function(){
				if(jd_leasingEdit !== null){
					jd_leasingEdit.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
}


function fn_afterchoose($thisWindow){
	//$('#table-aroth').on('draw.dt',function(){ redraw(); });
	fn_datatables('table-aroth',2,250,'YES');	
	
	$('#uieCHECKER').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieCHECKER').find(':selected').val() === 'undefined'?'':$('#uieCHECKER').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uieBILLCOLL').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieBILLCOLL').find(':selected').val() === 'undefined'?'':$('#uieBILLCOLL').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uieSALCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieSALCOD').find(':selected').val() === 'undefined'?'':$('#uieSALCOD').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uieACTICOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieACTICOD').find(':selected').val() === 'undefined'?'':$('#uieACTICOD').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uiePAYCODE').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uiePAYCODE').find(':selected').val() === 'undefined'?'':$('#uiePAYCODE').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uieCUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieCUSCOD').find(':selected').val() === 'undefined'?'':$('#uieCUSCOD').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#uieCONTSTAT').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTYPCONT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#uieCONTSTAT').find(':selected').val() === 'undefined'?'':$('#uieCONTSTAT').find(':selected').val());
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
		dropdownParent: $('.lobibox-body'),
		//disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var jd_UIESave = null;
	$('#UIESave').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการ<span style="color:red;">แก้ไขเลขที่สัญญา '+$('#uiesCONTNO').val()+' หรือไม่</span>',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน, แก้ไข',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก. ไว้ก่อน',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.CONTNO 	= $("#uiesCONTNO").val();
					dataToPost.CONTSTAT = (typeof $("#uieCONTSTAT").find(':selected').val() === 'undefined' ? '' : $("#uieCONTSTAT").find(':selected').val());
					dataToPost.CHECKER 	= (typeof $("#uieCHECKER").find(':selected').val() === 'undefined' ? '' : $("#uieCHECKER").find(':selected').val());
					dataToPost.ACTICOD 	= (typeof $("#uieACTICOD").find(':selected').val() === 'undefined' ? '' : $("#uieACTICOD").find(':selected').val());
					dataToPost.BILLCOLL = (typeof $("#uieBILLCOLL").find(':selected').val() === 'undefined' ? '' : $("#uieBILLCOLL").find(':selected').val());
					dataToPost.PAYCODE 	= (typeof $("#uiePAYCODE").find(':selected').val() === 'undefined' ? '' : $("#uiePAYCODE").find(':selected').val());
					dataToPost.SALCOD 	= (typeof $("#uieSALCOD").find(':selected').val() === 'undefined' ? '' : $("#uieSALCOD").find(':selected').val());
					
					dataToPost.DELYRT 	= $("#uieDELYRT").val();
					dataToPost.DLDAY 	= $("#uieDLDAY").val();
					dataToPost.CUSCOD 	= (typeof $("#uieCUSCOD").find(':selected').val() === 'undefined' ? '' : $("#uieCUSCOD").find(':selected').val());
					dataToPost.CALINT 	= $("input:radio[name=uieCALINT]:checked").val();
					dataToPost.CALDSC 	= $("input:radio[name=uieCALDSC]:checked").val();
					dataToPost.DAS	 	= $("#uieMEMO1").attr('das');
					dataToPost.MEMO1 	= $("#uieMEMO1").val();
					
					$('#loadding').fadeIn(200);
					jd_UIESave = $.ajax({
						url:'../SYS04/Leasing/EditCONTNO',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							if(data.error){
								Lobibox.notify('error', {
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
								
								$thisWindow.destroy();
							}
							$('#loadding').fadeOut(200);
							jd_UIESave = null;
						},
						error: function (x,c,b){
							Lobibox.notify('error', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: x.status +' '+ b
							});
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){
							if(jd_UIESave !== null){
								jd_UIESave.abort();
							}
						}
					});
				}
			}
		});
	});
	
}
























