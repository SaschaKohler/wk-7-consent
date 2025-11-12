<?php
/**
 * Admin Settings for WK7 Consent Manager
 * 
 * @package WK7_Consent_Manager
 * @author Sascha Kohler <office@sascha-kohler.at>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register settings, section and fields for the admin page.
 */
function wine_k7_consent_admin_init() {
    register_setting( 'wine_k7_consent_settings_group', WK7_CONSENT_OPTION_NAME, 'wine_k7_consent_sanitize_options' );

    // Main Section
    add_settings_section(
        'wine_k7_consent_main_section',
        __( 'Allgemeine Einstellungen', 'wine-k7-consent' ),
        function() {
            echo '<p>' . esc_html__( 'Konfiguriere IDs, Region und Links für das Consent-Banner.', 'wine-k7-consent' ) . '</p>';
        },
        'wine_k7_consent_settings'
    );

    $main_fields = array(
        'gtm_id' => __( 'Google Tag Manager ID', 'wine-k7-consent' ),
        'ga4_id' => __( 'GA4 Measurement ID', 'wine-k7-consent' ),
        'region' => __( 'Region', 'wine-k7-consent' ),
        'policy_url' => __( 'Datenschutzerklärung URL', 'wine-k7-consent' ),
        'imprint_url' => __( 'Impressum URL', 'wine-k7-consent' ),
        'storage_key' => __( 'Storage Key', 'wine-k7-consent' ),
        'show_fab' => __( 'Floating Button anzeigen', 'wine-k7-consent' ),
        'disable_fab_for_chatbot' => __( 'Floating Button für Chatbot freigeben', 'wine-k7-consent' ),
        'fab_label' => __( 'Button-Label', 'wine-k7-consent' ),
        'fab_position' => __( 'Floating Button Position', 'wine-k7-consent' ),
        'show_footer_link' => __( 'Footer-Link anzeigen', 'wine-k7-consent' ),
        'fab_avatar' => __( 'FAB Avatar', 'wine-k7-consent' ),
    );

    foreach ( $main_fields as $key => $label ) {
        add_settings_field(
            'wine_k7_consent_' . $key,
            $label,
            'wine_k7_consent_field_' . $key,
            'wine_k7_consent_settings',
            'wine_k7_consent_main_section'
        );
    }

    // Texts Section
    add_settings_section(
        'wine_k7_consent_texts_section',
        __( 'Texte (UI)', 'wine-k7-consent' ),
        function() { echo '<p>' . esc_html__( 'Passe die Texte im Cookie-Banner an.', 'wine-k7-consent' ) . '</p>'; },
        'wine_k7_consent_settings'
    );

    $text_fields = array(
        'txt_title' => __( 'Titel', 'wine-k7-consent' ),
        'txt_text' => __( 'Beschreibungstext', 'wine-k7-consent' ),
        'txt_btn_accept_all' => __( 'Button „Alle akzeptieren"', 'wine-k7-consent' ),
        'txt_btn_reject_all' => __( 'Button „Nur Notwendige"', 'wine-k7-consent' ),
        'txt_btn_save' => __( 'Button „Auswahl speichern"', 'wine-k7-consent' ),
        'txt_link_policy' => __( 'Linktext Datenschutzerklärung', 'wine-k7-consent' ),
        'txt_link_imprint' => __( 'Linktext Impressum', 'wine-k7-consent' ),
    );

    foreach ( $text_fields as $key => $label ) {
        add_settings_field(
            'wine_k7_consent_' . $key,
            $label,
            'wine_k7_consent_field_text_generic',
            'wine_k7_consent_settings',
            'wine_k7_consent_texts_section',
            array( 'key' => $key )
        );
    }

    // Categories Section
    add_settings_section(
        'wine_k7_consent_categories_section',
        __( 'Kategorien', 'wine-k7-consent' ),
        function() { echo '<p>' . esc_html__( 'Passe Labels und Beschreibungen der Kategorien an.', 'wine-k7-consent' ) . '</p>'; },
        'wine_k7_consent_settings'
    );

    $cat_fields = array(
        'lbl_necessary' => __( 'Label: Notwendig', 'wine-k7-consent' ),
        'lbl_analytics' => __( 'Label: Statistiken', 'wine-k7-consent' ),
        'lbl_marketing' => __( 'Label: Marketing', 'wine-k7-consent' ),
        'lbl_functional' => __( 'Label: Funktional', 'wine-k7-consent' ),
        'desc_necessary' => __( 'Beschreibung: Notwendig', 'wine-k7-consent' ),
        'desc_analytics' => __( 'Beschreibung: Statistiken', 'wine-k7-consent' ),
        'desc_marketing' => __( 'Beschreibung: Marketing', 'wine-k7-consent' ),
        'desc_functional' => __( 'Beschreibung: Funktional', 'wine-k7-consent' ),
    );

    foreach ( $cat_fields as $key => $label ) {
        $callback = strpos( $key, 'desc_' ) === 0 ? 'wine_k7_consent_field_textarea_generic' : 'wine_k7_consent_field_text_generic';
        add_settings_field(
            'wine_k7_consent_' . $key,
            $label,
            $callback,
            'wine_k7_consent_settings',
            'wine_k7_consent_categories_section',
            array( 'key' => $key )
        );
    }

    // Appearance Section
    add_settings_section(
        'wine_k7_consent_appearance_section',
        __( 'Darstellung', 'wine-k7-consent' ),
        function() { echo '<p>' . esc_html__( 'Farben und Position des Banners. Theme-Farben werden automatisch erkannt.', 'wine-k7-consent' ) . '</p>'; },
        'wine_k7_consent_settings'
    );

    $appearance_fields = array(
        'auto_detect_colors' => __( 'Theme-Farben automatisch erkennen', 'wine-k7-consent' ),
        'ui_position' => __( 'Banner-Position', 'wine-k7-consent' ),
        'ui_primary_color' => __( 'Primärfarbe', 'wine-k7-consent' ),
        'ui_text_color' => __( 'Textfarbe', 'wine-k7-consent' ),
        'ui_background_color' => __( 'Hintergrundfarbe', 'wine-k7-consent' ),
        'ui_template' => __( 'Banner-Template', 'wine-k7-consent' ),
    );

    foreach ( $appearance_fields as $key => $label ) {
        add_settings_field(
            'wine_k7_consent_' . $key,
            $label,
            'wine_k7_consent_field_' . $key,
            'wine_k7_consent_settings',
            'wine_k7_consent_appearance_section'
        );
    }
}
add_action( 'admin_init', 'wine_k7_consent_admin_init' );

