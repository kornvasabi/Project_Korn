/********************************************************
             ______@06/11/2019______
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

var jd_btnt1RpGroup = null;
$('#btnt1RpGroup').click(function(){
	dataToPost = new Object();
	dataToPost.STRNO = $('#STRNO').val();
	dataToPost.SDATE = $('#SDATE').val();
	dataToPost.TDATE = $('#TDATE').val();
	dataToPost.USERS = $('#USERS').val();
	
	$('#loadding').fadeIn(200);
	jd_btnt1RpGroup = $.ajax({
		url: '../SYS02/CReportGroup/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#result').html(data.html);
			
			//$('#table-reportgroup').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-reportgroup',1,390);
			
			// Export data to Excel
			$('.data-export').prepend('<img id="table-reportgroup-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-reportgroup-excel").click(function(){ 	
				tableToExcel_Export(data.html,"รายงานการเปลี่ยนกลุ่มรถ","reportgroup"); 
			});
			
			jd_btnt1RpGroup = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){
			if(jd_btnt1RpGroup !== null){ jd_btnt1RpGroup.abort(); }
		}
	});
});














