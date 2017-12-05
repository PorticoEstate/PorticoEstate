/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$('#adv-search-toggler').click(function () {
    $('.advance-search').toggle("slide", {direction: "up"}, 800);
    $("i", this).toggleClass("ion-chevron-down ion-chevron-up");
    
    if($("i", this).hasClass("ion-chevron-up")){
        $('html, body').animate({
            scrollTop: $(".advance-search").offset().top
        }, 800);

    } else if($("i", this).hasClass("ion-chevron-down")){
        $('html, body').animate({scrollTop: '0px'}, 800);  
    }
});


