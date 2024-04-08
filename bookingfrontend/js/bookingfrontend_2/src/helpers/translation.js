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

        // Function to process translation for a single group
        const processTranslation = (singleGroup) => {
            // Check if the group exists in the translations
            if (translations[singleGroup] && translations[singleGroup][tag]) {
                // Retrieve the template string for the specified group and tag
                let template = translations[singleGroup][tag];

                // Replace each placeholder with the corresponding argument
                return template.replace(/%\d+/g, placeholder => {
                    // Extract the number from the placeholder, convert it to index (0-based)
                    const index = parseInt(placeholder.substring(1)) - 1;
                    // Replace placeholder with the argument, if exists, otherwise keep the placeholder
                    return args[index] !== undefined ? args[index] : placeholder;
                });
            }
            return null; // Indicate no translation found for this group
        };

        // Check if group is an array and try to find a translation in one of the groups
        if (Array.isArray(group)) {
            for (let i = 0; i < group.length; i++) {
                const translation = processTranslation(group[i]);
                // if(translation) {
                //     console.log("found translation", group[i], tag, translation)
                // }
                if (translation !== null) return translation;
            }
        } else {
            // If group is not an array, process it directly
            const translation = processTranslation(group);
            // if(translation) {
            //     console.log("found translation", group, tag, translation)
            // }
            if (translation !== null) return translation;
        }

        // If no translation was found in the specified groups, and 'common' wasn't already tried, try 'common'
        if (!Array.isArray(group) || !group.includes('common')) {
            const commonTranslation = processTranslation('common');
            // if(commonTranslation) {
            //     console.log("found translation", 'common', tag, translation)
            // }
            if (commonTranslation !== null) return commonTranslation;
        }

        // If no translation was found even in 'common', return a fallback string
        return `Missing translation for [${Array.isArray(group) ? group.join(', ') : group}][${tag}]`;
    };


    class TransViewModel {
        constructor(params, componentInfo) {
            let translationTagParts = componentInfo.templateNodes.map(a => a.textContent)
            if (translationTagParts.length === 1) {
                if (translationTagParts[0].includes(':')) {
                    translationTagParts = translationTagParts[0].split(':')
                }
            }
            translationTagParts = translationTagParts.map(text => text.trim())

            // Adjust to handle the case where translationTagParts might not have both parts due to splitting by ':'
            if(translationTagParts.length >= 2) {
                this.tag = ko.observable(translationTagParts[1]);
                this.group = ko.observable(translationTagParts[0]);
            } else {
                this.tag = typeof params.tag === 'function' ? params.tag : ko.observable(params.tag);
                this.group = typeof params.group === 'function' ? params.group : ko.observable(params.group);
            }

            // Accepting suffix parameter
            this.suffix = ko.observable(params.suffix || ''); // Default to empty if not provided

            this.args = ko.observable(params.args);
            this.translations = globalThis['translations'];
            this.translated = ko.computed(() => {
                if (self.translations && self.translations() && Object.keys(self.translations()).length > 0 && this.group && this.tag) {
                    let translation = globalThis['trans'](this.group(), this.tag(), this.args());
                    // Apply suffix if it exists
                    return translation + this.suffix();
                }
            })
        }
    }
    ko.components.register('trans', {
        viewModel: {
            createViewModel: function (params, componentInfo) {
                return new TransViewModel(params, componentInfo); // Create a new instance of the view model class for each component instance
            },

        },
        // language=HTML
        template: `<!--ko text: translated()--><!--/ko-->`
    });


    // console.log("LOADED TRANSLATIONS", trans('tag', 'test'))
}
