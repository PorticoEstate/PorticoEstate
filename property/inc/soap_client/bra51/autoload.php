<?php


 function autoload_992a0f0542384f1ee5ef51b7cf4ae6c4($class)
{
    $classes = array(
        'Services' => __DIR__ .'/Services.php',
        'Login' => __DIR__ .'/Login.php',
        'LoginResponse' => __DIR__ .'/LoginResponse.php',
        'Logout' => __DIR__ .'/Logout.php',
        'LogoutResponse' => __DIR__ .'/LogoutResponse.php',
        'getProductionLines' => __DIR__ .'/getProductionLines.php',
        'getProductionLinesResponse' => __DIR__ .'/getProductionLinesResponse.php',
        'ArrayOfProductionLine' => __DIR__ .'/ArrayOfProductionLine.php',
        'ProductionLine' => __DIR__ .'/ProductionLine.php',
        'getDocumentSplitTypes' => __DIR__ .'/getDocumentSplitTypes.php',
        'getDocumentSplitTypesResponse' => __DIR__ .'/getDocumentSplitTypesResponse.php',
        'ArrayOfDocumentSplitType' => __DIR__ .'/ArrayOfDocumentSplitType.php',
        'DocumentSplitType' => __DIR__ .'/DocumentSplitType.php',
        'GetAvailableClasses' => __DIR__ .'/GetAvailableClasses.php',
        'GetAvailableClassesResponse' => __DIR__ .'/GetAvailableClassesResponse.php',
        'ArrayOfDocumentClassPrivilege' => __DIR__ .'/ArrayOfDocumentClassPrivilege.php',
        'DocumentClassPrivilege' => __DIR__ .'/DocumentClassPrivilege.php',
        'getRelativeFileURL' => __DIR__ .'/getRelativeFileURL.php',
        'getRelativeFileURLResponse' => __DIR__ .'/getRelativeFileURLResponse.php',
        'getAvailableAttributes' => __DIR__ .'/getAvailableAttributes.php',
        'getAvailableAttributesResponse' => __DIR__ .'/getAvailableAttributesResponse.php',
        'ArrayOfAttribute' => __DIR__ .'/ArrayOfAttribute.php',
        'Attribute' => __DIR__ .'/Attribute.php',
        'braArkivAttributeType' => __DIR__ .'/braArkivAttributeType.php',
        'ArrayOfAnyType' => __DIR__ .'/ArrayOfAnyType.php',
        'LookupValue' => __DIR__ .'/LookupValue.php',
        'Matrikkel' => __DIR__ .'/Matrikkel.php',
        'Pair' => __DIR__ .'/Pair.php',
        'Address' => __DIR__ .'/Address.php',
        'getLookupValues' => __DIR__ .'/getLookupValues.php',
        'ArrayOfLookupValue' => __DIR__ .'/ArrayOfLookupValue.php',
        'getLookupValuesResponse' => __DIR__ .'/getLookupValuesResponse.php',
        'searchDocument' => __DIR__ .'/searchDocument.php',
        'ArrayOfString' => __DIR__ .'/ArrayOfString.php',
        'searchDocumentResponse' => __DIR__ .'/searchDocumentResponse.php',
        'createDocument' => __DIR__ .'/createDocument.php',
        'Document' => __DIR__ .'/Document.php',
        'createDocumentResponse' => __DIR__ .'/createDocumentResponse.php',
        'getDocument' => __DIR__ .'/getDocument.php',
        'Variant' => __DIR__ .'/Variant.php',
        'ArrayOfVariant' => __DIR__ .'/ArrayOfVariant.php',
        'getDocumentResponse' => __DIR__ .'/getDocumentResponse.php',
        'ExtendedDocument' => __DIR__ .'/ExtendedDocument.php',
        'getAttribute' => __DIR__ .'/getAttribute.php',
        'getAttributeResponse' => __DIR__ .'/getAttributeResponse.php',
        'updateDocument' => __DIR__ .'/updateDocument.php',
        'updateDocumentResponse' => __DIR__ .'/updateDocumentResponse.php',
        'updateAttribute' => __DIR__ .'/updateAttribute.php',
        'updateAttributeResponse' => __DIR__ .'/updateAttributeResponse.php',
        'deleteDocument' => __DIR__ .'/deleteDocument.php',
        'deleteDocumentResponse' => __DIR__ .'/deleteDocumentResponse.php',
        'getFileName' => __DIR__ .'/getFileName.php',
        'getFileNameResponse' => __DIR__ .'/getFileNameResponse.php',
        'searchAndGetDocuments' => __DIR__ .'/searchAndGetDocuments.php',
        'ArrayOfExtendedDocument' => __DIR__ .'/ArrayOfExtendedDocument.php',
        'searchAndGetDocumentsResponse' => __DIR__ .'/searchAndGetDocumentsResponse.php',
        'putFileAsByteArray' => __DIR__ .'/putFileAsByteArray.php',
        'putFileAsByteArrayResponse' => __DIR__ .'/putFileAsByteArrayResponse.php',
        'getFileAsByteArray' => __DIR__ .'/getFileAsByteArray.php',
        'getFileAsByteArrayResponse' => __DIR__ .'/getFileAsByteArrayResponse.php',
        'fileTransferSendChunkedInit' => __DIR__ .'/fileTransferSendChunkedInit.php',
        'fileTransferSendChunkedInitResponse' => __DIR__ .'/fileTransferSendChunkedInitResponse.php',
        'fileTransferSendChunk' => __DIR__ .'/fileTransferSendChunk.php',
        'fileTransferSendChunkResponse' => __DIR__ .'/fileTransferSendChunkResponse.php',
        'fileTransferSendChunkedEnd' => __DIR__ .'/fileTransferSendChunkedEnd.php',
        'fileTransferSendChunkedEndResponse' => __DIR__ .'/fileTransferSendChunkedEndResponse.php',
        'fileTransferRequestChunkedInit' => __DIR__ .'/fileTransferRequestChunkedInit.php',
        'fileTransferRequestChunkedInitResponse' => __DIR__ .'/fileTransferRequestChunkedInitResponse.php',
        'fileTransferRequestChunk' => __DIR__ .'/fileTransferRequestChunk.php',
        'fileTransferRequestChunkResponse' => __DIR__ .'/fileTransferRequestChunkResponse.php',
        'fileTransferRequestChunkedEnd' => __DIR__ .'/fileTransferRequestChunkedEnd.php',
        'fileTransferRequestChunkedEndResponse' => __DIR__ .'/fileTransferRequestChunkedEndResponse.php',
        'GetArchiveName' => __DIR__ .'/GetArchiveName.php',
        'GetArchiveNameResponse' => __DIR__ .'/GetArchiveNameResponse.php'
    );
    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_992a0f0542384f1ee5ef51b7cf4ae6c4');

// Do nothing. The rest is just leftovers from the code generation.
{
}
