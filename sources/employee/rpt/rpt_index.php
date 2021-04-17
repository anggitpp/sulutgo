<?php

global $db,$rptMode;
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($rptMode) {
  case "rptemploc":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_loc.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempjab":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_jab.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempdep":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_dep.php';
    else
      echo "<script>logout();</script>";
    break;
    case "rptempkat":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_kat.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempedu":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_edu.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempage":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_age.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempcat":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_cat.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempcontractexpiry":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_contract_expiring.php';
    else
      echo "<script>logout();</script>";
    break;
    case "rptempstatemp":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_stat.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempcontractexpired":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_contract_expired.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptempstat":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_status.php';
    else
      echo "<script>logout();</script>";
    break;
  case "rptemppurna":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_purna.php';
    else
      echo "<script>logout();</script>";
    break;
    case "rptemphiskat":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_history_kat.php';
    else
      echo "<script>logout();</script>";
    break;
    case "rptempall":
    if (isset($menuAccess[$s]["view"]))
      include 'rpt_emp_all.php';
    else
      echo "<script>logout();</script>";
    break;
}