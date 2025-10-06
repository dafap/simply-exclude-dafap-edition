<?php

declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\Query;

final class TaxonomyQueryFilter
{
    private const META_KEY = '_simply_exclude_term';

    public static function init(): void
    {
        add_action('pre_get_posts', [TaxonomyQueryFilter::class, 'applyToQuery']);
        add_filter('get_terms', [TaxonomyQueryFilter::class, 'filterTerms'], 10, 2);
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

    public static function applyToQuery(\WP_Query $query): void
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        $context = self::detectContext($query);

        foreach (get_taxonomies(['public' => true]) as $taxonomy) {
            $excluded = self::getExcludedTermIds($taxonomy, $context);

            if (empty($excluded)) {
                continue;
            }

            if ($taxonomy === 'category') {
                $query->set('category__not_in', $excluded);
            } elseif ($taxonomy === 'post_tag') {
                $query->set('tag__not_in', $excluded);
            } else {
                $tax_query = $query->get('tax_query') ?: [];
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $excluded,
                    'operator' => 'NOT IN',
                ];
                $query->set('tax_query', $tax_query);
            }
        }
    }

    public static function filterTerms(array $terms, array $taxonomies): array
    {
        if (is_admin()) {
            return $terms; // ne rien filtrer dans l'admin
        }
        $filtered = [];

        foreach ($terms as $term) {
            if (!isset($term->taxonomy)) {
                $filtered[] = $term;
                continue;
            }

            $meta = get_term_meta($term->term_id, self::META_KEY, true);
            if (empty($meta['widget'])) {
                $filtered[] = $term;
            }
        }

        return $filtered;
    }

    private static function detectContext(\WP_Query $query): string
    {
        if ($query->is_search()) return 'search';
        if ($query->is_feed()) return 'feed';
        if ($query->is_home()) return 'front';
        if ($query->is_archive()) return 'archive';

        return 'front'; // fallback
    }
}
