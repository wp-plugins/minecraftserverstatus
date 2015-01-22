<?php

/*
Plugin Name: MinecraftServerStatus
Plugin URI: https://github.com/WarewolfCZ/MinecraftServerStatus
Description: MinecraftServerStatus widget is displaying MC server status, version and player count
Version: 1.0.0
Author: WarewolfCZ
Author URI: http://www.warewolf.cz
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/* 
Copyright (C) 2014 WarewolfCZ

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
defined('ABSPATH') or die("No script kiddies please!");

require_once("core/MCServer.php");
require_once("core/exception/MCException.php");

add_option("server-url", "", null, 'yes');
add_option("server-port", "", null, 'yes');

load_plugin_textdomain('mss_widget_domain', false, basename( dirname( __FILE__ ) ) . '/languages');

// Creating the widget 
class MssWidget extends WP_Widget {

    function MssWidget() {
        $widget_ops = array( 'classname' => 'example', 'description' => __('Status of minecraft server', 'mss_widget_domain') );
        $this->WP_Widget( 'mss_widget', __('MinecraftServerStatus', 'mss_widget_domain'), $widget_ops);//, $control_ops );
    }

    // widget front-end
    public function widget($args, $instance) {
        $server = new MCServer($instance['host'], $instance['port'], 5);
        $status = NULL;
        //TODO: move status query to separate script
        try {
            $status = $server->status();
        } catch (Exception $ex) {
            $status = NULL;
        }
        require dirname(__FILE__) . '/templates/widget.phtml';
    }

    // widget Backend 
    public function form($instance) {
        $defaults = array(
            'title' => 'Server status',
            'host' => 'yourserver.com',
            'port' => '25565',
            'show_status' => 'on',
            'show_host' => 'on',
            'show_port' => 'on',
            'show_ping' => 'on',
            'show_players' => 'on',
            'show_auto_players' => '',
            'show_version' => 'on',
            'show_plugins' => 'on',
            'avatar_size' => '25'
        );
        
        if (empty($instance)) {
            $instance = wp_parse_args( (array) $instance, $defaults );
        }
         
        // Widget admin form
        require dirname(__FILE__) . '/templates/form.phtml';
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        //$instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        foreach ($new_instance as $option => $value) {

            if((int) $value > 0 && !in_array($option, array('host'))) {
                $value = (int) $value;
            }
            $instance[$option] = strip_tags(trim($value));
        }
        return $instance;
    }

    private function hydrate(array $defaults, array $instance) {
        foreach ($defaults as $key => $val) {
            if (!array_key_exists($key, $instance)) {
                $instance[$key] = "";
            }
        }
        return $instance;
    }
}

// Class mss_widget ends here
// Register and load the widget
function mss_load_widget() {
    register_widget('MssWidget');
}

add_action('widgets_init', 'mss_load_widget');
