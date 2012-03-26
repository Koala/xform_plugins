<?php

class rex_xform_textplus extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		if ($this->getValue() == "" && !$send)
		{
			if (isset($this->elements[3])) $this->setValue($this->elements[3]);
		}

		if(isset($this->elements[5]) && $this->elements[5]!='')
		{
      $addClass = $addAttrs = '';
      $attrs = $this->elements[5].'###>>>';
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
            $addAttrs .= $attribute_key.'="'.$attribute_val.'" ';
        }
      }
    }
		
		$wc = "";
		if (isset($warning[$this->getId()]))
		{
			$wc = " ".$warning[$this->getId()];
		}

		$this->params["form_output"][$this->getId()] = '
			<p class="formtext formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
				<label class="text' . $wc . '" for="el_' . $this->getId() . '" >' . $this->elements[2] . '</label>
				<input type="text" class="text'.$addClass.$wc.'" name="'.$this->getFormFieldname().'" id="el_'.$this->getId().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" '.$addAttrs.'/>
			</p>';

		$email_elements[$this->elements[1]] = stripslashes($this->getValue());
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db")
		{
			$sql_elements[$this->elements[1]] = $this->getValue();
		}

	}

	function getDescription()
	{
    return '<strong>textplus</strong> : <em>Erweiterung der standard text Klasse um Attribute -></em><br />'.PHP_EOL
          .'<code class="xform-form-code">textplus|label|Bezeichnung|defaultwert|[no_db]|attribute1>>>value1###attribute2>>>value2</code>';
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'text',
						'values' => array(
									array( 'type' => 'name',   'label' => 'Feld' ),
									array( 'type' => 'text',    'label' => 'Bezeichnung'),
									array( 'type' => 'text',    'label' => 'Defaultwert'),
									array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 1),
									array( 'type' => 'text',    'label' => 'Attribute'),
								),
						'description' => 'Ein einfaches Textfeld als Eingabe',
						'dbtype' => 'text'
						);

	}
}

?>