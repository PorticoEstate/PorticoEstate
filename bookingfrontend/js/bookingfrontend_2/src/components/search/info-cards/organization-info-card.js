import {getSearchDatetimeString, getSearchTimeString, joinWithDot} from "../search-util";

ko.components.register('organization-info-card', {
    viewModel: function(params) {
        this.organization = params.organization;
        this.expanded = ko.observable(false);
        this.slideDownTarget = ko.observable();
        const self= this;
        self.addSlideDown = (target)  => {
            self.slideDownTarget(target)
        }
        self.toggle = (data,clickEvent)  => {
            if(clickEvent.target?.tagName === 'A') {
                return true;
            }
            const expanded = !this.expanded();

            expanded ? $(self.slideDownTarget()).slideDown(): $(self.slideDownTarget()).slideUp();

            self.expanded(expanded);
            return false;
        }
        self.infoText = ko.computed(() => {
            const remoteInstance = this.organization.remoteInstance?.name ? `<span class="text-overline">${this.organization.remoteInstance?.name}</span>` : '';

            return joinWithDot([
                remoteInstance,
                this.organization.email, this.organization.street].filter(Boolean));
        });


    },
    template: `
        <div class="col-12 mb-4">
          <div class="js-slidedown slidedown">
            <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false"  data-bind="click: toggle">
              <span data-bind="text: organization.name"></span>
              <span class="slidedown__toggler__info">
                <!-- Bind other organization details here -->
                <span class="slidedown__toggler__info" data-bind="html: infoText"></span>
              </span>
            </button>
            <div class="js-slidedown-content slidedown__content"  data-bind="withAfterRender: { afterRender: addSlideDown}">
              <p data-bind="text: organization.description"></p>
              <ul>
                  <li data-bind="text: 'Hjemmeside: ' + organization.homepage"></li>
                  <li data-bind="text: 'Tlf: ' + organization.phone"></li>
                  <li data-bind="text: 'E-post: ' + organization.email"></li>
                  <li data-bind="text: 'Adresse: ' + organization.street"></li>
                  <li data-bind="text: 'Postnr: ' + organization.zip_code"></li>
                  <li data-bind="text: 'Poststed: ' + organization.city"></li>
                  <li data-bind="text: 'Distrikt: ' + organization.district"></li>
                  <li><a data-bind="attr: { href: phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiorganization.show', id: organization.original_id !== undefined ? organization.original_id : organization.id}, false, organization.remoteInstance?.webservicehost || undefined) }">Mer info</a></li>
              </ul>
            </div>
          </div>
        </div>
    `
});
