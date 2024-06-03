// Custom binding for Select2
ko.bindingHandlers.select2 = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var options = {
            theme: 'select-v2',
            width: '100%',
                ...valueAccessor() || {}
        };
        $(element).select2(options);

        // When the selectedOptions observable changes, update the select2 element
        // var selectedOptions = allBindingsAccessor().selectedOptions;
        // if (selectedOptions) {
        //     selectedOptions.subscribe(function(changes) {
        //         $(element).val(ko.unwrap(selectedOptions)).trigger('change');
        //     });
        // }

        // Dispose the select2 plugin when the element is removed
        ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
            $(element).select2('destroy');
        });
    },
    update: function(element, valueAccessor, allBindingsAccessor) {
        var options = ko.unwrap(valueAccessor());
        var selectedOptions = allBindingsAccessor().selectedOptions;
        var value = allBindingsAccessor().value;

        if (selectedOptions && ko.unwrap(selectedOptions).length === 0) {
            $(element).val(ko.unwrap(selectedOptions)).trigger('change');
        }
        if(value && ko.unwrap(value) === undefined) {
            $(element).val(ko.unwrap(value)).trigger('change');

        }
    }
};


// Custom binding for Datepicker
ko.bindingHandlers.datepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var options = {
            dateFormat: "d.m.yy",
            changeMonth: true,
            changeYear: true,
            dayNames: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"],
            dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
            dayNamesShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
            monthNames: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"],
            monthNamesShort: monthNamesShort,
            firstDay: 1
        };
        // var options = ko.unwrap(valueAccessor());
        $(element).datepicker(options);

        // Dispose the datepicker plugin when the element is removed
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            $(element).datepicker('destroy');
        });
    }
};

/**
 * Custom binding for Multiselect with Datepicker inside,
 * @deprecated preferable do not use
 */
ko.bindingHandlers.multiselectWithDatepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var options = {
            select2Options: {
                theme: 'select-v2 select-v2--main-search',
                width: '100%',
            },
            datepickerOptions: {
                dateFormat: "d.m.yy",
                changeMonth: true,
                changeYear: true,
                dayNames: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"],
                dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
                dayNamesShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
                monthNames: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"],
                monthNamesShort: monthNamesShort,
                firstDay: 1
            }
        }

        // Initialize Select2
        $(element).find('.js-select-multisearch').select2(options.select2Options);

        // Initialize Datepicker
        $(element).find('#datepicker').datepicker(options.datepickerOptions);

        // Bind mouseDown event
        $(element).find(".multisearch__inner__item").on("mouseDown", function () {
            if ($(this).find('span .select2-container--open')) {
                $(this).find('.js-select-multisearch').select2("open");
                $(this).find('#datepicker').datepicker('show');
            } else {
                $(this).find('.js-select-multisearch').select2("close");
                $(this).find('#datepicker').datepicker("hide");
            }
        });

        // Dispose the plugins when the element is removed
        ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
            $(element).find('.js-select-multisearch').select2('destroy');
            $(element).find('#datepicker').datepicker('destroy');
        });


        // When the selectedOptions observable changes, update the select2 element
        var selectedOptions = allBindingsAccessor().selectedOptions;
        if (ko.isObservable(selectedOptions)) {
            selectedOptions.subscribe(function () {
                $(element).find('.js-select-multisearch').val(ko.unwrap(selectedOptions)).trigger('change');
            });
        } else {
            console.log("OH NO")
        }
    },
    update: function(element, valueAccessor, allBindingsAccessor) {
        // var options = ko.unwrap(valueAccessor());
        var selectedOptions = allBindingsAccessor().selectedOptions;

        if (selectedOptions) {
            console.log("CHANGEVENT", ko.unwrap(selectedOptions))

            $(element).find('.js-select-multisearch').val(ko.unwrap(selectedOptions)).trigger('change');
        }
    }
};
// Custom binding for Toggle Filter
ko.bindingHandlers.toggleFilter = {
    init: function (element, valueAccessor) {
        $(element).click(function () {
            $(this).toggleClass("toggle-filter--show");
            $(".filter-element").toggleClass("d-block");

            if ($(this).hasClass('toggle-filter--show')) {
                $(this).text('Se færre filter');
            } else {
                $(this).text('Se flere filter');
            }
        });
    }
};
