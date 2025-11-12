<?php
/**
 * Admin Field Renderers for Wine K7 Consent Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Main fields
function wine_k7_consent_field_gtm_id() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text" name="%1$s[gtm_id]" value="%2$s" placeholder="GTM-XXXXXXX" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['gtm_id'] )
    );
}

function wine_k7_consent_field_ga4_id() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text" name="%1$s[ga4_id]" value="%2$s" placeholder="G-XXXXXXXX" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['ga4_id'] )
    );
}

function wine_k7_consent_field_region() {
    $opts = wine_k7_consent_get_options();
    $value = in_array( $opts['region'], array( 'eu', 'us', 'auto' ), true ) ? $opts['region'] : 'eu';
    ?>
    <select name="<?php echo esc_attr( WK7_CONSENT_OPTION_NAME ); ?>[region]">
        <option value="eu" <?php selected( $value, 'eu' ); ?>><?php esc_html_e( 'EU', 'wine-k7-consent' ); ?></option>
        <option value="us" <?php selected( $value, 'us' ); ?>><?php esc_html_e( 'US', 'wine-k7-consent' ); ?></option>
        <option value="auto" <?php selected( $value, 'auto' ); ?>><?php esc_html_e( 'Auto', 'wine-k7-consent' ); ?></option>
    </select>
    <?php
}

function wine_k7_consent_field_policy_url() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="url" class="regular-text" name="%1$s[policy_url]" value="%2$s" placeholder="%3$s" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['policy_url'] ),
        esc_attr( home_url( '/datenschutz' ) )
    );
}

function wine_k7_consent_field_imprint_url() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="url" class="regular-text" name="%1$s[imprint_url]" value="%2$s" placeholder="%3$s" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['imprint_url'] ),
        esc_attr( home_url( '/impressum' ) )
    );
}

function wine_k7_consent_field_storage_key() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text" name="%1$s[storage_key]" value="%2$s" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['storage_key'] )
    );
    echo '<p class="description">' . esc_html__( 'Lokaler Speicher-Key (nur Kleinbuchstaben, Zahlen und Unterstrich).', 'wine-k7-consent' ) . '</p>';
}

function wine_k7_consent_field_show_fab() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<label><input type="checkbox" name="%1$s[show_fab]" value="1" %2$s /> %3$s</label>',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        checked( ! empty( $opts['show_fab'] ), true, false ),
        esc_html__( 'Floating Button anzeigen', 'wine-k7-consent' )
    );
}

function wine_k7_consent_field_disable_fab_for_chatbot() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<label><input type="checkbox" name="%1$s[disable_fab_for_chatbot]" value="1" %2$s /> %3$s</label><p class="description">%4$s</p>',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        checked( ! empty( $opts['disable_fab_for_chatbot'] ), true, false ),
        esc_html__( 'Consent-FAB deaktivieren (Chatbot übernimmt)', 'wine-k7-consent' ),
        esc_html__( 'Aktiviere diese Option, wenn der Chatbot den Floating Button nutzen soll.', 'wine-k7-consent' )
    );
}

function wine_k7_consent_field_fab_label() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text" name="%1$s[fab_label]" value="%2$s" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['fab_label'] )
    );
}

function wine_k7_consent_field_fab_position() {
    $opts = wine_k7_consent_get_options();
    $value = ( $opts['fab_position'] === 'right' ) ? 'right' : 'left';
    ?>
    <select name="<?php echo esc_attr( WK7_CONSENT_OPTION_NAME ); ?>[fab_position]">
        <option value="left" <?php selected( $value, 'left' ); ?>><?php esc_html_e( 'Links', 'wine-k7-consent' ); ?></option>
        <option value="right" <?php selected( $value, 'right' ); ?>><?php esc_html_e( 'Rechts', 'wine-k7-consent' ); ?></option>
    </select>
    <?php
}

function wine_k7_consent_field_show_footer_link() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<label><input type="checkbox" name="%1$s[show_footer_link]" value="1" %2$s /> %3$s</label>',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        checked( ! empty( $opts['show_footer_link'] ), true, false ),
        esc_html__( 'Kleinen Footer-Link anzeigen', 'wine-k7-consent' )
    );
}

function wine_k7_consent_field_fab_avatar() {
    $opts = wine_k7_consent_get_options();
    $id = isset( $opts['fab_avatar_id'] ) ? (int) $opts['fab_avatar_id'] : 0;
    $url = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
    $field_name = WK7_CONSENT_OPTION_NAME . '[fab_avatar_id]';
    ?>
    <div id="wk7-fab-avatar-field">
        <div class="wk7-fab-avatar-preview" style="margin-bottom:8px;">
            <?php if ( $url ) : ?>
                <img src="<?php echo esc_url( $url ); ?>" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:1px solid #ccc;" />
            <?php else : ?>
                <span style="display:inline-block;width:48px;height:48px;border-radius:50%;background:#eee;border:1px dashed #ccc;"></span>
            <?php endif; ?>
        </div>
        <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $id ); ?>" />
        <button type="button" class="button wk7-upload-avatar"><?php esc_html_e( 'Avatar auswählen', 'wk7-consent' ); ?></button>
        <button type="button" class="button link-button wk7-remove-avatar" style="color:#b32d2e;margin-left:8px;" <?php disabled( ! $id ); ?>><?php esc_html_e( 'Entfernen', 'wk7-consent' ); ?></button>
        <p class="description"><?php esc_html_e( 'Optionales kleines Bild-Icon für den Floating Button (empfohlen: quadratisch, z. B. 64x64px).', 'wk7-consent' ); ?></p>
    </div>
    <?php
}

// Generic fields
function wine_k7_consent_field_text_generic( $args ) {
    $opts = wine_k7_consent_get_options();
    $key = isset( $args['key'] ) ? $args['key'] : '';
    $val = isset( $opts[ $key ] ) ? $opts[ $key ] : '';
    printf(
        '<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $key ),
        esc_attr( $val )
    );
}

function wine_k7_consent_field_textarea_generic( $args ) {
    $opts = wine_k7_consent_get_options();
    $key = isset( $args['key'] ) ? $args['key'] : '';
    $val = isset( $opts[ $key ] ) ? $opts[ $key ] : '';
    printf(
        '<textarea class="large-text" rows="3" name="%1$s[%2$s]">%3$s</textarea>',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $key ),
        esc_textarea( $val )
    );
}

// Appearance fields
function wine_k7_consent_field_auto_detect_colors() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<label><input type="checkbox" name="%1$s[ui_auto_detect_colors]" value="1" %2$s /> %3$s</label>',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        checked( ! empty( $opts['ui_auto_detect_colors'] ), true, false ),
        esc_html__( 'Theme-Farben automatisch verwenden (empfohlen)', 'wine-k7-consent' )
    );
    echo '<p class="description">' . esc_html__( 'Wenn aktiviert, werden die Farben automatisch vom aktiven Theme übernommen.', 'wine-k7-consent' ) . '</p>';
}

function wine_k7_consent_field_ui_position() {
    $opts = wine_k7_consent_get_options();
    $value = in_array( $opts['ui_position'], array( 'bottom', 'top' ), true ) ? $opts['ui_position'] : 'bottom';
    ?>
    <select name="<?php echo esc_attr( WK7_CONSENT_OPTION_NAME ); ?>[ui_position]">
        <option value="bottom" <?php selected( $value, 'bottom' ); ?>><?php esc_html_e( 'Unten', 'wine-k7-consent' ); ?></option>
        <option value="top" <?php selected( $value, 'top' ); ?>><?php esc_html_e( 'Oben', 'wine-k7-consent' ); ?></option>
    </select>
    <?php
}

function wine_k7_consent_field_ui_primary_color() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text wk7-color" name="%1$s[ui_primary_color]" value="%2$s" placeholder="#ff6b35" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['ui_primary_color'] )
    );
}

function wine_k7_consent_field_ui_text_color() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text wk7-color" name="%1$s[ui_text_color]" value="%2$s" placeholder="#ededed" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['ui_text_color'] )
    );
}

function wine_k7_consent_field_ui_background_color() {
    $opts = wine_k7_consent_get_options();
    printf(
        '<input type="text" class="regular-text wk7-color" name="%1$s[ui_background_color]" value="%2$s" placeholder="#0a0a0a" />',
        esc_attr( WK7_CONSENT_OPTION_NAME ),
        esc_attr( $opts['ui_background_color'] )
    );
}

function wine_k7_consent_field_ui_template() {
    $opts = wine_k7_consent_get_options();
    $value = in_array( $opts['ui_template'], array( 'template1','template2','template3','template4' ), true ) ? $opts['ui_template'] : 'template1';
    
    $templates = array(
        'template1' => array(
            'name' => __( 'Klassisch', 'wk7-consent' ),
            'desc' => __( 'Standard-Layout mit allen Kategorien', 'wk7-consent' ),
        ),
        'template2' => array(
            'name' => __( 'Kompakt', 'wk7-consent' ),
            'desc' => __( 'Platzsparendes Design', 'wk7-consent' ),
        ),
        'template3' => array(
            'name' => __( 'Kartenartig', 'wk7-consent' ),
            'desc' => __( 'Moderne Karten-Optik', 'wk7-consent' ),
        ),
        'template4' => array(
            'name' => __( 'Vollbreite', 'wk7-consent' ),
            'desc' => __( 'Maximale Sichtbarkeit', 'wk7-consent' ),
        ),
    );
    ?>
    <div class="wk7-template-selector">
        <?php foreach ( $templates as $tpl_id => $tpl_data ) : ?>
            <label class="wk7-template-option <?php echo $value === $tpl_id ? 'selected' : ''; ?>">
                <input type="radio" 
                       name="<?php echo esc_attr( WK7_CONSENT_OPTION_NAME ); ?>[ui_template]" 
                       value="<?php echo esc_attr( $tpl_id ); ?>"
                       <?php checked( $value, $tpl_id ); ?> />
                <div class="wk7-template-thumb">
                    <div class="wk7-template-preview wk7-template-<?php echo esc_attr( $tpl_id ); ?>">
                        <div class="wk7-tpl-header"></div>
                        <div class="wk7-tpl-content">
                            <div class="wk7-tpl-line"></div>
                            <div class="wk7-tpl-line short"></div>
                        </div>
                        <div class="wk7-tpl-buttons">
                            <span class="wk7-tpl-btn"></span>
                            <span class="wk7-tpl-btn primary"></span>
                        </div>
                    </div>
                </div>
                <div class="wk7-template-info">
                    <strong><?php echo esc_html( $tpl_data['name'] ); ?></strong>
                    <span class="description"><?php echo esc_html( $tpl_data['desc'] ); ?></span>
                </div>
            </label>
        <?php endforeach; ?>
    </div>
    <style>
    .wk7-template-selector {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }
    .wk7-template-option {
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
    }
    .wk7-template-option:hover {
        border-color: #ff6b35;
        box-shadow: 0 2px 8px rgba(255, 107, 53, 0.1);
    }
    .wk7-template-option.selected {
        border-color: #ff6b35;
        background: #fff3cd;
    }
    .wk7-template-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    .wk7-template-thumb {
        margin-bottom: 10px;
    }
    .wk7-template-preview {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-height: 120px;
        position: relative;
    }
    .wk7-tpl-header {
        height: 8px;
        background: #333;
        border-radius: 2px;
        margin-bottom: 8px;
        width: 70%;
    }
    .wk7-tpl-content {
        margin-bottom: 10px;
    }
    .wk7-tpl-line {
        height: 4px;
        background: #999;
        border-radius: 2px;
        margin-bottom: 4px;
    }
    .wk7-tpl-line.short {
        width: 60%;
    }
    .wk7-tpl-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }
    .wk7-tpl-btn {
        height: 12px;
        width: 40px;
        background: #ddd;
        border-radius: 3px;
    }
    .wk7-tpl-btn.primary {
        background: #ff6b35;
    }
    /* Template-spezifische Styles */
    .wk7-template-template2 {
        padding: 8px;
        min-height: 100px;
    }
    .wk7-template-template2 .wk7-tpl-header {
        width: 50%;
        height: 6px;
    }
    .wk7-template-template3 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
        padding: 8px;
    }
    .wk7-template-template3 .wk7-tpl-header {
        grid-column: 1 / -1;
    }
    .wk7-template-template3 .wk7-tpl-buttons {
        grid-column: 1 / -1;
    }
    .wk7-template-template4 .wk7-tpl-header {
        width: 100%;
        height: 10px;
    }
    .wk7-template-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .wk7-template-info strong {
        color: #333;
        font-size: 14px;
    }
    .wk7-template-info .description {
        font-size: 12px;
        color: #666;
    }
    </style>
    <script>
    jQuery(document).ready(function($) {
        // Template selection handler
        $('.wk7-template-option').on('click', function() {
            var $radio = $(this).find('input[type="radio"]');
            
            // Update radio button
            $radio.prop('checked', true).trigger('change');
            
            // Update visual selection
            $('.wk7-template-option').removeClass('selected');
            $(this).addClass('selected');
            
            // Trigger preview update if available
            if (typeof renderPreview === 'function') {
                renderPreview();
            }
        });
        
        // Prevent double-click on radio button
        $('.wk7-template-option input[type="radio"]').on('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
    <?php
}
