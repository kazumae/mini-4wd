<?php
/**
 * ミニ四駆学習会 from Nichinan City — テーマ関数
 *
 * - カスタム投稿タイプ「アーカイブ(spec)」の登録
 * - CSS/JS の読み込み
 * - 開催情報のメタボックス
 * - サンプルデータの自動投入（テーマ有効化時に1回だけ）
 */

if (!defined('ABSPATH')) {
    exit;
}

define('M4_VER', '1.0.0');

/* ────────────────────────────────────────────
 * テーマ基本セットアップ
 * ──────────────────────────────────────────── */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => 'グローバルナビ']);
});

/* ────────────────────────────────────────────
 * CSS / JS の読み込み
 * ──────────────────────────────────────────── */
add_action('wp_enqueue_scripts', function () {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    // レース風の見出し用フォント（Oswald）
    wp_enqueue_style(
        'm4-fonts',
        'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // メインスタイル（style.css）
    wp_enqueue_style(
        'm4-style',
        get_stylesheet_uri(),
        ['m4-fonts'],
        file_exists($dir . '/style.css') ? filemtime($dir . '/style.css') : M4_VER
    );

    // スクリプト（モバイルメニュー等）
    wp_enqueue_script(
        'm4-main',
        $uri . '/assets/js/main.js',
        [],
        file_exists($dir . '/assets/js/main.js') ? filemtime($dir . '/assets/js/main.js') : M4_VER,
        true
    );
});

/* ────────────────────────────────────────────
 * カスタム投稿タイプ「アーカイブ(spec)」
 * ──────────────────────────────────────────── */
add_action('init', function () {
    register_post_type('spec', [
        'labels' => [
            'name'          => 'アーカイブ',
            'singular_name' => 'アーカイブ',
            'add_new'       => '新規追加',
            'add_new_item'  => 'アーカイブを追加',
            'edit_item'     => 'アーカイブを編集',
            'new_item'      => '新しいアーカイブ',
            'view_item'     => 'アーカイブを表示',
            'all_items'     => 'アーカイブ一覧',
            'menu_name'     => 'アーカイブ',
        ],
        'public'        => true,
        'has_archive'   => 'archive',
        'menu_icon'     => 'dashicons-flag',
        'menu_position' => 5,
        'supports'      => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'rewrite'       => ['slug' => 'archive', 'with_front' => false],
        'show_in_rest'  => true,
    ]);
});

/* アーカイブ一覧は新しい順（開催日の降順）で表示 */
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) {
        return;
    }
    if ($q->is_post_type_archive('spec')) {
        $q->set('orderby', 'date');
        $q->set('order', 'DESC');
        $q->set('posts_per_page', 24);
    }
});

/* ────────────────────────────────────────────
 * 開催情報メタボックス
 * ──────────────────────────────────────────── */
function m4_meta_fields() {
    return [
        '_m4_year'            => ['開催年（例: 2026）', 'text'],
        '_m4_spec_no'         => ['Spec番号（例: 11 / 第1回）', 'text'],
        '_m4_date'            => ['開催日（例: 2026年6月15日）', 'text'],
        '_m4_venue'           => ['会場', 'text'],
        '_m4_result_url'      => ['レース結果URL', 'url'],
        '_m4_regulation_url'  => ['レギュレーションURL', 'url'],
        '_m4_timetable_url'   => ['タイムスケジュールURL', 'url'],
    ];
}

add_action('add_meta_boxes', function () {
    add_meta_box('m4_spec_meta', '開催情報', function ($post) {
        wp_nonce_field('m4_spec_meta', 'm4_spec_nonce');
        echo '<style>#m4_spec_meta label{display:block;margin:.6em 0 .2em;font-weight:600}#m4_spec_meta input{width:100%}</style>';
        foreach (m4_meta_fields() as $key => [$label, $type]) {
            $val = esc_attr(get_post_meta($post->ID, $key, true));
            $name = ltrim($key, '_');
            printf(
                '<label for="%1$s">%2$s</label><input type="%3$s" id="%1$s" name="%1$s" value="%4$s">',
                esc_attr($name), esc_html($label), esc_attr($type), $val
            );
        }
    }, 'spec', 'normal', 'high');
});

add_action('save_post_spec', function ($post_id) {
    if (!isset($_POST['m4_spec_nonce']) || !wp_verify_nonce($_POST['m4_spec_nonce'], 'm4_spec_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    foreach (m4_meta_fields() as $key => $def) {
        $name = ltrim($key, '_');
        if (isset($_POST[$name])) {
            update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$name])));
        }
    }
});

/* ────────────────────────────────────────────
 * ヘルパー
 * ──────────────────────────────────────────── */
function m4_meta($id, $key, $default = '') {
    $v = get_post_meta($id, $key, true);
    return ($v !== '' && $v !== false) ? $v : $default;
}

/* 投稿から安定した色相を生成（ポスターの色変化用） */
function m4_hue($id) {
    $no = (int) preg_replace('/[^0-9]/', '', m4_meta($id, '_m4_spec_no', (string) $id));
    if ($no === 0) {
        $no = $id;
    }
    return ($no * 47 + 200) % 360;
}

