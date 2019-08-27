/********************************************************
             ______@--/02/2019______
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

$('#btnt1transfer').click(function(){
	/*
	var content = "hi";
	Lobibox.window({
		title: 'Window title',
		content: content,
		height: $(window).height(),
		width: $(window).width()
	});
	*/
	search();
});

var JASOBJsearch = null;
function search(){
	dataToPost = new Object();
	dataToPost.TRANSNO   = $('#TRANSNO').val();
	dataToPost.TRANSDTs  = $('#TRANSDTs').val();
	dataToPost.TRANSDTe  = $('#TRANSDTe').val();
	dataToPost.TRANSFM   = $('#TRANSFM').val();
	dataToPost.TRANSTO   = $('#TRANSTO').val();
	dataToPost.TRANSSTAT = $('#TRANSSTAT').val();
	dataToPost.TRANSSTAT2 = $('#TRANSSTAT2').val();
	dataToPost.TRANSSYS	 = $('#TRANSSYS').val();
	dataToPost.STRNO	 = $('#STRNO').val();
	dataToPost.CT	 	 = $('#CT').val();
	dataToPost.TR	 	 = $('#TR').val();	
	$('#loadding').show();

	JASOBJsearch = $.ajax({
		url: '../SYS02/CReport/TransfersSearch',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){
			$('#loadding').hide();			
			
			Lobibox.window({
				title: 'รายงานการโอนย้ายรถ',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			/*
			document.getElementById("table-fixed-TransfersSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
			*/
			//$('#table-Ctransferscars').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-TransfersSearch',1,320);
			
			// Export data to Excel
			$('.data-export').prepend('<img id="table-TransfersSearch-excel" src="../public/images/excel.png" style="width:30px;height:30px;cursor:pointer;">');
			$("#table-TransfersSearch-excel").click(function(){ 	
				tableToExcel_Export(data.html,"รายงานการโอนย้ายรถ","Report_transfers.xlsx"); 
			});
			
			
			JASOBJsearch = null;
		},
		beforeSend: function(){
			if(JASOBJsearch !== null){
				JASOBJsearch.abort();
			}
		}
	});
}