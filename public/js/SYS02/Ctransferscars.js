/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
var _locat  = $('.tab1[name="home"]').attr('locat');
var _diunem = $('.tab1[name="home"]').attr('diunem');

$(function(){
	$('.tab1').show();
	$('.tab2').hide();	 
	
	if(_insert == 'T'){
		$('#btnt1transfers').attr('disabled',false);
	}else{
		$('#btnt1transfers').attr('disabled',true);
	}
		
	initPage();
});

function initPage(){
	$('#add_TRANSFM').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_TRANSTO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
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
		disabled: false,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_EMPCARRY').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCOD').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 2000,
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
	
	$('#add_APPROVED').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCOD').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 2000,
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
	
	$('#add_TRANSSTAT').select2({
		placeholder: 'เลือก',       
		allowClear: true,
		multiple: false,
		disabled: true,
		dropdownParent: $(document.body).offset(),
		//theme: 'classic',
		width: '100%'
	});	
}

$('#add_TRANSTO').change(function(){
	if($('#add_TRANSFM').find(':selected').val() == $(this).find(':selected').val()){
		var msg = "สถานที่ต้นทาง และสถานที่ปลายทางต้องไม่เป็นสาขาเดียวกันครับ";
		Lobibox.notify('error', {
			title: 'ผิดพลาด',
			size: 'mini',
			pauseDelayOnHover: true,
			closeOnClick: false,
			continueDelayOnInactiveTab: false,
			delay: 5000,
			icon: true,
			messageHeight: '90vh',
			msg: msg
		});
		
		$(this).empty().trigger('change');
	}
});

$('#add_EMPCARRY').change(function(){
	data = new Object();
	data.display = $('#add_EMPCARRY').find(':selected').text();
	data.valued = $('#add_EMPCARRY').find(':selected').val();
	
	var STRNO = [];	
	$('#table-STRNOTRANS tr').each(function() {
		if (!this.rowIndex) return; // skip first row
		
		var len = this.cells.length;
		var r = [];
		for(var i=0;i<len;i++){
			if(i == 1){				
				if(this.cells[7].innerHTML == 'อยู่ระหว่างการโอนย้ายรถ' && $('#add_EMPCARRY').val() !== null){
					if( $('.SETEMPCARRY[STRNO=\''+this.cells[1].innerHTML+'\']').val() == '' || $('.SETEMPCARRY[STRNO=\''+this.cells[1].innerHTML+'\']').val() === null ){
						var newOption = new Option(data.display, data.valued, true, true);
						$('.SETEMPCARRY[STRNO=\''+this.cells[1].innerHTML+'\']').append(newOption).trigger('change');
					}
				}else if(this.cells[7].innerHTML == 'อยู่ระหว่างการโอนย้ายรถ'){
					$('.SETEMPCARRY[STRNO=\''+this.cells[1].innerHTML+'\']').val(null).trigger('change');
					$('.SETEMPCARRY[STRNO=\''+this.cells[1].innerHTML+'\']').val(null).trigger('change');
				}
			}
		}
	});
});

