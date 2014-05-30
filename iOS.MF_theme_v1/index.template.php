<?php
// Version: 2.0 RC4; Index

$linguaggio = $settings['theme_dir'].'/languages/iPhone.language.' . $context['user']['language'] . '.php';
if (file_exists($linguaggio))
  require($settings['theme_dir'].'/languages/iPhone.language.' . $context['user']['language'] . '.php');
else
  require($settings['theme_dir'].'/languages/iPhone.language.english.php');


global $txt;

function template_init()
{
  global $context, $settings, $options, $txt;

  $settings['theme_version'] = '1.0';
  
  // Portal disabling mafia
  // SimplePortal
  $settings['disable_sp'] = true;

  // TinyPortal
  if (function_exists('tp_hidebars'))
    tp_hidebars();

  // PortaMX
  $_SESSION['pmx_paneloff'] = array('head', 'top', 'left', 'right', 'bottom', 'foot', 'front', 'pages' => 'Pages');
}

function template_html_above()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;

  echo

'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>', $context['page_title_html_safe'], '</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
<link rel="stylesheet" media="screen" href="', $settings['theme_url'] ,'/style.css" type="text/css" />
<script type="application/x-javascript">

', (isset($_COOKIE['disablequoting'])) ? 'var aquoting = 1;

' : 'var aquoting = 0;

', 

'
var showchildboards = "', $txt['iShow'] ,' ', $txt['parent_boards'], '";
var hidechildboards = "', $txt['iHide'] ,' ', $txt['parent_boards'], '";
var quotingoff = "', $txt['iQuoting'],' ',$txt['iOff'], '";
var loading = "', $txt['iLoading'],'...";

</script>

<script type="application/x-javascript" src="'. $settings['theme_url'] .'/jquery-1.10.2.min.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/jquery.autosize.min.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/jquery.hammer.min.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/touchy.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/iphone.js"></script>',
((!empty($_GET['topic'])) && ($_GET['topic'])) ? '
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/quote.js"></script>' : '';

  echo '
</head>
<body><div id="wrapper">';
}

function iPhoneTitle(){

  global $context;

  $title = str_replace($context['forum_name_html_safe'].' - ','',$context['page_title_html_safe']);
  
  if($title=='Index')
    $title=$context['forum_name_html_safe'];
  
  $title = str_replace('View the profile of ','',$title);
  
  $title = str_replace('Set Search Parameters','Search',$title);
  
    return $title;
  
  }

function template_body_above()
{

global $txt, $_GET, $context, $modSettings, $settings, $user_info, $scripturl;

echo '

<div id="topbar">';
if((!empty($_GET['action'])) && (($_GET['action']=='login') || ($_GET['action']=='register'))) {
  $loginregister=' style="display:none;"';
  }
else
  $loginregister='';
  echo'

  <h1 id="pageTitle">';
  
  if((!empty($_GET['action']))&&($_GET['action']=='login'||$_GET['action']=='register'||$_GET['action']=='login2'||$_GET['action']=='register2'))
    echo'
    <div id="titleSwitcher">';
  else
    echo'
    <div id="theTitle">', iPhoneTitle(), '</div>';  
  
  echo'</h1>
   
  <a href="#" id="showhidesearch" onclick="if(document.getElementById(\'quickSearch\').style.display==\'block\'){document.getElementById(\'quickSearch\').style.display=\'none\';}else{document.getElementById(\'quickSearch\').style.display=\'block\';document.searchform.search.focus();}" id="tabsearch"', $issearch ,'></a>    
    <button id="showhidelogin" class="button">' , $context['user']['is_logged'] ? $txt['iLogout'] : $txt['login'] , '</button>

  </div>';

  echo '

  <div id="searchbar" class="inputContainer">

  <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="searchform">

  <input type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', !empty($context['search_string_limit']) ? ' maxlength="' . $context['search_string_limit'] . '"' : '', ' tabindex="', $context['tabindex']++, '" />
  <input type="submit" id="searchbutton" class="button inputbutton" value="'. $txt['search_button'] .'" onclick="this.style.opacity=0.3;if(document.searchform.search.value.length<3){alert(\'', $txt['iAlert'], '\');
  document.searchform.search.focus();this.style.opacity=1.0;return false;}" />
    
  </form>

  </div>';
  
  quick_login();
}

function template_body_below()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  //Subtle default mode button
$backname = $backlink = '';
if (!empty($modSettings['id_default_theme']))
  $backlink = 'index.php?theme=' . $modSettings['id_default_theme'];
else
  $backlink = 'index.php?theme='. $modSettings['theme_guests'];
$backname = 'Default Theme';
echo '<a class="button" id="classic" href="'. $backlink .'">', $backname ,'</a>';

    echo '<div id="copyright"><h4>', theme_copyright(), '</h4></div>';
  
