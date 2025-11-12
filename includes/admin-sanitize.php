<?php
/**
 * Options Sanitization for Wine K7 Consent Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wine_k7_consent_sanitize_options( $input ) {
    $defaults = wine_k7_consent_get_default_options();
    $clean = array();

    // IDs
    $clean['gtm_id'] = isset( $input['gtm_id'] ) ? strtoupper( preg_replace( '/[^A-Z0-9\-]/', '', wp_strip_all_tags( $input['gtm_id'] ) ) ) : $defaults['gtm_id'];
    $clean['ga4_id'] = isset( $input['ga4_id'] ) ? strtoupper( preg_replace( '/[^A-Z0-9\-]/', '', wp_strip_all_tags( $input['ga4_id'] ) ) ) : $defaults['ga4_id'];

    // Region
    $region = isset( $input['region'] ) ? strtolower( wp_strip_all_tags( $input['region'] ) ) : $defaults['region'];
    $clean['region'] = in_array( $region, array( 'eu', 'us', 'auto' ), true ) ? $region : 'eu';

    // URLs
    $clean['policy_url']  = isset( $input['policy_url'] ) ? esc_url_raw( $input['policy_url'] ) : $defaults['policy_url'];
    $clean['imprint_url'] = isset( $input['imprint_url'] ) ? esc_url_raw( $input['imprint_url'] ) : $defaults['imprint_url'];

    // Storage key
    $key = isset( $input['storage_key'] ) ? sanitize_key( $input['storage_key'] ) : $defaults['storage_key'];
    $clean['storage_key'] = $key ? $key : 'wine_k7_consent';

    // FAB settings
    $clean['show_fab']  = ! empty( $input['show_fab'] ) ? 1 : 0;
    $clean['disable_fab_for_chatbot'] = ! empty( $input['disable_fab_for_chatbot'] ) ? 1 : 0;
    $clean['fab_label'] = isset( $input['fab_label'] ) ? sanitize_text_field( $input['fab_label'] ) : $defaults['fab_label'];
    $clean['fab_position'] = ( isset( $input['fab_position'] ) && in_array( $input['fab_position'], array( 'left', 'right' ), true ) ) ? $input['fab_position'] : $defaults['fab_position'];
    $clean['show_footer_link'] = ! empty( $input['show_footer_link'] ) ? 1 : 0;
    $clean['fab_avatar_id'] = isset( $input['fab_avatar_id'] ) ? absint( $input['fab_avatar_id'] ) : 0;

    // Texts
    $text_keys = array( 'txt_title', 'txt_text', 'txt_btn_accept_all', 'txt_btn_reject_all', 'txt_btn_save', 'txt_link_policy', 'txt_link_imprint' );
    foreach ( $text_keys as $k ) {
        $clean[ $k ] = isset( $input[ $k ] ) ? wp_kses_post( $input[ $k ] ) : $defaults[ $k ];
    }

    // Labels
    $label_keys = array( 'lbl_necessary', 'lbl_analytics', 'lbl_marketing', 'lbl_functional' );
    foreach ( $label_keys as $k ) {
        $clean[ $k ] = isset( $input[ $k ] ) ? sanitize_text_field( $input[ $k ] ) : $defaults[ $k ];
    }

    // Descriptions
    $desc_keys = array( 'desc_necessary', 'desc_analytics', 'desc_marketing', 'desc_functional' );
    foreach ( $desc_keys as $k ) {
        $clean[ $k ] = isset( $input[ $k ] ) ? wp_kses_post( $input[ $k ] ) : $defaults[ $k ];
    }

    // Appearance
    $clean['ui_position'] = ( isset( $input['ui_position'] ) && in_array( $input['ui_position'], array( 'bottom', 'top' ), true ) ) ? $input['ui_position'] : $defaults['ui_position'];
    $clean['ui_template'] = ( isset( $input['ui_template'] ) && in_array( $input['ui_template'], array( 'template1','template2','template3','template4' ), true ) ) ? $input['ui_template'] : $defaults['ui_template'];
    $clean['ui_auto_detect_colors'] = ! empty( $input['ui_auto_detect_colors'] ) ? 1 : 0;

    // Colors - use sanitize_hex_color and add # if needed
    $clean['ui_primary_color'] = isset( $input['ui_primary_color'] ) ? sanitize_hex_color( $input['ui_primary_color'] ) : $defaults['ui_primary_color'];
    $clean['ui_text_color'] = isset( $input['ui_text_color'] ) ? sanitize_hex_color( $input['ui_text_color'] ) : $defaults['ui_text_color'];
    $clean['ui_background_color'] = isset( $input['ui_background_color'] ) ? sanitize_hex_color( $input['ui_background_color'] ) : $defaults['ui_background_color'];

    // Ensure colors have # prefix
    foreach ( array( 'ui_primary_color', 'ui_text_color', 'ui_background_color' ) as $ck ) {
        if ( ! empty( $clean[ $ck ] ) && $clean[ $ck ][0] !== '#' ) {
            $clean[ $ck ] = '#' . $clean[ $ck ];
        }
        // Fallback to default if sanitization failed
        if ( empty( $clean[ $ck ] ) ) {
            $clean[ $ck ] = $defaults[ $ck ];
        }
    }

    return $clean;
}
