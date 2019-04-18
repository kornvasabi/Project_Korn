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
				dataToPost.now = $('#add_cuscod').find(':selected').val();
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
});

$('#btnt1search').click(function(){
	$('#btnt1search').attr('disabled',true);
	dataToPost = new Object();
	dataToPost.contno 	= $('#CONTNO').val();
	dataToPost.sdatefrm = $('#SDATEFRM').val();
	dataToPost.sdateto 	= $('#SDATETO').val();
	dataToPost.locat 	= $('#LOCAT').val();
	dataToPost.strno 	= $('#STRNO').val();
	dataToPost.cuscod 	= (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? '' : $('#CUSCOD').find(':selected').val());
	
	$('#loadding').show();
	$.ajax({
		url:'../SYS04/Leasing/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			
			Lobibox.window({
				title: 'รายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					//wizard();		
					$('.leasingDetails').click(function(){
						leasingDetails($(this).attr('contno'),'search');
					});
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
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
			$('#loadding').hide();
		}
	});
});

function leasingDetails($contno,$event){
	dataToPost = new Object();
	dataToPost.contno = $contno;
	
	$('#loadding').show();
	$.ajax({
		url:'../SYS04/Leasing/loadLeasing',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			//load form leasing
			loadLeasing(data);
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

function loadLeasing($param){
	$('#loadding').show();
	$.ajax({
		url:'../SYS04/Leasing/getfromLeasing',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					wizard('old',$param,$this);
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});			
		}
	});
}
	
$('#btnt1leasing').click(function(){
	$('#btnt1leasing').attr('disabled',true);
	$('#loadding').show();
	$.ajax({
		url:'../SYS04/Leasing/getfromLeasing',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').hide();
			Lobibox.window({
				title: 'บันทึกรายการเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard('new','',$this);
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});			
		}
	});
});

