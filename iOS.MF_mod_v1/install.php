<?php

/*
* Install iOS.MF modification
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//Add integration hooks to load theme functions file
add_integration_function('integrate_theme_include', '$sourcedir/iOS.MF.ThemeFunctions.php',TRUE);

?>