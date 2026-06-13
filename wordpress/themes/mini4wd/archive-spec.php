<?php
/**
 * アーカイブ一覧ページ（カスタム投稿タイプ spec）
 */
if (!defined('ABSPATH')) {
    exit;
}

// 総開催回数
$total = wp_count_posts('spec');
$count = isset($total->publish) ? (int) $total->publish : 0;

get_header();
?>

<section class="page-hero">
    <div class="container page-hero__inner">
        <span class="page-hero__en">Archive</span>
        <h1 class="page-hero__ja">アーカイブ</h1>
        <p class="page-hero__lead">これまでに<b><?php echo esc_html($count); ?></b>回開催されました</p>
    </div>
</section>

<section class="archive-list">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="spec-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="spec-card">
                        <div class="spec-card__poster"><?php m4_poster(get_the_ID(), 'card'); ?></div>
                        <div class="spec-card__body">
                            <div class="spec-card__year"><?php echo esc_html(m4_meta(get_the_ID(), '_m4_year')); ?></div>
                            <div class="spec-card__name"><?php echo esc_html(m4_spec_label(get_the_ID())); ?></div>
                            <a class="spec-card__view" href="<?php the_permalink(); ?>">VIEW <span>›</span></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="center-cta">
                <?php
                the_posts_pagination([
                    'mid_size'  => 1,
                    'prev_text' => '‹ 前へ',
                    'next_text' => '次へ ›',
                ]);
                ?>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--muted);padding:40px 0">まだ開催記録がありません。</p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
