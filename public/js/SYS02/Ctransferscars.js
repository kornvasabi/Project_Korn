var setwidth = $(window).width();
var setheight = $(window).height();
if(setwidth > 1000){
	setwidth = 1000;
}else{
	setwidth = setwidth - 50;
}

if(setheight > 800){
	setheight = 800;
}else{
	setheight = setheight - 50;
}

$(function(){
	$('.tab1').show();
	$('.tab2').hide();	 
	
	if($('.tab1[name="home"]').attr('cin') == 'T'){
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
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_TRANSTO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
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
		//theme: 'classic',
		width: '100%'
	});
	
	$('#add_APPROVED').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getUSERS',
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
		//theme: 'classic',
		width: '100%'
	});	
	
	$('#add_TRANSSTAT').select2({
		placeholder: 'เลือก',       
		allowClear: true,
		multiple: false,
		//theme: 'classic',
		width: '100%'
	});	
}

$('#btnt2save').click(function(){ 
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องการบันทึกการโอนย้ายรถ ?",
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
				dataToPost.TRANSNO = $('#add_TRANSNO').val();
				dataToPost.TRANSDT = $('#add_TRANSDT').val();
				dataToPost.TRANSFM = $('#add_TRANSFM').val();
				dataToPost.TRANSTO = $('#add_TRANSTO').val();
				dataToPost.EMPCARRY = $('#add_EMPCARRY').val();
				dataToPost.APPROVED = $('#add_APPROVED').val();
				dataToPost.TRANSSTAT = $('#add_TRANSSTAT').val();
				dataToPost.MEMO1 = $('#add_MEMO1').val();
				
				var STRNO = [];	
				$('#table-STRNOTRANS tr').each(function() {
					if (!this.rowIndex) return; // skip first row
					
					var len = this.cells.length;
					var r = [];
					for(var i=0;i<len;i++){
						r.push(this.cells[i].innerHTML);
					}	
					STRNO.push(r);
				});
				
				dataToPost.STRNO = STRNO;
				
				$.ajax({
					url:'../SYS02/Ctransferscars/saveTransferCAR',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						if(data.status){
							Lobibox.notify('success', {
								title: 'สำเร็จ',
								closeOnClick: false,
								delay: 5000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: false,
								messageHeight: '90vh',
								msg: data.msg
							});
							$('.tab1').show();
							$('.tab2').hide();
							search();
						}else{
							Lobibox.notify('error', {
								title: 'ผิดพลาด',
								closeOnClick: false,
								delay: false,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: false,
								messageHeight: '90vh',
								msg: data.msg
							});
						}
					}
				});
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: false,
					messageHeight: '90vh',
					msg: 'ยกเลิกการบันทึกรายการโอนย้ายรถแล้ว'
				});
			}
		}
	});
});


$('#btnt1search').click(function(){ search(); });

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
			
			document.getElementById("table-fixed-Ctransferscars").addEventListener("scroll", function(){
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
				dataToPost = new Object();
				dataToPost.TRANSNO = $(this).attr('TRANSNO');
				dataToPost.cup = $('.tab1[name="home"]').attr('cup');
				dataToPost.clev = $('.tab1[name="home"]').attr('clev');
				
				$.ajax({
					url:'../SYS02/Ctransferscars/getDetails',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						$('#table-STRNOTRANS tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
						var newOption;
						$('#add_TRANSNO').val(data.html['TRANSNO']);
						$('#add_TRANSDT').val(data.html['TRANSDT']);
						newOption = new Option(data.html['TRANSFM'], data.html['TRANSFM'], false, false);
						$('#add_TRANSFM').empty();
						$('#add_TRANSFM').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
						newOption = new Option(data.html['TRANSTO'], data.html['TRANSTO'], false, false);
						$('#add_TRANSTO').empty();
						$('#add_TRANSTO').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
						$('#add_EMPCARRY').val(data.html['EMPCARRY']);
						newOption = new Option(data.html['APPROVNM'], data.html['APPROVED'], false, false);
						$('#add_APPROVED').append(newOption).trigger('change'); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
						$('#add_TRANSSTAT').val(data.html['TRANSSTAT']).trigger('change'); //กรณี select2 มี option แล้ว
						$('#add_MEMO1').val(data.html['MEMO1']);
						
						$('#add_TRANSNO').attr('readonly',true);
						$('#add_TRANSDT').attr('disabled',true);
						$('#add_TRANSFM').attr('disabled',true);
						$('#add_TRANSTO').attr('disabled',true);
						$('#add_APPROVED').attr('disabled',true);
						$('#add_TRANSSTAT').attr('disabled',true);
						
						//$('.tab1[name="home"]').attr('locat'),data.html['TRANSFM']
						if(data.html['TRANSSTAT'] == 'Received'){ //สถานะรับครบแล้ว ปิดการบันทึก และเพิ่มข้อมูลทั้งหมด
							$('#add_EMPCARRY').attr('disabled',true);							
							$('#add_MEMO1').attr('disabled',true);
							$('#btnt2addSTRNo').attr('disabled',true);
							$('#btnt2del').attr('disabled',true);
							$('#btnt2save').attr('disabled',true);
						}else if(data.html['TRANSSTAT'] == 'Pendding'){ //สถานะรับบางส่วน ปิดการลบบิลโอน  แต่สามารถบันทึกได้หากมีสิทธิ์
							$('#add_EMPCARRY').attr('disabled',false);
							$('#add_MEMO1').attr('disabled',false);
							//$('#btnt2addSTRNo').attr('disabled',false);
							$('#btnt2del').attr('disabled',true);
							
							$('#btnt2addSTRNo').attr('disabled',true);
							
							if($('.tab1[name="home"]').attr('cup') == 'T'){ //มีสิทธิ์แก้ไขไหม
								if($('.tab1[name="home"]').attr('locat') == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
									$('#btnt2save').attr('disabled',false);
								}else{
									$('#btnt2save').attr('disabled',true);
								}
							}else{
								$('#btnt2save').attr('disabled',true);
							}
						}else{ //สถานะกำลังโอนย้ายรถ  เปิดการลบบิลโอน  และบันทึกข้อมูลได้หากมีสิทธิ์
							$('#add_EMPCARRY').attr('disabled',false);
							$('#add_MEMO1').attr('disabled',false);
							$('#btnt2addSTRNo').attr('disabled',false);
							$('#btnt2del').attr('disabled',false);
							$('#btnt2save').attr('disabled',false);
							
							if($('.tab1[name="home"]').attr('cup') == 'T'){ //มีสิทธิ์แก้ไขไหม
								if($('.tab1[name="home"]').attr('locat') == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
									$('#btnt2save').attr('disabled',false);	
									$('#btnt2addSTRNo').attr('disabled',false);
								}else{
									$('#btnt2save').attr('disabled',true);
									$('#btnt2addSTRNo').attr('disabled',true);
								}
							}else{								
								$('#btnt2save').attr('disabled',true);
								$('#btnt2addSTRNo').attr('disabled',true);
							}
							
							$('#btnt2del').show();
							if($('.tab1[name="home"]').attr('cdel') == 'T'){
								if($('.tab1[name="home"]').attr('locat') == data.html['TRANSFM']){ //เป็นสาขาตัวเองหรือไม่ ถ้าไม่ใช่ ห้ามแก้ไข
									$('#btnt2del').attr('disabled',false);
								}else{
									$('#btnt2del').attr('disabled',true);
								}
							}else{
								$('#btnt2del').attr('disabled',true);	
							}
						}

						var STRNO = data.html['STRNO'];
						for(var i=0;i<STRNO.length;i++){
							$('#table-STRNOTRANS tbody').append(STRNO[i]);
						}
						
						$('.tab1').hide();
						$('.tab2').show();
						//afterSelect();
						delSTRNO();
					}
				});
			});
		}
	});
}

