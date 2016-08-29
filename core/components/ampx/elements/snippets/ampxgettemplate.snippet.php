<?php
$id = $modx->getOption('id',$scriptProperties,0);
$field = $modx->getOption('field',$scriptProperties,'templatename');
$value = '';
if ($template = $modx->getObject('modTemplate',$id)){
    $value = $template->get($field);    
}
return $value;