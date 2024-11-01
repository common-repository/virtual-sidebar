<?php
/*
Plugin Name: Virtual Sidebar
Plugin URI: http://athena.outer-reaches.com/wiki/doku.php?id=projects:wpvs:home
Description: Virtual Sidebars can contain widgets, but can be added to a post using the shortcode [vs id="<ID>"]
Version: 0.1.2
Author: Christina Louise Warne
Author URI: http://athena.outer-reaches.com
License: GPL3
*/

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

include_once("vs-constants.php");

// Load our configuration data
function vs_loadconfig( &$config ) {
    // Load the configuration data from our config variable
    $config = unserialize( get_option( VSOPTIONS ) );
}

// Save our configuration data
function vs_saveconfig( $config ) {
    // Save the configuration data to our config variable
    update_option( VSOPTIONS, serialize( $config ) );
}

// Get a new ID for a new virtual sidebar widget
function vs_getfirstunusedid( $config ) {   
    $id = 0;
    
    // Loop through from the first possible ID to the last and stop at the
    // first one that is not used
    if ( isset( $config ) ) {
        for ( $loop = VSFIRSTID; $loop <= VSLASTID; $loop++ )
        {
            if ( !array_key_exists( $loop, $config ) )
            {
                $id = $loop;
                break;
            }
        }
    }
    
    return $id;    
}

// Unset a set of variables in the $_POST array
function vs_unsetpost( $name ) {
	unset( $_POST[ VSBASENAMEFIELD . $name ] );
    unset( $_POST[ VSBASEDESCFIELD . $name ] );
    $_POST[ VSBASEWIDGETPREFIELD . $name ] = VSDEFWIDGETPRE;
    $_POST[ VSBASEWIDGETPOSTFIELD . $name ] = VSDEFWIDGETPOST;
    $_POST[ VSBASETITLEPREFIELD . $name ] = VSDEFTITLEPRE;
    $_POST[ VSBASETITLEPOSTFIELD . $name ] = VSDEFTITLEPOST;
    $_POST[ VSBASECONTAINPREFIELD . $name ] = VSDEFCONTAINPRE;
    $_POST[ VSBASECONTAINPOSTFIELD . $name ] = VSDEFCONTAINPOST;
}

// Create a configuration array entry from the specified fields in the $_POST array
function vs_configarrayfrompost( $name ) {
    // Collect the required data from the $_POST array
	$data = array(
    	"name"              => $_POST[ VSBASENAMEFIELD . $name ],
        "description"       => $_POST[ VSBASEDESCFIELD . $name ],
        "before_widget"     => $_POST[ VSBASEWIDGETPREFIELD . $name ],
        "after_widget"      => $_POST[ VSBASEWIDGETPOSTFIELD . $name ],
        "before_title"      => $_POST[ VSBASETITLEPREFIELD . $name ],
        "after_title"       => $_POST[ VSBASETITLEPOSTFIELD . $name ],
        "before_contain"    => $_POST[ VSBASECONTAINPREFIELD . $name ],
        "after_contain"     => $_POST[ VSBASECONTAINPOSTFIELD . $name ]
    );
    
    // Run the collected data through the necessary filters
	$data[ "name" ] = strip_tags( stripslashes( $data[ "name" ] ) );
	$data[ "description" ] = strip_tags( stripslashes( $data[ "description" ] ) );
    
	if ( current_user_can( 'unfiltered_html' ) ) {
		$data[ "before_widget" ]    =  stripslashes( $data[ "before_widget" ] );
		$data[ "after_widget" ]     = stripslashes( $data[ "after_widget" ] );
        $data[ "before_title" ]     = stripslashes( $data[ "before_title" ] );
        $data[ "after_title" ]      = stripslashes( $data[ "after_title" ] );
        $data[ "before_contain" ]   = stripslashes( $data[ "before_contain" ] );
        $data[ "after_contain" ]    = stripslashes( $data[ "after_contain" ] );
	} else {
		$data[ "before_widget" ]    = stripslashes( wp_filter_post_kses( $data[ "before_widget" ] ) );
		$data[ "after_widget" ]     = stripslashes( wp_filter_post_kses( $data[ "after_widget" ] ) );
        $data[ "before_title" ]     = stripslashes( wp_filter_post_kses( $data[ "before_title" ] ) );
        $data[ "after_title" ]      = stripslashes( wp_filter_post_kses( $data[ "after_title" ] ) );        
        $data[ "before_contain" ]   = stripslashes( wp_filter_post_kses( $data[ "before_contain" ] ) );
        $data[ "after_contain" ]    = stripslashes( wp_filter_post_kses( $data[ "after_contain" ] ) );        
	}		
	
    return $data;
}

