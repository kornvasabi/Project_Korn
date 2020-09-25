/********************************************************
             ______@19/08/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#BILLCOLL').select2({
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
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCATNM',
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
		allowClear: false,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#removeadd').click(function(){
		$('#add_locat').val("");
	});
	/*
	$('#btnaddlocat').click(function(){
		var html = " \
			<div class='row'> \
				<div class='col-sm-4'> \
					<div class='form-group'> \
						รหัสสาขา \
						<input type='text' id='locat' class='form-control'> \
					</div> \
				</div>	\
				<div class='col-sm-4'> \
					<div class='form-group'> \
						ชื่อสาขา \
						<input type='text' id='locatnm' class='form-control'> \
					</div> \
				</div> \
				<div class='col-sm-4'> \
					<div class='form-group'> \
						รหัสย่อ \
						<input type='text' id='shortl' class='form-control'> \
					</div> \
				</div> \
				<div class='col-sm-12'> \
					<button id='btnsearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button> \
				</div> \
				<br> \
				<div id='locat_result' class='col-sm-12'></div> \
			</div> \
		";
		Lobibox.window({
			title: 'FORM SEARCH',
			//width: $(window).width(),
			//height: $(window).height(),
			content:html,
			draggable: false,
			closeOnEsc: true,
			shown: function($this){
				var kb_btnsearch = null;
				$('#btnsearch').click(function(){ fnResultLOCAT(); });
				
				function fnResultLOCAT(){
					dataToPost = new Object();
					dataToPost.locat   = $('#locat').val();
					dataToPost.locatnm = $('#locatnm').val();
					dataToPost.shortl  = $('#shortl').val();
					$('#loadding').fadeIn(200);
					
					kb_btnsearch = $.ajax({
						url:'../Cselect2K/getResultLOCAT',
						data:dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(200);
							$('#locat_result').html(data.html);
							
							$('.getit').hover(function(){
								$(this).css({'background-color':'#a9a9f9'});
								$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
							},function(){
								$(this).css({'background-color':''});
								$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
							});
							$('.getit').unbind('click');
							$('.getit').click(function(){
								att = new Object();
								att.locat    = $(this).attr('LOCAT');
								att.locatnam = $(this).attr('LOCATNAM');
								
								$('#add_locat').attr('LOCAT',att.locat);
								$('#add_locat').val(att.locat);
								$this.destroy();
							});
							kb_btnsearch = null;
						},
						beforeSend: function(){
							if(kb_btnsearch !== null){ kb_btnsearch.abort(); }
						}
					});
				}
			},
			beforeClose : function(){
				
			}
		});	
	});	
	*/
});

$('#btnreport').click(function(){
	printReport();
});
var DBT_Report = null;
function printReport(){
	dataToPost = new Object();
	dataToPost.LOCAT     = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').val());
	dataToPost.BILLCOLL  = (typeof $('#BILLCOLL').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL').val());
	dataToPost.ATDATE    = $('#ATDATE').val();
	DBT_Report = $.ajax({
		url: '../SYS09/ReportDebtAtDate/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS09/ReportDebtAtDate/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
			DBT_Report = null;
		},
		beforeSend: function(){
			if(DBT_Report !== null){
				DBT_Report.abort();
			}
		}
	});
}