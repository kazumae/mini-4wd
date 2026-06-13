<?php
/**
 * 共通フッター
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
</main><!-- #content -->

<footer class="site-footer" id="contact">
    <div class="container site-footer__inner">
        <div class="foot-about">
            <div class="brand">
                <span class="brand__mark"><?php echo m4_logo_mark(); ?></span>
                <span class="brand__text">
                    <span class="brand__title">ミニ四駆学習会</span>
                    <span class="brand__sub">from Nichinan City</span>
                </span>
            </div>
            <p class="foot-lead">勝ちより和に。正しさを。正しさの先に、ほんとうの楽しさを。<br>宮崎県日南市で開催するミニ四駆の学習会です。</p>
        </div>

        <div class="foot-contact">
            <h4>最新情報・お問い合わせ</h4>
            <a class="sns sns--fb" href="#" target="_blank" rel="noopener">
                <span class="sns__ic"><?php echo m4_icon_facebook(); ?></span>
                <span><span class="sns__label">Facebook</span><br><span class="sns__name">ミニ四駆学習会</span></span>
            </a>
            <a class="sns sns--x" href="#" target="_blank" rel="noopener">
                <span class="sns__ic"><?php echo m4_icon_x(); ?></span>
                <span><span class="sns__label">X（旧Twitter）</span><br><span class="sns__name">@mini4study</span></span>
            </a>
        </div>
    </div>
    <div class="foot-bottom">
        &copy; <?php echo esc_html(date('Y')); ?> ミニ四駆学習会 from Nichinan City
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
