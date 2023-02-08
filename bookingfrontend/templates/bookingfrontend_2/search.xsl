<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <H1 class="text-primary">Lei lokale til det du trenger</H1>
    <div id="search-page-content">
        <div class="bodySection">
            <div class="multisearch w-100">
                <div class="multisearch__inner">
                    <div class="multisearch__inner__item">
                        <label for="id_label_search">Søk</label>
                        <input id="id_label_search" type="text" placeholder="Søk"></input>
                    </div>
                    <div class="multisearch__inner__item multisearch__inner__item--border">
                        <label for="id_label_area">Område</label>
                        <select class="js-select-multisearch" id="id_label_area" aria-label="Bydel" data-bind="options: towns,
						   optionsText: 'name',
						   value: selectedTown,
						   optionsCaption: 'Område/bydel'"/>

                    </div>
                    <div class="multisearch__inner__item multisearch__inner__item--border">
                        <label for="id_label_location">Lokale</label>
                        <select class="js-select-multisearch" id="id_label_location" aria-label="Lokale" data-bind="options: locations,
                            optionsText: 'name',
                            value: selectedLocation,
                            optionsCaption: 'Velg'
                            "/>
                    </div>
                    <div class="multisearch__inner__item multisearch__inner__item--border">
                        <label for="datepicker">Dato</label>
                        <input type="text" id="datepicker" placeholder="Velg"/>
                    </div>
                    <button type="button" class="pe-btn pe-btn-primary pe-btn--large w-100 d-md-none">Søk</button>
                    <button type="button"
                            class="pe-btn pe-btn-primary pe-btn--circle d-none d-md-flex multisearch__inner__icon-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 mb-4">
                <label class="text-bold text-primary" for="js-select-activities">Aktivitet</label>
                <select class="js-select-multisearch" id="js-select-activities" aria-label="Aktiviteter"
                        multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selectedActivities
            "/>
            </div>
            <div class="col-sm-4 mb-4">
                <label class="text-bold text-primary" for="js-select-resources">Ressurser</label>
                <select class="js-select-multisearch" id="js-select-resources" aria-label="Ressurser"
                        multiple="true" data-bind="options: resources,
            optionsText: 'name',
            selectedOptions: selectedResources
            "/>
            </div>
            <div class="col-sm-4 mb-4">
                <label class="text-bold text-primary" for="js-select-facilities">Fasiliteter</label>
                <select class="js-select-multisearch" id="js-select-facilities" aria-label="Fasiliteter"
                        multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selectedFacilities
            "/>
            </div>
        </div>
    </div>
</xsl:template>

