<?php

/*
    Copyright (C) 2010-12 Christina Louise Warne (email : athena@outer-reaches.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    Define our constants
    
    VSTEXTDOMAIN            - Specifies the text domain to be used by the plugin
    VSSITELINK              - Specifies the URL for any links to my blog
    VSBLOGLINK              - Specifies the URL for any links to the Virtual Sidebar page on my blog
    VSWIKILINK              - Specifies the URL for any links to the Virtual Sidebar page on my wiki
    VSOPTIONS               - Specifies the option name to use for our configuration storage
    VSVERSION               - Specifies the version of the plugin
    VSTRIGGERFIELD          - Specifies the name of our form verification field
    VSTRIGGERSESSIONVAR     - Specified the option name to use for storing our verification value
    VSFIRSTID               - Since we have a specified range of IDs we use, this is the first one
    VSLASTID                - The last ID available for use
    VSBASENAMEFIELD         - Base name for a name input field (will have -ID or -new tacked on)
    VSBASEDESCFIELD         - Base name for a description input field
    VSBASEWIDGETPREFIELD    - Base name for a text area (Widget pre wrap)
    VSBASEWIDGETPOSTFIELD   - Base name for a text area (Widget post wrap)
    VSBASETITLEPREFIELD     - Base name for a text area (Title pre wrap)
    VSBASETITLEPOSTFIELD    - Base name for a text area (Title post wrap)
    
*/

define( 'VSTEXTDOMAIN', 'virtual-sidebar' );  
define( 'VSSITELINK', 'http://athena.outer-reaches.com' );
define( 'VSBLOGLINK', 'http://athena.outer-reaches.com/wp/index.php/wiki/virtual-sidebar' );
define( 'VSWIKILINK', 'http://athena.outer-reaches.com/wiki/doku.php?id=projects:virtualsidebar:home' );
define( 'VSOPTIONS', 'vs-options' );
define( 'VSVERSION', '0.1.2' );
define( 'VSTRIGGERFIELD', 'vs-trigger' );
define( 'VSTRIGGERSESSIONVAR', 'vs-options-trigger-value' );
define( 'VSFIRSTID', 9000 );
define( 'VSLASTID', 9999 );
define( 'VSBASENAMEFIELD', 'vsname-');
define( 'VSBASEDESCFIELD', 'vsdesc-');
define( 'VSBASEWIDGETPREFIELD', 'vswrapwidgetpre-' );
define( 'VSBASEWIDGETPOSTFIELD', 'vswrapwidgetpost-' );
define( 'VSBASETITLEPREFIELD', 'vswraptitlepre-' );
define( 'VSBASETITLEPOSTFIELD', 'vswraptitlepost-' );    
define( 'VSBASECONTAINPREFIELD', 'vswrapcontainpre-' );
define( 'VSBASECONTAINPOSTFIELD', 'vswrapcontainpost-' );
define( 'VSDEFWIDGETPRE', '<li id="%1$s" class="widget %2$s">' );
define( 'VSDEFWIDGETPOST', '</li>' );
define( 'VSDEFTITLEPRE', '<h2 class="widgettitle">' );
define( 'VSDEFTITLEPOST', '</h2>' );
define( 'VSDEFCONTAINPRE', '<ul>' );
define( 'VSDEFCONTAINPOST', '</ul>' );
?>
