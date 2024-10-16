<?php

/**
 * create a alert for any validation
 * @param $msg
 * @param $type
 */

function createAlert($msg, $type = "danger")
{
	return "<p class=\"alert alert-{$type} d-flex justify-content-between\">{$msg} <button class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></p>";
}

/**
 * Get old value
 * @param $field_name
 */
function oldValue($field_name, $default = '')
{
	return $_POST[$field_name] ?? $default;
}

/**
 * reset form
 * @param $field_name
 */
function resetForm()
{
	return $_POST = [];
}
