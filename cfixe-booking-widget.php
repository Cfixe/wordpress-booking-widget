<?php
/*
Plugin Name: Cfixé Booking Widget
Plugin URI: https://cfixe.com/
Description: Extension pour intégrer facilement le widget de prise de rendez-vous Cfixé sur les sites WordPress.
Version: 1.0.0
Author: Cfixé
License: GPLv2
*/

if (!defined('ABSPATH')) exit;

define("CFIXE_BASE_URL", 'https://cfixe.com/');

function cfixe_booking_widget_enqueue_scripts() {
  wp_enqueue_script('cfixe-booking-widget-script', esc_url(constant('CFIXE_BASE_URL')) . 'js/widget.js', array(), '1.0.0', true);
}

function cfixe_booking_widget_options_page() {
  add_options_page(
    'Configuration du Widget',
    'Widget Cfixé',
    'manage_options',
    'cfixe-booking-widget',
    'cfixe_booking_widget_render_options_page'
  );
}


function cfixe_booking_widget_render_options_page() {
  ?>
    <div class="wrap">
        <h1>Paramètres du Widget de réservation Cfixé</h1>
        <form method="post" action="options.php">
          <?php
          settings_fields('cfixe-booking-widget-settings');
          do_settings_sections('cfixe-booking-widget');
          submit_button();
          ?>
        </form>
    </div>
  <?php
}

function cfixe_booking_widget_register_settings() {
  add_settings_section(
    'cfixe-booking-widget-section',
    'Paramètres du Widget Cfixé',
    'cfixe_booking_widget_section_callback',
    'cfixe-booking-widget'
  );

  add_settings_field(
    'cfixe-booking-widget-slug',
    'Votre identifiant chez Cfixé',
    'cfixe_booking_widget_slug_field_callback',
    'cfixe-booking-widget',
    'cfixe-booking-widget-section'
  );

  add_settings_field(
    'cfixe-booking-widget-color-1',
    'Couleur Principale',
    'cfixe_booking_widget_color_1_field_callback',
    'cfixe-booking-widget',
    'cfixe-booking-widget-section'
  );

  add_settings_field(
    'cfixe-booking-widget-color-2',
    'Couleur Secondaire',
    'cfixe_booking_widget_color_2_field_callback',
    'cfixe-booking-widget',
    'cfixe-booking-widget-section'
  );

  register_setting('cfixe-booking-widget-settings', 'cfixe-booking-widget-slug');
  register_setting('cfixe-booking-widget-settings', 'cfixe-booking-widget-color-1');
  register_setting('cfixe-booking-widget-settings', 'cfixe-booking-widget-color-2');
}

function cfixe_booking_widget_section_callback() {
  echo "Vous trouverez l'identifiant à la fin de l'url de votre page Cfixé, par exemple pour " . esc_url(constant('CFIXE_BASE_URL')) . "pro/wm-dev, l'identifiant est wm-dev";
}

function cfixe_booking_widget_slug_field_callback() {
  $slug = get_option('cfixe-booking-widget-slug', '');
  echo '<input type="text" name="cfixe-booking-widget-slug" value="' . esc_attr($slug) . '" />';
}

function cfixe_booking_widget_color_1_field_callback() {
  $color_1 = get_option('cfixe-booking-widget-color-1', '');
  echo '<input type="color" id="color1_picker" name="cfixe-booking-widget-color-1" value="' . esc_attr($color_1) . '" />';
  echo '<input type="text" id="color1_text" name="cfixe-booking-widget-color-1-text" value="' . esc_attr($color_1) . '" />';
}

function cfixe_booking_widget_color_2_field_callback() {
  $color_2 = get_option('cfixe-booking-widget-color-2', '');
  echo '<input type="color" id="color2_picker" name="cfixe-booking-widget-color-2" value="' . esc_attr($color_2) . '" />';
  echo '<input type="text" id="color2_text" name="cfixe-booking-widget-color-2-text" value="' . esc_attr($color_2) . '" />';
}

function cfixe_booking_widget_shortcode($atts) {
  $slug = get_option('cfixe-booking-widget-slug', '');
  $color_1 = get_option('cfixe-booking-widget-color-1', '');
  $color_2 = get_option('cfixe-booking-widget-color-2', '');

  $widget_html = '<div id="cfixe-booking-widget" data-slug="' . esc_attr($slug) . '"';
  $widget_html .= ' data-color-1="' . esc_attr(str_replace("#", "", $color_1)) . '"';
  $widget_html .= ' data-color-2="' . esc_attr(str_replace("#", "", $color_2)) . '"';
  $widget_html .= '><a href="' . esc_url(constant('CFIXE_BASE_URL')) . 'pro/' . esc_attr($slug) . '">Prenez rendez-vous directement sur ma page Cfixé</a></div>';

  return $widget_html;
}

function cfixe_enqueue_admin_scripts() {
  echo "
  <script type='text/javascript'>
    document.addEventListener('DOMContentLoaded', function() {
      let color1Picker = document.getElementById('color1_picker');
      let color1Text = document.getElementById('color1_text');
      let color2Picker = document.getElementById('color2_picker');
      let color2Text = document.getElementById('color2_text');

      color1Picker.addEventListener('change', function() {
        color1Text.value = color1Picker.value;
      });

      color1Text.addEventListener('change', function() {
        color1Picker.value = color1Text.value;
      });

      color2Picker.addEventListener('change', function() {
        color2Text.value = color2Picker.value;
      });

      color2Text.addEventListener('change', function() {
        color2Picker.value = color2Text.value;
      });
    });
  </script>
  ";
}

function cfixe_admin_styles() {
  echo "
    <style>
        #color1_picker, #color2_picker {
            vertical-align: middle;
        }
        #color1_text, #color2_text {
            margin-left: 10px; 
            vertical-align: middle;
            height: 28px; 
            padding: 0 8px;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
        }
    </style>
    ";
}

add_action('admin_menu', 'cfixe_booking_widget_options_page');
add_action('admin_init', 'cfixe_booking_widget_register_settings');
add_action('wp_enqueue_scripts', 'cfixe_booking_widget_enqueue_scripts');
add_action('admin_footer', 'cfixe_enqueue_admin_scripts');
add_shortcode('cfixe_booking_widget', 'cfixe_booking_widget_shortcode');
add_action('admin_head', 'cfixe_admin_styles');