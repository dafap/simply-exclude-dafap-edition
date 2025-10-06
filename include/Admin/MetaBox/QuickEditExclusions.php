<?php

declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\MetaBox;

use Dafap\SimplyExclude\ConfigTrait;
use Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox;

class QuickEditExclusions
{
    use ConfigTrait;

    // Use the exported column key from the main meta-box class to avoid magic strings.
    const string COLUMN_NAME = SimplyExcludeMetaBox::COLUMN_KEY;

    // QuickEditExclusions does not add the column header. Backend is responsible for
    // registering the visible column (header) and rendering the status icons. This
    // class provides the Quick Edit UI (checkboxes) and injects hidden per-row data
    // used by the Quick Edit JS to prefill the inline editor.
    public static function init()
    {
        add_action('quick_edit_custom_box', [self::class, 'render'], 10, 2);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueScripts']);
        add_action('manage_post_posts_custom_column', [self::class, 'renderColumn'], 10, 2);
    }

    public static function render($column_name, $post_type)
    {
        if ($column_name !== self::COLUMN_NAME || $post_type !== 'post') return;

        $options = self::getContexts();

        $help_text = self::helpMessage();
        echo '<fieldset class="inline-edit-col-right" style="border: 1px solid #ddd"><div class="inline-edit-col">';
    echo '<span class="'. self::COLUMN_NAME. '">' . self::columnLabel() . '</span>';
        // show help icon to the right of the title
        echo ' <button type="button" class="simply-exclude-help dashicons dashicons-editor-help" data-help="' . esc_attr($help_text) . '" aria-label="' . esc_attr(__('Help', self::LANGUAGE_DOMAIN)) . '"></button>';
        echo '<ul>';
        $behaviors = get_option('simply_exclude_behavior', []);
        foreach ($options as $key => $label) {
            $mode = $behaviors[$key] ?? 'exclude';
            $title = self::titleForMode($mode);
            echo '<li class="alignleft" style="margin-right:1em;">';
            // use associative name to match SimplyExcludeMetaBox
            echo '<input type="checkbox" name="simply_exclude[' . esc_attr($key) . ']" value="1" title="' . esc_attr($title) . '"> ';
            echo esc_html($label);
            echo '</li>';
        }
        // Add nonce so saveMetaBox can verify when Quick Edit submits
        wp_nonce_field('simply_exclude_nonce_action', 'simply_exclude_nonce');
        echo '</ul></div></fieldset>';
    }

    public static function enqueueScripts()
    {
        // charge le script depuis le dossier assets/js du plugin
        $plugin_dir = plugin_dir_url(dirname(__DIR__, 2));
        wp_enqueue_script('simply-exclude-quickedit', $plugin_dir . 'assets/js/simply-exclude-quickedit.js', ['jquery'], null, true);
    }

    // addColumn handled by Backend to avoid duplicate column headers

    public static function renderColumn($column, $post_id)
    {
        if ($column !== self::COLUMN_NAME) return;

        $meta = get_post_meta($post_id, SimplyExcludeMetaBox::META_KEY, true) ?: [];
        $flags = [];
        if (is_array($meta)) {
            foreach ($meta as $k => $v) {
                if ($v) $flags[] = (string)$k;
            }
        }
        echo '<div id="simply_exclude_data_' . $post_id . '" data-flags="' . esc_attr(json_encode($flags)) . '"></div>';
    }
}
