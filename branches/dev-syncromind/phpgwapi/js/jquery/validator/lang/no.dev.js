/**
 * jQuery Form Validator
 * ------------------------------------------
 *
 * Swedish language package
 *
 * @website http://formvalidator.net/
 * @license MIT
 * @version 2.2.83
 */
(function($, window) {

  'use strict';

  $(window).bind('validatorsLoaded', function() {

    $.formUtils.LANG = {
      errorTitle: 'innsending av skjema mislyktes!',
      requiredField: 'Dette er et obligatorisk felt',
      requiredFields: 'Du har ikke svart på alle obligatoriske felter',
      badTime: 'Du har ikke angitt en gyldig tid',
      badEmail: 'Du har ikke angitt en gyldig e-postadresse',
      badTelephone: 'Du har ikke angitt et gyldig telefonnummer',
      badSecurityAnswer: 'Du har ikke gitt korrekt svar på sikkerhetsspørsmålet',
      badDate: 'Du har ikke gitt en gyldig dato',
      lengthBadStart: 'Inputverdien må være mellom ',
      lengthBadEnd: ' karakterer',
      lengthTooLongStart: 'Inputverdien er lengre enn ',
      lengthTooShortStart: 'Inputverdien er kortere enn ',
      notConfirmed: 'Inputverdiene kunne ikke bekreftes',
      badDomain: 'Feilaktig domene verdi',
      badUrl: 'Inputverdiene er ikke et riktig nettadresse',
      badCustomVal: 'Inputverdien er feil',
      andSpaces: ' og mellomrom ',
      badInt: 'Du har ikke angitt et tall',
      badSecurityNumber: 'Personnummeret validerer ikke',
      badUKVatAnswer: 'Feil britisk moms-kode',
      badStrength: 'Passordet er ikke sterk nok',
      badNumberOfSelectedOptionsStart: 'Du må velge minst ',
      badNumberOfSelectedOptionsEnd: ' svar',
      badAlphaNumeric: 'Inputverdiene kan bare inneholde alfanumeriske tegn ',
      badAlphaNumericExtra: ' og ',
      wrongFileSize: 'Filen du prøver å laste opp er for stor (max %s)',
      wrongFileType: 'Bare filer av type %s er mulig',
      groupCheckedRangeStart: 'Vennligst velg mellom ',
      groupCheckedTooFewStart: 'Vennligst velg minst ',
      groupCheckedTooManyStart: 'Vennligst velg maksimum ',
      groupCheckedEnd: ' alternativ',
      badCreditCard: 'Kredittkortnummeret er ikke gyldig',
      badCVV: 'CVV-nummer ikke var gyldig',
      wrongFileDim : 'Feil bildedimensjoner,',
      imageTooTall : 'bildet kan ikke være høyere enn',
      imageTooWide : 'bildet kan ikke være bredere enn',
      imageTooSmall : 'bildet var for liten',
      min : 'minimum',
      max : 'maximum',
      imageRatioNotAccepted : 'Bildeforholdet kan ikke aksepteres',
      badBrazilTelephoneAnswer: 'Telefonnummeret er ugyldig',
      badBrazilCEPAnswer: 'CEP er ugyldig',
      badBrazilCPFAnswer: 'CPF er ugyldig'
    };

  });

})(jQuery, window);
