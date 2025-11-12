<?php
/**
 * Plugin Name:       WK7 Consent Manager
 * Plugin URI:        https://vision.sascha-kohler.at
 * Description:       GDPR-konformer Cookie-Banner mit Google Consent Mode v2 für GTM/GA4. Erkennt automatisch Theme-Farben.
 * Version:           1.2.0
 * Author:            Sascha Kohler
 * Author URI:        https://vision.sascha-kohler.at
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Text Domain:       wk7-consent
 * Domain Path:       /languages
 * 
 * @package           WK7_Consent_Manager
 * @author            Sascha Kohler <office@sascha-kohler.at>
 * @copyright         2025 Sascha Kohler
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
if ( ! defined( 'WK7_CONSENT_VERSION' ) ) {
    define( 'WK7_CONSENT_VERSION', '1.2.0' );
}
if ( ! defined( 'WK7_CONSENT_URL' ) ) {
    define( 'WK7_CONSENT_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WK7_CONSENT_PATH' ) ) {
    define( 'WK7_CONSENT_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WK7_CONSENT_OPTION_NAME' ) ) {
    define( 'WK7_CONSENT_OPTION_NAME', 'wk7_consent_options' );
}
if ( ! defined( 'WK7_CONSENT_PLUGIN_FILE' ) ) {
    define( 'WK7_CONSENT_PLUGIN_FILE', __FILE__ );
}

/**
 * Auto-detect theme colors from active theme
 */
function wine_k7_consent_detect_theme_colors() {
    $theme_colors = array(
        'primary' => '#ff6b35',
        'text' => '#111111',
        'background' => '#ffffff',
    );

    // Try to detect from theme customizer
    $primary_color = get_theme_mod( 'primary_color' );
    if ( $primary_color ) {
        $theme_colors['primary'] = $primary_color;
    }

    // Try to detect from CSS variables in active theme
    $theme_slug = get_stylesheet();
    
    // Wine K7 specific colors
    if ( strpos( $theme_slug, 'wine-k7' ) !== false ) {
        $theme_colors = array(
            'primary' => '#ff6b35',
            'text' => '#ededed',
            'background' => '#0a0a0a',
        );
    }

    /**
     * Filter: wine_k7_consent_theme_colors
     * Allow themes to override detected colors
     */
    return apply_filters( 'wine_k7_consent_theme_colors', $theme_colors );
}

/**
 * Default options for the plugin settings.
 */
function wine_k7_consent_get_default_options() {
    $theme_colors = wine_k7_consent_detect_theme_colors();
    
    return array(
        'gtm_id'      => '',
        'ga4_id'      => '',
        'region'      => 'eu',
        'policy_url'  => home_url( '/datenschutz' ),
        'imprint_url' => home_url( '/impressum' ),
        'storage_key' => 'wk7_consent',
        'show_fab'    => 1,
        'disable_fab_for_chatbot' => 0,
        'fab_label'   => __( 'Cookie-Einstellungen', 'wine-k7-consent' ),
        'fab_position'=> 'left', // left|right
        'show_footer_link' => 0,
        // Banner texts
        'txt_title'   => __( 'Wir respektieren deine Privatsphäre', 'wine-k7-consent' ),
        'txt_text'    => __( 'Wir verwenden Cookies, um unsere Website zu verbessern. Du kannst selbst entscheiden, welche Kategorien du zulassen möchtest.', 'wine-k7-consent' ),
        'txt_btn_accept_all' => __( 'Alle akzeptieren', 'wine-k7-consent' ),
        'txt_btn_reject_all' => __( 'Nur Notwendige', 'wine-k7-consent' ),
        'txt_btn_save'       => __( 'Auswahl speichern', 'wine-k7-consent' ),
        'txt_link_policy'    => __( 'Datenschutzerklärung', 'wine-k7-consent' ),
        'txt_link_imprint'   => __( 'Impressum', 'wine-k7-consent' ),
        // Category labels
        'lbl_necessary' => __( 'Notwendig', 'wine-k7-consent' ),
        'lbl_analytics' => __( 'Statistiken', 'wine-k7-consent' ),
        'lbl_marketing' => __( 'Marketing', 'wine-k7-consent' ),
        'lbl_functional' => __( 'Funktional', 'wine-k7-consent' ),
        // Category descriptions
        'desc_necessary' => __( 'Erforderlich für die Grundfunktionen der Website.', 'wine-k7-consent' ),
        'desc_analytics' => __( 'Hilft uns zu verstehen, wie Besucher die Website nutzen (z. B. GA4).', 'wine-k7-consent' ),
        'desc_marketing' => __( 'Wird verwendet, um personalisierte Werbung anzuzeigen.', 'wine-k7-consent' ),
        'desc_functional' => __( 'Verbessert Funktionen, z. B. Einbettungen.', 'wine-k7-consent' ),
        // Appearance (auto-detected from theme)
        'ui_position' => 'bottom', // bottom|top
        'ui_primary_color' => $theme_colors['primary'],
        'ui_text_color' => $theme_colors['text'],
        'ui_background_color' => $theme_colors['background'],
        'ui_auto_detect_colors' => 1, // Auto-detect theme colors
        // Templates and FAB avatar
        'ui_template' => 'template1', // template1|template2|template3|template4
        'fab_avatar_id' => 0,
    );
}

