
const createJsSlidedowns = () => {
    // Dropdown f.ex. search result
    $(".js-slidedown").each(function(){
        var $toggler = $(this).find(".js-slidedown-toggler");
        var $dropDown = $(this).find(".js-slidedown-content");

        $(this).on("click", function(){
            $dropDown.slideToggle('fast', function() {
                if($dropDown.is(':visible')) {
                    $toggler.attr("aria-expanded","true");
                    $('.js-slidedown').addClass('slidedown--open');
                } else {
                    $toggler.attr("aria-expanded","false");
                    $('.js-slidedown').removeClass('slidedown--open');
                }
            });
        });
    });
}

$(document).ready(function () {
    /* Basic dropdown */
     $('#js-select-basic').select2({
        theme: 'select-v2',
    });

    // Dropdown f.ex. information
    $(document).on('click', function(event) {
        var container = $(".js-dropdown");

        //check if the clicked area is dropdown or not
        if (container.has(event.target).length === 0) {
            $('.js-dropdown-toggler').attr("aria-expanded","false");
        }
    })

    $(".js-dropdown-toggler").each(function(){
        $(this).on("click", function(){
            var isExpanded = $(this).attr("aria-expanded");        
            $(this).attr("aria-expanded", function() {
            return (isExpanded == "false") ? "true" : "false";
            });
        });
    });

    createJsSlidedowns();


    //Datepicker
    $('#datepicker').datepicker();

    
    /* Dropdown multisearch */
    $('.js-select-multisearch').select2({
        theme: 'select-v2 select-v2--main-search',
        width: '100%',
    });

    $(".multisearch__inner__item").on("mouseDown", function () {
        if ($(this).find('span .select2-container--open')) {
            $(this).find('.js-select-multisearch').select2("open");
            $(this).find('#datepicker').datepicker('show');
        } else {
            $(this).find('.js-select-multisearch').select2("close");
            $(this).find('#datepicker').datepicker("hide");
        }
    });
});
