/*
* Main Javascript file containing iOS.MF specific functions
*/


/* Cookie management */

function Set_Cookie(name, value, expires, path, domain, secure) {
  var today = new Date();
  today.setTime(today.getTime());

  if (expires) {
    expires = expires * 1000 * 60 * 60 * 24;
  }
  var expires_date = new Date(today.getTime() + (expires));

  document.cookie = name + "=" + escape(value) +
    ((expires) ? ";expires=" + expires_date.toGMTString() : "") +
    ((path) ? ";path=" + path : "") +
    ((domain) ? ";domain=" + domain : "") +
    ((secure) ? ";secure" : "");
}

function Get_Cookie(check_name) {
  var a_all_cookies = document.cookie.split(';');
  var a_temp_cookie = '';
  var cookie_name = '';
  var cookie_value = '';
  var b_cookie_found = false; // set boolean t/f default f

  for (i = 0; i < a_all_cookies.length; i++) {

    a_temp_cookie = a_all_cookies[i].split('=');

    cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

    // if the extracted name matches passed check_name
    if (cookie_name == check_name) {
      b_cookie_found = true;
      // we need to handle case where cookie has no value but exists (no = sign, that is):
      if (a_temp_cookie.length > 1) {
        cookie_value = unescape(a_temp_cookie[1].replace(/^\s+|\s+$/g, ''));
      }
      // note that in cases where cookie is initialized but no value, null is returned
      return cookie_value;
      break;
    }
    a_temp_cookie = null;
    cookie_name = '';
  }
  if (!b_cookie_found) {
    return null;
  }
}

function Delete_Cookie(name, path, domain) {
  if (Get_Cookie(name)) document.cookie = name + "=" +
    ((path) ? ";path=" + path : "") +
    ((domain) ? ";domain=" + domain : "") +
    ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}


/* Security and login */

//Turn the users password into a hash for submission to the server
function hashLoginPassword(doForm, cur_session_id) {
  // Compatibility.
  if (cur_session_id == null)
    cur_session_id = smf_session_id;

  if (typeof (hex_sha1) == "undefined")
    return;
  // Are they using an email address?
  if (doForm.user.value.indexOf("@") != -1)
    return;

  // Unless the browser is Opera, the password will not save properly.
  if (typeof (window.opera) == "undefined")
    doForm.passwrd.autocomplete = "off";

  doForm.hash_passwrd.value = hex_sha1(hex_sha1(doForm.user.value.php_to8bit().php_strtolower() + doForm.passwrd.value.php_to8bit()) + cur_session_id);

  // It looks nicer to fill it with asterisks, but Firefox will try to save that.
  if (navigator.userAgent.indexOf("Firefox/") != -1)
    doForm.passwrd.value = "";
  else
    doForm.passwrd.value = doForm.passwrd.value.replace(/./g, "*");
}


/* jQuery Mobile setup */

//Initialise jQuery mobile settings
$.mobile.ignoreContentEnabled = true;
$.event.special.swipe.horizontalDistanceThreshold = 100;
$.mobile.hideUrlBar = false;

//Remove some jQuery mobile CSS from the footer
$(function () {
  $("#toolbar").removeClass("ui-footer")
});

//Remove the first page from the DOM when we navigate away to prevent caching. This allows us to update post and message counts.
$(document).one('pagehide', document, function (event, ui) {
  $('[data-role="page"]').not(".ui-page-active").remove();
});


/* Website navigation */

//Navigate to a location via a jQuery Mobile AJAX request
function go(location) {
  if (location == 'home')
    $.mobile.changePage('index.php', { reloadPage : true });
  else
    $.mobile.changePage('index.php?action=' + location, { reloadPage : true });
}

//Navigate forwards and backwards when the user swipes across the page
$(function () {
  $(document).on("swiperight", function (event) {
    window.history.back();
  });
  $(document).on("swipeleft", function (event) {
    window.history.forward();
  });
});

//Hide the loader when leaving page in case it has been shown manually
window.addEventListener("popstate", function() { $(".ui-loader").loader("hide"); });


/* Mobile specific methods */

// Hide toolbar when input is focused. This is needed as the iPhone doesn't honour fixed elements when the keyboard is showing.
if (/iPhone|iPod|Android|iPad/.test(window.navigator.platform)) {
  $(document)
    .on('focus', 'textarea,input,select', function (e) {
      $('.toolbar').css('display', 'none');
      $('#copyright').css('margin-bottom', '4px');
    })
    .on('blur', 'textarea,input,select', function (e) {
      $('.toolbar').css('display', 'initial');
      $('#copyright').css('margin-bottom', '47px');
    });
}


/* Enable or disable message quoting */

function toggleQuoting() {
  if (!disableQuoting) {
    //Disable quoting
    disableQuoting = true;
    Set_Cookie('disablequoting', '1', '', '/', '', '');
    document.getElementById('quoting').innerHTML = quotingoff;

    //Remove all onclick events from a class
    $('.message').each(function () {
      $(this).off("click");
      $(this).prop("onclick", null);
    });
  } else {
    //Enable quoting
    disableQuoting = false;
    Delete_Cookie('disablequoting', '/', '', '');
    document.getElementById('quoting').innerHTML = loading;
    $.mobile.changePage(window.location.href, {
      allowSamePageTransition: true,
      transition: 'none',
      reloadPage: true
    });
  }
}

//Remove all onclick events from a class
function removeOnClick(objClass) {
  $(objClass).each(function () {
    $(this).off("click");
    $(this).prop("onclick", null);
  });
}