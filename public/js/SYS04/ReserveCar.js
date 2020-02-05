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
			fn_datatables('table-ReserveCar',1,350);
			
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
				
				$('.getit').hover(function(){
					$(this).css({'background-color':'yellow'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
			}
			
			$('#loadding').fadeOut(100);
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
	
	$('#fCUSCOD_removed').click(function(){
		$('#fCUSCOD').attr('CUSCOD','');
		$('#fCUSCOD').val('');
	});
	
	$('#fCUSCOD').click(function(){
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url:'../Cselect2/getfromCUSTOMER',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#fCUSCOD').attr('disabled',true);
				$('#btnSave').attr('disabled',true);
				
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
							data.fname = $('#cus_fname').val();
							data.lname = $('#cus_lname').val();
							data.idno = $('#cus_idno').val();
							
							let use = new Object();
							use.recomcod = $('#add_recomcod').attr('CUSCOD');
							data.inuse = use;
							
							$('#loadding').fadeIn(200);
							jd_cus_search = $.ajax({
								url:'../Cselect2/getResultCUSTOMER',
								data:data,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#cus_result').html(data.html);
									
									$('.CUSDetails').unbind('click');
									$('.CUSDetails').click(function(){
										dtp = new Object();
										dtp.cuscod  = $(this).attr('CUSCOD');
										dtp.cusname = $(this).attr('CUSNAMES');
										
										$('#fCUSCOD').attr('CUSCOD',dtp.cuscod);
										$('#fCUSCOD').val(dtp.cusname);
										
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
						$('#fCUSCOD').attr('disabled',false);
						$('#btnSave').attr('disabled',false);
					}
				});
				
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
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
				dataToPost.now 	 = (typeof $('#fSTRNO').find(':selected').val() === 'undefined' ? $('#btncantStrno').attr('strno') : $('#fSTRNO').find(':selected').val());
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
		allowClear: false,
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
				/*
				newOption = new Option(data.CC, data.CC, false, false);
				$('#fCC').empty().append(newOption).trigger('change');
				*/
				$('#fCC').val(data.CC).trigger('change');
				$('#fSTAT').val(data.STAT).trigger('select2:select'); //event select action
				$('#fSTAT').val(data.STAT).trigger('change'); // change form interface selected
				
				JDfSTRNO_select = null;
			},
			beforeSend: function(){
				if(JDfSTRNO_select !== null){
					JDfSTRNO_select.abort();
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	var jdbtncantStrno = null;
	$('#btncantStrno').click(function(){
		$('#fSTRNO').empty().trigger('changed');
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
	
	$('#fACTICOD ,#fGRPCOD ,#fTYPE ,#fMODEL ,#fBAAB ,#fCOLOR').on("select2:select",function(){ restd(); });
	
	
	function restd(){
		$('#fSTAT').trigger('select2:select');
		setTimeout(function(){ fn_balance(); },250);
	}
	
	$('#fACTICOD').on("select2:unselect",function(){
		$('#fPRICE').attr('stdid','');
		$('#fPRICE').attr('stdplrank','');
		$('#fPRICE').val('');
		
		setTimeout(function(){ fn_balance(); },250);
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
			url: '../Cselect2/getJDCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now 	 = (typeof $('#fCOLOR').find(':selected').val() === 'undefined' ? '' : $('#fCOLOR').find(':selected').val());
				dataToPost.q 	 = (typeof params.term === 'undefined' ? '' : params.term);
				
				dataToPost.MODEL = (typeof $('#fMODEL').find(':selected').val() === 'undefined' ? '' : $('#fMODEL').find(':selected').val());
				dataToPost.BAAB	 = (typeof $('#fBAAB').find(':selected').val() === 'undefined' ? '' : $('#fBAAB').find(':selected').val());
				
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
		
		
			dataToPost = new Object();
			dataToPost.RESVDT   = $('#fRESVDT').val();
			dataToPost.ACTICOD  = (typeof $('#fACTICOD').find(':selected').val() === "undefined" ? "":$('#fACTICOD').find(':selected').val());
			dataToPost.ACTIDES  = (typeof $('#fACTICOD').find(':selected').val() === "undefined" ? "":$('#fACTICOD').find(':selected').text());
			dataToPost.MODEL 	= (typeof $('#fMODEL').find(':selected').val() === "undefined" ? "":$('#fMODEL').find(':selected').val());
			dataToPost.BAAB  	= (typeof $('#fBAAB').find(':selected').val() === "undefined" ? "":$('#fBAAB').find(':selected').val());
			dataToPost.COLOR 	= (typeof $('#fCOLOR').find(':selected').val() === "undefined" ? "":$('#fCOLOR').find(':selected').val());
			dataToPost.LOCAT 	= (typeof $('#fLOCAT').find(':selected').val() === "undefined" ? "":$('#fLOCAT').find(':selected').val());
			dataToPost.GCODE 	= (typeof $('#fGRPCOD').find(':selected').val() === "undefined" ? "":$('#fGRPCOD').find(':selected').val());
			dataToPost.STAT 	= $valued;
			
			JDfSTAT_select = $.ajax({
				url:'../SYS04/ReserveCar/getStandard',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					
					if(data.error){
						$('#fPRICE').val('');
						$('#fPRICE').attr('stdid','');
						$('#fPRICE').attr('subid','');
						$('#fPRICE').attr('disabled',false);
						$('#fBALANCE').val('');
						
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
					}else{
						$('#fPRICE').val(data.PRICE);
						$('#fPRICE').attr('stdid',data.STDID);
						$('#fPRICE').attr('subid',data.SUBID);
						
						if($('#fRESPAY').val() == ''){
							$('#fRESPAY').val();
							$('#fRESPAY').focus();
							$('#fBALANCE').val(data.price);
						}else{
							var bl = data.price - ($('#fRESPAY').val()).replace(',','');
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
				},
				error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
			});
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
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
		$('#btnDelete').hide(0);
		$('#btnClear').show(0);
	}else{
		$('#btnClear').hide(0);
		if(_level == 1){
			$('#fRESVDT').attr('disabled',false);
			$('#fCUSCOD').attr('disabled',false);
			$('#fCUSCOD_removed').attr('disabled',false);
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
			$('#fCUSCOD_removed').attr('disabled',true);
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
		
		$('#btnDelete').show(0);
		if(_delete == 'T'){
			$('#btnDelete').attr('disabled',false);
		}else{
			$('#btnDelete').attr('disabled',true);
		}
	}
	
	
	$('#btnClear').click(function(){
		$('#fCUSCOD').attr('CUSCOD','');
		$('#fCUSCOD').val('');
		$('#fSTRNO').val(null).trigger('change');
		$('#fACTICOD').val(null).trigger('change');
		$('#fGRPCOD').val(null).trigger('change');
		$('#fMODEL').val(null).trigger('change');
		$('#fBAAB').val(null).trigger('change');
		$('#fCOLOR').val(null).trigger('change');
		$('#fCC').val(null).trigger('change');
		$('#fSTAT').val('').trigger('change');
		$('#fPRICE').val();
		$('#fRESPAY').val();
		$('#fBALANCE').val();
		$('#fRECVDUE').val();
	});

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
	dataToPost.CUSCOD 	= $('#fCUSCOD').attr('CUSCOD');
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
	dataToPost.SUBID	= (typeof $('#fPRICE').attr('subid') === 'undefined' ? '':$('#fPRICE').attr('subid'));
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
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}



























