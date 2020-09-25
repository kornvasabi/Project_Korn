/********************************************************
             ______@21/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$("#SACTICOD").selectpicker();
	$("#Search_LOCAT").selectpicker();
	if(_locat == "OFFยน"){
		$('#Search_LOCAT').attr('disabled',false);
	}else{
		$('#Search_LOCAT').attr('disabled',true);
	}
});

var KBselectPickers = null;
var KBselectPickers_Cache = null;
//กิจกรรมการขาย
$('#SACTICOD').on('show.bs.select', function(e, clickedIndex, isSelected, previousValue) {
	$filter = $('#SACTICOD').parent().find("[aria-label=Search]");
	FN_KB_BSSELECT("SACTICOD",$filter,"getSACTICOD2");
}); 
$('#SACTICOD').parent().find("[aria-label=Search]").keyup(function(){
	FN_KB_BSSELECT("SACTICOD",$(this),"getSACTICOD2");
});
//สาขา
$("#Search_LOCAT").on('show.bs.select', function(e, clickedIndex, isSelected, previousValue){
	$filter = $("Search_LOCAT").parent().find("[aria-lable=Search]");
	FN_KB_BSSELECT("Search_LOCAT",$(this),"getLOCAT2");
});
$("#Search_LOCAT").parent().find("[aria-lable=Search]").keyup(function(){
	FN_KB_BSSELECT("Search_LOCAT",$(this),"getLOCAT2");
});
function FN_KB_BSSELECT($id,$thisSelected,$fn){
	var dataToPost = new Object();
	dataToPost.filter = $thisSelected.val();
	dataToPost.now    = (typeof $("#"+$id).selectpicker('val') == null ? "":$("#"+$id).selectpicker('val'));
	
	clearTimeout(KBselectPickers);
	KBselectPickers = setTimeout(function(){
		getdata();
	},250);
	
	function getdata(){
		KBselectPickers_Cache = $.ajax({
			url: '../SYS04/StandardReport/'+$fn,
			data: dataToPost,
			type: "POST",
			dataType: 'json',
			success: function(data){
				$("#"+$id).empty().append(data.opt);
				$("#"+$id).selectpicker('refresh');
				KBselectPickers_Cache = null;
			},
			beforeSend: function(){
				if(KBselectPickers_Cache !== null){
					KBselectPickers_Cache.abort();
				}
			}
			/*
			//Ajax error function this viwe
			,
			error: function(jqXHR, exception){
				fnAjaxERROR(jqXHR,exception);
			}
			*/
		});
	}
}
$('#btnsearchRP').click(function(){
	fn_SearchReport();
});
var searchreport = null;
function fn_SearchReport(){
	dataToPost = new Object();
	dataToPost.MODEL 		= $('#SMODEL').val();
	dataToPost.BAAB 		= $('#SBAAB').val();
	dataToPost.COLOR 		= $('#SCOLOR').val();
	//dataToPost.GCODE 		= $('#SGCODE').val();
	dataToPost.DOWN 		= $('#SDOWN').val();
	dataToPost.NOPAY 	    = $('#SNOPAY').val();
	dataToPost.Search_STDID = $('#Search_STDID').val();
	dataToPost.Search_SUBID = $('#Search_SUBID').val();
	dataToPost.EVENTDT      = $('#EVENTDT').val();
	dataToPost.SACTICOD     = $('#SACTICOD').val();
	dataToPost.Search_LOCAT = $('#Search_LOCAT').val();
	dataToPost.stat 		= $("input[name='stana']:checked").val();
	//dataToPost.payment      = $("input[name='payment']:checked").val();
	$('#loadding').fadeIn(250);
	searchreport = $.ajax({
		url: '../SYS04/StandardReport/Search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
			if(data.error){
				var msg = data.msg.length;
				var msgNum = "";
				for(i=0;i<msg;i++){
					if(i>0){msgNum += "<br>"; }
					msgNum += (i+1)+". "+data.msg[i];
				}
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: msgNum
				});
				$('#TableResultStandard').empty();
			}else{
				$('#TableResultStandard').html(data.html);
				fn_datatables('table-standard',1,250);
				$('.dataTables_scrollBody').css({'height':'calc(-500px + 100vh)'});
				fn_DetailCalculate();
			}
			searchreport = null;	
		},
		beforeSend: function(){
			if(searchreport !== null){searchreport.abort();}
		}
	});
}