$('#btnt1search').click(function(){ 
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $('#TRANSNO').val();
	dataToPost.TRANSDT = $('#TRANSDT').val();
	dataToPost.TRANSFM = $('#TRANSFM').val();
	dataToPost.TRANSSTAT = $('#TRANSSTAT').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1transfers').html('');
	$('#resultt1transfers').append(spinner);
	
	$.ajax({
		url:'../SYS02/Ctransferscars/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt1transfers').find('.spinner, .spinner-backdrop').remove();
			$('#resultt1transfers').html(data.html);
			
			/*
			document.getElementById("table-fixed-Ctransferscars").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});			
			*/
			
			$('#table-Ctransferscars').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-Ctransferscars',1,360);
			
			/*
			// Export data to Excel
			$('.data-export').prepend('<img id="table-Ctransferscars-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-Ctransferscars-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ข้อมูลการโอนย้าย","transfers.xlsx"); 
			});
			*/
			
			function redraw(){
				$('.getit').hover(function(){
					$(this).css({'background-color':'yellow'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
				},function(){
					$(this).css({'background-color':'white'});
					$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
				});
				
				$('.getit').click(function(){					
					dataToPost = new Object();
					dataToPost.TRANSNO = $(this).attr('TRANSNO');
					dataToPost.cup  = _update;
					dataToPost.clev = _level;
					
					loadData(dataToPost);
				});
			}		
		}
	});
}

var JASOBJloadData = null;
function loadData(dataToPost){
	$('#loadding').show();
	JASOBJloadData = $.ajax({
		url:'../SYS02/Ctransferscars/getDetails',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#loadding').hide();
			
			$('#table-STRNOTRANS tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
			
			document.getElementById("table-fixed-STRNOTRANS").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;	
				this.querySelector("thead").style.zIndex = 999;
			});
			
			var newOption;
			$('#add_TRANSNO').val(data.html['TRANSNO']);
			$('#add_TRANSDT').val(data.html['TRANSDT']);
			newOption = new Option(data.html['TRANSFM'], data.html['TRANSFM'], false, false);
			$('#add_TRANSFM').empty();
			$('#add_TRANSFM').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
			newOption = new Option(data.html['TRANSTO'], data.html['TRANSTO'], false, false);
			$('#add_TRANSTO').empty();
			$('#add_TRANSTO').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
			newOption = new Option(data.html['EMPCARRYNM'], data.html['EMPCARRY'], false, false);
			$('#add_EMPCARRY').empty();
			$('#add_EMPCARRY').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
			newOption = new Option(data.html['APPROVNM'], data.html['APPROVED'], false, false);
			$('#add_APPROVED').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
			$('#add_TRANSSTAT').val(data.html['TRANSSTAT']).trigger('change'); //กรณี select2 มี option แล้ว
			$('#add_MEMO1').val(data.html['MEMO1']);
			
			$('#add_TRANSNO').attr('readonly',true);
			$('#add_TRANSDT').attr('disabled',true);
			//$('#add_TRANSFM').attr('disabled',true);
			//$('#add_TRANSTO').attr('disabled',true);
			//$('#add_EMPCARRY').attr('disabled',true);
			//$('#add_APPROVED').attr('disabled',true);
			//$('#add_TRANSSTAT').attr('disabled',true);
			$('#add_TRANSFM').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			$('#add_TRANSTO').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			$('#add_EMPCARRY').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			$('#add_APPROVED').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			$('#add_TRANSSTAT').select2({ disabled: true,dropdownParent: $(document.body).offset(),width: '100%' });
			
			if(data.html['TRANSSTAT'] == 'Sendding'){ //สถานะกำลังโอนย้ายรถ  เปิดการลบบิลโอน  และบันทึกข้อมูลได้หากมีสิทธิ์
				$('#add_MEMO1').attr('disabled',false);
				$('#btnt2addSTRNo').attr('disabled',false);
				$('#btnt2del').attr('disabled',false);
				$('#btnt2save').attr('disabled',false);
				
				$('#btnt2del').show();
				if(_level == 1){
					if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
						$('#btnt2save').attr('disabled',false);	
						$('#btnt2addSTRNo').attr('disabled',true);
					}else{
						$('#btnt2save').attr('disabled',true);
						$('#btnt2addSTRNo').attr('disabled',true);
					}
					
					if(_delete == 'T'){
						$('#btnt2del').attr('disabled',false);
					}else{
						$('#btnt2del').attr('disabled',true);	
					}
				}else{
					if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
						if(_locat == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
							$('#btnt2save').attr('disabled',false);	
							$('#btnt2addSTRNo').attr('disabled',true);
						}else{
							$('#btnt2save').attr('disabled',true);
							$('#btnt2addSTRNo').attr('disabled',true);
						}
					}else{
						$('#btnt2save').attr('disabled',true);
						$('#btnt2addSTRNo').attr('disabled',true);
					}
					
					if(_delete == 'T'){
						if(_locat == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
							$('#btnt2del').attr('disabled',false);
						}else{
							$('#btnt2del').attr('disabled',true);
						}
					}else{
						$('#btnt2del').attr('disabled',true);	
					}
				}
				
				$('#btnt2bill').attr('disabled',false);
				$('#btnt2billOption').attr('disabled',true);
				$('#btnt2billUnlock').attr('disabled',true);				
				$('.tab2').css({'background-color':'#fff'});
			}else if(data.html['TRANSSTAT'] == 'Pendding'){ //สถานะรับบางส่วน ปิดการลบบิลโอน  แต่สามารถบันทึกได้หากมีสิทธิ์
				$('#add_MEMO1').attr('disabled',false);
				$('#btnt2del').attr('disabled',true);
				$('#btnt2addSTRNo').attr('disabled',true);
				
				
				if(_level == 1){
					if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
						$('#btnt2save').attr('disabled',false);
						$('#btnt2addSTRNo').attr('disabled',true);
					}else{
						$('#btnt2save').attr('disabled',true);
						$('#btnt2addSTRNo').attr('disabled',true);
					}
				}else{
					if(_update == 'T'){ //มีสิทธิ์แก้ไขไหม
						if(_locat == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
							$('#btnt2save').attr('disabled',false);
							$('#btnt2addSTRNo').attr('disabled',true);
						}else{
							$('#btnt2save').attr('disabled',true);
							$('#btnt2addSTRNo').attr('disabled',true);
						}
					}else{
						$('#btnt2save').attr('disabled',true);
						$('#btnt2addSTRNo').attr('disabled',true);
					}
				}
				
				$('#btnt2del').attr('disabled',true);	
				$('#btnt2bill').attr('disabled',false);
				$('#btnt2billOption').attr('disabled',true);
				$('#btnt2billUnlock').attr('disabled',true);
				$('.tab2').css({'background-color':'#fff'});
			}else if(data.html['TRANSSTAT'] == 'Received'){ //สถานะรับครบแล้ว ปิดการบันทึก และเพิ่มข้อมูลทั้งหมด
				$('#add_MEMO1').attr('disabled',true);
				$('#btnt2addSTRNo').attr('disabled',true);
				$('#btnt2del').attr('disabled',true);
				$('#btnt2save').attr('disabled',true);
				$('#btnt2bill').attr('disabled',true);
				$('#btnt2billOption').attr('disabled',false);
				$('#btnt2billUnlock').attr('disabled',false);
				$('.tab2').css({'background-color':'#fff'});
			}else if(data.html['TRANSSTAT'] == 'Cancel'){ //สถานะยกเลิกบิลโอน
				$('#add_MEMO1').attr('disabled',true);
				$('#btnt2addSTRNo').attr('disabled',true);
				$('#btnt2del').attr('disabled',true);
				$('#btnt2save').attr('disabled',true);
				$('#btnt2bill').attr('disabled',true);
				$('#btnt2billOption').attr('disabled',true);
				$('#btnt2billUnlock').attr('disabled',true);
				$('.tab2').css({'background-color':'#ffd6d6'});
			}
			
			var STRNO = data.html['STRNO'];
			for(var i=0;i<STRNO.length;i++){
				$('#table-STRNOTRANS tbody').append(STRNO[i]);
			}
			
			$('.SETEMPCARRY').select2({
				placeholder: 'เลือก',
				ajax: {
					url: '../Cselect2/getVUSER',
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
				width: '200px'
			});
			
			$('.tab1').hide();
			$('.tab2').show();
			
			delSTRNO();
			
			JASOBJloadData = null;
		},
		beforeSend: function(){
			if(JASOBJloadData !== null){
				JASOBJloadData.abort();
			}
		}		
	});
}

