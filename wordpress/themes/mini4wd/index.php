<?php
/**
 * 汎用テンプレート（お知らせ一覧・カテゴリ・検索などのフォールバック）
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$heading = 'お知らせ';
if (is_category()) {
    $heading = single_cat_title('', false);
} elseif (is_search()) {
    $heading = '検索結果: ' . get_search_query();
} elseif (is_archive()) {
    $heading = get_the_archive_title();
}
?>

<section class="page-hero">
    <div class="container page-hero__inner">
        <span class="page-hero__en">News</span>
        <h1 class="page-hero__ja"><?php echo esc_html($heading); ?></h1>
    </div>
</section>

<section class="list-wrap">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="post-row">
                    <div class="post-row__meta"><?php echo esc_html(get_the_date('Y.m.d')); ?></div>
                    <h2 class="post-row__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="post-row__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 60)); ?></p>
                </article>
            <?php endwhile; ?>

            <div class="center-cta">
                <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '‹ 前へ', 'next_text' => '次へ ›']); ?>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--muted);padding:40px 0">記事がありません。</p>
        <?php endif; ?>

        <div class="center-cta">
            <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/')); ?>">‹ TOPへ戻る</a>
        </div>
    </div>
</section>

<?php
get_footer();
