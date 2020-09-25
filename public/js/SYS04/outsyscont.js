/********************************************************
             ______@14/05/2020______
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
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCUSTOMER',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? "":$('#LOCAT').find(':selected').val());
				dataToPost.vatstop = "cancel";
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});
$('#btnsearch').click(function(){
	fn_search();
});
var getoutsys = null;
function fn_search(){
	dataToPost = new Object();
	dataToPost.CUSCOD  = (typeof $('#CUSCOD').find(':selected').val() === 'undefined' ? "":$('#CUSCOD').find(':selected').val());
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.STRNO   = $('#STRNO').val();
	dataToPost.F_SDATE = $('#F_SDATE').val();
	dataToPost.T_SDATE = $('#T_SDATE').val();
	$('#dataTables-outsyscont tbody').html('');
	$('#dataTables-outsyscont tbody').html("<table width='100%' height='100%'><tr><td colspan='9'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
	
	//$('#loadding').fadeIn(250);
	getoutsys = $.ajax({
		url: '../SYS04/Question/Searchoutsys',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//$('#loadding').fadeOut(250);
			
			var newOption = new Option(data.custext, data.cusid, false, false);
            $('#CUSCOD').empty().append(newOption).trigger('change');
			$('#CONTNO').val(data.contno);
			$('#STRNO').val(data.strno);
			
			$('#dataTables-outsyscont tbody').empty().append(data.outsys);
			document.getElementById("dataTable-fixed-outsyscont").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			$('.getit').hover(function(){
				$(this).css({'background-color':'#a9a9f9'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
			},function(){
				$(this).css({'background-color':'white'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
			});
			
			$('#dataTables-harpay tbody').empty().append(data.harpay);
			document.getElementById("dataTable-fixed-harpay").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			$('.getit').click(function(){
				var contno = $(this).attr('CONTNO');
				alert("รอดำเนินการแก้ไขอยู่ครับ");
				//fn_getharpay(contno);
			});
			getoutsys = null;
		},
		beforeSend: function(){
			if(getoutsys !== null){
				getoutsys.abort();
			}
		}
	});
}
function fn_getharpay(contno){
	//alert(contno);
	dataToPost = new Object();
	dataToPost.CONTNO = contno;
	$('#loadding').fadeIn(250);
	$.ajax({
		url: '../SYS04/Question/getHarpayDetail',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
			
			$('#dataTables-harpay tbody').empty().append(data.harpay);
			document.getElementById("dataTable-fixed-harpay").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
		}
	});
}