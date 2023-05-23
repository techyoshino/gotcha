<?php
/*
Plugin Name: FE Advanced Search
Plugin URI: https://fe-advanced-search.com/
Description: 複合的な条件（カテゴリ/カスタムタクソノミ/タグ/年月/カスタムフィールド/キーワード）による絞り込み検索機能を提供します。複数の検索フォームの作成・管理ができます。ショートコードで任意の投稿・ページ・ウィジェット等に検索フォームを設置できます。検索結果のソート（並べ替え）ができます。
Author: FirstElement, Inc.
Author URI: https://www.firstelement.co.jp/
Version: 1.8.2
*/

/*============================
	設定・共通関数読み込み
 ============================*/

/**
 *	フック
 */

// 設定ページ生成
add_action( 'admin_menu', 'feas_load_setting_pages', 10, 2 );

// 管理画面ヘッダーに出力するCSS（アイコンなど）
add_action( 'admin_print_styles', 'feas_add_inline_css' );

// 「デザイン」のCSSをヘッダーに出力
add_action( 'wp_head', 'feas_header_style', 10, 1 );

add_action( 'admin_notices', 'feas_admin_notices', 99 );

//add_action( 'upgrader_process_complete', 'feas_delete_transient_all',10, 2);

add_filter( 'posts_where', 'feas_posts_where', 10, 2 );

//add_filter( 'posts_join', 'join_datas', 10, 1 );

add_filter( 'posts_groupby', 'feas_posts_groupby', 10, 2 );

add_filter( 'posts_orderby', 'feas_posts_orderby', 10, 2 );

add_filter( 'posts_request', 'feas_posts_request', 10, 2 );

// add_filter( 'post_limits', 'feas_post_limits', 10, 2 );

// ajaxフィルタリング
add_filter( 'init', 'feas_retrun_child', 10 );

// iframe内にプレビューを生成
add_filter( 'init', 'feas_print_preview', 10 );

// プラグインアップデートチェック
add_filter( 'pre_set_site_transient_update_plugins', 'feas_pre_set_site_transient_update_plugins' );

// 詳細を表示
add_filter( 'plugins_api', 'feas_altapi_information', 10, 3 );

// アップデート関連デバッグ用
//add_action( 'init', 'feas_altapi_delete_transient' );

// 独自レポジトリURL
define( 'FEAS_ALT_API', 'https://download.fe-advanced-search.com/plugin-api/' );


function feas_posts_where( $where, $query ) {
	global $wp_query;	
	if ( isset( $_GET['csp'] ) && 'search_add' == $_GET['csp'] && $query->is_main_query() ) {		
		$wp_query->is_search = true;
		$wp_query->is_home  = false;	
		// 検索本体
		$where = search_where_add( $where );
	} 
	return $where;
}

function feas_posts_orderby( $orderby, $query ) {
	if ( isset( $_GET['csp'] ) && 'search_add' == $_GET['csp'] && $query->is_main_query() ) {	
		// ソート本体
		$orderby = custom_sort( $orderby );
	} 
	return $orderby;
}

function feas_posts_request( $request, $query ) {
	if ( isset( $_GET['csp'] ) && 'search_add' == $_GET['csp'] && $query->is_main_query() ) {	
		// カスタムフィールドでソートする際に付加するSQLを生成
		$request = sort_add_field( $request ); 
	} 
	return $request;
}

function feas_posts_join( $join, $query ) {
	if ( isset( $_GET['csp'] ) && 'search_add' == $_GET['csp'] && $query->is_main_query() ) {
		$join = join_datas( $join );
	} 
	return $join;
}

function feas_posts_groupby( $groupby, $query ) {
	if ( isset( $_GET['csp'] ) && 'search_add' == $_GET['csp'] && $query->is_main_query() ) {
		$groupby = groupby_datas( $groupby );
	} 
	return $groupby;
}

// テンプレート側でソートするために全件取得（LIMITなし）
function feas_post_limits( $limit, $query ) {
	if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
		return 'LIMIT 0, 99999';
	}
	return $limit;
}

function feas_load_admin_css() {
    wp_enqueue_style( 'feas_admin', plugin_dir_url( __FILE__ ) . 'admin.css' );
}

