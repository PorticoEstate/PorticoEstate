<?php

/**
 * mime.class
 *
 * Copyright (c) 2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions needed to handle mime messages.
 *
 * $Id: class.mime.inc.php,v 1.1.1.1 2005/08/23 05:03:53 skwashd Exp $
 * @package squirrelmail
 */

/** Load in the entire MIME system */
require_once(PHPGW_APP_INC . '/mime/Rfc822Header.class.php');
require_once(PHPGW_APP_INC . '/mime/MessageHeader.class.php');
require_once(PHPGW_APP_INC . '/mime/AddressStructure.class.php');
require_once(PHPGW_APP_INC . '/mime/Message.class.php');
require_once(PHPGW_APP_INC . '/mime/SMimeMessage.class.php');
require_once(PHPGW_APP_INC . '/mime/Disposition.class.php');
require_once(PHPGW_APP_INC . '/mime/Language.class.php');
require_once(PHPGW_APP_INC . '/mime/ContentType.class.php');
require_once(PHPGW_APP_INC . '/mime/date.php');

?>