$('#btnt1transfers').click(function(){
	initPage();
	
	$('.tab1').hide();
	$('.tab2').show();
	
	$('#add_TRANSNO').val('Auto Generate');
	$('#add_TRANSNO').attr('readonly',true);
	
	$('#add_TRANSDT').val('');
	$('#add_TRANSTO').val(null).trigger('change');
	$('#add_EMPCARRY').val(null).trigger('change');
	$('#add_APPROVED').val(null).trigger('change');
	$('#add_TRANSSTAT').val('Pendding');
	$('#add_MEMO1').val('');
	
	var newOption;
	newOption = new Option(_locat, _locat, false, false);
	$('#add_TRANSFM').empty();
	$('#add_TRANSFM').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
	$('#add_TRANSTO').empty();
	$('#add_EMPCARRY').val('');
	$('#add_APPROVED').empty();
	$('#add_TRANSSTAT').val('Sendding').trigger('change'); //กรณี select2 มี option แล้ว
	$('#add_MEMO1').val('');
	
	$('#add_TRANSDT').attr('disabled',false);
	$('#add_TRANSTO').attr('disabled',false);
	$('#add_APPROVED').attr('disabled',false);
	$('#add_TRANSSTAT').attr('disabled',true);
	$('#add_EMPCARRY').attr('disabled',false);
	$('#add_MEMO1').attr('disabled',false);
	
	if(_level == '1'){
		$('#add_TRANSFM').attr('disabled',false);	
	}else{
		$('#add_TRANSFM').attr('disabled',true);
	}
	
	if(_insert == 'T'){
		$('#btnt2save').attr('disabled',false);	
		$('#btnt2addSTRNo').attr('disabled',false);	
	}else{
		$('#btnt2save').attr('disabled',true);	
		$('#btnt2addSTRNo').attr('disabled',true);								
	}
	
	$('#btnt2bill').attr('disabled',true);	
	$('#btnt2billOption').attr('disabled',true);
	$('#btnt2billOption').attr('disabled',true);
	$('#btnt2billUnlock').attr('disabled',true);
	
	$('#table-STRNOTRANS tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
	
	$('#btnt2del').hide();
	document.getElementById("table-fixed-STRNOTRANS").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});
	
	//$('#table-Ctransferscars').on('draw.dt',function(){ redraw(); });
	//fn_datatables('table-STRNOTRANS',3,450);			
});

