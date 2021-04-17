<?php
global $s, $par, $menuAccess;
if (!isset($menuAccess[$s]['view']))
    echo "<script>logout();</script>";

switch ($par[mode]) {
    default:
        include "passing_grade_view.php";
        break;
}
?>