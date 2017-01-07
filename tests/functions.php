<?php

if(!function_exists('jsFormat')):
function jsFormat($object, $flags = 0)
{
	return json_encode($object, $flags|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}
endif;

if(!function_exists('jsObject')):
function jsObject($object, $flags = 0)
{
	$flags = is_array($object) || is_object($object) ? $flags|JSON_FORCE_OBJECT : $flags;
	return str_replace("\n", "\n  ", jsFormat($object, $flags));
}
endif;


if(!function_exists('hr')):
function hr($style = null, $size = null, $return = false)
{
	$line = "\n".str_repeat(($style ?: '-'), ($size ?: 80));
	if($return) return $line;
	echo $line;
}
endif;
