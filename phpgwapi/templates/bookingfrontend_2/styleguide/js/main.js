$(document).ready(function () {
    /* Basic dropdown */
    $('.js-select-multisearch').select2({
        theme: 'select-v2 select-v2--main-search',
        width: '100%',
    });

    //Datepicker
    $('#datepicker').datepicker();

    // $(".multisearch__inner__item").on("mouseDown", function () {
    //     if ($(this).find('span .select2-container--open')) {
    //         $(this).find('.js-select-multisearch').select2("open");
    //         $(this).find('#datepicker').datepicker('show');
    //     } else {
    //         $(this).find('.js-select-multisearch').select2("close");
    //         $(this).find('#datepicker').datepicker("hide");
    //     }
    // });


});
