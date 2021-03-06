<?php
/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\wizard;


class System{

    function init(){
        global $site;

        // loading required Javascript libraries
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.ui.tooltip.js',2);
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.ui.overlay.js',2);
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.toolbox.expose.js',2);
        $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/wizard/public/jquery.simulate.js',2);
        // loading module's elements
        $site->addCSS(BASE_URL.MODULE_DIR.'administrator/wizard/public/wizard.css',2);
        $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/wizard/public/wizard.js',2);
    }
}