/*
function feas_load_admin_js() {
    wp_enqueue_script( 'feas_admin', plugin_dir_url( __FILE__ ) . 'admin.js' );
}
*/


/*============================
	設定カラムのプリフィクス
 ============================*/
 
// 検索フォーム
// **連番の必要性あり**
$cols = array(
	 0 => 'feadvns_disp_number_', 
	 1 => 'feadvns_disp_',
	 2 => 'feadvns_par_cat_',
	 3 => 'feadvns_label_',
	 4 => 'feadvns_kind_',
	 5 => 'feadvns_disp_op_order_',
	 6 => 'feadvns_search_and_',
	 7 => 'feadvns_before_',
	 8 => 'feadvns_after_',
	 9 => 'feadvns_del_',
	10 => 'feadvns_dchi_',
	11 => 'feadvns_exclude_cat_',
	12 => 'feadvns_order_', // 未使用
	13 => 'feadvns_kwds_target_',
	14 => 'feadvns_nocnt_emptycat_',
	15 => 'feadvns_kwds_yuragi_',
	16 => 'feadvns_cf_range_',
	17 => 'feadvns_cf_unit_',
	18 => 'feadvns_cf_kugiri_',
	19 => 'feadvns_kwds_ajax_',
	20 => 'feadvns_cf_specify_key_',
	21 => 'feadvns_cf_specify_key_switch', // 未使用
	22 => 'feadvns_cf_range_free_input_',
	23 => 'feadvns_cache_number_', // 未使用
	24 => 'feadvns_cf_shingi_', // 将来的に33に統合
	25 => 'feadvns_cf_shingi_text_',
	26 => 'feadvns_cf_tani_position_',
	27 => 'feadvns_noselect_text_',
	28 => 'feadvns_default_select_', // 未使用
	29 => 'feadvns_cf_range_as_',
	30 => 'feadvns_kwds_placeholder_',
	31 => 'feadvns_noselect_text_status_',
	32 => 'feadvns_cf_value_',
	33 => 'feadvns_cf_scf_',
	34 => 'feadvns_disp_op_order_cf_as_',
	35 => 'feadvns_disp_op_order_sort_',
	36 => 'feadvns_disp_op_order_freetext_',
);

// ソートボタン
$cols_order = array(
	 0 => 'feadvns_order_target_',
	 1 => 'feadvns_order_display_',
	 2 => 'feadvns_order_sort_',
	 3 => 'feadvns_order_del_',
	 4 => 'feadvns_order_before_',
	 5 => 'feadvns_order_after_',
	 6 => 'feadvns_order_label_',
	 7 => 'feadvns_order_asc_',
	 8 => 'feadvns_order_desc_',
	 9 => 'feadvns_sort_target_cfkey_',
	10 => 'feadvns_sort_target_cfkey_as_'
);

// Transient用のキー
$cols_transient = array(
	 0 => 'archive',
	 1 => 'taxonomy',
	 2 => 'post_meta',
	 3 => 'tag',
	 4 => 'target_post_type',
	 5 => 'default_taxonomy',
	 6 => 'exclude_ids',
	 7 => 'exclude_sp',
	 8 => 'total_cnt',
);

// 除外する記事ID
$feadvns_exclude_id = 'feadvns_exclude_id_';

// 除外するタームID
$feadvns_exclude_term_id = 'feadvns_exclude_term_id_';

// 除外するカスタムフィールド
$feadvns_exclude_cf = 'feadvns_exclude_cf_';

// 除外するカスタムフィールドの値
//$feadvns_exclude_cf_val = 'feadvns_exclude_cf_val_';

// 現在編集中のフォームID
$feadvns_current_form = "feadvns_current_form";

// 保存時に選択していたタブ
$feadvns_search_current_tab = 'feadvns_search_current_tab';
$feadvns_sort_current_tab = 'feadvns_sort_current_tab';

// linecountの付属語
$feadvns_max_line = "feadvns_max_line_";

// linecountの付属語（ソート用）
$feadvns_max_line_order = "feadvns_max_line_order";

// search_button_labelの付属語
$feadvns_search_b_label = "feadvns_search_button_label_";

// フォームID
//$manag_no = 0;

// ソートID
$manag_order_no = 0;

// フォームIDのプリフィクス
$feadvns_form_no = "feadvns_form_no_";

