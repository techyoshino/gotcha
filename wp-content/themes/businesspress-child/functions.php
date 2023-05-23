<?php
/**
 * BusinessPress functions and definitions
 *
 * @package BusinessPress
 */

if ( ! function_exists( 'businesspress_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function businesspress_setup() {

	// Make theme available for translation.
	load_theme_textdomain( 'businesspress', get_theme_file_path( '/languages' ) );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 840, 0, false );
	add_image_size( 'businesspress-post-thumbnail-large', 1280, 540, true );
	add_image_size( 'businesspress-post-thumbnail-medium', 482, 318, true );
	add_image_size( 'businesspress-post-thumbnail-list', 482, 361, true );
	add_image_size( 'businesspress-post-thumbnail-small', 80, 60, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 720;

	// This theme uses wp_nav_menu().
	register_nav_menus( array(
		'primary'       => esc_html__( 'Main Navigation', 'businesspress' ),
		'header-social' => esc_html__( 'Header Social Links', 'businesspress' ),
		'footer'        => esc_html__( 'Footer Menu', 'businesspress' ),
		'footer-social' => esc_html__( 'Footer Social Links', 'businesspress' ),
	) );
	
	// Switch default core markup to output valid HTML5.
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Setup the WordPress core custom header feature.
	add_theme_support( 'custom-header', apply_filters( 'businesspress_custom_header_args', array(
		'default-image' => get_parent_theme_file_uri( '/images/header.jpg' ),
		'width'         => 1280,
		'height'        => 540,
		'flex-width'    => true,
		'flex-height'   => true,
		'header-text'   => false,
	) ) );
	register_default_headers( array(
	'default-image' => array(
		'url'           => '%s/images/header.jpg',
		'thumbnail_url' => '%s/images/header.jpg',
		'description'   => __( 'Default Header Image', 'businesspress' )
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for full width images
	add_theme_support( 'align-wide' );

	// This theme styles the visual editor to resemble the theme style.
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'css/editor-style.css' ) );
	
}
endif; // businesspress_setup
add_action( 'after_setup_theme', 'businesspress_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function businesspress_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'businesspress' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'This is the normal sidebar for blog pages. If you do not use this sidebar or Blog Sticky Sidebar, blog pages will be a one-column design.', 'businesspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sticky Sidebar', 'businesspress' ),
		'id'            => 'sidebar-1-s',
		'description'   => esc_html__( 'Displays while following the PC\'s scrolling.', 'businesspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'businesspress' ),
		'id'            => 'sidebar-page',
		'description'   => esc_html__( 'This is the normal sidebar for static pages. If you do not use this sidebar or Page Sticky Sidebar, static pages will be a one-column design.', 'businesspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Page Sticky Sidebar', 'businesspress' ),
		'id'            => 'sidebar-page-s',
		'description'   => esc_html__( 'Displays while following the PC\'s scrolling.', 'businesspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'businesspress' ),
		'id'            => 'footer-1',
		'description'   => __( 'You can set the width of footers from Customize. If you do not use a footer widget, nothing will be displayed.', 'businesspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'businesspress' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'businesspress' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'businesspress' ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 5', 'businesspress' ),
		'id'            => 'footer-5',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 6', 'businesspress' ),
		'id'            => 'footer-6',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'businesspress_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function businesspress_scripts() {
	wp_enqueue_style( 'fontawesome', get_theme_file_uri( '/inc/font-awesome/css/font-awesome.css' ), array(), '4.7.0' );
	wp_enqueue_style( 'normalize', get_theme_file_uri( '/css/normalize.css' ),  array(), '8.0.0' );
	wp_enqueue_style( 'businesspress-style', get_stylesheet_uri(), array(), '1.0.0' );

	wp_enqueue_script( 'fitvids', get_theme_file_uri( '/js/jquery.fitvids.js' ), array(), '1.1', true );
	if ( is_home() && ! is_paged() && get_theme_mod( 'businesspress_enable_featured_slider' ) ) {
		wp_enqueue_script( 'slick', get_theme_file_uri( '/js/slick.js' ), array( 'jquery' ), '1.9.0', true );
		wp_enqueue_style( 'slick-style', get_theme_file_uri( '/css/slick.css' ), array(), '1.9.0' );
	}
	if ( is_active_sidebar( 'sidebar-1-s' ) || is_active_sidebar( 'sidebar-page-s' ) ) {
		wp_enqueue_script( 'stickyfill', get_theme_file_uri( '/js/stickyfill.js' ), array( 'jquery' ), '2.1.0' );
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'businesspress-functions', get_theme_file_uri( '/js/functions.js' ), array( 'jquery' ), '20180907', true );
	wp_enqueue_script( 'businesspress-navigation', get_theme_file_uri( '/js/navigation.js' ), array( 'jquery' ), '1.0.0', true );
		$businesspress_l10n = array();
		$businesspress_l10n['expand']         = __( 'Expand child menu', 'businesspress' );
		$businesspress_l10n['collapse']       = __( 'Collapse child menu', 'businesspress' );
		wp_localize_script( 'businesspress-navigation', 'businesspressScreenReaderText', $businesspress_l10n );
	wp_enqueue_script( 'businesspress-skip-link-focus-fix', get_theme_file_uri( '/js/skip-link-focus-fix.js' ), array(), '20160525', true );
	wp_enqueue_script( 'businesspress-yoshino', get_theme_file_uri( '/js/yoshino.js' ), array(), true );
	
	//カルーセル
	
	wp_enqueue_style( 'Swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css', array(), true );
	wp_enqueue_script( 'Swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), true );
}
add_action( 'wp_enqueue_scripts', 'businesspress_scripts' );

/**
 * Add custom classes to the body.
 */
function businesspress_body_classes( $classes ) {

	if ( get_theme_mod( 'businesspress_hide_blogname' ) ) {
		$classes[] = 'hide-blogname';
	}
	if ( get_theme_mod( 'businesspress_hide_blogdescription' ) ) {
		$classes[] = 'hide-blogdescription';
	}
	if ( get_theme_mod( 'businesspress_hide_date' ) ) {
		$classes[] = 'hide-date';
	}
	if ( get_theme_mod( 'businesspress_hide_author' ) ) {
		$classes[] = 'hide-author';
	}
	if ( get_theme_mod( 'businesspress_hide_comments_number' ) ) {
		$classes[] = 'hide-comments-number';
	}

	if ( ( is_home()    && '3-column' == get_theme_mod( 'businesspress_content' ) ) ||
		 ( is_archive() && '3-column' == get_theme_mod( 'businesspress_content_archive' ) ) ||
		 ( is_search()  && '3-column' == get_theme_mod( 'businesspress_content_search' ) ) ) {
		$classes[] = 'three-column';
	} elseif ( ( ( is_home() || is_archive() || is_search() || is_single() ) && ( is_active_sidebar( 'sidebar-1' ) || is_active_sidebar( 'sidebar-1-s' ) ) ) ||
			   ( is_page() && ( is_active_sidebar( 'sidebar-page' ) || is_active_sidebar( 'sidebar-page-s' ) ) && ! is_page_template( 'nosidebar.php' ) ) ) {
		$classes[] = 'has-sidebar';
	} else {
		$classes[] = 'no-sidebar';
	}

	if ( ( is_home()    && '2-column' == get_theme_mod( 'businesspress_content' ) ) ||
		 ( is_archive() && '2-column' == get_theme_mod( 'businesspress_content_archive' ) ) ||
		 ( is_search()  && '2-column' == get_theme_mod( 'businesspress_content_search' ) ) ) {
		$classes[] = 'two-column';
	}

	if ( get_option( 'show_avatars' ) ) {
		$classes[] = 'has-avatars';
	}

	return $classes;
}
add_filter( 'body_class', 'businesspress_body_classes' );


/**
 * Custom template tags for this theme.
 */
require get_theme_file_path( '/inc/template-tags.php' );

/**
 * Custom widgets for this theme.
 */
require get_theme_file_path( '/inc/widgets.php' );

/**
 * Custom functions that act independently of the theme templates.
 */
require get_theme_file_path( '/inc/extras.php' );

/**
 * Customizer additions.
 */
require get_theme_file_path( '/inc/customizer.php' );

/**
 * Set CSS for Customizer options.
 */
require get_theme_file_path( '/inc/customizer-css.php' );

/**
 * Load Jetpack compatibility file.
 */
require get_theme_file_path( '/inc/jetpack.php' );

/**
 * Set auto update.
 */
require get_theme_file_path( '/inc/theme_update_check.php' );
$KernlUpdater = new ThemeUpdateChecker(
	'businesspress',
	'https://kernl.us/api/v1/theme-updates/5cc9457dce9a6f19c76ab84e/'
);


////////////////////////////////////////
// ゼミ一覧


function getNewItems($atts) {
	extract(shortcode_atts(array(
		"num" => '',	//最新記事リストの取得数
		"cat" => ''	//表示する記事のカテゴリー指定
	), $atts));
	global $post;
	$oldpost = $post;
	$myposts = get_posts('post_type=seminar&numberposts='.$num.'&order=DESC&orderby=post_date&taxonomy=seminar&term='.$cat);
	$retHtml='<div class="blogs_flex">';
	foreach($myposts as $post) :
	$cat = get_the_terms($post->ID,'seminar-college');//get_the_categoryをget_the_termsに変更
    $catname = $cat[0]->cat_name;
    $catslug = $cat[0]->slug;
        setup_postdata($post);
         $retHtml.='<div class="topItemList">';
        $retHtml.='<span class="news_thumb"><a href="'.get_permalink().'">';
        if(has_post_thumbnail()){ $retHtml.=get_the_post_thumbnail($page->ID,'medium');
}else{
$retHtml.='<span class="noimg"><i class="fa fa-camera" aria-hidden="true"></i></span>';}
$retHtml.='</a></span>';
	$retHtml.='<div class="post_desc">';
	$retHtml.='<span class="news_date">'.get_the_date().'</span>';
        $retHtml.='<span class="news_title"><a href="'.get_permalink().'">'.the_title("","",false).'</a></span>';
        $retHtml.='</div>';
        $retHtml.='</div>';
    endforeach;
	$retHtml.='</div>';
	$post = $oldpost;
	wp_reset_postdata();
	return $retHtml;
}
add_shortcode("bloglist", "getNewItems");

////////////////////////////////////////
// ゼミ一覧2

/**
 * 最新の固定ページや投稿のタイトルをリスト表示するショートコードです。
 */
function recent_posts_shortcode( $atts ) {
	global $post;

	$atts = shortcode_atts( array(
		'class'       => 'recent-posts',
		'count'       => '10',
		'post_type'   => 'any',
		'category'    => '',
		'exclude'     => '',
		'orderby'     => 'modified',
		'date_format' => '',
		'new_days'    => '30',
		'taxonomy'    => '',
		'terms'       => '',
	), $atts, 'recent_posts' );

	$args = array(
		'showposts'        => $atts['count'],
		'post_type'        => $atts['post_type'],
		'orderby'          => $atts['orderby'],
		'order'            => 'DESC',
		'category_name'    => $atts['category'],
		'exclude'          => $atts['exclude'],
		'suppress_filters' => false,
	);

	if ( ! empty( $atts['taxonomy'] ) ) {
		$args['tax_query'] = array( array(
			'taxonomy' => $atts['taxonomy'],
			'field'    => 'slug',
			'terms'    => $atts['terms'],
		) );
	}

	$myposts = get_posts( $args );

	$new_days = $atts['new_days'];
	$prev_date = null;
	$retour = '<div class="loop-wrapper-college"><div' . ( $atts['class'] != '' ? ' class="' . esc_attr( $atts['class'] ). '"' : '' ) . '>';
	foreach ( $myposts as $post ) {
		setup_postdata( $post );

		$today = date( 'U' );
		$entry = $atts['orderby'] == 'modified' ? get_the_modified_date( 'U' ) : get_the_date( 'U' );
		$diff = date( 'U', ( $today - $entry ) ) / 86400;

		$date = $atts['orderby'] == 'modified' ? get_the_modified_date( $atts['date_format'] ) : get_the_date( $atts['date_format'] );
		if ( $prev_date === $date ) {
			$date = '&nbsp;';
		} else {
			$prev_date = $date;
		}

		//$retour .= '<dt>' . esc_html( $date ) . '</dt>';
		
		$retour .= '<div class="post-full">';
		$retour .= '<a href="' . get_permalink() . '">';
		$retour .= '<div class="post-thumbnail">' . get_the_post_thumbnail($page->ID,'medium') . '</div>';
	//	$retour .= '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
	//	$retour .= '<div class="contents-L">' . ( $new_days > $diff ? '<span class="recent-posts-new">NEW!</span>' : '' ) . '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a></div>';
	//	$retour .= '<dd>' . ( $new_days > $diff ? '<span class="recent-posts-new">NEW!</span>' : '' ) . '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a></dd>';
		$retour .= '<div class="container-in-bottom">';
		$retour .= '<div class="contents-L">';
	//	$retour .= '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
		$retour .=  '<h3>' .the_title( '', '', false ).'</h3>';
		$retour .= '</div>';
		$retour .= '<div class="contents-R">' .'<span class="news_date">'.get_the_date().'</span>'.'</div>';
		$retour .= '</div>';
		$retour .= '</a>';
		$retour .= '</div>';

	}
	wp_reset_postdata();
	$retour .= '</div> </div>' . "\n";

	return $retour;
}

add_shortcode( 'recent_posts', 'recent_posts_shortcode' );







////////////////////////////////////////

// 企業詳細ページの追加

add_action( 'init', 'custom_post_type' );

function custom_post_type() {


  register_post_type( 'company', // カスタム投稿タイプのスラッグの指定
    array(
      'labels' => array(
        'name' => __( '企業一覧' ),          // メニューに表示されるアサル（ASAL）
        'singular_name' => __( '企業' ), // 単体系のアサル（ASAL）
        'add_new' => _x('新規追加', 'company'),        // 新規追加のアサル（ASAL）
        'add_new_item' => __('新規追加')            // 新規追加のアサル（ASAL）
      ),
      'public' => true,                 // 投稿タイプをパブリックにする
      'has_archive' => true,            // アーカイブを有効にする
      'hierarchical' => true,          // ページ階層の指定
      'menu_position' =>5,              // 管理画面上の配置指定
      'menu_icon' => 'dashicons-edit',  // アイコン
      'supports' => array('title','editor','thumbnail','revisions','author') // サポート指定
      // 全てのサポートを使う場合は下記参照
      //'supports' => array('title','editor','thumbnail','custom-fields','excerpt','author','trackbacks','comments','revisions','page-attributes')
    )
  );

  

  register_post_type( 'college', // カスタム投稿タイプのスラッグの指定
  array(
	'labels' => array(
	  'name' => __( '学校一覧' ),          // メニューに表示されるアサル（ASAL）
	  'singular_name' => __( '学校' ), // 単体系のアサル（ASAL）
	  'add_new' => _x('新規追加', 'college'),        // 新規追加のアサル（ASAL）
	  'add_new_item' => __('新規追加')            // 新規追加のアサル（ASAL）
	),
	'public' => true,                 // 投稿タイプをパブリックにする
	'has_archive' => true,            // アーカイブを有効にする
	'hierarchical' => true,          // ページ階層の指定
	'menu_position' =>5,              // 管理画面上の配置指定
	'menu_icon' => 'dashicons-edit',  // アイコン
	'supports' => array('title','editor','thumbnail','revisions','author') // サポート指定
	// 全てのサポートを使う場合は下記参照
	//'supports' => array('title','editor','thumbnail','custom-fields','excerpt','author','trackbacks','comments','revisions','page-attributes')
  )
);


	register_post_type( 'seminar', // カスタム投稿タイプのスラッグの指定
	array(
	'labels' => array(
		'name' => __( 'ゼミ一覧' ),          // メニューに表示されるアサル（ASAL）
		'singular_name' => __( 'ゼミ' ), // 単体系のアサル（ASAL）
		'add_new' => _x('新規追加', 'seminar'),        // 新規追加のアサル（ASAL）
		'add_new_item' => __('新規追加')            // 新規追加のアサル（ASAL）
	),
	'public' => true,                 // 投稿タイプをパブリックにする
	'has_archive' => true,            // アーカイブを有効にする
	'hierarchical' => true,          // ページ階層の指定
	'menu_position' =>5,              // 管理画面上の配置指定
	'menu_icon' => 'dashicons-edit',  // アイコン
	'supports' => array('title','editor','thumbnail','revisions','author') // サポート指定
	// 全てのサポートを使う場合は下記参照
	//'supports' => array('title','editor','thumbnail','custom-fields','excerpt','author','trackbacks','comments','revisions','page-attributes')
	)
	);

}





// タクソノミーを追加
function add_taxonomy() {

	register_taxonomy(
	  'company_category', // タクソノミースラッグ
	  'company',          // 使用するカスタム投稿タイプを指定
	  array(
		'public' => true,                 // 投稿タイプをパブリックにする
      	'has_archive' => true,            // アーカイブを有効にする
		'hierarchical' => true,          // 階層を持たせるかを指定(trueでカテゴリー、falseでタグ)
		'label' => '業種',          // メニューに表示されるアサル（ASAL）
		'singular_label' => '業種一覧', // 単体系のアサル（ASAL）
		'public' => true,                // 投稿タイプをパブリックにする
		'show_ui' => true ,               // 管理画面上に編集画面を表示するかを指定
		'page-attributes',
	  )
	);
  
	register_taxonomy(
	  'company_area', // タクソノミースラッグ
	  'company',          // 使用するカスタム投稿タイプを指定
	  array(
		'public' => true,                 // 投稿タイプをパブリックにする
      	'has_archive' => true,            // アーカイブを有効にする
		'hierarchical' => true,          // 階層を持たせるかを指定(trueでカテゴリー、falseでタグ)
		'label' => 'エリア',          // メニューに表示されるアサル（ASAL）
		'singular_label' => 'エリア', // 単体系のアサル（ASAL）
		'public' => true,                // 投稿タイプをパブリックにする
		'show_ui' => true,                // 管理画面上に編集画面を表示するかを指定
		'page-attributes',
	  )
	);


  
  }
  add_action( 'init', 'add_taxonomy' );


/* ---------- カスタム投稿タイプを追加 ---------- */




function create_post_type() {

  register_post_type(
    'college',
    array(
      'label' => '学校一覧',
      'public' => true,
      'has_archive' => true,
      'show_in_rest' => true,
      'menu_position' => 5,
      'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'revisions',
		'author',
		'page-attributes',
      ),
    )
  );

  register_taxonomy(
    'college-area',
    'college',
    array(
      'label' => 'エリア',
      'hierarchical' => true,
      'public' => true,
      'show_in_rest' => true,
    )
  );
  
}

add_action( 'init', 'create_post_type' );



/* ---------- カスタム投稿タイプを追加 「ゼミ」---------- */




function seminar_post_type() {

  register_post_type(
    'seminar',
    array(
      'label' => 'ゼミ一覧',
      'public' => true,
      'has_archive' => true,
      'show_in_rest' => true,
      'menu_position' => 5,
      'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'revisions',
		'author',
		'page-attributes',
      ),
    )
  );

  register_taxonomy(
    'seminar-area',
    'seminar',
    array(
      'label' => 'エリア',
      'hierarchical' => true,
      'public' => true,
      'show_in_rest' => true,
    )
  );
  


  register_taxonomy(
    'seminar-college',
    'seminar',
    array(
      'label' => '学校',
      'hierarchical' => true,
      'public' => true,
      'show_in_rest' => true,
	  
    )
  );

  
}

add_action( 'init', 'seminar_post_type' );



   /**
   * 指定したタームの子どもを再帰的に取得してリスト出力
   *
   * @param string $taxonomy_slug
   * @param \WP_Term|null $parent_term
   * @return void
   */
  function taxonomy_tree($taxonomy_slug, $parent_term = null ) {
    $parent_term_id = 0;
    if ($parent_term !== null) {
      $parent_term_id = $parent_term->term_id;
    }

    $terms = get_terms($taxonomy_slug, array('hide_empty' => true, 'parent' => $parent_term_id));

    if (!empty($terms)) {
      echo '<ul>';
      foreach ($terms as $term) {
        echo '<li>';
          echo '<a href="' . get_term_link($term) . '">' . $term->name . '（' . $term->count . '）</a>';
          taxonomy_tree($taxonomy_slug, $term);
        echo '</li>';
      }
      echo '</ul>';
    }
  }


// 管理画面の投稿一覧をログイン中のユーザーの投稿のみに制限する(管理者以外)
function pre_get_author_posts( $query ) {
    if ( is_admin() && !current_user_can('administrator') && $query->is_main_query()
            && ( !isset($_GET['author']) || $_GET['author'] == get_current_user_id() )) {
        $query->set( 'author', get_current_user_id() );
        unset($_GET['author']);
    }
}
add_action( 'pre_get_posts', 'pre_get_author_posts' );
function count_author_posts( $counts, $type = 'post', $perm = '' ) {
  if ( !is_admin() || current_user_can('administrator') ) {
    return $counts;
  }
  global $wpdb;
  if ( ! post_type_exists( $type ) )
    return new stdClass;
  $cache_key = _count_posts_cache_key( $type, $perm ) . '_author'; // 2
  $counts = wp_cache_get( $cache_key, 'counts' );
  if ( false !== $counts ) {
    return $counts;
  }
  $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
  $query .= $wpdb->prepare( " AND ( post_author = %d )", get_current_user_id() );
  $query .= ' GROUP BY post_status';
 
  $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
  $counts = array_fill_keys( get_post_stati(), 0 );
  foreach ( $results as $row ) {
    $counts[ $row['post_status'] ] = $row['num_posts'];
  }
  $counts = (object) $counts;
  wp_cache_set( $cache_key, $counts, 'counts' );
  return $counts;
}
add_filter( 'wp_count_posts', 'count_author_posts', 10, 3 );


//編集者権限でも自分がアップしたメディアにしか表示されないようにする
if (! current_user_can('administrator') ) {
	function display_only_self_uploaded_medias( $query ) {
	  if ( $user = wp_get_current_user() ) {
		$query['author'] = $user->ID;
	  }
	  return $query;
	}
	add_action( 'ajax_query_attachments_args', 'display_only_self_uploaded_medias' );
	}


	
	add_action('admin_menu', 'myplugin_add_custom_box');
	function myplugin_add_custom_box()
	{
		if (function_exists('add_meta_box')) {
			add_meta_box('myplugin_sectionid', __('作成者', 'myplugin_textdomain'), 'post_author_meta_box', 'custom_post_name', 'advanced');
		}
	}
	