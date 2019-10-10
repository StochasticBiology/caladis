// sets up menu interactivity
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

$(function() {
    var $btns, $btnActive, $contentActive;
    $btns = $('.menu-item');
    $btnActive = $btns.first().addClass('active');
    $contentActive = $( $btnActive.attr('href'));
        
    $btns.not(':first').each( function(){
        $( $(this).attr('href')).hide();
    });

    $btns.click( function(e){
                
        $btnActive.removeClass('active');        
        $btnActive = $(this).addClass('active');
        
        $contentActive.hide();
        $contentActive = $( $btnActive.attr('href')).show();

        e.preventDefault();
        
        location.hash = $(this).attr('href');
    });
    
	if( location.hash){
        $('a[href="' + window.location.hash + '"]').click();
    }
});