$('#btnt1transfers').click(function(){
	$('.tab1').hide();
	$('.tab2').show();
	
	$('#add_TRANSNO').val('Auto Generate');
	$('#add_TRANSNO').attr('readonly',true);
	
	$('#add_TRANSDT').val('');
	$('#add_TRANSTO').val(null).trigger('change');
	$('#add_EMPCARRY').val('');
	$('#add_APPROVED').val(null).trigger('change');
	$('#add_TRANSSTAT').val('Pendding');
	$('#add_MEMO1').val('');
	
	var newOption;
	newOption = new Option($('.tab1[name="home"]').attr('locat'), $('.tab1[name="home"]').attr('locat'), false, false);
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
	
	if($('.tab1[name="home"]').attr('clev') == '1'){
		$('#add_TRANSFM').attr('disabled',false);	
	}else{
		$('#add_TRANSFM').attr('disabled',true);
	}
	
	if($('.tab1[name="home"]').attr('cin') == 'T'){
		$('#btnt2save').attr('disabled',false);	
		$('#btnt2addSTRNo').attr('disabled',false);	
	}else{
		$('#btnt2save').attr('disabled',true);	
		$('#btnt2addSTRNo').attr('disabled',true);								
	}
	
	$('#table-STRNOTRANS tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
	
	$('#btnt2del').hide();
	
	document.getElementById("table-fixed-STRNOTRANS").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});
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
				shown: function($this){
					$('#STRNOSearch').click(function(){
						//$('#fCRLOCAT option').remove();
						//$('#fCRLOCAT').append('<option val='+$('#add_TRANSFM').val()+'>'+$('#add_TRANSFM').val()+'</option>').trigger('change');
						
						dataToPost = new Object();
						dataToPost.fSTRNO = $('#fSTRNO').val();
						dataToPost.fMODEL = $('#fMODEL').val();
						dataToPost.fCRLOCAT = $('#fCRLOCAT').val();
						
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
									var STRNO = $(this).attr('STRNO');
									var TYPE = $(this).attr('TYPE');
									var MODEL = $(this).attr('MODEL');
									var BAAB = $(this).attr('BAAB');
									var COLOR = $(this).attr('COLOR');
									var CC = $(this).attr('CC');
									var CRLOCAT = $(this).attr('CRLOCAT');
									
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
											closeOnClick: false,
											delay: 5000,
											pauseDelayOnHover: true,
											continueDelayOnInactiveTab: false,
											icon: false,
											messageHeight: '90vh',
											msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
										});
									}else{
										var row = '<tr seq="new'+generate+'">';
										row += '<td><input type="button" class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'" value="ยกเลิก"></td>';
										row += '<td>'+STRNO+'</td>';
										row += '<td>'+TYPE+'</td>';
										row += '<td>'+MODEL+'</td>';
										row += '<td>'+BAAB+'</td>';
										row += '<td>'+COLOR+'</td>';
										row += '<td>'+CC+'</td>';
										row += '<td>อยู่ระหว่างการโอนย้ายรถ</td>';	
										row += '</tr>';
										  
										$('#table-STRNOTRANS tbody').append(row);
										generate = generate+1;
										delSTRNO();
									}
									
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
	/*
	$('.delSTRNO').click(function(){
		$('#table-STRNOTRANS tr[seq="'+($(this).attr('seq'))+'"]').remove();
	});  
	*/
	$('.delSTRNO').click(function(){ $(this).closest('tr').remove(); }); 
}





















