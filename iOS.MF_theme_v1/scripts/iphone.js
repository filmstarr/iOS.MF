function Set_Cookie( name, value, expires, path, domain, secure )
{
var today = new Date();
today.setTime( today.getTime() );


if ( expires )
{
expires = expires * 1000 * 60 * 60 * 24;
}
var expires_date = new Date( today.getTime() + (expires) );

document.cookie = name + "=" +escape( value ) +
( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
( ( path ) ? ";path=" + path : "" ) +
( ( domain ) ? ";domain=" + domain : "" ) +
( ( secure ) ? ";secure" : "" );
}
  
function Get_Cookie( check_name ) {
  var a_all_cookies = document.cookie.split( ';' );
  var a_temp_cookie = '';
  var cookie_name = '';
  var cookie_value = '';
  var b_cookie_found = false; // set boolean t/f default f

  for ( i = 0; i < a_all_cookies.length; i++ )
  {
    
    a_temp_cookie = a_all_cookies[i].split( '=' );


    cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

    // if the extracted name matches passed check_name
    if ( cookie_name == check_name )
    {
      b_cookie_found = true;
      // we need to handle case where cookie has no value but exists (no = sign, that is):
      if ( a_temp_cookie.length > 1 )
      {
        cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
      }
      // note that in cases where cookie is initialized but no value, null is returned
      return cookie_value;
      break;
    }
    a_temp_cookie = null;
    cookie_name = '';
  }
  if ( !b_cookie_found )
  {
    return null;
  }
}
    
// this deletes the cookie when called
function Delete_Cookie( name, path, domain ) {
if ( Get_Cookie( name ) ) document.cookie = name + "=" +
( ( path ) ? ";path=" + path : "") +
( ( domain ) ? ";domain=" + domain : "" ) +
";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

function hideClass(objClass){

var elements = document.getElementsByTagName('li');
  for (i=0; i<elements.length; i++){
    if (elements[i].className==objClass){
      elements[i].style.display="none"
    }
  }
}

function showClass(objClass){

var elements = document.getElementsByTagName('li');
  for (i=0; i<elements.length; i++){
    if (elements[i].className==objClass){
      elements[i].style.display="block"
    }
  }
}

function go(location){
  
  if (location=='home')
    $.mobile.changePage('index.php');
  else
    $.mobile.changePage('index.php?action='+location);

}

function toggle(cb,img){

  var checked = document.getElementById(cb).checked;
  
  var src = document.getElementById(img).src;

  if(!checked)
    document.getElementById(img).src = src.replace('On','Off');
  else
    document.getElementById(img).src = src.replace('Off','On');
  
}

function hashLoginPassword(doForm, cur_session_id)
{
  // Compatibility.
  if (cur_session_id == null)
    cur_session_id = smf_session_id;

  if (typeof(hex_sha1) == "undefined")
    return;
  // Are they using an email address?
  if (doForm.user.value.indexOf("@") != -1)
    return;

  // Unless the browser is Opera, the password will not save properly.
  if (typeof(window.opera) == "undefined")
    doForm.passwrd.autocomplete = "off";

  doForm.hash_passwrd.value = hex_sha1(hex_sha1(doForm.user.value.php_to8bit().php_strtolower() + doForm.passwrd.value.php_to8bit()) + cur_session_id);

  // It looks nicer to fill it with asterisks, but Firefox will try to save that.
  if (navigator.userAgent.indexOf("Firefox/") != -1)
    doForm.passwrd.value = "";
  else
    doForm.passwrd.value = doForm.passwrd.value.replace(/./g, "*");
}

function iswitch(id){

  if(id=='switcher'){
    document.getElementById(id).id = 'switcheralt';
    
    var x= document.getElementsByTagName('ul');
    
    for(var i = 0;i<x.length;i++){
      
      if(x[i].id=='contentrecent')
        x[i].style.display='block';
      if(x[i].id=='contentboards')
        x[i].style.display='none';
        }
    document.getElementById('childbuttondiv').style.display='none';
        
    var x= document.getElementsByTagName('h2');
    
    for(var i = 0;i<x.length;i++){
      
      if(x[i].id=='contentrecenth2')
        x[i].style.display='block';
      if(x[i].id=='contentboardsh2')
        x[i].style.display='none';
        }
    
  }else{
  
    document.getElementById(id).id = 'switcher';
    
    var x= document.getElementsByTagName('ul');
    
    for(var i = 0;i<x.length;i++){
      
      if(x[i].id=='contentrecent')
        x[i].style.display='none';
      if(x[i].id=='contentboards')
        x[i].style.display='block';
        }
    document.getElementById('childbuttondiv').style.display='block';
        
    var x= document.getElementsByTagName('h2');
    
    for(var i = 0;i<x.length;i++){
      
      if(x[i].id=='contentrecenth2')
        x[i].style.display='none';
      if(x[i].id=='contentboardsh2')
        x[i].style.display='block';
        }
  
    }
}

$(function(){
  $(document).on("swiperight", function (event) { window.history.back(); } );
  $(document).on("swipeleft", function (event) { window.history.forward(); } );
});

$(window).on('beforeunload' , function() {
  $('.clicked').each(function() {
    $(this).className = '';
});
});

// Hide toolbar when input is focused
if(/iPhone|iPod|Android|iPad/.test(window.navigator.platform)){
  $(document)
  .on('focus', 'textarea,input,select', function(e) {
    $('.toolbar').css('display', 'none');
    $('#copyright').css('margin-bottom', '4px');
  })
  .on('blur', 'textarea,input,select', function(e) {
    $('.toolbar').css('display', 'initial');
    $('#copyright').css('margin-bottom', '47px');
  });
}

$.mobile.ignoreContentEnabled=true;
$.event.special.swipe.horizontalDistanceThreshold = 100;

$(document).bind("mobileinit", function() {
    $.mobile.buttonMarkup.hoverDelay = 5000
});

$(function() {
  $("#toolbar").removeClass( "ui-footer" )
});

$(document).one('pagehide', document, function(event, ui){
  $('[data-role="page"]').not(".ui-page-active").remove();
});