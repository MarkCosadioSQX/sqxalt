<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues Google Fonts for typography.
if ( ! function_exists( 'twentytwentyfive_enqueue_google_fonts' ) ) :
	/**
	 * Enqueues Google Fonts (Roboto Neue).
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_google_fonts() {
		wp_enqueue_style(
			'twentytwentyfive-google-fonts',
			'https://fonts.googleapis.com/css2?family=Roboto+Neue:wght@400;500;700&display=swap',
			array(),
			null
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_google_fonts', 5 );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// Changes site title/logo link to point to front page and removes link on front page.
if ( ! function_exists( 'twentytwentyfive_site_title_link' ) ) :
	/**
	 * Filters the site title block to change link behavior.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @param string $block_content The block content.
	 * @param array  $block         The full block, including name and attributes.
	 * @return string Modified block content.
	 */
	function twentytwentyfive_site_title_link( $block_content, $block ) {
		if ( isset( $block['blockName'] ) && 'core/site-title' === $block['blockName'] ) {
			// Check if we're on the front page
			$front_page_id = get_option( 'page_on_front' );
			$is_front_page = is_front_page() || ( $front_page_id && is_page( $front_page_id ) );
			
			if ( $is_front_page ) {
				// Remove link on front page - convert anchor to plain text
				$block_content = preg_replace( '/<a[^>]*>(.*?)<\/a>/', '$1', $block_content );
			} else {
				// Get the front page URL
				if ( 'page' === get_option( 'show_on_front' ) && $front_page_id ) {
					$home_url = get_permalink( $front_page_id );
				} else {
					$home_url = home_url( '/' );
				}
				
				// Replace any existing href with the front page URL
				$block_content = preg_replace( '/href="[^"]*"/', 'href="' . esc_url( $home_url ) . '"', $block_content );
			}
		}
		return $block_content;
	}
endif;
add_filter( 'render_block', 'twentytwentyfive_site_title_link', 10, 2 );