if ($context['user']['is_logged']){

  
  $array = array('search', 'pm', 'profile');
  $ishome = '';
  $issearch = '';
  $ispm = '';
  $isprofile = '';
  $home = true;
  foreach ($array as $arr){
    if ((!empty($_GET['action'])) && (strstr($_GET['action'],$arr))){
      $var = 'is' . $arr; 
      $$var = ' class="active"';
      $home = false;
      }
    }
  if ($home)
    $ishome = ' class="active"';
      
echo '
</div>
';
}
    
    //Toolbar HTML
    echo '<style> #copyright {margin-bottom: 47px;} </style>';
      echo '<div class="toolbar">
      <ul>
        <li><div onclick="window.location.href=\'',$scripturl,'\'" style="background: url('.$settings['theme_url'].'/images/toolbar/home.png) transparent center no-repeat;"></div></li>
        <li><div onclick="window.history.back();" style="background: url('.$settings['theme_url'].'/images/toolbar/back.png) transparent center no-repeat;"></div></li>
        <li><div onclick="location.reload();" style="background: url('.$settings['theme_url'].'/images/toolbar/refresh.png) transparent center no-repeat;"></div></li>
        <li><div onclick="window.history.forward();" style="background: url('.$settings['theme_url'].'/images/toolbar/forward.png) transparent center no-repeat;"></div></li>
        <li><div onclick="window.location.href=\'?action=unread;all\'" style="background: url('.$settings['theme_url'].'/images/toolbar/inbox.png) transparent center no-repeat;"></div></li>
      </ul>
    </div>';
}

function template_html_below()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;

  echo '
</body>
</html>';
}

function theme_linktree(){

  return false;

}

function iPhoneTime($time){

  global $txt;
  
  $diff = forum_time() - $time;
  
  if($diff<60)
    return $diff . ' ' . $txt['iSecondsAgo'];
  elseif(round($diff/60)==1)
    return '1 '. $txt['iMinuteAgo'];
  elseif($diff>59 && $diff<3600)  
    return round($diff/60) . ' '. $txt['iMinutesAgo'];
  elseif(round($diff/60/60)==1)
    return '1 '. $txt['iHourAgo'];
  elseif(round($diff/60/60)>1 && round($diff/60/60)<24)
    return round($diff/60/60) . ' '. $txt['iHoursAgo'];
  elseif(round($diff/60/60/24)==1)
    return '1 '. $txt['iDayAgo'];
  elseif(round($diff/60/60/24)>1&&round($diff/60/60/24)<7)
    return round($diff/60/60/24) . ' '. $txt['iDaysAgo'];
  elseif(round($diff/60/60/24/7)==1)
    return '1 '. $txt['iWeekAgo'];
  elseif(round($diff/60/60/24/7)>1)
    return round($diff/60/60/24/7) . ' '. $txt['iWeeksAgo'];
  elseif(round($diff/60/60/24/7/4)==1)
    return '1 '. $txt['iMonthAgo'];
  elseif(round($diff/60/60/24/7/4)>1)
    return round($diff/60/60/24/7) . ' '. $txt['iMonthsAgo'];
  else return $diff;
  
}


function short1($ret)
{
  $ret = ' ' . $ret;
  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2'>$2</a>", $ret);
  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2'>$2</a>", $ret);
  short2($ret);
  $ret = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $ret);  
  $ret = substr($ret, 1);
  return($ret);
}

function short2(&$ret)
{
   
   $links = explode('<a', $ret);
   $countlinks = count($links);
   for ($i = 0; $i < $countlinks; $i++)
   {
      $link = $links[$i];
      
      
      $link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;

      $begin = strpos($link, '>') + 1;
      $end = strpos($link, '<', $begin);
      $length = $end - $begin;
      $urlname = substr($link, $begin, $length);

$chunked = (strlen(str_replace('http://','',$urlname)) > 28 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace(str_replace('http://','',$urlname), '.....', 12, -12) : $urlname;
$ret = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $ret);
   }
}

function template_button_strip()
{
  return;
}

function quick_login()
{    
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  if ($context['user']['is_logged'])
  {
    echo '<script>';
    echo 'var control = document.getElementById("showhidelogin");';
    echo 'control.onclick = function() { go("logout;sesc=', $context['session_id'] ,'"); };';
    echo '</script>';
  }
  else
  {
  
    echo '<script>
    var control = document.getElementById("showhidelogin");

    var toggleQuickLogin = function() {
      if ($("#quickLogin").is(":visible"))
      {
        $("#user ").blur();
        $("#quickLogin").hide();
        control.innerHTML = "'.$txt['login'].'";
      }
      else
      {
        $("#quickLogin").show();
        $("#user ").blur();
        $("#user ").focus();
        control.innerHTML = "Close";
      }
    };

    control.onclick = toggleQuickLogin;
    </script>';

    echo '<div id="quickLogin">  
  <form action="', $scripturl, '?action=login2" name="frmLogin" id="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>

<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['username'] .'</span>';
  echo'<input id="user" type="text" tabindex="', $context['tabindex']++, '" name="user" />
</div>
<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['password'] .'</span>';
  echo'<input type="password" tabindex="', $context['tabindex']++, '" name="passwrd" />
</div>
<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['iRemember'] .'</span>';
  echo'<input type="checkbox" checked="checked" name="cookieneverexp" value="1" id="cookieneverexp">
</div>
    
  <input type="hidden" name="hash_passwrd" value="" />
  <div class="buttons" style="margin-top: -9px; padding-bottom: 5px;">
    <button class="button twobuttons" type="submit">' . $txt['login'] . '</button>
    <button class="button twobuttons" type="button" onclick="go(\'register\')">'. $txt['register'] .'</button>
  </div>
  </form>
  </div>';
  }
}

?>