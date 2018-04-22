<?php

function check_roles($roles){
  while(strpos($roles, "**")!==false){
    $roles = str_replace("**", "*", $roles);
  }
  $rarr = explode(",", $roles);
  $cnt = count($rarr);
  $darr = array();
  for ($i=0; $i<$cnt; $i++){
    $t = trim($rarr[$i]);
    if ($t=="*" || strpos($t, "**")!==false) return 1;
    if ($t!=""){
      $darr[] = str_replace('*', '%', $t);
    }
  }
  $cnt = count($darr);
  if ($cnt==0) return -1;
  return $darr;
}

function build_role_condition($roles, $src_field='cnum', $dst_field='dst'){
  $cnt = count($roles);
  $wheres = array();
  for($i=0; $i<$cnt; $i++){
    $wheres[] = sprintf("(%s like '%s')", $src_field, $roles[$i]);
    $wheres[] = sprintf("(%s like '%s')", $dst_field, $roles[$i]);
    $wheres[] = sprintf("(channel like 'SIP/%s-%%')", $roles[$i]);
  }
  return "(".implode(" OR ", $wheres).")";
}  

?>