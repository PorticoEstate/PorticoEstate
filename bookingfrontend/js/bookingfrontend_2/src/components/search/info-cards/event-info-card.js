import {getSearchDatetimeString, getSearchTimeString, joinWithDot} from "../search-util";

ko.components.register('event-info-card', {
    viewModel: function(params) {
        this.event = params.event;
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
            const remoteInstance = this.event.remoteInstance?.name ? `<span class="text-overline">${this.event.remoteInstance?.name}</span>` : '';

            return joinWithDot([
                remoteInstance,
                this.event.location_name,
                getSearchDatetimeString(new Date(this.event.from)) + " - " + ((new Date(this.event.from)).getDate() === (new Date(this.event.to)).getDate() ? getSearchTimeString(new Date(this.event.to)) : getSearchDatetimeString(new Date(this.event.to)))].filter(Boolean));
        });
    },
    template: `
        <div class="col-12 mb-4">
          <div class="js-slidedown slidedown">
            <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false" data-bind="click: toggle">
              <span data-bind="text: event.event_name"></span>
                                  <span class="slidedown__toggler__info" data-bind="html: infoText"></span>
            </button>
            <div class="js-slidedown-content slidedown__content" data-bind="withAfterRender: { afterRender: addSlideDown}">
              <p data-bind="text: event.location_name"></p>
              <ul>
                  <li data-bind="text: 'Fra: ' + event.from"></li>
                  <li data-bind="text: 'Til: ' + event.to"></li>
              </ul>
            </div>
          </div>
        </div>
    `
});
