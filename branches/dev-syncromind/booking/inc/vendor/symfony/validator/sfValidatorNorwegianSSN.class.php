<?php

/**
 * sfValidatorEmail validates norwegian style Social Security Numbers (fødselsnummer).
 *
 * Oppbygning (http://no.wikipedia.org/wiki/F%C3%B8dselsnummer)
 *
 * Et fødselsnummer består av 11 sifre fordelt på to hoveddeler: fødselsdato (seks sifre) og personnummer (fem sifre).
 *
 *     * De seks første sifrene angir normalt fødselsdato: dag, måned og år. Det finnes eksempler på personer som har fødselsdato på et annet tidspunkt enn hva som fremkommer av de seks første sifrene av fødselsnummeret (som fremstår som en umulig fødselsdato), se D-nummer og H-nummer nedenfor.
 *           o Siffer 1 og 2 er dag i måned (01–31).
 *           o Siffer 3 og 4 er måned i året (01–12).
 *           o Siffer 5 og 6 er de to siste sifrene i årstallet (00–99).
 *     * De fem siste sifrene kalles personnummer.
 *           o De tre første sifrene i personnummeret kalles individsifre. Tidligere ble denne inndelingen brukt:
 *                 + 000–499 omfatter personer født i 1900 eller senere.
 *                 + 500–749 omfatter personer født i 1899 eller tidligere.
 *                 + 750–999 omfatter en del særtilfeller, f.eks. for fremmed arbeidskraft, adopsjon o.l.
 *           o Etter år 2000 har det vært gjort et antall endringer angående fastsettelse av individsifre, og pr. i dag (juli 2005) benyttes denne inndelingen[1]:
 *                 + 000–499 omfatter personer født i perioden 1900–1999.
 *                 + 500–749 omfatter personer født i perioden 1855–1899.
 *                 + 500–999 omfatter personer født i perioden 2000–2039.
 *                 + 900–999 omfatter personer født i perioden 1940–1999.
 *           o Det tredje sifferet i personnummeret angir kjønn: kvinner har partall, menn har oddetall.
 *           o De to siste sifrene i personnummeret kalles kontrollsifre og er beregnet ut fra de foregående sifrene.
 *
 * For individsifre kan altså nummerserien 900–999 benyttes både for personer født i perioden 1940–1999 og for personer født i perioden 2000–2039. Dette er gjort fordi prognoser for innvandring antyder at nummerserien 000–499 ikke vil være tilstrekkelig selv om år 1999 er passert. Personer som er født i 2000 eller senere kan på sin side tildeles ledige numre i serien 500–749 siden det neppe vil bli noen tilvekst i befolkningen av personer født i perioden 1855–1899. I den nye inndelingen ligger også at fødselsnummersystemet må omlegges senest år 2039, mot tidligere senest år 2054. Dette kan bli fremskyndet ytterligere, da det diskuteres en felles oppbygning av identitetsnummer for landene i EU og EØS, hvor man blant annet forventer at det kan bli innført åttesifret fødselsdato for å unngå problemer med århundreskiftet.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfValidatorNorwegianSSN extends sfValidatorString
{
	public function __construct($options = array(), $messages = array())
	{
		parent::__construct($options, $messages);
	}
	
  /**
   * Valid format for a norwegian SSN is DDMMYY\d\d\d\d\d
   *
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {	
	$this->addOption('full_required', true);
    $this->addMessage('invalid', '%field% contains an invalid Norwegian social security number (11 digits)');
    $this->addMessage('invalid2', '%field% contains an invalid Norwegian social security number (6 or 11 digits)');
    parent::configure($options, $messages);
  }

  /**
   * @see sfValidatorString
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

	if($this->getOption('full_required') && !preg_match('/^(0[1-9]|[12]\\d|3[01])([04][1-9]|[15][0-2])\\d{7}$/', $clean))
	{
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
	}
	if(!$this->getOption('full_required') && !preg_match('/^(0[1-9]|[12]\\d|3[01])([04][1-9]|[15][0-2])\\d{2}(\\d{5})?$/', $clean))
	{
      throw new sfValidatorError($this, 'invalid2', array('value' => $value));
	}
    return $clean;
  }
}
