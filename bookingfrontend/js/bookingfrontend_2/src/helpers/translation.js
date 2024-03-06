/**
 *
 */



if (!globalThis['trans']) {
    // Initialize translations as a Knockout observable object
    globalThis['translations'] = ko.observable({});
    const getJsonURL = phpGWLink('bookingfrontend/lang.php', null, true);

    fetch(getJsonURL).then(a => a.json()).then(a => globalThis['translations'](a))

    // Define the translate function
    globalThis['trans'] = (group, tag, ...args) => {
        // Retrieve the current value of the translations observable
        const translations = globalThis['translations']();
        // Check if the group exists in the translations
        if (translations[group] && translations[group][tag]) {
            // Retrieve the template string for the specified group and tag
            let template = translations[group][tag];

            // Replace each placeholder with the corresponding argument
            return template.replace(/%\d+/g, placeholder => {
                // Extract the number from the placeholder, convert it to index (0-based)
                const index = parseInt(placeholder.substring(1)) - 1;
                // Replace placeholder with the argument, if exists, otherwise keep the placeholder
                return args[index] !== undefined ? args[index] : placeholder;
            });
        } else {
            if(group !== 'common') {
                return globalThis['trans']('common', tag, args);
            }
            // Return a fallback string or handle missing translation
            return `Missing translation for [${group}][${tag}]`;
        }
    };


    ko.components.register('trans', {
        viewModel: {
            createViewModel: function (params, componentInfo) {
                const ViewModel = (params) => {
                    console.log("PARAMS", params)
                    let translationTagParts = componentInfo.templateNodes.map(a => a.textContent)
                    if (translationTagParts.length === 1) {
                        if (translationTagParts[0].includes(':')) {
                            translationTagParts = translationTagParts[0].split(':')
                        }
                    }
                    translationTagParts = translationTagParts.map(text => text.trim())

                    self.translations = globalThis['translations'];
                    self.translated = ko.computed(() => {
                        if (self.translations && self.translations() && Object.keys(self.translations()).length > 0) {
                            if (!params.group || !params.tag) {
                                return globalThis['trans'](translationTagParts[0], translationTagParts[1], params.args)

                            }
                            return globalThis['trans'](params.group, params.tag, params.args)
                        }
                    })
                }
                return ViewModel(params);
            },

        },
        // language=HTML
        template: `<!--ko text: translated()--><!--/ko-->`
    });


    // console.log("LOADED TRANSLATIONS", trans('tag', 'test'))
}
