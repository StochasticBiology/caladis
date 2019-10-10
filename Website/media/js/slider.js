// controls interactive sliders
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

function update_slider( lb, ub){
    $("#slider-lb").text( lb);
    $("#slider-ub").text( ub);
    
    var prob = 0;
    $('.hist li').each(function(){
        var min = $(this).data("min");
        if(  min >= lb && min < ub ) prob += $(this).data("prob")
    });
  
     $("#slider-prob").text( Math.round(prob*1000)/10);   
}