/**
 * Retrieve merged options (saved options overriding defaults).
 */
function wine_k7_consent_get_options() {
    $saved = get_option( WK7_CONSENT_OPTION_NAME, array() );
    $defaults = wine_k7_consent_get_default_options();
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }
    $merged = wp_parse_args( $saved, $defaults );
    
    // If auto-detect is enabled, refresh theme colors
    if ( ! empty( $merged['ui_auto_detect_colors'] ) ) {
        $theme_colors = wine_k7_consent_detect_theme_colors();
        $merged['ui_primary_color'] = $theme_colors['primary'];
        $merged['ui_text_color'] = $theme_colors['text'];
        $merged['ui_background_color'] = $theme_colors['background'];
    }
    
    return $merged;
}

/**
 * Enqueue frontend assets and inject minimal gtag stub + default consent (denied) early.
 */
function wine_k7_consent_enqueue_assets() {
    $css_version = WK7_CONSENT_VERSION;
    $js_version  = WK7_CONSENT_VERSION;

    $css_path = WK7_CONSENT_PATH . 'assets/consent.css';
    $js_path  = WK7_CONSENT_PATH . 'assets/consent.js';

    if ( file_exists( $css_path ) ) {
        $css_version = (string) filemtime( $css_path );
    }
    if ( file_exists( $js_path ) ) {
        $js_version = (string) filemtime( $js_path );
    }

    // CSS
    wp_enqueue_style(
        'wk7-consent',
        WK7_CONSENT_URL . 'assets/consent.css',
        array(),
        $css_version
    );

    // JS
    wp_enqueue_script(
        'wk7-consent',
        WK7_CONSENT_URL . 'assets/consent.js',
        array(),
        $js_version,
        true
    );

    // Pass settings to JS (merged with options)
    $opts = wine_k7_consent_get_options();
    $settings = array(
        'storageKey' => sanitize_key( $opts['storage_key'] ),
        'region' => in_array( $opts['region'], array( 'eu', 'us', 'auto' ), true ) ? $opts['region'] : 'eu',
        'policyUrl' => esc_url( $opts['policy_url'] ),
        'imprintUrl' => esc_url( $opts['imprint_url'] ),
        'i18n' => array(
            'title' => $opts['txt_title'],
            'text' => $opts['txt_text'],
            'btnAcceptAll' => $opts['txt_btn_accept_all'],
            'btnRejectAll' => $opts['txt_btn_reject_all'],
            'btnSave' => $opts['txt_btn_save'],
            'linkPolicy' => $opts['txt_link_policy'],
            'linkImprint' => $opts['txt_link_imprint'],
            'categories' => array(
                'necessary' => $opts['lbl_necessary'],
                'analytics' => $opts['lbl_analytics'],
                'marketing' => $opts['lbl_marketing'],
                'functional' => $opts['lbl_functional'],
            ),
            'desc' => array(
                'necessary' => $opts['desc_necessary'],
                'analytics' => $opts['desc_analytics'],
                'marketing' => $opts['desc_marketing'],
                'functional' => $opts['desc_functional'],
            ),
        ),
        'ui' => array(
            'position' => in_array( $opts['ui_position'], array( 'bottom', 'top' ), true ) ? $opts['ui_position'] : 'bottom',
            'primaryColor' => $opts['ui_primary_color'],
            'textColor' => $opts['ui_text_color'],
            'backgroundColor' => $opts['ui_background_color'],
            'fabPosition' => in_array( $opts['fab_position'], array( 'left', 'right' ), true ) ? $opts['fab_position'] : 'left',
            'showFooterLink' => ! empty( $opts['show_footer_link'] ),
            'template' => in_array( $opts['ui_template'], array( 'template1','template2','template3','template4' ), true ) ? $opts['ui_template'] : 'template1',
            'fabAvatar' => ( ! empty( $opts['fab_avatar_id'] ) ? esc_url( wp_get_attachment_image_url( (int) $opts['fab_avatar_id'], 'thumbnail' ) ) : '' ),
        ),
    );
    wp_localize_script( 'wk7-consent', 'WK7ConsentSettings', $settings );
}
add_action( 'wp_enqueue_scripts', 'wine_k7_consent_enqueue_assets' );

