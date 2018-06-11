<?php

class LoginResponse
{

    /**
     * @var string $LoginResult
     */
    protected $LoginResult = null;

    /**
     * @param string $LoginResult
     */
    public function __construct($LoginResult)
    {
      $this->LoginResult = $LoginResult;
    }

    /**
     * @return string
     */
    public function getLoginResult()
    {
      return $this->LoginResult;
    }

    /**
     * @param string $LoginResult
     * @return LoginResponse
     */
    public function setLoginResult($LoginResult)
    {
      $this->LoginResult = $LoginResult;
      return $this;
    }

}