// フォームIDリストのプリフィクス
$feadvns_form_no_list = "feadvns_form_no_";

// 累積フォーム生成数のプリフィクス
$feadvns_autoinc_no = "feadvns_autoinc_no";

// ソートIDのプリフィクス
$feadvns_order_no = "feadvns_order_no_";

// 何ページあるかを格納するkey
$feadvns_max_page = "feadvns_max_page";

// 取得するstyleを使うかのkeyを設定
$use_style_key = "feadvns_style_use_";
// 取得するstyleのkeyを設定
$style_body_key = "feadvns_style_body_";

// 検索結果の並び順
$feadvns_sort_target = "feadvns_sort_target_";
$feadvns_sort_order = "feadvns_sort_order_";
$feadvns_sort_target_cfkey = "feadvns_sort_target_cfkey_";
$feadvns_sort_target_cfkey_as = "feadvns_sort_target_cfkey_as_";
$feadvns_sort_target_2nd = "feadvns_sort_target_2nd_";
$feadvns_sort_order_2nd = "feadvns_sort_order_2nd_";
$feadvns_sort_target_cfkey_2nd = "feadvns_sort_target_cfkey_2nd_";
$feadvns_sort_target_cfkey_as_2nd = "feadvns_sort_target_cfkey_as_2nd_";

// ソート時にカスタムフィールドを判別する付属語
$meta_sort_key = "meta_";

// プレビューで「フォーム外観」CSSを反映するかどうかの設定付
$pv_css = 'feadvns_pv_css_';
$pv_theme_css = 'feadvns_pv_theme_css_';

// 各フォームのタイトルの付属語
$feadvns_search_form_name = 'feadvns_search_form_name_';

// 検索対象のpost_type指定の付属語
$feadvns_search_target = 'feadvns_search_target_';

// 初期設定カテゴリの付属語
$feadvns_default_cat = 'feadvns_default_cat_';

// 検索条件未指定時オプションの付属語
$feadvns_empty_request = 'feadvns_empty_request_';

// ドロップダウン内に件数表示の付属語
$feadvns_show_count = 'feadvns_show_count_';

// Sticky Postsを検索対象に含む設定の付属語
$feadvns_include_sticky = 'feadvns_include_sticky_';

// フリーワード検索のターゲット指定の付属語
$feadvns_kwds_targets = 'feadvns_kwds_targets_';

// フリーワード検索のゆらぎ検索指定の付属語
$feadvns_kwds_yuragi = 'feadvns_kwds_yuragi_';

// キャッシュ
$feas_cache_enable = 'feas_cache_enable';
$feas_cache_time = 'feas_cache_time';

/*============================
	プラグイン設定ページ登録
 ============================*/

function feas_add_inline_css() {
	echo '<style>div.dashicons-feas:before{content:"\f179";}</style>'."\n";
}

function feas_load_setting_pages() {
	$feas_suffix_search     = add_menu_page( '検索', '検索', 'manage_options', 'feas_management', 'feas_management', 'dashicons-feas', null );
	$feas_suffix_sort       = add_submenu_page( 'feas_management', 'ソート &laquo; 検索', 'ソート', 'manage_options', 'feas_sort_management', 'feas_sort_management' );
	$feas_suffix_design     = add_submenu_page( 'feas_management', 'デザイン &laquo; 検索', 'デザイン', 'manage_options', 'feas_style_management', 'feas_style_management' );
	$feas_suffix_cache      = add_submenu_page( 'feas_management', 'キャッシュ &laquo; 検索', 'キャッシュ', 'manage_options', 'feas_cache_management', 'feas_cache_management' );
	$feas_suffix_management = add_submenu_page( 'feas_management', '管理 &laquo; 検索', '管理', 'manage_options', 'feas_backup_management', 'feas_backup_management' );

	add_action( 'load-' . $feas_suffix_search, 'feas_load_scripts_on_search' );
	add_action( 'load-' . $feas_suffix_sort, 'feas_load_scripts_on_sort' );	
	add_action( 'load-' . $feas_suffix_design, 'feas_load_scripts_on_design' );
	add_action( 'load-' . $feas_suffix_cache, 'feas_load_scripts_on_cache' );
	add_action( 'load-' . $feas_suffix_management, 'feas_load_scripts_on_management' );
}
/**
 *	ファイル読み込み
 */
