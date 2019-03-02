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
				closeOnEsc: true,
				draggable: false
			});
			
			document.getElementById("table-fixed-TransfersSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
		}
	});
}