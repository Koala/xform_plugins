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
* Löscht/ersetzt HTML aus user Eingabe..
*
* $Id: class.xform.validate_cookies_enabled.inc.php 132 2011-07-01 14:11:36Z jeffe $:
*/

class rex_xform_validate_cookies_enabled extends rex_xform_validate_abstract
{

  function enterObject(&$warning, $send, &$warning_messages)
  {
    /*if($send=="1")
    {
      $replacement = $this->elements[3];

      foreach($this->obj_array as $Object)
      {
        $input = $Object->getValue();
        $sanitized = preg_replace('/<.*>.*<\/.*>/i', $replacement, $input);
        $Object->setValue($sanitized);
      }
    }*/

    setcookie('test', 1, time()+3600);
    /*if(!isset($_GET['cookies']))
    {
      $warning[] = $this->elements[1];
    }*/
    if(count($_COOKIE) == 0)
    {
      $send = 1;
      $warning['el_'.$this->getId()] = $this->params['error_class'];
      $warning_messages[] = $this->elements[2];
    }
  }

  function getDescription()
  {
    return '<strong>strip_html</strong> : <em>entfernt HTML aus Formulareingabe (ersetzt optional gegen vordefinierten Text) -></em><br />'.PHP_EOL
    .'<code class="xform-form-code">validate|strip_html|label|replace_string</code> (optional)';
  }

}