$('#btnt2home').click(function(){ 
	$('.tab1').show(); 
	$('.tab2').hide(); 
});

$('#add_TRANSFM').change(function(){
	$('#table-STRNOTRANS tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
});

var generate = 1;
$('#btnt2addSTRNo').click(function(){
	dataToPost = new Object();
	dataToPost.locat = $('#add_TRANSFM').val();
	
	$.ajax({
		url: '../SYS02/Ctransferscars/getSTRNoForm',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				closeOnEsc: false,
				shown: function($this){
					$('#STRNOSearch').click(function(){
						dataToPost = new Object();
						dataToPost.fSTRNO = $('#fSTRNO').val();
						dataToPost.fMODEL = $('#fMODEL').val();
						dataToPost.fCRLOCAT = $('#add_TRANSFM').val();
						dataToPost.fGCODE	= $('#fGCODE').val();
						
						var spinner = $('body>.spinner').clone().removeClass('hide');
						$('#resultSTRNO').html('');
						$('#resultSTRNO').append(spinner);
						
						$.ajax({
							url: '../SYS02/Ctransferscars/getSTRNo',
							data: dataToPost,
							Type: 'POST',
							dataType:'json',
							success: function(data){
								$('#resultSTRNO').find('.spinner, .spinner-backdrop').remove();
								$('#resultSTRNO').html(data.html);
								
								document.getElementById("table-fixed-getSTRNo").addEventListener("scroll", function(){
									var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
									this.querySelector("thead").style.transform = translate;						
								});
								
								$('.getit').hover(function(){
									$(this).css({'background-color':'yellow'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
								},function(){
									$(this).css({'background-color':'white'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
								});
								
								$('.getit').click(function(){
									var STRNO = $(this).attr('STRNO').trim();
									//var TYPE = $(this).attr('TYPE');
									var MODEL = $(this).attr('MODEL');
									var BAAB = $(this).attr('BAAB');
									var COLOR = $(this).attr('COLOR');
									//var CC = $(this).attr('CC');
									var CRLOCAT = $(this).attr('CRLOCAT');
									var GCODE = $(this).attr('GCODE');
									
									var stat = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
									$('#table-STRNOTRANS tr').each(function() {
										if (!this.rowIndex) return; // skip first row
										
										if(this.cells[1].innerHTML == STRNO){
											stat=true;
										}
									});
									  
									if(stat){
										Lobibox.notify('error', {
											title: 'ผิดพลาด',
											size: 'mini',
											closeOnClick: false,
											delay: 5000,
											pauseDelayOnHover: true,
											continueDelayOnInactiveTab: false,
											icon: true,
											messageHeight: '90vh',
											msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
										});
									}else{
										var display = $('#add_EMPCARRY').find(':selected').text();
										var valued = $('#add_EMPCARRY').find(':selected').val();	
										
										var row = '<tr seq="new'+generate+'">';
										row += '<td><input type="button" class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'" value="ยกเลิก"></td>';
										row += '<td>'+STRNO+'</td>';
										row += '<td>'+MODEL+'</td>';
										row += '<td>'+BAAB+'</td>';
										row += '<td>'+COLOR+'</td>';
										row += '<td>'+GCODE+'</td>';
										row += '<td>อยู่ระหว่างการโอนย้ายรถ</td>';
										row += '<td><input type="text" STRNO="'+STRNO+'" class="SETTRANSDT form-control input-sm" data-provide="datepicker" data-date-language="th-th" placeholder="วันที่โอน"  style="width:100px;" value="'+($('#add_TRANSDT').val())+'"></td>';
										row += '<td><select STRNO="'+STRNO+'" class="SETEMPCARRY select2"><option value=\''+(valued)+'\'>'+(display)+'</option></select></td>';
										row += '</tr>';
										  
										$('#table-STRNOTRANS tbody').append(row);
										generate = generate+1;
										delSTRNO();
									}
									
									$('.SETEMPCARRY').select2({
										placeholder: 'เลือก',
										ajax: {
											url: '../Cselect2/getVUSER',
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
										width: '200px'
									});
									
									$this.destroy();
								});
							}
						});
					});
				}
			});
		}
	});
});

function delSTRNO(){
	$('.delSTRNO').click(function(){ $(this).closest('tr').remove(); }); 
	
	$('.SETTRANSDT').change(function(){
		//วันที่โอนต้องไม่มากกว่าวันปัจจุบัน
		checkdt($(this));
	});
}

$('#add_TRANSDT').change(function(){
	//วันที่โอนต้องไม่มากกว่าวันปัจจุบัน
	checkdt($(this));
});

function checkdt($this){
	dataToPost = new Object();
	dataToPost.dt = $this.val();
	
	$.ajax({
		url: '../SYS02/Ctransferscars/checkdt',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			if(data.html == 'T'){
				$this.val('');
				
				Lobibox.notify('warning', {
					title: 'ผิดพลาด',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					rounded: true,
					messageHeight: '90vh',
					msg: 'วันที่โอนย้าย จะต้องไม่เกิน 4 วันนับจากวันปัจจุบันครับ'
				});
			}
		}
	});
}

$('#btnt2bill').click(function(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $('#add_TRANSNO').val();
	
	$.ajax({
		url: '../SYS02/Ctransferscars/transcode',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			if($('#add_TRANSNO').val() == "" || $('#add_TRANSNO').val() == ""){
				alert('xxx');
			}else{
				
				var baseUrl = $('body').attr('baseUrl');
				var url = baseUrl+'SYS02/Ctransferscars/pdf?transno='+data[0];
				var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
				
				Lobibox.window({
					title: 'Window title',
					content: content,
					closeOnEsc: false,
					height: $(window).height(),
					width: $(window).width()
				});
			}
		}
	});
});

$('#btnt2save').click(function(){ 
	var msg = "คุณต้องการบันทึกการโอนย้ายรถ เลขที่บิล "+$('#add_TRANSNO').val()+" ?";
	if($('#add_TRANSNO').val() == "Auto Generate"){
		msg = "คุณต้องการบันทึกการโอนย้ายรถ ?<br><span style='color:red;'>**เมื่อบันทึก จะไม่สามารถแก้ไขรายการรถได้อีก</span>";
	}
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: msg,
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
			var btnType;
			if (type === 'ok'){
				dataToPost = new Object();
				dataToPost.TRANSNO 	 = $('#add_TRANSNO').val();
				dataToPost.TRANSDT 	 = $('#add_TRANSDT').val();
				dataToPost.TRANSFM 	 = $('#add_TRANSFM').val();
				dataToPost.TRANSTO 	 = $('#add_TRANSTO').val();
				dataToPost.EMPCARRY	 = $('#add_EMPCARRY').val();
				dataToPost.APPROVED  = $('#add_APPROVED').val();
				dataToPost.TRANSSTAT = $('#add_TRANSSTAT').val();
				dataToPost.MEMO1 	 = $('#add_MEMO1').val();
				
				var STRNO = [];	
				$('#table-STRNOTRANS tr').each(function() {
					if (!this.rowIndex) return; // skip first row
					
					var len = this.cells.length;
					var r = [];
					for(var i=0;i<len;i++){
						if(i == 7){ // วันที่โอนย้าย
							r.push($('.SETTRANSDT[STRNO='+this.cells[1].innerHTML+']').val());
						}else if(i == 8){ // พขร.
							var emp = '';
							if(typeof $('.SETEMPCARRY[STRNO='+this.cells[1].innerHTML+']').find(':selected').val() !== 'undefined'){
								emp = $('.SETEMPCARRY[STRNO='+this.cells[1].innerHTML+']').find(':selected').val();
							}
							
							r.push(emp);
						}else{
							r.push(this.cells[i].innerHTML);
						}
					}	
					STRNO.push(r);
				});
				
				dataToPost.STRNO = STRNO;
				$('#loadding').show();
				
				$.ajax({
					url:'../SYS02/Ctransferscars/saveTransferCAR',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						if(data.status){
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
							
							dataToPost = new Object();
							dataToPost.TRANSNO 	= data.transno;
							dataToPost.cup  	= _update;
							dataToPost.clev 	= _level;
							
							loadData(dataToPost); //โหลดข้อมูลที่บันทึก
							$('#btnt2bill').attr('disabled',false); //ให้พิมพ์บิลโอนได้
							$('#btnt2billOption').attr('disabled',true);
							$('#btnt2billUnlock').attr('disabled',true);
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
						
						$('#loadding').hide();
					}
				});
			}
		}
	});
});

