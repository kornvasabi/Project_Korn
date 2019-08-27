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
var _locat  = $('.tab1[name="home"]').attr('locat');


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
	
	document.getElementById("table-fixed-option").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
		this.querySelector("thead").style.zIndex 	= 1000;						
	});
	
	initPage();
});

function initPage(){
	$('#add_TRANSNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTransfercars',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCOD').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TRANSTO = $('#add_TRANSTO').val()
				
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
		//theme: 'classic',
		width: '100%'
	});
}

$('#add_TRANSNO').change(function(){
	dataToPost = new Object();
	dataToPost.TRANSNO = (typeof $(this).find(':selected').val() === 'undefined' ? '' : $(this).find(':selected').val());
	
	$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
	$('#loadding').show();	
	
	$.ajax({
		url:'../SYS02/Creceivedcars/getReceivedDATA',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#loadding').hide();
			
			$('#add_TRANSDT').val(data.html['TRANSDT']);
			$('#add_TRANSFM').val(data.html['TRANSFM']);
			$('#add_TRANSTO').val(data.html['TRANSTO']);
			$('#add_EMPCARRY').val(data.html['EMPCARRY']);
			$('#add_APPROVED').val(data.html['APPNAME']);
			$('#add_TRANSSTAT').val(data.html['TRANSSTATDesc']);
			$('#add_MEMO1').val(data.html['MEMO1']);
			//@190214-$('#add_MOVEDT').val(data.html['MOVEDT']);
			
			$('#add_TRANSDT').attr('disabled',true);
			$('#add_TRANSFM').attr('readonly',true);
			$('#add_TRANSTO').attr('readonly',true);
			$('#add_EMPCARRY').attr('readonly',true);
			$('#add_APPROVED').attr('readonly',true);
			$('#add_TRANSSTAT').attr('readonly',true);
			$('#add_MEMO1').attr('readonly',true);
			
			/*
			//@190214-
			if(data.html['TRANSSTAT'] == 'Sendding'){
				$('#add_MOVEDT').attr('disabled',false);
			}else{
				$('#add_MOVEDT').attr('disabled',true);
			}
			*/
			
			if(data.html['TRANSSTAT'] == "Received"){
				$('#btnt2save').attr('disabled',true);
				$('#btnt2addSTRNO').attr('disabled',true);
				
			}else{
				//$('#btnt2save').attr('disabled',false);
				//$('#btnt2addSTRNO').attr('disabled',false);
				if(_locat == $('#add_TRANSTO').val() || _level == 1){
					$('#btnt2addSTRNO').attr('disabled',false);
					$('#btnt2save').attr('disabled',false);
				}else{
					$('#btnt2addSTRNO').attr('disabled',true);
					$('#btnt2save').attr('disabled',true);
				}
			}
			
			var STRNO = data.html['STRNO'];
			
			$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน
			for(var i=0;i<STRNO.length;i++){
				$('#table-option tbody').append(STRNO[i]);
			}
			
			delSTRNO();
		}
	});
});

function delSTRNO(){
	$('.delSTRNO').click(function(){ $(this).closest('tr').remove(); }); 
}

var generate = 1;
$('#btnt2addSTRNO').click(function(){
	dataToPost = new Object();
	dataToPost.TRANSNO = $('#add_TRANSNO').val();
	
	$('#loadding').show();
	
	$.ajax({
		url:'../SYS02/Creceivedcars/addSTRNO',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#loadding').hide();
			
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				closeOnEsc: false,
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
						//var TYPE = $(this).attr('TYPE');
						var MODEL = $(this).attr('MODEL');
						var BAAB = $(this).attr('BAAB');
						var COLOR = $(this).attr('COLOR');
						var GCODE = $(this).attr('GCODE');
						var TRANSDT = $(this).attr('TRANSDT');
						var EMPCARRYNM = $(this).attr('EMPCARRYNM');
						
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
							var row = '<tr seq="new'+generate+'">';
							row += '<td><input type="button" class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'" value="ยกเลิก"></td>';
							row += '<td>'+STRNO+'</td>';
							//row += '<td>'+TYPE+'</td>';
							row += '<td>'+MODEL+'</td>';
							row += '<td>'+BAAB+'</td>';
							row += '<td>'+COLOR+'</td>';
							row += '<td>'+GCODE+'</td>';
							row += '<td>อยู่ระหว่างการโอนย้ายรถ</td>';	
							row += '<td>'+TRANSDT+'<br>-</td>';	
							row += '<td>'+EMPCARRYNM+'<br>-</td>';	
							
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
			
			/*
			document.getElementById("table-fixed-Creceivedcars").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
			*/
			$('#table-Creceivedcars').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-Creceivedcars',1,325);
			
			/*
			// Export data to Excel
			$('.data-export').prepend('<img id="table-Ctransferscars-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-Ctransferscars-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ข้อมูลการรับโอน","Received"); 
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
					dataToPost.cup = $('.tab1[name="home"]').attr('cup');
				
					$('#loadding').show();	
					$('#table-option tbody tr').remove(); //ลบข้อมูลเลขตัวถังเดิมออกก่อน				
					
					var newOption;						
					newOption = new Option($(this).attr('TRANSNO'), $(this).attr('TRANSNO'), false, false);
					$('#add_TRANSNO').empty();
					$('#add_TRANSNO').append(newOption); //กรณี select2 ไม่มี option จะต้อง append ค่าให้ใหม่
					$('#add_TRANSNO').attr('disabled',true).trigger('change');
					
					$('.tab1').hide();
					$('.tab2').show();
				});
			}
		}
	});
}

/*
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
	//@190214-$('#add_MOVEDT').val('');
	
	$('#add_TRANSNO').attr('disabled',false).trigger('change');
	$('#add_TRANSDT').attr('disabled',true);
	$('#add_TRANSTO').attr('disabled',true);
	$('#add_APPROVED').attr('disabled',true);
	$('#add_TRANSSTAT').attr('disabled',true);
	$('#add_EMPCARRY').attr('disabled',true);
	$('#add_MEMO1').attr('disabled',true);
	//@190214-$('#add_MOVEDT').attr('disabled',false);
	
	/*	
	if($('.tab1[name="home"]').attr('clev') == '1'){
		$('#add_TRANSFM').attr('disabled',false);	
	}else{
		$('#add_TRANSFM').attr('disabled',true);
	}
	-------------------------------------------------------------/
	
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
*/

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
				//@190214-dataToPost.MOVEDT = $('#add_MOVEDT').val();
				
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
								size: 'mini',
								closeOnClick: false,
								delay: 5000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.msg
							});
							
							$('#add_TRANSNO').trigger('change');
							/*
							$('.tab1').show();
							$('.tab2').hide();
							search();
							*/
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
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					delay: 5000,
					closeOnClick: false,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: 'ยกเลิกการบันทึกแล้ว'
				});
			}
		}
	});
});
























