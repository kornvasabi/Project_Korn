 
 $(function(){
	$('#locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#locat').find(':selected').val() === 'undefined' ? '':$('#locat').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	

	$('#STAT').select2();
	
	$('#TYPE').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getTYPES',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#TYPE').find(':selected').val() === 'undefined' ? '':$('#TYPE').find(':selected').val());
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#MODEL').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.TYPECOD = 'HONDA';
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});

var jdbtnt1search=null;
$('#btnt1search').click(function(){ fnSearch(); });

function fnSearch(){
	dataToPost = new Object();
	dataToPost.LOCAT 	= (typeof $('#locat').find(':selected').val() === 'undefined' ? '':$('#locat').find(':selected').val());
	dataToPost.SDATE	= $('#SDATE').val();
	dataToPost.EDATE	= $('#EDATE').val();
	dataToPost.TYPE 	= (typeof $('#TYPE').find(':selected').val() === 'undefined' ? '':$('#TYPE').find(':selected').val());
	dataToPost.MODEL 	= (typeof $('#MODEL').find(':selected').val() === 'undefined' ? '':$('#MODEL').find(':selected').val());
	dataToPost.STAT 	= (typeof $('#STAT').find(':selected').val() === 'undefined' ? '':$('#STAT').find(':selected').val());
	dataToPost.REPORT 	= $('input:radio[name=REPORT]:checked').val();
	dataToPost.turnover = $('input:radio[name=turnover]:checked').val();
		
	$('#loadding').fadeIn(200);
	jdbtnt1search = $.ajax({
		url:'../SYS07/report/stockcardSearch',
		data: dataToPost,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'รายงาน',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					fn_datatables('table-RPstc',11,280,'NO');
					$('.data-export').prepend('<img id="table-RPstc-print" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;margin-left:10px;">');
					
					var JDRPstcPrint=null;
					$('#table-RPstc-print').click(function(){
						JDRPstcPrint = $.ajax({
							url:'../SYS07/report/stockcardFormPrint',
							data: dataToPost,
							type:'POST',
							dataType:'json',
							success: function(data){
								Lobibox.window({
									title: 'File PDF',
									width: $(window).width(),
									height: $(window).height(),
									content: data.html,
									draggable: false,
									closeOnEsc: true,
									shown: function($this){
										document.getElementById("formsubmit").submit();
									}
								});
								
								JDRPstcPrint = null;
							},
							beforeSend: function(){ if(JDRPstcPrint !== null){ JDRPstcPrint.abort(); } }
						});
					});
				},
				beforeClose : function(){
					$('#btnt1leasing').attr('disabled',false);
				}
			});			
			
			$('#loadding').fadeOut(200);
			jdbtnt1search = null;
		},
		beforeSend: function(){ if(jdbtnt1search !== null){ jdbtnt1search.abort(); } }
	});
}
















