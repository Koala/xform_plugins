<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
*
*
* $Id: redmine_feeds.inc.php 84 2011-02-16 04:01:36Z jeffe $:
*/

global $REX;

// IDENTIFIER, ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself = 'xform_plugins';
$myroot = $REX['INCLUDE_PATH'].'/addons/xform/plugins/'.$myself;

$rc = new redmine_connect($REX['ADDON'][$myself]['redmine_url'],$REX['ADDON'][$myself]['redmine_key']);
echo $rc->getList(rex_request('chapter', 'string'));
