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

class rex_xform_getslice extends rex_xform_abstract
{

  function enterObject()
  {
    $slice_id = $this->getElement(1);
    $slice    = OOArticleSlice::getArticleSliceById($slice_id);
    if(is_object($slice)) {
      $content = $slice->getSlice();
    } else {
      $content = 'Slice Id['.$slice_id.'] not found.';
    }
    $form_output[] = '<div class="formtext getslice">'.$content.'</div>';
  }


  function getDescription()
  {
    return '<strong>getslice</strong> : <em>Gibt den Inhalt eines Slices aus -></em><br />'.PHP_EOL
    .'<code class="xform-form-code">getslice|[SLICE_ID]</code>'
    ;
  }
}
