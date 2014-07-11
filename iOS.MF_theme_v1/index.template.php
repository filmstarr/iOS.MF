<?php

// Version: 2.0 RC4; Index

$linguaggio = $settings['theme_dir'] . '/languages/iPhone.language.' . $context['user']['language'] . '.php';
if (file_exists($linguaggio)) require ($settings['theme_dir'] . '/languages/iPhone.language.' . $context['user']['language'] . '.php');
else require ($settings['theme_dir'] . '/languages/iPhone.language.english.php');

global $txt;

function template_init() {
  global $context, $settings, $options, $txt;
  
  $settings['theme_version'] = '1.0';
  
  // Portal disabling mafia
  // SimplePortal
  $settings['disable_sp'] = true;
  
  // TinyPortal
  if (function_exists('tp_hidebars')) tp_hidebars();
  
  // PortaMX
  $_SESSION['pmx_paneloff'] = array('head', 'top', 'left', 'right', 'bottom', 'foot', 'front', 'pages' => 'Pages');
}

function template_html_above() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>

<head>

<title>', $context['page_title_html_safe'], '</title>
<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
<meta name="description" content="', $context['page_title_html_safe'], '" />
', !empty($context['meta_keywords']) ? '<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />

<link type="text/css" rel="stylesheet" media="screen" href="', $settings['theme_url'], '/css/index.css" />
<link type="text/css" rel="stylesheet" href="' . $settings['theme_url'] . '/css/jquery.mobile.structure-1.4.2.min.css" />
<link type="text/css" rel="stylesheet" href="' . $settings['theme_url'] . '/css/magnific-popup.css" />
', ($context['right_to_left'] ? '<link type="text/css" rel="stylesheet" href="' . $settings['default_theme_url'] . '/css/rtl.css" />' : ''), '
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/fastclick.min.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.mobile-1.4.2.min.js"></script>
<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/script.js?fin20"></script>
<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/captcha.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.autosize.min.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.hammer.min.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/theme.js"></script>
<script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.magnific-popup.min.js"></script>

<script type="text/javascript">
  var disableQuoting = ', (isset($_COOKIE['disablequoting'])) ? 'true' : 'false', ';
  var showchildboards = "', $txt['iShow'], ' ', $txt['parent_boards'], '";
  var hidechildboards = "', $txt['iHide'], ' ', $txt['parent_boards'], '";
  var quotingoff = "', $txt['iQuoting'], ' ', $txt['iOff'], '";
  var loading = "', $txt['iLoading'], '...";    var smf_theme_url = "', $settings['theme_url'], '";
  var smf_default_theme_url = "', $settings['default_theme_url'], '";
  var smf_images_url = "', $settings['images_url'], '";
  var smf_scripturl = "', $scripturl, '";
  var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
  var smf_charset = "', $context['character_set'], '";
  var ajax_notification_text = "', $txt['ajax_in_progress'], '";
  var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
  $.mobile.defaultPageTransition = \'', isset($settings['page_transition_animation']) ? $settings['page_transition_animation'] : 'none', '\';

  var setTopMargin = function() {
    var topBar = $(".topbar").last();
    if (topBar.css("position") == "fixed") {
      var topBarHeight = topBar.height();
      $(".marginTopContent").last().css("padding-top", topBarHeight);
    }
  }

  $(function() {
    FastClick.attach(document.body);
  });

  $(document).on("pagecontainershow", function() {
    setTopMargin();
  });

</script>';
  
  if (isset($settings['disable_webkit_select']) && $settings['disable_webkit_select']) {
    echo '
    <style type="text/css">
      body {
        -webkit-user-select: none;
      }
    </style>';
  }
  
  if (isset($settings['enable_transparent_toolbar']) && $settings['enable_transparent_toolbar']) {
    echo '
    <style type="text/css">
      .toolbar {
        background: rgba(240,240,244,0.75) !important;
      }
    </style>';
  }
  
  echo '
</head>
<body><div id="wrapper" data-role="page"><div data-enhance="false">';
}

function iPhoneTitle() {
  
  global $context;
  
  $title = str_replace($context['forum_name_html_safe'] . ' - ', '', $context['page_title_html_safe']);
  
  if ($title == 'Index') $title = $context['forum_name_html_safe'];
  
  $title = str_replace('View the profile of ', '', $title);
  
  $title = str_replace('Set Search Parameters', 'Search', $title);
  
  $title = str_replace('Personal Messages Index', 'Personal Messages', $title);
  
  $title = str_replace('Send message', 'Compose Message', $title);
  
  return $title;
}

