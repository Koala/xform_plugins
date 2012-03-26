<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
* @version Klasse: 0.1
*
* $Id: class.xform.mailfrom.inc.php 69 2011-01-19 01:43:57Z jeffe $:
*/

class rex_xform_mailfrom extends rex_xform_abstract
{

  function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
  {

    // mailto als referenz auf anderes input feld (muss vor dem mailto feld stehen!)
    if(isset($email_elements[$this->elements[1]]))
    {

      $this->params["mail_from"] = $email_elements[$this->elements[1]];
      $this->params["mail_from"] = str_replace(array("\n", "\r\n", "\r"), '', $this->params["mail_from"]);
    }else
    {
      // direkt angegebene Emailadresse
      $this->params["mail_from"] = $this->elements[1];
    }

  }

  function getDescription()
  {
    return
    '<strong>mailfrom</strong> : Bef&uuml;llt das FROM: der Email wahlweise mit fester Adresse, oder aus Formulareingabe -><br />'.PHP_EOL
   .'<code class="xform-form-code">mailfrom|email@domain.de</code><br />'.PHP_EOL
   .'<code class="xform-form-code">mailfrom|usr_email</code> <i>(Verweis auf vorhergehendes Eingabefeld)</i>'
      ;
  }
}
