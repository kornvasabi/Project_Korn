/********************************************************
             ______@08/03/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
"use strict";
var _groupType  = $('.tab1[name="home"]').attr('groupType');
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
				var dataToPost = new Object();
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
	
	if(_groupType != "OFF"){
		$("#LOCAT").val(_locat);
		$("#LOCAT").attr('disabled',true);
	}else{
		$("#LOCAT").val(_locat);
		$("#LOCAT").attr('disabled',false);
	}
});

var jd_btnt1search = null;
$('#btnt1search').click(function(){
	var dataToPost = new Object();
	dataToPost.contno 	= $('#CONTNO').val();
	dataToPost.sdatefrm = $('#SDATEFRM').val();
	dataToPost.sdateto 	= $('#SDATETO').val();
	dataToPost.locat 	= $('#LOCAT').val();
	dataToPost.strno 	= $('#STRNO').val();
	dataToPost.cuscod 	= (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? '' : $('#CUSCOD').find(':selected').val());
	
	$('#loadding').fadeIn(200);
	jd_btnt1search = $.ajax({
		url:'../SYS04/Leasing/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#jd_result').html(data.html);
			$('#table-LeasingCar').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-LeasingCar',1,225);
			
			function redraw(){
				$('[data-toggle="tooltip"]').tooltip();
				$('.leasingDetails').unbind('click');
				$('.leasingDetails').click(function(){
					leasingDetails($(this).attr('contno'),'search');
				});
			}
			
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

function leasingDetails($contno,$event){
	var dataToPost = new Object();
	dataToPost.contno = $contno;
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../SYS04/Leasing/loadLeasing',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//load form leasing
			loadLeasing(data);
			
			$('#loadding').fadeOut(200);
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function loadLeasing($param){
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../SYS04/Leasing/getfromLeasing',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
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
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
	
$('#btnt1leasing').click(function(){
	$('#btnt1leasing').attr('disabled',true);
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../SYS04/Leasing/getfromLeasing',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
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
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
				var dataToPost = new Object();
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
				var dataToPost = new Object();
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
				var dataToPost = new Object();
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
		allowClear: true,
		multiple: false,
		dropdownParent: $("#wizard-leasing"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_approve').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getANALYZE',
			data: function (params) {
				var dataToPost = new Object();
				dataToPost.now = (typeof $('#add_approve').find(':selected').val() === 'undefined' ? '' : $('#add_approve').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
				
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
	
	var jd_dateChanged = null;
	$('#add_sdate').change(function(){ dateChanged(); });
	function dateChanged(){
		var dataToPost = new Object();
		dataToPost.sdate = $('#add_sdate').val();
		dataToPost.nopay = $('#add_nopay').val();
		
		$('#loadding').fadeIn(200);
		jd_dateChanged = $.ajax({
			url:'../SYS04/Leasing/dateChanged',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success: function(data){
				$('#add_released').val(data.released);
				$('#add_duefirst').val(data.fdate);
				$('#add_duelast').val(data.ldate);
				
				jd_dateChanged = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){
				if(jd_dateChanged !== null){ jd_dateChanged.abort(); }
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ 
				fnAjaxERROR(jqXHR,exception);
				$('#loadding').fadeOut(200);
			}
		});
	}
	
	var jd_add_approve = null;
	$('#add_approve').on("select2:select",function(){
		var dataToPost = new Object();
		dataToPost.ANID = $(this).find(':selected').val();
		
		$('#loadding').fadeIn(200);
		jd_add_approve = $.ajax({
			url:'../SYS04/Leasing/getDataANALYZE',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success: function(data){
				if(data.CUS_GRADE == "F" || data.CUS_GRADE == "FF"){
					Lobibox.notify('error', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "รหัสลูกค้า "+data.CUSCOD+" <br>เกรดลูกหนี้เป็น "+data.CUS_GRADE+" <br>ไม่สามารถปล่อยสินเชื่อได้ครับ"
					});
				}else{
					/*tab 1*/
					var newOption = new Option(data.RESVNO, data.RESVNO, true, true);
					$('#add_resvno').attr('disabled',true);
					$('#add_resvno').empty().append(newOption).trigger('change');	
					
					$('#add_approve').attr('model',data.MODEL);
					$('#add_approve').attr('baab',data.BAAB);
					$('#add_approve').attr('color',data.COLOR);
					$('#add_approve').attr('stat',data.STAT);
					
					var newOption = new Option(data.CUSNAME, data.CUSCOD, true, true);
					$('#add_cuscod').attr('disabled',true);
					$('#add_cuscod').empty().append(newOption).trigger('change');	
					
					var newOption = new Option(data.ADDRNODetails, data.ADDRDOCNO, true, true);
					$('#add_addrno').empty().append(newOption).trigger('change');	
					
					var newOption = new Option(data.STRNO, data.STRNO, true, true);
					//$('#add_strno').attr('disabled',true);
					$('#add_strno').empty().append(newOption).trigger('change');
					
					var newOption = new Option(data.ACTIDES, data.ACTICOD, true, true);
					$('#add_acticod').attr('disabled',true);
					$('#add_acticod').empty().append(newOption).trigger('change');
					
					/*tab 2*/
					$('#add_inprc').val(addCommas(data.PRICE_TOTAL));
					$('#add_indwn').val(addCommas(data.DWN));
					$('#add_nopay').val(data.NOPAY);
					$('#add_upay').val(data.NOPAYPerMonth);
					
					if(data.OPTCODE != "NOTUSE"){
						var ref_size = (typeof data.OPTCODE === 'undefined' ? 0 : Object.keys(data.OPTCODE).length); // นับว่ามีคนค้ำกี่คน
						$('#dataTables-inopt tbody').empty(); // เคลียข้อมูลอุปกรณ์เสริม
						var row = '<tr seq="new">';							
						row += "<td align='center'> ";
						row += "	<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
						row += "		opcode='"+data.OPTCODE+"' total1='0.00' total2='0.00' price1='0.00' price2='0.00' vat1='0.00' vat2='0.00' qty='1' uprice='0' ";
						row += "		style='cursor:pointer;'> ลบ   ";
						row += "	</i> ";
						row += "</td> ";
						row += '<td>'+data.OPTNAME+'</td>';
						row += '<td class="text-right">0</td>';
						row += '<td class="text-right">1</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '</tr>';
						
						$('#dataTables-inopt tbody').append(row);
						
						$('#add2_optcost').val('0.00');
						$('#add2_optsell').val('0.00');
					}
					
					/*tab 3*/
					$('#add_payfirst').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_paynext').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_paylast').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_sell').val(addCommas(data.TOTAL_CAROPT));
					$('#add_totalSell').val(addCommas(data.TOTAL_CAROPT));
					$('#add_interest').val(addCommas(data.HP_TOTAL));
					
					$('#add_intRate').val(data.INT_RATE);
					$('#add_delay').val(data.DELAY_DAY);
					$('#add_interestRate').val(data.INTERAST_RATE).attr('disabled',true);
					$('#add_interestRateReal').val(data.INTERAST_RATE_REAL).attr('disabled',true);
					
					/*tab 4*/
					$('#add_payother').val(addCommas(data.PAYOTHER));
					$('#add_comments').val(data.STD_OPT_TOTAL_DESC);
					
					/*tab 5*/
					var ref_size = (typeof data.REF === 'undefined' ? 0 : Object.keys(data.REF).length); // นับว่ามีคนค้ำกี่คน
					$('#dataTable_ARMGAR tbody').empty(); // เคลียข้อมูลคนค้ำ
					for(var i = 1;i<=ref_size;i++){
						var rank 	 = data.REF[i]['rank']; //ลำดับ
						var cuscod 	 = data.REF[i]['cuscod']; //รหัสลูกค้า
						var refname  = data.REF[i]['refname']; //ชื่อลูกค้า
						var relation = data.REF[i]['relation']; //ความสัมพันธ์
						
						var row = '<tr seq="new">';							
						row += "<td align='center'> ";
						row += "	<i class='mgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
						row += "		position='"+(rank)+"' cuscod='"+cuscod+"' cusval='"+refname+"' relation='"+relation+"' ";
						row += "		style='cursor:pointer;'> ลบ   ";
						row += "	</i> ";
						row += "</td> ";
						row += "<td>"+($('.mgarTab5').length + 1)+"</td>";
						row += "<td>"+refname+"</td>";
						row += "<td>"+relation+"</td>";
						row += '</tr>';
						
						$('#dataTable_ARMGAR tbody').append(row);
					}
					
					dateChanged(); //update วันที่ทำสัญญา ดิวเดต lastดิว วันที่ปล่อยรถ
				}
				
				jd_add_approve = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){
				if(jd_add_approve !== null){ jd_add_approve.abort(); }
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});	
	
	$('#add_approve').on("select2:unselect",function(){
		$('#add_approve').attr('model','');
		$('#add_approve').attr('baab','');
		$('#add_approve').attr('color','');
		$('#add_approve').attr('stat','');
		
		/*tab 1*/
		$('#add_resvno').attr('disabled',false);	
		$('#add_resvno').empty().trigger('change');	
		$('#add_cuscod').attr('disabled',false);
		$('#add_cuscod').empty().trigger('change');	
		$('#add_addrno').empty().trigger('change');	
		$('#add_strno').attr('disabled',false);
		$('#add_strno').empty().trigger('change');	
		
		/*tab 2*/
		$('#add_inprc').val('');
		$('#add_indwn').val('');
		$('#add_nopay').val('');
		$('#add_upay').val('');
		$('#dataTables-inopt tbody').empty();
		
		/*tab 3*/
		$('#add_payfirst').val('');
		$('#add_paynext').val('');
		$('#add_paylast').val('');
		$('#add_sell').val('');
		$('#add_totalSell').val('');		
		$('#add_interest').val('');
		
		$('#add_intRate').val('');
		$('#add_delay').val('');
		$('#add_interestRate').val('');
		$('#add_interestRateReal').val('');
		
		/*tab 5*/
		$('#dataTable_ARMGAR tbody').empty();
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
				var dataToPost = new Object();
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
				var dataToPost = new Object();
				dataToPost.now = $('#add_strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = $('#add_locat').find(':selected').val();
				
				dataToPost.MODEL = $('#add_approve').attr('model');
				dataToPost.BAAB  = $('#add_approve').attr('baab');
				dataToPost.COLOR = $('#add_approve').attr('color');
				dataToPost.STAT  = $('#add_approve').attr('stat');
				
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
	
	var jdadd_strno = null;
	$('#add_strno').on("select2:select",function(){
		var dataToPost = new Object();
		dataToPost.ANID  = (typeof $('#add_approve').find(':selected').val() === 'undefined' ?'': $('#add_approve').find(':selected').val());
		dataToPost.STRNO = (typeof $('#add_strno').find(':selected').val() === 'undefined' ?'': $('#add_strno').find(':selected').val());
		
		$('#loadding').fadeIn(200);
		jdadd_strno = $.ajax({
			url: '../SYS04/Leasing/getSTDSpecial',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				var data = data.html;
				if(data.error){
					Lobibox.notify('error', {
						title: 'ผิดพลาด',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "ผิดพลาด ไม่พบข้อมูลใบอนุมัติ / ข้อมูลสแตนดาร์ด"
					});
				}else if(data.analyze){
					/*tab 1*/
					var newOption = new Option(data.RESVNO, data.RESVNO, true, true);
					$('#add_resvno').attr('disabled',true);
					$('#add_resvno').empty().append(newOption).trigger('change');	
					
					$('#add_approve').attr('model',data.MODEL);
					$('#add_approve').attr('baab',data.BAAB);
					$('#add_approve').attr('color',data.COLOR);
					$('#add_approve').attr('stat',data.STAT);
					
					var newOption = new Option(data.CUSNAME, data.CUSCOD, true, true);
					$('#add_cuscod').attr('disabled',true);
					$('#add_cuscod').empty().append(newOption).trigger('change');	
					
					var newOption = new Option(data.ADDRNODetails, data.ADDRDOCNO, true, true);
					$('#add_addrno').empty().append(newOption).trigger('change');	
					
					var newOption = new Option(data.STRNO, data.STRNO, true, true);
					//$('#add_strno').attr('disabled',true);
					$('#add_strno').empty().append(newOption).trigger('change');
					
					var newOption = new Option(data.ACTIDES, data.ACTICOD, true, true);
					$('#add_acticod').attr('disabled',true);
					$('#add_acticod').empty().append(newOption).trigger('change');
					
					/*tab 2*/
					$('#add_inprc').val(addCommas(data.PRICE_TOTAL));
					$('#add_indwn').val(addCommas(data.DWN));
					$('#add_nopay').val(data.NOPAY);
					$('#add_upay').val(data.NOPAYPerMonth);
					
					if(data.OPTCODE != "NOTUSE"){
						var row = '<tr seq="new">';							
						row += "<td align='center'> ";
						row += "	<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
						row += "		opcode='"+data.OPTCODE+"' total1='0.00' total2='0.00' price1='0.00' price2='0.00' vat1='0.00' vat2='0.00' qty='1' uprice='0' ";
						row += "		style='cursor:pointer;'> ลบ   ";
						row += "	</i> ";
						row += "</td> ";
						row += '<td>'+data.OPTNAME+'</td>';
						row += '<td class="text-right">0</td>';
						row += '<td class="text-right">1</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '<td class="text-right">0.00</td>';
						row += '</tr>';
						$('#dataTables-inopt tbody').empty().append(row);
						
						$('#add2_optcost').val('0.00');
						$('#add2_optsell').val('0.00');
						
						inopt_remove();
					}
					
					/*tab 3*/
					$('#add_payfirst').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_paynext').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_paylast').val(addCommas(data.PERMONTH_TOTAL));
					$('#add_sell').val(addCommas(data.TOTAL_CAROPT));
					$('#add_totalSell').val(addCommas(data.TOTAL_CAROPT));
					$('#add_interest').val(addCommas(data.HP_TOTAL));
					
					$('#add_intRate').val(data.INT_RATE);
					$('#add_delay').val(data.DELAY_DAY);
					$('#add_interestRate').val(data.INTERAST_RATE).attr('disabled',true);
					$('#add_interestRateReal').val(data.INTERAST_RATE_REAL).attr('disabled',true);
					
					/*tab 5*/
					var ref_size = (typeof data.REF === 'undefined' ? 0 : Object.keys(data.REF).length); // นับว่ามีคนค้ำกี่คน
					$('#dataTable_ARMGAR tbody').empty(); // เคลียข้อมูลคนค้ำ
					for(var i = 1;i<=ref_size;i++){
						var rank 	 = data.REF[i]['rank']; //ลำดับ
						var cuscod 	 = data.REF[i]['cuscod']; //รหัสลูกค้า
						var refname  = data.REF[i]['refname']; //ชื่อลูกค้า
						var relation = data.REF[i]['relation']; //ความสัมพันธ์
						
						var row = '<tr seq="new">';							
						row += "<td align='center'> ";
						row += "	<i class='mgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus' ";
						row += "		position='"+(rank)+"' cuscod='"+cuscod+"' cusval='"+refname+"' relation='"+relation+"' ";
						row += "		style='cursor:pointer;'> ลบ   ";
						row += "	</i> ";
						row += "</td> ";
						row += "<td>"+($('.mgarTab5').length + 1)+"</td>";
						row += "<td>"+refname+"</td>";
						row += "<td>"+relation+"</td>";
						row += '</tr>';
						
						$('#dataTable_ARMGAR tbody').append(row);
					}
					
					dateChanged(); //update วันที่ทำสัญญา ดิวเดต lastดิว วันที่ปล่อยรถ
				}
				
				jdadd_strno = null;
				$('#loadding').fadeOut(200);
			}
		});
	});
	
	$('#add_paydue').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getPAYDUE',
			data: function (params) {
				var dataToPost = new Object();
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
	
	//$('#add_cuscod').change(function(){
	$('#add_cuscod').on("select2:select",function(){	
		var dataToPost = new Object();
		dataToPost.cuscod = $(this).find(':selected').val();
		
		$.ajax({
			url:'../SYS04/Leasing/checkCustomer',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				var newOption = new Option(data.ADDRDT, data.ADDRNO, true, true);
				$('#add_addrno').empty().append(newOption).trigger('change');					
				
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
						msg: "รหัสลูกค้า "+dataToPost.cuscod+" <br>เกรดลูกหนี้เป็น "+data.GRADE+" <br>ไม่สามารถปล่อยสินเชื่อได้ครับ"
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
								$('#add_resvno').trigger('select2:select');
								
								$this.destroy();
							});
						}
					});
				}
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_cuscod').on("select2:unselect",function(){
		$('#add_cuscod').empty().trigger('change');
		$('#add_addrno').empty().trigger('change');
	});
	
	//$('#add_resvno').change(function(){
	$('#add_resvno').on("select2:select",function(){	
		var dataToPost = new Object();
		dataToPost.resvno = (typeof $(this).find(':selected').val() === 'undefined' ? '' : $(this).find(':selected').val());
		dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
		
		$.ajax({
			url:'../SYS04/Leasing/resvnoChanged',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if(data.SMCHQ > 0 || data.msg != ""){
					$('#add_cuscod').attr('disabled',true);
					$('#add_cuscod').empty().trigger('change');
					$('#add_addrno').empty().trigger('change');
					
					$('#add_strno').attr('disabled',true);
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
					$('#add_cuscod').empty().trigger('change');
					$('#add_addrno').empty().trigger('change');
					$('#add_strno').empty().trigger('change');
				}else{
					var newOption = new Option(data.CUSNAME, data.CUSCOD, true, true);
					$('#add_cuscod').attr('disabled',true);
					$('#add_cuscod').empty().append(newOption).trigger('change');
					$('#add_addrno').empty().trigger('change');
					
					var newOption = new Option(data.STRNO, data.STRNO, true, true);
					$('#add_strno').attr('disabled',true);
					$('#add_strno').empty().append(newOption).trigger('change');
					
					var newOption = new Option(data.ACTIDES, data.ACTICOD, true, true);
					$('#add_acticod').attr('disabled',true);
					$('#add_acticod').empty().append(newOption).trigger('change');
				}
				
				$('#add_cuscod').trigger('select2:select');
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_resvno').on("select2:unselect",function(){	
		$('#add_cuscod').attr('disabled',false);
		$('#add_cuscod').empty().trigger('change');
		$('#add_addrno').empty().trigger('change');
		$('#add_strno ,#add_acticod').attr('disabled',false);
		$('#add_strno ,#add_acticod').empty().trigger('change');
	});
	
	$('#add_vatrt').attr('disabled',true);
	$('.add_nextlastmonth').hide();
	
	document.getElementById("dataTable-fixed-inopt").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
		this.querySelector("thead").style.transform = translate;
		this.querySelector("thead").style.zIndex = 100;
	});
	
	$('#add_inopt').click(function(){
		$('#add_inopt').attr('disabled',true);
		
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url: '../SYS04/Leasing/getFormInopt',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				Lobibox.window({
					title: 'เพิ่มอุปกรณ์เสริม',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						//$this.destroy();		
						$('#op_code').select2({ 
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getOPTMAST',
								data: function (params) {
									var dataToPost = new Object();
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
							var dataToPost = new Object();
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
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
					}
				});
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	/*
	$('#add_inprc').attr('disabled',true);
	$('#add_indwn').attr('disabled',true);
	$('#add_nopay').attr('disabled',true);
	$('#add_upay').attr('disabled',true);
	$('#add_payfirst').attr('disabled',true);
	$('#add_paynext').attr('disabled',true);
	$('#add_paylast').attr('disabled',true);
	$('#add_sell').attr('disabled',true);
	$('#add_totalSell').attr('disabled',true);	
	$('#add_interest').attr('disabled',true);
	$('#add_interestRate').attr('disabled',true);
	$('#add_interestRateReal').attr('disabled',true);	
	*/
	
	$('#add_emp').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
			data: function (params) {
				var dataToPost = new Object();
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
				var dataToPost = new Object();
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
	
	$('#add_empSell').select2({ dropdownParent: true,disabled: true,width:'100%' });
	
	$('#add_acticod').select2({ 
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				var dataToPost = new Object();
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
				var dataToPost = new Object();
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
		allowClear: true,
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
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url: '../SYS04/Leasing/getFormMGAR',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				Lobibox.window({
					title: 'เพิ่มผู้ค้ำประกัน',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data,
					draggable: true,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						$('#mgar_cuscod').select2({
							placeholder: 'เลือก',
							ajax: {
								url: '../Cselect2/getCUSTOMERS',
								data: function (params) {
									var dataToPost = new Object();
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
									var dataToPost = new Object();
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
								var msg = "";
								if(relation == ''){ msg = "ความสัมพันธ์คนค้ำ"; }
								if(addrno   == ''){ msg = "ที่อยู่ผู้ค้ำ"; }
								if(cuscod   == ''){ msg = "รหัส/ชื่อ ผู้ค้ำประกัน"; }
								
								if(msg != ""){
									Lobibox.notify('warning', {
										title: 'แจ้งเตือน',
										size: 'mini',
										closeOnClick: false,
										delay: 15000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										msg: 'คุณยังไม่ได้ระบุ'+msg+' โปรดระบุ'+msg+' ก่อนครับ'
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
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
					}
				});
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_othmgar').click(function(){
		$('#add_othmgar').attr('disabled',true);
		$('#loadding').fadeIn(200);
		
		$.ajax({
			url: '../SYS04/Leasing/getFormOTHMGAR',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				Lobibox.window({
					title: 'เพิ่มผู้ค้ำประกัน',
					//width: $(window).width(),
					//height: 220,
					content: data,
					draggable: true,
					closeOnEsc: true,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
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
								var msg = "";
								if(garcod == ''){ msg = "รหัสหลักทรัพย์"; }
								if(refno  == ''){ msg = "เลขที่อ้างอิง"; }
								
								if(msg != ""){
									Lobibox.notify('warning', {
										title: 'แจ้งเตือน',
										size: 'mini',
										closeOnClick: false,
										delay: 15000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										msg: 'คุณยังไม่ได้ระบุ'+msg+' โปรดระบุ'+msg+' ก่อนครับ'
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
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
					}
				});
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
						var msg = "";
						
						if(paydue 		== ''){ msg = "ไม่พบวิธีชำระค่างวด โปรดระบุวิธีชำระค่างวดก่อนครับ"; }
						if(strno 		== ''){ msg = "ไม่พบเลขตัวถัง โปรดระบุเลขตัวถังก่อนครับ"; }
						if(cuscodaddr 	== ''){ msg = "ไม่พบที่อยู่ในการพิมพ์สัญญา โปรดระบุที่อยู่ในการพิมพ์สัญญาก่อนครับ"; }
						if(cuscod 		== ''){ msg = "ไม่พบรหัสลูกค้า โปรดระบุรหัสลูกค้าก่อนครับ"; }
						if(sdate 		== ''){ msg = "ไม่พบวันที่ขาย โปรดระบุวันที่ขายก่อนครับ"; }
						
						if($('#add_contno').val() == "Auto Genarate"){
							var FNANALYZE = (typeof $('#add_approve').attr('FNANALYZE') === 'undefined' ? '' : $('#add_approve').attr('FNANALYZE'));
							var APPROVE   = (typeof $('#add_approve').find(':selected').val() === 'undefined' ? '' : $('#add_approve').find(':selected').val());
							// if(FNANALYZE == 'N' && APPROVE == ""){ //allow finance not approve อนุญาติให้คีย์ขายกรณ๊ืตั้งไฟแนนท์โดยไม่มีใบวิเคราะห์
							if(APPROVE == ""){ msg = "ไม่พบเลขที่ใบอนุมัติ โปรดตรวจสอบข้อมูลอีกครั้ง"; }
						}
						
						if(msg != ""){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 15000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: msg
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
		var dataToPost = new Object();
		dataToPost.strno = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
		dataToPost.inclvat = (typeof $('#add_inclvat').find(':selected').val() === 'undefined' ? '' : $('#add_inclvat').find(':selected').val());
		dataToPost.vatrt = $('#add_vatrt').val();
		
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../SYS04/Leasing/getFormCalNopay',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').fadeOut(200);
				if(data.status){
					Lobibox.window({
						title: 'คำนวนค่างวด',
						width: $(window).width(),
						height: $(window).height(),
						content: data.msg,
						draggable: false,
						closeOnEsc: true,
						shown: function($thisFormCalNopay){
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
							
							
							$('#btnStd').click(function(){ 
								$('#btnStd').attr('disabled',true);
								
								var dataToPost = new Object();
								dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
								dataToPost.strno = (typeof $('#add_strno').find(':selected').val() === 'undefined' ? '' : $('#add_strno').find(':selected').val());
								
								$.ajax({
									url:'../SYS04/Leasing/getFormStd',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data) {
										Lobibox.window({
											title: 'ดึงข้อมูลราคา std',
											width: $(window).width(),
											height: $(window).height(),
											content: data.html,
											draggable: true,
											closeOnEsc: true,
											shown: function($thisFormStd){
												$('#std_acticod').select2({ 
													placeholder: 'เลือก',
													ajax: {
														url: '../Cselect2/getACTI',
														data: function (params) {
															var dataToPost = new Object();
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
													dropdownParent: $("#lobiwin_std"),
													//disabled: true,
													//theme: 'classic',
													width: '100%'
												});
												
												$('#btnStdSearch').click(function(){
													var dataToPost = new Object();
													dataToPost.locat 	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '' : $('#add_locat').find(':selected').val());
													dataToPost.sdate    = $('#add_sdate').val();
													dataToPost.model 	= $(this).attr('MODEL');
													dataToPost.baab  	= $(this).attr('BAAB');
													dataToPost.color 	= $(this).attr('COLOR');
													dataToPost.acticod 	= (typeof $('#std_acticod').find(':selected').val() === 'undefined' ? '' : $('#std_acticod').find(':selected').val());
													dataToPost.dwn 		= $('#std_dwn').val();
													dataToPost.nopay 	= $('#std_nopay').val();
													
													$.ajax({
														url:'../SYS04/Leasing/getFormStdSearch',
														data: dataToPost,
														type: 'POST',
														dataType: 'json',
														success: function(data) {
															if(data.status == 'S'){
																$('#stdResult').html(data.msg);
																
																$('#stdCond1').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond1').prop('checked',false);
																	}else{
																		$('#stdCond1').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการใช้อัตราดอกเบี้ยข้าราชการหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, ใช้ดอกเบี้ยข้าราชการ',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-ok',
																				text: ' ยืนยัน, ใช้ดอกเบี้ยทั่วไป',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond1').prop('checked',true);
																			}else{
																				$('#stdCond1').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#stdCond2').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond2').prop('checked',false);
																	}else{
																		$('#stdCond2').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการรวมเบี้ยประกันด้วยหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, รวมเบี้ยประกัน',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-remove',
																				text: ' ยืนยัน, ไม่รวมเบี้ยประกัน',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond2').prop('checked',true);
																			}else{
																				$('#stdCond2').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#stdCond3').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond3').prop('checked',false);
																	}else{
																		$('#stdCond3').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการรวมค่าโอนด้วยหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, รวมค่าโอน',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-remove',
																				text: ' ยืนยัน, ไม่รวมค่าโอน',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond3').prop('checked',true);
																			}else{
																				$('#stdCond3').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#stdCond4').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond4').prop('checked',false);
																	}else{
																		$('#stdCond4').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการรวมค่าทะเบียนด้วยหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, รวมค่าทะเบียน',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-remove',
																				text: ' ยืนยัน, ไม่รวมค่าทะเบียน',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond4').prop('checked',true);
																			}else{
																				$('#stdCond4').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#stdCond5').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond5').prop('checked',false);
																	}else{
																		$('#stdCond5').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการรวมค่าพ.ร.บ. ด้วยหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, รวมค่าพ.ร.บ.',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-remove',
																				text: ' ยืนยัน, ไม่รวมค่าพ.ร.บ.',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond5').prop('checked',true);
																			}else{
																				$('#stdCond5').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#stdCond6').change(function(){
																	if($(this).is(':checked')){
																		$('#stdCond6').prop('checked',false);
																	}else{
																		$('#stdCond6').prop('checked',true);
																	}
																	
																	Lobibox.confirm({
																		title: 'ยืนยันการทำรายการ',
																		iconClass: false,
																		msg: 'คุณต้องการรวมคูปองชิงโชค ด้วยหรือไม่',
																		buttons: {
																			ok : {
																				'class': 'btn btn-primary glyphicon glyphicon-ok',
																				text: ' ยืนยัน, รวมคูปองชิงโชค',
																				closeOnClick: true,
																			},
																			cancel : {
																				'class': 'btn btn-danger glyphicon glyphicon-remove',
																				text: ' ยืนยัน, ไม่รวมคูปองชิงโชค',
																				closeOnClick: true
																			},
																		},
																		callback: function(lobibox, type){
																			if (type === 'ok'){
																				$('#stdCond6').prop('checked',true);
																			}else{
																				$('#stdCond6').prop('checked',false);
																			}
																		}
																	});
																});
																
																$('#btnStdReceipt').click(function(){
																	var dataToPost = new Object();
																	dataToPost.stdCond1 = ($('#stdCond1').is(':checked') ? 'T' : 'F');
																	dataToPost.stdCond2 = ($('#stdCond2').is(':checked') ? 'T' : 'F');
																	dataToPost.stdCond3 = ($('#stdCond3').is(':checked') ? 'T' : 'F');
																	dataToPost.stdCond4 = ($('#stdCond4').is(':checked') ? 'T' : 'F');
																	dataToPost.stdCond5 = ($('#stdCond5').is(':checked') ? 'T' : 'F');
																	dataToPost.stdCond6 = ($('#stdCond6').is(':checked') ? 'T' : 'F');
																	
																	dataToPost.stdid = $(this).attr('stdid');
																	dataToPost.plrank = $(this).attr('plrank');
																	dataToPost.price = $(this).attr('price');
																	dataToPost.interest_rate = $(this).attr('interest_rate');
																	dataToPost.interest_rate2 = $(this).attr('interest_rate2');
																	dataToPost.insurance = $(this).attr('insurance');
																	dataToPost.transfers = $(this).attr('transfers');
																	dataToPost.regist = $(this).attr('regist');
																	dataToPost.act = $(this).attr('act');
																	dataToPost.coupon = $(this).attr('coupon');
																	dataToPost.optionTotal = $(this).attr('optionTotal');
																	dataToPost.down = $(this).attr('down');
																	dataToPost.nopay = $(this).attr('nopay');
																	
																	$.ajax({
																		url:'../SYS04/Leasing/getStdReceived',
																		data: dataToPost,
																		type: 'POST',
																		dataType: 'json',
																		success: function(data) {
																			$('#btnStd').attr('stdid',data['stdid']);
																			$('#calc_incvat').select2({ dropdownParent: true,disabled: true,width:'100%' });
																			$('#calc_incvat').val('Y').trigger('change');
																			$('#calc_installment').select2({ dropdownParent: true,disabled: true,width:'100%' });
																			$('#calc_installment').val('Y').trigger('change');
																			
																			$('#calc_npricev').attr('disabled',true);
																			$('#calc_ndownv').attr('disabled',true);
																			$('#calc_nopay').attr('disabled',true);
																			$('#calc_nopays').attr('disabled',true);
																			$('#calc_vatyear').attr('disabled',true);
																			$('#calc_npricevOpt').attr('disabled',true);
																			
																			$('#calc_npricev').val(data['price']);
																			$('#calc_ndownv').val(data['down']);
																			$('#calc_nopay').val(data['nopay']);
																			$('#calc_nopays').val(data['nopay']);
																			$('#calc_vatyear').val(data['interestY']);
																			$('#calc_npricevOpt').val(data['priceOpt']);
																			
																			fnCalculate();
																			$thisFormStd.destroy();
																		},
																		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
																	});
																});	
																
																$('#btnStdReceiptOld').click(function(){	
																	$('#btnStd').attr('stdid',$(this).attr('stdid'));
																	
																	$('#calc_incvat').select2({ dropdownParent: true,disabled: true,width:'100%' });
																	$('#calc_incvat').val('Y').trigger('change');
																	
																	$('#calc_installment').select2({ dropdownParent: true,disabled: true,width:'100%' });
																	$('#calc_installment').val('Y').trigger('change');
																	
																	$('#calc_npricev').attr('disabled',true);
																	$('#calc_ndownv').attr('disabled',true);
																	$('#calc_nopay').attr('disabled',true);
																	$('#calc_nopays').attr('disabled',true);
																	$('#calc_vatyear').attr('disabled',true);
																	$('#calc_npricevOpt').attr('disabled',true);
																	
																	$('#calc_npricev').val($(this).attr('price'));
																	$('#calc_ndownv').val($(this).attr('down'));
																	$('#calc_nopay').val($(this).attr('nopay'));
																	$('#calc_nopays').val($(this).attr('nopay'));
																	$('#calc_vatyear').val($(this).attr('interest_rate'));
																	$('#calc_npricevOpt').val($(this).attr('optionTotal'));
																	
																	fnCalculate();
																	$thisFormStd.destroy();
																});																
															}else if(data.status == 'W'){
																$('#stdResult').html(data.msg);
															}
														},
														error: function(x,m,l){
															$('#stdResult').html(l);
														}
													});
												});
											},
											beforeClose : function(){
												$('#btnStd').attr('disabled',false);
											},
											error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
										});
									}
								});
							});
							
							$('#btnCalculate').click(function(){ fnCalculate(); });
							$('#calc_decimal').change(function(){ fnCalculate(); });
							
							function fnCalculate(){
								var dataToPost = new Object();
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
									},
									error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
								});
							}
							
							$('#btnReceived').click(function(){
								var dataToPost = new Object();
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
								dataToPost.installmentn			= $('#calc_installmentn').val();								
								dataToPost.sellvatLast 			= $('#calc_sellvatLast').val();
								dataToPost.installmentvLast		= $('#calc_installmentvLast').val();
								dataToPost.sellBvatOpt			= $('#calc_sellBvatOpt').val();
								dataToPost.installmentnOpt		= $('#calc_installmentnOpt').val();
								dataToPost.sellvatLastOpt 		= $('#calc_sellvatLastOpt').val();
								dataToPost.installmentvLastOpt 	= $('#calc_installmentvLastOpt').val();
								dataToPost.totalSell 			= $('#calc_totalSell').val();
								dataToPost.totalInstallment 	= $('#calc_totalInstallment').val();
								dataToPost.strno		 		= $('#add_strno').find(':selected').val();
								dataToPost.duefirst 			= $('#add_duefirst').val();
								dataToPost.npricevOpt 			= $('#calc_npricevOpt').val();
								dataToPost.npriceOpt  			= $('#calc_npriceOpt').val();
								
								$('#loadding').fadeIn(200);
								$.ajax({
									url:'../SYS04/Leasing/getReceivedCAL',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									success: function(data) {
										$('#loadding').fadeOut(200);
										$('#add_inprc').val(data.sell);
										$('#add_indwn').val(data.down);
										$('#add_nopay').val(data.nopay);
										$('#add_upay').val(data.upay);
										$('#add_payfirst').val(data.pay1);
										$('#add_paynext').val(data.pay2);
										$('#add_paylast').val(data.pay3);	
										
										$('#add_sell').val(data.sellrv);
										$('#add_totalSell').val(data.sellFresh);
										$('#add_interest').val(data.interate);
										$('#add_intRate').val(data.INT_RATE);
										$('#add_delay').val(data.DELAY_DAY);
										
										$('#add_interestRate').val(data.vatyear);
										$('#add_interestRateReal').val(data.vatyearReal);
										
										$('#add_duelast').val(data.duelast);
										
										$('#add_save').attr('cal','y');										
										$thisFormCalNopay.destroy();
									},
									error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	$('#add_detailsCond').click(function(){
		var dataToPost = new Object();
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
		
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../SYS04/Leasing/getDetailsCond',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').fadeOut(200);
				if(data.status){
					Lobibox.window({
						title: 'คำนวนค่างวด',
						//width: $(window).width(),
						//height: $(window).height(),
						content: data.html,
						draggable: true,
						closeOnEsc: true,
						onShow: function(lobibox){ $('body').append(jbackdrop); },
						shown: function($this){
							$('#add_detailsCond').attr('disabled',true);
						},
						beforeClose: function($this){
							$('#add_detailsCond').attr('disabled',false);
							$('.jbackdrop')[($('.jbackdrop').length)-1].remove(); 
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
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});

	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกการขายผ่อนหรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
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
					dataToPost.comments_free = $('#add_comments_free').val();
					
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
					dataToPost.cal = (typeof $('#add_save').attr('cal') === 'undefined' ? 'n' : $('#add_save').attr('cal'));
					
					$('#loadding').fadeIn(200);
					$.ajax({
						url:'../SYS04/Leasing/save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data) {
							$('#loadding').fadeOut(200);
							
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
							
							$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
						},
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
					
				}else{
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				}
			}
		});
	});
	
	$('#add_save').attr('disabled',(_insert == 'T' ? false:true));
	$('#add_delete').attr('disabled',(_delete == 'T' ? false:true));
	
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
				var dataToPost = new Object();
				dataToPost.now = $(this).find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);				
				
				dataToPost.locat = $('#add_locat').find(':selected').val();
				dataToPost.sdate = $('#add_sdate').val();
				
				var customers = new Array();
				if(typeof $('#add_cuscod').find(':selected').val() !== 'undefined'){
					customers.push($('#add_cuscod').find(':selected').val());
				}
				$('.mgarTab5').each(function(){ customers.push($(this).attr('cuscod')); });
				dataToPost.customers = (customers.length > 0 ? customers : []);
				
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
					if(typeof $(this).find(':selected').val() === 'undefined'){ 
						$(this).select2('destroy');
						$(this).remove();
						size -= 1;
					}
				}
			});
		}
		
		fn_calbilldas();
	});
	
	//$('.add_billdas[rank='+rank+']').on("select2:unselecting", function(e) { fn_calbilldas(); });
}

function fn_calbilldas(){
	var saleno = new Array();
	$('.add_billdas').each(function(){
		if(typeof $(this).find(':selected').val() !== 'undefined'){
			saleno.push($(this).find(':selected').val());
		}
	});	
	
	if(saleno.length > 0){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../SYS04/Leasing/calbilldas',
			data: {saleno:saleno,locat:(typeof $("#add_locat").find(':selected').val() === 'undefined' ? '' : $("#add_locat").find(':selected').val())},
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#add_free').val(data.TotalAmt);
				
				/*
				var comment = $('#add_comments').val().split("\n");
				$('#add_comments').val(data.Details+"\n"+(typeof comment[1] === 'undefined' ? '' : comment[1]));
				*/
				$('#add_comments_free').val(data.Details);
				$('#loadding').fadeOut(200);
			},
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		})
	}else{
		$('#add_free').val('0.00');
		/*
		var comment = $('#add_comments').val().split("\n");
		$('#add_comments').val((typeof comment[1] === 'undefined' ? '' : "\n"+comment[1]));
		*/
		$('#add_comments_free').val('');
	}
}

function inopt_remove(){
	$('.inoptTab2').unbind('click');
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
	$('.mgarTab5').unbind('click');
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
	$('.othmgarTab5').unbind('click');
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
	var newOption = new Option($dataLoad.APPVNO, $dataLoad.APPVNO, true, true);
	$('#add_approve').empty().append(newOption).trigger('change');
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
	var newOption = new Option($dataLoad.ACTINAME, $dataLoad.ACTICOD, true, true);
	$('#add_acticod').empty().append(newOption).trigger('change');
	/*tab2*/
	//$('#add_inopt').attr('disabled',true);
	$('#dataTables-inopt tbody').empty().append($dataLoad.option);
	inopt_remove();
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
	for(var i=0;i<billDas.length;i++){
		var billdas = "<select class='add_billdas form-control input-sm chosen-select' process='' rank='"+i+"' data-placeholder='เลขที่บิล'><option value='"+billDas[i]+"'>"+billDas[i]+"</option></select>";
		$('#formBillDas').append(billdas);
		
		fn_billdasActive(i);
	}
	$('#add_comments_free').val($dataLoad.MEMO1_FREE);
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
	//$('#add_inprcCal').unbind('click');
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
	$('#add_interestRate').attr('disabled',true);
	$('#add_interestRateReal').attr('disabled',true);
	$('#add_nextlastmonth').attr('disabled',true);
	
	$('#add_empSell').select2({ dropdownParent: true,disabled: true,width:'100%' });
	$('#add_acticod').select2({ dropdownParent: true,disabled: true,width:'100%' });
	
	
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
	
	$('#add_delete').attr('disabled',(_delete == 'T' ? false:true));
	if(_locat != $('#add_locat').find(':selected').val() && _level != 1){ 
		$('#add_inopt').attr('disabled',true);
		$('.inoptTab2').attr('disabled',true);
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
var JDbtnOther = null;
function btnOther($thisWindowLeasing){
	$('#btnArpay').click(function(){
		var dataToPost = new Object();
		dataToPost.contno = $('#add_contno').val();
		
		$('#btnArpay').attr('disabled',true);
		
		$('#loadding').fadeIn(200);
		JDbtnOther = $.ajax({
			url:'../SYS04/Leasing/loadARPAY',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				$('#loadding').fadeOut(200);
				
				Lobibox.window({
					title: 'ตารางสัญญา',
					width: 'col-sm-10 col-sm-offset-1',
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: true,		
					onShow: function(lobibox){ $('body').append(jbackdrop); },	
					shown: function($this){
						document.getElementById("dataTable-fixed-arpay").addEventListener("scroll", function(){
							var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
							this.querySelector("thead").style.transform = translate;
							this.querySelector("thead").style.zIndex = 100;
						});
					},
					beforeClose: function($this){
						$('#btnArpay').attr('disabled',false);
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
					}
				});
				
				JDbtnOther = null;
			},
			beforeSend: function(){ if(JDbtnOther !== null){ JDbtnOther.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	
	var JDadd_delete = null;
	$('#add_delete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการ<span style="color:red;">ลบเลขที่สัญญา</span> '+$('#add_contno').val()+' หรือไม่',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ลบ',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){
					var dataToPost = new Object();
					dataToPost.contno = $('#add_contno').val();
					
					$('#loadding').fadeIn(200);
					JDadd_delete = $.ajax({
						url:'../SYS04/Leasing/deleteContno',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
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
							
							$('#loadding').fadeOut(200);
							JDadd_delete = null;
							
							$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
						},
						beforeSend: function(){ if(JDadd_delete !== null){ JDadd_delete.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
					});
				}else{
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				}
			}
		});
	});
	
	$('#btnApproveSell').click(function(){
		var dataToPost = new Object();
		dataToPost.CONTNO = $("#add_contno").val();
		$.ajax({
			url:'../SYS04/Leasing/Encode',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			//beforeSend: function(){ if(OBJadd_btnFORMSETAlert !== null){ OBJadd_btnFORMSETAlert.abort(); }},
			success: function(data){
				$('#btnApproveSell').attr('disabled',true);	
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'SYS04/Leasing/approvepdf?contno='+data.CONTNO;
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
			}
		});
		
	});
	
	$('#btnEFF').click(function(){
		var dataToPost = new Object();
		dataToPost.CONTNO = $("#add_contno").val();
		$.ajax({
			url:'../SYS04/Leasing/Encode',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			//beforeSend: function(){ if(OBJadd_btnFORMSETAlert !== null){ OBJadd_btnFORMSETAlert.abort(); }},
			success: function(data){
				$('#btnEFF').attr('disabled',true);
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'SYS04/Leasing/effpdf?contno='+data.CONTNO;
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				
				Lobibox.window({
					title: 'ตารางแสดงภาระหนี้ตามสัญญา',
					width: $(window).width(),
					height: $(window).height(),
					content: content,
					draggable: false,
					closeOnEsc: true,			
					beforeClose : function(){
						$('#btnEFF').attr('disabled',false);
					}
				});
			}
		});
		/*
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
		*/
	});
	
	$('#btnSend').click(function(){
		/*
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
		*/
		
		documents('ใบส่งมอบสินค้า');
	});	
	
	function documents($type){
		var contno = $("#add_contno").val();
		var baseUrl = $('body').attr('baseUrl');
		var url = baseUrl+'SYS04/Agent/sendpdf?contno='+contno+'&document='+$type;
		var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
		
		Lobibox.window({
			title: $type,
			width: $(window).width(),
			height: $(window).height(),
			content: content,
			draggable: false,
			closeOnEsc: true,			
			beforeClose : function(){
				$('#btnApproveSell').attr('disabled',false);
			}
		});
	}
	
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










































