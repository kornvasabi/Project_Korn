/********************************************************
             ______@23/07/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	function initPage(){
		$('#wizard-financedetail').bootstrapWizard({
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
				switch(index){
					case 0: //tab1
						nextTab(ind2); 
						break;
					case 1: //tab2						
						nextTab(ind2); 
						break;
					case 2: //tab3
						nextTab(ind2);
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
	$('#btnremove').click(function(){
		$('#add_strno').val('');
	});
});
$('#add_strno').click(function(){
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2K/getfromSTRNO',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			Lobibox.window({
				title: 'FORM SEARCH',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					var kb_searchstr = null;
					$('#str_search').click(function(){ fnResultSTRNO(); });
					function fnResultSTRNO(){
						dataToPost = new Object();
						dataToPost.s_strno  = $('#s_strno').val();
						dataToPost.s_type   = $('#s_type').val();
						dataToPost.s_model  = $('#s_model').val();
						$('#loadding').fadeIn(200);
						kb_searchstr = $.ajax({
							url:'../Cselect2K/getResultSTRNO',
							data:dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#str_result').html(data.html);
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});								
								$('.getit').unbind('click');
								$('.getit').click(function(){
									str = new Object();
									str.strno = $(this).attr('STRNO');
									$('#add_strno').val(str.strno);
									$this.destroy();
									fn_ResultStrStock();
								});
								
								$('#loadding').fadeOut(200);
								kb_searchstr = null;
							},
							beforeSend: function(){
								if(kb_searchstr !== null){ kb_searchstr.abort(); }
							}
						});
					}
				}
			});
		}
	});
});
$('#btnsearchstock').click(function(){
	fn_ResultStrStock();
});
var KB_ResultStr = null;
function fn_ResultStrStock(){
	dataToPost = new Object();
	dataToPost.STRNO = $('#add_strno').val();
	$('#loadding').fadeIn(200);
	KB_ResultStr = $.ajax({
		url: '../SYS02/Askstockbystrno/Searchstockbystrno',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);	
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
					soundExt: '.ogg',
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				fn_Cleardata();
			}else{
				fn_detailstockcar(data);
			}
			KB_ResultStr = null;
		},
		beforeSend:function(){
			if(KB_ResultStr !== null){KB_ResultStr.abort();}
		}
	});
}
function fn_detailstockcar($data){
	$('#STRNO').val($data.STRNO);
	$('#REGNO').val($data.REGNO);
	$('#REFNO').val($data.REFNO);
	$('#KEYNO').val($data.KEYNO);
	$('#CUSCOD').val($data.CUSCOD);
	$('#NAME1').val($data.NAME1);
	$('#NAME2').val($data.NAME2);
	$('#CONTNO').val($data.CONTNO);
	$('#TSALE').val($data.TSALE);
	$('#TYPE').val($data.TYPE);
	$('#MODEL').val($data.MODEL);
	$('#BAAB').val($data.BAAB);
	$('#COLOR').val($data.COLOR);
	$('#CC').val($data.CC);
	$('#STATNOW').val($data.STATNOW);
	$('#STAT').val($data.STAT);
	$('#RECVNO').val($data.RECVNO);
	$('#RECVDT').val($data.RECVDT);
	$('#RVLOCAT').val($data.RVLOCAT);
	$('#TAXNO').val($data.TAXNO);
	$('#TAXDT').val($data.TAXDT);
	$('#APCODE').val($data.APCODE);
	$('#ENGNO').val($data.ENGNO);
	$('#CRLOCAT').val($data.CRLOCAT);
	$('#NADDCOST').val($data.NADDCOST);
	$('#VADDCOST').val($data.VADDCOST);
	$('#TADDCOST').val($data.TADDCOST);
	$('#TOTCOSTCAR').val($data.TOTCOSTCAR);
	
	$('#GCODE').val($data.GCODE);
	$('#COSTAMT').val($data.COSTAMT);
	$('#VATAMT').val($data.VATAMT);
	$('#TOTCOST').val($data.TOTCOST);
	
	$('#dataTables-listservice tbody').empty().append($data.listservic);
	$('#dataTables-listmove tbody').empty().append($data.listmove);
}
function fn_Cleardata(){
	$('#STRNO').val("");
	$('#REGNO').val("");
	$('#REFNO').val("");
	$('#KEYNO').val("");
	$('#CUSCOD').val("");
	$('#NAME1').val("");
	$('#NAME2').val("");
	$('#CONTNO').val("");
	$('#TSALE').val("");
	$('#TYPE').val("");
	$('#MODEL').val("");
	$('#BAAB').val("");
	$('#COLOR').val("");
	$('#CC').val("");
	$('#STATNOW').val("");
	$('#STAT').val("");
	$('#RECVNO').val("");
	$('#RECVDT').val("");
	$('#RVLOCAT').val("");
	$('#TAXNO').val("");
	$('#TAXDT').val("");
	$('#APCODE').val("");
	$('#ENGNO').val("");
	$('#CRLOCAT').val("");
	$('#NADDCOST').val("");
	$('#VADDCOST').val("");
	$('#TADDCOST').val("");
	$('#TOTCOSTCAR').val("");
	
	$('#COSTAMT').val("");
	$('#VATAMT').val("");
	$('#TOTCOST').val("");
	
	$('#dataTables-listservice tbody').empty();
	$('#dataTables-listmove tbody').empty();
}

