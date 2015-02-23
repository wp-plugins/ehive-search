<?php
/*
	Plugin Name: eHive Search
	Plugin URI: http://developers.ehive.com/wordpress-plugins/
	Author: Vernon Systems limited
	Description: Search and display results from eHive. The <a href="http://developers.ehive.com/wordpress-plugins#ehiveaccess" target="_blank">eHiveAccess plugin</a> must be installed.
	Version: 2.1.5
	Author URI: http://vernonsystems.com
	License: GPL2+
*/
/*
	Copyright (C) 2012 Vernon Systems Limited

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
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if (in_array('ehive-access/EHiveAccess.php', (array) get_option('active_plugins', array()))) {
	
	define('EHIVE_SEARCH_PLUGIN_DIR', plugin_dir_url( __FILE__ ));
	
    class eHiveSearch {
    	
    	const CURRENT_VERSION = 1; // Increment each time an upgrade is required / options added or deleted.
    	const EHIVE_SEARCH_OPTIONS = "ehive_search_options";   

        function __construct() {
                        
            add_action("admin_init", array(&$this, "ehive_search_admin_options_init"));
            add_action("admin_menu", array(&$this, "ehive_search_admin_menu"));

            add_action( 'wp_print_styles', array(&$this,'enqueue_styles'));        
            add_shortcode('ehive_search', array(&$this, 'ehive_search_shortcode'));
        }
        
        function ehive_search_admin_options_init(){
        	
        	$this->ehive_plugin_update();
        	 
        	wp_register_style($handle = 'eHiveAdminCSS', $src = plugins_url('eHiveAdmin.css', '/ehive-access/css/eHiveAdmin.css'));
        	
        	wp_enqueue_script( 'jquery' );
        	
        	wp_enqueue_style( 'farbtastic' );
        	wp_enqueue_script( 'farbtastic' );
        	
        	wp_register_script($handle = 'eHiveSearchOptions', $src = plugins_url('options.js', '/ehive-search/js/options.js'), $deps = array('jquery'), $ver = '1.0.0', false);
        	wp_enqueue_script( 'eHiveSearchOptions' );
        	 
        	register_setting('ehive_search_options', 'ehive_search_options', array(&$this, 'ehive_search_options_validate') );
        	 
        	add_settings_section('comment_section', '', array(&$this, 'comment_section_text_fn'), __FILE__);
        	 
        	add_settings_section('general_options_section', 'General options', array(&$this, 'general_options_section_fn'), __FILE__);
        
        	add_settings_section('result_views_section', 'Result views', array(&$this, 'result_views_section_fn'), __FILE__);
        	 
        	add_settings_section('list_view_section', 'List view', array(&$this, 'list_view_section_fn'), __FILE__);
        	 
        	add_settings_section('lightbox_view_section', 'Lightbox view', array(&$this, 'lightbox_view_section_fn'), __FILE__);
        	 
        	add_settings_section('advanced_options_section', 'Advanced', array(&$this, 'advanced_options_fn'), __FILE__);
        	 
        	add_settings_section('css_section', 'CSS - stylesheet', array(&$this, 'css_section_fn'), __FILE__);
        	 
        	add_settings_section('css_inline_section', 'CSS - inline', array(&$this, 'css_inline_section_fn'), __FILE__);
        	 
        }
        
        function ehive_search_admin_enqueue_styles() {
        	wp_enqueue_style('eHiveAdminCSS');
        }
        
        function ehive_search_options_validate($input) {
        	add_settings_error('ehive_search_options', 'updated', 'eHive Search settings saved.', 'updated');
        	return $input;
        }
        
        function comment_section_text_fn() {
        	echo "<p><em>An overview of the plugin and shortcode documentation is available in the help.</em></p>";
        }
        
        function general_options_section_fn() {
        	add_settings_field('limit', 'Limit', array(&$this, 'limit_fn'), __FILE__, 'general_options_section');
        	add_settings_field('show_catalogue_type_icon', 'Show catalogue type icon', array(&$this, 'show_catalogue_type_icon_fn'), __FILE__, 'general_options_section');
        	add_settings_field('hide_search_form_enabled', 'Hide the search form', array(&$this, 'hide_search_form_enabled_fn'), __FILE__, 'general_options_section');
        }
        
        function result_views_section_fn() {
        	$items = array(	'results_view_lightbox_enabled' => 'lightbox',
        					'results_view_list_enabled' 	=> 'list'
        				  );
        
        	echo '<div class="result-view-left-label"></div>';
        	echo '<div class="result-view-middle-label">Label</div>';
        	echo '<div class="result-view-right-label">Default</div>';
        
        	foreach ($items as $key => $value) {
        		add_settings_field($key, $value, array(&$this, "result_views_options_fn"), __FILE__, 'result_views_section', array($key, $value));
        	}
        	add_settings_field('show_powered_by_ehive', 'Show "Powered by eHive" logo', array(&$this, 'show_powered_by_ehive_fn'), __FILE__, 'result_views_section');
        }
        
        function list_view_section_fn() {
        	$items = array(	'list_object_number_enabled' => 'Object Number',
		        			'list_name_enabled' => 'Name/Title',
		        			'list_primary_creator_maker_enabled' => 'Primary Creator/Maker',
		        			'list_primary_creator_maker_role_enabled' => 'Primary Creator/Maker Role',
		        			'list_taxonomic_classification_enabled' => 'Taxonomic Classification',
		        			'list_taxonomic_type_indicator_enabled' => 'Taxonomic Type Indicator',
		        			'list_field_collector_enabled' => 'Field Collector',
		        			'list_web_public_description_enabled' => 'About this Object',
		        			'list_date_made_enabled' => 'Date made',
		        			'list_place_made_enabled' => 'Place made',
		        			'list_object_type_enabled' => 'Object Type',
		        			'list_medium_description_enabled' => 'Medium and Materials',
		        			'list_measurement_description_enabled' => 'Measurements',
		        			'list_named_collection_enabled' => 'Collection',
		        			'list_credit_line_enabled' => 'Credit Line',
        	);
        
        
        	foreach ($items as $key => $value) {
        		echo '<tr>';
        		add_settings_field($key, $value, array(&$this, "list_view_options_fn"), __FILE__, 'list_view_section', $key);
        		echo '</tr>';
        	}
        	add_settings_field('show_public_profile_name', 'Public profile name', array(&$this, "list_view_options_fn"), __FILE__, 'list_view_section', 'show_public_profile_name');
        }
        
        function lightbox_view_section_fn() {
        	$items = array(	'lightbox_object_number_enabled' => 'Object Number',
        			'lightbox_name_enabled' => 'Name/Title',
        			'lightbox_primary_creator_maker_enabled' => 'Primary Creator/Maker',
        			'lightbox_primary_creator_maker_role_enabled' => 'Primary Creator/Maker Role',
        			'lightbox_taxonomic_classification_enabled' => 'Taxonomic Classification',
        			'lightbox_taxonomic_type_indicator_enabled' => 'Taxonomic Type Indicator',
        			'lightbox_field_collector_enabled' => 'Field Collector',
        			'lightbox_web_public_description_enabled' => 'About this Object',
        			'lightbox_date_made_enabled' => 'Date made',
        			'lightbox_place_made_enabled' => 'Place made',
        			'lightbox_object_type_enabled' => 'Object Type',
        			'lightbox_medium_description_enabled' => 'Medium and Materials',
        			'lightbox_measurement_description_enabled' => 'Measurements',
        			'lightbox_named_collection_enabled' => 'Collection',
        			'lightbox_credit_line_enabled' => 'Credit Line',
        	);
        
        	echo '<div class="field-label">Field</div>';
        	echo '<div class="label-label">Label</div>';
        	echo '<div class="enabled-label">Enabled</div>';
        
        	foreach ($items as $key => $value) {
        		add_settings_field($key, $value, array(&$this, "lightbox_view_options_fn"), __FILE__, 'lightbox_view_section', array($key, $value));
        	}
        	add_settings_field('lightbox_columns', 'Columns', array(&$this, 'lightbox_columns_fn'), __FILE__, 'lightbox_view_section');
        	add_settings_field('lightbox_more_link', 'Add "more..." link to search result items', array(&$this, 'lightbox_more_link_fn'), __FILE__, 'lightbox_view_section');
        }
        
        function advanced_options_fn() {
        	add_settings_field('query_var', 'Search query URL patameter name', array(&$this, 'query_var_fn'), __FILE__, 'advanced_options_section');
        	add_settings_field('page_var', 'Search page URL patameter name', array(&$this, 'page_var_fn'), __FILE__, 'advanced_options_section');
        }
        
        function css_section_fn() {
        	add_settings_field('css_class', 'Custom class selector', array(&$this, 'css_class_fn'), __FILE__, 'css_section');
        	add_settings_field('plugin_css_enabled', 'Enable plugin stylesheet', array(&$this, 'plugin_css_enabled_fn'), __FILE__, 'css_section');
        }
        
        function css_inline_section_fn() {
        	add_settings_field('item_background_colour', 'Item background colour', array(&$this, 'item_background_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('item_border_colour', 'Item border colour', array(&$this, 'item_border_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('item_border_width', 'Item border width', array(&$this, 'item_border_width_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_background_colour', 'Image background colour', array(&$this, 'image_background_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_padding', 'Image padding', array(&$this, 'image_padding_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_border_colour', 'Image border colour', array(&$this, 'image_border_colour_fn'), __FILE__, 'css_inline_section');
        	add_settings_field('image_border_width', 'Image border width', array(&$this, 'image_border_width_fn'), __FILE__, 'css_inline_section');
        }
        
        //
        //	GENERAL OPTIONS SECTION
        //
        function limit_fn() {
        	$options = get_option('ehive_search_options');
        	echo "<input class='small-text' id='limit' name='ehive_search_options[limit]' type='number' value='{$options['limit']}' />";
        }
        	
        function show_catalogue_type_icon_fn() {
        	$options = get_option('ehive_search_options');
        	if($options['show_catalogue_type_icon']) {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input {$checked} id='show_catalogue_type_icon' name='ehive_search_options[show_catalogue_type_icon]' type='checkbox' />";
        }
        
        function hide_search_form_enabled_fn() {
	        $options = get_option('ehive_search_options');
	        if(isset($options['hide_search_form_enabled']) && $options['hide_search_form_enabled'] == 'on') {
	        	$checked = ' checked="checked" ';
	        }
        	echo "<input {$checked} id='hide_search_form_enabled' name='ehive_search_options[hide_search_form_enabled]' type='checkbox' />";
        	echo '<p>Use the "Hide search form" option in conjunction with an eHive plugin or widget that instigates a search.<br />(eHive Search widget, eHive Objects Tag Cloud widget or plugin, eHive Objects Gallery Widget)';
        }
        
        //
        //	RESULTS VIEW SECTION
        //
        function result_views_options_fn($keyValuePair) {
        	$options = get_option('ehive_search_options');
        
        	$key = $keyValuePair[0];
        	$value = $keyValuePair[1];
        
        	if ($options[$key]) {
        		$checked =	' checked="checked" ';
        	}
        
        	if ($options['results_view_default'] == $value) {
        		$checkedDefault =	' checked="checked" ';
        	}
        
        	echo "<input {$checked} id='{$key}' name='ehive_search_options[{$key}]' type='checkbox' />";
        	echo "<td><input {$checkedDefault} id='{$key}_default' name='ehive_search_options[results_view_default]' type='radio' value='{$value}' /></td>";
        }
        
        function show_powered_by_ehive_fn() {
        	$options = get_option('ehive_search_options');
        	if($options['show_powered_by_ehive'] == 'on') {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input ".$checked." id='show_powered_by_ehive' name='ehive_search_options[show_powered_by_ehive]' type='checkbox' />";
        }
        
        //
        //	LIST VIEW SECTION
        //
        function list_view_options_fn($key) {
        	$options = get_option('ehive_search_options');
        	if($options[$key]) {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input ".$checked." id='{$key}' name='ehive_search_options[{$key}]' type='checkbox' />";
        }
        
        //
        //	LIGHTBOX VIEW SECTION
        //
        function lightbox_columns_fn() {
        	$options = get_option('ehive_search_options');
        	echo "<input class='small-text' id='lightbox_columns' name='ehive_search_options[lightbox_columns]' type='number' value='{$options['lightbox_columns']}' />";
        }
        
        function lightbox_more_link_fn() {
        	$options = get_option('ehive_search_options');
        	if(isset($options['lightbox_more_link_enabled'])) {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input id='lightbox_more_link_text' name='ehive_search_options[lightbox_more_link_text]' size='40' type='text' value='{$options['lightbox_more_link_text']}' />";
        	echo "<td><input $checked id='lightbox_more_link_enabled' name='ehive_search_options[lightbox_more_link_enabled]' type='checkbox' value='{$options['lightbox_more_link_enabled']}' /></td>";
        }
        	 
        function lightbox_view_options_fn($keyValuePair) {
        	$options = get_option('ehive_search_options');
        	$key = $keyValuePair[0];
        	$value = $keyValuePair[1];
        	if($options[$key]) {
        		$checked = ' checked="checked" ';
        	}
        	echo "<input id='{$key}_label' name='ehive_search_options[{$key}_label]' size='40' type='text' value='{$options[$key.'_label']}' />";
        	echo "<td><input ".$checked." id='{$key}' name='ehive_search_options[{$key}]' type='checkbox' /></td>";
        }
        	
        //
        //	ADVANCED OPTIONS SECTION
        //
        function query_var_fn() {
        	$options = get_option('ehive_search_options');
        	echo "<input class='medium-text' id='query_var' name='ehive_search_options[query_var]' type='text' value='{$options['query_var']}' />";
        }
        
        function page_var_fn() {
        	$options = get_option('ehive_search_options');
        	echo "<input class='medium-text' id='page_var' name='ehive_search_options[page_var]' type='text' value='{$options['page_var']}' />";
		}
        
		//
		//	CSS OPTIONS SECTION
		//
		function css_class_fn() {
			$options = get_option('ehive_search_options');
			echo "<input class='medium-text' id='css_class' name='ehive_search_options[css_class]' type='text' value='{$options['css_class']}' />";
			echo '<p>Adds a class name to the ehive-search div.';
		}
		
		function plugin_css_enabled_fn() {
		$options = get_option('ehive_search_options');
			if($options['plugin_css_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input {$checked} id='plugin_css_enabled' name='ehive_search_options[plugin_css_enabled]' type='checkbox' />";
		}
		
		//
		//	INLINE CSS OPTIONS SECTION
		//
		function item_background_colour_fn() {
			$options = get_option('ehive_search_options');
			if(isset($options['item_background_colour_enabled']) && $options['item_background_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='item_background_colour' name='ehive_search_options[item_background_colour]' type='text' value='{$options['item_background_colour']}' />";
			echo '<div id="item_background_colourpicker"></div>';
			echo "<td><input ".$checked." id='item_background_colour_enabled' name='ehive_search_options[item_background_colour_enabled]' type='checkbox' /></td>";
			
			
			$options = get_option('ehive_search_options');
			$listViewEnabled = false;
			if(isset($options['results_view_list_enabled']) && $options['results_view_list_enabled'] == 'on') {
				echo '<td rowspan="12"><img src="'.EHIVE_SEARCH_PLUGIN_DIR.'/images/search_item_list.png" /></td>';
				$listViewEnabled = true;
			}
			 
			if (isset($options['results_view_lightbox_enabled']) && $options['results_view_lightbox_enabled'] == 'on') {
				if ($listViewEnabled) {
					$bothEnabledCssClass = 'both-ehive-views-enabled';
				}
				echo '<td rowspan="12"><img src="'.EHIVE_SEARCH_PLUGIN_DIR.'images/search_item_lightbox.png" /></td>';
			}						
			
		}
		
        function item_border_colour_fn() {
        	$options = get_option('ehive_search_options');
			if(isset($options['item_border_colour_enabled']) && $options['item_border_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='item_border_colour' name='ehive_search_options[item_border_colour]' type='text' value='{$options['item_border_colour']}' />";
			echo '<div id="item_border_colourpicker"></div>';
			echo "<td><input ".$checked." id='item_border_colour_enabled' name='ehive_search_options[item_border_colour_enabled]' type='checkbox' /></td>";
		}
		
		function item_border_width_fn() {
        	$options = get_option('ehive_search_options');
			if(isset($options['item_border_width_enabled']) && $options['item_border_width_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='small-text' id='item_border_width' name='ehive_search_options[item_border_width]' type='number' value='{$options['item_border_width']}' />";
		}
		
		function image_background_colour_fn() {
			$options = get_option('ehive_search_options');
			if(isset($options['image_background_colour_enabled']) && $options['image_background_colour_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='image_background_colour' name='ehive_search_options[image_background_colour]' type='text' value='{$options['image_background_colour']}' />";
			echo '<div id="image_background_colourpicker"></div>';
			echo "<td><input ".$checked." id='image_background_colour_enabled' name='ehive_search_options[image_background_colour_enabled]' type='checkbox' /></td>";
		}
		
		function image_padding_fn() {
			$options = get_option('ehive_search_options');
			if(isset($options['image_padding_enabled']) && $options['image_padding_enabled'] == 'on') {
				$checked = ' checked="checked" ';
			}
			echo "<input class='small-text' id='image_padding' name='ehive_search_options[image_padding]' type='number' value='{$options['image_padding']}' />";
			echo "<td><input ".$checked." id='image_padding_enabled' name='ehive_search_options[image_padding_enabled]' type='checkbox' /></td>";
		}
		
		function image_border_colour_fn() {
			$options = get_option('ehive_search_options');
			if(isset($options['image_border_colour_enabled']) && $options['image_border_colour_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
			}
			echo "<input class='medium-text' id='image_border_colour' name='ehive_search_options[image_border_colour]' type='text' value='{$options['image_border_colour']}' />";
			echo '<div id="image_border_colourpicker"></div>';
			echo "<td rowspan='2'><input ".$checked." id='image_border_colour_enabled' name='ehive_search_options[image_border_colour_enabled]' type='checkbox' /></td>";
		}
		
		function image_border_width_fn() {
        	$options = get_option('ehive_search_options');
        	if(isset($options['image_border_width_enabled']) && $options['image_border_width_enabled'] == 'on') {
        		$checked = ' checked="checked" ';
			}
        	echo "<input class='small-text' id='image_border_width' name='ehive_search_options[image_border_width]' type='number' value='{$options['image_border_width']}' />";
		}
		
        function ehive_search_admin_menu() {
        	 
        	global $ehive_search_options_page;
        	 
        	$ehive_search_options_page = add_submenu_page('ehive_access', 'eHive search', 'Search', 'manage_options', 'ehive_search', array(&$this, 'ehive_search_options_page'));
        
        	add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'ehive_search_plugin_action_links'), 10, 2);
        	 
        	add_action("admin_print_styles-" . $ehive_search_options_page, array(&$this, "ehive_search_admin_enqueue_styles") );
        
        	add_action("load-$ehive_search_options_page",array(&$this, "ehive_search_options_help"));
        }
        
        function ehive_search_plugin_action_links($links, $file) {
        	$settings_link = '<a href="admin.php?page=ehive_search">' . __('Settings') . '</a>';
        	array_unshift($links, $settings_link); // before other links
        	return $links;
        }
                
        public function enqueue_styles() {
        	$options = get_option('ehive_search_options');
        
        	if ($options[plugin_css_enabled] == 'on') {
        		wp_register_style($handle = 'eHiveSearchCSS', $src = plugins_url('eHiveSearch.css', '/ehive-search/css/eHiveSearch.css'), $deps = array(), $ver = '0.0.1', $media = 'all');
        		wp_enqueue_style( 'eHiveSearchCSS');
        	}
        }
        
        function ehive_search_options_help() {
        	global $ehive_search_options_page;
        
        	$screen = get_current_screen();
        	if ($screen->id != $ehive_search_options_page) {
        		return;
        	}
        	
        	$screen->add_help_tab(array('id'      => 'ehive-search-overview',
        								'title'   => 'Overview',
        								'content' => "<p>Search and display results from eHive.",
        	));
        	
        	$htmlShortcode = "<p><strong>Shortcode</strong> [ehive_search]</p>";
        	$htmlShortcode.= "<p><strong>Attributes:</strong></p>";
        	$htmlShortcode.= "<ul>";
        	
        	$htmlShortcode.= '<li><strong>css_class</strong> - Adds a custom class selector to the plugin markup.</li>';
        	 
        	$htmlShortcode.= '<p><strong>Examples:</strong></p>';
        	$htmlShortcode.= '<p>[ehive_search]<br/>Shortcode with no attributes. Attributes default to the options settings.</p>';
        	$htmlShortcode.= '<p>[ehive_search css_class="myClass"]<br/>Add a custom class selector "myClass" to the markup of the plugin.</p>';
        	$htmlShortcode.= "</ul>";
        	 
        	$screen->add_help_tab(array('id'	  => 'ehive-search-shortcode',
        								'title'	  => 'Shortcode',
        								'content' => $htmlShortcode
        	));
        	 
        	$screen->set_help_sidebar('<p><strong>For more information:</strong></p><p><a href="http://developers.ehive.com/wordpress-plugins#ehivesearch" target="_blank">Documentation for eHive plugins</a></p>');
        }
        
        function ehive_search_options_page() {
        	?>
           	<div class="wrap">
           		<div class="icon32" id="icon-options-ehive"><br></div>
              		<h2>eHive Search Settings</h2>
              		<?php settings_errors();?>        		
               		<form action="options.php" method="post">
               			<?php settings_fields('ehive_search_options'); ?>
               			<?php do_settings_sections(__FILE__); ?>
               			<p class="submit">
               				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
               			</p>
               		</form>
               	</div>
           	<?php
        }
                
        public function getSearchOptions() {
        	return get_option('ehive_search_options');
        }
        
        public function ehive_search_shortcode($atts) {
        	global $eHiveAccess;
        	
        	$options = get_option('ehive_search_options');    

        	extract(shortcode_atts(array('css_class' 						=> array_key_exists('css_class', $options) ? $options['css_class'] : '',
        								 'item_background_colour'			=> array_key_exists('item_background_colour', $options) ? $options['item_background_colour'] : '#f3f3f3',
										 'item_background_colour_enabled'	=> array_key_exists('item_background_colour_enabled', $options) ? $options['item_background_colour_enabled'] : 'on',
										 'item_border_colour'				=> array_key_exists('item_border_colour', $options) ? $options['item_border_colour'] : '#666666',
										 'item_border_colour_enabled'		=> array_key_exists('item_border_colour_enabled', $options) ? $options['item_border_colour_enabled'] : '',
										 'item_border_width' 				=> array_key_exists('item_border_width', $options) ? $options['item_border_width'] : '2',
										 'image_background_colour'			=> array_key_exists('image_background_colour', $options) ? $options['image_background_colour'] : '#ffffff',
										 'image_background_colour_enabled'	=> array_key_exists('image_background_colour_enabled', $options) ? $options['image_background_colour_enabled'] : 'on',
										 'image_padding' 					=> array_key_exists('image_padding', $options) ? $options['image_padding'] : '1',
										 'image_padding_enabled' 			=> array_key_exists('image_padding_enabled', $options) ? $options['image_padding_enabled'] : 'on',
										 'image_border_colour'				=> array_key_exists('image_border_colour', $options) ? $options['image_border_colour'] : '#666666',
										 'image_border_colour_enabled'		=> array_key_exists('image_border_colour_enabled', $options) ? $options['image_border_colour_enabled'] : 'on',
										 'image_border_width' 				=> array_key_exists('image_border_width', $options) ? $options['image_border_width'] : '2'), $atts));
        	         	        	
        	$resultsViewLightboxEnabled = $options['results_view_lightbox_enabled'] == 'on'? true : false;
        	$resultsViewListEnabled = $options['results_view_list_enabled'] == 'on' ? true : false;
        	$resultsViewPosterboardEnabled = $options['results_view_posterboard_enabled'] == 'on' ? true : false;  
        	$resultsViewDefault = $options['results_view_default'];      
        		
        	$poweredByEhiveEnabled = $options['show_powered_by_ehive'] == 'on' ? true : false;
        	
        	$queryAll = ehive_get_var('all', false);
        	        	
        	$query = ehive_get_var( $options['query_var'], false);
        	
        	$query = rawurldecode($query);        	
        	$query = stripslashes_deep( $query );        	
        	        	
            $page = ehive_get_var($options['page_var'], 1) - 1;
            $offset = $page * $options['limit'];
            
            $a = ehive_get_var('a' ,false);
            
            try {
	            if (!$query == false || $queryAll == true) {
	            
	           		$siteType = $eHiveAccess->getSiteType();
	            	$accountId = $eHiveAccess->getAccountId();
	            	$communityId = $eHiveAccess->getCommunityId();
	            	$searchPrivateRecords = $eHiveAccess->getSearchPrivateRecords();
	            	
	            	$eHiveApi = $eHiveAccess->eHiveApi();
	
	            	$hasImages = false;
	            	$sort = null;
	            	$direction = null;
	            	
					switch($siteType){
					case 'Account':						
						if ($searchPrivateRecords) {
							$objectRecordsCollection = $eHiveApi->getObjectRecordsInAccount( $accountId, $query, $hasImages, $sort, $direction, $offset, $options['limit'], 'any' );
						} else {
							$objectRecordsCollection = $eHiveApi->getObjectRecordsInAccount( $accountId, $query, $hasImages, $sort, $direction, $offset, $options['limit'], 'public' );
						}						
						break;

					case 'Community':						
						if ($a) {
							$objectRecordsCollection = $eHiveApi->getObjectRecordsInAccountInCommunity($communityId, $a, $query, $hasImages, $sort, $direction, $offset, $options['limit'] );
						} else {
							$objectRecordsCollection = $eHiveApi->getObjectRecordsInCommunity( $communityId, $query, $hasImages, $sort, $direction, $offset, $options['limit'] );
						}						
						break;
					default: 
						$objectRecordsCollection = $eHiveApi->getObjectRecordsInEHive( $query, $hasImages, $sort, $direction, $offset, $options['limit'] );
					}         
	            }
            } catch (Exception $exception) {
            	error_log('EHive Search plugin returned and error while accessing the eHive API: ' . $exception->getMessage());
            	$eHiveApiErrorMessage = " ";
            	if ($eHiveAccess->getIsErrorNotificationEnabled()) {
            		$eHiveApiErrorMessage = $eHiveAccess->getErrorMessage();
            	}
            }
            
            $template = locate_template(array('eHiveSearch.php'));
            if ('' == $template) {
            	$template = "templates/eHiveSearch.php";
            }
            
            ob_start();
            require($template);
            return apply_filters('ehive_search', ob_get_clean());           
        }

        
        private function do_search($query, $offset = 0) {
        	$options = get_option('ehive_search_options');
        	
            global $eHiveAccess;
            $eHiveApi = $eHiveAccess->eHiveApi();
            $objectSummariesCollection = $eHiveApi->getObjects($query, $offset, $options['page_size']);
            return $objectSummariesCollection;           
        }

        
        function query_vars($vars) {
            $vars[] = $this->options['query_var'];
            $vars[] = $this->options['page_var'];
            return $vars;
        }

        function add_rewrite_rules($rules) {
            global $eHiveAccess, $wp_rewrite;

            $pageId = $eHiveAccess->getSearchPageId();
            
            if ($pageId != 0) {
	            $page = get_post( $pageId );	            

	            $queryToken = '%eHiveQuery%';
	            
	            $wp_rewrite->add_rewrite_tag($queryToken, '([^/]+)', "pagename={$page->post_name}&eHive_query=");
	            	            
	            $rules = $wp_rewrite->generate_rewrite_rules($wp_rewrite->root . "/{$page->post_name}/$queryToken") + $rules;
            }
            return $rules;            
        }
        
        //
        //	Setup the plugin options, handle upgrades to the plugin.
        //
        function ehive_plugin_update() {
        	
        	// Add the default options.
        	if ( get_option(self::EHIVE_SEARCH_OPTIONS) === false ) {

				$options = array("update_version"=>self::CURRENT_VERSION,
								 "plugin_css_enabled"=>"on",
								 "css_class"=>"",
								 "item_background_colour"=>"#f3f3f3",
								 "item_background_colour_enabled"=>'on',
								 "item_border_colour"=>"#666666",
						 		 "item_border_colour_enabled"=>'',
								 "item_border_width"=>"2",
								 "image_background_colour"=>"#ffffff",
								 "image_background_colour_enabled"=>'on',
								 "image_padding"=>"1",
								 "image_padding_enabled"=>"on",
								 "image_border_colour"=>"#666666",
								 "image_border_colour_enabled"=>'on',
								 "image_border_width"=>"2",
								 "limit"=>12,
								 "hide_search_form_enabled"=>'',
								 "view"=>"summary",
								 "query_var"=>"eHive_query",
								 "page_var"=>"eHive_page",
								 "results_view_default"=>"list",
								 "results_view_list_enabled"=>'on',
								 "results_view_lightbox_enabled"=>'',
								 'list_object_number_enabled' => 'on',
								 'list_name_enabled' => 'on',
								 'list_primary_creator_maker_enabled' => '',
								 'list_primary_creator_maker_role_enabled' => '',
								 'list_taxonomic_classification_enabled' => '',
								 'list_taxonomic_type_indicator_enabled' => '',
								 'list_field_collector_enabled' => '',
								 'list_web_public_description_enabled' => '',
								 'list_date_made_enabled' => '',
								 'list_place_made_enabled' => '',
								 'list_object_type_enabled' => '',
								 'list_medium_description_enabled' => '',
								 'list_measurement_description_enabled' => '',
								 'list_named_collection_enabled' => '',
								 'list_credit_line_enabled' => '',
								 'show_public_profile_name' => 'on',
								 "show_powered_by_ehive"=>'on',
								 "lightbox_date_made_enabled"=>'on',
								 "lightbox_field_collection_description_enabled"=>'on',
								 "lightbox_isbn_issn_enabled"=>'on',
								 "lightbox_name_enabled"=>'on',
								 "lightbox_object_number_enabled"=>'on',
								 "lightbox_place_made_enabled"=>'on',
								 "lightbox_primary_creator_maker_enabled"=>'on',
								 "lightbox_specimen_category_enabled"=>'on',
								 "lightbox_taxonomic_classification_enabled"=>'on',
								 "lightbox_taxonomic_type_indicator_enabled"=>'on',
								 "lightbox_date_made_label"=>"Date made:",
								 "lightbox_field_collection_description_label"=>"Collection description:",
								 "lightbox_isbn_issn_label"=>"ISBN ISSN:",
								 "lightbox_name_label"=>"Name:",
								 "lightbox_object_number_label"=>"Object number:",
								 "lightbox_place_made_label"=>"Place made:",
								 "lightbox_primary_creator_maker_label"=>"Primary creator maker:",
								 "lightbox_specimen_category_label"=>"Specimen category:",
								 "lightbox_taxonomic_classification_label"=>"Taxonomic classification:",
								 "lightbox_taxonomic_type_indicator_label"=>"Taxonomic type indicator:",
								 "lightbox_columns" => 3,
								 "lightbox_more_link_enabled" => 'on',
								 "lightbox_more_link_text" => "more..." );
				 
				update_option(self::EHIVE_SEARCH_OPTIONS, $options);
        		 
			} else {

				$options = get_option(self::EHIVE_SEARCH_OPTIONS);
				
				if ( array_key_exists("update_version", $options)) {
					$updateVersion = $options["update_version"];
				} else {
					$updateVersion = 0;
				}
				
				if ( $updateVersion == self::CURRENT_VERSION ) {
					// Nothing to do.
				}  else {
				
					if ( $updateVersion == 0 ) {							
						$updateVersion = 1;
					}
						
					// End of the update chain, save the options to the database.
					$options["update_version"] = self::CURRENT_VERSION;
					update_option(self::EHIVE_SEARCH_OPTIONS, $options);
				}
			}        	
        }
                
        public function activate() {
        }

        public function deactivate() {
        }
    }

    $eHiveSearch = new EHiveSearch();

    add_filter('query_vars', array(&$eHiveSearch, 'query_vars'));
    add_filter('rewrite_rules_array', array(&$eHiveSearch, 'add_rewrite_rules'));
    
    add_action('activate_ehive-search/EHiveSearch.php', array(&$eHiveSearch, 'activate'));
    add_action('deactivate_ehive-search/EHiveSearch.php', array(&$eHiveSearch, 'deactivate'));
}
?>