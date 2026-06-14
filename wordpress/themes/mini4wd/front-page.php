<?php
/**
 * TOPページ
 *
 * ── 編集ポイント ──
 * ヒーロー文言・NEXT ROUND の内容はこのファイル上部の $hero / $next で変更できます。
 * お知らせ・アーカイブは管理画面の投稿／アーカイブと連動します。
 */
if (!defined('ABSPATH')) {
    exit;
}

// ── ヒーロー ──
$hero = [
    'title'  => 'ミニ四駆学習会',
    'sub'    => 'from Nichinan City',
    'tag'    => 'Manners Maketh Mini 4WD',
    'slogan' => '勝ちより和に。正しさを。正しさの先に、ほんとうの楽しさを。',
];

// ── NEXT ROUND（次回情報）──
$next = [
    'spec'    => '12',
    'when'    => '2027年 4月 開催予定',
    'details' => [
        ['会場',                'まなびア（宮崎県日南市）'],
        ['開催クラス',          'レディース / ジュニア / オープン'],
        ['参加費',              'クラスにより異なる'],
        ['参加案内・レギュレーション', '詳細は決まり次第お知らせします'],
    ],
];

get_header();
?>

<!-- ヒーロー（背景写真＋HTML大タイポ） -->
<section class="hero">
    <div class="hero__media"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/mv.png')); ?>" alt="" aria-hidden="true"></div>
    <div class="hero__streak" aria-hidden="true"></div>
    <div class="container hero__in">
        <span class="kicker">From Nichinan City &middot; Est. Study Club</span>
        <h1 class="hero__title">
            <span class="l1">Mini</span>
            <span class="l2">4<b>WD</b> Lab</span>
        </h1>
        <div class="hero__row">
            <p class="hero__ja"><?php echo esc_html($hero['title']); ?><small><?php echo esc_html($hero['tag']); ?></small></p>
            <p class="hero__slogan"><?php echo esc_html($hero['slogan']); ?></p>
        </div>
    </div>
    <span class="scroll" aria-hidden="true">Scroll &darr;</span>
</section>

<!-- NEXT ROUND -->
<section class="next" id="next">
    <div class="container next__grid">
        <div class="next__main">
            <div class="next__headline">
                <span class="badge">NEXT ROUND</span>
                <div class="spec-big"><span>Spec</span><strong><?php echo esc_html($next['spec']); ?></strong></div>
                <div class="spec-when"><?php echo esc_html($next['when']); ?></div>
            </div>
            <ul class="next__details">
                <?php foreach ($next['details'] as [$label, $value]) : ?>
                    <li class="detail">
                        <span class="detail__ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
                        </span>
                        <span>
                            <span class="detail__label"><?php echo esc_html($label); ?></span><br>
                            <span class="detail__value"><?php echo esc_html($value); ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <aside class="next__side">
            <h3>最新情報はSNSで！</h3>
            <a class="sns sns--fb" href="#" target="_blank" rel="noopener">
                <span class="sns__ic"><?php echo m4_icon_facebook(); ?></span>
                <span><span class="sns__label">Facebook</span><br><span class="sns__name">ミニ四駆学習会</span></span>
            </a>
            <a class="sns sns--x" href="#" target="_blank" rel="noopener">
                <span class="sns__ic"><?php echo m4_icon_x(); ?></span>
                <span><span class="sns__label">X（旧Twitter）</span><br><span class="sns__name">@mini4study</span></span>
            </a>
            <a class="btn btn--red btn--block" href="#contact">
                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg>
                お問い合わせ
            </a>
        </aside>
    </div>
</section>

<!-- お知らせ -->
<section class="news">
    <div class="container">
        <div class="news__box">
            <?php
            $news_q = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'no_found_rows'  => true,
            ]);
            if ($news_q->have_posts()) :
                while ($news_q->have_posts()) : $news_q->the_post();
                    ?>
                    <a class="news__row" href="<?php the_permalink(); ?>">
                        <span class="news__flag">
                            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M4 2h2v20H4zM7 3h11l-2 4 2 4H7z"/></svg>
                        </span>
                        <span class="news__date"><?php echo esc_html(get_the_date('Y.m.d')); ?></span>
                        <span class="news__title"><?php the_title(); ?></span>
                        <span class="news__more">詳細 <span class="arw">›</span></span>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p style="padding:18px 4px;color:var(--muted)">お知らせはまだありません。</p>';
            endif;
            ?>
        </div>
        <div class="news__all">
            <?php $news_cat = get_category_by_slug('news'); ?>
            <a class="btn btn--ghost" href="<?php echo esc_url($news_cat ? get_category_link($news_cat->term_id) : home_url('/')); ?>">お知らせ一覧 <span class="arw">›</span></a>
        </div>
    </div>
</section>

<!-- アーカイブ（ティザー） -->
<section class="archive-teaser">
    <div class="container">
        <div class="sec-head">
            <div class="sec-head__inner">
                <span class="sec-head__en">Archive</span>
                <h2 class="sec-head__ja">アーカイブ</h2>
                <p class="sec-head__lead">これまでの開催記録を掲載しています。</p>
            </div>
        </div>
        <?php
        $teaser = new WP_Query([
            'post_type'      => 'spec',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
        if ($teaser->have_posts()) :
            ?>
            <div class="spec-grid">
                <?php while ($teaser->have_posts()) : $teaser->the_post(); ?>
                    <article class="spec-card">
                        <div class="spec-card__poster"><?php m4_poster(get_the_ID(), 'card'); ?></div>
                        <div class="spec-card__body">
                            <div class="spec-card__year"><?php echo esc_html(m4_meta(get_the_ID(), '_m4_year')); ?></div>
                            <div class="spec-card__name"><?php echo esc_html(m4_spec_label(get_the_ID())); ?></div>
                            <a class="spec-card__view" href="<?php the_permalink(); ?>">VIEW <span>›</span></a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="center-cta">
                <a class="btn btn--red" href="<?php echo esc_url(get_post_type_archive_link('spec')); ?>">アーカイブを見る <span class="arw">›</span></a>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--muted)">これまでの開催記録を掲載しています。もうしばらくお待ちください。</p>
        <?php endif; ?>
    </div>
</section>

<!-- マニフェスト -->
<section class="manifesto-sec">
    <div class="container">
        <blockquote class="manifesto">
            <q>速さは、<b>礼節</b>の先にある。<br>整えること、待つこと、讃えること。</q>
            <div class="sign">— Mini 4WD Study, Nichinan</div>
        </blockquote>
    </div>
</section>

<?php
get_footer();
