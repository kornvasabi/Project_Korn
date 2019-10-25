/********************************************************
             ______@08/03/2019______
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
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCOD').find(':selected').val();
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	if(_insert == 'T'){
		$('#btnt1reserve').attr('disabled',false);
	}else{
		$('#btnt1reserve').attr('disabled',true);
	}
});

var divcondition = $(".divcondition").height() ;

$('#btnt1search').click(function(){
	dataToPost = new Object()
	dataToPost.RESVNO = $("#RESVNO").val();
	dataToPost.SRESVDT = $("#SRESVDT").val();
	dataToPost.ERESVDT = $("#ERESVDT").val();
	dataToPost.STRNO  = $("#STRNO").val();
	dataToPost.CUSCOD = $("#CUSCOD").val();
	
	$('#loadding').fadeIn(500);
	$.ajax({
		url:'../SYS04/ReserveCar/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$("#result").html(data.html);
			
			$('#table-ReserveCar').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-ReserveCar',1,250);
			
			// Export data to Excel
			$('.data-export').prepend('<img id="table-ReserveCar-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-ReserveCar-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ใบจอง","ReserveCar"); 
			});
			
			function redraw(){
				$('.resvnoClick').unbind('click');
				$('.resvnoClick').click(function(){
					fn_load_formResv($(this),'edit');
				});
			}
			
			$('#loadding').fadeOut(100);
		}
	});
});

$('#btnt1reserve').click(function(){
	fn_load_formResv($(this),'add');
});

function fn_load_formResv($this,$event){
	dataToPost = new Object();
	dataToPost.RESVNO = (typeof $this.attr('RESVNO') === 'undefined' ? '':$this.attr('RESVNO'));
	dataToPost.EVENT = $event;
	
	$('#loadding').fadeIn(250);
	$.ajax({
		url:'../SYS04/ReserveCar/getfromReserve',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'รายการจองรถ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					fn_loadPropoties($this,data.EVENT);
					
					$('#loadding').fadeOut(100);
				}
			});
		}
	});
}


function fn_loadPropoties($thisWindow,$EVENT){
	//$('#fRESVNO').attr('disabled',true);
	//$('#fRESVNO').val('Auto Genarate');
	
	$('#fLOCAT').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fLOCAT').find(':selected').val() === 'undefined' ? '' : $('#fLOCAT').find(':selected').val());
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
	
	$('#fCUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fCUSCOD').find(':selected').val() === 'undefined' ? '' : $('#fCUSCOD').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				$('#loadding').fadeIn(200);
				return dataToPost;				
			},
			dataType: 'json',
			delay: 250,
			processResults: function (data) {
				$('#loadding').fadeOut(200);
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: (_level == 1 ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fCUSCOD').on("select2:select",function(){
		//
	});
	
	$('#fRECVCD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fRECVCD').find(':selected').val() === 'undefined' ? '' : $('#fRECVCD').find(':selected').val());
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
		//disabled: (_level == 1 ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fSALCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#fSALCOD').find(':selected').val() === 'undefined' ? '' : $('#fSALCOD').find(':selected').val());
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
		disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fSTRNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fSTRNO').find(':selected').val() === 'undefined' ? '' : $('#fSTRNO').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#fLOCAT').find(':selected').val() === 'undefined' ? '' : $('#fLOCAT').find(':selected').val());
				
				dataToPost.GCODE = (typeof $('#fGRPCOD').find(':selected').val() === 'undefined' ? '' : $('#fGRPCOD').find(':selected').val());
				dataToPost.TYPE  = (typeof $('#fTYPE').find(':selected').val() === 'undefined' ? '' : $('#fTYPE').find(':selected').val());
				dataToPost.MODEL = (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '' : $('#fMODEL').find(':selected').val());
				dataToPost.BAAB  = (typeof $('#fBAAB').find(':selected').val() === 'undefined' ? '' : $('#fBAAB').find(':selected').val());
				dataToPost.COLOR = (typeof $('#fCOLOR').find(':selected').val() === 'undefined' ? '' : $('#fCOLOR').find(':selected').val());
				dataToPost.STAT = (typeof $('#fSTAT').find(':selected').val() === 'undefined' ? '' : $('#fSTAT').find(':selected').val());
				
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDfSTRNO_select = null;
	$('#fSTRNO').on("select2:select",function(){
		dataToPost = new Object();
		dataToPost.STRNO = (typeof $('#fSTRNO').find(':selected').val() === 'undefined' ? '' : $('#fSTRNO').find(':selected').val());
		
		JDfSTRNO_select = $.ajax({
			url:'../SYS04/ReserveCar/getSTRNOSelect',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
							
				newOption = new Option(data.GDESC, data.GCODE, false, false);
				$('#fGRPCOD').empty().append(newOption).trigger('change');
				newOption = new Option(data.TYPE, data.TYPE, false, false);
				$('#fTYPE').empty().append(newOption).trigger('change');
				newOption = new Option(data.MODEL, data.MODEL, false, false);
				$('#fMODEL').empty().append(newOption).trigger('change');
				newOption = new Option(data.BAAB, data.BAAB, false, false);
				$('#fBAAB').empty().append(newOption).trigger('change');
				newOption = new Option(data.COLOR, data.COLOR, false, false);
				$('#fCOLOR').empty().append(newOption).trigger('change');
				newOption = new Option(data.CC, data.CC, false, false);
				$('#fCC').empty().append(newOption).trigger('change');
				$('#fSTAT').val(data.STAT).trigger('select2:select'); //event select action
				$('#fSTAT').val(data.STAT).trigger('change'); // change form interface selected
				
				JDfSTRNO_select = null;
			},
			beforeSend: function(){
				if(JDfSTRNO_select !== null){
					JDfSTRNO_select.abort();
				}
			}
		});
	});
	
	$('#fACTICOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fACTICOD').find(':selected').val() === 'undefined' ? '' : $('#fACTICOD').find(':selected').val());
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fACTICOD').on("select2:select",function(){
		$('#fSTAT').trigger('select2:select');
		
		setTimeout(function(){
			fn_balance();
		},250);
	});
	
	$('#fACTICOD').on("select2:unselect",function(){
		$('#fPRICE').attr('stdid','');
		$('#fPRICE').attr('stdplrank','');
		$('#fPRICE').val('');
		
		fn_balance();
		//$('#fSTAT').trigger('select2:select');
	});
		
	$('#fGRPCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getGCode',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fGRPCOD').find(':selected').val() === 'undefined' ? '' : $('#fGRPCOD').find(':selected').val());
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fTYPE').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTYPES',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fTYPE').find(':selected').val() === 'undefined' ? '' : $('#fTYPE').find(':selected').val());
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
		allowClear: true,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fMODEL').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '' : $('#fMODEL').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = (typeof $('#fTYPE').find(':selected').val() === 'undefined' ? '' : $('#fTYPE').find(':selected').val());
			
				if($('#fTYPE').find(':selected').val() === 'undefined'){
					alert('undefined');
				}
			
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fMODEL').on("select2:opening",function(){
		if($('#fGRPCOD').find(':selected').val() === 'undefined'){
			alert('fGRPCOD undefined');
		}
		if($('#fTYPE').find(':selected').val() === 'undefined'){
			alert('fTYPE undefined');
		}
	});
	
	$('#fBAAB').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fBAAB').find(':selected').val() === 'undefined' ? '' : $('#fBAAB').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = (typeof $('#fTYPE').find(':selected').val() === 'undefined' ? '' : $('#fTYPE').find(':selected').val());
				dataToPost.MODEL = (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '' : $('#fMODEL').find(':selected').val());
				
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fCOLOR').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fCOLOR').find(':selected').val() === 'undefined' ? '' : $('#fCOLOR').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
				dataToPost.model = (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '' : $('#fMODEL').find(':selected').val());
				dataToPost.baab	 = (typeof $('#fBAAB').find(':selected').val() === 'undefined' ? '' : $('#fBAAB').find(':selected').val());
				
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
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fCC').select2({
		placeholder: 'เลือก',        
		allowClear: true,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fSTAT').select2({
		placeholder: 'เลือก',        
		allowClear: false,
		multiple: false,
		dropdownParent: $(".lobibox-body"),
		minimumResultsForSearch: -1,
		allowClear: true,
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#fRESVDT').change(function(){
		$('#fSTAT').trigger('select2:select');
	});
	
	var JDfSTAT_select = null;
	$('#fSTAT').on("select2:select",function(){
		$valued = $(this).find(':selected').val();
		
		if($valued == 'N'){
			dataToPost = new Object();
			dataToPost.RESVDT   = $('#fRESVDT').val();
			dataToPost.ACTICOD  = (typeof $('#fACTICOD').find(':selected').val() === "undefined" ? "":$('#fACTICOD').find(':selected').val());
			dataToPost.ACTIDES  = (typeof $('#fACTICOD').find(':selected').val() === "undefined" ? "":$('#fACTICOD').find(':selected').text());
			dataToPost.MODEL 	= (typeof $('#fMODEL').find(':selected').val() === "undefined" ? "":$('#fMODEL').find(':selected').val());
			dataToPost.BAAB  	= (typeof $('#fBAAB').find(':selected').val() === "undefined" ? "":$('#fBAAB').find(':selected').val());
			dataToPost.COLOR 	= (typeof $('#fCOLOR').find(':selected').val() === "undefined" ? "":$('#fCOLOR').find(':selected').val());
			
			JDfSTAT_select = $.ajax({
				url:'../SYS04/ReserveCar/getStandart',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					
					if(data.error){
						$('#fPRICE').val('');
						$('#fPRICE').attr('stdid','');
						$('#fPRICE').attr('stdplrank','');
						$('#fPRICE').attr('disabled',false);
						$('#fBALANCE').val('');
						
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
						$('#fPRICE').val(data.price);
						$('#fPRICE').attr('stdid',data.stdid);
						$('#fPRICE').attr('stdplrank',data.stdplrank);
						
						if($('#fRESPAY').val() == ''){
							$('#fRESPAY').val();
							$('#fRESPAY').focus();
							$('#fBALANCE').val(data.price);
						}else{
							var bl = data.price - $('#fRESPAY').val();
							$('#fBALANCE').val(bl);
						}						
					}
					
					$('#fPRICE').attr('disabled',true);
					JDfSTAT_select = null;
				},
				beforeSend: function(){
					if(JDfSTAT_select !== null){
						JDfSTAT_select.abort();
					}
				}
			});
		}else{
			$('#fPRICE').val('');
			$('#fPRICE').attr('disabled',false);
			$('#fPRICE').focus();
		}
	});
	
	$('#fSTAT').on("select2:unselect",function(){
		$('#fPRICE').val('');
		$('#fPRICE').attr('disabled',false);
		$('#fPRICE').focus();
	});
	
	$('#fPRICE').focusout(function(){ fn_balance(); });
	$('#fRESPAY').focusout(function(){ fn_balance(); });
	
	var jd_fn_balance = null;
	function fn_balance(){
		dataToPost = new Object();
		dataToPost.PRICE  = $('#fPRICE').val();
		dataToPost.RESPAY = $('#fRESPAY').val();
		
		//$("#loadding").fadeIn(250);
		jd_fn_balance = $.ajax({
			url:'../SYS04/ReserveCar/setBalance',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#fPRICE').val(data.PRICE);
				$('#fRESPAY').val(data.RESPAY)
				$('#fBALANCE').val(data.BALANCE);
				
				//$("#loadding").fadeOut(100);
				jd_fn_balance = null;
			},
			beforeSend: function(){
				if(jd_fn_balance !== null){
					jd_fn_balance.abort();
				}
			}
		});
	}
	
	$('#btnSave').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: 'คุณต้องการบันทึกบิลจอง ?',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: 'บันทึก',
					closeOnClick: false,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					fn_save($thisWindow,lobibox);
				}
			}
		});
	});	
	
	if($EVENT == 'add'){
		$('#fCC').val(null).trigger('change');
		$('#fSTAT').val(null).trigger('change');
		$('#btnDelete').hide();
	}else{
		if(_level == 1){
			$('#fRESVDT').attr('disabled',false);
			$('#fCUSCOD').attr('disabled',false);
			$('#fACTICOD').attr('disabled',false);
			$('#fGRPCOD').attr('disabled',false);
			$('#fTYPE').attr('disabled',false);
			$('#fMODEL').attr('disabled',false);
			$('#fBAAB').attr('disabled',false);
			$('#fCOLOR').attr('disabled',false);
			$('#fCC').attr('disabled',false);
			$('#fSTAT').attr('disabled',false);
			$('#fPRICE').attr('disabled',false);
			$('#fRESPAY').attr('disabled',false);
		}else{
			$('#fRESVDT').attr('disabled',true);
			$('#fCUSCOD').attr('disabled',true);
			$('#fACTICOD').attr('disabled',true);
			$('#fGRPCOD').attr('disabled',true);
			$('#fTYPE').attr('disabled',true);
			$('#fMODEL').attr('disabled',true);
			$('#fBAAB').attr('disabled',true);
			$('#fCOLOR').attr('disabled',true);
			$('#fCC').attr('disabled',true);
			$('#fSTAT').attr('disabled',true);
			$('#fPRICE').attr('disabled',true);
			$('#fRESPAY').attr('disabled',true);
		}
		
		if(_update == 'T'){
			$('#btnSave').attr('disabled',false);
		}else{
			$('#btnSave').attr('disabled',true);
		}
		
		$('#btnDelete').show();
		if(_delete == 'T'){
			$('#btnDelete').attr('disabled',false);
		}else{
			$('#btnDelete').attr('disabled',true);
		}
	}

	var jd_btnDelete = null;
	$('#btnDelete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeOnEsc: false,
			closeButton: false,
			msg: 'คุณต้องการลบบิลจองเลขที่ :: '+$("#fRESVNO").val()+' ?',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: 'บันทึก',
					closeOnClick: false,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.RESVNO = $("#fRESVNO").val();
					
					jd_btnDelete = $.ajax({
						url:'../SYS04/ReserveCar/DeletedRESV',
						data: dataToPost,
						type:'POST',
						dataType: 'json',
						success: function(data){
							var noti = 'success'; 
							if(data.error){ noti = 'warning'; } 
							
							Lobibox.notify(noti, {
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
							if(!data.error){ 
								$thisWindow.destroy(); 
							} 
							jd_btnDelete = null;
						},
						beforeSend: function(){
							if(jd_btnDelete !== null){
								jd_btnDelete.abort();
							}
						}
					});
				}
			}
		});
	});
}

var JD_fn_save = null;
function fn_save($thisWindow,lobibox){
	dataToPost = new Object();
	dataToPost.RESVNO 	= $('#fRESVNO').val();
	dataToPost.RESVDT	= $('#fRESVDT').val();
	dataToPost.LOCAT 	= (typeof $('#fLOCAT').find(':selected').val() === 'undefined' ? '':$('#fLOCAT').find(':selected').val());
	dataToPost.CUSCOD 	= (typeof $('#fCUSCOD').find(':selected').val() === 'undefined' ? '':$('#fCUSCOD').find(':selected').val());
	dataToPost.RECVCD 	= (typeof $('#fRECVCD').find(':selected').val() === 'undefined' ? '':$('#fRECVCD').find(':selected').val());
	dataToPost.SALCOD 	= (typeof $('#fSALCOD').find(':selected').val() === 'undefined' ? '':$('#fSALCOD').find(':selected').val());
	dataToPost.VATRT 	= $('#fVATRT').val();
	dataToPost.TAXNO 	= $('#fTAXNO').val();
	dataToPost.TAXDT 	= $('#fTAXDT').val();
	dataToPost.STRNO 	= (typeof $('#fSTRNO').find(':selected').val() === 'undefined' ? '':$('#fSTRNO').find(':selected').val());
	dataToPost.ACTICOD 	= (typeof $('#fACTICOD').find(':selected').val() === 'undefined' ? '':$('#fACTICOD').find(':selected').val());
	dataToPost.GCODE 	= (typeof $('#fGRPCOD').find(':selected').val() === 'undefined' ? '':$('#fGRPCOD').find(':selected').val());
	dataToPost.TYPE 	= (typeof $('#fTYPE').find(':selected').val() === 'undefined' ? '':$('#fTYPE').find(':selected').val());
	dataToPost.MODEL 	= (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '':$('#fMODEL').find(':selected').val());
	dataToPost.BAAB 	= (typeof $('#fBAAB').find(':selected').val() === 'undefined' ? '':$('#fBAAB').find(':selected').val());
	dataToPost.COLOR 	= (typeof $('#fCOLOR').find(':selected').val() === 'undefined' ? '':$('#fCOLOR').find(':selected').val());
	dataToPost.CC 		= (typeof $('#fCC').find(':selected').val() === 'undefined' ? '':$('#fCC').find(':selected').val());
	dataToPost.STAT 	= (typeof $('#fSTAT').find(':selected').val() === 'undefined' ? '':$('#fSTAT').find(':selected').val());
	dataToPost.PRICE 	= $('#fPRICE').val();
	dataToPost.STDID 	= (typeof $('#fPRICE').attr('stdid') === 'undefined' ? '':$('#fPRICE').attr('stdid'));
	dataToPost.STDPLRANK = (typeof $('#fPRICE').attr('stdplrank') === 'undefined' ? '':$('#fPRICE').attr('stdplrank'));
	dataToPost.RESPAY 	= $('#fRESPAY').val();
	dataToPost.BALANCE 	= $('#fBALANCE').val();
	dataToPost.RECVDUE 	= $('#fRECVDUE').val();
	dataToPost.RECVDT 	= $('#fRECVDT').val();
	dataToPost.SMPAY 	= $('#fSMPAY').val();
	dataToPost.SMCHQ 	= $('#fSMOWE').val();
	dataToPost.MEMO1 	= $('#fMEMO1').val();
	
	$('#loadding').fadeIn(500);
	JD_fn_save = $.ajax({
		url:'../SYS04/ReserveCar/SaveRESV',
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
				
				$thisWindow.destroy();
			}
			JD_fn_save = null;
			lobibox.destroy();
			$('#loadding').fadeOut(100);
		},
		beforeSend: function(){
			if(JD_fn_save !== null){
				JD_fn_save.abort();
			}
		}
	});
}



























