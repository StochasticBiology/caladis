// shows properties of variables when hovered over on output screen
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

$(function() {
    $(".qd-var").hover( function(){ 
		$(this).showTooltip(); 
	}, function(){ 
		$('.tooltip').remove();
	});
    $(".qd-var").click( function(){
        $('.tooltip').remove();
        $(this).showTooltip(); 
    });    
    
    $('.tooltip').click( function(){ $('.tooltip').remove(); });
});


jQuery.fn.showTooltip = function () {
    
    // get distribution properties
    var distArr = $(this).data("dist").split(";");
    
    var propStr = "";
    if( search( dist, distArr[0]) >= 0){
        propStr += "<p class='tooltip-prop'>Distribution: " + dist[ search( dist, distArr[0])].name + "</p>";
        for( var i=0; i<dist[ search( dist, distArr[0])].param.length; i++){
            propStr += "<p class='tooltip-prop'>" + dist[ search( dist, distArr[0])].param[i] + ": " + distArr[i+1] + "</p>";
        }
    }
    
    $("body").append(   "<div class='tooltip'>" +
                            "<div class='tooltip-arrow'></div>" +
                            "<p class='tooltip-var'>" + $(this).text() + "</p>" +
                            propStr +
                        "</div>");
    
    
    var pos = $(this).offset();  
    $(".tooltip").css( { "left": (pos.left + ($(this).width()/2) - 110) + "px", "top": (pos.top+30) + "px" } ).show();        
    
    return this;
};
