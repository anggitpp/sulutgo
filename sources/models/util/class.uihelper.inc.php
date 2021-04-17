<?php

/*
*  Build on pojay.dev @42A
*/

/**
 * Description of class
 *
 * @author mazte
 */
class UIHelper
{

  /**
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createInput($val, $id, $name, $class = "", $type = "text")
  {
    return '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />';
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $attr <strong>Attribute<strong> default is field
   * @param string $labelClass <strong>Label Class<strong><p>default class is l-input-small</p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */

  function createField($label, $id, $value, $required = "", $fieldTable = "", $attr = "", $addEvent = "", $maxlength = "", $readonly = "", $datePicker = "", $sideAttr ="")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . $value . '" class="mediuminput ' . (!empty($datePicker) ? 'hasDatePicker' : '') . '"" ' . $attr . ' maxlength="' . $maxlength . '" ' . (!empty($readonly) ? 'readonly' : '') . ' /> '.$sideAttr.'
      </div>';
    return $ret;
  }

  function createFieldWithoutLabel($id, $value, $attr = "", $addEvent = "", $maxlength = "", $readonly = "")
  {
    $ret = '                    
      <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . $value . '" class="mediuminput" ' . $attr . ' maxlength="' . $maxlength . '" ' . (!empty($readonly) ? 'readonly' : '') . ' /> ';
    return $ret;
  }

  function createTimePicker($label, $id, $value, $required = "", $fieldTable = "", $addEvent = "")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="inp[' . $id . ']" ' . $addEvent . ' value="' . $value . '" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" readonly="readonly" size="10" maxlength="5" /> 
      </div>';
    return $ret;
  }

  function createDatePicker($label, $id, $value, $required = "", $fieldTable = "", $addEvent = "", $sideAttr ="")
  {
    $ret = '         
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*</span></label>' : '</label>') . '
      <div class="field">           
          <input type="text" id="' . $id . '" name="' . $id . '" ' . $addEvent . ' value="' . getTanggal($value) . '" class="mediuminput hasDatePicker" /> '.$sideAttr.'
      </div>';
    return $ret;
  }

  function createComboData($label, $sql, $key, $value, $id, $selectedValue, $addEvent = "", $width = "", $classChosen = "", $required = "", $fieldTable = "", $all = " ")
  {
    $ret = '';
    $ret .= '
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*</span></label>' : '</label>') . '           
    <div class="field">
        ' . comboData($sql, $key, $value, $id, $all, $selectedValue, $addEvent, (!empty($width) ? $width : '250px'), (!empty($classChosen) ? 'chosen-select' : '')) . '
    </div>';

    return $ret;
  }

  function createRadio($label, $name, $arrayValue, $selectedValue, $fieldTable = "", $addEvent = "", $required = "")
  {
    $ret = '';
    $ret .= '
      <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '">' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">';
    $count = 0;
    foreach ($arrayValue as $key => $value) {
      if ($count == 0 && $selectedValue == "") {
        $tchecked = "checked=\"checked\"";
      } else {
        $tchecked = ($key == $selectedValue ? " checked = \"checked\"" : "");
      }
      $ret .= "<input type='radio' id='".$name.$key."' name='$name' $addEvent value='$key' $tchecked /> <span class='sradio'>$value</span>";
      $count++;
    }
    $ret .= "
      </div>";

    return $ret;
  }

  function createSingleCheckBox($label, $id, $value, $selectedValue, $text ="", $fieldTable ="", $addEvent ="", $required =""){
    $ret = '';
    $checked = ($selectedValue == $value ? " checked = \"checked\"" : "");
    $ret .='
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '">' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '</label>') . '
      <div class="field">
      <input type="checkbox" id="'.$id.'" name="'.$id.'" value="'.$value.'" '.$checked.' '.$addEvent.' /> '.$text.'
    </div>';

    return $ret;
  }
  

  function createTextArea($label, $id, $value, $attr = "", $fieldTable = "", $required = "", $addEvent = "")
  {
    $ret = '';
    $ret .= '
  <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . ' ' . (!empty($required) ? '<span class="required">*)</span></label>' : '') . '
  <div class="field">
      <textarea name="' . $id . '" id="' . $id . '" class="longinput" ' . (empty($attr) ? 'style="width:250px;"' : $attr) . ' ' . $addEvent . '>' . $value . '</textarea>
  </div>';

    return $ret;
  }

  function createFile($label, $id, $value, $attr = "", $fieldTable = "", $target, $idTarget, $delFunction)
  {
    global $par;
    $ret = '';
    $ret .= '  
    <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '" >' . $label . '</label>
    <div class="field">';
    empty($value) ?
      $ret .= '
    <input type="text" id="fileTemp" name="fileTemp" class="input" ' . (empty($attr) ? 'style="width:180px;"' : $attr) . '/>
    <div class="fakeupload" ' . (empty($attr) ? 'style="width:250px;"' : $attr) . '>
      <input type="file" id="' . $id . '" name="' . $id . '" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" />
    </div>
    ' : $ret .= '
    <a href="download.php?d=' . $target . '&f=' . $idTarget . '"><img src="' . getIcon($value) . '" align="left"></a>
    <input type="file" id="' . $id . '" name="' . $id . '" style="display:none;" />
    <a href="?par[mode]=' . $delFunction . getPar($par, "mode") . '" onclick="return confirm(\'anda yakin akan menghapus file ini?\')" class="action delete"><span>Delete</span></a>
    <br clear="all"/>';
    $ret .= '
    </div>';

    return $ret;
  }

  function createSpan($label, $value, $id = "", $fieldTable ="")
  {
    $ret = '
  <label class="l-input-small' . (!empty($fieldTable) ? '2' : '') . '">' . $label . '</label>
  <span class="field" id="' . $id . '">' . $value . '&nbsp;</span>';

    return $ret;
  }

  function createDashboardBox($label, $color = "", $value, $attr = "", $class = "")
  {
    $arrColor = array("dark-orchid", "camarone", "goldenrod", "citrus", "caper", "moon-raker", "allports", "chocolate");
    $randColor = array_rand($arrColor);
    $color = empty($color) ? $arrColor[$randColor] : $color;
    $class = empty($class) ? "box" : $class;
    $ret = '
  <div class="' . $class . ' ' . $color . '" ' . $attr . '>
      <div class="' . $class . '-header">
          <p class="' . $class . '-title">' . $label . '</p>
      </div>
      <div class="' . $class . '-content">
          <p class="' . $class . '-number">' . $value . '</p>
      </div>
  </div>';

    return $ret;
  }
  
  function createLabelSpanInputAttr($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small", $type = "text", $spanClass = "fieldB") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }
  function createLabelSpanInputAttrRead($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small", $type = "text", $spanClass = "fieldB") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input readonly type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }
  function createLabelSpanInputAttrWithJava($label, $val, $id, $name, $class = "mediuminput", $attr = "",  $labelClass = "l-input-small", $type = "text", $spanClass = "fieldB") {
    // $java = "onkeyup=\"cekAngka()\"";
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' onkeyup="nospaces(this);" />
            </span>';
    return $ret;
  }
   function createLabelSpanInputAttrK($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small5", $type = "text", $spanClass = "fieldB") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }
    function createLabelSpanInputAttrD($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small5", $type = "text", $spanClass = "field",$spanStyle = "border-bottom:0px") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '" style="'. $spanStyle .'">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }
      function createLabelSpanInputAttrZ($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small", $type = "text", $spanClass = "field",$spanStyle = "border-bottom:0px") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '" style="'. $spanStyle .'">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }


   function createLabelSpanInputAttrB($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small", $type = "text", $spanClass = "field") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }
function createLabelSpanInputAttrE($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small7", $type = "text", $spanClass = "fieldE") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }function createLabelSpanInputAttrF($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small6", $type = "text", $spanClass = "fieldE") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }

function createLabelSpanInputAttrA($label, $val, $id, $name, $class = "mediuminput", $attr = "", $labelClass = "l-input-small6", $type = "text", $spanClass = "field") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }

  function createLabelSpanInput($label, $val, $id, $name, $class = "", $type = "text", $spanClass = "fieldB") {
    $ret = '<label class="l-input-small">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />
            </span>';
    return $ret;
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $attr <strong>Attribute of Input<strong>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @param string $labelClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelClassSpanInput($label, $val, $id, $name, $class = "", $attr = "", $type = "text", $spanClass = "fieldB", $labelClass = "l-input-small") {
    $ret = '<label class="' . $labelClass . '">' . $label . '</label>
            <span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" ' . $attr . ' />
            </span>';
    return $ret;
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is hidden<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelSpanInputDisplay($label, $val, $id, $name, $class = "", $type = "hidden", $spanClass = "fieldB") {
    $ret = '<label class="l-input-small">' . $label . '</label>
            <span class="' . $spanClass . '">' . ( $val == "" ? '&nbsp;' : $val) . '
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />
            </span>';
    return $ret;
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $desc <strong>Text To Display instead of code</strong>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is hidden<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelSpanInputDisplayDescription($label, $desc, $val, $id, $name, $class = "", $type = "hidden", $spanClass = "fieldB") {
    $ret = '<label class="l-input-small">' . $label . '</label>
            <span class="' . $spanClass . '">' . ( $desc == "" ? '&nbsp;' : $desc) . '
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />
            </span>';
    return $ret;
  }

  /**
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createSpanInput($val, $id, $name, $class = "", $type = "text", $spanClass = "fieldB") {
    $ret = '<span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" class="' . $class . '" value="' . $val . '" />
            </span>';
    return $ret;
  }

  /**
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createSpanInputAttr($val, $id, $name, $attr = "", $class = "", $type = "text", $spanClass = "fieldB") {
    $ret = '<span class="' . $spanClass . '">
            <input type="' . $type . '" id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '" value="' . $val . '" />
            </span>';
    return $ret;
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $attr <strong>Additional Attributes</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelSpanTextArea($label, $val, $id, $name, $attr = "", $class = "", $spanClass = "fieldB") {
    $ret = '<label class="l-input-small">' . $label . '</label>
            <span class="' . $spanClass . '">
            <textarea id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '">' . $val . '</textarea>
            </span>';
    return $ret;
  }

    function createLabelSpanTextAreaB($label, $val, $id, $name, $attr = "", $class = "", $spanClass = "fieldB") {
    $ret = '<label class="l-input-medium" style="text-align: left; padding-left: 20px;width:41%">' . $label . '</label>
            <span class="' . $spanClass . '">
            <textarea id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '">' . $val . '</textarea>
            </span>';
    return $ret;
  }

  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $attr <strong>Additional Attributes</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $labelClass <strong>Class of Label<strong> default is l-input-small
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelClassSpanTextArea($label, $val, $id, $name, $attr = "", $class = "", $labelClass = "l-input-small", $spanClass = "fieldB", $labelStyle="") {
	  
    $ret = '<label class="' . $labelClass . '" style="'.$labelStyle.'">' . $label . '</label>
            <span class="' . $spanClass . '">
            <textarea id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '">' . $val . '</textarea>
            </span>';
    return $ret;
  }

 function createLabelClassSpanTextAreaB($label, $val, $id, $name, $attr = "", $class = "", $labelClass = "l-input-small", $spanClass = "fieldB", $labelStyle="width:79%;margin-bottom:5px") {
    
    $ret = '<label class="' . $labelClass . '" style="'.$labelStyle.'">' . $label . '</label>
            <span class="' . $spanClass . '">
            <textarea id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '">' . $val . '</textarea>
            </span>';
    return $ret;
  }
  /**
   * @param string $label <strong>Label Text<strong><p>default class is required</p>
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $customInput <strong>Custom input e.g: Generated Select or else</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $spanClass <strong>Class of SPAN<strong> default is field
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createLabelSpanCustom($label, $customInput = "", $class = "", $spanClass = "fieldB") {
    $ret = '<label class="l-input-small">' . $label . '</label>
            <span class="' . $spanClass . '">' . $customInput . '
            </span>';
    return $ret;
  }

  function createSpanCustom($label, $customInput = "", $class = "", $spanClass = "fieldB") {
    $ret = '<span  class="' . $spanClass . '">&nbsp;&nbsp;&nbsp;' . $label . '&nbsp;&nbsp;&nbsp;</span><span class="' . $spanClass . '">' . $customInput . '
            </span>';
    return $ret;
  }

  /**
   * @param string $val <strong>Value of input</strong>
   * @param string $id <strong>ID of input</strong>
   * @param string $name <strong>Name of input</strong>
   * @param string $class <strong>Class of input</strong><p>you can define class in multiple class, like <strong>"mnum mnumx mnumy"</strong></p>
   * @param string $type <strong>Type of Input<strong>default is text<p>"hidden" "text"</p>
   * @author Mazte(ekobudy79@gmail.com)
   * 
   */
  function createInputOnly($val, $id, $name, $attr = "", $class = "", $type = "text") {
    $ret = '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" ' . $attr . ' class="' . $class . '" value="' . $val . '" />';
    return $ret;
  }

  /**
   * @param type $labelText <strong>Text</strong>
   * @param type $val <strong>Value</strong>
   * @param type $labelClass <strong>Class of Label</strong><p>Default is l-input-small</p>
   * @param type $spanClass <strong>Class of Span</strong><p>Default is field</p>
   * @return type
   */
  function createPLabelSpanDisplay($labelText, $val = "", $labelClass = "l-input-small", $spanClass = "field") {
    $ret = "<p><label class=\"" . ($labelClass == "" ? "l-input-small" : $labelClass) . "\">$labelText</label><span class=\"" . ($spanClass == "" ? "field" : $spanClass) . "\">$val&nbsp;</span></p>";
    return $ret;
  }
   function createPLabelSpanDisplayB($labelText, $val = "", $labelClass = "l-input-small", $spanClass = "field" ,$spanStyle = "margin-left:40%") {
    $ret = "<p><label class=\"" . ($labelClass == "" ? "l-input-small" : $labelClass) . "\">$labelText</label><span class=\"" . ($spanClass == "" ? "field" : $spanClass) . "\" style=\"".$spanStyle."\">$val&nbsp;</span></p>";
    return $ret;
  }

  function createComboYear($nama, $sel, $range = "", $java = "", $width = "", $all = "", $awal = "", $akhir = "") {
    $style = $width == "" ? "" : "style=\"width:$width%\"";
    $text = "<select id='$nama' name='$nama' $java $style>";
    $range = $range == "" ? 5 : $range;
    if (empty($awal))
      $awal = date('Y') - $range;
    if (empty($akhir))
      $akhir = date('Y') + $range;
    if (!empty($all))
      $text.=empty($sel) ? "<option value='' selected>All</option>" : "<option value=''>All</option>";
    for ($nilai = $awal; $nilai <= $akhir; $nilai++) {
      if ($nilai == $sel) {
        $text.="<option value='$nilai' selected>$nilai</option>";
      } else {
        $text.="<option value='$nilai'>$nilai</option>";
      }
    }
    $text.="</select>";
    return $text;
  }

  function createComboMonth($nama, $sel, $java = "", $width = "", $all = "") {
    $style = $width == "" ? "" : "style=\"width:$width%\"";
    $text = "<select id='$nama' name='$nama' $java $style>";
    if (!empty($all))
      $text.=empty($sel) ? "<option value='' selected>All</option>" : "<option value=''>All</option>";
    for ($nilai = 1; $nilai <= 12; $nilai++) {
      $bulan = str_pad($nilai, 2, "0", STR_PAD_LEFT);
      if ($nilai == $sel) {
        $text.="<option value='$bulan' selected>" . getBulan($bulan) . "</option>";
      } else {
        $text.="<option value='$bulan'>" . getBulan($bulan) . "</option>";
      }
    }
    $text.="</select>";
    return $text;
  }
}