// Adds custom header background color.
if ( ! function_exists( 'twentytwentyfive_custom_header_styles' ) ) :
	/**
	 * Adds custom header background color.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_custom_header_styles() {
		$banner_image_url = esc_url( admin_url( 'images/uploads/pexels-expect-best-79873-323705.jpg' ) );
		$custom_css = '
			header.wp-block-template-part,
			.wp-block-template-part[area="header"] {
				background-color: rgb(3, 28, 38) !important;
			}
			header.wp-block-template-part .wp-block-group.alignfull,
			.wp-block-template-part[area="header"] .wp-block-group.alignfull {
				background-color: rgb(3, 28, 38) !important;
			}
			/* Navigation links - white color */
			header.wp-block-template-part .wp-block-navigation a,
			header.wp-block-template-part .wp-block-navigation-item__content,
			header.wp-block-template-part .wp-block-page-list__item__link,
			.wp-block-template-part[area="header"] .wp-block-navigation a,
			.wp-block-template-part[area="header"] .wp-block-navigation-item__content,
			.wp-block-template-part[area="header"] .wp-block-page-list__item__link {
				color: #ffffff !important;
			}
			/* Animated underline on hover */
			header.wp-block-template-part .wp-block-navigation a,
			header.wp-block-template-part .wp-block-navigation-item__content,
			header.wp-block-template-part .wp-block-page-list__item__link,
			.wp-block-template-part[area="header"] .wp-block-navigation a,
			.wp-block-template-part[area="header"] .wp-block-navigation-item__content,
			.wp-block-template-part[area="header"] .wp-block-page-list__item__link {
				position: relative;
				text-decoration: none;
			}
			header.wp-block-template-part .wp-block-navigation a::after,
			header.wp-block-template-part .wp-block-navigation-item__content::after,
			header.wp-block-template-part .wp-block-page-list__item__link::after,
			.wp-block-template-part[area="header"] .wp-block-navigation a::after,
			.wp-block-template-part[area="header"] .wp-block-navigation-item__content::after,
			.wp-block-template-part[area="header"] .wp-block-page-list__item__link::after {
				content: "";
				position: absolute;
				bottom: -2px;
				left: 0;
				width: 0;
				height: 2px;
				background-color: #ffffff;
				transition: width 0.3s ease;
			}
			header.wp-block-template-part .wp-block-navigation a:hover::after,
			header.wp-block-template-part .wp-block-navigation-item__content:hover::after,
			header.wp-block-template-part .wp-block-page-list__item__link:hover::after,
			.wp-block-template-part[area="header"] .wp-block-navigation a:hover::after,
			.wp-block-template-part[area="header"] .wp-block-navigation-item__content:hover::after,
			.wp-block-template-part[area="header"] .wp-block-page-list__item__link:hover::after {
				width: 100%;
			}
			/* Home page banner */
			.custom-home-banner {
				position: relative;
				height: 16rem;
				width: 100%;
				max-width: 100%;
				background-image: url(' . $banner_image_url . ');
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
				margin: 0;
				padding: 0;
				display: block;
			}
			.custom-home-banner::before {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background-color: rgba(3, 28, 38, 0.8);
				z-index: 1;
			}
			/* Banner content styling - left aligned with logo */
			.custom-home-banner > .wp-block-group {
				position: relative;
				z-index: 2;
			}
			.custom-home-banner .alignwide {
				position: relative;
				z-index: 2;
				/* Match header alignment - same padding as header alignwide */
			}
			/* Comprehensive alignment solution for H2 with H1 across all screen sizes */
			/* Key: Both H1 and H2 use: alignfull → constrained → alignwide structure */
			/* The constrained container applies responsive padding: clamp(30px, 5vw, 50px) */
			/* The alignwide is centered with max-width: 1340px */
			
			/* Ensure both constrained containers have identical responsive padding */
			.custom-home-banner .wp-block-group.is-layout-constrained,
			.custom-home-banner ~ .wp-block-group.alignfull .wp-block-group.is-layout-constrained,
			.custom-home-banner ~ .wp-block-group.alignfull .wp-block-group.layout-type-constrained {
				padding-left: var(--wp--preset--spacing--50) !important;
				padding-right: var(--wp--preset--spacing--50) !important;
			}
			
			/* Ensure alignwide containers have no additional padding/margin that affects alignment */
			.custom-home-banner .wp-block-group.is-layout-constrained > .wp-block-group.alignwide,
			.custom-home-banner ~ .wp-block-group.alignfull .wp-block-group.is-layout-constrained > .wp-block-group.alignwide,
			.custom-home-banner .wp-block-group.layout-type-constrained > .wp-block-group.alignwide,
			.custom-home-banner ~ .wp-block-group.alignfull .wp-block-group.layout-type-constrained > .wp-block-group.alignwide {
				padding-left: 0 !important;
				padding-right: 0 !important;
				margin-left: auto !important;
				margin-right: auto !important;
				max-width: var(--wp--style--global--wide-size, 1340px) !important;
			}
			
			/* Remove any padding from the headings themselves - alignment comes from containers */
			.custom-home-banner h1.banner-title,
			.custom-home-banner ~ .wp-block-group.alignfull h2.banner-title,
			.custom-home-banner ~ .wp-block-group.alignfull h2.wp-block-heading {
				padding-left: 0 !important;
				padding-right: 0 !important;
				margin-left: 0 !important;
				margin-right: 0 !important;
			}
			.custom-home-banner .banner-title,
			.custom-home-banner .banner-subtitle {
				color: #ffffff !important;
				text-align: left;
				margin-left: 0;
				margin-right: 0;
			}
			.custom-home-banner .banner-title {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 500 !important;
				color: #ffffff !important;
				margin-bottom: 1rem !important;
			}
			/* Featured News Articles heading - uses banner-title class but outside banner */
			.custom-home-banner ~ .wp-block-group.alignfull h2.banner-title,
			.wp-block-group.alignfull:not(.custom-home-banner) h2.banner-title {
				color: #005286 !important;
			}
			.custom-home-banner .banner-subtitle {
				font-family: "Athelas", "AthelasRegular", Georgia, serif !important;
				color: #ffffff !important;
				margin-bottom: 1.5rem !important;
			}
			.custom-home-banner .wp-block-buttons {
				justify-content: flex-start;
				margin-left: 0;
			}
			.custom-home-banner .banner-button .wp-block-button__link {
				background-color: #005286 !important; /* primary blue */
				color: #ffffff !important;
				padding: 0.75rem 2rem;
				border-radius: 0.25rem;
				text-decoration: none;
				transition: background-color 0.25s ease;
			}
			.custom-home-banner .banner-button .wp-block-button__link:hover {
				background-color: #003f66 !important; /* darker blue on hover */
			}
			/* Project Typography Styles */
			/* Body Text - Athelas Regular, Black, 18pt */
			body,
			.wp-block-group,
			.wp-block-post-content,
			p {
				font-family: "Athelas", "AthelasRegular", Georgia, serif !important;
				color: #000000 !important;
				font-size: 18pt !important;
				line-height: 1.5 !important;
			}
			/* Headings - Roboto Neue Medium, #1f497d, Tracking 50 */
			h1,
			h2,
			.wp-block-heading,
			.wp-block-post-title {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 500 !important;
				color: #1f497d !important;
				letter-spacing: 0.05em !important;
			}
			/* Subheading 1 - Roboto Neue Bold, #005286, Tracking 50 */
			.subheading-1,
			h3.is-style-subheading-1,
			.wp-block-heading.has-subheading-1-style {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 700 !important;
				color: #005286 !important;
				letter-spacing: 0.05em !important;
			}
			/* Subheading 2 - Roboto Neue Medium, #595959, Tracking 8 */
			.subheading-2,
			h4.is-style-subheading-2,
			.wp-block-heading.has-subheading-2-style {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 500 !important;
				color: #595959 !important;
				letter-spacing: 0.008em !important;
			}
			/* Charts or Tables - Roboto Neue, Black */
			table,
			.wp-block-table,
			.wp-block-table__cell,
			.chart,
			.wp-block-chart {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				color: #000000 !important;
			}
			/* MBIS Logo Background */
			.mbis-logo-bg {
				background-color: #293439 !important;
			}
			/* MBIS Lettering over logo background */
			.mbis-logo-text {
				color: #d2ca60 !important;
			}
			/* SQX Logo Lettering */
			.sqx-logo-text {
				color: #005286 !important;
			}
			/* Hide specific content block with navigation and logos */
			.wp-block-post-content .wp-block-navigation,
			.wp-block-post-content .wp-block-site-logo,
			.wp-block-group.alignfull.has-global-padding.is-layout-constrained:has(.wp-block-post-content .wp-block-navigation),
			.wp-block-group.alignfull.has-global-padding.is-layout-constrained:has(.wp-block-post-content .wp-block-site-logo) {
				display: none !important;
			}
			/* Hide specific navigation link items */
			li.wp-block-navigation-item.wp-block-navigation-link a[href*="page_id=12"],
			li.wp-block-navigation-item.wp-block-navigation-link {
				display: none !important;
			}
			/* Hide Elementor #43 navigation link */
			.wp-block-pages-list__item__link[href*="elementor-43"],
			.wp-block-navigation-item__content[href*="elementor-43"],
			a[href*="elementor-43"].wp-block-pages-list__item__link,
			a[href*="elementor-43"].wp-block-navigation-item__content {
				display: none !important;
			}
			li:has(> a[href*="elementor-43"]) {
				display: none !important;
			}
			/* Hide Blog heading - more specific selectors */
			.hidden-blog-heading,
			main h1.wp-block-heading.has-text-align-left,
			.wp-block-heading.has-text-align-left,
			h1.wp-block-heading.has-text-align-left,
			/* Target the specific Blog heading pattern */
			main .wp-block-group h1.wp-block-heading.has-text-align-left {
				display: none !important;
				visibility: hidden !important;
				opacity: 0 !important;
				height: 0 !important;
				overflow: hidden !important;
				margin: 0 !important;
				padding: 0 !important;
			}
			/* Hide site title h2 - specifically in footer and anywhere else */
			footer h2.wp-block-site-title,
			footer .wp-block-site-title h2,
			.wp-block-template-part[area="footer"] h2.wp-block-site-title,
			.wp-block-template-part[area="footer"] .wp-block-site-title h2,
			main h2.wp-block-site-title,
			.wp-block-column h2.wp-block-site-title,
			/* Catch all instances */
			h2.wp-block-site-title:not(header h2),
			.wp-block-site-title[data-level="2"] {
				display: none !important;
				visibility: hidden !important;
				opacity: 0 !important;
				height: 0 !important;
				overflow: hidden !important;
				margin: 0 !important;
				padding: 0 !important;
			}
			/* Featured Articles Section Styling */
			.featured-articles-section,
			.wp-block-group.featured-articles-section,
			.alignfull.featured-articles-section {
				background-color: #ffffff !important;
				display: block !important;
				visibility: visible !important;
				opacity: 1 !important;
			}
			.featured-articles-section .wp-block-query,
			.featured-articles-section .wp-block-post-template {
				display: block !important;
				visibility: visible !important;
			}
			/* Featured Articles Header - flexbox layout with heading and button */
			.featured-articles-header,
			.wp-block-group.featured-articles-header,
			.alignwide.featured-articles-header {
				display: flex !important;
				flex-wrap: wrap;
				justify-content: space-between !important;
				align-items: center;
				margin-bottom: 3rem !important;
				visibility: visible !important;
				opacity: 1 !important;
				height: auto !important;
			}
			/* Featured Articles Heading with blue underline - ensure visibility */
			.featured-articles-heading,
			h2.featured-articles-heading,
			.wp-block-heading.featured-articles-heading,
			.alignwide .featured-articles-heading,
			.featured-articles-header .featured-articles-heading,
			.featured-articles-header h2,
			h2.wp-block-heading.featured-articles-heading {
				display: block !important;
				visibility: visible !important;
				opacity: 1 !important;
				color: #333333 !important;
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 700 !important;
				font-size: 2rem !important;
				letter-spacing: 0.05em !important;
				margin: 0 !important;
				padding: 0 !important;
				padding-bottom: 0.5rem !important;
				position: relative;
				height: auto !important;
				overflow: visible !important;
			}
			.featured-articles-heading::after {
				content: "";
				position: absolute;
				bottom: 0;
				left: 0;
				width: 6rem;
				height: 3px;
				background-color: #005286 !important;
			}
			/* See All News Button */
			.see-all-news-button .wp-block-button__link {
				background-color: transparent !important;
				color: #333333 !important;
				border: 1px solid #cccccc !important;
				border-radius: 0.25rem !important;
				padding: 0.5rem 1rem !important;
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-size: 0.9375rem !important;
				text-decoration: none !important;
				transition: all 0.25s ease !important;
			}
			.see-all-news-button .wp-block-button__link:hover {
				background-color: #f5f5f5 !important;
				border-color: #999999 !important;
			}
			/* Featured Article Cards - 3 column grid layout */
			.featured-articles-section .wp-block-post-template {
				display: grid !important;
				grid-template-columns: repeat(3, 1fr) !important;
				gap: 2rem !important;
				margin-top: 0 !important;
			}
			@media (max-width: 1024px) {
				.featured-articles-section .wp-block-post-template {
					grid-template-columns: repeat(2, 1fr) !important;
				}
			}
			@media (max-width: 600px) {
				.featured-articles-section .wp-block-post-template {
					grid-template-columns: 1fr !important;
				}
			}
			/* Individual Article Card Styling */
			.featured-article-card {
				background-color: #ffffff !important;
				border-radius: 8px !important;
				box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
				padding: 0 !important;
				overflow: hidden;
				transition: box-shadow 0.25s ease, transform 0.25s ease;
			}
			.featured-article-card:hover {
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
				transform: translateY(-2px);
			}
			/* Card Content Padding */
			.featured-article-card > * {
				padding-left: 1.25rem !important;
				padding-right: 1.25rem !important;
			}
			.featured-article-card .wp-block-post-featured-image {
				padding: 0 !important;
				margin-bottom: 0 !important;
			}
			.featured-article-card .wp-block-post-featured-image img,
			.featured-article-card .wp-block-post-featured-image a img {
				border-radius: 8px 8px 0 0 !important;
				width: 100% !important;
				height: auto !important;
				display: block !important;
			}
			/* Article Date with calendar icon */
			.article-date,
			.featured-article-card .wp-block-post-date {
				color: #999999 !important;
				font-size: 0.875rem !important;
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				margin-bottom: 0.9375rem !important;
				margin-top: 1.25rem !important;
				padding-top: 0 !important;
				position: relative;
			}
			.article-date::before,
			.featured-article-card .wp-block-post-date::before {
				content: "📅";
				margin-right: 0.5rem;
				font-size: 0.875rem;
			}
			.article-date a,
			.featured-article-card .wp-block-post-date a {
				color: #999999 !important;
				text-decoration: none !important;
			}
			/* Article Title */
			.article-title,
			.featured-article-card .wp-block-post-title {
				font-family: "Roboto Neue", "RobotoNeue", sans-serif !important;
				font-weight: 700 !important;
				font-size: 1.125rem !important;
				color: #333333 !important;
				margin-bottom: 0.9375rem !important;
				line-height: 1.4 !important;
			}
			.article-title a,
			.featured-article-card .wp-block-post-title a {
				color: inherit !important;
				text-decoration: none !important;
			}
			.article-title a:hover,
			.featured-article-card .wp-block-post-title a:hover {
				color: #005286 !important;
			}
			/* Article Excerpt/Description */
			.article-excerpt,
			.featured-article-card .wp-block-post-excerpt {
				font-family: "Athelas", "AthelasRegular", Georgia, serif !important;
				font-size: 0.9375rem !important;
				line-height: 1.6 !important;
				color: #666666 !important;
				margin: 0 !important;
				padding-bottom: 1.5rem !important;
			}
			.article-excerpt p,
			.featured-article-card .wp-block-post-excerpt p {
				margin: 0 !important;
				font-size: inherit !important;
				line-height: inherit !important;
				color: inherit !important;
			}
		';
		wp_add_inline_style( 'twentytwentyfive-style', $custom_css );
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_custom_header_styles', 11 );

