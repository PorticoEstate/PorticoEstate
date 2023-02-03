<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="search-page-content">
		<div class="bodySection">
            <div class="border-top border-2 py-5">
                <div class="multisearch w-100">
                    <div class="multisearch__inner">
                        <div class="multisearch__inner__item">
                            <label for="id_label_area">Område</label>
                            <select class="js-select-multisearch" id="id_label_area" aria-label="Large" data-bind="options: towns,
						   optionsText: 'name',
						   value: selectedTown,
						   optionsCaption: 'Område/bydel'"/>

                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="id_label_location">Lokale</label>
                            <select class="js-select-multisearch" id="id_label_location">
                                <option value="">Velg</option>
                            </select>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="datepicker">Dato</label>
                            <input type="text" id="datepicker" placeholder="Velg" />
                        </div>
                        <button type="button" class="btn btn-primary btn--large w-100 d-md-none">Søk</button>
                        <button type="button" class="btn btn-primary btn--circle d-none d-md-flex multisearch__inner__icon-button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
	</div>
</xsl:template>

