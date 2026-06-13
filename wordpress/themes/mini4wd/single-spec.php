<?php
/**
 * アーカイブ詳細ページ（カスタム投稿タイプ spec）
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $id          = get_the_ID();
    $year        = m4_meta($id, '_m4_year');
    $date        = m4_meta($id, '_m4_date');
    $venue       = m4_meta($id, '_m4_venue');
    $result_url  = m4_meta($id, '_m4_result_url', '#');
    $regula_url  = m4_meta($id, '_m4_regulation_url', '#');
    $time_url    = m4_meta($id, '_m4_timetable_url', '#');
    $hue         = m4_hue($id);

    // 「Spec」＋数字 を分割して数字を赤く強調（"第1回" 等の非数値はそのまま表示）
    $label       = m4_spec_label($id);
    if (preg_match('/^(Spec)(\d+)$/', $label, $m)) {
        $title_html = esc_html($m[1]) . '<span class="spec-no">' . esc_html($m[2]) . '</span>';
    } else {
        $title_html = esc_html($label);
    }
    ?>

    <div class="single-wrap">
        <div class="container">
            <a class="backlink" href="<?php echo esc_url(get_post_type_archive_link('spec')); ?>">‹ アーカイブ一覧に戻る</a>

            <!-- ヘッダー -->
            <div class="detail-head">
                <div>
                    <div class="detail-head__year"><?php echo esc_html($year); ?></div>
                    <h1 class="detail-head__title"><?php echo $title_html; ?></h1>

                    <div class="detail-meta">
                        <?php if ($date) : ?>
                        <div class="detail">
                            <span class="detail__ic"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M7 2v2H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm12 7v10H5V9h14z"/></svg></span>
                            <span><span class="detail__label">開催日</span><br><span class="detail__value"><?php echo esc_html($date); ?></span></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($venue) : ?>
                        <div class="detail">
                            <span class="detail__ic"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg></span>
                            <span><span class="detail__label">会場</span><br><span class="detail__value"><?php echo esc_html($venue); ?></span></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (trim(wp_strip_all_tags(get_the_content()))) : ?>
                        <div class="detail-desc" style="margin-top:18px;color:var(--muted);font-size:14px"><?php the_content(); ?></div>
                    <?php endif; ?>
                </div>

                <div class="detail-head__poster"><?php m4_poster($id, 'detail'); ?></div>
            </div>

            <!-- 開催ギャラリー -->
            <section class="block">
                <div class="block__head">
                    <div>
                        <span class="en">Gallery</span>
                        <h2>開催ギャラリー</h2>
                    </div>
                </div>
                <div class="gallery-grid">
                    <?php for ($i = 0; $i < 8; $i++) : ?>
                        <div class="tile" style="--h:<?php echo (int) (($hue + $i * 24) % 360); ?>"></div>
                    <?php endfor; ?>
                </div>
                <p style="color:var(--muted-2);font-size:12px;margin-top:10px">※ 画像はイメージです（管理画面から差し替え可能）</p>
            </section>

            <!-- レース結果 / 記録・資料 -->
            <section class="block">
                <div class="doc-grid">
                    <div class="doc-card">
                        <h3>レース結果</h3>
                        <div class="btns">
                            <a class="btn btn--red" href="<?php echo esc_url($result_url); ?>">結果を見る <span class="arw">›</span></a>
                        </div>
                    </div>
                    <div class="doc-card">
                        <h3>記録・資料</h3>
                        <div class="btns">
                            <a class="btn btn--ghost" href="<?php echo esc_url($regula_url); ?>">レギュレーション <span class="arw">›</span></a>
                            <a class="btn btn--ghost" href="<?php echo esc_url($time_url); ?>">タイムスケジュール <span class="arw">›</span></a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php
endwhile;

get_footer();
