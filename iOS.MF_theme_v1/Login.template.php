<?php
// Version: 2.0 RC4; Login

function template_login()
{
	global $context, $scripturl, $settings, $txt;

	if (!empty($context['login_errors']))
		foreach ($context['login_errors'] as $error)
			echo '<h2>', $error, '</h2>';
	if (isset($context['description']))
		echo '<h2>', $context['description'], '</h2>';

echo'<h2></h2>';		
	echo'<ul class="login" style="clear:both;">
	
	<form action="', $scripturl, '?action=login2" name="frmLogin" id="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
		
		<li>
			<div class="field">
				<div class="fieldname">'. $txt['username'] .'</div>
				<div class="fieldinfo"><input type="text" name="user" /></div>
			</div>
		</li>
		
		<li>
			<div class="field">
				<div class="fieldname">'. $txt['password'] .'</div>
				<div class="fieldinfo"><input type="password" name="passwrd" /></div>
			</div>
		</li>
		
		<li>
			<div class="last field">
				<div class="fieldname toggle">'. $txt['iRemember'] .'</div>
				<div class="fieldinfo toggle">
					<input type="checkbox" checked="checked" name="cookieneverexp" value="1" id="cookieneverexp">
				</div>
			</div>
		</li>
		
	<input type="hidden" name="hash_passwrd" value="" />
		
	</ul>
	
	<div class="child buttons" style="margin-top:10px !important;">
	
	<button type="submit">'. $txt['iSubmit'] .'</button>
	
	</div>

';
}

function template_kick_guest(){
global $txt;
echo '<h2>', empty($context['kick_message']) ? $txt['only_members_can_access'] : $context['kick_message'],'</h2>';
template_login();
}

?>