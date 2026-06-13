<?php
/**
 * 共通ヘッダー
 */
if (!defined('ABSPATH')) {
    exit;
}
$archive_url = get_post_type_archive_link('spec');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="brand__mark"><?php echo m4_logo_mark(); ?></span>
            <span class="brand__text">
                <span class="brand__title">ミニ四駆学習会</span>
                <span class="brand__sub">from Nichinan City</span>
            </span>
        </a>

        <button class="nav-toggle" aria-label="メニュー" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="nav" id="globalnav">
            <ul>
                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo (is_front_page() ? 'is-active' : ''); ?>">TOP</a></li>
                <li><a href="<?php echo esc_url(home_url('/#next')); ?>">次回情報</a></li>
                <li><a href="<?php echo esc_url($archive_url); ?>" class="<?php echo (is_post_type_archive('spec') || is_singular('spec') ? 'is-active' : ''); ?>">アーカイブ</a></li>
                <li><a href="#" class="is-disabled">（準備中）</a></li>
            </ul>
        </nav>

        <div class="social">
            <a class="s-fb" href="#" aria-label="Facebook" target="_blank" rel="noopener"><?php echo m4_icon_facebook(); ?></a>
            <a class="s-x" href="#" aria-label="X" target="_blank" rel="noopener"><?php echo m4_icon_x(); ?></a>
        </div>
    </div>
</header>

<main id="content">
