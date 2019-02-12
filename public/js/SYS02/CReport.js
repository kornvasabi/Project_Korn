/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

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

function search(){
	dataToPost = new Object();
	dataToPost.TRANSNO   = $('#TRANSNO').val();
	dataToPost.TRANSDTs  = $('#TRANSDTs').val();
	dataToPost.TRANSDTe  = $('#TRANSDTe').val();
	dataToPost.TRANSFM   = $('#TRANSFM').val();
	dataToPost.TRANSSTAT = $('#TRANSSTAT').val();
	dataToPost.TRANSSYS	 = $('#TRANSSYS').val();
	
	$('#loadding').show();

	$.ajax({
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
			
			document.getElementById("table-fixed-TransfersSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
		}
	});
}