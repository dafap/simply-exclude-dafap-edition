<?php

declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\MetaBox;

use Dafap\SimplyExclude\ConfigTrait;

final class TaxonomyExclusionManager
{
    use ConfigTrait;
    
    private const META_KEY = '_simply_exclude_term';

    public static function getContexts()
    {
        return   [
            'widget'  => __('Widget', self::LANGUAGE_DOMAIN),
            'front'   => __('Home page', self::LANGUAGE_DOMAIN),
            'search'  => __('Search', self::LANGUAGE_DOMAIN),
            'archive' => __('Archive', self::LANGUAGE_DOMAIN),
            'feed'    => __('RSS Feed', self::LANGUAGE_DOMAIN),
        ];
    }

    public static function init(): void
    {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        foreach ($taxonomies as $taxonomy) {
            add_action("{$taxonomy->name}_add_form_fields", [self::class, 'renderAddMetaBox']);
            add_action("{$taxonomy->name}_edit_form_fields", [self::class, 'renderEditMetaBox']);
            add_action("create_{$taxonomy->name}", [self::class, 'saveMeta']);
            add_action("edited_{$taxonomy->name}", [self::class, 'saveMeta']);
            add_filter("manage_edit-{$taxonomy->name}_columns", [self::class, 'addExclusionColumn']);
            add_filter("manage_{$taxonomy->name}_custom_column", [self::class, 'renderExclusionColumn'], 10, 3);
        }
    }

    public static function addExclusionColumn(array $columns): array
    {
        $columns[\Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox::COLUMN_KEY] = self::columnLabel();
        return $columns;
    }

    public static function renderExclusionColumn($unused, string $column, int $term_id): string
    {
        if ($column !== \Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox::COLUMN_KEY) {
            return '';
        }

        $meta = get_term_meta($term_id, self::META_KEY, true) ?: [];

        $output = '';
        foreach (self::getContexts() as $key => $label) {
            $status = !empty($meta[$key]) ? '✅' : '❌';
            $output .= "<span title='" . esc_attr($label) . "'>$status</span> ";
        }
        echo $output;
        return '';
    }

    public static function renderAddMetaBox(): void
    {
        echo '<div class="form-field">';
        foreach (self::getContexts() as $key => $label) {
            echo "<label><input type='checkbox' name='simply_exclude_term[$key]' value='1'> $label</label><br>";
        }
        echo '</div>';
    }

    public static function renderEditMetaBox($term): void
    {
        $meta = get_term_meta($term->term_id, self::META_KEY, true) ?: [];

        foreach (self::getContexts() as $key => $label) {
            $checked = !empty($meta[$key]) ? 'checked' : '';
            echo "<tr class='form-field'>
                <th scope='row'>$label</th>
                <td><input type='checkbox' name='simply_exclude_term[$key]' value='1' $checked></td>
              </tr>";
        }
    }


    public static function saveMeta(int $term_id): void
    {
        if (!isset($_POST['simply_exclude_term'])) {
            delete_term_meta($term_id, self::META_KEY);
            return;
        }

        $meta = array_map('intval', $_POST['simply_exclude_term']);
        update_term_meta($term_id, self::META_KEY, $meta);
    }

    public static function getExcludedTermIds(string $taxonomy, string $context): array
    {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        $excluded = [];

        foreach ($terms as $term) {
            $meta = get_term_meta($term->term_id, self::META_KEY, true);
            if (!empty($meta[$context])) {
                $excluded[] = $term->term_id;
            }
        }

        return $excluded;
    }
}
