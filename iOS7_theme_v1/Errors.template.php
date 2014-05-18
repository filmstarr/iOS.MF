<?php
// Version: 2.0 RC4; Errors

// !!!
/*	This template file contains only the sub template fatal_error. It is
	shown when an error occurs, and should show at least a back button and
	$context['error_message'].
*/

// Show an error message.....
function template_fatal_error()
{
	global $context, $settings, $options, $txt;

	
	echo '<h2>', $context['error_title'], '</h2>';
	
	echo '<h4 style="margin-left:16px;margin-right:16px;">', $context['error_message'], '</h3>';

}

?>