function wizard($param,$dataLoad,$thisWindowLeasing){
	//$thisWindowLeasing.destroy(); return;
	$('#add_contno').val('Auto Genarate');
	$('#add_contno').attr('readonly',true);
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
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
		dropdownParent: (_level == 1 ? $("#wizard-leasing") : true),
		disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_resvno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getRESVNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_resvno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_cuscod').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_cuscod').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_inclvat').select2({ 
		dropdownParent: $("#wizard-leasing"), 
		minimumResultsForSearch: -1,
		width: '100%'
	});
	
	$('#add_addrno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_addrno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = $('#add_cuscod').find(':selected').val();
				
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_strno').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_paydue').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_paydue').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_cuscod').change(function(){
		dataToPost = new Object();
		dataToPost.cuscod = $(this).find(':selected').val();
		
		$.ajax({
			url:'../SYS04/Leasing/checkCustomer',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if(data.GRADE == "F" || data.GRADE == "FF"){
					Lobibox.notify('error', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "รหัสลูกค้า "+dataToPost.cuscod+" เกรดลูกหนี้เป็น "+data.GRADE+" ไม่สามารถปล่อยสินเชื่อได้ครับ"
					});
					$('#add_cuscod').empty().trigger('change');					
					$('#add_addrno').empty().trigger('change');
				}else if(data.GRADE == ""){
					$('#add_addrno').empty().trigger('change');
				}
				
				var resvno = (typeof $("#add_resvno").find(':selected').val() === 'undefined' ? '' : $("#add_resvno").find(':selected').val());
				if(data.ARRESV != "" && resvno == ""){
					Lobibox.window({
						title: 'รายการบิลจอง',
						//width: setwidth,
						//height: '300',
						draggable: false,
						content: data.ARRESV,
						closeOnEsc: false,
						shown: function($this){
							$('.cusinresv').click(function(){
								var resvno = $(this).attr('resvno');
								var newOption = new Option(resvno, resvno, true, true);
								$('#add_resvno').empty().append(newOption).trigger('change');
								
								$this.destroy();
							});
						}
					});
				}
			}
		});
	});
	
	$('#add_resvno').change(function(){
		dataToPost = new Object();
		dataToPost.resvno = (typeof $(this).find(':selected').val() === 'undefined' ? '' : $(this).find(':selected').val());
		
		$.ajax({
			url:'../SYS04/Leasing/resvnoChanged',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if(data.SMCHQ > 0 || data.msg != ""){
					$('#add_cuscod').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					$('#add_cuscod').empty().trigger('change');
					$('#add_addrno').empty().trigger('change');
					
					$('#add_strno').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					$('#add_strno').empty().trigger('change');
					
					Lobibox.notify('error', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else if(data.RESVNO == ""){
					$('#add_cuscod').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getCUSTOMERS',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.now = $('#add_cuscod').find(':selected').val();
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
						dropdownParent: $("#wizard-leasing"),
						disabled: false,
						//theme: 'classic',
						width: '100%'
					});
					$('#add_cuscod').empty().trigger('change');
					$('#add_addrno').empty().trigger('change');
					
					$('#add_strno').select2({ 
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getSTRNO',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.now = $('#add_strno').find(':selected').val();
								dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
								dataToPost.locat = $('#add_locat').find(':selected').val();
								
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
						dropdownParent: $("#wizard-leasing"),
						disabled: false,
						//theme: 'classic',
						width: '100%'
					});
					$('#add_strno').empty().trigger('change');
				}else{
					$('#add_cuscod').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					var newOption = new Option(data.CUSNAME, data.CUSCOD, true, true);
					$('#add_cuscod').empty().append(newOption).trigger('change');
					$('#add_addrno').empty().trigger('change');
					
					$('#add_strno').select2({
						dropdownParent: true,
						disabled: true,
						width:'100%'
					});
					var newOption = new Option(data.STRNO, data.STRNO, true, true);
					$('#add_strno').empty().append(newOption).trigger('change');
				}
			}
		});
	});
	
	document.getElementById("dataTable-fixed-inopt").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	$('#add_inopt').click(function(){
		$('#add_inopt').attr('disabled',true);
		
		$('#loadding').show();
		
		$.ajax({
			url: '../SYS04/Leasing/getFormInopt',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').hide();
				Lobibox.window({
					title: 'เพิ่มอุปกรณ์เสริม',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					shown: function($this){
						//$this.destroy();		
						$('#op_code').select2({ 
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getOPTMAST',
								data: function (params) {
									dataToPost = new Object();
									dataToPost.now = $('#op_code').find(':selected').val();
									dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
									dataToPost.locat = $('#add_locat').find(':selected').val();
									
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
							dropdownParent: $("#inoptform"),
							//disabled: true,
							//theme: 'classic',
							width: '100%'
						});
						
						$('#receipt_inopt').hide();
						$('#cal_inopt').click(function(){
							dataToPost = new Object();
							dataToPost.qty 	   = $('#op_qty').val();
							dataToPost.uprice  = $('#op_uprice').val();
							dataToPost.cvt     = $('#op_cvt').val();
							dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '' : $('#add_inclvat').find(':selected').val());
							dataToPost.vatrt   = $('#add_vatrt').val();
							dataToPost.opCode  = (typeof $('#op_code').find(':selected').val() === 'undefined' ? '' : $('#op_code').find(':selected').val());
							dataToPost.opText  = (typeof $('#op_code').find(':selected').text() === 'undefined' ? '' : $('#op_code').find(':selected').text());
							
							$.ajax({
								url: '../SYS04/Leasing/calculate_inopt',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									if(data.status){
										$('#receipt_inopt').attr({
											'price1' : data["1price"].replace(',','')
											,'vat1'  : data["1vat"].replace(',','')
											,'total1': data["1total"].replace(',','')
											,'price2': data["2price"].replace(',','')
											,'vat2'  : data["2vat"].replace(',','')
											,'total2': data["2total"].replace(',','')
											,'opCode': $('#op_code').find(':selected').val()
											,'opText': $('#op_code').find(':selected').text()
											,'qty'  : data["qty"].replace(',','')
											,'uprice': data["uprice"].replace(',','')
										});
										
										$('#inopt_results').html(data.html);
										$('#receipt_inopt').show();
									}else{
										$('#inopt_results').html(data.html);
										$('#receipt_inopt').hide();
									}
								}
							});
						});
						
						$('#receipt_inopt').click(function(){
							var opCode = $(this).attr('opCode');
							var opText = $(this).attr('opText');
							var price1 = $(this).attr('price1');
							var vat1   = $(this).attr('vat1');
							var total1 = $(this).attr('total1');
							var price2 = $(this).attr('price2');
							var vat2   = $(this).attr('vat2');
							var total2 = $(this).attr('total2');
							var qty	   = $(this).attr('qty');
							var uprice = $(this).attr('uprice');
							
							var stat = true;
							$('.inoptTab2').each(function(){
								if(opCode == $(this).attr('opCode')){
									stat = false;
								}
							});
							
							if(stat){
								var row = '<tr seq="new">';							
								row += "<td align='center'> ";
								row += "	<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
								row += "		opCode='"+opCode+"' total1='"+total1+"' total2='"+total2+"' ";
								row += "		price1='"+price1+"' price2='"+price2+"' vat1='"+vat1+"' ";
								row += "		vat2='"+vat2+"' qty='"+qty+"' uprice='"+uprice+"' ";
								row += "		style='cursor:pointer;'> ลบ   ";
								row += "	</i> ";
								row += "</td> ";
								row += "<td>"+opText+"</td>";
								row += "<td class='text-right'>"+uprice.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+qty.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+price1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+vat1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+total1.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+price2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+vat2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += "<td class='text-right'>"+total2.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+"</td>";
								row += '</tr>';
								
								$('#dataTables-inopt tbody').append(row);
								
								var sumTotal1 = 0;
								var sumTotal2 = 0;
								$('.inoptTab2').each(function(){
									sumTotal1 += parseFloat($(this).attr('total1'));
									sumTotal2 += parseFloat($(this).attr('total2'));
								});
															
								$('#add2_optsell').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
								$('#add2_optcost').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
								
								inopt_remove();
								$this.destroy();
							}else{
								Lobibox.notify('warning', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: 'ผิดพลาดรหัสอุปกรณ์เสริม '+opCode+' มีอยู่แล้ว ไม่สามารถเพิ่มซ้ำได้ครับ'
								});
							}
						});
					},
					beforeClose : function(){
						$('#add_inopt').attr('disabled',false);
					}
				});
			}
		});
	});
	
	$('#add_emp').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_emp').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_audit').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_audit').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_empSell').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_empSell').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_acticod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_acticod').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_advisor').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_acticod').find(':selected').val();
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#btn_addBillDas').click(function(){
		var data = false;
		$('.add_billdas').each(function(){
			if(typeof $(this).find(':selected').val() === 'undefined'){
				data = true;
			}
		});
		
		if(data){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 15000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
				soundExt: '.ogg',
				icon: true,
				messageHeight: '90vh',
				msg: 'ไม่สามารถดึงบิลเพิ่มได้<br>เนื่องจากคุณยังมีช่องที่ไม่ได้เลือกบิลอยู่ครับ'
			});
		}else{
			var rank = 'in'+$('.add_billdas').length;	
			var billdas = "<select class='add_billdas form-control input-sm chosen-select' process='' rank='"+rank+"' data-placeholder='เลขที่บิล'></select>";
			$('#formBillDas').append(billdas);
			
			fn_billdasActive(rank);
		}
	});
	
	
	$('#add_mgar').click(function(){
		$('#add_mgar').attr('disabled',true);
		$('#loadding').show();
		
		$.ajax({
			url: '../SYS04/Leasing/getFormMGAR',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').hide();
				Lobibox.window({
					title: 'เพิ่มผู้ค้ำประกัน',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					shown: function($this){
						$('#mgar_cuscod').select2({
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getCUSTOMERS',
								data: function (params) {
									dataToPost = new Object();
									dataToPost.now = $('#mgar_cuscod').find(':selected').val();
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
							dropdownParent: $("#mgarform"),
							//disabled: true,
							//theme: 'classic',
							width: '100%'
						});
						
						$('#mgar_cuscod').change(function(){
							$('#mgar_addrno').empty().trigger('change');
						});
						
						$('#mgar_addrno').select2({
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getCUSTOMERSADDRNo',
								data: function (params) {
									dataToPost = new Object();
									dataToPost.now = $('#mgar_addrno').find(':selected').val();
									dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
									dataToPost.cuscod = $('#mgar_cuscod').find(':selected').val();
									
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
							dropdownParent: $("#mgarform"),
							//disabled: true,
							//theme: 'classic',
							width: '100%'
						});
						
						$('#mgar_receipt').click(function(){
							var cuscod = (typeof $('#mgar_cuscod').find(':selected').val() === 'undefined' ? '' : $('#mgar_cuscod').find(':selected').val());
							var cusval = $('#mgar_cuscod').find(':selected').text();
							var addrno = (typeof $('#mgar_addrno').find(':selected').val() === 'undefined' ? '' : $('#mgar_addrno').find(':selected').val());
							var relation = $('#mgar_relation').val();
							
							var stat = true;
							$('.mgarTab5').each(function(){
								if(cuscod == $(this).attr('cuscod')){
									stat = false;
								}
							});
							
							if(stat){
								$msg = "";
								if(relation == ''){ $msg = "ความสัมพันธ์คนค้ำ"; }
								if(addrno   == ''){ $msg = "ที่อยู่ผู้ค้ำ"; }
								if(cuscod   == ''){ $msg = "รหัส/ชื่อ ผู้ค้ำประกัน"; }
								
								if($msg != ""){
									Lobibox.notify('warning', {
										title: 'แจ้งเตือน',
										size: 'mini',
										closeOnClick: false,
										delay: 15000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										msg: 'คุณยังไม่ได้ระบุ'+$msg+' โปรดระบุ'+$msg+' ก่อนครับ'
									});	
								}else{
									var row = '<tr seq="new">';							
									row += "<td align='center'> ";
									row += "	<i class='mgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
									row += "		position='"+($('.mgarTab5').length + 1)+"' cuscod='"+cuscod+"' cusval='"+cusval+"' relation='"+relation+"' ";
									row += "		style='cursor:pointer;'> ลบ   ";
									row += "	</i> ";
									row += "</td> ";
									row += "<td>"+($('.mgarTab5').length + 1)+"</td>";
									row += "<td>"+cusval+"</td>";
									row += "<td>"+relation+"</td>";
									row += '</tr>';
									
									$('#dataTable_ARMGAR tbody').append(row);
									
									mgar_remove();
									$this.destroy();									
								}
							}else{
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: 'ผิดพลาดมีรหัสผู้ค้ำ '+cusval+' อยู่แล้ว ไม่สามารถเพิ่มซ้ำได้ครับ'
								});
							}
						});
					},
					beforeClose : function(){
						$('#add_mgar').attr('disabled',false);
					}
				});
			}
		});
	});
	
	$('#add_othmgar').click(function(){
		$('#add_othmgar').attr('disabled',true);
		$('#loadding').show();
		
		$.ajax({
			url: '../SYS04/Leasing/getFormOTHMGAR',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').hide();
				Lobibox.window({
					title: 'เพิ่มผู้ค้ำประกัน',
					//width: $(window).width(),
					//height: 220,
					content: data,
					draggable: true,
					closeOnEsc: true,
					shown: function($this){
						$('#othmgar_garcod').select2({
							placeholder: 'เลือก',
							allowClear: false,
							multiple: false,
							dropdownParent: $("#othmgarform"),
							minimumResultsForSearch: -1,
							//disabled: true,
							//theme: 'classic',
							width: '100%'
							
						});
						
						$('#othmgar_receipt').click(function(){
							var garcod = (typeof $('#othmgar_garcod').find(':selected').val() === 'undefined' ? '' :$('#othmgar_garcod').find(':selected').val());
							var garval = (typeof $('#othmgar_garcod').find(':selected').text() === 'undefined' ? '' :$('#othmgar_garcod').find(':selected').text());
							var refno  = $('#othmgar_refno').val();
							
							var stat = true;
							$('.othmgarTab5').each(function(){
								if(garcod == $(this).attr('garcod') && refno == $(this).attr('refno')){
									stat = false;
								}
							});
							
							if(stat){
								$msg = "";
								if(garcod == ''){ $msg = "รหัสหลักทรัพย์"; }
								if(refno  == ''){ $msg = "เลขที่อ้างอิง"; }
								
								if($msg != ""){
									Lobibox.notify('warning', {
										title: 'แจ้งเตือน',
										size: 'mini',
										closeOnClick: false,
										delay: 15000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										msg: 'คุณยังไม่ได้ระบุ'+$msg+' โปรดระบุ'+$msg+' ก่อนครับ'
									});	
								}else{
									var row = '<tr seq="new">';							
									row += "<td align='center'> ";
									row += "	<i class='othmgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
									row += "		position='"+($('.othmgarTab5').length + 1)+"' garcod='"+garcod+"' garval='"+garval+"' refno='"+refno+"' ";
									row += "		style='cursor:pointer;'> ลบ   ";
									row += "	</i> ";
									row += "</td> ";
									row += "<td>"+($('.othmgarTab5').length + 1)+"</td>";
									row += "<td>"+garval+"</td>";
									row += "<td>"+refno+"</td>";
									row += '</tr>';
									
									$('#dataTable_AROTHGAR tbody').append(row);
									
									othmgar_remove();
									$this.destroy();									
								}
							}else{
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 15000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: 'ผิดพลาดมีรหัสหลักทรัพย์ '+garval+' เลขที่อ้างอิง '+refno+' อยู่แล้ว ไม่สามารถเพิ่มซ้ำได้ครับ'
								});
							}
							
						});
					},
					beforeClose : function(){
						$('#add_othmgar').attr('disabled',false);
					}
				});
			}
		});	
	});
	
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	
	function initPage(){
		$('#wizard-leasing').bootstrapWizard({
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
				
				var sdate = $('#add_sdate').val();
				var cuscod = (typeof $('#add_cuscod').find(':selected').val() === 'undefined' ? '' : $('#add_cuscod').find(':selected').val());
				var cuscodaddr = (typeof $('#add_addrno').find(':selected').val() === 'undefined' ? '' : $('#add_addrno').find(':selected').val());
				var strno = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
				var paydue = (typeof $('#add_paydue').find(':selected').val() === 'undefined' ? '' : $('#add_paydue').find(':selected').val());
				
				switch(index){
					case 0: //tab1
						$msg = "";
						
						if(paydue 		== ''){ $msg = "ไม่พบวิธีชำระค่างวด โปรดระบุวิธีชำระค่างวดก่อนครับ"; }
						if(strno 		== ''){ $msg = "ไม่พบเลขตัวถัง โปรดระบุเลขตัวถังก่อนครับ"; }
						if(cuscodaddr 	== ''){ $msg = "ไม่พบที่อยู่ในการพิมพ์สัญญา โปรดระบุที่อยู่ในการพิมพ์สัญญาก่อนครับ"; }
						if(cuscod 		== ''){ $msg = "ไม่พบรหัสลูกค้า โปรดระบุรหัสลูกค้าก่อนครับ"; }
						if(sdate 		== ''){ $msg = "ไม่พบวันที่ขาย โปรดระบุวันที่ขายก่อนครับ"; }
						
						if($msg != ""){
							Lobibox.notify('warning', {
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
							
							return false;
						}else{ 
							nextTab(ind2); 
						}
						
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
	
	$('#add_inprcCal').click(function(){
		dataToPost = new Object();
		dataToPost.strno = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
		dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '' : $('#add_inclvat').find(':selected').val());
		dataToPost.vatrt = $('#add_vatrt').val();
		
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/getFormCalNopay',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').hide();
				if(data.status){
					Lobibox.window({
						title: 'คำนวนค่างวด',
						width: $(window).width(),
						height: $(window).height(),
						content: data.msg,
						draggable: true,
						closeOnEsc: true,
						shown: function($this){
							var incv = {
								"results": [
									{
									  "id": 'Y',
									  "text": 'รวม VAT'
									},
									{
									  "id": 'N',
									  "text": 'แยก VAT'
									}
								]
							};
							
							$('#calc_vatrt').val($('#add_vatrt').val() == "" ? '0.00' : $('#add_vatrt').val());
							
							$('#calc_incvat').select2({
								data: incv.results,
								disabled: false,
								dropdownParent: $(".win2"), 
								minimumResultsForSearch: -1,
								width: '100%',
							});
							$('#calc_incvat').val($('#add_inclvat').find(':selected').val()).trigger('change');
							
							$('#calc_installment').select2({
								disabled: false,
								dropdownParent: $(".win2"), 
								minimumResultsForSearch: -1,
								width: '100%',
							});
							
							$('#calc_decimal').select2({
								disabled: false,
								dropdownParent: $(".win2"), 
								minimumResultsForSearch: -1,
								width: '100%',
							});
							
							$('#calc_incvat').change(function(){
								if($(this).find(':selected').val() == 'Y'){
									if(!$('#span_npricev').hasClass('text-info')){
										$('#span_npricev').addClass('text-info');
									}
									if($('#span_nprice').hasClass('text-info')){
										$('#span_nprice').removeClass('text-info');
									}
									$('#calc_npricev').attr('disabled',false);
									$('#calc_nprice').attr('disabled',true);
									
									if(!$('#span_ndownv').hasClass('text-info')){
										$('#span_ndownv').addClass('text-info');
									}
									if($('#span_ndown').hasClass('text-info')){
										$('#span_ndown').removeClass('text-info');
									}
									$('#calc_ndownv').attr('disabled',false);
									$('#calc_ndown').attr('disabled',true);
									
									if(!$('#span_npricevOpt').hasClass('text-info')){
										$('#span_npricevOpt').addClass('text-info');
									}
									if($('#span_npriceOpt').hasClass('text-info')){
										$('#span_npriceOpt').removeClass('text-info');
									}
									$('#calc_npricevOpt').attr('disabled',false);
									$('#calc_npriceOpt').attr('disabled',true);
								}else{
									if(!$('#span_nprice').hasClass('text-info')){
										$('#span_nprice').addClass('text-info');
									}
									if($('#span_npricev').hasClass('text-info')){
										$('#span_npricev').removeClass('text-info');
									}
									$('#calc_nprice').attr('disabled',false);
									$('#calc_npricev').attr('disabled',true);
									
									if(!$('#span_ndown').hasClass('text-info')){
										$('#span_ndown').addClass('text-info');
									}
									if($('#span_ndownv').hasClass('text-info')){
										$('#span_ndownv').removeClass('text-info');
									}
									$('#calc_ndown').attr('disabled',false);
									$('#calc_ndownv').attr('disabled',true);
									
									if(!$('#span_npriceOpt').hasClass('text-info')){
										$('#span_npriceOpt').addClass('text-info');
									}
									if($('#span_npricevOpt').hasClass('text-info')){
										$('#span_npricevOpt').removeClass('text-info');
									}
									$('#calc_npriceOpt').attr('disabled',false);
									$('#calc_npricevOpt').attr('disabled',true);
								}
								
								__decss(); //load script css in VIEW disabled and enabled.
							});
							
							$('#calc_installment').change(function(){
								if($(this).find(':selected').val() == 'Y'){
									if(!$('#span_vatyear').hasClass('text-info')){
										$('#span_vatyear').addClass('text-info');
									}
									if($('#span_vatmonth').hasClass('text-info')){
										$('#span_vatmonth').removeClass('text-info');
									}
									$('#calc_vatyear').attr('disabled',false);
									$('#calc_vatmonth').attr('disabled',true);
								}else{
									if(!$('#span_vatmonth').hasClass('text-info')){
										$('#span_vatmonth').addClass('text-info');
									}
									if($('#span_vatyear').hasClass('text-info')){
										$('#span_vatyear').removeClass('text-info');
									}
									$('#calc_vatmonth').attr('disabled',false);
									$('#calc_vatyear').attr('disabled',true);
								}
							});
							
							$('#btnCalculate').click(function(){ fnCalculate(); });
							$('#calc_decimal').change(function(){ fnCalculate(); });
							
							function fnCalculate(){
								dataToPost = new Object();
								dataToPost.nprice 	= $('#calc_nprice').val();
								dataToPost.npricev 	= $('#calc_npricev').val();
								dataToPost.ndownv 	= $('#calc_ndownv').val();
								dataToPost.ndown 	= $('#calc_ndown').val();
								dataToPost.nopay 	= $('#calc_nopay').val();
								dataToPost.nopays 	= $('#calc_nopays').val();
								dataToPost.vatyear 	= $('#calc_vatyear').val();
								dataToPost.vatmonth = $('#calc_vatmonth').val();
								
								dataToPost.npriceOpt 	= $('#calc_npriceOpt').val();
								dataToPost.npricevOpt 	= $('#calc_npricevOpt').val();
								
								dataToPost.incvat 	= $('#calc_incvat').find(':selected').val();
								dataToPost.vatrt 	= $('#calc_vatrt').val();
								
								dataToPost.installment 	= $('#calc_installment').find(':selected').val();
								dataToPost.decimal	 	= $('#calc_decimal').find(':selected').val();
								dataToPost.resvno	 	= (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '' : $('#add_resvno').find(':selected').val());
								
								$.ajax({
									url:'../SYS04/Leasing/getFormCalNopayCalculate',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data) {
										if(data.status){
											$('#calc_npricev').val(data.ds1);
											$('#calc_nprice').val(data.ds2);
											$('#calc_ndownv').val(data.ds3);
											$('#calc_ndown').val(data.ds4);
											$('#calc_debtor').val(data.ds5);
											$('#calc_debtorv').val(data.ds6);
											$('#calc_nopay').val(data.ds7);
											$('#calc_nopays').val(data.ds8);
											$('#calc_vatyear').val(data.ds9);
											$('#calc_vatmonth').val(data.ds10);
											$('#calc_vatall').val(data.ds11);
											$('#calc_sellBvat').val(data.ds12);
											$('#calc_installmentn').val(data.ds13);
											$('#calc_sellvat').val(data.ds14);
											$('#calc_installmentv').val(data.ds15);
											$('#calc_sellvatLast').val(data.ds16);
											$('#calc_installmentvLast').val(data.ds17);
											
											$('#calc_npricevOpt').val(data.do1);
											$('#calc_npriceOpt').val(data.do2);
											$('#calc_ndownvOpt').val(data.do3);
											$('#calc_ndownOpt').val(data.do4);
											$('#calc_debtorvOpt').val(data.do5);
											$('#calc_debtorOpt').val(data.do6);
											$('#calc_nopayOpt').val(data.do7);
											$('#calc_nopaysOpt').val(data.do8);
											$('#calc_vatyearOpt').val(data.do9);
											$('#calc_vatmonthOpt').val(data.do10);
											$('#calc_vatallOpt').val(data.do11);
											$('#calc_sellBvatOpt').val(data.do12);
											$('#calc_installmentnOpt').val(data.do13);
											$('#calc_sellvatOpt').val(data.do14);
											$('#calc_installmentvOpt').val(data.do15);
											$('#calc_sellvatLastOpt').val(data.do16);
											$('#calc_installmentvLastOpt').val(data.do17);
											
											$('#calc_totalSell').val(data.tt01);
											$('#calc_totalInstallment').val(data.tt02);											
										}else{
											Lobibox.notify('warning', {
												title: 'แจ้งเตือน',
												size: 'mini',
												closeOnClick: false,
												delay: 15000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
												soundExt: '.ogg',
												icon: true,
												messageHeight: '90vh',
												msg: data.msg
											});
										}
									}
								});
							}
							
							$('#btnReceived').click(function(){
								dataToPost = new Object();
								dataToPost.aincvat	= $('#add_inclvat').find(':selected').val();
								dataToPost.avatrt  	= $('#add_vatrt').val();
								dataToPost.cincvat	= $('#calc_incvat').find(':selected').val();
								dataToPost.cvatrt  	= $('#calc_vatrt').val();
								dataToPost.npricev  = $('#calc_npricev').val();
								dataToPost.nprice  	= $('#calc_nprice').val();
								dataToPost.ndownv 	= $('#calc_ndownv').val();
								dataToPost.ndown  	= $('#calc_ndown').val();
								dataToPost.debtorv	= $('#calc_debtorv').val();
								dataToPost.debtor	= $('#calc_debtor').val();
								dataToPost.nopay 	= $('#calc_nopay').val();
								dataToPost.nopays 	= $('#calc_nopays').val();
								dataToPost.vatyear 	= $('#calc_vatyear').val();
								dataToPost.vatmonth	= $('#calc_vatmonth').val();
								dataToPost.vatall 	= $('#calc_vatall').val();
								dataToPost.sellBvat = $('#calc_sellBvat').val();
								dataToPost.installmentn	= $('#calc_installmentn').val();								
								dataToPost.sellvatLast 	= $('#calc_sellvatLast').val();
								dataToPost.installmentvLast	= $('#calc_installmentvLast').val();
								dataToPost.sellBvatOpt		= $('#calc_sellBvatOpt').val();
								dataToPost.installmentnOpt	= $('#calc_installmentnOpt').val();
								dataToPost.sellvatLastOpt 	= $('#calc_sellvatLastOpt').val();
								dataToPost.installmentvLastOpt 	= $('#calc_installmentvLastOpt').val();
								dataToPost.totalSell 		= $('#calc_totalSell').val();
								dataToPost.totalInstallment = $('#calc_totalInstallment').val();
								dataToPost.strno = $('#add_strno').find(':selected').val();
								dataToPost.duefirst = $('#add_duefirst').val();
								
								$('#loadding').show();
								$.ajax({
									url:'../SYS04/Leasing/getReceivedCAL',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data) {
										$('#loadding').hide();
										$('#add_inprc').val(data.sell);
										$('#add_indwn').val(data.down);
										$('#add_nopay').val(data.nopay);
										$('#add_upay').val(data.upay);
										$('#add_payfirst').val(data.pay1);
										$('#add_paynext').val(data.pay2);
										$('#add_paylast').val(data.pay3);	
										
										$('#add_sell').val(data.STDPRC);
										$('#add_totalSell').val(data.sellFresh);
										$('#add_interest').val(data.interate);
										$('#add_intRate').val(data.INT_RATE);
										$('#add_delay').val(data.DELAY_DAY);
										
										$('#add_interestRate').val(data.vatyear);
										$('#add_interestRateReal').val(data.vatyearReal);
										
										$('#add_duelast').val(data.duelast);
										
										$this.destroy();
									}
								});								
							});
						}
					});
				}else{
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
						soundExt: '.ogg',
						icon: true,
						messageHeight: '90vh',
						msg: 'ไม่พบเลขตัวถัง โปรดระบุเลขตัวถังก่อนคำนวนค่างวดครับ'
					});
				}				
			}
		});
	});
	
	$('#add_detailsCond').click(function(){
		dataToPost = new Object();
		dataToPost.aincvat		= $('#add_inclvat').find(':selected').val();
		dataToPost.avatrt  		= $('#add_vatrt').val();
		dataToPost.ainprc  		= $('#add_inprc').val();
		dataToPost.aindwn  		= $('#add_indwn').val();
		dataToPost.aresvno		= (typeof $("#add_resvno").find(':selected').val() === 'undefined' ? '' : $("#add_resvno").find(':selected').val());
		dataToPost.apayfirst 	= $('#add_payfirst').val();
		dataToPost.apaynext	 	= $('#add_paynext').val();
		dataToPost.apaylast  	= $('#add_paylast').val();
		dataToPost.asell  		= $('#add_sell').val();
		dataToPost.atotalSell  	= $('#add_totalSell').val();
		dataToPost.ainterest  	= $('#add_interest').val();
		
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/getDetailsCond',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').hide();
				if(data.status){
					Lobibox.window({
						title: 'คำนวนค่างวด',
						//width: $(window).width(),
						//height: $(window).height(),
						content: data.html,
						draggable: true,
						closeOnEsc: true,
						shown: function($this){
							$('#add_detailsCond').attr('disabled',true);
						},
						beforeClose: function($this){
							$('#add_detailsCond').attr('disabled',false);
						}
					});
				}else{
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
						soundExt: '.ogg',
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}
			}
		});
	});

	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกการขายผ่อนหรือไม่',
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
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.contno = $('#add_contno').val();
					dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val() );
					dataToPost.sdate = $('#add_sdate').val();
					dataToPost.resvno = (typeof $('#add_resvno').find(':selected').val() === 'undefined' ? '':$('#add_resvno').find(':selected').val() );
					dataToPost.approve = $('#add_approve').val();
					dataToPost.cuscod = (typeof $('#add_cuscod').find(':selected').val() === 'undefined' ? '':$('#add_cuscod').find(':selected').val() );
					dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '':$('#add_inclvat').find(':selected').val() );
					dataToPost.vatrt  = $('#add_vatrt').val();
					dataToPost.addrno = $('#add_addrno').val();
					dataToPost.strno = $('#add_strno').val();
					dataToPost.reg = $('#add_reg').val();
					dataToPost.paydue = $('#add_paydue').val();
					
					var inopt = [];
					$('.inoptTab2').each(function(){
						var data = [];
						data.push($(this).attr('opCode'));
						data.push($(this).attr('uprice'));
						data.push($(this).attr('qty'));
						data.push($(this).attr('price1'));
						data.push($(this).attr('vat1'));
						data.push($(this).attr('total1'));
						data.push($(this).attr('price2'));
						data.push($(this).attr('vat2'));
						data.push($(this).attr('total2'));
						data.push($('#add2_optcost').val());
						data.push($('#add2_optsell').val());
						inopt.push(data);
					});
					
					dataToPost.inopt = inopt;
					
					dataToPost.inprc 	= $('#add_inprc').val();
					dataToPost.indwn 	= $('#add_indwn').val();
					dataToPost.dwninv 	= $('#add_dwninv').val();
					dataToPost.dwninvDt = $('#add_dwninvDt').val();
					dataToPost.nopay 	= $('#add_nopay').val();
					dataToPost.upay 	= $('#add_upay').val();
					
					dataToPost.payfirst = $('#add_payfirst').val();
					dataToPost.paynext 	= $('#add_paynext').val();
					dataToPost.paylast 	= $('#add_paylast').val();
					dataToPost.sell 	= $('#add_sell').val();
					dataToPost.totalSell = $('#add_totalSell').val();
					dataToPost.interest = $('#add_interest').val();
					
					dataToPost.duefirst = $('#add_duefirst').val();
					dataToPost.duelast 	= $('#add_duelast').val();
					dataToPost.release 	= $('#add_release').val();
					dataToPost.released = $('#add_released').val();
					dataToPost.emp 		= (typeof $('#add_emp').find(':selected').val() === 'undefined' ? '':$('#add_emp').find(':selected').val() );
					dataToPost.audit 	= (typeof $('#add_audit').find(':selected').val() === 'undefined' ? '':$('#add_audit').find(':selected').val() );
					dataToPost.intRate 	= $('#add_intRate').val();
					dataToPost.delay 	= $('#add_delay').val();
					dataToPost.interestRate 	= $('#add_interestRate').val();
					dataToPost.interestRateReal = $('#add_interestRateReal').val();
					dataToPost.empSell 	= $('#add_empSell').find(':selected').val();
					dataToPost.agent 	= $('#add_agent').val();
					dataToPost.acticod 	= (typeof $('#add_acticod').find(':selected').val() === 'undefined' ? '':$('#add_acticod').find(':selected').val() );
					dataToPost.nextlastmonth = ($('#add_nextlastmonth').is(':checked') ? 'Y':'N');
					
					dataToPost.advisor 	= (typeof $('#add_advisor').find(':selected').val() === 'undefined' ? '':$('#add_advisor').find(':selected').val() );
					dataToPost.paydown 	= $('#add_paydown').val();
					dataToPost.payall 	= $('#add_payall').val();
					dataToPost.commission = $('#add_commission').val();
					dataToPost.free 	= $('#add_free').val();
					dataToPost.payother = $('#add_payother').val();
					dataToPost.calint 	= $("input:radio[name=CALINT]:checked").val();
					dataToPost.discfm 	= $("input:radio[name=DISC_FM]:checked").val();
					dataToPost.comments = $('#add_comments').val();
					
					var billdas = [];
					$('.add_billdas').each(function(){
						billdas.push($(this).find(':selected').val());
					});		
					dataToPost.billdas = billdas;
					
					var mgar = [];
					$('.mgarTab5').each(function(){
						var data = [];
						data.push($(this).attr('position'));
						data.push($(this).attr('cuscod'));
						data.push($(this).attr('addrno'));
						data.push($(this).attr('relation'));			
						mgar.push(data);
					});
					dataToPost.mgar = mgar;
					
					var othmgar = [];
					$('.othmgarTab5').each(function(){
						var data = [];
						data.push($(this).attr('position'));
						data.push($(this).attr('garcod'));
						data.push($(this).attr('refno'));
						
						othmgar.push(data);
					});
					dataToPost.othmgar = othmgar;
					
					$('#loadding').show();
					$.ajax({
						url:'../SYS04/Leasing/save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').hide();
							
							if(data.status == 'S'){
								$thisWindowLeasing.destroy();
								
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
							}else if(data.status == 'W'){
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
							}else if(data.status == 'E'){
								Lobibox.notify('error', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
						}
					});
				}
			}
		});
	});
	
	if(_insert == 'T'){
		$('#add_save').attr('disabled',false);
	}else{
		$('#add_save').attr('disabled',true);
	}	
	$('#add_delete').attr('disabled',true);
	
	if($param == 'old'){
		//เอาข้อมูลที่โหลดมาแสดง
		permission($dataLoad,$thisWindowLeasing);
	}
}


