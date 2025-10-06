<?php

declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\Query;

final class SimplyExcludeQueryFilter
{
    private const META_KEY = '_simply_exclude';

    public static function init(): void
    {
        add_action('pre_get_posts', [self::class, 'filterQuery']);
    }

    public static function filterQuery(\WP_Query $query): void
    {
        if (is_admin() /*|| !$query->is_main_query()*/) {
            return;
        }

        $exclude_ids = [];

        // Front page
        if ($query->is_home()) {
            $exclude_ids = array_merge($exclude_ids, self::getExcludedIds('front_page'));
        }

        // Search
        if ($query->is_search()) {
            $exclude_ids = array_merge($exclude_ids, self::getExcludedIds('search'));
        }

        // Category
        if ($query->is_category()) {
            $exclude_ids = array_merge($exclude_ids, self::getExcludedIds('category'));
        }

        // Archive
        if ($query->is_archive()) {
            $exclude_ids = array_merge($exclude_ids, self::getExcludedIds('archive'));
        }

        // Feed
        if ($query->is_feed()) {
            $exclude_ids = array_merge($exclude_ids, self::getExcludedIds('feed'));
        }

        if (!empty($exclude_ids)) {
            $query->set('post__not_in', array_unique(array_merge(
                (array) $query->get('post__not_in'),
                $exclude_ids
            )));
        }
    }

    private static function getExcludedIds(string $context): array
    {
        global $wpdb;

        $options = get_option('simply_exclude_behavior', []);
        $mode = $options[$context] ?? 'exclude';

        global $wpdb;
        $like = '%' . $wpdb->esc_like('"' . $context . '";i:1') . '%';

        if ($mode === 'exclude') {
            $sql = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s", self::META_KEY, $like);
        } else {
            $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID NOT IN (
                SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s
                )", self::META_KEY, $like);
        }

        return $wpdb->get_col($sql) ?: [];
    }
}
