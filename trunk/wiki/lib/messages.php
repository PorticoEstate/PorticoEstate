<?php
// $Id$

// Error messages.
$ErrorSuffix          = '<br /><br />Please contact the ' .
                        '<a href="mailto:' . $Admin . '">administrator</a> ' .
                        'for assistance.';
$ErrorDatabaseConnect = 'Error connecting to database.' . $ErrorSuffix;
$ErrorDatabaseSelect  = 'Error selecting database.' . $ErrorSuffix;
$ErrorDatabaseQuery   = 'Error executing database query.' . $ErrorSuffix;
$ErrorCreatingTemp    = 'Error creating temporary file.' . $ErrorSuffix;
$ErrorWritingTemp     = 'Error writing to temporary file.' . $ErrorSuffix;
$ErrorDeniedAccess    = 'You have been denied access to this site.' .
                        $ErrorSuffix;
$ErrorRateExceeded    = 'You have exeeded the number of pages you are ' .
                        'allowed to visit in a given period of time.  Please ' .
                        'return later.' . $ErrorSuffix;
$ErrorNameMatch       = 'You have entered an invalid user name.' . $ErrorSuffix;
$ErrorInvalidPage     = 'Invalid page name.' . $ErrorSuffix;
$ErrorAdminDisabled   = 'Administration features are disabled for this wiki.' .
                        $ErrorSuffix;
$ErrorPageLocked      = 'The page you have tried to edit is locked.' .
                        $ErrorSuffix;
?>