function fn_billdasActive(rank){
	$('.add_billdas[rank='+rank+']').select2({ 
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getBILLDAS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $(this).find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);				
				
				dataToPost.locat = $('#add_locat').find(':selected').val();
				dataToPost.sdate = $('#add_sdate').val();
				
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
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:opening', function (e) {
		$(this).attr('process','use');
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:select', function(e){
		var thisData = $(this).find(':selected').val();
		
		if(typeof thisData !== 'undefined' && thisData != ''){
			var status = false;
			
			$('.add_billdas').each(function(){
				var process = $(this).attr('process');
				var this2Data = $(this).find(':selected').val();
				
				if(process == ""){
					if(this2Data == thisData){
						status = true;
					}
				}
			});
			
			if(status){					
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 15000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
					soundExt: '.ogg',
					icon: true,
					messageHeight: '90vh',
					msg: 'ไม่สามารถเลือกบิลซ้ำได้ครับ'
				});					
				$(this).val(null).trigger("change");
			}
		}
		
		$(this).attr('process','');
	});
	
	$('.add_billdas[rank='+rank+']').on('select2:close', function(e){
		if(typeof $(this).find(':selected').val() === 'undefined'){
			//alert('undi');
			var size = 0;
			$('.add_billdas').each(function(){ if(typeof $(this).find(':selected').val() === 'undefined'){ size += 1; } });
		
			$('.add_billdas').each(function(){								
				if(size > 1){
					$(this).select2('destroy');
					$(this).remove();
					size -= 1;
				}
			});
		}
		
		fn_calbilldas();
	});
	
	//$('.add_billdas[rank='+rank+']').on("select2:unselecting", function(e) { fn_calbilldas(); });
}

