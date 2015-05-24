$(function() {
	$('#collaborators_toggle').on('click', function() {
	    var el = $('#collaborators_row'),
	    	curHeight = el.height(),
	    	autoHeight = el.css('height', 'auto').height();

	    if (curHeight > 105) 
	    {
			el.animate({height: '105px'}, 500);
	    }
	    else {
			el.height(curHeight).animate({height: autoHeight}, 500);
	    }
	});
});