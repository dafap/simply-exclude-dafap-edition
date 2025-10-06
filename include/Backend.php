<?php
/**
 * Admin backend for Simply Exclude: registers meta boxes, settings and admin UI.
 *
 * Responsible for registering columns, enqueuing admin assets and rendering
 * the admin-side UI used to manage per-post exclusions.
 *
 * @package Dafap\SimplyExclude
 * @author Alain
 * @license GPL-2.0-or-later
 * @since 1.0.0
 */
declare(strict_types=1);

namespace Dafap\SimplyExclude;

use Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox;
use Dafap\SimplyExclude\Admin\Settings\SimplyExcludeSettingsPage;
use Dafap\SimplyExclude\Admin\MetaBox\TaxonomyExclusionManager;
use Dafap\SimplyExclude\Admin\MetaBox\QuickEditExclusions;
use Dafap\SimplyExclude\ConfigTrait;

class Backend
{
    use ConfigTrait;
    // column label is provided by ConfigTrait::columnLabel()
    public function init(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        // meta box pour les posts, les taxonomies
        SimplyExcludeMetaBox::init();
        TaxonomyExclusionManager::init();
        QuickEditExclusions::init();
        

        // page de réglages
        SimplyExcludeSettingsPage::get_instance()->init();

        // Backend registers the visible column header and renders the status icons.
        // QuickEditExclusions only supplies per-row hidden data and the inline edit UI.
        add_filter('manage_post_posts_columns', [$this, 'addExclusionColumn']);
        add_action('manage_post_posts_custom_column', [$this, 'renderExclusionColumn'], 10, 2);
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_style('simply-exclude-admin', plugin_dir_url(__DIR__) . 'assets/css/simply-exclude-admin.css');
        wp_enqueue_script('simply-exclude-admin', plugin_dir_url(__DIR__) . 'assets/js/simply-exclude-admin.js', [], null, true);
    }

    public function sanitize_options(array $input): array
    {
        $valid_choices = ['default', 'yes', 'no'];
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = in_array($value, $valid_choices, true) ? $value : 'default';
        }
        return $sanitized;
    }

    public static function addExclusionColumn(array $columns): array
    {
        // Use the exported column key from the meta-box class to avoid magic strings.
        $label = self::columnLabel();
        // Legend: green check = Include, red check = Exclude
        $legend = ' <span class="simply-exclude-legend" title="' . esc_attr__('Legend: green = include, red = exclude', self::LANGUAGE_DOMAIN) . '">'
            . '<span aria-hidden="true"> ✅</span> <span class="screen-reader-text">' . esc_html__('Include', self::LANGUAGE_DOMAIN) . '</span>'
            . ' <span aria-hidden="true"> ❌</span> <span class="screen-reader-text">' . esc_html__('Exclude', self::LANGUAGE_DOMAIN) . '</span>'
            . '</span>';

        $columns[\Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox::COLUMN_KEY] = $label . $legend;
        return $columns;
    }

    public static function renderExclusionColumn(string $column, int $post_id): void
    {
        if ($column !== \Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox::COLUMN_KEY) {
            return;
        }

        $meta = get_post_meta($post_id, SimplyExcludeMetaBox::META_KEY, true);

        $labels = self::getContexts();

        // Read behavior settings per context. Values expected: 'include' or 'exclude'.
        $behaviors = get_option('simply_exclude_behavior', []);

        foreach ($labels as $key => $label) {
            $checked = !empty($meta[$key]);
            $mode = $behaviors[$key] ?? 'exclude';

            // Apply rules:
            // - checked && mode === 'include'  => green (✅)
            // - !checked && mode === 'exclude' => green (✅)
            // - otherwise => red (❌)
            $is_green = ($checked && $mode === 'include') || (!$checked && $mode === 'exclude');
            $status = $is_green ? '✅' : '❌';
            echo "<span title='" . esc_attr($label) . "'>$status</span> ";
        }
    }
}