function fn_calbilldas(){
	$saleno = new Array();
	$('.add_billdas').each(function(){
		if(typeof $(this).find(':selected').val() !== 'undefined'){
			$saleno.push($(this).find(':selected').val());
		}
	});	
	
	if($saleno.length > 0){
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/calbilldas',
			data: {saleno:$saleno,locat:(typeof $("#add_locat").find(':selected').val() === 'undefined' ? '' : $("#add_locat").find(':selected').val())},
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#add_free').val(data.TotalAmt);
				
				var comment = $('#add_comments').val().split("\n");
				$('#add_comments').val(data.Details+"\n"+(typeof comment[1] === 'undefined' ? '' : comment[1]));
				$('#loadding').hide();
			}
		})
	}else{
		$('#add_free').val('0.00');
		var comment = $('#add_comments').val().split("\n");
		$('#add_comments').val((typeof comment[1] === 'undefined' ? '' : "\n"+comment[1]));
	}
}

function inopt_remove(){
	$('.inoptTab2').click(function(){
		$(this).parents().closest('tr').remove(); 
		
		var sumTotal1 = 0;
		var sumTotal2 = 0;
		$('.inoptTab2').each(function(){
			sumTotal1 += parseFloat($(this).attr('total1'));
			sumTotal2 += parseFloat($(this).attr('total2'));
		});
									
		$('#add2_optsell').val((sumTotal1.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
		$('#add2_optcost').val((sumTotal2.toFixed(2).toString()).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
	});
}

function mgar_remove(){
	$('.mgarTab5').click(function(){ 
		$(this).parents().closest('tr').remove(); 
		
		var start = 1;
		$('.mgarTab5').each(function(){
			/*เปลี่ยนลำดับที่ กรณีลบรายการ*/
			$(this).attr('position',start);
			$(this).parents().closest('tr').each(function(){
				this.cells[1].innerHTML = start;
			});	
			
			start += 1;
		});		
	});
}

function othmgar_remove(){
	$('.othmgarTab5').click(function(){
		$(this).parents().closest('tr').remove(); 
		
		var start = 1;
		$('.othmgarTab5').each(function(){
			/*เปลี่ยนลำดับที่ กรณีลบรายการ*/
			$(this).attr('position',start);
			$(this).parents().closest('tr').each(function(){
				this.cells[1].innerHTML = start;
			});	
			
			start += 1;
		});
	});
}

function permission($dataLoad,$thisWindowLeasing){
	$('#add_resvno').unbind('change'); //เพื่อไม่ให้ชื่อลูกค้า กับเลขที่สัญญาเปลี่ยน
	$('#add_cuscod').unbind('change'); //เพื่อแสดงข้อมูลการจองของลูกค้าคนนี้ กรณี ไม่ได้ใช้ใบจอง
	
	/*tab1*/
	$('#add_contno').val($dataLoad.CONTNO);
	var newOption = new Option($dataLoad.LOCAT, $dataLoad.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_sdate').val($dataLoad.SDATE);
	var newOption = new Option($dataLoad.RESVNO, $dataLoad.RESVNO, true, true);
	$('#add_resvno').empty().append(newOption).trigger('change');
	$('#add_approve').val($dataLoad.APPVNO);
	var newOption = new Option($dataLoad.CUSNAME, $dataLoad.CUSCOD, true, true);
	$('#add_cuscod').empty().append(newOption).trigger('change');
	$('#add_inclvat').val($dataLoad.INCLVAT).trigger('change');
	$('#add_vatrt').val($dataLoad.VATRT);
	var newOption = new Option($dataLoad.ADDRDetail, $dataLoad.ADDRNO, true, true);
	$('#add_addrno').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.STRNO, $dataLoad.STRNO, true, true);
	$('#add_strno').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.PAYDESC, $dataLoad.PAYTYP, true, true);
	$('#add_paydue').empty().append(newOption).trigger('change');
	/*tab2*/
	$('#add_inopt').attr('disabled',true);
	$('#dataTables-inopt tbody').empty().append($dataLoad.option);
	$('#add2_optcost').val($dataLoad.OPTCTOT);
	$('#add2_optsell').val($dataLoad.OPTPTOT);
	$('#add_inprc').val($dataLoad.KEYINPRC);
	$('#add_indwn').val($dataLoad.KEYINDWN);
	$('#add_dwninv').val($dataLoad.TAXNO);
	$('#add_dwninvDt').val($dataLoad.TAXDT);
	$('#add_nopay').val($dataLoad.T_NOPAY);
	$('#add_upay').val($dataLoad.T_UPAY);
	/*tab3*/
	$('#add_payfirst').val($dataLoad.KEYINFUPAY);
	$('#add_paynext').val($dataLoad.KEYINUPAY);
	$('#add_paylast').val($dataLoad.T_LUPAY);
	$('#add_sell').val($dataLoad.STDPRC);
	$('#add_totalSell').val($dataLoad.KEYINCSHPRC);
	$('#add_interest').val($dataLoad.NPROFIT);
	$('#add_duefirst').val($dataLoad.FDATE);
	$('#add_duelast').val($dataLoad.LDATE);
	$('#add_release').val($dataLoad.ISSUNO);
	$('#add_released').val($dataLoad.ISSUDT);
	var newOption = new Option($dataLoad.BILLNAME, $dataLoad.BILLCOLL, true, true);
	$('#add_emp').empty().append(newOption).trigger('change');
	var newOption = new Option($dataLoad.CHECKNAME, $dataLoad.CHECKER, true, true);
	$('#add_audit').empty().append(newOption).trigger('change');
	$('#add_intRate').val($dataLoad.DELYRT);
	$('#add_delay').val($dataLoad.DLDAY);
	$('#add_interestRate').val($dataLoad.INTRT);
	$('#add_interestRateReal').val($dataLoad.EFRATE);
	var newOption = new Option($dataLoad.SALNAME, $dataLoad.SALCOD, true, true);
	$('#add_empSell').empty().append(newOption).trigger('change');
	$('#add_agent').val($dataLoad.COMITN);
	var newOption = new Option($dataLoad.ACTINAME, $dataLoad.ACTICOD, true, true);
	$('#add_acticod').empty().append(newOption).trigger('change');
	/*tab4*/
	var newOption = new Option($dataLoad.RECOMNAME, $dataLoad.RECOMCOD, true, true);
	$('#add_advisor').empty().append(newOption).trigger('change');
	$('#add_paydown').val($dataLoad.PAYDWN);
	$('#add_payall').val($dataLoad.SMPAY);
	$('#add_commission').val($dataLoad.COMEXT);
	$('#add_free').val($dataLoad.COMOPT);
	$('#add_payother').val($dataLoad.COMOTH);
	$('input[name="CALINT"]').each(function(){
		if($(this).val() == $dataLoad.CALINT){
			$(this).prop('checked',true);
		}else{
			$(this).prop('checked',false);
		}		
	});
	$('input[name="DISC_FM"]').each(function(){
		if($(this).val() == $dataLoad.CALDSC){
			$(this).prop('checked',true);
		}else{
			$(this).prop('checked',false);
		}
	});
	
	var billDas = (typeof $dataLoad.billDAS === 'undefined' ? [] : $dataLoad.billDAS);
	for($i=0;$i<billDas.length;$i++){
		var billdas = "<select class='add_billdas form-control input-sm chosen-select' process='' rank='"+$i+"' data-placeholder='เลขที่บิล'><option value='"+billDas[$i]+"'>"+billDas[$i]+"</option></select>";
		$('#formBillDas').append(billdas);
		
		fn_billdasActive($i);
	}
	$('#add_comments').val($dataLoad.MEMO1);
	/*tab5*/
	$('#dataTable_ARMGAR tbody').empty().append($dataLoad.mgar);
	$('#dataTable_AROTHGAR tbody').empty().append($dataLoad.othmgar);
	mgar_remove();
	othmgar_remove();
	
	/******************************************************************
		Enabled,Disabled  or Other 
	*******************************************************************/
	$('#add_contno').attr('readonly',true);
	$('#add_contno').css({'color':'red'});
	$('#add_locat').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_sdate').attr('disabled',true);
	$('#add_resvno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_approve').attr('disabled',true);
	
	$('#add_cuscod').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_inclvat').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_vatrt').attr('disabled',true);
	$('#add_addrno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_strno').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_reg').attr('disabled',true);
	
	$('#add_inprc').attr('disabled',true);
	$('#add_inprcCal').unbind('click');
	$('#add_indwn').attr('disabled',true);
	$('#add_dwninv').attr('disabled',true);
	$('#add_dwninvDt').attr('disabled',true);
	$('#add_nopay').attr('disabled',true);
	$('#add_upay').attr('disabled',true);
	
	$('#add_payfirst').attr('disabled',true);
	$('#add_paynext').attr('disabled',true);
	$('#add_paylast').attr('disabled',true);
	$('#add_sell').attr('disabled',true);
	$('#add_totalSell').attr('disabled',true);
	$('#add_interest').attr('disabled',true);
	$('#add_duefirst').attr('disabled',true);
	$('#add_duelast').attr('disabled',true);
	$('#add_release').attr('disabled',true);
	$('#add_released').attr('disabled',true);
	$('#add_interestRateReal').attr('disabled',true);
	$('#add_nextlastmonth').attr('disabled',true);
	
	$('#add_empSell').select2({ dropdownParent: true,disabled: true,width:'100%' });
	
	if(_update == 'T'){
		$('#add_save').attr('disabled',false);
	}else{
		$('#add_paydue').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_emp').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_audit').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_intRate').attr('disabled',true);
		$('#add_delay').attr('disabled',true);
		$('#add_interestRate').attr('disabled',true);
		$('#add_agent').attr('disabled',true);
		$('#add_acticod').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_advisor').select2({ dropdownParent: true,disabled: true,width:'100%' });
		$('#add_commission').attr('disabled',true);
		$('#add_free').attr('disabled',true);
		$('#add_payother').attr('disabled',true);
		$("input:radio[name=CALINT]").attr('disabled',true);
		$("input:radio[name=DISC_FM]").attr('disabled',true);
		$('#add_comments').attr('disabled',true);
		$('#btn_addBillDas').attr('disabled',true);
		$('.add_billdas').attr('disabled',true);
		$('#add_mgar').attr('disabled',true);
		$('.mgarTab5').attr('disabled',true); 
		$('#add_othmgar').attr('disabled',true);
		$('.othmgarTab5').attr('disabled',true); 
		
		$('#add_save').attr('disabled',true);
	}
	
	if(_delete == 'T'){
		$('#add_delete').attr('disabled',false);
	}else{
		$('#add_delete').attr('disabled',true);
	}
	
	$('#btnArpay').attr('disabled',false);
	$('#btnSend').attr('disabled',false);
	$('#btnTax').attr('disabled',false);
	$('#btnApproveSell').attr('disabled',false);
	$('#btnContno').attr('disabled',false);
	$('#btnLock').attr('disabled',false);
	
	__decss(); //load script css in VIEW disabled and enabled.
	
	btnOther($thisWindowLeasing);
}