require_once( dirname( __FILE__ ) . '/functions.php' );
require_once( dirname( __FILE__ ) . '/form-controller.php' );
require_once( dirname( __FILE__ ) . '/search-controller.php' );
require_once( dirname( __FILE__ ) . '/result-controller.php' );
require_once( dirname( __FILE__ ) . '/sort-controller.php' );

/**
 *	管理トップページ
 */
function feas_management() {
	feas_func_management( 'management' );
}

/**
 *	フォームのstyleの設定画面
 */
function feas_style_management() {
	feas_func_management( 'style_management' );
}

/**
 *	ソート設定画面
 */
function feas_sort_management() {
	feas_func_management( 'sort_management' );
}

/**
 *	キャッシュ設定画面
 */
function feas_cache_management() {
	feas_func_management( 'cache_management' );
}

/**
 *	インポート/エクスポート
 */
function feas_backup_management() {
	feas_func_management( 'backup_management' );
}

/**
 *
 *	設定ページごとに読み込む内容
 *
 */

// 検索 > 検索
function feas_load_scripts_on_search() { 

	// CSSを読み込む
	add_action( 'admin_enqueue_scripts', 'feas_load_admin_css', 10, 1 );
	
	// JSを読み込む
	// add_action( 'admin_enqueue_scripts', 'feas_load_admin_js', 10, 1 );
}

// 検索 > ソート
function feas_load_scripts_on_sort() { 

	// CSSを読み込む
	add_action( 'admin_enqueue_scripts', 'feas_load_admin_css', 10, 1 );
	
	// JSを読み込む
	// add_action( 'admin_enqueue_scripts', 'feas_load_admin_js', 10, 1 );
}

// 検索 > デザイン
function feas_load_scripts_on_design() { 

	// CSSを読み込む
	add_action( 'admin_enqueue_scripts', 'feas_load_admin_css', 10, 1 );

	// JSを読み込む
	// add_action( 'admin_enqueue_scripts', 'feas_load_admin_js', 10, 1 );
		
	// コードエディターにCodeMirrorを適用
	$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
	wp_localize_script('jquery', 'cm_settings', $cm_settings);
	wp_enqueue_script('wp-theme-plugin-editor');
	wp_enqueue_style('wp-codemirror');
}

// 検索 > キャッシュ
function feas_load_scripts_on_cache() { 

	// CSSを読み込む
	add_action( 'admin_enqueue_scripts', 'feas_load_admin_css', 10, 1 );

	// JSを読み込む
	// add_action( 'admin_enqueue_scripts', 'feas_load_admin_js', 10, 1 );
}

// 検索 > 管理
function feas_load_scripts_on_management() { 

	// CSSを読み込む
	add_action( 'admin_enqueue_scripts', 'feas_load_admin_css', 10, 1 );

	// JSを読み込む
	// add_action( 'admin_enqueue_scripts', 'feas_load_admin_js', 10, 1 );
}

/*============================
	ショートコード
 ============================*/
add_shortcode( 'create-searchform', 'feas_search_form_shortcode' );
add_shortcode( 'feas-count-posts', 'feas_search_count_shortcode' );
add_shortcode( 'feas-sort-menu', 'feas_shortcode_sort_menu' );
// 新
add_shortcode( 'feas-search-form', 'feas_search_form_shortcode' );
add_shortcode( 'feas-search-count', 'feas_search_count_shortcode' );
add_shortcode( 'feas-search-query', 'feas_search_query_shortcode' );

/**
 *	検索フォーム
 */
function feas_search_form_shortcode( $id = 0 ) {
	if ( isset( $id['id'] ) == true ) {
		$id = $id['id'];
	} else {
		$id = 0;
	}
	return feas_search_form( $id, 'shortcode' );
}

/**
 *	該当記事数
 */
function feas_search_count_shortcode( $form_id = 0 ) {
	if ( isset( $form_id['id'] ) ) {
		$form_id = $form_id['id'];
	} else {
		$form_id = 0;
	}
	return feas_search_count( $form_id, false );
}

/**
 *	検索クエリ
 */
