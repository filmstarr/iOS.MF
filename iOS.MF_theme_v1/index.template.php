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

<link rel="stylesheet" media="screen" href="', $settings['theme_url'] ,'/css/style.css" type="text/css" />
<link rel="stylesheet" href="'. $settings['theme_url'] .'/css/jquery.mobile.structure-1.4.2.min.css" />
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/scripts/energize-min.js"></script>
<script src="'. $settings['theme_url'] .'/scripts/jquery-2.1.1.min.js"></script>
<script src="'. $settings['theme_url'] .'/scripts/jquery.mobile-1.4.2.min.js"></script>
<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
<script type="text/javascript" src="'. $settings['default_theme_url'] .'/scripts/captcha.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/scripts/jquery.autosize.min.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/scripts/jquery.hammer.min.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/scripts/iphone.js"></script>
<script type="application/x-javascript" src="'. $settings['theme_url'] .'/scripts/quote.js"></script>

<script type="application/x-javascript">
  ', (isset($_COOKIE['disablequoting'])) ? 'var aquoting = 1;' : 'var aquoting = 0;','
  var showchildboards = "', $txt['iShow'] ,' ', $txt['parent_boards'], '";
  var hidechildboards = "', $txt['iHide'] ,' ', $txt['parent_boards'], '";
  var quotingoff = "', $txt['iQuoting'],' ',$txt['iOff'], '";
  var loading = "', $txt['iLoading'],'...";    var smf_theme_url = "', $settings['theme_url'], '";
  var smf_default_theme_url = "', $settings['default_theme_url'], '";
  var smf_images_url = "', $settings['images_url'], '";
  var smf_scripturl = "', $scripturl, '";
  var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
  var smf_charset = "', $context['character_set'], '";
  var ajax_notification_text = "', $txt['ajax_in_progress'], '";
  var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
  $.mobile.defaultPageTransition = \'' , isset( $settings['page_transition_animation']) ?  $settings['page_transition_animation'] : 'none' , '\';
</script>';

if (isset($settings['disable_webkit_select']) && $settings['disable_webkit_select'])
{
  echo '
    <style>
      body {
        -webkit-user-select: none;
      }
    </style>';
}

  echo '
</head>
<body><div id="wrapper" data-role="page"><div data-enhance=false>';
}

function iPhoneTitle(){

  global $context;

  $title = str_replace($context['forum_name_html_safe'].' - ','',$context['page_title_html_safe']);
  
  if($title=='Index')
    $title=$context['forum_name_html_safe'];
  
  $title = str_replace('View the profile of ','',$title);
  
  $title = str_replace('Set Search Parameters','Search',$title);
  
  $title = str_replace('Personal Messages Index','Personal Messages',$title);

  return $title;
  
  }

function template_body_above()
{

global $txt, $_GET, $context, $modSettings, $settings, $user_info, $scripturl;

echo '

<div id="topbar" data-role="header">';
if((!empty($_GET['action'])) && (($_GET['action']=='login') || ($_GET['action']=='register'))) {
  $loginregister=' style="display:none;"';
  }
else
  $loginregister='';
  echo'

  <h1 id="pageTitle">';
  
  echo'<div id="theTitle" class="theTitle">', iPhoneTitle(), '</div>';  
  
  echo'</h1>

  <div id="showhidesearch" class="showhidesearch magnifierIcon" onclick="toggleSearch" id="tabsearch"', $issearch ,'></div>    
  <div id="showhidelogin" class="showhidelogin ' , $context['user']['is_logged'] ? 'logoutIcon' : 'loginIcon' , '"></div>

  <script>
    var searchControl = $(".showhidesearch").last().get(0);
    var toggleSearch = function() {
      if ($("#searchbar").is(":visible"))
      {
        $("#searchbar").hide();
        searchControl.className = "magnifierIcon";
      }
      else
      {
        $("#searchbar").show();
        searchControl.className = "closeIcon";
        $("#searchText").focus();
      }
    };
    searchControl.onclick = toggleSearch;
  </script>

  </div>';

  echo '

  <div id="searchbar" class="inputContainer">

  <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="searchform">

  <input id="searchText" type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', !empty($context['search_string_limit']) ? ' maxlength="' . $context['search_string_limit'] . '"' : '', ' tabindex="', $context['tabindex']++, '" />
  <input type="submit" id="searchbutton" class="button inputbutton" value="'. $txt['search_button'] .'" />
    
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
echo '<a class="classic button" id="classic" href="'. $backlink .'">', $backname ,'</a>';

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
    echo '</div>';

    //Toolbar HTML
    require_once ($settings[theme_dir].'/ThemeFunctions.php');
    $unreadPostCount = UnreadPostCount();

    echo '<div id="toolbar" class="toolbar" data-role="footer" data-id="footer" data-position="fixed" data-tap-toggle="false" data-enhance=true>
    <ul>
      <li><div onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);$.mobile.changePage(\'',$scripturl,'\')" style="background: url('.$settings['theme_url'].'/images/icons/home.png) transparent center no-repeat;"></div></li>
      <li><div onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);$.mobile.changePage(\'?action=profile\')' : '' , '" style="background: url('.$settings['theme_url'].'/images/icons/person.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;' , '"></div></li>
      <li>
        <div onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);$.mobile.changePage(\'?action=pm\')' : '' , '" style="background: url('.$settings['theme_url'].'/images/icons/messages.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;' , '"></div>
        ', $context['user']['unread_messages'] > 0 && $context['user']['is_logged'] ? '<div id="unreadCount">' . $context['user']['unread_messages'] . '</div>' : '' , '
      </li>
      <li><div onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);$.mobile.changePage(\'?action=recent\')" style="background: url('.$settings['theme_url'].'/images/icons/inbox.png) transparent center no-repeat;"></div></li>
      <li>
        <div onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);$.mobile.changePage(\'?action=unread;all\')' : '' , '" style="background: url('.$settings['theme_url'].'/images/icons/newpost.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;' , '"></div>
        ', $unreadPostCount > 0 && $context['user']['is_logged'] ? '<div id="unreadCount">' . $unreadPostCount . '</div>' : '' , '
      </li>
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
    echo 'var control = $(".showhidelogin").last().get(0);';
    echo 'control.onclick = function() { go("logout;sesc=', $context['session_id'] ,'"); };';
    echo '</script>';
  }
  else
  {
    echo '<script>
    var control = $(".showhidelogin").last().get(0);

    var toggleQuickLogin = function() {
      if ($("#quickLogin").is(":visible"))
      {
        $("#user").blur();
        $("#quickLogin").hide();
        control.className = "loginIcon";
      }
      else
      {
        $("#quickLogin").show();
        $("#user").focus();
        control.className = "closeIcon";
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