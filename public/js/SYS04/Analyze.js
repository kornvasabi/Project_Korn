/********************************************************
             ______@27/08/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

$("#SANSTAT").select2({minimumResultsForSearch: -1,width: '100%'});

var divcondition = $(".divcondition").height() ;
$("#result").css({
	//'height':'calc(100vh - '+divcondition+'px)',		
	'width':'100%'
});	

var JDbtnt1search = null;
$("#btnt1search").click(function(){
	dataToPost = new Object();
	dataToPost.SSTRNO 		= $("#SSTRNO").val();
	dataToPost.SMODEL 		= $("#SMODEL").val();
	dataToPost.SCREATEDATEF = $("#SCREATEDATEF").val();
	dataToPost.SCREATEDATET = $("#SCREATEDATET").val();
	dataToPost.SAPPROVEF 	= $("#SAPPROVEF").val();
	dataToPost.SAPPROVET 	= $("#SAPPROVET").val();
	dataToPost.SRESVNO 		= $("#SRESVNO").val();
	dataToPost.SCUSNAME 	= $("#SCUSNAME").val();
	dataToPost.SANSTAT 		= (typeof $("#SANSTAT").find(":selected").val() === 'undefined' ? "":$("#SANSTAT").find(":selected").val());
	
	$('#loadding').fadeIn(500);
	JDbtnt1search = $.ajax({
		url:'../SYS04/Analyze/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			$("#result").html(data.html);
		
			$("#table-fixed-Analyze").show(0);
			$("#table-fixed-Analyze-detail").hide(0);
			
			$('#table-Analyze').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-Analyze',1,divcondition + 100);
			//fn_datatables('table-Analyze',1,505);
			
			// Export data to Excel
			$('.data-export').prepend('<img id="table-Analyze-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-Analyze-excel").click(function(){ 	
				tableToExcel_Export(data.html,"ใบวิเคราะห์","Analyze"); 
			});
			
			function redraw(){
				var JDandetail = null;
				$(".andetail").unbind('click');
				$(".andetail").click(function(){
					dataToPost = new Object();
					dataToPost.ANID = $(this).attr('ANID');
					
					$('#loadding').fadeIn(500);
					JDandetail = $.ajax({
						url:'../SYS04/Analyze/searchDetail',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(200);
							
							Lobibox.window({
								title: 'รายการวิเคราะห์สินเชื่อ',
								width: $(window).width(),
								height: $(window).height(),
								content: data.html,
								draggable: false,
								closeOnEsc: false,
								shown: function($this){
									$("#back").click(function(){
										$this.destroy();
									});
								}
							});
							//$("#table-fixed-Analyze-detail").html(data.html);
							
							/*
							$("#table-fixed-Analyze").hide(0);
							$("#table-fixed-Analyze-detail").show(0);
							
							$("#back").click(function(){
								$("#table-fixed-Analyze").show(0);
								$("#table-fixed-Analyze-detail").hide(0);
								$("#table-fixed-Analyze-detail").html('');
							});
							*/
							
							// ซ่อนปุ่มอนุมัติ
							if(_update == "T"){
								$("#approve").show();
							}else{
								$("#approve").hide();
							}
							
							$('.cushistory').click(function(){
								dataToPost = new Object();
								dataToPost.cuscod = $(this).attr('cuscod');
								
								$.ajax({
									url:'../SYS04/Analyze/getCusHistory',
									data: dataToPost,
									type: 'POST',
									dataType: 'json',
									beforeSend: function(){
										$("#approve").attr("disabled",true);
										$("#back").attr("disabled",true);
									},
									success: function(data){
										Lobibox.window({
											title: 'ประวัติ',
											width: 700,
											//height: $(window).height(),
											content: data.html,
											draggable: true,
											closeOnEsc: true,
											shown:function(){
												fn_datatables(data.tableName,1,100,'YES');
											}
										});
									}
								});
							});
							
							var JDapprove = null;
							$("#approve").click(function(){
								$.ajax({
									url:'../SYS04/Analyze/formApproved',
									data:'',
									type:'POST',
									dataType:'json',
									beforeSend: function(){
										$("#approve").attr("disabled",true);
										$("#back").attr("disabled",true);
									},
									success: function(data){
										Lobibox.confirm({
											title: 'ยืนยันการทำรายการ',
											draggable: true,
											iconClass: false,
											closeOnEsc: false,
											closeButton: false,
											msg: data.html,
											buttons: {
												ok : {
													'class': 'btn btn-primary glyphicon glyphicon-ok',
													text: ' ยืนยันการทำรายการ',
													closeOnClick: false,
												},
												cancel : {
													'class': 'btn btn-danger glyphicon glyphicon-remove',
													text: ' ยกเลิก',
													closeOnClick: true
												},
											},
											shown: function($this){
												$(this).css({
													'background': 'rgba(0, 0, 0, 0) url("../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png") repeat scroll 0% 0%'
												});
												
												$("#APPTYPE").select2({ 
													placeholder: 'เลือก',
													width:'100%',
													dropdownParent: $('#APPTYPE').parent().parent(),
													minimumResultsForSearch: -1
												});
												
												$("#APPTYPE").on("select2:select",function(){
													if($(this).find(":selected").val() == "A"){
														$("#APPTYPE ,#APPCOMMENT").css({
															'color':'green'
														});
													}else{
														$("#APPTYPE ,#APPCOMMENT").css({
															'color':'red'
														});
													}
												});	
											},
											callback: function(lobibox, type){
												if (type === 'ok'){
													if($("#APPTYPE").find(":selected").val() == ""){
														Lobibox.notify('warning', {
															title: 'แจ้งเตือน',
															size: 'mini',
															closeOnClick: false,
															delay: 5000,
															pauseDelayOnHover: true,
															continueDelayOnInactiveTab: false,
															icon: true,
															messageHeight: '90vh',
															msg: "คุณยังไม่ได้ระบุรายการอนุมัติ"
														});
													}else if($("#APPCOMMENT").val() == ""){
														Lobibox.notify('warning', {
															title: 'แจ้งเตือน',
															size: 'mini',
															closeOnClick: false,
															delay: 5000,
															pauseDelayOnHover: true,
															continueDelayOnInactiveTab: false,
															icon: true,
															messageHeight: '90vh',
															msg: "คุณยังไม่ได้ระบุผลการวิเคราห์"
														});
													}else{
														dataToPost.apptype = $("#APPTYPE").find(":selected").val();
														dataToPost.comment = $("#APPCOMMENT").val();
														
														$('#loadding').fadeIn(500);
														JDapprove = $.ajax({
															url:'../SYS04/Analyze/approved',
															data: dataToPost,
															type: 'POST',
															dataType: 'json',
															success: function(data){
																JDapprove = null;
																$('#loadding').fadeOut(200);
																lobibox.destroy();
															},
															beforeSend: function(){
																if(JDapprove !== null){ JDapprove.abort(); }
															}
														});
													}
												}else{
													$("#approve").attr("disabled",false);
													$("#back").attr("disabled",false);
												}
											}
										});
									}
								});
							});
							
							JDandetail = null;
						},
						beforeSend: function(){
							if(JDandetail !== null){ JDandetail.abort(); }
						}
					});
				});						
			}
			
			
			JDbtnt1search = null;
		},
		beforeSend: function(){
			if(JDbtnt1search !== null){
				JDbtnt1search.abort();
			}
		}
	});
});


