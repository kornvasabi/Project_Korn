/********************************************************
             ______@--/--/2018______
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
		$('#btnt1received').attr('disabled',false);	
		
	}else{
		$('#btnt1received').attr('disabled',true);
	}
	
	initPage();
});

function initPage(){
	$('#add_TRANSNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTransfercars',
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
}

$('#add_TRANSNO').change(function(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $(this).val();
		
	$.ajax({
		url:'../SYS02/Creceivedcars/getReceivedDATA',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#add_TRANSDT').val(data.html['TRANSDT']);
			$('#add_TRANSFM').val(data.html['TRANSFM']);
			$('#add_TRANSTO').val(data.html['TRANSTO']);
			$('#add_EMPCARRY').val(data.html['EMPCARRY']);
			$('#add_APPROVED').val(data.html['APPNAME']);
			$('#add_TRANSSTAT').val(data.html['TRANSSTATDesc']);
			$('#add_MEMO1').val(data.html['MEMO1']);
			$('#add_MOVEDT').val(data.html['MOVEDT']);
			
			$('#add_TRANSDT').attr('disabled',true);
			$('#add_TRANSFM').attr('readonly',true);
			$('#add_TRANSTO').attr('readonly',true);
			$('#add_EMPCARRY').attr('readonly',true);
			$('#add_APPROVED').attr('readonly',true);
			$('#add_TRANSSTAT').attr('readonly',true);
			$('#add_MEMO1').attr('readonly',true);
			
			if(data.html['TRANSSTAT'] == 'Sendding'){
				$('#add_MOVEDT').attr('disabled',false);
			}else{
				$('#add_MOVEDT').attr('disabled',true);
			}
			
			if(data.html['TRANSSTAT'] == "Received"){
				$('#btnt2save').attr('disabled',true);
				$('#btnt2addSTRNO').attr('disabled',true);
				
			}else{
				$('#btnt2save').attr('disabled',false);
				$('#btnt2addSTRNO').attr('disabled',false);
			}
			
			$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
			var STRNO = data.html['STRNO'];
			for(var i=0;i<STRNO.length;i++){
				$('#table-option tbody').append(STRNO[i]);
			}
			
			delSTRNO();
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

var generate = 1;
$('#btnt2addSTRNO').click(function(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $('#add_TRANSNO').val();
	
	$.ajax({
		url:'../SYS02/Creceivedcars/addSTRNO',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				shown: function($this){
					document.getElementById("table-fixed-addSTRNO").addEventListener("scroll", function(){
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
						
						var stat = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
						$('#table-option tr').each(function() {
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
							  
							$('#table-option tbody').append(row);
							generate = generate+1;
							delSTRNO();
						}
						
						$this.destroy();
					});
				}
			});
		}
	});
});

$('#btnt1search').click(function(){ search(); });

function search(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $('#TRANSNO').val();
	dataToPost.TRANSDT = $('#TRANSDT').val();
	dataToPost.MOVEDT = $('#MOVEDT').val();
	dataToPost.TRANSTO = $('#TRANSTO').val();
	dataToPost.TRANSSTAT = $('#TRANSSTAT').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1received').html('');
	$('#resultt1received').append(spinner);
	
	$.ajax({
		url:'../SYS02/Creceivedcars/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt1received').find('.spinner, .spinner-backdrop').remove();
			$('#resultt1received').html(data.html);
			
			document.getElementById("table-fixed-Creceivedcars").addEventListener("scroll", function(){
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
			
				/*
				var spinner = $('body>.spinner').clone().removeClass('hide');
				$('.tab2').html('');
				$('.tab2').append(spinner);
				*/
				
				$.ajax({
					url:'../SYS02/Creceivedcars/getDetails',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
						
						var newOption;
						
						newOption = new Option(data.html['TRANSNO'], data.html['TRANSNO'], false, false);
						$('#add_TRANSNO').empty();
						$('#add_TRANSNO').append(newOption); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
						$('#add_TRANSNO').attr('disabled',true).trigger('change');
						
						$('.tab1').hide();
						$('.tab2').show();
						
						/*
						$('#add_TRANSDT').val(data.html['TRANSDT']);						
						$('#add_TRANSFM').val(data.html['TRANSFM']);						
						$('#add_TRANSTO').val(data.html['TRANSTO']);
						$('#add_EMPCARRY').val(data.html['EMPCARRY']);
						$('#add_APPROVED').val(data.html['APPROVNM']);
						$('#add_TRANSSTAT').val(data.html['TRANSSTATDesc']);
						$('#add_MEMO1').val(data.html['MEMO1']);
						$('#add_MOVEDT').val(data.html['MOVEDT']);
						
						$('#add_TRANSNO').attr('disabled',true).trigger('change');
						$('#add_TRANSDT').attr('disabled',true);
						$('#add_TRANSFM').attr('disabled',true);
						$('#add_TRANSTO').attr('disabled',true);
						$('#add_APPROVED').attr('disabled',true);
						$('#add_TRANSSTAT').attr('disabled',true);
						$('#add_MOVEDT').attr('disabled',true);
						
						if(data.html['TRANSSTAT'] == 'Received'){
							$('#add_EMPCARRY').attr('disabled',true);							
							$('#add_MEMO1').attr('disabled',true);
							$('#btnt2addSTRNo').attr('disabled',true);
							$('#btnt2del').attr('disabled',true);							
							$('#btnt2save').attr('disabled',true);
						}else if(data.html['TRANSSTAT'] == 'Pendding'){
							$('#add_EMPCARRY').attr('disabled',false);
							$('#add_MEMO1').attr('disabled',false);
							$('#btnt2addSTRNo').attr('disabled',true);
							$('#btnt2del').attr('disabled',true);
							$('#btnt2save').attr('disabled',false);
							
							if($('.tab1[name="home"]').attr('cup') == 'T'){
								$('#btnt2save').attr('disabled',false);	
							}else{
								$('#btnt2save').attr('disabled',true);
							}
						}else{
							$('#add_EMPCARRY').attr('disabled',false);
							$('#add_MEMO1').attr('disabled',false);
							$('#btnt2addSTRNo').attr('disabled',false);
							$('#btnt2del').attr('disabled',false);
							$('#btnt2save').attr('disabled',false);
							
							if($('.tab1[name="home"]').attr('cup') == 'T'){
								$('#btnt2save').attr('disabled',false);	
								$('#btnt2addSTRNo').attr('disabled',false);	
							}else{
								$('#btnt2save').attr('disabled',true);	
								$('#btnt2addSTRNo').attr('disabled',true);								
							}
							
							if($('.tab1[name="home"]').attr('cdel') == 'T'){
								$('#btnt2del').show();
								$('#btnt2del').attr('disabled',false);	
							}else{
								$('#btnt2del').show();
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
						*/
					}
				});
			});
		}
	});
}


