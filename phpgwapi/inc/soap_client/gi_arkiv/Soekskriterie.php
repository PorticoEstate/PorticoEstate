<?php

class Soekskriterie
{

    /**
     * @var SoekeOperatorEnum $operator
     */
    protected $operator = null;

    /**
     * @var Kriterie $Kriterie
     */
    protected $Kriterie = null;

    /**
     * @param SoekeOperatorEnum $operator
     * @param Kriterie $Kriterie
     */
    public function __construct($operator, $Kriterie)
    {
      $this->operator = $operator;
      $this->Kriterie = $Kriterie;
    }

    /**
     * @return SoekeOperatorEnum
     */
    public function getOperator()
    {
      return $this->operator;
    }

    /**
     * @param SoekeOperatorEnum $operator
     * @return Soekskriterie
     */
    public function setOperator($operator)
    {
      $this->operator = $operator;
      return $this;
    }

    /**
     * @return Kriterie
     */
    public function getKriterie()
    {
      return $this->Kriterie;
    }

    /**
     * @param Kriterie $Kriterie
     * @return Soekskriterie
     */
    public function setKriterie($Kriterie)
    {
      $this->Kriterie = $Kriterie;
      return $this;
    }

}
