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
            $('html, body').animate({scrollTop: $("#adv-search-toggler").offset().top }, 800);
        } else {
            check = 0;
            $('html, body').animate({scrollTop: '0px'}, 800);
        }
    });
});