function fn_DetailCalculate(){
	$('.detailCalculate').click(function(){
		//alert($(this).attr('SUBID'));
		dataToPost = new Object();
		dataToPost.STDID = $(this).attr('STDID');
		dataToPost.SUBID = $(this).attr('SUBID');
		dataToPost.DOWN  = $('#SDOWN').val();
		dataToPost.NOPAY = $('#SNOPAY').val();
		$('#loadding').fadeIn(250);
		$.ajax({
			url: '../SYS04/StandardReport/DetailCalculate',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(250);
				Lobibox.window({
					title:'Form CUSTOMER',
					width: $(window).width(),                
					height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					shown: function($this){
						$('.checkedtotal').change(function(){
							if($(this).is(':checked')){
								$('.checkall').each(function(){
									this.checked = true;
								});
							}else{
								$('.checkall').each(function(){
									this.checked = false;
								});
							}
						});
						$('.checkpayment').change(function (){
							var stdid = $(this).attr('id1');
							var subid = $(this).attr('id2');
							//alert(stdid+subid);
							var sumpay = [];
							$('.checkpayment input[type="checkbox"]:checked').each(function(){
								sumpay.push($(this).val());	
							});
							//alert(sumpay);
							dataToPost = new Object();
							dataToPost.DOWN 	= $('#SDOWN').val();
							dataToPost.NOPAY 	= $('#SNOPAY').val();
							dataToPost.stdid    = stdid;
							dataToPost.subid    = subid;
							dataToPost.sumpay   = sumpay;
							$.ajax({
								url: '../SYS04/StandardReport/Calculate',
								data: dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									fn_calculate(data);
								}
							});
						});
					}
				});
			}
		});
	});
}
function fn_calculate($data){
	/*
	$('.PRICE_AFTER_VAT').val($data.PRICE_AFTER_VAT);
	$('.PRICE_BEFORE_VAT').val($data.PRICE_BEFORE_VAT);
	$('.DWN_AFTER_VAT').val($data.DWN_AFTER_VAT);
	$('.DWN_BEFORE_VAT').val($data.DWN_BEFORE_VAT);
	$('.PRICEDOWN_AFTER_VAT').val($data.PRICEDOWN_AFTER_VAT);
	$('.PRICEDOWN_BEFORE_VAT').val($data.PRICEDOWN_BEFORE_VAT);
	$('.INTERAST_RATE').val($data.INTERAST_RATE);
	$('.INTERAST_RATE_MONTH').val($data.INTERAST_RATE_MONTH);
	$('.HP_AFTER_VAT').val($data.HP_AFTER_VAT);
	$('.HP_BEFORE_VAT').val($data.HP_BEFORE_VAT);
	$('.PRICEHP_BEFORE_VAT').val($data.PRICEHP_BEFORE_VAT);
	$('.PERMONTH_BEFORE_VAT').val($data.PERMONTH_BEFORE_VAT);
	$('.PRICEHP_AFTER_VAT').val($data.PRICEHP_AFTER_VAT);
	$('.PERMONTH_AFTER_VAT').val($data.PERMONTH_AFTER_VAT);
	$('.PRICEHP_AFTER_VAT_TOTAL').val($data.PRICEHP_AFTER_VAT_TOTAL);
	$('.PERMONTH_AFTER_VAT_TOTAL').val($data.PERMONTH_AFTER_VAT_TOTAL);
	*/
	$('.OPT_AFTER_VAT').val($data.OPT_AFTER_VAT);
	$('.OPT_BEFORE_VAT').val($data.OPT_BEFORE_VAT);
	$('.OPTPERMONTH_AFTER_VAT').val($data.OPTPERMONTH_AFTER_VAT);
	$('.OPT_AFTER_VAT_TOTAL').val($data.OPT_AFTER_VAT_TOTAL);
	$('.OPTPERMONTH_AFTER_VAT_TOTAL').val($data.OPTPERMONTH_AFTER_VAT_TOTAL);
	$('.HP_TOTAL').val($data.HP_TOTAL);
	$('.PERMONTH_TOTAL').val($data.PERMONTH_TOTAL);
	$('.PRICE_TOTAL').val($data.PRICE_TOTAL);
}
//checkbox class value
/*
$('.checkpayment input[type="checkbox"]').change(function (){
	var stdid = $(this).attr('stdid');
	var subid = $(this).attr('subid');
	$('.checkpayment').each(function(){
		var id1 = $(this).attr('id1');
		var id2 = $(this).attr('id2');
		if(id1 == stdid && id2 == subid){
			//alert(id1+id2);
			var a = [];
			$(".checkpayment input[type='checkbox']:checked").each(function (){
				if($(this).attr('subid') == id2 && $(this).attr('stdid') == id1){
					var b = [];
					var korn = (this.checked ? $(this).val() : "");
					b.push(korn);
					a.push(b);
				}
			});
			//alert(a);
			dataToPost = new Object();
			dataToPost.DOWN 	= $('#SDOWN').val();
			dataToPost.NOPAY 	= $('#SNOPAY').val();
			dataToPost.std1     = id1;
			dataToPost.std2     = id2;
			dataToPost.pay      = a;
			$.ajax({
				url: '../SYS04/StandardReport/search_payment_test',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					fn_calculate(data);
				}
			});
		}
	});
});
*/





















