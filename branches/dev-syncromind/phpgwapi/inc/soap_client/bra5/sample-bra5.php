<?php
/**
 * Test with Bra5 for '/home/hc483/wsdl/braarkiv_51/services.asmx.wsdl'
 * @package Bra5
 * @date 2015-03-06
 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/Bra5Autoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 *
 * This is an associative array as:
 * - the key must be a Bra5WsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 *
 * Here is below an example of how you can set the array:
 * $wsdl = array();
 * $wsdl[Bra5WsdlClass::WSDL_URL] = '/home/hc483/wsdl/braarkiv_51/services.asmx.wsdl';
 * $wsdl[Bra5WsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[Bra5WsdlClass::WSDL_TRACE] = true;
 * $wsdl[Bra5WsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[Bra5WsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as:
 * - $wsdlObject = new Bra5WsdlClass($wsdl);
 */
/**
 * Examples
 */


/******************************
 * Example for Bra5ServiceLogin
 */
$bra5ServiceLogin = new Bra5ServiceLogin();
// sample call for Bra5ServiceLogin::Login()
if($bra5ServiceLogin->Login(new Bra5StructLogin(/*** update parameters list ***/)))
    print_r($bra5ServiceLogin->getResult());
else
    print_r($bra5ServiceLogin->getLastError());

/*******************************
 * Example for Bra5ServiceLogout
 */
$bra5ServiceLogout = new Bra5ServiceLogout();
// sample call for Bra5ServiceLogout::Logout()
if($bra5ServiceLogout->Logout(new Bra5StructLogout(/*** update parameters list ***/)))
    print_r($bra5ServiceLogout->getResult());
else
    print_r($bra5ServiceLogout->getLastError());

/****************************
 * Example for Bra5ServiceGet
 */
$bra5ServiceGet = new Bra5ServiceGet();
// sample call for Bra5ServiceGet::getProductionLines()
if($bra5ServiceGet->getProductionLines(new Bra5StructGetProductionLines(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getDocumentSplitTypes()
if($bra5ServiceGet->getDocumentSplitTypes(new Bra5StructGetDocumentSplitTypes(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::GetAvailableClasses()
if($bra5ServiceGet->GetAvailableClasses(new Bra5StructGetAvailableClasses(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getRelativeFileURL()
if($bra5ServiceGet->getRelativeFileURL(new Bra5StructGetRelativeFileURL(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getAvailableAttributes()
if($bra5ServiceGet->getAvailableAttributes(new Bra5StructGetAvailableAttributes(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getLookupValues()
if($bra5ServiceGet->getLookupValues(new Bra5StructGetLookupValues(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getDocument()
if($bra5ServiceGet->getDocument(new Bra5StructGetDocument(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getAttribute()
if($bra5ServiceGet->getAttribute(new Bra5StructGetAttribute(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getFileName()
if($bra5ServiceGet->getFileName(new Bra5StructGetFileName(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::getFileAsByteArray()
if($bra5ServiceGet->getFileAsByteArray(new Bra5StructGetFileAsByteArray(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());
// sample call for Bra5ServiceGet::GetArchiveName()
if($bra5ServiceGet->GetArchiveName(new Bra5StructGetArchiveName(/*** update parameters list ***/)))
    print_r($bra5ServiceGet->getResult());
else
    print_r($bra5ServiceGet->getLastError());

/*******************************
 * Example for Bra5ServiceSearch
 */
$bra5ServiceSearch = new Bra5ServiceSearch();
// sample call for Bra5ServiceSearch::searchDocument()
if($bra5ServiceSearch->searchDocument(new Bra5StructSearchDocument(/*** update parameters list ***/)))
    print_r($bra5ServiceSearch->getResult());
else
    print_r($bra5ServiceSearch->getLastError());
// sample call for Bra5ServiceSearch::searchAndGetDocuments()
if($bra5ServiceSearch->searchAndGetDocuments(new Bra5StructSearchAndGetDocuments(/*** update parameters list ***/)))
    print_r($bra5ServiceSearch->getResult());
else
    print_r($bra5ServiceSearch->getLastError());

/*******************************
 * Example for Bra5ServiceCreate
 */
$bra5ServiceCreate = new Bra5ServiceCreate();
// sample call for Bra5ServiceCreate::createDocument()
if($bra5ServiceCreate->createDocument(new Bra5StructCreateDocument(/*** update parameters list ***/)))
    print_r($bra5ServiceCreate->getResult());
else
    print_r($bra5ServiceCreate->getLastError());

/*******************************
 * Example for Bra5ServiceUpdate
 */
$bra5ServiceUpdate = new Bra5ServiceUpdate();
// sample call for Bra5ServiceUpdate::updateDocument()
if($bra5ServiceUpdate->updateDocument(new Bra5StructUpdateDocument(/*** update parameters list ***/)))
    print_r($bra5ServiceUpdate->getResult());
else
    print_r($bra5ServiceUpdate->getLastError());
// sample call for Bra5ServiceUpdate::updateAttribute()
if($bra5ServiceUpdate->updateAttribute(new Bra5StructUpdateAttribute(/*** update parameters list ***/)))
    print_r($bra5ServiceUpdate->getResult());
else
    print_r($bra5ServiceUpdate->getLastError());

/*******************************
 * Example for Bra5ServiceDelete
 */
$bra5ServiceDelete = new Bra5ServiceDelete();
// sample call for Bra5ServiceDelete::deleteDocument()
if($bra5ServiceDelete->deleteDocument(new Bra5StructDeleteDocument(/*** update parameters list ***/)))
    print_r($bra5ServiceDelete->getResult());
else
    print_r($bra5ServiceDelete->getLastError());

/****************************
 * Example for Bra5ServicePut
 */
$bra5ServicePut = new Bra5ServicePut();
// sample call for Bra5ServicePut::putFileAsByteArray()
if($bra5ServicePut->putFileAsByteArray(new Bra5StructPutFileAsByteArray(/*** update parameters list ***/)))
    print_r($bra5ServicePut->getResult());
else
    print_r($bra5ServicePut->getLastError());

/*****************************
 * Example for Bra5ServiceFile
 */
$bra5ServiceFile = new Bra5ServiceFile();
// sample call for Bra5ServiceFile::fileTransferSendChunkedInit()
if($bra5ServiceFile->fileTransferSendChunkedInit(new Bra5StructFileTransferSendChunkedInit(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());
// sample call for Bra5ServiceFile::fileTransferSendChunk()
if($bra5ServiceFile->fileTransferSendChunk(new Bra5StructFileTransferSendChunk(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());
// sample call for Bra5ServiceFile::fileTransferSendChunkedEnd()
if($bra5ServiceFile->fileTransferSendChunkedEnd(new Bra5StructFileTransferSendChunkedEnd(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());
// sample call for Bra5ServiceFile::fileTransferRequestChunkedInit()
if($bra5ServiceFile->fileTransferRequestChunkedInit(new Bra5StructFileTransferRequestChunkedInit(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());
// sample call for Bra5ServiceFile::fileTransferRequestChunk()
if($bra5ServiceFile->fileTransferRequestChunk(new Bra5StructFileTransferRequestChunk(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());
// sample call for Bra5ServiceFile::fileTransferRequestChunkedEnd()
if($bra5ServiceFile->fileTransferRequestChunkedEnd(new Bra5StructFileTransferRequestChunkedEnd(/*** update parameters list ***/)))
    print_r($bra5ServiceFile->getResult());
else
    print_r($bra5ServiceFile->getLastError());