/**
 * Add settings page under Settings.
 */
function wine_k7_consent_admin_menu() {
    add_options_page(
        __( 'WK7 Consent Manager', 'wk7-consent' ),
        __( 'WK7 Consent', 'wk7-consent' ),
        'manage_options',
        'wine_k7_consent_settings',
        'wine_k7_consent_render_settings_page'
    );
}
add_action( 'admin_menu', 'wine_k7_consent_admin_menu' );

/**
 * Enqueue admin assets.
 */
function wine_k7_consent_admin_assets( $hook ) {
    if ( $hook !== 'settings_page_wine_k7_consent_settings' ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'wk7-consent', WK7_CONSENT_URL . 'assets/consent.css', array(), WK7_CONSENT_VERSION );
    wp_enqueue_media();
    wp_enqueue_script( 'wk7-consent-admin', WK7_CONSENT_URL . 'assets/admin.js', array( 'wp-color-picker', 'jquery' ), WK7_CONSENT_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'wine_k7_consent_admin_assets' );

/**
 * Settings page renderer.
 */
function wine_k7_consent_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    $theme_colors = wine_k7_consent_detect_theme_colors();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <div class="notice notice-info">
            <p>
                <strong><?php esc_html_e( 'Hinweise', 'wk7-consent' ); ?></strong><br><br>
                
                <strong><?php esc_html_e( 'Erkannte Theme-Farben:', 'wk7-consent' ); ?></strong><br>
                <?php esc_html_e( 'Primärfarbe:', 'wk7-consent' ); ?> <code><?php echo esc_html( $theme_colors['primary'] ); ?></code> |
                <?php esc_html_e( 'Textfarbe:', 'wk7-consent' ); ?> <code><?php echo esc_html( $theme_colors['text'] ); ?></code> |
                <?php esc_html_e( 'Hintergrund:', 'wk7-consent' ); ?> <code><?php echo esc_html( $theme_colors['background'] ); ?></code><br><br>
                
                <strong><?php esc_html_e( 'Features:', 'wk7-consent' ); ?></strong> 
                <?php esc_html_e( 'GDPR-konform, Google Consent Mode v2, GTM & GA4 Integration, 4 Banner-Templates', 'wk7-consent' ); ?><br><br>
                
                <?php esc_html_e( 'Shortcode:', 'wk7-consent' ); ?> <code>[wine_k7_consent_preferences label="Cookie-Einstellungen"]</code>
            </p>
        </div>
        
        <div style="background: #fff3cd; border-left: 4px solid #ff6b35; padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #ff6b35;">
                <span class="dashicons dashicons-admin-plugins" style="font-size: 20px; vertical-align: middle;"></span>
                <?php esc_html_e( 'Über dieses Plugin', 'wk7-consent' ); ?>
            </h3>
            <p style="margin: 10px 0;">
                <strong>WK7 Consent Manager v<?php echo esc_html( WK7_CONSENT_VERSION ); ?></strong><br>
                <?php esc_html_e( 'Entwickelt von', 'wk7-consent' ); ?> <strong>Sascha Kohler</strong> – <?php esc_html_e( 'Kongruenz-Mentor & Web-Entwickler', 'wk7-consent' ); ?>
            </p>
            <p style="margin: 10px 0;">
                <span class="dashicons dashicons-email" style="color: #ff6b35;"></span> 
                <a href="mailto:office@sascha-kohler.at">office@sascha-kohler.at</a> &nbsp;|&nbsp;
                <span class="dashicons dashicons-admin-site" style="color: #ff6b35;"></span> 
                <a href="https://sascha-kohler.at" target="_blank">sascha-kohler.at</a> &nbsp;|&nbsp;
                <span class="dashicons dashicons-share" style="color: #ff6b35;"></span> 
                <a href="https://www.linkedin.com/in/sascha-kohler" target="_blank">LinkedIn</a>
            </p>
            <p style="font-size: 12px; color: #666; margin: 10px 0 0 0;">
                <?php esc_html_e( 'Dieses Plugin ist wiederverwendbar für alle WordPress/Divi Websites. GTM/GA4 IDs können auch über Konstanten definiert werden (siehe README.md).', 'wk7-consent' ); ?>
            </p>
        </div>
        
        <form method="post" action="options.php">
            <?php
                settings_fields( 'wine_k7_consent_settings_group' );
                do_settings_sections( 'wine_k7_consent_settings' );
                submit_button();
            ?>
        </form>
        
        <hr />
        <h2><?php esc_html_e( 'Live-Vorschau', 'wk7-consent' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Klicke auf den Button, um eine Vorschau des Cookie-Banners zu sehen.', 'wk7-consent' ); ?></p>
        <button type="button" class="button button-secondary" id="wk7-consent-toggle-preview">
            <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span>
            <?php esc_html_e( 'Vorschau anzeigen', 'wk7-consent' ); ?>
        </button>
        <div id="wk7-consent-admin-preview" style="position:relative; min-height:220px; margin-top:15px; display:none;"></div>
        
        <script>
        jQuery(document).ready(function($) {
            var previewVisible = false;
            $('#wk7-consent-toggle-preview').on('click', function() {
                var $preview = $('#wk7-consent-admin-preview');
                var $button = $(this);
                
                if (previewVisible) {
                    $preview.slideUp();
                    $button.html('<span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span> <?php esc_html_e( 'Vorschau anzeigen', 'wk7-consent' ); ?>');
                    previewVisible = false;
                } else {
                    $preview.slideDown();
                    $button.html('<span class="dashicons dashicons-hidden" style="vertical-align: middle;"></span> <?php esc_html_e( 'Vorschau ausblenden', 'wk7-consent' ); ?>');
                    previewVisible = true;
                }
            });
        });
        </script>
    </div>
    <?php
}