/**
 * Output gtag stub and default denied consent in the head as early as possible.
 */
function wine_k7_consent_head_gtag_stub() {
    ?>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      // Consent Mode v2 default (deny non-essential)
      gtag('consent', 'default', {
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'functionality_storage': 'granted',
        'security_storage': 'granted',
        'wait_for_update': 500
      });
    </script>
    <?php
}
add_action( 'wp_head', 'wine_k7_consent_head_gtag_stub', 1 );

/**
 * Get GTM and GA4 IDs via constants or filters.
 */
function wine_k7_consent_get_gtm_id() {
    $opts = wine_k7_consent_get_options();
    $id = ! empty( $opts['gtm_id'] ) ? $opts['gtm_id'] : ( defined( 'WINE_K7_GTM_ID' ) ? WINE_K7_GTM_ID : '' );
    /**
     * Filter: wine_k7_consent_gtm_id
     * Return a string like 'GTM-XXXXXXX' to enable GTM output.
     */
    return apply_filters( 'wine_k7_consent_gtm_id', $id );
}

function wine_k7_consent_get_ga4_id() {
    $opts = wine_k7_consent_get_options();
    $id = ! empty( $opts['ga4_id'] ) ? $opts['ga4_id'] : ( defined( 'WINE_K7_GA4_ID' ) ? WINE_K7_GA4_ID : '' );
    /**
     * Filter: wine_k7_consent_ga4_id
     * Return a GA4 Measurement ID like 'G-XXXXXXXX' to enable gtag output.
     */
    return apply_filters( 'wine_k7_consent_ga4_id', $id );
}

/**
 * Output GTM script (head) after consent stub, if ID present.
 */
function wine_k7_consent_output_gtm_head() {
    $gtm = wine_k7_consent_get_gtm_id();
    if ( ! $gtm ) return;
    ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_js( $gtm ); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action( 'wp_head', 'wine_k7_consent_output_gtm_head', 2 );

/**
 * Output GTM noscript (body). Uses wp_body_open hook.
 */
function wine_k7_consent_output_gtm_noscript() {
    $gtm = wine_k7_consent_get_gtm_id();
    if ( ! $gtm ) return;
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm ); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action( 'wp_body_open', 'wine_k7_consent_output_gtm_noscript', 1 );

/**
 * Output GA4 gtag.js loader + config after consent stub, if GA4 ID present.
 * This is optional; usually GTM is preferred. If both are set, both will load.
 */
function wine_k7_consent_output_ga4() {
    $ga4 = wine_k7_consent_get_ga4_id();
    if ( ! $ga4 ) return;
    ?>
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4 ); ?>"></script>
    <script>
      // gtag stub already defined above. Configure GA4 after consent defaults.
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js( $ga4 ); ?>', { 'anonymize_ip': true });
    </script>
    <!-- End Google Analytics 4 -->
    <?php
}
add_action( 'wp_head', 'wine_k7_consent_output_ga4', 3 );

