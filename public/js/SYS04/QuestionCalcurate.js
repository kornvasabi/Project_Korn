var calc = null;
function search(){
	dataToPost = new Object();
	dataToPost.price = $('#price').val();
	dataToPost.dwn   = $('#dwn').val();
	dataToPost.vat   = $('#vat').val();
	dataToPost.opt   = $('#opt').val();
	dataToPost.intrt = $('#intrt').val();
	dataToPost.nopay = $('#nopay').val();
	dataToPost.dcm   = $('#dcm').val();
	$("#loadding").fadeIn(200);
	calc = $.ajax({
		url:'../SYS04/Question/CalcuratePrice',
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
				$("#result").html(data.html);				
			}
			$("#loadding").fadeOut(300);
			calc = null;
		},
		beforeSend: function(){
			if(calc !== null){ calc.abort(); }
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
$('#calc').click(function(){ search(); });

$('#price').keyup(function(e){ if(e.keyCode === 13){ $('#dwn').focus(); } });
$('#dwn').keyup(function(e){ if(e.keyCode === 13){ $('#opt').focus(); } });
$('#opt').keyup(function(e){ if(e.keyCode === 13){ $('#intrt').focus(); } });
$('#intrt').keyup(function(e){ if(e.keyCode === 13){ $('#nopay').focus(); } });
$('#nopay').keyup(function(e){ if(e.keyCode === 13){ $('#dwn').focus(); } });
$('#nopay').keyup(function(e){ if(e.keyCode === 13){ search();$('#price').focus(); } });

var OBJbtn_penalty = null;
$('#btn_penalty').click(function(){
	var _form = "<div class='row'>";
	_form += "<div class='col-sm-3'>";
	_form += "	<div class='form-group'>เลขที่สัญญา<input type='text' id='penalty_contno' class='form-control'></div>";
	_form += "</div>";
	_form += "<div class='col-sm-3'>";
	_form += "	<div class='form-group'>วันที่<input type='text' id='penalty_caldt' class='form-control' data-provide='datepicker' data-date-language='th-th'></div>";
	_form += "</div>";
	_form += "<div class='col-sm-3'>";
	_form += "	<br><button id='btnPenalty' class='form-control'> คำนวณ</button>";
	_form += "</div>";
	_form += "</div>";
	_form += "<div class='row col-sm-12' id='penalty_result' style='height:calc(100% - 100px);overflow:auto;'></div>";
	
	Lobibox.window({
		title: 'ดอกเบี้ยปรับ',
		width: $(window).width(),
		height: $(window).height(),
		content: _form,
		draggable: false,
		closeOnEsc: false,
		shown: function($this){
			$('#btnPenalty').click(function(){
				var dataToPost = new Object();
				dataToPost.CONTNO = $('#penalty_contno').val();
				dataToPost.CALDT  = $('#penalty_caldt').val();
				$("#loadding").fadeIn(200);
				OBJbtn_penalty = $.ajax({
					url:'../SYS04/Question/CalPenalty',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					beforeSend: function(){ if(OBJbtn_penalty !== null) { OBJbtn_penalty.abort(); } },
					success: function(data){
						$('#penalty_result').html(data.html);
						fn_datatables('tbCalPenalty',2,320);
						
						OBJbtn_penalty = null;
						$("#loadding").fadeOut(200);
					}
				});
			});
		},
		beforeClose : function(){
			$('#btnt1leasing').attr('disabled',false);
		}
	});		
});