function btnOther($thisWindowLeasing){
	$('#btnArpay').click(function(){
		dataToPost = new Object();
		dataToPost.contno = $('#add_contno').val();
		
		$('#btnArpay').attr('disabled',true);
		
		$('#loadding').show();
		$.ajax({
			url:'../SYS04/Leasing/loadARPAY',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').hide();
				
				Lobibox.window({
					title: 'ตารางสัญญา',
					width: 'col-sm-10 col-sm-offset-1',
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,					
					shown: function($this){
						document.getElementById("dataTable-fixed-arpay").addEventListener("scroll", function(){
							var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
							this.querySelector("thead").style.transform = translate;
							this.querySelector("thead").style.zIndex = 100;
						});
					},
					beforeClose: function($this){
						$('#btnArpay').attr('disabled',false);
					}
				});
			}
		});
	});
		
	$('#add_delete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการ<span style="color:red;">ลบเลขที่สัญญา</span> '+$('#add_contno').val()+' หรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ลบ',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.contno = $('#add_contno').val();
					
					$('#loadding').show();
					
					$.ajax({
						url:'../SYS04/Leasing/deleteContno',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							$('#loadding').hide();
							
							if(data.status == 'S'){
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
								
								$thisWindowLeasing.destroy();
							}else{
								Lobibox.notify('error', {
									title: 'ผิดพลาด',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
						},
						error: function(){
							$('#loadding').hide();
						}
					});
				}
			}
		});
	});
	
	$('#btnApproveSell').click(function(){
		$('#btnApproveSell').attr('disabled',true);		
		
		var baseUrl = $('body').attr('baseUrl');
		var url = baseUrl+'SYS04/Leasing/approvepdf?contno='+$("#add_contno").val();
		var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
		
		Lobibox.window({
			title: 'ใบอนุมัติขาย',
			width: $(window).width(),
			height: $(window).height(),
			content: content,
			draggable: false,
			closeOnEsc: true,			
			beforeClose : function(){
				$('#btnApproveSell').attr('disabled',false);
			}
		});
	});
	
	$('#btnSend').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});	
	
	$('#btnTax').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});	
	
	$('#btnContno').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});
	
	$('#btnLock').click(function(){
		Lobibox.notify('info', {
			title: 'info',
			size: 'mini',
			closeOnClick: false,
			delay: 3000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: 'feature นี้ยังไม่สามารถใช้งานได้ครับ'
		});
	});
}










