// Add Featured Articles heading if not present in template
if ( ! function_exists( 'twentytwentyfive_add_featured_articles_heading' ) ) :
	/**
	 * Adds "Featured Articles" heading after banner if not already present.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_add_featured_articles_heading( $content ) {
		// Only on front page or home page
		if ( is_front_page() || is_home() ) {
		// Check if heading already exists in content
		if ( ! preg_match( '/Featured (News )?Articles/i', $content ) && ! preg_match( '/<h2[^>]*>.*?Featured (News )?Articles.*?<\/h2>/i', $content ) ) {
				// Find the banner closing tag and add heading after it
				$banner_pattern = '/(<\/div>\s*<!--\s*\/wp:group\s*-->\s*<\/div>\s*<!--\s*\/wp:group\s*-->)/';
				$heading_html = '
<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"0"},"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide">
			<!-- wp:heading {"level":2,"className":"featured-articles-heading"} -->
			<h2 class="wp-block-heading featured-articles-heading">Featured Articles</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
';
				// Try to insert after banner
				if ( preg_match( '/custom-home-banner/', $content ) ) {
					$content = preg_replace( $banner_pattern, '$1' . $heading_html, $content, 1 );
				}
			}
		}
		return $content;
	}
endif;
// Filter query to support Featured tag by slug - converts slug to ID before query is built
if ( ! function_exists( 'twentytwentyfive_filter_featured_posts_query_vars' ) ) :
	// Helper function to modify query vars for featured posts
	function twentytwentyfive_filter_featured_posts_query_vars( $query_args, $block ) {
		// Check if this query has taxQuery with post_tag containing "featured" (either as string or already converted to 0)
		if ( isset( $block->context['query']['taxQuery']['post_tag'] ) ) {
			$tags = $block->context['query']['taxQuery']['post_tag'];
			
			// Check if we have "featured" as string in original query
			$has_featured = ( is_array( $tags ) && in_array( 'featured', $tags, true ) );
			
			// Also check if WordPress already converted "featured" to 0 in the processed tax_query
			if ( ! $has_featured && ! empty( $query_args['tax_query'] ) ) {
				foreach ( $query_args['tax_query'] as $tax_query ) {
					if ( isset( $tax_query['taxonomy'] ) && 'post_tag' === $tax_query['taxonomy'] ) {
						if ( isset( $tax_query['terms'] ) && is_array( $tax_query['terms'] ) && in_array( 0, $tax_query['terms'], true ) ) {
							$has_featured = true;
							break;
						}
					}
				}
			}
			
			if ( $has_featured ) {
				$featured_tag = get_term_by( 'slug', 'featured', 'post_tag' );
				
				if ( $featured_tag && ! is_wp_error( $featured_tag ) ) {
					// Remove any existing post_tag tax_query entries (including the broken one with 0)
					if ( ! empty( $query_args['tax_query'] ) ) {
						$query_args['tax_query'] = array_values( array_filter( $query_args['tax_query'], function( $tax_query ) {
							return ! isset( $tax_query['taxonomy'] ) || 'post_tag' !== $tax_query['taxonomy'];
						} ) );
					}
					
					// Add the correct Featured tag ID to tax_query
					$query_args['tax_query'][] = array(
						'taxonomy'         => 'post_tag',
						'terms'            => array( $featured_tag->term_id ),
						'include_children' => false,
					);
				}
			}
		}
		
		return $query_args;
	}
endif;
add_filter( 'query_loop_block_query_vars', 'twentytwentyfive_filter_featured_posts_query_vars', 10, 2 );

// Inject h2 title below banner on front page
if ( ! function_exists( 'twentytwentyfive_inject_featured_heading_after_banner' ) ) :
	/**
	 * Injects "Featured Articles" h2 heading after the banner on front page.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The full block, including name and attributes.
	 * @param WP_Block $instance      The block instance.
	 * @return string The modified block content.
	 */
	function twentytwentyfive_inject_featured_heading_after_banner( $block_content, $block, $instance ) {
		// Only on front page
		if ( ! is_front_page() ) {
			return $block_content;
		}
		
		// Check if this is the custom-home-banner group closing
		if ( isset( $block['attrs']['className'] ) && 'custom-home-banner' === $block['attrs']['className'] ) {
			// Check if heading already exists after this banner
			static $heading_injected = false;
			if ( ! $heading_injected ) {
				$heading_injected = true;
				
				// Inject the h2 heading after the banner as rendered HTML
				// Use exact same structure as banner: alignfull → constrained (with is-layout-constrained class) → alignwide
				$heading_html = '<div class="wp-block-group alignfull" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:0">
	<div class="wp-block-group is-layout-constrained wp-block-group-is-layout-constrained">
		<div class="wp-block-group alignwide">
			<h2 class="wp-block-heading banner-title">Featured Articles</h2>
		</div>
	</div>
</div>';
				
				// Append after the banner group closes
				return $block_content . $heading_html;
			}
		}
		
		return $block_content;
	}
