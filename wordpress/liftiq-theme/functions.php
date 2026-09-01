<?php
/**
 * LIFTIQ — child theme van Storefront.
 * Laadt het ouder-thema, de LIFTIQ-styles en de Impact-achtige koplettertype.
 */

if (!defined('ABSPATH')) { exit; }

add_action('wp_enqueue_scripts', function () {
    // Ouder-thema (Storefront) stylesheet.
    wp_enqueue_style(
        'storefront-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme(get_template())->get('Version')
    );

    // Google font "Anton" (Impact-achtig) + Oswald voor accenten.
    wp_enqueue_style(
        'liftiq-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Oswald:wght@400;600;700&display=swap',
        array(),
        null
    );

    // LIFTIQ child-styles (dit bestand).
    wp_enqueue_style(
        'liftiq-child-style',
        get_stylesheet_uri(),
        array('storefront-parent-style', 'liftiq-fonts'),
        wp_get_theme()->get('Version')
    );
}, 20);

/**
 * Storefront-standaardkleuren alvast op de LIFTIQ-huisstijl zetten
 * (de gebruiker kan dit later fijn afstellen via Weergave → Aanpassen).
 */
add_filter('storefront_customizer_defaults', function ($defaults) {
    $defaults['background_color']       = '0a0a0a';
    $defaults['header_background_color'] = '111111';
    $defaults['header_text_color']      = 'ffffff';
    $defaults['header_link_color']      = '00CFFF';
    $defaults['footer_background_color'] = '111111';
    $defaults['footer_link_color']      = '00CFFF';
    $defaults['button_background_color'] = '00CFFF';
    $defaults['button_text_color']      = '0a0a0a';
    $defaults['text_color']             = 'd0d0d0';
    return $defaults;
});

/**
 * WooCommerce-ondersteuning + nette galerij-features overnemen van Storefront.
 */
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('custom-logo', array('height' => 60, 'width' => 200, 'flex-height' => true, 'flex-width' => true));
});
