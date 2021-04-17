<?php

function selectArray($name, $title, $datas, $index, $column, $select, $all = "", $class = "", $width = "", $java = "")
{

    $style = $width ? "width: $width;" : "";

    $html = "<select id='$name' class='$class' name='$name' title='$title' style='$style' $java>";

    $html .= !empty($all) ? "<option value=''>$all</option>" : "";

    foreach ($datas as $key => $data) {

        $key = $index ? $data[$index] : $key;
        $value = $column ? $data[$column] : $data;

        $selected = $key == $select ? "selected" : "";

        $html .= "<option value='$key' $selected>$value</option>";
    }

    $html .= "</select>";

    return $html;
}

function selectKey($name, $title, $datas, $select, $all = "", $class = "", $width = "", $java = "")
{

    $style = $width ? "width: $width;" : "";

    $html = "<select id='$name' class='$class' name='$name' title='$title' style='$style' $java>";

    $html .= !empty($all) ? "<option value=''>$all</option>" : "";

    foreach ($datas as $value) {

        $selected = $value == $select ? "selected" : "";

        $html .= "<option value='$value' $selected>$value</option>";
    }

    $html .= "</select>";

    return $html;
}