$('.tab1').hide();

var setwidth = $(window).width();
var setheight = $(window).height();
if(setwidth > 1000){
	setwidth = 1000;
}else{
	setwidth = setwidth - 50;
}

if(setheight > 800){
	setheight = 800;
}else{
	setheight = setheight - 50;
}

$('#tab21_CUSCODClear').click(function(){
	$('#tab21_CUSCOD').val('');
	$('#tab21_CUSNAME').val('');
});

var lobibox;
$('#tab21_CUSCODSearch').click(function(){	
	$.ajax({
		url: '../sell/CSYS04/getCustomersForm',
		Type: 'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				shown: function($this){
					$('#customerSearch').click(function(){
						dataToPost = new Object();
						dataToPost.CUSCOD = $('#CUSCOD').val();
						dataToPost.CUSNAME = $('#CUSNAME').val();
						
						var spinner = $('body>.spinner').clone().removeClass('hide');
						$('#resultCustomers').html('');
						$('#resultCustomers').append(spinner);
						
						$.ajax({
							url: '../sell/CSYS04/getCustomersSearch',
							data: dataToPost,
							Type: 'POST',
							dataType:'json',
							success: function(data){
								$('#resultCustomers').find('.spinner, .spinner-backdrop').remove();
								$('#resultCustomers').html(data.html);
								
								document.getElementById("table-fixed-getCustomersSearch").addEventListener("scroll", function(){
									var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
									this.querySelector("thead").style.transform = translate;						
								});
								
								$('.getit').hover(function(){
									$(this).css({'background-color':'yellow'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
								},function(){
									$(this).css({'background-color':'white'});
									$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
								});
								
								$('.getit').click(function(){
									$('#tab21_CUSCOD').val($(this).attr('CUSCOD'));
									$('#tab21_CUSNAME').val($(this).attr('CUSNAME'));
									$this.destroy();
									
								});
							}
						});
					});
				}
			});
		}
	});
});

//$('#tab21_CUSCODAction').attr('disabled',true);



$('#optionAdd').click(function(){
	var table = document.getElementById("table-option");
    var setlength = table.length;
	var attr = setlength;
	alert(setlength);
	alert(attr);
    var row = table.insertRow(setlength);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);
	var cell6 = row.insertCell(5);
	var cell7 = row.insertCell(6);
	var cell8 = row.insertCell(7);
	var cell9 = row.insertCell(8);
	var cell10 = row.insertCell(9);
	
	cell1.innerHTML = "<input type='button' value='ลบ'>";
	cell1.style.color = 'red';
	cell1.style.cursor = 'pointer';
	cell1.setAttribute('nrow','2');
	cell1.className = 'optionDel';
	
    cell2.innerHTML = "NEW CELL2";
	cell3.innerHTML = "NEW CELL3";
	cell4.innerHTML = "NEW CELL4";
	cell5.innerHTML = "NEW CELL5";
	cell6.innerHTML = "NEW CELL6";
	cell7.innerHTML = "NEW CELL7";
	cell8.innerHTML = "NEW CELL8";
	cell9.innerHTML = "NEW CELL9";
	cell10.innerHTML = "NEW CELL10";
	
		
	
	$('#table-option tbody td').on('click', function () {
		$(this).closest('tr').remove();
	});


});

document.getElementById("table-fixed-option").addEventListener("scroll", function(){
	var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
	this.querySelector("thead").style.transform = translate;						
});

/*
$('#table-option tr').on('click', 'input[type="button"]', function () {
    $(this).closest('tr').remove();
});
*/
$('#table-option tr').on('click', function () {
    $(this).closest('tr').remove();
});












