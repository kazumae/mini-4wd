<?php
/**
 * Plugin Name: Disable Mail (production demo)
 * Description: デモ環境では送信メールを完全に無効化する。
 *   pre_wp_mail を short-circuit して wp_mail() を即時 no-op 化。
 *   （本番compose には MailHog を含めないため、SMTP接続で詰まらせない）
 *
 * mu-plugins に置くことで有効化操作なしで常に読み込まれる。
 */
if (!defined('ABSPATH')) {
    exit;
}

// すべての wp_mail() を送信せずに成功扱いで短絡
add_filter('pre_wp_mail', '__return_false');
