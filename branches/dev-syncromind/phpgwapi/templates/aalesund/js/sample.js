/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function toogleAdvSearch() {

    var check = 0;
    $('#adv-search-toggler').click(function () {
        $('.advance-search').toggle("slide", {direction: "up"}, 800);

        if (check === 0) {
            check = 1;
            $('html, body').animate({scrollTop: $("#adv-search-toggler").offset().top}, 800);
        } else {
            check = 0;
            $('html, body').animate({scrollTop: '0px'}, 800);
        }
    });
});




function openModal(param){
    $('#mediaModal').modal('show');
    var src = $(param).attr('src');
    $('#fullSizeImage').empty();
    $('#fullSizeImage').append('<img src="'+ src +'" />');
}

$(function toogleAdvSearch() {
    if (!document.getElementById("main-page")) {
        $(".header-container").css("border-bottom", "1.5px solid #89266a");
        $(".header-container").css("margin-bottom", "2em");
    }
});


//$(function checkView() {
//
//$(window).scroll(function() {
//    var top_of_element = $("#advance-search-container").offset().top;
//    var bottom_of_element = $("#advance-search-container").offset().top + $("#advance-search-container").outerHeight();
//    var bottom_of_screen = $(window).scrollTop() + window.innerHeight;
//    var top_of_screen = $(window).scrollTop();
//
//    if((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)){
//        $("#update-search-result").show();
//    }
//    else {
//        $("#update-search-result").hide();
//    }
//});
//
//});