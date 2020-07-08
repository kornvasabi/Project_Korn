/********************************************************
             ______@03/06/2020______
			 Pasakorn Boonded

********************************************************/
$(function(){
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
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
	/*
	$('#CONTNO').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getCONTNO_RP',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
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
	*/
	$('#GCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGCODE',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
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
	$('#OFFICER').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
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
$('#btnaddcont').click(function(){
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2K/getfromCONTNO',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'FORM SEARCH',
				//width: $(window).width(),
				//height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($this){
					$('#loadding').fadeOut(200);
					var kb_cont_search = null;
					$('#cont_search').click(function(){ fnResultCONTNO(); });
					function fnResultCONTNO(){
						dataToPost = new Object();
						dataToPost.s_contno = $('#s_contno').val();
						dataToPost.s_name1  = $('#s_name1').val();
						dataToPost.s_name2  = $('#s_name2').val();
						$('#loadding').fadeIn(200);
						kb_cont_search = $.ajax({
							url:'../Cselect2K/getResultCONTNO',
							data:dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#loadding').fadeOut(200);
								$('#cont_result').html(data.html);
								$('.getit').hover(function(){
									$(this).css({'background-color':'#a9a9f9'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
								},function(){
									$(this).css({'background-color':''});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
								});
								$('.getit').unbind('click');
								$('.getit').click(function(){
									cln = new Object();
									cln.contno  = $(this).attr('CONTNO');
									cln.locat   = $(this).attr('LOCAT');
									$('#CONTNO').val(cln.contno);
									var newOption = new Option(cln.locat,cln.locat, false, false);
									$('#LOCAT').empty().append(newOption).trigger('change');
									$this.destroy();
								});
								kb_cont_search = null;
							},
							beforeSend: function(){
								if(kb_cont_search !== null){ kb_cont_search.abort(); }
							}
						});
					}
				},
				beforeClose : function(){
					
				}
			});
		}
	});
});
$('#TODATE').change(function(){
	dataToPost = new Object();
	dataToPost.TODATE = $('#TODATE').val();
	$.ajax({
		url: '../SYS06/SYDIncomeMonth/getendmonth',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#TODATE').val(data.TODATE);
		}
	});
});
$('#btnreport').click(function(){
	printReport();
});
var IM_SYD = null;
function printReport(){
	var order = null;
	if($('#OR1').is(':checked')){
		order = "LOCAT";
	}else if($('#OR2').is(':checked')){
		order = "CONTNO";
	}else if($('#OR3').is(':checked')){
		order = "CUSCOD";
	}
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.TODATE  = $('#TODATE').val();
	dataToPost.GCODE   = OFFICER = (typeof $('#GCODE').find(':selected').val() === 'undefined' ? '':$('#GCODE').find(':selected').val());
	dataToPost.OFFICER = (typeof $('#OFFICER').find(':selected').val() === 'undefined' ? '':$('#OFFICER').find(':selected').val());
	dataToPost.order   = order;
	IM_SYD = $.ajax({
		url:'../SYS06/SYDIncomeMonth/conditiontopdf',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/SYDIncomeMonth/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title:'พิมพ์รายงาน',
				content:content,
				closeOnEsc:false,
				height:$(window).height(),
				width:$(window).width()
			});
			IM_SYD = null;
		},
		beforeSend:function(){
			if(IM_SYD !== null){IM_SYD.abort();}
		}
	});	
}