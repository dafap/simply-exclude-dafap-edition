<?php

declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\MetaBox;

use Dafap\SimplyExclude\ConfigTrait;

final class SimplyExcludeMetaBox
{
    use ConfigTrait;

    public const META_KEY = '_simply_exclude';
    // Public column key used in admin list tables. Exported to avoid magic strings elsewhere.
    public const COLUMN_KEY = 'simply_exclude';
    private const NONCE_ACTION = 'simply_exclude_nonce_action';
    private const NONCE_NAME = 'simply_exclude_nonce';

    public static function init(): void
    {
        add_action('add_meta_boxes', [self::class, 'addMetaBox']);
        add_action('save_post', [self::class, 'saveMetaBox']);
    }

    public static function addMetaBox(): void
    {
        add_meta_box(
            'simply_exclude_meta',
            __('Simply Exclude', self::LANGUAGE_DOMAIN),
            [self::class, 'renderMetaBox'],
            'post',
            'side',
            'default'
        );
    }

    public static function renderMetaBox(\WP_Post $post): void
    {
        $values = get_post_meta($post->ID, self::META_KEY, true) ?: [];

        $fields = self::getContexts();

        // Help button: data-help will be used by JS to open a modal
    $help_text = self::helpMessage();
    echo '<div style="text-align:right;margin-bottom:6px;">';
    echo '<button type="button" class="simply-exclude-help dashicons dashicons-editor-help" data-help="' . esc_attr($help_text) . '" aria-label="' . esc_attr(__('Help', self::LANGUAGE_DOMAIN)) . '"></button>';
    echo '</div>';

        // load behavior options to set title attribute
        $behaviors = get_option('simply_exclude_behavior', []);

        foreach ($fields as $key => $label) {
            $checked = !empty($values[$key]) ? 'checked' : '';
            $mode = $behaviors[$key] ?? 'exclude';
            $title = self::titleForMode($mode);
            echo "<label><input type='checkbox' name='simply_exclude[$key]' value='1' $checked title='" . esc_attr(
                $title
            ) . "'> $label</label><br>";
        }

        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
    }

    public static function saveMetaBox(int $postId): void
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $postId)) return;

        $data = array_map('intval', $_POST['simply_exclude'] ?? []);
        update_post_meta($postId, self::META_KEY, $data);
    }

    public static function getMeta(int $postId): array
    {
        return get_post_meta($postId, self::META_KEY, true) ?: [];
    }
}
