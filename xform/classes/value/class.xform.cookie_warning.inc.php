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
*/

class rex_xform_cookie_warning extends rex_xform_abstract
{

  function enterObject()
  {
    setcookie('test', 1, time()+10);
    if(/*!isset($_GET['cookies']) ||*/ count($_COOKIE) == 0)
    {
      $this->params["form_output"][$this->getId()] = '
        <div class="formtext formlabel-'.$this->getName().' cookie-warning" id="'.$this->getHTMLId().'">
          <h3>'.$this->getElement(1).'</h3>
        </div>';
    }
  }

  function getDescription()
  {
    return
    '<strong>cookie_warning</strong> : Gibt eine Cookie Warnung aus falls deaktiviert..<br />'.PHP_EOL
   .'<code class="xform-form-code">cookie_warning|Please enable cookies..</code><br />'.PHP_EOL
      ;
  }
}
