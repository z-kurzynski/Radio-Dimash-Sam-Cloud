<?php
/*
Plugin Name: RadioDimah Player
Description: Odtwarzacz radia z ustawieniami kolorów i shortcode.
Version: 5.1
Author: zk
Author URI: https://radiodimash.pl/
License: GPL2
Text Domain: radiodimah-player
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// Funkcja odtwarzacza (shortcode)
function radiodimah_radio_player() {
    $settings = get_option('radiodimah_radio_settings', array());
    $backgroundTop = isset($settings['backgroundTop']) ? $settings['backgroundTop'] : '#474747';
    $backgroundBottom = isset($settings['backgroundBottom']) ? $settings['backgroundBottom'] : '#5f5f5f';
    $widgetBorder = isset($settings['widgetBorder']) ? $settings['widgetBorder'] : '#5f5f5f';
    $dividers = isset($settings['dividers']) ? $settings['dividers'] : '#5f5f5f';
    $buttons = isset($settings['buttons']) ? $settings['buttons'] : '#bebebe';
    $text = isset($settings['text']) ? $settings['text'] : '#bebebe';

    foreach (compact('backgroundTop','backgroundBottom','widgetBorder','dividers','buttons','text') as $k => $v) {
        if ( ! preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $v) ) {
            ${$k} = '#000000'; // fallback
        }
    }

    // URL strumienia - możesz zmieniać lub pobierać z ustawień
    $stream_url = isset($settings['stream_url']) ? $settings['stream_url'] : 'https://stream.radiodimash.pl/stream.mp3';

    $html = <<<HTML
<script type="module" src="//samcloudmedia.spacial.com/webwidgets/widget/v6/sam-widgets/sam-widgets.esm.js"></script>
<sam-widget type="player" station-id="125052"
	token="23900a2375edc9d8d421a43fefc9fca3d22ccc0d"
	playlist-id="undefined"
	anim-type='focus-in-expand' easing='ease-in-out' refresh-interval='30s'
	station-refresh-interval='default' theme-border-radius='square' image-border-radius='rounded'
	theme='{
		"backgroundTop":"{$backgroundTop}",
		"backgroundBottom":"{$backgroundBottom}",
		"widgetBorder":"{$widgetBorder}",
		"dividers":"{$dividers}",
		"buttons":"{$buttons}",
		"text":"{$text}"
	}'
>
</sam-widget>
<script>
document.addEventListener("DOMContentLoaded", function() {
  var player = document.querySelector("sam-widget");
  if (player) {
    // Możesz dodać obsługę odtwarzania, pauzy, itp.
  }
});
</script>
HTML;

    return $html;
}
add_shortcode('radio_player', 'radiodimah_radio_player');


// Panel ustawień
function radiodimah_register_settings() {
    register_setting('radiodimah_radio_options', 'radiodimah_radio_settings');

    add_action('admin_post_radiodimah_reset_defaults', function() {
        if ( ! check_admin_referer('radiodimah_reset_defaults') ) wp_die('Nieprawidłowa autoryzacja');
        update_option('radiodimah_radio_settings', array(
            'backgroundTop' => '#474747',
            'backgroundBottom' => '#5f5f5f',
            'widgetBorder' => '#5f5f5f',
            'dividers' => '#5f5f5f',
            'buttons' => '#bebebe',
            'text' => '#bebebe',
            'stream_url' => 'https://stream.radiodimash.pl/stream.mp3',
        ));
        wp_redirect( admin_url('options-general.php?page=radiodimah-player') );
        exit;
    });
}
add_action('admin_init', 'radiodimah_register_settings');


// Strona ustawień w menu
function radiodimah_add_menu() {
    add_options_page(
        'RadioDimah Player',
        'RadioDimah Player',
        'manage_options',
        'radiodimah-player',
        'radiodimah_settings_page'
    );
}
add_action('admin_menu', 'radiodimah_add_menu');

function radiodimah_settings_page() {
    $settings = get_option('radiodimah_radio_settings', array());
    $defaults = array(
        'backgroundTop' => '#474747',
        'backgroundBottom' => '#5f5f5f',
        'widgetBorder' => '#5f5f5f',
        'dividers' => '#5f5f5f',
        'buttons' => '#bebebe',
        'text' => '#bebebe',
        'stream_url' => 'https://stream.radiodimash.pl/stream.mp3',
    );
    $settings = wp_parse_args( $settings, $defaults );
    ?>
    <div class="wrap">
        <h1>Ustawienia RadioDimah Player</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('radiodimah_radio_options');
            do_settings_sections('radiodimah_radio_settings');

            ?>
            <table class="form-table">
                <tr>
                    <th><label for="stream_url">URL strumienia:</label></th>
                    <td><input type="url" id="stream_url" name="radiodimah_radio_settings[stream_url]" value="<?php echo esc_attr($settings['stream_url']); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <th><label for="backgroundTop">Tło górne:</label></th>
                    <td><input type="color" id="backgroundTop" name="radiodimah_radio_settings[backgroundTop]" value="<?php echo esc_attr($settings['backgroundTop']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="backgroundBottom">Tło dolne:</label></th>
                    <td><input type="color" id="backgroundBottom" name="radiodimah_radio_settings[backgroundBottom]" value="<?php echo esc_attr($settings['backgroundBottom']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="widgetBorder">Obramowanie widgetu:</label></th>
                    <td><input type="color" id="widgetBorder" name="radiodimah_radio_settings[widgetBorder]" value="<?php echo esc_attr($settings['widgetBorder']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="dividers">Podziałki:</label></th>
                    <td><input type="color" id="dividers" name="radiodimah_radio_settings[dividers]" value="<?php echo esc_attr($settings['dividers']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="buttons">Przyciski:</label></th>
                    <td><input type="color" id="buttons" name="radiodimah_radio_settings[buttons]" value="<?php echo esc_attr($settings['buttons']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="text">Tekst:</label></th>
                    <td><input type="color" id="text" name="radiodimah_radio_settings[text]" value="<?php echo esc_attr($settings['text']); ?>"></td>
                </tr>
            </table>
            <?php submit_button('Zapisz ustawienia'); ?>
        </form>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="radiodimah_reset_defaults" />
            <?php wp_nonce_field('radiodimah_reset_defaults'); ?>
            <input type="submit" class="button button-secondary" value="Przywróć domyślne" />
        </form>
    </div>
    <?php
}
?>