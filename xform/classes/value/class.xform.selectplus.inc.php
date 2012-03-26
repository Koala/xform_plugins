<?php

class rex_xform_selectplus extends rex_xform_abstract
{

  function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
  {

    $multiple = FALSE;
    if(isset($this->elements[6]) && $this->elements[6]==1)
    $multiple = TRUE;

    $size = (int) $this->getElement(7);
    if($size < 1)
      $size = 1;

    $SEL = new rex_select();
    $SEL->setId("el_" . $this->getId());
    if($multiple)
    {
      if($size == 1)
       $size = 2;
      $SEL->setName($this->getFormFieldname()."[]");
      $SEL->setSize($size);
      $SEL->setMultiple(1);
    }else
    {
      $SEL->setName($this->getFormFieldname());
      $SEL->setSize(1);
    }

    foreach (explode(";", $this->elements[3]) as $v)
    {
      $teile = explode("=", $v);
      $wert = $teile[0];
      if (is_array($teile) && isset ($teile[1]))
      {
        $bezeichnung = $teile[1];
      }else
      {
        $bezeichnung = $teile[0];
      }
      $SEL->addOption($wert, $bezeichnung);
    }

    if(isset($this->elements[8]) && $this->elements[8]!='')
    {
      $addClass = '';
      $attrs = $this->elements[8].'###>>>';
      foreach (explode("###", $attrs) as $attrs_array)
      {
        $attribute = explode(">>>", $attrs_array);
        $attribute_key = $attribute[0];
        $attribute_val = $attribute[1];
        
        switch($attribute_key)
        {
          case '':
            break;
          case 'class':
            $addClass = ' '.trim($attribute_val);
            break;
          default:
            $SEL->setAttribute($attribute_key,$attribute_val);
        }
      }
    }

    if (!$send && $this->value=="" && isset($this->elements[5]) && $this->elements[5] != "")
    $this->value = $this->elements[5];

    if(!is_array($this->getValue()))
    {
      $this->value = explode(",",$this->getValue());
    }

    foreach($this->getValue() as $v)
    {
      $SEL->setSelected($v);
    }

    $this->value = implode(",",$this->getValue());

    $wc = '';
    if (isset($warning[$this->getId()]))
      $wc = ' '.$warning[$this->getId()];

    $SEL->setStyle(' class="select'.$wc.$addClass.'"');

    $form_output[$this->getId()] = '
      <p class="formselect formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
      <label class="select '.$wc.'" for="el_'.$this->getId().'" >'.$this->elements[2].'</label>'. 
    $SEL->get().
      '</p>';

    $email_elements[$this->elements[1]] = $this->getValue();
    if (!isset($this->elements[4]) || $this->elements[4] != "no_db")
      $sql_elements[$this->elements[1]] = $this->getValue();

  }

  function getDescription()
  {
    return '<strong>selectplus</strong> : <em>Erweiterung der standard select Klasse um Attribute -></em><br />'.PHP_EOL
          .'<code class="xform-form-code">selectplus|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert|multiple=1|height|attribute1>>>value1###attribute2>>>value2</code>';
  }

  function getDefinitions()
  {
    return array(
            'type' => 'value',
            'name' => 'select',
            'values' => array(
    array( 'type' => 'name',   'label' => 'Feld' ),
    array( 'type' => 'text',    'label' => 'Bezeichnung'),
    array( 'type' => 'text',    'label' => 'Selektdefinition',   'example' => 'Frau=w;Herr=m'),
    array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 1),
    array( 'type' => 'text',    'label' => 'Defaultwert'),
    array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
    array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
    array( 'type' => 'text',    'label' => 'Attribute'),
    ),
            'description' => 'Ein Selektfeld mit festen Definitionen',
            'dbtype' => 'text'
            );

  }

}

?>