endif;
// Inject Featured Articles section after banner if database template is overriding file template
if ( ! function_exists( 'twentytwentyfive_inject_featured_articles_section' ) ) :
	/**
	 * Injects Featured Articles section after banner if not present in rendered content.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @param string $content The page content.
	 * @return string Modified content.
	 */
	function twentytwentyfive_inject_featured_articles_section( $content ) {
		// Only on front page
		if ( ! is_front_page() ) {
			return $content;
		}
		
		// Check if Featured Articles section already exists
		if ( strpos( $content, 'featured-articles-section' ) !== false || strpos( $content, 'Featured Articles' ) !== false ) {
			return $content;
		}
		
		// Find banner closing tag and inject after it
		$banner_closing = '</div><!-- /wp:group -->';
		$insert_position = strrpos( $content, $banner_closing );
		
		if ( $insert_position !== false ) {
			$featured_section = '
<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"},"layout":{"flexWrap":"wrap","justifyContent":"space-between"}},"layout":{"type":"flex"}} -->
		<div class="wp-block-group alignwide featured-articles-header" style="gap:var(--wp--preset--spacing--40);justify-content:space-between">
			<!-- wp:heading {"level":2,"className":"featured-articles-heading"} -->
			<h2 class="wp-block-heading featured-articles-heading">Featured Articles</h2>
			<!-- /wp:heading -->
			
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"see-all-news-button"} -->
				<div class="wp-block-button see-all-news-button"><a class="wp-block-button__link wp-element-button" href="/news/">See All News →</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"featured-articles-section","style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull featured-articles-section" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide">
			<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"post_tag":["featured"]}},"align":"full","layout":{"type":"default"}} -->
			<div class="wp-block-query alignfull">
				<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
				<!-- wp:group {"className":"featured-article-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
				<div class="wp-block-group featured-article-card">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"8px"},"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->
					<!-- wp:post-date {"isLink":false,"style":{"typography":{"fontSize":"0.875rem"},"spacing":{"margin":{"bottom":"var:preset|spacing|15"}}},"className":"article-date"} /-->
					<!-- wp:post-title {"isLink":true,"fontSize":"large","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|15"}}},"className":"article-title"} /-->
					<!-- wp:post-excerpt {"excerptLength":30,"moreText":"","style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.6"}},"className":"article-excerpt"} /-->
				</div>
				<!-- /wp:group -->
				<!-- /wp:post-template -->
				<!-- wp:query-no-results -->
				<!-- wp:paragraph -->
				<p>No featured articles found.</p>
				<!-- /wp:paragraph -->
				<!-- /wp:query-no-results -->
			</div>
			<!-- /wp:query -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->';
			
			$content = substr_replace( $content, $banner_closing . $featured_section, $insert_position, strlen( $banner_closing ) );
		}
		
		return $content;
	}
