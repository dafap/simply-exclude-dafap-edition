<?php
/**
 * Simply Exclude - Frontend Class
 * @package SimplyExclude\DAFAP\Edition
 * @author Alain
 * @license GPL2
 * @link https://example.com/
 * @since 3.0.0
 */
declare(strict_types=1);
namespace Dafap\SimplyExclude;

use Dafap\SimplyExclude\Admin\Query\SimplyExcludeQueryFilter;
use Dafap\SimplyExclude\Admin\Query\TaxonomyQueryFilter;
use Dafap\SimplyExclude\Admin\Query\MediaQueryFilter;

class Frontend
{
    public function init(): void
    {
        add_filter('pre_get_posts', [$this, 'filter_query']);
        add_filter('get_terms', [$this, 'filter_terms'], 10, 3);
        add_filter('get_authors', [$this, 'filter_authors']);
        SimplyExcludeQueryFilter::init();
        TaxonomyQueryFilter::init();
        //MediaQueryFilter::init();
    }

    public function filter_query(\WP_Query $query): void {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Exemple : exclusion par meta
        $excluded_ids = $this->get_excluded_post_ids();
        if (!empty($excluded_ids)) {
            $query->set('post__not_in', $excluded_ids);
        }
    }

    private function get_excluded_post_ids(): array {
        // Récupère les IDs exclus via meta
        return [];
    }

    public function filter_terms($terms, $taxonomies, $args) {
        // Filtrage des catégories/tags
        return $terms;
    }

    public function filter_authors($authors) {
        // Filtrage des auteurs
        return $authors;
    }
}
