<?php
namespace Page;

class PropertyMessageList
{
    // include url of current page
    public static $URL = '/?menuaction=property.uitts.index';

    public static $priorityCSSClass = [
        '.priority1',
        '.priority2',
        '.priority3'
    ];

     /**
     * @var AcceptanceTester
     */
    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function selectRow($rowCSSSelector)
    {
        $I = $this->tester;
        $I->click($rowCSSSelector);
        return $this;
    }

    public function deSelectRow($rowCSSSelector)
    {
        $I = $this->tester;
        $I->click($rowCSSSelector);
        return $this;
    }

    public function moveCursor() {
        $I = $this->tester;
        $I->click('#top');
    }
}
