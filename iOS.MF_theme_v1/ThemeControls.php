<?php

/*
* Template controls specific to the theme
*/


//Add a quick login control where this method is called
function template_control_quick_login() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  echo '<div id="show-hide-login" class="show-hide-login ', $context['user']['is_logged'] ? 'logout-icon' : 'login-icon', '"></div>';

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
    
    echo '<div id="quick-login" class="quick-login">';

    template_control_login_form();

  echo '</div>';
  }
}

function template_control_login_form() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  echo '
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
  ';
}

//Add a quick search control where this method is called
function template_control_quick_search() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  echo '
    <div id="show-hide-search" class="show-hide-search magnifier-icon" onclick="toggleSearch"></div>

    <script type="text/javascript">
      var searchControl = $(".show-hide-search").last().get(0);
      var toggleSearch = function() {
        if ($("#search-bar").is(":visible"))
        {
          $("#search-bar").hide();
          searchControl.className = "magnifier-icon";
        }
        else
        {
          $("#search-bar").show();
          searchControl.className = "close-icon";
          $("#search-text").focus();
        }
        setTopMargin();
      };
      searchControl.onclick = toggleSearch;
    </script>

    <div id="search-bar" class="input-container">
      <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="search-form">
        <input id="search-text" type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', !empty($context['search_string_limit']) ? ' maxlength="' . $context['search_string_limit'] . '"' : '', ' tabindex="', $context['tabindex']++, '" />
        <input type="submit" class="button input-button" value="' . $txt['search_button'] . '" onclick="if(document.searchform.search.value.length<3){alert(\'', $txt['iAlert'], '\');return false;}" />
      </form>
    </div>';
}

//Add quick reply functionality to the header bar
function template_control_quick_reply() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  $quickReply = '<script type="text/javascript">

    $(function(){
      $(".editor").last().autosize();
    });

    var toggleQuickReply = function() {
      if ($("#quick-reply").is(":visible"))
      {
        $("#message").blur();
        $("#quick-reply").hide();      
      }
      else
      {
        $("#quick-reply").show();
        $("#message").focus();
      }
      setTopMargin();
    };

    var title = $(".the-title").last().get(0);
    title.onclick = function() { $(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0); toggleQuickReply();};
    title.style.color = "#007AFF";
    $(".the-title").addClass("quick-reply-title");

    var submitForm = function() {
      submitonce(this);
      smc_saveEntities("postmodify", ["subject", "' . $context['post_box_name'] . '", "guestname", "evtitle", "question"], "options");
    };

    </script>';
  
  $quickReply.= '<div id="quick-reply">';
  $quickReply.= '<form action="' . $scripturl . '?action=post2;' . (empty($context['current_board']) ? '' : 'board=') . $context['current_board'] . '.new#new" method="post" accept-charset="' . $context['character_set'] . '" name="postmodify" id="postmodify" onsubmit="submitForm();" enctype="multipart/form-data" style="margin: 0;">';
  
  $quickReply.= '
  <div id="post-container" class="input-container" style="padding-bottom: 0;">
    <div class="new-post">
      <textarea class="editor" name="message" id="message" rows="1" cols="60" tabindex="2" style="width: 100%; height: 16px; overflow: hidden; word-wrap: break-word; resize: horizontal;"></textarea>
    </div>
  </div>';
  
  // Guests have to put in their name and email...
  if (!$context['user']['is_logged'] && isset($context['name']) && isset($context['email'])) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">' . $txt['username'] . '</span>';
    $quickReply.= '<input type="text" name="guestname" size="25" value="' . $context['name'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
    $quickReply.= '<span id="smf_autov_username_div" style="display: none;">
            <a id="smf_autov_username_link" href="#">
              <img id="smf_autov_username_img" src="' . $settings['images_url'] . '/icons/field_check.png" alt="*" />
            </a>
          </span>';
    $quickReply.= '</div>';
    
    if (empty($modSettings['guest_post_no_email'])) {
      $quickReply.= '<div class="no-left-padding input-container pad-top">';
      $quickReply.= '<span class="input-label">' . $txt['email'] . '</span>';
      $quickReply.= '<input type="text" name="email" size="25" value="' . $context['email'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
      $quickReply.= '</div>';
    }
  }
  
  if ($context['require_verification']) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">Code</span>';
    $quickReply.= template_control_verification($context['visual_verification_id'], 'all');
    $quickReply.= '</div>';
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">Verify</span>';
    $quickReply.= '<input type="text" tabindex="' . $context['tabindex']++ . '" name="post_vv[code]" />';
    $quickReply.= '</div>';
  }
  
  $quickReply.= '<div class="child buttons">
  
  <button class="button" type="submit">' . $txt['iPost'] . '</button>

  </div>';
  
  if (isset($context['num_replies'])) $quickReply.= '<input type="hidden" name="num_replies" value="' . $context['num_replies'] . '" />';
  
  if (!empty($context['subject'])) {
    $quickReply.= '<input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
  
  $quickReply.= '
      <input type="hidden" name="additional_options" value="' . ($context['show_additional_options'] ? 1 : 0) . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
      <input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />
      <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />    
      <input type="hidden" name="goback" value="' . $options['return_to_post'] . '" />
    </form>';
  
  $quickReply.= '</div>';
  
  echo '<script type="text/javascript">
    $(function() {
      $(".topbar").last().append(', json_encode($quickReply), ');
    });
  </script>';
}