$('#btnt1received').click(function(){
	$('.tab1').hide();
	$('.tab2').show();
	
	$('#add_TRANSNO').val(null).trigger('change');
	
	$('#add_TRANSDT').val('');
	$('#add_TRANSFM').val('');
	$('#add_TRANSTO').val('');
	$('#add_EMPCARRY').val('');
	$('#add_APPROVED').val('');
	$('#add_TRANSSTAT').val('');
	$('#add_MEMO1').val('');
	$('#add_MOVEDT').val('');
	
	$('#add_TRANSNO').attr('disabled',false).trigger('change');
	$('#add_TRANSDT').attr('disabled',true);
	$('#add_TRANSTO').attr('disabled',true);
	$('#add_APPROVED').attr('disabled',true);
	$('#add_TRANSSTAT').attr('disabled',true);
	$('#add_EMPCARRY').attr('disabled',true);
	$('#add_MEMO1').attr('disabled',true);
	$('#add_MOVEDT').attr('disabled',false);
	
	/*	
	if($('.tab1[name="home"]').attr('clev') == '1'){
		$('#add_TRANSFM').attr('disabled',false);	
	}else{
		$('#add_TRANSFM').attr('disabled',true);
	}
	*/
	
	if($('.tab1[name="home"]').attr('cin') == 'T'){
		$('#btnt2save').attr('disabled',false);	
		$('#btnt2addSTRNo').attr('disabled',false);	
	}else{
		$('#btnt2save').attr('disabled',true);	
		$('#btnt2addSTRNo').attr('disabled',true);								
	}
	
	$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
	
	document.getElementById("table-fixed-STRNOTRANS").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});
});

$('#btnt2home').click(function(){
	$('.tab1').show();
	$('.tab2').hide();
});


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
				dataToPost.MOVEDT = $('#add_MOVEDT').val();
				
				var STRNO = [];	
				$('#table-option tr').each(function() {
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
					url:'../SYS02/Creceivedcars/saveReceivedCAR',
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
