/**
 * Render the banner container in footer if consent not stored yet.
 */
function wine_k7_consent_render_banner_container() {
    echo '<div id="wk7-consent-banner-root" class="wk7-hidden" aria-hidden="true"></div>';
}
add_action( 'wp_footer', 'wine_k7_consent_render_banner_container' );

/**
 * Floating bottom-left preferences button
 */
function wine_k7_consent_render_fab() {
    $opts = wine_k7_consent_get_options();
    $show_default = ( ! empty( $opts['show_fab'] ) && empty( $opts['disable_fab_for_chatbot'] ) );
    $show = apply_filters( 'wine_k7_consent_show_fab', $show_default );
    if ( ! $show ) return;

    $label_default = ! empty( $opts['fab_label'] ) ? $opts['fab_label'] : __( 'Cookie-Einstellungen', 'wk7-consent' );
    $label = apply_filters( 'wine_k7_consent_fab_label', $label_default );
    $pos_class = ( ! empty( $opts['fab_position'] ) && $opts['fab_position'] === 'right' ) ? 'wk7-consent-fab-right' : 'wk7-consent-fab-left';
    $avatar_url = ! empty( $opts['fab_avatar_id'] ) ? wp_get_attachment_image_url( (int) $opts['fab_avatar_id'], 'thumbnail' ) : '';
    $button_classes = 'wk7-fab-primary';
    $button_content = esc_html( $label );

    if ( $avatar_url ) {
        $button_classes .= ' wk7-fab-avatar-only';
        $button_content = '<img class="wk7-fab-avatar" src="' . esc_url( $avatar_url ) . '" alt="" />';
    }

    echo '<div class="wk7-consent-fab ' . esc_attr( $pos_class ) . '" aria-hidden="false">'
        . '<button type="button" class="' . esc_attr( $button_classes ) . '" aria-label="' . esc_attr( $label ) . '"'
        . ' onclick="window.WK7Consent && window.WK7Consent.openPreferences && window.WK7Consent.openPreferences(); return false;">'
        . $button_content
        . '</button>'
        . '</div>';
}
add_action( 'wp_footer', 'wine_k7_consent_render_fab', 98 );

/**
 * Shortcode to render a link/button to open the consent preferences dialog.
 * Usage: [wine_k7_consent_preferences label="Cookie-Einstellungen"]
 */
function wine_k7_consent_shortcode_preferences( $atts ) {
    $atts = shortcode_atts( array(
        'label' => __( 'Cookie-Einstellungen', 'wk7-consent' ),
        'class' => 'wk7-btn wk7-btn-outline',
    ), $atts, 'wine_k7_consent_preferences' );

    $label = esc_html( $atts['label'] );
    $class = esc_attr( $atts['class'] );

    return '<button type="button" class="' . $class . '" onclick="window.WK7Consent && window.WK7Consent.openPreferences && window.WK7Consent.openPreferences(); return false;">' . $label . '</button>';
}
add_shortcode( 'wine_k7_consent_preferences', 'wine_k7_consent_shortcode_preferences' );

// Include admin files only in admin context
if ( is_admin() ) {
    require_once WK7_CONSENT_PATH . 'includes/admin-fields.php';
    require_once WK7_CONSENT_PATH . 'includes/admin-sanitize.php';
    require_once WK7_CONSENT_PATH . 'includes/admin-settings.php';
}
