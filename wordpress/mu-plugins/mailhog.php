<?php
/**
 * Plugin Name: MailHog SMTP (local)
 * Description: WordPress の送信メールを MailHog (mailhog:1025) にルーティングします。ローカル開発専用。
 *
 * mu-plugins（must-use plugins）に置くことで、有効化操作なしで常に読み込まれます。
 */

if (!defined('ABSPATH')) {
    exit;
}

// すべての wp_mail() を MailHog 経由の SMTP 送信に切り替える
add_action('phpmailer_init', function ($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'mailhog'; // docker-compose のサービス名
    $phpmailer->Port       = 1025;      // MailHog の SMTP ポート（コンテナ内部）
    $phpmailer->SMTPAuth   = false;
    $phpmailer->SMTPSecure = '';
    $phpmailer->SMTPAutoTLS = false;
});

// 送信元アドレス／表示名（任意・見やすさのため固定）
add_filter('wp_mail_from', function () {
    return 'wordpress@mini-4wd.local';
});
add_filter('wp_mail_from_name', function () {
    return 'Mini 4WD (local)';
});