function feas_search_query_shortcode( $atts ) {
	
	$options = shortcode_atts( array(
		'sep'    => ',',
		'before' => '<span>',
		'after'  => '</span>',
	), $atts );
	
	$separator = $options['sep'];
	$before    = $options['before'];
	$after     = $options['after'];
	
	return feas_search_query( false, $separator, $before, $after, true );
}

/**
 *	ソートボタンを表示
 */
function feas_shortcode_sort_menu( $id = 0 ) {
	if ( isset( $id['id'] ) == true ) {
		$id = $id['id'];
	} else {
		$id = 0;
	}	
	return feas_sort_menu( $id, 'shortcode_f' );
}

/*============================
	自動アップデート
 ============================*/
global $wp_version;

/**
 *	アップデートチェック
 */
function feas_pre_set_site_transient_update_plugins( $transient ) {
	
	// fe-advanced-search/plugin.php
	$plugin_slug = plugin_basename( __FILE__ );
	
	$args = array(
		'action'      => 'update-check',
		'plugin_name' => $plugin_slug,
		'version'     => $transient->checked[$plugin_slug],
	);
	
 	// アップデートチェック
	$response = feas_altapi_request( $args );
		
    if ( false !== $response ) {
        
        // アップデートがある場合はトランジェントを更新
        $transient->response[$plugin_slug] = $response;
   
    } else {
	   
		// アップデートがない場合はダミーデータをかます（v5.5〜）
	    // Adding the "mock" item to the `no_update` property is required
        // for the enable/disable auto-updates links to correctly appear in UI.
        $item = (object) array(
            'id'            => 'fe-advanced-search/plugin.php',
            'slug'          => 'fe-advanced-search',
            'plugin'        => 'fe-advanced-search/plugin.php',
            'new_version'   => feas_plugin_get_version(),
            'url'           => '',
            'package'       => '',
            'icons'         => array(),
            'banners'       => array(),
            'banners_rtl'   => array(),
            'tested'        => '',
            'requires_php'  => '',
            'compatibility' => new stdClass(),
        );
        $transient->no_update['fe-advanced-search/plugin.php'] = $item;
    }
 
    return $transient;
}


/**
 *	最新版チェックを自社レポジトリに投げる
 */
function feas_altapi_request( $args ) {
	
	// リクエスト
	$request = wp_remote_post( FEAS_ALT_API, array( 'body' => $args ) );
	
	if ( is_wp_error( $request ) or wp_remote_retrieve_response_code( $request ) != 200 )
		return false;
	
	// レスポンス取得
	$response = unserialize( wp_remote_retrieve_body( $request ) );
	
	if ( is_object( $response ) ) {
		$new_version = $response->new_version;
		$now_version = feas_plugin_get_version();
		
		// 新バージョンがある場合レスポンスを返す
		if ( $now_version < $new_version )
			return $response;
		else
			return false;
			
	} else {
		
		return false;
	}
}

/**
 *	プラグイン情報取得
 */
function feas_altapi_information( $false, $action, $args ) {
	
	// plugin.php
	$plugin_slug = plugin_basename( __FILE__ );
	
	// スラッグで当プラグインのものかチェック
	if ( ( isset( $args->slug ) ) && ( $args->slug != $plugin_slug ) )
		return false;
	
	// POST
	$args = array(
		'action'      => 'plugin_information',
		'plugin_name' => $plugin_slug,
		'is_ssl' => is_ssl(),
		'fields' => array(
			'banners' => array(
				'low'  => '',
				'high' => 'https://download.fe-advanced-search.com/plugin-api/trunk/asset/banner-1841x1171.png',
				),
			'icons' => true,
			'reviews' => true,
			'downloaded' => false,
			'active_installs' => true
		)
		//'version' => $transient->checked[$plugin_slug],
	);
		
	// プラグイン情報を取得
	$response = feas_altapi_request( $args );
	
	// ダブリ？
	//$request = wp_remote_post( FEAS_ALT_API, array( 'body' => $args ) );
	
	return $response;
}

/**
 * Returns current plugin version.
 * プラグインのバージョンを返します
 *
 * @return string Plugin version
 */
function feas_plugin_get_version() {
	
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	
	// plugin.php
	$plugin_file = basename( ( __FILE__ ) );
	
	return $plugin_folder[$plugin_file]['Version'];
}

function feas_altapi_delete_transient() {
	delete_site_transient( 'update_plugins' );
}