// Add our admin page option to the plugins menu
function vs_adminmenu() {
    add_submenu_page( 'plugins.php', __( 'Virtual Sidebar Options', VSTEXTDOMAIN ), __( 'Virtual Sidebar', VSTEXTDOMAIN ), 'manage_options', 'virtual-sidebar-options', 'vs_options' );
}
add_action('admin_menu', 'vs_adminmenu');


function vs_addmessage( $newmsg, &$dstmsg ) {
    if ( $dstmsg != "" ) {
        $dstmsg .= "<br />\n";
    }
    $dstmsg .= $newmsg;
}

function vs_cptableheaders() {
    ?>
    <thead>
        <tr>
            <th align="center" width="12%" class="manage-column" scope="col"><?php _e( 'VSID', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Name/Description', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Container', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Widget Wrappers', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Title Wrappers', VSTEXTDOMAIN ); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th align="center" width="12%" class="manage-column" scope="col"><?php _e( 'VSID', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Name/Description', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Container', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Widget Wrappers', VSTEXTDOMAIN ); ?></th>
            <th width="22%" class="manage-column" scope="col"><?php _e( 'Title Wrappers', VSTEXTDOMAIN ); ?></th>
        </tr>
    </tfoot>
    <?php
}

// Register a sidebar using our configuration data
function vs_registerbar( $id, $config ) {
    $newbar = array();
    $newbar[ 'id' ] = 'vs' . $id;
    $newbar[ 'name' ] = $config[ 'name' ] . " (" . $id . ")";
    if ( $config[ 'description' ] != "" ) {
        $newbar[ 'description' ] = $config[ 'description' ];
    }
    $newbar[ 'before_widget' ] = $config[ 'before_widget' ];
    $newbar[ 'after_widget' ] = $config[ 'after_widget' ];
    $newbar[ 'before_title' ] = $config[ 'before_title' ];
    $newbar[ 'after_title' ] = $config[ 'after_title' ];
        
    register_sidebar( $newbar );
}

// Generate our configuration page and process any requests it generates
function vs_options() {
   
    // Check that the current user can manage the options... if not, get rid of them
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
  
    // Load our configuration data
    vs_loadconfig( $config );
  
    // Read the verification data from our 'session variable'
    $triggervalue = get_option( VSTRIGGERSESSIONVAR );
    
    // Reset the feedback message
    $successmessage = "";
    $errormessage = "";
    $blankpost = true;
    
    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if ( ( isset( $_POST[ VSTRIGGERFIELD ] ) ) && ( $_POST[ VSTRIGGERFIELD ] == $triggervalue ) ) {
        
        // Process the incoming request
        if ( ( isset( $_POST[ 'vsnewadd' ] ) ) && ( $_POST[ 'vsnewadd' ] == __( "Add", VSTEXTDOMAIN ) ) ) {
        	
        	//------------------------------------------------------------------
        	// Add new sidebar
        	
            $newid = vs_getfirstunusedid( $config );
            
            // Validate the incoming data (the bare minimum we need is a name)
            if ( $newid > 0 ) {
                // We've obtained a valid ID
                if ( $_POST[ VSBASENAMEFIELD . 'new' ] == "" ) {
                    $errormessage = __( "New virtual sidebar was not added - You must specify a name!", VSTEXTDOMAIN );
                    $blankpost = false;
                }    
            } else {
                $errormessage = __( "Failed to add new sidebar - No available identities", VSTEXTDOMAIN );
                $blankpost = false;
            }

            // If we've gotten to this point with no message, then we're done and we
            // can save the configuration etc.
            if ( $errormessage == "" ) {
                
                // Create the configuration entry
                $barconfig = vs_configarrayfrompost( 'new' );
                $config[ $newid ] = $barconfig;
                    
                // Save it
                vs_saveconfig( $config );
                
                // Register the sidebar - This will ensure it is available immediately.
                // Tthe next page request will register them all using an 'init' hook
                vs_registerbar( $newid, $barconfig );
                
                // Set the feedback message
                $successmessage = __( "New virtual sidebar created", VSTEXTDOMAIN );
    		
                // Clear the data for the form
	        	vs_unsetpost('new');        
            }
            
        } else {
            if ( isset( $_POST[ 'vsdel' ] ) ) {
                //--------------------------------------------------------------
                // Delete a sidebar
                
                $id = $_POST[ 'vsdel' ];
                
                if ( array_key_exists( $id, $config ) ) {
                    // I was going to handle the widgets that may be left in the
                    // sidebar when we delete it, but the widget admin panel
                    // has facilities in it to capture orphaned widgets.
                    
                    unregister_sidebar( $id );
                    unset( $config[ $id ] );
                    
                    $successmessage = sprintf( __( "Sidebar %s deleted", VSTEXTDOMAIN ), $id );
                    
                    vs_saveconfig( $config );
                } else {
                    $errormessage = sprintf( __( "Deletion of sidebar %s failed - Sidebar does not exist!", VSTEXTDOMAIN ), $id );
                }
            } else {
                if ( isset( $_POST[ 'vsupd' ] ) ) {
                    //----------------------------------------------------------
                    // Update the existing sidebars
                    
                    foreach ( $config as $vsid => $vsconfig ) {
                        // For each existing bar, collect a new config array for it
                        $barconfig = vs_configarrayfrompost( $vsid );
                        
                        // Validate the name
                        if ( isset( $barconfig[ 'name' ] ) && ( $barconfig[ 'name' ] != '' ) ) {
                            // We have a name so we can update this bar
                            
                            // Check the whole record for changes
                            $changed = false;
                            foreach ( $barconfig as $key => $val ) {
                                if ( $config[ $vsid][ $key ] != $val ) {
                                    $changed = true;
                                    break;
                                }
                            }
                            
                            // If it has changed, then save it
                            if ( $changed ) {
                                // Remove the existing configuration
                                unset( $config[ $vsid ] );
                            
                                // Store the new one
                                $config[ $vsid ] = $barconfig;
                            
                                vs_addmessage( sprintf( __( "Bar %s updated successfully", VSTEXTDOMAIN ), $vsid ), $successmessage );
                            }
                        } else {
                            vs_addmessage( sprintf( __( "Changes to bar %s not saved - You must specify a name!", VSTEXTDOMAIN ), $vsid ), $errormessage );
                        }
                    }
                    
                    // Save the revised configuration
                    vs_saveconfig( $config );
                } else {
                    $errormessage = __( "Unknown action!", VSTEXTDOMAIN );
                }
            }
        }
    }
    
    // Set out form verification value
    $triggervalue = rand( 1, 65535 );
    update_option( VSTRIGGERSESSIONVAR, $triggervalue );
    
    if ( $blankpost ) {
        // Blank the post variables ready for a new form
        vs_unsetpost( 'new' );
    }

    ?>
<div class="wrap">
<h2><?php _e( 'Virtual Sidebar', VSTEXTDOMAIN ) ?> (<?php printf( __( 'Version. %1$s', VSTEXTDOMAIN ), VSVERSION ) ?>)</h2>
<?php 
    // Handle the messages that can be returned by the request processing
    $includehr = false;
    if ( ( isset( $successmessage ) ) && ( $successmessage != "" ) ) {
        echo '<div class="updated"><p><strong>' . $successmessage . '</strong></p></div>';
        $includehr = true;
    } 
    if ( ( isset( $errormessage ) ) && ( $errormessage != "" ) ) {
        echo '<div class="error"><p><strong>' . $errormessage . '</strong></p></div>';
        $includehr = true;
    } 
    if ( $includehr ) {
        echo '<hr />';
    }
?>
<p><?php _e( 'Using Virtual Sidebar, you can add sidebar content to a post using the shortcode [vs].  Setup the virtual sidebar below, add widgets to it in the normal way and then add the shortcode [vs id="&lt;VSID&gt;"] to your post, where &lt;VSID&gt; is the virtual sidebar ID.', VSTEXTDOMAIN ) ?></p>
<hr />
<script>
function default_vs_values(id) {
    if (id!='new') {
        doit=confirm("<?php _e( "Are you sure?", VSTEXTDOMAINS ); ?>");   
        
    } else {
        doit=true;
    }
    
    if (doit) {
        window.document.forms['vs-form'].elements['<?php echo VSBASECONTAINPREFIELD; ?>'+id].value='<?php echo VSDEFCONTAINPRE; ?>';
        window.document.forms['vs-form'].elements['<?php echo VSBASECONTAINPOSTFIELD; ?>'+id].value='<?php echo VSDEFCONTAINPOST; ?>';
        window.document.forms['vs-form'].elements['<?php echo VSBASEWIDGETPREFIELD; ?>'+id].value='<?php echo VSDEFWIDGETPRE; ?>';
        window.document.forms['vs-form'].elements['<?php echo VSBASEWIDGETPOSTFIELD; ?>'+id].value='<?php echo VSDEFWIDGETPOST; ?>';
        window.document.forms['vs-form'].elements['<?php echo VSBASETITLEPREFIELD; ?>'+id].value='<?php echo VSDEFTITLEPRE; ?>';
        window.document.forms['vs-form'].elements['<?php echo VSBASETITLEPOSTFIELD; ?>'+id].value='<?php echo VSDEFTITLEPOST; ?>';
    }
}
</script>
<form id="vs-form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo VSTRIGGERFIELD; ?>" value="<?php echo $triggervalue; ?>" />
<div class="tablenav"><div class="alignright"><input type="submit" class="button-secondary action" name="vsupd" value="<?php _e( "Save", VSTEXTDOMAIN ); ?>" /></div></div>
<table class="widefat">
    <?php vs_cptableheaders(); ?>
    <tbody>    
    
    <?php

    // Build the list of sidebars    
    // Add 'alternate' class to TR to get alternating row backgrounds
    $row=0;
    
    if ( count( $config ) > 0 ) {
        for ( $vsid = VSFIRSTID; $vsid <= VSLASTID; $vsid++ ) {
            if ( array_key_exists( $vsid, $config ) ) {
                $vs = $config [ $vsid ];
                ?>
    <tr<?php if ( $row % 2 == 0 ) { echo ' class="alternate"'; } ?>>
        <td align="center" style="vertical-align:middle">
            <span class="submit"><input title="<?php _e( "Delete this virtual sidebar", VSTEXTDOMAIN ); ?>" onclick="return confirm('Are you sure you want to delete this sidebar?');" type="submit" name="vsdel" value="<?php echo $vsid; ?>" /></span></td>
        <td style="vertical-align:middle">
            <b><?php _e( "Name", VSTEXTDOMAIN ); ?></b><br />
                <input type="text" name="<?php echo VSBASENAMEFIELD; ?><?php echo $vsid; ?>" style="width:100%" maxlength="32" value="<?php echo $vs[ "name" ]; ?>" /><br />
            <b><?php _e( "Description", VSTEXTDOMAIN ); ?></b><br />
                <input type="text" name="<?php echo VSBASEDESCFIELD; ?><?php echo $vsid; ?>" style="width:100%" maxlength="256" value="<?php echo $vs[ "description" ]; ?>" /><br />
            <a href="javascript:default_vs_values('<?php echo $vsid; ?>')">Restore default wrappers</a>
        </td>
        <td>
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASECONTAINPREFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASECONTAINPREFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "before_contain" ] ); ?></textarea></br>
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASECONTAINPOSTFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASECONTAINPOSTFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "after_contain" ] ); ?></textarea>
        </td>
        <td style="vertical-align:middle">
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASEWIDGETPREFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASEWIDGETPREFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "before_widget" ] ); ?></textarea><br />
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASEWIDGETPOSTFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASEWIDGETPOSTFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "after_widget" ] ); ?></textarea>
        </td>
        <td style="vertical-align:middle">
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASETITLEPREFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASETITLEPREFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "before_title" ] ); ?></textarea><br />
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" id="<?php echo VSBASETITLEPOSTFIELD; ?><?php echo $vsid; ?>" name="<?php echo VSBASETITLEPOSTFIELD; ?><?php echo $vsid; ?>"><?php echo esc_attr( $vs[ "after_title" ] ); ?></textarea>
        </td>
    </tr>
                <?php    
            
                $row=(($row+1)%2);
            }
        } 
    }else {
        ?>
        <tr class="alternate"><td colspan="5" align="center"><?php _e( "No virtual sidebars", VSTEXTDOMAIN ); ?></td></tr>
        <?php
    }
    
    ?>