endif;
// Remove the_content filter - doesn't work with block themes. Using render_block instead below.
// add_filter( 'the_content', 'twentytwentyfive_inject_featured_articles_section', 20 );

// Inject Featured Articles section after banner using render_block (works with block themes)
if ( ! function_exists( 'twentytwentyfive_inject_featured_articles_after_banner_block' ) ) :
	/**
	 * Injects Featured Articles section after the banner block on front page.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The full block, including name and attributes.
	 * @param WP_Block $instance      The block instance.
	 * @return string The modified block content.
	 */
	function twentytwentyfive_inject_featured_articles_after_banner_block( $block_content, $block, $instance ) {
		// Only on front page
		if ( ! is_front_page() ) {
			return $block_content;
		}
		
		// Check if this is the custom-home-banner group closing
		if ( isset( $block['attrs']['className'] ) && 'custom-home-banner' === $block['attrs']['className'] ) {
			// Static flag to ensure we only inject once
			static $section_injected = false;
			if ( ! $section_injected ) {
				$section_injected = true;
				
				// Check if section already exists
				if ( strpos( $block_content, 'featured-articles-section' ) !== false ) {
					return $block_content;
				}
				
				// Inject the Featured Articles section as rendered HTML
				$featured_html = '<div class="wp-block-group alignfull" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)">
	<div class="wp-block-group is-layout-constrained wp-block-group-is-layout-constrained">
		<div class="wp-block-group alignwide featured-articles-header" style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-bottom:3rem;gap:var(--wp--preset--spacing--40)">
			<h2 class="wp-block-heading featured-articles-heading" style="margin:0;padding:0;padding-bottom:0.5rem;position:relative">Featured Articles</h2>
			<div class="wp-block-buttons">
				<div class="wp-block-button see-all-news-button">
					<a class="wp-block-button__link wp-element-button" href="/news/">See All News →</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wp-block-group alignfull featured-articles-section" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)">
	<div class="wp-block-group is-layout-constrained wp-block-group-is-layout-constrained">
		<div class="wp-block-group alignwide">' . do_blocks( '<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"post_tag":["featured"]}},"align":"full","layout":{"type":"default"}} -->
			<div class="wp-block-query alignfull">
				<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
				<!-- wp:group {"className":"featured-article-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
				<div class="wp-block-group featured-article-card">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"8px"},"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->
					<!-- wp:post-date {"isLink":false,"style":{"typography":{"fontSize":"0.875rem"},"spacing":{"margin":{"bottom":"var:preset|spacing|15"}}},"className":"article-date"} /-->
					<!-- wp:post-title {"isLink":true,"fontSize":"large","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|15"}}},"className":"article-title"} /-->
					<!-- wp:post-excerpt {"excerptLength":30,"moreText":"","style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.6"}},"className":"article-excerpt"} /-->
				</div>
				<!-- /wp:group -->
				<!-- /wp:post-template -->
				<!-- wp:query-no-results -->
				<!-- wp:paragraph -->
				<p>No featured articles found.</p>
				<!-- /wp:paragraph -->
				<!-- /wp:query-no-results -->
			</div>
			<!-- /wp:query -->' ) . '
		</div>
	</div>
</div>';
				
				return $block_content . $featured_html;
			}
		}
		
		return $block_content;
	}
endif;
add_filter( 'render_block', 'twentytwentyfive_inject_featured_articles_after_banner_block', 10, 3 );

// Resets WordPress site URLs to auto-detect from current request (default behavior).
if ( ! function_exists( 'twentytwentyfive_reset_site_urls' ) ) :
	/**
	 * Resets WordPress site URLs to default auto-detect values.
	 * Run this once, then remove or comment out this function.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_reset_site_urls() {
		// Only run if accessed from front-end (not admin/ajax/cron)
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// Get current request URL
		$protocol = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) ? 'https://' : 'http://';
		$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
		$current_url = $protocol . $host;

		// Update WordPress URLs in database to auto-detect
		update_option( 'home', $current_url );
		update_option( 'siteurl', $current_url );
	}
endif;
// Bot: Temporarily enabled to reset URLs. Comment out after visiting the site once.
add_action( 'init', 'twentytwentyfive_reset_site_urls', 1 );
