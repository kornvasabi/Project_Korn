 $('#btnt1transfer').click(function(){
	dataToPost = new Object();
	dataToPost.locat = $('#add_TRANSFM').val();
	
	$.ajax({
		url: '../SYS02/CReport/TransfersSearch',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){
			$('#resultt1transfer').html(data.html);
						
			document.getElementById("table-fixed-TransfersSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
		}
	});
 });