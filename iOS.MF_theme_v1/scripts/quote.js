function removeOnClick(objClass) {
  $(objClass).each(function () {
    $(this).off("click");
    $(this).prop("onclick", null);
  });
}

function quoting() {
  if (aquoting == 0) {
    // disable quoting
    aquoting = 1;
    Set_Cookie('disablequoting', '1', '', '/', '', '');
    document.getElementById('quoting').innerHTML = quotingoff;
    removeOnClick('.message');
  } else {
    // enable quoting
    aquoting = 0;
    Delete_Cookie('disablequoting', '/', '', '');
    document.getElementById('quoting').innerHTML = loading;
    $.mobile.changePage(window.location.href, {
      allowSamePageTransition: true,
      transition: 'none',
      reloadPage: true
    });
  }
}