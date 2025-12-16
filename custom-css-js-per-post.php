<?php

/**
 * Plugin Name: カスタムCSS/JS per Post
 * Plugin URI: 
 * Description: 投稿記事ごとにカスタムのCSS、JavaScriptを追加できるプラグイン
 * Version: 1.0.0
 * Author: Nagaoka Design Office
 * Author URI: https://nag-design.com
 * License: GPL v2 or later
 * Text Domain: custom-css-js-per-post
 */

// 直接アクセスを防ぐ
if (! defined('ABSPATH')) {
    exit;
}

/**
 * カスタムフィールドの追加
 */
function ccjpp_add_meta_boxes()
{
    $screens = array('post', 'page');

    foreach ($screens as $screen) {
        // カスタムCSS用メタボックス
        add_meta_box(
            'ccjpp_custom_css',
            'カスタムCSS',
            'ccjpp_render_css_meta_box',
            $screen,
            'normal',
            'default'
        );

        // カスタムJS用メタボックス
        add_meta_box(
            'ccjpp_custom_js',
            'カスタムJavaScript',
            'ccjpp_render_js_meta_box',
            $screen,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'ccjpp_add_meta_boxes');

/**
 * CSSメタボックスの表示
 */
function ccjpp_render_css_meta_box($post)
{
    $keyname = 'ccjpp_custom_css';
    $value = get_post_meta($post->ID, $keyname, true);

    wp_nonce_field('ccjpp_save_css', 'ccjpp_css_nonce');
?>
    <p>
        <label for="<?php echo esc_attr($keyname); ?>">
            CSSを記述してください。<code>&lt;style&gt;</code>タグは不要。
        </label>
    </p>
    <textarea
        id="<?php echo esc_attr($keyname); ?>"
        name="<?php echo esc_attr($keyname); ?>"
        rows="10"
        style="width: 100%; font-family: monospace;"
        placeholder="例:&#10;.my-custom-class {&#10;    color: #333;&#10;    font-size: 16px;&#10;}"><?php echo esc_textarea($value); ?></textarea>
<?php
}

/**
 * JSメタボックスの表示
 */
function ccjpp_render_js_meta_box($post)
{
    $keyname = 'ccjpp_custom_js';
    $value = get_post_meta($post->ID, $keyname, true);

    wp_nonce_field('ccjpp_save_js', 'ccjpp_js_nonce');
?>
    <p>
        <label for="<?php echo esc_attr($keyname); ?>">
            JavaScriptを記述してください。<code>&lt;script&gt;</code>タグは不要。
        </label>
    </p>
    <textarea
        id="<?php echo esc_attr($keyname); ?>"
        name="<?php echo esc_attr($keyname); ?>"
        rows="10"
        style="width: 100%; font-family: monospace;"
        placeholder="例:&#10;jQuery(document).ready(function($) {&#10;    console.log('カスタムJS実行');&#10;});"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

/**
 * カスタムフィールドの保存
 */
function ccjpp_save_meta_boxes($post_id)
{
    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    // CSSの保存
    if (isset($_POST['ccjpp_css_nonce']) && wp_verify_nonce($_POST['ccjpp_css_nonce'], 'ccjpp_save_css')) {
        if (isset($_POST['ccjpp_custom_css'])) {
            $css = wp_strip_all_tags($_POST['ccjpp_custom_css']);
            update_post_meta($post_id, 'ccjpp_custom_css', $css);
        } else {
            delete_post_meta($post_id, 'ccjpp_custom_css');
        }
    }

    // JSの保存
    if (isset($_POST['ccjpp_js_nonce']) && wp_verify_nonce($_POST['ccjpp_js_nonce'], 'ccjpp_save_js')) {
        if (isset($_POST['ccjpp_custom_js'])) {
            $js = wp_strip_all_tags($_POST['ccjpp_custom_js']);
            update_post_meta($post_id, 'ccjpp_custom_js', $js);
        } else {
            delete_post_meta($post_id, 'ccjpp_custom_js');
        }
    }
}
add_action('save_post', 'ccjpp_save_meta_boxes');

/**
 * フロントエンドにカスタムCSSを出力
 */
function ccjpp_output_custom_css()
{
    if (is_singular()) {
        global $post;
        $custom_css = get_post_meta($post->ID, 'ccjpp_custom_css', true);

        if (! empty($custom_css)) {
            echo '<style type="text/css" id="ccjpp-custom-css">' . "\n";
            echo esc_html($custom_css) . "\n";
            echo '</style>' . "\n";
        }
    }
}
add_action('wp_head', 'ccjpp_output_custom_css', 999);

/**
 * フロントエンドにカスタムJSを出力
 */
function ccjpp_output_custom_js()
{
    if (is_singular()) {
        global $post;
        $custom_js = get_post_meta($post->ID, 'ccjpp_custom_js', true);

        if (! empty($custom_js)) {
            echo '<script type="text/javascript" id="ccjpp-custom-js">' . "\n";
            echo '/* カスタムJS - Post ID: ' . esc_js($post->ID) . ' */' . "\n";
            echo $custom_js . "\n";
            echo '</script>' . "\n";
        }
    }
}
add_action('wp_footer', 'ccjpp_output_custom_js', 999);

/**
 * 管理画面用のスタイル追加
 */
function ccjpp_admin_styles()
{
    global $post_type;

    if (in_array($post_type, array('post', 'page'))) {
    ?>
        <style>
            #ccjpp_custom_css textarea,
            #ccjpp_custom_js textarea {
                font-family: 'Courier New', Courier, monospace;
                font-size: 13px;
                line-height: 1.5;
            }
        </style>
<?php
    }
}
add_action('admin_head', 'ccjpp_admin_styles');
