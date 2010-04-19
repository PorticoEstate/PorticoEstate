<?php

/**
 * Language.class.php
 *
 * Copyright (c) 2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions needed to handle mime messages.
 *
 * $Id: Language.class.php,v 1.1.1.1 2005/08/23 05:04:03 skwashd Exp $
 * @package squirrelmail
 */

/**
 * Undocumented class
 * @package squirrelmail
 */
class Language {
    function Language($name) {
       $this->name = $name;
       $this->properties = array();
    }
}

?>