//Generate a paging control
function template_control_paging($pageControl = null) {
  global $context, $txt;
  
  if ($pageControl) {
    parse_page_control($pageControl);
  }
  
  echo '

  <script type="text/javascript">

    $(function(){

      Hammer($(".previous-page").last()).on("tap", function(event) {
        $.mobile.changePage(\'', $context['links']['prev'], '\');
      });

      Hammer($(".next-page").last()).on("tap", function(event) {
        $.mobile.changePage(\'', $context['links']['next'], '\');
      });

      Hammer($(".previous-page").last()).on("hold", function(event) {
        $.mobile.changePage(\'', $context['links']['first'], '\');
      });

      Hammer($(".next-page").last()).on("hold", function(event) {
        $.mobile.changePage(\'', $context['links']['last'], '\');
      });

    });

  </script>


  <div id="page-buttons" class="page buttons">
  
  <button id="previous-page" class="previous-page button" ', $context['page_info']['current_page'] == 1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
  
  <button id="page-count">', $txt['iPage'], ' ', $context['page_info']['current_page'], ' ', $txt['iOf'], ' ', ($context['page_info']['num_pages'] == 0) ? '1' : $context['page_info']['num_pages'], '</button>
  
  <button id="next-page" class="next-page button" ', ($context['page_info']['current_page'] == $context['page_info']['num_pages'] || $context['page_info']['num_pages'] == 0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
  
  </div>';
}

//Backward engineer paging links and details from page contol
function parse_page_control($pageControl) {
  global $context;
  
  $currentPageStart = strrpos($pageControl, '<strong>') + 8;
  $currentPageEnd = strrpos($pageControl, '</strong>');
  $currentPage = substr($pageControl, $currentPageStart, $currentPageEnd - $currentPageStart);
  
  $lastLinkEnd = strrpos($pageControl, '</a>');
  $lastLinkStart = $lastLinkEnd ? strrpos($pageControl, '>', -strlen($pageControl) + $lastLinkEnd) + 1 : false;
  $lastLinkedPage = $lastLinkStart && $lastLinkEnd ? substr($pageControl, $lastLinkStart, $lastLinkEnd - $lastLinkStart) : 1;
  
  $lastPageCountEnd = $lastLinkStart ? $lastLinkStart - 2 : false;
  $lastPageCountStart = $lastPageCountEnd ? strrpos($pageControl, 'start=', -strlen($pageControl) + $lastPageCountEnd) + 6 : false;
  $lastPageCount = $lastPageCountStart && $lastPageCountEnd ? substr($pageControl, $lastPageCountStart, $lastPageCountEnd - $lastPageCountStart) : 1;
  $pageSize = $lastPageCount / ($lastLinkedPage - 1);
  
  $firstPage = 1;
  $lastPage = $currentPage > $lastLinkedPage ? $currentPage : $lastLinkedPage;
  
  $previousPage = $currentPage >= 2 ? $currentPage - 1 : 1;
  $nextPage = $currentPage == $lastPage ? $lastPage : $currentPage + 1;
  
  $urlStart = strrpos($pageControl, 'href="') + 6;
  $urlEnd = $urlStart ? strrpos($pageControl, '"', $urlStart) : false;
  $url = $urlStart && $urlEnd ? substr($pageControl, $urlStart, $urlEnd - $urlStart) : '';
  $url = substr($url, 0, strrpos($url, ';')) . ';start=';
  
  $context['links'] = array('first' => $url . (($firstPage - 1) * $pageSize), 'prev' => $url . (($previousPage - 1) * $pageSize), 'next' => $url . (($nextPage - 1) * $pageSize), 'last' => $url . (($lastPage - 1) * $pageSize),);
  
  $context['page_info'] = array('current_page' => $currentPage, 'num_pages' => $lastPage,);
}
?>