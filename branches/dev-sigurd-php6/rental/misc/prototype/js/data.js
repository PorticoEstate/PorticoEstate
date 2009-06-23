YAHOO.namespace('booking');
YAHOO.booking.Data = {
  buildings: [
    {id:  1, name: 'Haukelandshallen', district: 'Årstad', 
    address: 'St. Olavsv. 50', img: '../frontend/imgs/haukelandshallen.jpg'},
    {id:  2, name: 'Vikinghallen', district: '', 
     address: ''},
    {id:  3, name: 'Vitalitetssenteret', district: 'Møhlenpris',
     address: 'Wolfsgate 12X', img: '../backend/img/p1000716.jpg',
     mapThumb: '../frontend/imgs/vitalitetssenter_thumb.jpg',
     map: '../frontend/imgs/vitalitetssenter.pdf',
     desc: '<p>Vitalitetsenteret på Møhlenpris Adresse: Wolfsgate 12X. Består av en kulturdel og en idrettshall, samt fellesarealer og kafé. Det er leiligheter over kulturarealene og parkeringsanlegg med soneparkering Under hele bygget. Vitalitetsenteret på Møhlenpris ble åpnet i 2003 etter en lang planleggingsperiode, hvor lokale organisasjoner var svært aktive.</p><p>I dag fremstår senteret som en møteplass for aktive og engasjerte mennesker fra nærmiljøet og byen for øvrig. Idrett og kultur driver dette bygget sammen. På huset er der kontorfasiliteter til Djerv, frivillighetssentralen, Møhlenpris vel og 2gether.</p><p>Vitalitetsenteret er tilgjengelige for lokale kulturaktører, organisasjoner, lag og privat personer. Bergenhus og Årstad kulturkontor forvalter kulturdelen og sørger for en kulturfaglig tilnærming og prioritering etter politiske vedtak og føringer.</p>'
     },
    {id:  4, name: 'Ulriken bydelssenter', district: 'Ulriken',
    address: 'Landåsveien 31'},
    {id:  5, name: 'Meyermarken Bydelshus', district: 'Meyermarken',
    address: 'Ladegårdsgaten 65 B'}
  ],
  resources: [
    {id:  1, building_id:  1, name: 'Bane 1', cost: '100'},
    {id:  2, building_id:  1, name: 'Bane 2', cost: '100'},
    {id:  3, building_id:  1, name: 'Bane 3', cost: '100'},
    {id:  4, building_id:  3, name: 'Musikkøvningsrom 1', cost: '50'},
    {id:  5, building_id:  3, name: 'Musikkøvningsrom 2', cost: '50'},
    {id:  6, building_id:  3, name: 'Kunstverksted', cost: '100'},
    {id:  7, building_id:  3, name: 'Videoverksted', cost: '200'},
    {id:  8, building_id:  3, name: 'Dataverksted', cost: '100'},
    {id:  9, building_id:  3, name: 'Storsal', cost: '300'},
    {id: 11, building_id:  3, name: 'Idrettshall', img: '../backend/img/p1000702.jpg', cost: '400',
    desc: '<p>Idrettsdelen består av: 1 stk flerbruks idrettshall med klatrevegg og standard utrusning for denne typen hall 1 stk. fotballbane kunstgress 100 x 60 meter. 1. stk fotballbane kunstgress 50x30 meter 1 . stk styrkerom med apperatur. 3. stk. garderobeanlegg, 1 stk. vaktrom</p><p>Idrettshallen Håndballbane som kan deles inn i 3 mindre baner for f. eks. basket, volley eller lek/undervisning. Klatrevegg til bruk med kyndig personell. Diverse utstyr til forannevnte aktiviteter m.m.</p>'},
    {id: 12, building_id:  3, name: 'Styrkerom', cost: '100',
    desc: '<p>Styrkerom med diverse styrkeapperatur til bruk etter tildeling fra Bergen kommune.</p>'}
  ],
  equipment: [
    {id:1, resource_id: 4, name: '2 x Loudspeaker', included: 'Included'},
    {id:2, resource_id: 4, name: 'Mixer board', included: 'Extra', cost: '100'}
  ],
  bookings:[
    {id:  1, resources: [1,2], name: 'BHK',
     from: '10:00', to: '12:00', dow: 'mon'},
    {id:  2, resources: [1], name: 'Årstad',
     from: '11:00', to: '14:45', dow: 'tue'},
    {id:  3, resources: [1,2], name: 'Gimle',
     from: '15:00', to: '16:00', dow: 'mon'},
    {id:  4, resources: [4], name: 'BGS',
     from: '15:00', to: '19:00', dow: 'tue'},
	{id:  5, resources: [4], name: 'BGS',
     from: '15:00', to: '16:00', dow: 'wed'},
	{id:  6, resources: [4], name: 'BFS',
     from: '16:00', to: '18:00', dow: 'mon'},
	{id:  7, resources: [5,6,7], name: 'BFS',
     from: '16:00', to: '18:00', dow: 'wed'},
	{id:  10, resources: [11], name: 'Gimle BBK',
     from: '18:00', to: '20:00', dow: 'wed'},
	{id:  11, resources: [11], name: 'Gimle BBK',
     from: '18:00', to: '20:00', dow: 'thu'},
	{id:  12, resources: [11], name: 'Årstad Hb',
     from: '20:00', to: '21:00', dow: 'mon'},
	{id:  13, resources: [11], name: 'Årstad Hb',
     from: '20:00', to: '22:00', dow: 'thu'},
	{id:  14, resources: [11], name: 'Bbk',
     from: '16:00', to: '19:00', dow: 'mon'},
	{id:  15, resources: [11], name: 'Bbk',
     from: '16:00', to: '20:00', dow: 'tue'},
	{id:  15, resources: [5,6,7], name: 'Bbk',
     from: '16:00', to: '18:00', dow: 'thu'},
	{id:  15, resources: [5,6], name: 'Bbk',
     from: '18:00', to: '19:00', dow: 'thu'},
	{id:  15, resources: [5,6], name: 'Bbk',
     from: '20:00', to: '22:00', dow: 'fri'},
	{id:  15, resources: [8], name: 'BH',
     from: '18:00', to: '22:00', dow: 'fri'}
  ],
  organizations: [
    {id: 1, name: 'Årstad Håndball'},
    {id: 2, name: 'Gimle BBK'},
    {id: 3, name: 'Tertnes Elite'},
    {id: 4, name: 'Viking Håndball'},
    {id: 5, name: 'BHK'},
    {id: 6, name: 'Bergen kammerkor',
     desc: '<p>Vil du synge med oss?</p><p>Har du note- og/eller korerfaring, da håper vi du har lyst til å synge med oss. Hos oss vil du få musikalske utfordringer, variert repertoar, en spesielt dyktig dirigent og et trivelig miljø! Vi øver på mandager kl. 19.00 - 21.30 i Vitalitetssenteret på Møhlenpris. Ta kontakt hvis du vil vite mer. Leder Cecilie Lycke, tlf. 917 39 377 E-post: post@bergen-kammerkor.no</p>'},
    {id: 7, name: 'Ashtanga Yoga',
     desc: '<p>Ashtanga yoga er en veldig fysisk og dynamisk form for yoga. Ashtanga yoga gir økt styrke, fleksibilitet og kontroll. Yogaen består av forskjellige stillinger eller posturer, disse kalles for asana. Rekkefølgen på de forskjellige stillingene er viktig fordi hver stilling forbereder kroppen på neste stilling. For å gjennomføre asana på korrekt måte i Ashtanga yoga må vi fokusere på tre ting under øvelsene</p>'}
  ],
  groups: [
    {id: 1, org_id: 1, name: 'Håndball Gutter 97'},
    {id: 2, org_id: 1, name: 'Håndball Jenter 97'}
  ],
  seasons: [
    {id: 1, name: 'Idrott 2008', building_id: 1,
     from: '2008/01/01', to: '2008/12/30', status: 'Active', allocations:[]},
    {id: 3, name: 'Idrott 2009', building_id: 1,
     from: '2009/01/01', to: '2009/12/30', status: 'Planning', allocations:[
    {id:  1, resources: [1,2], name: 'BHK',
     from: '18:00', to: '21:00', dow: 'mon'},
    {id:  2, resources: [1,2], name: 'BHK',
     from: '18:00', to: '21:00', dow: 'wed'},
    {id:  3, resources: [1,2], name: 'Årstad',
     from: '18:00', to: '20:00', dow: 'tue'},
    {id:  4, resources: [1,1], name: 'Gimle BBK',
     from: '18:00', to: '21:00', dow: 'thu'},
    {id:  5, resources: [1,2], name: 'Viking Håndball',
     from: '18:00', to: '20:00', dow: 'fri'},
    {id:  6, resources: [3], name: 'Gimle BBK',
     from: '21:00', to: '22:00', dow: 'fri'}
     ]},
    {id: 4, name: 'Idrott 2009', building_id: 3,
     from: '2009/01/01', to: '2009/12/30', status: 'Planning', allocations:[
    {id:  1, resources: [11], name: 'BHK',
     from: '18:00', to: '21:00', dow: 'mon'},
    {id:  2, resources: [11], name: 'BHK',
     from: '18:00', to: '21:00', dow: 'wed'},
    {id:  3, resources: [11], name: 'Årstad',
     from: '18:00', to: '20:00', dow: 'tue'},
    {id:  4, resources: [11], name: 'Gimle BBK',
     from: '18:00', to: '21:00', dow: 'thu'},
    {id:  5, resources: [11], name: 'Viking Håndball',
     from: '18:00', to: '20:00', dow: 'fri'},
    {id:  6, resources: [12], name: 'Gimle BBK',
     from: '21:00', to: '22:00', dow: 'fri'}
     ]},
    {id: 7, name: 'Kultur 2009', building_id: 3,
     from: '2009/01/01', to: '2009/12/30', status: 'Active', allocations:[]},
  ],
  applications: [
    {id: 123, from: 'Fredrik Sjöberg', date: '2009-01-08', org: 'Årstad', status: 'New'},
    {id: 224, from: 'Jonas Borgström', date: '2009-01-09', org: 'Redpill Linpro', status: 'Accepted'},
    {id: 345, from: 'Martin Midböe', date: '2009-01-10', org: 'Redpill Linpro', status: 'Confirmed'}
  ],
  cancelledbookings: [
    {id: 1, date: '2009-01-08', time: '10:00-12:00', name: 'Årstad'},
  ],
  costs: [
    {id: 1, date: '2009-01-07', amount: 100, payer: 'Arne Weise', 
     org: 'Årstad', group: 'Group 1', type: 'Booking'},
    {id: 2, date: '2009-01-08', amount: 200, payer: 'Arne Weise',
     org: 'Årstad', group: 'Group 1', type: 'Booking'},
    {id: 3, date: '2009-01-09', amount: 200, payer: 'Arne Weise', 
     org: 'Årstad', group: 'Group 1', type: 'Cancellation fee'}
  ],
  events: [
  ]
};

getApplication = function(id) {
    var x = YAHOO.booking.Data.applications.filter(function(x) {return x.id==id});
    return x.length > 0 ? x[0] : null;
}
getBuilding = function(id) {
    var x = YAHOO.booking.Data.buildings.filter(function(x) {return x.id==id});
    return x.length > 0 ? x[0] : null;
}
getResource = function(id) {
    var x = YAHOO.booking.Data.resources.filter(function(x) {return x.id==id});
    return x.length > 0 ? x[0] : null;
}
getResources = function(building_id) {
    return YAHOO.booking.Data.resources.filter(function(x) {return x.building_id==building_id});
}
getSeason = function(id) {
    var x = YAHOO.booking.Data.seasons.filter(function(x) {return x.id==id});
    return x.length > 0 ? x[0] : null;
}