if(_insert == "T"){
	$("#btnt1createappr").show();
}else{
	$("#btnt1createappr").hide();
}

var JDbtnt1createappr = null;
$("#btnt1createappr").click(function(){
	$('#loadding').fadeIn(500);
	JDbtnt1createappr = $.ajax({
		url:'../SYS04/Analyze/loadform',
		//data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			
			Lobibox.window({
				title: 'รายการขออนุมัติสินเชื่อบุคคลเช่าซื้อ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					fnload($this);
				},
				beforeClose : function(){
					$('#btnt1search').attr('disabled',false);
				}
			});
			
			JDbtnt1createappr = null;
		},
		beforeSend: function(){
			if(JDbtnt1createappr !== null){
				JDbtnt1createappr.abort();
			}
		}
	});
});

function fnload($thisForm){
	$("#locat").select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#locat').find(':selected').val();
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
		dropdownParent: $(".lobibox-body"),
		disabled: (_level == 1 ? false:true),
		//theme: 'classic',
		width: '100%'
	});
	
	$("#locat").change(function(){
		$('#resvno').val(null).trigger('change');
		$('#strno').val(null).trigger('change');
		$('#model').val(null).trigger('change');
		$('#baab').val(null).trigger('change');
		$('#color').val(null).trigger('change');
	});
	
	
	$('#acticod').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getACTI',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#acticod').find(':selected').val();
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
		dropdownParent: $('#acticod').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	//$('#acticod').val().trigger('change');
	
	
	$('#resvno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getRESVNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#resvno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $("#locat").find(":selected").val() === "undefined" ? "" : $("#locat").find(":selected").val());
				
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
		dropdownParent: $('#resvno').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDresvno = null;
	$('#resvno').on("select2:select",function(){
		//$('#resvno').val(null).trigger('change');
		dataToPost = new Object();
		dataToPost.dwnAmt = $('#dwnAmt').val();
		dataToPost.resvno = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
		dataToPost.acticod	= (typeof $("#acticod").find(':selected').val() === "undefined" ? "ALL" : $("#acticod").find(':selected').val());
		
		$('#loadding').fadeIn(500);
		JDresvno = $.ajax({
			url:'../SYS04/Analyze/dataResv',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				
				if(data.error){
					resvnull(); // เคลียร์รายการ
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 10000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$("#resvAmt").val((typeof data.html["RESPAY"] === 'undefined' ? "": data.html["RESPAY"]));
					
					var newOption = new Option(data.html["ACTIDES"], data.html["ACTICOD"], true, true);
					$('#acticod').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["STRNO"], data.html["STRNO"], true, true);
					$('#strno').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["MODEL"], data.html["MODEL"], true, true);
					$('#model').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["BAAB"], data.html["BAAB"], true, true);
					$('#baab').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["COLOR"], data.html["COLOR"], true, true);
					$('#color').empty().append(newOption).trigger('change');
					$("#stat").val((typeof data.html["STAT"] === 'undefined' ? "": data.html["STAT"]));
					$("#sdateold").val((typeof data.html["SDATE"] === 'undefined' ? "": data.html["SDATE"]));
					$("#ydate").val((typeof data.html["YDATE"] === 'undefined' ? "": data.html["YDATE"]));
					
					var newOption = new Option(data.html["CUSNAME"], data.html["CUSCOD"], true, true);
					$('#cuscod').empty().append(newOption).trigger('change');
					$('#cuscod').trigger('select2:select');
					$("#idno").val((typeof data.html["IDNO"] === 'undefined' ? "": data.html["IDNO"]));
					$('#idnoBirth').val(data.html["BIRTHDT"]);
					$('#idnoExpire').val(data.html["EXPDT"]);
					$('#idnoAge').val(data.html["AGE"]);				
					
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#addr1').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#addr2').empty().append(newOption).trigger('change');
					
					$('#phoneNumber').val(data.html["MOBILENO"]);
					$('#income').val(data.html["MREVENU"]);
					$('#price').val(data.html["price"]);
					$('#price').attr('stdid',data.html["stdid"]);
					$('#price').attr('stdplrank',data.html["stdplrank"]);
					$('#interatert').val(data.html["interest_rate"]);
					
					
					if(typeof data.html["RESVNO"] === 'undefined'){
						$('#acticod').attr("disabled",false).trigger('change');
						$('#strno').attr("disabled",false).trigger('change');
						$('#model').attr("disabled",false).trigger('change');
						$('#baab').attr("disabled",false).trigger('change');
						$('#color').attr("disabled",false).trigger('change');
						
						$('#price').attr("disabled",false);				
						$('#price').attr('stdid','');
						$('#price').attr('stdplrank','');						
						$('#interatert').attr("disabled",false);
						$('#cuscod').attr("disabled",false).trigger('change');
						$("#idno").attr("disabled",false);
						$('#idnoBirth').attr("disabled",false);
						$('#idnoExpire').attr("disabled",false);
						$('#idnoAge').attr("disabled",false);
					}else{
						$('#acticod').attr("disabled",true).trigger('change');
						$('#strno').attr("disabled",true).trigger('change');
						$('#model').attr("disabled",true).trigger('change');
						$('#baab').attr("disabled",true).trigger('change');
						$('#color').attr("disabled",true).trigger('change');
						
						$('#price').attr("disabled",true);
						$('#interatert').attr("disabled",true);
						$('#cuscod').attr("disabled",true).trigger('change');
						$("#idno").attr("disabled",true);
						$('#idnoBirth').attr("disabled",true);
						$('#idnoExpire').attr("disabled",true);
						$('#idnoAge').attr("disabled",true);
					}
					
					
				}
				
				JDresvno = null;
			},
			beforeSend: function(){
				if(JDresvno !== null){
					JDresvno.abort();
				}
			}
		});
	});
	
	$('#resvno').on("select2:unselect",function(){ resvnull(); }); // เคลียร์รายการ
	
	function resvnull(){ 
		// เคลียร์รายการ
		$('#resvno').empty().trigger('change');
		$("#resvAmt").val("");
		//$('#acticod').empty().trigger('change');
		$('#strno').empty().trigger('change');
		$('#model').empty().trigger('change');
		$('#baab').empty().trigger('change');
		$('#color').empty().trigger('change');
		$("#stat").val("");
		$("#sdateold").val("");
		$("#ydate").val("");
		$('#price').val("");
		$('#price').attr("stdid","");
		$('#price').attr("stdplrank","");
		$('#interatert').val("");
		
		$('#cuscod').empty().trigger('change');
		$("#idno").val("");
		$('#idnoBirth').val("");
		$('#idnoExpire').val("");
		$('#idnoAge').val("");				
		
		$('#addr1').empty().trigger('change');
		$('#addr2').empty().trigger('change');
		
		$('#phoneNumber').val("");
		$('#income').val("");
		
		$('#acticod').attr("disabled",false).trigger('change');
		$('#strno').attr("disabled",false).trigger('change');
		$('#model').attr("disabled",false).trigger('change');
		$('#baab').attr("disabled",false).trigger('change');
		$('#color').attr("disabled",false).trigger('change');
		$('#price').attr("disabled",false);
		$('#interatert').attr("disabled",false);
		$('#cuscod').attr("disabled",false).trigger('change');
		$("#idno").attr("disabled",false);
		$('#idnoBirth').attr("disabled",false);
		$('#idnoExpire').attr("disabled",false);
		$('#idnoAge').attr("disabled",false);
	}
	
	$('#strno').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getSTRNO',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#strno').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $("#locat").find(":selected").val() === "undefined" ? "" : $("#locat").find(":selected").val());
				
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
		dropdownParent: $('#strno').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDstrno = null;
	//$('#strno').on('select2:select', function (e) {
	$('#strno').on("select2:select",function(){
		dataToPost = new Object();
		dataToPost.dwnAmt 	  = $('#dwnAmt').val();
		dataToPost.createDate = $('#createDate').val();
		dataToPost.strno 	  = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
		dataToPost.acticod	  = (typeof $("#acticod").find(':selected').val() === "undefined" ? "ALL" : $("#acticod").find(':selected').val());
		
		if(dataToPost.strno != ""){
			$('#loadding').fadeIn(0);
			JDstrno = $.ajax({
				url:'../SYS04/Analyze/dataSTR',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if(data.error){
						resvnull(); // เคลียร์รายการ
						Lobibox.notify('warning', {
							title: 'แจ้งเตือน',
							size: 'mini',
							closeOnClick: false,
							delay: 10000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							msg: data.msg
						});
					}else{
						if($("#locat").find(':selected').val() == data.html["CRLOCAT"]){
							var newOption = new Option(data.html["MODEL"], data.html["MODEL"], true, true);
							$('#model').empty().append(newOption).trigger('change');
							var newOption = new Option(data.html["BAAB"], data.html["BAAB"], true, true);
							$('#baab').empty().append(newOption).trigger('change');
							var newOption = new Option(data.html["COLOR"], data.html["COLOR"], true, true);
							$('#color').empty().append(newOption).trigger('change');
							$("#stat").val((typeof data.html["STAT"] === 'undefined' ? "": data.html["STAT"]));
							$("#sdateold").val((typeof data.html["SDATE"] === 'undefined' ? "": data.html["SDATE"]));
							$("#ydate").val((typeof data.html["YDATE"] === 'undefined' ? "": data.html["YDATE"]));
							$('#price').val(data.html["price"]);
							$('#price').attr("stdid",data.html["stdid"]);
							$('#price').attr("stdplrank",data.html["stdplrank"]);
							$('#interatert').val(data.html["interest_rate"]);
							
							if(typeof data.html["STRNO"] === 'undefined'){
								$('#model').attr("disabled",false).trigger('change');
								$('#baab').attr("disabled",false).trigger('change');
								$('#color').attr("disabled",false).trigger('change');					
								$('#price').attr("disabled",false);
								$('#price').attr("stdid","");
								$('#price').attr("stdplrank","");
								$('#interatert').attr("disabled",false);
							}else{
								$('#model').attr("disabled",true).trigger('change');
								$('#baab').attr("disabled",true).trigger('change');
								$('#color').attr("disabled",true).trigger('change');
								if(data.html["STAT"] == "รถใหม่"){
									$('#price').attr("disabled",true);
									$('#interatert').attr("disabled",true);
								}else{
									$('#price').attr("disabled",false);
									$('#interatert').attr("disabled",false);
								}
							}					
						}else{
							/*
							$('#strno').val(null).trigger('change');
							$('#model').val(null).trigger('change');
							$('#baab').val(null).trigger('change');
							$('#color').val(null).trigger('change');
							$('#stat').val('');
							$('#sdateold').val('');
							$('#ydate').val('');
							*/
							$('#resvno').val(null).trigger('change');
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 5000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: "ผิดพลาด รถอยู่ที่สาขา ["+data.html["CRLOCAT"]+"] ไม่สามารถคีย์ขายที่สาขา [" +$("#locat").find(':selected').val()+"] ได้ครับ"
							});
						}
					}
					
					JDstrno = null;
					$('#loadding').fadeOut(0);
				},
				beforeSend: function(){
					if(JDstrno !== null){
						JDstrno.abort();
					}
				}
			});
		}
	});
	
	$('#strno').on("select2:unselect",function(){
		$('#model').empty().trigger('change');
		$('#baab').empty().trigger('change');
		$('#color').empty().trigger('change');
		$("#stat").val("");
		$("#sdateold").val("");
		$("#ydate").val("");
		$('#price').val("");
		$('#price').attr("stdid","");
		$('#price').attr("stdplrank","");
		$('#interatert').val("");
		
		$('#model').attr("disabled",false).trigger('change');
		$('#baab').attr("disabled",false).trigger('change');
		$('#color').attr("disabled",false).trigger('change');
		$('#price').attr("disabled",false);
		$('#interatert').attr("disabled",false);
	});	
	
	$('#model').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#model').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = "HONDA";
				
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
		dropdownParent: $('#model').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#baab').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getBAAB',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#baab').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = "HONDA";
				dataToPost.MODEL = (typeof $("#model").find(":selected").val() === "undefined" ? "" : $("#model").find(":selected").val());
				
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
		dropdownParent: $('#baab').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#color').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCOLOR',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#color').find(':selected').val();
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
		dropdownParent: $('#color').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#cuscod').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#cuscod').find(':selected').val() === 'undefined' ? '' : $('#cuscod').find(':selected').val());
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
		dropdownParent: $('#cuscod').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDcuscod = null;
	//$('#cuscod').change(function(){
	$('#cuscod').on("select2:select",function(){
		dataToPost = new Object();
		dataToPost.cuscod = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
		$('#loadding').fadeIn(0);
		
		JDcuscod = $.ajax({
			url:'../SYS04/Analyze/dataCUS',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(0);
				
				$("#idno").val((typeof data.html["IDNO"] === 'undefined' ? "": data.html["IDNO"]));
				$('#idnoBirth').val(data.html["BIRTHDT"]);
				$('#idnoExpire').val(data.html["EXPDT"]);
				$('#idnoAge').val(data.html["AGE"]);
				
				var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
				$('#addr1').empty().append(newOption).trigger('change');
				var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
				$('#addr2').empty().append(newOption).trigger('change');
				$('#career').val(data.html["OCCUP"]);
				$('#careerOffice').val(data.html["OFFIC"]);
				
				$('#phoneNumber').val(data.html["MOBILENO"]);
				$('#income').val(data.html["MREVENU"]);
				
				if(typeof data.html["CUSCOD"] === 'undefined'){
					$("#idno").attr("disabled",false);
					$('#idnoBirth').attr("disabled",false);
					$('#idnoExpire').attr("disabled",false);
					$('#idnoAge').attr("disabled",false);
				}else{
					$("#idno").attr("disabled",true);
					$('#idnoBirth').attr("disabled",true);
					$('#idnoExpire').attr("disabled",true);
					$('#idnoAge').attr("disabled",true);
				}
				
			
				// กรณีติด F ให้บันทึกข้อมูลมาได้ แต่ฝ่ายวิเคราะห์ จะมีหน้าที่ตรวจสอบอีกทีว่าจะอนุมัติขายหรือไม่
				// if (data.html["GRADE"] == "F" || data.html["GRADE"] == "FF" ){
					// resvnull();
					// Lobibox.notify('error', {
						// title: 'แจ้งเตือน',
						// size: 'mini',
						// closeOnClick: false,
						// delay: false,
						// pauseDelayOnHover: true,
						// continueDelayOnInactiveTab: false,
						// icon: true,
						// messageHeight: '90vh',
						// msg: $("#cuscod").find(':selected').text()+"<br>ผู้เช่าซื้ออยู่ในกลุ่มเสี่ยง ("+data.html["GRADE"]+") ไม่สามารถเลือกได้ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์"
					// });
					
					// $('#cuscod').val(null).trigger('change');
				// }
				
				//เช่าซื้อภายใน 7 วัน
				if(data.html["ARM"] > 0){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 15000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "ลูกค้า "+$("#cuscod").find(':selected').text()+" ได้มีการทำรายการเช่าซื้อภายใน 7 วันที่ผ่านมา"
					});
				}
				
				JDcuscod = null;
			},
			beforeSend: function(){
				if(JDcuscod !== null){
					JDcuscod.abort();
				}
			}
		});
	});
	
	$('#is1_cuscod').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is1_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is1_cuscod').find(':selected').val());
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
		dropdownParent: $('#is1_cuscod').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDis1_cuscod = null;
	$('#is1_cuscod').on('select2:select', function (e) {
		data = new Object();
		data.cuscod 	= (typeof $('#cuscod').find(':selected').val() === "undefined" ? "" : $('#cuscod').find(':selected').val());
		data.is1_cuscod = (typeof $('#is1_cuscod').find(':selected').val() === "undefined" ? "" : $('#is1_cuscod').find(':selected').val());
		
		if(data.cuscod == data.is1_cuscod){
			$msg = "ผิดพลาด ผู้เช่าซื้อ จะมาเป็นผู้ค้ำประกันไม่ได้จ้า";
			$obj = data;
			clearIS1_CUSCOD($msg,$obj);	
		}else{
			dataToPost = new Object();
			dataToPost.cuscod = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
			$('#loadding').fadeIn(0);
			
			JDis1_cuscod = $.ajax({
				url:'../SYS04/Analyze/dataCUS',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').fadeOut(0);
					
					$("#is1_idno").val((typeof data.html["IDNO"] === 'undefined' ? "": data.html["IDNO"]));
					$('#is1_idnoBirth').val(data.html["BIRTHDT"]);
					$('#is1_idnoExpire').val(data.html["EXPDT"]);
					$('#is1_idnoAge').val(data.html["AGE"]);
					
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#is1_addr1').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#is1_addr2').empty().append(newOption).trigger('change');
					$('#is1_career').val(data.html["OCCUP"]);
					$('#is1_careerOffice').val(data.html["OFFIC"]);
					
					$('#is1_phoneNumber').val(data.html["MOBILENO"]);
					$('#is1_income').val(data.html["MREVENU"]);
					
					if(typeof data.html["CUSCOD"] === 'undefined'){
						$("#is1_idno").attr("disabled",false);
						$('#is1_idnoBirth').attr("disabled",false);
						$('#is1_idnoExpire').attr("disabled",false);
						$('#is1_idnoAge').attr("disabled",false);
					}else{
						$("#is1_idno").attr("disabled",true);
						$('#is1_idnoBirth').attr("disabled",true);
						$('#is1_idnoExpire').attr("disabled",true);
						$('#is1_idnoAge').attr("disabled",true);
					}
					
					//กรณีติด F ให้บันทึกข้อมูลมาได้ แต่ฝ่ายวิเคราะห์ จะมีหน้าที่ตรวจสอบอีกทีว่าจะอนุมัติขายหรือไม่
					// if (data.html["GRADE"] == "F" || data.html["GRADE"] == "FF" ){
						// $msg = $("#is1_cuscod").find(':selected').text()+"<br>ผู้ค้ำประกัน 1 อยู่ในกลุ่มเสี่ยง ("+data.html["GRADE"]+") ไม่สามารถเลือกได้ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์";
						// clearIS1_CUSCOD($msg,$obj);	
						// /*
						// Lobibox.notify('error', {
							// title: 'แจ้งเตือน',
							// size: 'mini',
							// closeOnClick: false,
							// delay: false,
							// pauseDelayOnHover: true,
							// continueDelayOnInactiveTab: false,
							// icon: true,
							// messageHeight: '90vh',
							// msg: $("#is1_cuscod").find(':selected').text()+"<br>ผู้ค้ำประกัน 1 อยู่ในกลุ่มเสี่ยง ("+data.html["GRADE"]+") ไม่สามารถเลือกได้ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์"
						// });
						
						// $('#is1_cuscod').val(null).trigger('change');
						// */
					// }
					
					JDis1_cuscod = null;
				},
				beforeSend: function(){
					if(JDis1_cuscod !== null){
						JDis1_cuscod.abort();
					}
				}
			});
		}
	});
	
	$('#is2_cuscod').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is2_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is2_cuscod').find(':selected').val());
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
		dropdownParent: $('#is2_cuscod').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	var JDis2_cuscod = null;
	$('#is2_cuscod').on('select2:select', function (e) {
		data = new Object();
		data.cuscod 	= (typeof $('#cuscod').find(':selected').val() === "undefined" ? "" : $('#cuscod').find(':selected').val());
		data.is1_cuscod = (typeof $('#is1_cuscod').find(':selected').val() === "undefined" ? "" : $('#is1_cuscod').find(':selected').val());
		data.is2_cuscod = (typeof $('#is2_cuscod').find(':selected').val() === "undefined" ? "" : $('#is2_cuscod').find(':selected').val());
		
		//ตรวจสอบว่าเลือกคนค้ำคนแรกยัง ถ้ายังให้ไปเลือกคนแรกก่อน
		if(data.is1_cuscod == ''){
			$msg = "ผิดพลาด คุณยังไม่ระบุผู้ค้ำประกัน 1 <br>ไม่สามารถเลือกผู้ค้ำประกัน 2 ได้จ้า";
			$obj = data;
			clearIS2_CUSCOD($msg,$obj);	
		}else if(data.cuscod == data.is2_cuscod){
			$msg = "ผิดพลาด ผู้เช่าซื้อ จะมาเป็นผู้ค้ำประกันไม่ได้จ้า";
			$obj = data;
			clearIS2_CUSCOD($msg,$obj);	
		}else if(data.is1_cuscod == data.is2_cuscod){
			$msg = "ผิดพลาด คุณระบุผู้ค้ำประกันคนนี้แล้ว ไม่สามารถระบุซ้ำได้จ้า";
			$obj = data;
			clearIS2_CUSCOD($msg,$obj);
		}else{
			dataToPost = new Object();
			dataToPost.cuscod = (typeof $(this).find(':selected').val() === "undefined" ? "" : $(this).find(':selected').val());
			$('#loadding').fadeIn(0);
			
			JDis2_cuscod = $.ajax({
				url:'../SYS04/Analyze/dataCUS',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#loadding').fadeOut(0);
					
					$("#is2_idno").val((typeof data.html["IDNO"] === 'undefined' ? "": data.html["IDNO"]));
					$('#is2_idnoBirth').val(data.html["BIRTHDT"]);
					$('#is2_idnoExpire').val(data.html["EXPDT"]);
					$('#is2_idnoAge').val(data.html["AGE"]);
					
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#is2_addr1').empty().append(newOption).trigger('change');
					var newOption = new Option(data.html["ADDR"], data.html["ADDRNO"], true, true);
					$('#is2_addr2').empty().append(newOption).trigger('change');
					$('#is2_career').val(data.html["OCCUP"]);
					$('#is2_careerOffice').val(data.html["OFFIC"]);
					
					$('#is2_phoneNumber').val(data.html["MOBILENO"]);
					$('#is2_income').val(data.html["MREVENU"]);
					
					if(typeof data.html["CUSCOD"] === 'undefined'){
						$("#is2_idno").attr("disabled",false);
						$('#is2_idnoBirth').attr("disabled",false);
						$('#is2_idnoExpire').attr("disabled",false);
						$('#is2_idnoAge').attr("disabled",false);
					}else{
						$("#is2_idno").attr("disabled",true);
						$('#is2_idnoBirth').attr("disabled",true);
						$('#is2_idnoExpire').attr("disabled",true);
						$('#is2_idnoAge').attr("disabled",true);
					}
					
					//กรณีติด F ให้บันทึกข้อมูลมาได้ แต่ฝ่ายวิเคราะห์ จะมีหน้าที่ตรวจสอบอีกทีว่าจะอนุมัติขายหรือไม่
					// if (data.html["GRADE"] == "F" || data.html["GRADE"] == "FF" ){
						
						// $msg = $("#is2_cuscod").find(':selected').text()+"<br>ผู้ค้ำประกัน 2 อยู่ในกลุ่มเสี่ยง ("+data.html["GRADE"]+") ไม่สามารถเลือกได้ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์";
						// clearIS2_CUSCOD($msg,$obj);	
						
						// /*
						// Lobibox.notify('error', {
							// title: 'แจ้งเตือน',
							// size: 'mini',
							// closeOnClick: false,
							// delay: false,
							// pauseDelayOnHover: true,
							// continueDelayOnInactiveTab: false,
							// icon: true,
							// messageHeight: '90vh',
							// msg: $("#is2_cuscod").find(':selected').text()+"<br>ผู้ค้ำประกัน 2 อยู่ในกลุ่มเสี่ยง ("+data.html["GRADE"]+") ไม่สามารถเลือกได้ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์"
						// });
						
						// $('#is2_cuscod').val(null).trigger('change');
						// */
					// }
					
					JDis2_cuscod = null;
				},
				beforeSend: function(){
					if(JDis2_cuscod !== null){
						JDis2_cuscod.abort();
					}
				}
			});
		}
	});
	
	$('#cuscod').on('select2:unselect', function (e) {
		$("#idno").val("");
		$('#idnoBirth').val("");
		$('#idnoExpire').val("");
		$('#idnoAge').val("");
		$('#addr1').val(null).trigger('change');
		$('#addr2').val(null).trigger('change');
		$('#idnoStat').val(null).trigger('change');
		$('#phoneNumber').val("");
		$('#baby').val("");		
		$('#income').val("");
		
		$("#idno").attr("disabled",false);
		$('#idnoBirth').attr("disabled",false);
		$('#idnoExpire').attr("disabled",false);
		$('#idnoAge').attr("disabled",false);
	});
	
	$('#is1_cuscod').on('select2:unselect', function (e) {
		$("#is1_idno").val("");
		$('#is1_idnoBirth').val("");
		$('#is1_idnoExpire').val("");
		$('#is1_idnoAge').val("");
		$('#is1_addr1').val(null).trigger('change');
		$('#is1_addr2').val(null).trigger('change');
		$('#is1_idnoStat').val(null).trigger('change');
		$('#is1_phoneNumber').val("");
		$('#is1_baby').val("");		
		$('#is1_income').val("");
		
		$("#is1_idno").attr("disabled",false);
		$('#is1_idnoBirth').attr("disabled",false);
		$('#is1_idnoExpire').attr("disabled",false);
		$('#is1_idnoAge').attr("disabled",false);
		
		$("#is2_idno").val("");
		$('#is2_idnoBirth').val("");
		$('#is2_idnoExpire').val("");
		$('#is2_idnoAge').val("");
		$('#is2_addr1').val(null).trigger('change');
		$('#is2_addr2').val(null).trigger('change');
		$('#is2_idnoStat').val(null).trigger('change');
		$('#is2_phoneNumber').val("");
		$('#is2_baby').val("");		
		$('#is2_income').val("");
		
		$("#is2_idno").attr("disabled",false);
		$('#is2_idnoBirth').attr("disabled",false);
		$('#is2_idnoExpire').attr("disabled",false);
		$('#is2_idnoAge').attr("disabled",false);
		
		$("#is2_cuscod").val(null).trigger("change");
	});
	
	$('#is2_cuscod').on('select2:unselect', function (e) {
		$("#is2_idno").val("");
		$('#is2_idnoBirth').val("");
		$('#is2_idnoExpire').val("");
		$('#is2_idnoAge').val("");
		$('#is2_addr1').val(null).trigger('change');
		$('#is2_addr2').val(null).trigger('change');
		$('#is2_idnoStat').val(null).trigger('change');
		$('#is2_phoneNumber').val("");
		$('#is2_baby').val("");		
		$('#is2_income').val("");
		
		$("#is2_idno").attr("disabled",false);
		$('#is2_idnoBirth').attr("disabled",false);
		$('#is2_idnoExpire').attr("disabled",false);
		$('#is2_idnoAge').attr("disabled",false);
	});
	
	function clearIS1_CUSCOD($msg,$obj){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: $msg
		});
		
		$("#is1_idno").val("");
		$('#is1_idnoBirth').val("");
		$('#is1_idnoExpire').val("");
		$('#is1_idnoAge').val("");
		$('#is1_addr1').val(null).trigger('change');
		$('#is1_addr2').val(null).trigger('change');
		$('#is1_phoneNumber').val("");
		$('#is1_income').val("");
		
		$("#is1_idno").attr("disabled",false);
		$('#is1_idnoBirth').attr("disabled",false);
		$('#is1_idnoExpire').attr("disabled",false);
		$('#is1_idnoAge').attr("disabled",false);
		
		$("#is2_idno").val("");
		$('#is2_idnoBirth').val("");
		$('#is2_idnoExpire').val("");
		$('#is2_idnoAge').val("");
		$('#is2_addr1').val(null).trigger('change');
		$('#is2_addr2').val(null).trigger('change');
		$('#is2_phoneNumber').val("");
		$('#is2_income').val("");
		
		$("#is2_idno").attr("disabled",false);
		$('#is2_idnoBirth').attr("disabled",false);
		$('#is2_idnoExpire').attr("disabled",false);
		$('#is2_idnoAge').attr("disabled",false);
		//เปลี่ยนเป็นค่าว่าง		
		if($obj.is1_cuscod != ''){ $('#is1_cuscod').val(null).trigger('change'); }
	}
	
	function clearIS2_CUSCOD($msg,$obj){
		Lobibox.notify('warning', {
			title: 'แจ้งเตือน',
			size: 'mini',
			closeOnClick: false,
			delay: 5000,
			pauseDelayOnHover: true,
			continueDelayOnInactiveTab: false,
			icon: true,
			messageHeight: '90vh',
			msg: $msg
		});
		
		$("#is2_idno").val("");
		$('#is2_idnoBirth').val("");
		$('#is2_idnoExpire').val("");
		$('#is2_idnoAge').val("");
		$('#is2_addr1').val(null).trigger('change');
		$('#is2_addr2').val(null).trigger('change');
		$('#is2_phoneNumber').val("");
		$('#is2_income').val("");
		
		$("#is2_idno").attr("disabled",false);
		$('#is2_idnoBirth').attr("disabled",false);
		$('#is2_idnoExpire').attr("disabled",false);
		$('#is2_idnoAge').attr("disabled",false);
		//เปลี่ยนเป็นค่าว่าง		
		if($obj.is2_cuscod != ''){ $('#is2_cuscod').val(null).trigger('change'); }
	}
	
	$('#idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
	$('#idnoStat').val(null).trigger('change');
	$('#is1_idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#is1_idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
	$('#is1_idnoStat').val(null).trigger('change');
	$('#is2_idnoStat').select2({ placeholder: 'เลือก',dropdownParent: $('#is2_idnoStat').parent().parent(),minimumResultsForSearch: -1,width: '100%' });
	$('#is2_idnoStat').val(null).trigger('change');
	
	$('#addr1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#addr1').find(':selected').val() === 'undefined' ? '' : $('#addr1').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#cuscod').find(':selected').val() === 'undefined' ? '' : $('#cuscod').find(':selected').val());
				
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
		dropdownParent: $('#addr1').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is1_addr1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is1_addr1').find(':selected').val() === 'undefined' ? '' : $('#is1_addr1').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#is1_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is1_cuscod').find(':selected').val());
				
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
		dropdownParent: $('#is1_addr1').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is2_addr1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is2_addr1').find(':selected').val() === 'undefined' ? '' : $('#is2_addr1').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#is2_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is2_cuscod').find(':selected').val());
				
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
		dropdownParent: $('#is2_addr1').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#addr2').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#addr2').find(':selected').val() === 'undefined' ? '' : $('#addr2').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#cuscod').find(':selected').val() === 'undefined' ? '' : $('#cuscod').find(':selected').val());
				
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
		dropdownParent: $('#addr2').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is1_addr2').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is1_addr2').find(':selected').val() === 'undefined' ? '' : $('#is1_addr2').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#is1_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is1_cuscod').find(':selected').val());
				
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
		dropdownParent: $('#is1_addr2').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is2_addr2').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERSADDRNo',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is2_addr2').find(':selected').val() === 'undefined' ? '' : $('#is2_addr2').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.cuscod = (typeof $('#is2_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is2_cuscod').find(':selected').val());
				
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
		dropdownParent: $('#is2_addr2').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#empRelation').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#empRelation').find(':selected').val() === 'undefined' ? '' : $('#empRelation').find(':selected').val());
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
		dropdownParent: $('#empRelation').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is1_empRelation').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is1_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is1_empRelation').find(':selected').val());
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
		dropdownParent: $('#is1_empRelation').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#is2_empRelation').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#is2_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is2_empRelation').find(':selected').val());
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
		dropdownParent: $('#is2_empRelation').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#empIDNo').select2({
		placeholder: 'เลือก',
		tags: true,
		//tokenSeparators: [","],
		createTag: function (params) {
			var term = $.trim(params.term);
			if (term === '') { return null; }
			return {id: term,text: term + ' (พนักงานใหม่)'};
		},
		ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#empIDNo').find(':selected').val();
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
		dropdownParent: $('#empIDNo').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#mngIDNo').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getVUSER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#mngIDNo').find(':selected').val();
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
		tags: true,
		createTag: function (params) {
			var term = $.trim(params.term);
			if (term === '') { return null; }
			return {id: term,text: term + ' (พนักงานใหม่)'};
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $('#mngIDNo').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$(".toggleData").click(function(){
		var thisc = $(this).attr('thisc');
		
		if($(this).hasClass('glyphicon-minus')){
			$(this).removeClass('glyphicon-minus');
			$(this).addClass('glyphicon-plus');
		}else{
			$(this).addClass('glyphicon-minus');
			$(this).removeClass('glyphicon-plus');			
		}
		
		if($("."+thisc).attr('isshow')==1){
			$("."+thisc).fadeOut(300);	
			$("."+thisc).attr('isshow',0);
		}else{
			$("."+thisc).fadeIn(1000);	
			$("."+thisc).attr('isshow',1);
		}
	});
	
	var JDsave = null;
	$("#save5555").click(function(){
		var test = $('#empIDNo').find(':selected').val();
		alert(test);
		//alert(data[0].test);
	});
	$("#save").click(function(){
		dataToPost = new Object();
		dataToPost.locat 		= (typeof $('#locat').find(':selected').val() === 'undefined' ? '' : $('#locat').find(':selected').val());
		dataToPost.resvno 		= (typeof $('#resvno').find(':selected').val() === 'undefined' ? '' : $('#resvno').find(':selected').val());
		dataToPost.resvAmt 		= $('#resvAmt').val();
		dataToPost.dwnAmt 		= $('#dwnAmt').val();
		dataToPost.insuranceAmt = $('#insuranceAmt').val();
		dataToPost.nopay		= $('#nopay').val();
		dataToPost.strno 		= (typeof $('#strno').find(':selected').val() === 'undefined' ? '' : $('#strno').find(':selected').val());
		dataToPost.model 		= (typeof $('#model').find(':selected').val() === 'undefined' ? '' : $('#model').find(':selected').val());
		dataToPost.baab 		= (typeof $('#baab').find(':selected').val() === 'undefined' ? '' : $('#baab').find(':selected').val());
		dataToPost.color 		= (typeof $('#color').find(':selected').val() === 'undefined' ? '' : $('#color').find(':selected').val());
		dataToPost.stat			= $('#stat').val();
		dataToPost.sdateold		= $('#sdateold').val();
		dataToPost.ydate		= $('#ydate').val();
		dataToPost.price		= $('#price').val();
		dataToPost.stdid		= $('#price').attr('stdid');
		dataToPost.stdplrank	= $('#price').attr('stdplrank');
		dataToPost.interatert	= $('#interatert').val();
		
		dataToPost.cuscod 		= (typeof $('#cuscod').find(':selected').val() === 'undefined' ? '' : $('#cuscod').find(':selected').val());
		dataToPost.idno			= $('#idno').val();
		dataToPost.idnoBirth	= $('#idnoBirth').val();
		dataToPost.idnoExpire	= $('#idnoExpire').val();
		dataToPost.idnoAge		= $('#idnoAge').val();
		dataToPost.idnoStat 	= (typeof $('#idnoStat').find(':selected').val() === 'undefined' ? '' : $('#idnoStat').find(':selected').val());
		dataToPost.addr1 		= (typeof $('#addr1').find(':selected').val() === 'undefined' ? '' : $('#addr1').find(':selected').val());
		dataToPost.addr2 		= (typeof $('#addr2').find(':selected').val() === 'undefined' ? '' : $('#addr2').find(':selected').val());
		dataToPost.phoneNumber	= $('#phoneNumber').val();
		dataToPost.socialSecurity	= $('#socialSecurity').val();
		dataToPost.baby			= $('#baby').val();
		dataToPost.career		= $('#career').val();
		dataToPost.careerOffice	= $('#careerOffice').val();
		dataToPost.careerPhone	= $('#careerPhone').val();
		dataToPost.income		= $('#income').val();
		dataToPost.hostName		= $('#hostName').val();
		dataToPost.hostIDNo		= $('#hostIDNo').val();
		dataToPost.hostPhone	= $('#hostPhone').val();
		dataToPost.hostRelation	= $('#hostRelation').val();
		dataToPost.empRelation	= (typeof $('#empRelation').find(':selected').val() === 'undefined' ? '' : $('#empRelation').find(':selected').val());
		dataToPost.reference	= $('#reference').val();
		
		dataToPost.is1_cuscod 		= (typeof $('#is1_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is1_cuscod').find(':selected').val());
		dataToPost.is1_idno			= $('#is1_idno').val();
		dataToPost.is1_idnoBirth	= $('#is1_idnoBirth').val();
		dataToPost.is1_idnoExpire	= $('#is1_idnoExpire').val();
		dataToPost.is1_idnoAge		= $('#is1_idnoAge').val();
		dataToPost.is1_idnoStat 	= (typeof $('#is1_idnoStat').find(':selected').val() === 'undefined' ? '' : $('#is1_idnoStat').find(':selected').val());
		dataToPost.is1_addr1 		= (typeof $('#is1_addr1').find(':selected').val() === 'undefined' ? '' : $('#is1_addr1').find(':selected').val());
		dataToPost.is1_addr2 		= (typeof $('#is1_addr2').find(':selected').val() === 'undefined' ? '' : $('#is1_addr2').find(':selected').val());
		dataToPost.is1_phoneNumber	= $('#is1_phoneNumber').val();
		dataToPost.is1_socialSecurity	= $('#is1_socialSecurity').val();
		dataToPost.is1_baby			= $('#is1_baby').val();
		dataToPost.is1_career		= $('#is1_career').val();
		dataToPost.is1_careerOffice	= $('#is1_careerOffice').val();
		dataToPost.is1_careerPhone	= $('#is1_careerPhone').val();
		dataToPost.is1_income		= $('#is1_income').val();
		dataToPost.is1_hostName		= $('#is1_hostName').val();
		dataToPost.is1_hostIDNo		= $('#is1_hostIDNo').val();
		dataToPost.is1_hostPhone	= $('#is1_hostPhone').val();
		dataToPost.is1_hostRelation	= $('#is1_hostRelation').val();
		dataToPost.is1_empRelation	= (typeof $('#is1_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is1_empRelation').find(':selected').val());
		dataToPost.is1_reference	= $('#is1_reference').val();
		
		dataToPost.is2_cuscod 		= (typeof $('#is2_cuscod').find(':selected').val() === 'undefined' ? '' : $('#is2_cuscod').find(':selected').val());
		dataToPost.is2_idno			= $('#is2_idno').val();
		dataToPost.is2_idnoBirth	= $('#is2_idnoBirth').val();
		dataToPost.is2_idnoExpire	= $('#is2_idnoExpire').val();
		dataToPost.is2_idnoAge		= $('#is2_idnoAge').val();
		dataToPost.is2_idnoStat 	= (typeof $('#is2_idnoStat').find(':selected').val() === 'undefined' ? '' : $('#is2_idnoStat').find(':selected').val());
		dataToPost.is2_addr1 		= (typeof $('#is2_addr1').find(':selected').val() === 'undefined' ? '' : $('#is2_addr1').find(':selected').val());
		dataToPost.is2_addr2 		= (typeof $('#is2_addr2').find(':selected').val() === 'undefined' ? '' : $('#is2_addr2').find(':selected').val());
		dataToPost.is2_phoneNumber	= $('#is2_phoneNumber').val();
		dataToPost.is2_baby			= $('#is2_baby').val();
		dataToPost.is2_socialSecurity	= $('#is2_socialSecurity').val();
		dataToPost.is2_career		= $('#is2_career').val();
		dataToPost.is2_careerOffice	= $('#is2_careerOffice').val();
		dataToPost.is2_careerPhone	= $('#is2_careerPhone').val();
		dataToPost.is2_income		= $('#is2_income').val();
		dataToPost.is2_hostName		= $('#is2_hostName').val();
		dataToPost.is2_hostIDNo		= $('#is2_hostIDNo').val();
		dataToPost.is2_hostPhone	= $('#is2_hostPhone').val();
		dataToPost.is2_hostRelation	= $('#is2_hostRelation').val();
		dataToPost.is2_empRelation	= (typeof $('#is2_empRelation').find(':selected').val() === 'undefined' ? '' : $('#is2_empRelation').find(':selected').val());
		dataToPost.is2_reference	= $('#is2_reference').val();
		
		dataToPost.empIDNo	= (typeof $('#empIDNo').find(':selected').val() === 'undefined' ? '' : $('#empIDNo').find(':selected').val());
		dataToPost.empTel	= $('#empTel').val();
		dataToPost.mngIDNo	= (typeof $('#mngIDNo').find(':selected').val() === 'undefined' ? '' : $('#mngIDNo').find(':selected').val());
		dataToPost.mngTel	= $('#mngTel').val();
		
		
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: 'คุณต้องการบันทึกการใบวิเคราะห์หรือไม่ ? <br><span style="color:red;font-size:16pt">*** กรณีที่บันทึกแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก ยืนยันการทำรายการ</span>',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-remove',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				if (type === 'ok'){
					$('#loadding').fadeIn(500);
					
					JDsave = $.ajax({
						url:'../SYS04/Analyze/save',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(200);
							
							if(data.error){
								var msg = data.msg.length;
								msgDesplay = "";
								for(var i=0;i<msg;i++){
									if(i>0) msgDesplay += "<br>";
									msgDesplay += (i+1)+". "+data.msg[i];
								}
								
								if(msgDesplay != ""){
									msgDesplay += "<br><br><span style='background-color:white;color:red;font-size:16pt;'>ไม่สามารถบันทึกได้ครับ</span>";
								}
								
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: msgDesplay
								});
							}else{
								Lobibox.notify('success', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: false,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg[0]
								});
								
								$thisForm.destroy();
							}
							
							JDsave = null;
						},
						beforeSend: function(){
							if(JDsave !== null){
								JDsave.abort();
							}
						}
					});
				}else{
					Lobibox.notify('info', {
						title: '',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: "คุณยังไม่ได้บันทึกข้อมูล"
					});
				}
			}
		});
	});
}

















