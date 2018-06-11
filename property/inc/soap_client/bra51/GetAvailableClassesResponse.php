<?php

class GetAvailableClassesResponse
{

    /**
     * @var ArrayOfDocumentClassPrivilege $GetAvailableClassesResult
     */
    protected $GetAvailableClassesResult = null;

    /**
     * @param ArrayOfDocumentClassPrivilege $GetAvailableClassesResult
     */
    public function __construct($GetAvailableClassesResult)
    {
      $this->GetAvailableClassesResult = $GetAvailableClassesResult;
    }

    /**
     * @return ArrayOfDocumentClassPrivilege
     */
    public function getGetAvailableClassesResult()
    {
      return $this->GetAvailableClassesResult;
    }

    /**
     * @param ArrayOfDocumentClassPrivilege $GetAvailableClassesResult
     * @return GetAvailableClassesResponse
     */
    public function setGetAvailableClassesResult($GetAvailableClassesResult)
    {
      $this->GetAvailableClassesResult = $GetAvailableClassesResult;
      return $this;
    }

}
