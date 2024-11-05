import {getSearchDatetimeString, getSearchTimeString, joinWithDot} from "../search-util";

ko.components.register('organization-info-card', {
    viewModel: function (params) {
        this.organization = params.organization;
        this.expanded = ko.observable(false);
        // this.slideDownTarget = ko.observable();
        const self = this;
        // self.addSlideDown = (target)  => {
        //     self.slideDownTarget(target)
        // }
        // self.toggle = (data,clickEvent)  => {
        //     if(clickEvent.target?.tagName === 'A') {
        //         return true;
        //     }
        //     const expanded = !this.expanded();
        //
        //     expanded ? $(self.slideDownTarget()).slideDown(): $(self.slideDownTarget()).slideUp();
        //
        //     self.expanded(expanded);
        //     return false;
        // }
        self.infoText = ko.computed(() => {
            const remoteInstance = this.organization.remoteInstance?.name ? `<span class="text-overline">${this.organization.remoteInstance?.name}</span>` : '';

            return joinWithDot([
                remoteInstance,
                this.organization.email, this.organization.street].filter(Boolean));
        });


    },
    // language=HTML
    template: `
        <div class="col-12 mb-4">
            <a class="link-button " type="button" aria-expanded="false"
               data-bind="attr: { href: phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiorganization.show', id: organization.original_id !== undefined ? organization.original_id : organization.id}, false, organization.remoteInstance?.webservicehost || undefined) }">
                <div style="min-height: 54px">
                    <div class="font-weight-bold gap-2 d-flex align-items-center mb-1">
                        <h3 class="m-0 fa-solid fa-futbol" style="font-size:20px"></h3>
                        <h3 class="m-0" data-bind="text: organization.name">
                        </h3>
                    </div>
                    <span class="slidedown__toggler__info" data-bind="html: infoText"></span>
                </div>
            </a>
        </div>
    `
});
