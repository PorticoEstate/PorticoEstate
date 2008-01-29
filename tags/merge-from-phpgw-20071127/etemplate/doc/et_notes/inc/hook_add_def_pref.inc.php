<?php
 /**********************************************************************\
 * phpGroupWare - eTemplate						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Bettina Gille - <ceb at phpgroupware.org>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id: hook_add_def_pref.inc.php 17936 2007-02-10 16:03:46Z sigurdne $ */

	global $pref;
	$pref->change('notes','notes_font','Verdana,Arial,Helvetica,sans-serif');
	$pref->change('notes','notes_font_size','3');
?>