</table>
<div class="tablenav"><div class="alignright"><input type="submit" class="button-secondary action" name="vsupd" value="<?php _e( "Save", VSTEXTDOMAIN ); ?>" /></div></div>
<h2>Add new virtual sidebar</h2>    
<table class="widefat">
    <?php vs_cptableheaders(); ?>
    <tbody>    
    <tr class="alternate">
        <td align="center" style="vertical-align:middle"><span class="submit"><input type="submit" name="vsnewadd" value="<?php _e( "Add", VSTEXTDOMAIN ); ?>" /></span></td>
        <td style="vertical-align:middle">
            <b><?php _e( "Name", VSTEXTDOMAIN ); ?></b><br />
            <input type="text" name="<?php echo VSBASENAMEFIELD; ?>new" style="width:100%" maxlength="32" value="<?php echo $_POST[ VSBASENAMEFIELD . 'new' ]; ?>" /><br />
            <b><?php _e( "Description", VSTEXTDOMAIN ); ?></b><br />
            <input type="text" name="<?php echo VSBASEDESCFIELD; ?>new" style="width:100%" maxlength="256" value="<?php echo $_POST[ VSBASEDESCFIELD . 'new' ]; ?>" /><br />
            <a href="javascript:default_vs_values('new')">Restore default wrappers</a>
        </td>
        <td>
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASECONTAINPREFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASECONTAINPREFIELD . 'new' ] ); ?></textarea></br>
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASECONTAINPOSTFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASECONTAINPOSTFIELD . 'new' ] ); ?></textarea>
        </td>
        <td>
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASEWIDGETPREFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASEWIDGETPREFIELD . 'new' ] ); ?></textarea></br>
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASEWIDGETPOSTFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASEWIDGETPOSTFIELD . 'new' ] ); ?></textarea>
        </td>
        <td>
            <b><?php _e( "Before", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASETITLEPREFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASETITLEPREFIELD . 'new' ] ); ?></textarea></br>
            <b><?php _e( "After", VSTEXTDOMAIN ); ?></b><br /><textarea rows="3" style="width:100%" name="<?php echo VSBASETITLEPOSTFIELD; ?>new"><?php echo esc_attr( $_POST[ VSBASETITLEPOSTFIELD . 'new' ] ); ?></textarea>
        </td>
    </tr>
    </tbody>
    