function template_body_above() {
  
  global $txt, $_GET, $context, $modSettings, $settings, $user_info, $scripturl;
  
  //Add fixed-top-bar to class of topbar to fix at the top. Mobile Safari doesn't like fixed items when the keyboard is showing at present though (iOS7).
  //ToDo: When Safari can handle this better present this as a user option.
  echo '
  <div class="topbar" id="topbar" data-role="header">';
  if ((!empty($_GET['action'])) && (($_GET['action'] == 'login') || ($_GET['action'] == 'register'))) {
    $loginregister = ' style="display:none;"';
  } else $loginregister = '';
  echo '

    <div id="page-title">';
  
  echo '<div id="the-title" class="the-title">', iPhoneTitle(), '</div>';
  
  echo '</div>

    <div id="show-hide-search" class="show-hide-search magnifier-icon" onclick="toggleSearch" ', $issearch, '></div>    
    <div id="show-hide-login" class="show-hide-login ', $context['user']['is_logged'] ? 'logout-icon' : 'login-icon', '"></div>

    <script type="text/javascript">
      var searchControl = $(".show-hide-search").last().get(0);
      var toggleSearch = function() {
        if ($("#searchbar").is(":visible"))
        {
          $("#searchbar").hide();
          searchControl.className = "magnifier-icon";
        }
        else
        {
          $("#searchbar").show();
          searchControl.className = "close-icon";
          $("#searchText").focus();
        }
        setTopMargin();
      };
      searchControl.onclick = toggleSearch;
    </script>';
  
  echo '

    <div id="searchbar" class="input-container">

    <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="search-form">

    <input id="searchText" type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', !empty($context['search_string_limit']) ? ' maxlength="' . $context['search_string_limit'] . '"' : '', ' tabindex="', $context['tabindex']++, '" />
    <input type="submit" id="searchbutton" class="button input-button" value="' . $txt['search_button'] . '" />
      
    </form>
    </div>';
  
  quick_login();
  
  echo '</div>';
  
  echo '<div class="marginTopContent">';
}

function template_body_below() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '<div>';
  
  //Subtle default mode button
  $themeNumber = '1';
  if (!empty($modSettings['id_default_theme'])) $themeNumber = $modSettings['id_default_theme'];
  else $themeNumber = $modSettings['theme_guests'];
  
  $currentUrl = getUrl();
  $backlink = "index.php?theme=" . $themeNumber . ";";
  if (strpos($currentUrl, 'index.php') !== false) {
    $backlink = $currentUrl;
    $backlink.= strpos($backlink, '?') == false ? '?' : (substr($backlink, -1) !== ';' ? ';' : '');
    $backlink.= strpos($backlink, 'theme=') == false ? 'theme=' . $themeNumber . ';' : '';
    $backlink = preg_replace("/theme=\d+/", "theme=" . $themeNumber, $backlink);
  }
  
  $backname = 'Default Theme';
  echo '<button data-ajax="false" class="classic button" id="classic">', $backname, '</button>';
  
  echo '<script type="text/javascript">
    Hammer($(".classic").last()).on("tap", function(event) {
      $(".ui-loader").loader("show");
      window.location.href = "', $backlink, '";
    });

    Hammer($(".classic").last()).on("hold", function(event) {
      $(".ui-loader").loader("show");
      window.location.href = "', preg_replace("/theme=\d+/", "theme=1", $backlink), '";
    });
  </script>';
  
  echo '<div id="copyright"><h4>', theme_copyright(), '</h4></div>';
  
  echo '</div>';
  
  //Toolbar HTML
  require_once ($settings[theme_dir] . '/ThemeFunctions.php');
  $unreadPostCount = UnreadPostCount();
  
  //Use javascript to set post count as the toolbar may not be reloaded each time; we need to do this within main page
  echo '
    <script type="text/javascript">
      $(document).one("pageload", function()
        {
          var unreadPostCount = ', $unreadPostCount, ';
          var unreadMessageCount = ', $context['user']['unread_messages'], ';

          var unreadPostCountElement = $(".unreadPosts").last();
          var unreadMessageCountElement = $(".unread-messages").last();

          unreadPostCountElement.get()[0].innerHTML = unreadPostCount;
          unreadMessageCountElement.get()[0].innerHTML = unreadMessageCount;

          if (unreadPostCount != 0) {
            unreadPostCountElement.show();
          }
          else {
            unreadPostCountElement.hide();
          }

          if (unreadMessageCount != 0) {
            unreadMessageCountElement.show();
          }
          else {
            unreadMessageCountElement.hide();
          }
        });
    </script>';
  
  echo '</div>
    </div>';
  
  echo '<div id="toolbar" class="toolbar" data-role="footer" data-id="footer" data-position="fixed" data-tap-toggle="false" data-enhance="true">
        <div><div class="toolbar-icon" onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'home\');" style="background: url(' . $settings['theme_url'] . '/images/icons/home.png) transparent center no-repeat;"></div></div>
        <div><div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'profile\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/person.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div></div>
        <div>
          <div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'pm\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/messages.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div>
          <div class="unread-count unread-messages"', ($context['user']['unread_messages'] > 0 && $context['user']['is_logged'] ? '>' . $context['user']['unread_messages'] : ' style="display:none;">'), '</div>
        </div>
        <div><div class="toolbar-icon" onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'recent\');" style="background: url(' . $settings['theme_url'] . '/images/icons/inbox.png) transparent center no-repeat;"></div></div>
        <div>
          <div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'unread;all\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/newpost.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div>
          <div class="unread-count unreadPosts"', ($unreadPostCount > 0 && $context['user']['is_logged'] ? '>' . $unreadPostCount : ' style="display:none">'), '</div>
        </div>
    </div>';
}

