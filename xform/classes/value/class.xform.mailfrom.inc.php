<?php
/**
* @name    REXDEV XForm Plugins
* @link    http://rexdev.de/addons/xform-plugins.html
* @link    http://redaxo.de/180-Addondetails.html?addon_id=319
* @author  rexdev.de
* @package redaxo4
* @version Addon: 0.2
* @version Klasse: 0.1
*/

class rex_xform_mailfrom extends rex_xform_abstract
{

  function enterObject()
  {
    // mailto als referenz auf anderes input feld (muss vor dem mailto feld stehen!)
    if(isset($this->params['value_pool']['email'][$this->getElement(1)]))
    {
      $this->params['mail_from'] = str_replace(
        array('\n','\r'),
        '',
        $this->params['value_pool']['email'][$this->getElement(1)]
        );
    }
    else
    {
      // direkt angegebene Emailadresse
      $this->params['mail_from'] = $this->getElement(1);
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


  function getHelp()
  {
    return array ('type'    => 'textile',
                  'content' => '
h3. Formularbeispiel

bc.. text|usr_email|* Email|
validate|email|usr_email|Bitte geben Sie Ihre Email-Adresse an.

mailfrom|usr_email
');
  }

  



}