</table>
</form>
<hr />
<p></p><?php printf( __( 'For assistance with Virtual Sidebar, post your comments on <a href="%1$s" target="_BLANK">my blog</a>, and to read the on-line user manual visit <a href="%2$s" target="_BLANK">my wiki</a>.  <i>Thanks, AthenaOfDelphi</i>', VSTEXTDOMAIN ), VSBLOGLINK, VSWIKILINK ) ?>
<p><?php printf( __( 'Virtual Sidebar is copyright &copy; 2010-12 Christina Louise Warne (aka <a href="%1$s" target="_BLANK">AthenaOfDelphi</a>)', VSTEXTDOMAIN), VSSITELINK ) ?></p>
</div>
    <?php
}

// Callback to handle the vs shortcode
function vs_getsidebar( $atts ) {
    extract( shortcode_atts( array(
        'id' => '0'
    ), $atts ) );

    $sbid = $atts['id'];
    
    vs_loadconfig( $config );
    
    if ( $sbid != 0 ) {
        // Validate the ID against one of our known ID's
        if ( array_key_exists( $sbid, $config ) ) {
            ob_start();
            dynamic_sidebar( 'vs' . $sbid );
            $content = $config[ $sbid ][ "before_contain" ] . ob_get_contents(). $config[ $sbid ][ "after_contain" ];
            ob_end_clean();
        } else {
            $content = "<b>" . sprintf( __( "Virtual Sidebar - Unknown sidebar ID (%s)", VSTEXTDOMAIN ), $sbid ) . "</b>";
        }
    } else {
        $content = "<b>" . __( "Virtual Sidebar - Specify the sidebar ID using the ID parameter of the shortcode", VSTEXTDOMAIN ) . "</b>";
    }
    
    return $content;
}
add_shortcode( 'vs', 'vs_getsidebar' );

// Initialise the virtual sidebars that are currently configured
function vs_initvirtualsidebars() {
    // Load our configuration
    vs_loadconfig( $config );
    
    for ( $vsid = VSFIRSTID; $vsid <= VSLASTID; $vsid++ ) {
        if ( array_key_exists( $vsid, $config ) ) {
            $vs = $config [ $vsid ];
            
            vs_registerbar( $vsid, $vs );
        }
    }
}
add_action( 'init', 'vs_initvirtualsidebars' );

// Activation hook handler
function vs_activation() {
    // Setup our initial options
    add_option( VSOPTIONS, serialize( array() ), '', true );
    add_option( VSTRIGGERSESSIONVAR, '', '', false );
}
register_activation_hook( __FILE__, 'vs_activation' );  

// Setup localisation
$plugin_dir = dirname( plugin_basename( __FILE__ ) );
load_plugin_textdomain( VSTEXTDOMAIN, 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
  
?>