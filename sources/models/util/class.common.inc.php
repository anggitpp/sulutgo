<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class
 *
 * @author mazte
 */
class Common extends DAL {

//put your code here
  function generateSelect($customSQL, $fieldId, $fieldDesc, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $c = 0;
      foreach ($rows as $row) {
//        $selected = "";
//        if (c == 0 && empty($selValue)) {
//          $selected = "selected";
//        } else
//        if ($row[$fieldId] == $selValue) {
//          $selected = "selected";
//        }
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
        $c++;
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOption($customSQL, $fieldId, $fieldDesc, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . " class=\"chosen-select\" >";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOptionP($customSQL, $fieldId, $fieldDesc, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . " >";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }
  function generateSelectWithEmptyOptionM($customSQL, $fieldId, $fieldDesc, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . " >";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOptionAttribute($customSQL, $fieldId, $fieldDesc, $selName, $selValue = "", $addOption = "", $addEvent = "", $attributes = array()) {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected='selected' " : "");
        foreach ($attributes as $key => $value) {
          $_ret.=$key . '="' . $row[$value] . '" ';
        }
        $_ret .= ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOptionId($customSQL, $fieldId, $fieldDesc, $selId, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selId . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectChainedWithOption($customSQL, $fieldId, $fieldDesc, $fieldParent, $selName, $selValue = "", $addOption = "", $addEvent = "", $addAll = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $_ret.="<option class='' value=\"" . "\">---</option>";
	  
	  if ($addAll != "") {
		$arr = array();
		foreach ($rows as $row) {
			$arr[(htmlspecialchars($row[$fieldParent], ENT_QUOTES))] = (htmlspecialchars($row[$fieldParent], ENT_QUOTES));
		}
		
		if (is_array($arr)) {
			  asort($arr);
			  reset($arr);
			  while (list($class) = each($arr)) {
				$_ret.="<option class='".$class."' value=\"-\">--- ALL</option>";
			  }
		}
	  }
	  
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . " class='" . (htmlspecialchars($row[$fieldParent], ENT_QUOTES)) . "'" . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectChainedWithOptionId($customSQL, $fieldId, $fieldDesc, $fieldParent, $selId, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selId . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $_ret.="<option class='' value=\"" . "\">---</option>";
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . " class='" . (htmlspecialchars($row[$fieldParent], ENT_QUOTES)) . "'" . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectChainedWithOptionEmptyIsAll($customSQL, $fieldId, $fieldDesc, $fieldParent, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $_ret.="<option class='ALL' value='ALL'>ALL</option>";
      foreach ($rows as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row[$fieldId], ENT_QUOTES) . "\"" . ($row[$fieldId] == $selValue ? " selected" : "") . " class='" . (htmlspecialchars($row[$fieldParent], ENT_QUOTES)) . "'" . ">" . htmlspecialchars($row[$fieldDesc], ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateCheckBox($customSQL, $field_value, $field_string, $group_name, $selected_value = "", $add_option = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "";
      $count = 0;
      foreach ($rows as $r) {
        $_ret .= "<input type=\"checkbox\" name=\"" . $group_name . "\" value=\"" . $r[$field_value] . "\" id=\"" . $group_name . "_" . $count . "\"" . (in_array($r[$field_value], $selected_value) ? " checked=\"checked\"" : "") . "" . $add_option . " />&nbsp;<span for=\"" . $group_name . "_" . $count . "\">" . $r[$field_string] . "</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $count++;
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }

    return ($_ret == "" ? "&nbsp;-" : $_ret);
  }

  function generateRadio($customSQL, $field_value, $field_string, $group_name, $selected_value = "", $add_option = "") {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_ret = "";
      $count = 0;
      foreach ($rows as $r) {
        if ($count == 0 && $selected_value == "") {
          $tchecked = "checked=\"checked\"";
        } else {
          $tchecked = ($r[$field_value] == $selected_value ? " checked = \"checked\"" : "");
        }
        $_ret .= "<input type=\"radio\" name=\"" . $group_name . "\" value=\"" . $r[$field_value] . "\" id=\"" . $group_name . "_" . $count . "\"" . $tchecked . "" . $add_option . " />&nbsp;<span for=\"" . $group_name . "_" . $count . "\">" . $r[$field_string] . "</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $count++;
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }

    return ($_ret == "" ? "&nbsp;-" : $_ret);
  }

  function generateRadioArray($arr, $group_name, $selected_value = "", $add_option = "") {
    try {
      $count = 0;
      foreach ($arr as $id => $text) {
        if ($count == 0 && $selected_value == "") {
          $tchecked = "checked=\"checked\"";
        } else {
          $tchecked = ($id === $selected_value ? " checked = \"checked\"" : "");
        }
        $_ret .= "<input type=\"radio\" name=\"" . $group_name . "\" value=\"" . $id . "\" id=\"" . $group_name . "_" . $count . "\"" . $tchecked . "" . $add_option . " />&nbsp;<span for=\"" . $group_name . "_" . $count . "\">" . $text . "</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $count++;
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }

    return ($_ret == "" ? "&nbsp;-" : $_ret);
  }

  function generateRadioArrayFlex($arr, $group_name, $selected_value = "", $add_option = "") {
    try {
      $count = 0;
      foreach ($arr as $id => $text) {
        if ($count == 0 && $selected_value == "") {
          $tchecked = "checked=\"checked\"";
        } else {
          $tchecked = ($id == $selected_value ? " checked = \"checked\"" : "");
        }
        $_ret .= "<input type=\"radio\" name=\"" . $group_name . "\" value=\"" . $id . "\" id=\"" . $group_name . "_" . $count . "\"" . $tchecked . "" . $add_option . " />&nbsp;<span for=\"" . $group_name . "_" . $count . "\">" . $text . "</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $count++;
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }

    return ($_ret == "" ? "&nbsp;-" : $_ret);
  }

  function getDescription($customSQL, $fieldReturn) {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($rows as $row) {
        return htmlspecialchars($row[$fieldReturn], ENT_QUOTES);
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function getMstDataDesc($id) {
    $sql = "SELECT namaData from mst_data where kodeData=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":pId", $id);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($rows as $row) {
        return htmlspecialchars($row["namaData"], ENT_QUOTES);
      }
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function executeSQL($customSQL) {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $rows;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function executeSqlToArray($customSQL, $fieldReturn) {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($rows as $row) {
        $ret[] = $row[$fieldReturn];
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function execute($customSQL) {
    try {
      $stmt = $this->db->prepare($customSQL);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function generateSelectArray($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else {
        $_ret.="<option value=\"" . "\">----</option>";
      }
      foreach ($arr as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row, ENT_QUOTES) . "\"" . ($row == $selValue ? " selected" : "") . ">" . htmlspecialchars($row, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectArray2($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      } else
        $_ret.="<option value=\"" . "\">----</option>";
      foreach ($arr as $id => $text) {
//        echo "SELVAL: $selValue ; ID: $id; MARK AS " . ($id === $selValue ? " selected" : "");
//        if ($selValue == "") {
//          $selected = "selected";
//        } else {
        $selected = ($id == $selValue ? " selected" : "");
//        }
        $_ret .= "<option value=\"" . htmlspecialchars($id, ENT_QUOTES) . "\"" . $selected . ">" . htmlspecialchars($text, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectArrayNotNull($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
//      if ($addOption != "") {
//        $_ret .= $addOption;
//      }
//      $_ret.="<option value=\"" . "\">----</option>";
      foreach ($arr as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row, ENT_QUOTES) . "\"" . ($row == $selValue ? " selected" : "") . ">" . htmlspecialchars($row, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectArrayNotNull2($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
//      if ($addOption != "") {
//        $_ret .= $addOption;
//      }
//      $_ret.="<option value=\"" . "\">----</option>";
      foreach ($arr as $id => $text) {
        $_ret .= "<option value=\"" . htmlspecialchars($id, ENT_QUOTES) . "\"" . ($id == $selValue ? " selected" : "") . ">" . htmlspecialchars($text, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOptionArray($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $_ret.="<option value=\"" . "\">----</option>";
      foreach ($arr as $row) {
        $_ret .= "<option value=\"" . htmlspecialchars($row, ENT_QUOTES) . "\"" . ($row == $selValue ? " selected" : "") . ">" . htmlspecialchars($row, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  function generateSelectWithEmptyOptionArray2($arr, $selName, $selValue = "", $addOption = "", $addEvent = "") {
    try {
      $_ret = "<select name=\"" . $selName . "\" id=\"" . $selName . "\" " . $addEvent . ">";
      if ($addOption != "") {
        $_ret .= $addOption;
      }
      $_ret.="<option value=\"" . "\">----</option>";
      foreach ($arr as $id => $text) {
        $_ret .= "<option value=\"" . htmlspecialchars($id, ENT_QUOTES) . "\"" . ($id == $selValue ? " selected" : "") . ">" . htmlspecialchars($text, ENT_QUOTES) . "</option>";
      }
      $_ret .= "</select>";
      return $_ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  public function generateId($_prefix, $_len, $_id, $_tblName) {
    $start_ = strlen($_prefix);
    $len_ = $_len - $start_;
    $sql = "SELECT lpad(coalesce(max(substr(" . $_id . "," . ++$start_ . ")),0)+1," . $len_ . ",'0') gen_id FROM " . $_tblName . " WHERE substr(" . $_id . ",1," . strlen($_prefix) . ")='$_prefix'";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($rows as $row) {
        return $_prefix . $row["gen_id"];
      }
    } catch (Exception $ex) {
      return -1;
    }
  }

}
