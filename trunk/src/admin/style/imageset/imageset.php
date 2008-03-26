<?php
/**
* qGuestbook
*
* An advanced Guestbook written in PHP5.
*
* PHP version 5
*
* LICENSE:
* Copyright (C) 2007-2008 Simon Lauger
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category   Guestbook
* @package    qGuestbook
* @subpackage template
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK'))
{
	die('Hacking');
	exit;
}

if (!isset($template_images) || !is_array($template_images))
{
	$template_images = array();
}

$imageset = array_merge($template_images, array(
	'IMAGE_ENABLE_POST' => '%1s/enable.gif',
	'IMAGE_DISABLE_POST' => '%1s/disable.gif',
	'IMAGE_COMMENT_POST' => '%1s/comment.gif',
	'IMAGE_EDIT_POST' => '%1s/edit.gif',
	'IMAGE_DELETE_POST' => '%1s/delete.gif',
));

// Wir bleiben kompatibel ;D
$template_images = $imageset;

?>