$('#btnt2del').click(function(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องการยกเลิกการโอนย้ายรถ เลขที่บิลโอน "+$('#add_TRANSNO').val()+" ?",
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
				dataToPost.TRANSNO = $('#add_TRANSNO').val();
				
				$('#loadding').show();
				
				$.ajax({
					url: '../SYS02/Ctransferscars/cancelBill',
					data: dataToPost,
					type:'POST',
					dataType: 'json',
					success: function(data){	
						if(data.status){
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
							
							dataToPost = new Object();
							dataToPost.TRANSNO = data.transno;
							dataToPost.cup  = _update;
							dataToPost.clev = _level;
							
							loadData(dataToPost);
							$('#btnt2bill').attr('disabled',true); //ให้พิมพ์บิลโอนได้
							$('#btnt2billOption').attr('disabled',false);
							$('#btnt2billUnlock').attr('disabled',false);
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
					}
				});
			}
		}
	});
});

// ปลดล็อค
$('#btnt2billUnlock').click(function(){
	var html = "";
	//tobul = transferout unlock
	html += "<div class='col-xs-12 col-sm-12'><div class='form-group'>รหัสพนง./รหัส ปชช.<input type='text' id='tobul_username' class='form-control input-sm' placeholder='USERID'></div></div>";
	html += "<div class='col-xs-12 col-sm-12'><div class='form-group'>รหัสผ่าน<input type='password' id='tobul_password' class='form-control input-sm' placeholder='Password'></div></div>";
	html += "<div class='col-xs-12 col-sm-12'><div class='form-group'>หมายเหตุ<textarea id='tobul_comments' class='form-control' placeholder='หมายเหตุ' maxlength=250 style='max-width:100%;max-height:70px;'></textarea></div></div>";
	html += "<button id='tobul_comfirm' class='btn btn-sm btn-primary col-sm-12'>ปลดล็อค</button>";
	Lobibox.window({
		title: 'ฟอร์มขอปลดล็อคบิลโอน',
		height: 350,
		content: html,
		closeOnEsc: false,
		shown: function($this){
			var JASOBJbillUnlock = null;
			$("#tobul_comfirm").click(function(){
				dataToPost = new Object();
				dataToPost.user 	= $("#tobul_username").val();
				dataToPost.pass 	= $("#tobul_password").val();
				dataToPost.comments = $("#tobul_comments").val();
				dataToPost.TRANSNO  = $("#add_TRANSNO").val();
				
				dataToPost.diunem 	= _diunem;
				
				JASOBJbillUnlock = $.ajax({
					url: '../SYS02/Ctransferscars/billunlock',
					data: dataToPost,
					type:'POST',
					dataType: 'json',
					success: function(data){
						Lobibox.notify((data.error?"waning":"success"), {
							//title: 'ผิดพลาด',
							size: 'mini',
							closeOnClick: false,
							delay: 5000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.msg
						});
						
						if(!data.error){  
							$('#btnt2bill').attr('disabled',false);
							$('#btnt2billOption').attr('disabled',true);
							$('#btnt2billUnlock').attr('disabled',true);
							$this.destroy();
						}
						
						JASOBJbillUnlock = null;
					},
					beforeSend: function(){
						if(JASOBJbillUnlock !== null){
							JASOBJbillUnlock.abort();
						}
					}
				});
			});
		}
	});
});
