function template_html_below() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '
</div>
</body>
</html>';
}

function theme_linktree() {
  
  return false;
}

function getUrl() {
  $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
  $url.= ($_SERVER["SERVER_PORT"] !== "80") ? ":" . $_SERVER["SERVER_PORT"] : "";
  $url.= $_SERVER["REQUEST_URI"];
  return $url;
}

function iPhoneTime($time) {
  
  global $txt;
  
  $diff = forum_time() - $time;
  
  if ($diff < 60) return $diff . ' ' . $txt['iSecondsAgo'];
  elseif (round($diff / 60) == 1) return '1 ' . $txt['iMinuteAgo'];
  elseif ($diff > 59 && $diff < 3600) return round($diff / 60) . ' ' . $txt['iMinutesAgo'];
  elseif (round($diff / 60 / 60) == 1) return '1 ' . $txt['iHourAgo'];
  elseif (round($diff / 60 / 60) > 1 && round($diff / 60 / 60) < 24) return round($diff / 60 / 60) . ' ' . $txt['iHoursAgo'];
  elseif (round($diff / 60 / 60 / 24) == 1) return '1 ' . $txt['iDayAgo'];
  elseif (round($diff / 60 / 60 / 24) > 1 && round($diff / 60 / 60 / 24) < 7) return round($diff / 60 / 60 / 24) . ' ' . $txt['iDaysAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) == 1) return '1 ' . $txt['iWeekAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iWeeksAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) == 1) return '1 ' . $txt['iMonthAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iMonthsAgo'];
  else return $diff;
}

function short1($ret) {
  $ret = ' ' . $ret;
  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2'>$2</a>", $ret);
  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2'>$2</a>", $ret);
  short2($ret);
  $ret = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $ret);
  $ret = substr($ret, 1);
  return ($ret);
}

function short2(&$ret) {
  
  $links = explode('<a', $ret);
  $countlinks = count($links);
  for ($i = 0; $i < $countlinks; $i++) {
    $link = $links[$i];
    
    $link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
    
    $begin = strpos($link, '>') + 1;
    $end = strpos($link, '<', $begin);
    $length = $end - $begin;
    $urlname = substr($link, $begin, $length);
    
    $chunked = (strlen(str_replace('http://', '', $urlname)) > 28 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace(str_replace('http://', '', $urlname), '.....', 12, -12) : $urlname;
    $ret = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $ret);
  }
}

function template_button_strip() {
  return;
}

function quick_login() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  if ($context['user']['is_logged']) {
    echo '<script type="text/javascript">';
    echo 'var control = $(".show-hide-login").last().get(0);';
    echo 'control.onclick = function() { $(".ui-loader").loader("show"); window.location.href = "index.php?action=logout;sesc=', $context['session_id'], '"; };';
    echo '</script>';
  } else {
    echo '<script type="text/javascript">
    var control = $(".show-hide-login").last().get(0);

    var toggleQuickLogin = function() {
      if ($("#quick-login").is(":visible"))
      {
        $("#user").blur();
        $("#quick-login").hide();
        control.className = "login-icon";
      }
      else
      {
        $("#quick-login").show();
        $("#user").focus();
        control.className = "close-icon";
      }
      setTopMargin();
    };

    control.onclick = toggleQuickLogin;
    </script>';
    
    echo '<div id="quick-login" class="quick-login">
  <form data-ajax="false" action="', $scripturl, '?action=login2" name="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>

<div class="no-left-padding input-container pad-top">';
    echo '<span class="input-label">' . $txt['username'] . '</span>';
    echo '<input id="user" class="user" type="text" tabindex="', $context['tabindex']++, '" name="user" />
</div>
<div class="no-left-padding input-container pad-top">';
    echo '<span class="input-label">' . $txt['password'] . '</span>';
    echo '<input type="password" tabindex="', $context['tabindex']++, '" name="passwrd" />
</div>
<div class="no-left-padding input-container pad-top">';
    echo '<span class="input-label">' . $txt['iRemember'] . '</span>';
    echo '<input type="checkbox" checked="checked" name="cookieneverexp" value="1" />
</div>
    
  <input type="hidden" name="hash_passwrd" value="" />
  <div class="buttons" style="margin-top: -9px; padding-bottom: 5px;">
    <button onclick="$(\'.ui-loader\').loader(\'show\');" class="button two-buttons" type="submit">' . $txt['login'] . '</button>
    <button class="button two-buttons" type="button" onclick="go(\'register\')">' . $txt['register'] . '</button>
  </div>
  </form>
  </div>';
  }
}
?>