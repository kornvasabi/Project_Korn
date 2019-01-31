$(function(){
	$.ajax({
		url:'../setup/CStock/maxstock_search',
		data: '',
		typr: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setmaxstockResult').html(data.html);
			
			document.getElementById("table-fixed-maxstockSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			
			$('.getit').hover(function(){
				$(this).css({'background-color':'yellow'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
			},function(){
				$(this).css({'background-color':'white'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
			});
			
			$('.mst-edit').click(function(){
				dataToPost = new Object();
				dataToPost.LOCAT = $(this).attr('LOCAT');
				
				$.ajax({
					url:'../setup/CStock/maxstock_form_edit',
					data: dataToPost,
					typr: 'POST',
					dataType: 'json',
					success: function(edata){
						Lobibox.window({
							title: 'Form Edit..',
							width: setwidth,
							height: setheight,
							content: edata.html,
							//shown: function($this){}
						});
					}
				});
			});
		}
	});
	
});
