/* Spec番号のラベル（数値なら "Spec11"、それ以外はそのまま） */
function m4_spec_label($id) {
    $no = m4_meta($id, '_m4_spec_no', '');
    if ($no === '') {
        return get_the_title($id);
    }
    return is_numeric($no) ? 'Spec' . $no : $no;
}

/* ポスター（CSSで生成。画像差し替え時はアイキャッチを使用） */
function m4_poster($id, $variant = 'card') {
    if (has_post_thumbnail($id)) {
        printf(
            '<div class="m4-poster m4-poster--%s m4-poster--img">%s</div>',
            esc_attr($variant),
            get_the_post_thumbnail($id, 'large')
        );
        return;
    }
    $hue   = m4_hue($id);
    $label = m4_spec_label($id);
    printf(
        '<div class="m4-poster m4-poster--%1$s" style="--h:%2$d">'
        . '<span class="m4-poster__sub">Mini 4WD Study</span>'
        . '<span class="m4-poster__spec">%3$s</span>'
        . '<span class="m4-poster__line"></span>'
        . '<span class="m4-poster__tag">Manners Maketh Mini 4WD</span>'
        . '</div>',
        esc_attr($variant),
        (int) $hue,
        esc_html($label)
    );
}

/* ロゴ（歯車）SVG */
function m4_logo_mark() {
    return '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">'
        . '<path fill="currentColor" d="M19.4 13c.04-.33.06-.66.06-1s-.02-.67-.06-1l2.11-1.65a.5.5 0 0 0 .12-.64l-2-3.46a.5.5 0 0 0-.61-.22l-2.49 1a7.3 7.3 0 0 0-1.73-1l-.38-2.65A.49.49 0 0 0 13.5 1h-4a.49.49 0 0 0-.49.42l-.38 2.65c-.63.25-1.21.59-1.73 1l-2.49-1a.5.5 0 0 0-.61.22l-2 3.46a.5.5 0 0 0 .12.64L3.55 11c-.04.33-.06.66-.06 1s.02.67.06 1l-2.11 1.65a.5.5 0 0 0-.12.64l2 3.46c.14.24.42.33.61.22l2.49-1c.52.41 1.1.75 1.73 1l.38 2.65c.04.24.25.42.49.42h4c.24 0 .45-.18.49-.42l.38-2.65c.63-.25 1.21-.59 1.73-1l2.49 1c.24.09.5 0 .61-.22l2-3.46a.5.5 0 0 0-.12-.64L19.4 13zM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7z"/>'
        . '</svg>';
}

/* SNSアイコン */
function m4_icon_facebook() {
    return '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>';
}
function m4_icon_x() {
    return '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.22-6.82-5.96 6.82H1.66l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.01 4.13H5.05l12.03 15.64z"/></svg>';
}

/* ────────────────────────────────────────────
 * サンプルデータ投入（テーマ有効化時に1回だけ）
 * ──────────────────────────────────────────── */
add_action('after_switch_theme', 'm4_seed_content');

function m4_seed_content() {
    // パーマリンクを整える
    flush_rewrite_rules();

    if (get_option('m4_seeded')) {
        return;
    }

    // お知らせカテゴリ
    $news = term_exists('news', 'category');
    if (!$news) {
        $news = wp_insert_term('お知らせ', 'category', ['slug' => 'news']);
    }
    $news_id = (is_array($news) && isset($news['term_id'])) ? (int) $news['term_id'] : 0;

    // お知らせ投稿
    // 注: post_date は「現在日付より過去」にすること（未来日付は予約投稿(future)になり公開されない）
    $news_items = [
        ['Spec12 開催日が決定しました。', '次回大会「Spec12」の開催日が決定しました。詳細は決まり次第お知らせします。', '2026-05-25 10:00:00'],
        ['Webサイトを公開しました。', 'ミニ四駆学習会の公式サイトを公開しました。今後の情報はこちらで発信していきます。', '2026-05-10 10:00:00'],
    ];
    foreach ($news_items as [$title, $body, $date]) {
        wp_insert_post([
            'post_title'    => $title,
            'post_content'  => $body,
            'post_status'   => 'publish',
            'post_date'     => $date,
            'post_category' => $news_id ? [$news_id] : [],
        ]);
    }

    // アーカイブ（Spec11 → Spec1）
    $venue      = 'まなびア（宮崎県日南市）';
    $start_year = 2026;
    $start_no   = 11;
    for ($i = 0; $i < 11; $i++) {
        $no   = $start_no - $i;
        $year = $start_year - $i;
        $pid  = wp_insert_post([
            'post_type'    => 'spec',
            'post_title'   => 'Spec' . $no,
            'post_status'  => 'publish',
            'post_content' => 'ミニ四駆学習会 Spec' . $no . '（' . $year . '年開催）の記録ページです。レース結果やギャラリーを掲載します。',
            'menu_order'   => $no,
            'post_date'    => sprintf('%04d-03-15 10:00:00', $year),
        ]);
        if ($pid && !is_wp_error($pid)) {
            update_post_meta($pid, '_m4_year', (string) $year);
            update_post_meta($pid, '_m4_spec_no', (string) $no);
            update_post_meta($pid, '_m4_date', $year . '年3月15日（土）');
            update_post_meta($pid, '_m4_venue', $venue);
        }
    }

    update_option('m4_seeded', 1);
}
