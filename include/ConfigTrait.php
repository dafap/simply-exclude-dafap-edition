<?php
/**
 * Configuration helpers and shared constants for the Simply Exclude plugin.
 *
 * Fournit des utilitaires partagés (textdomain, contextes, libellés) utilisés
 * par les classes admin et frontend du plugin.
 *
 * @package Dafap\SimplyExclude
 * @author Alain
 * @license GPL-2.0-or-later
 * @since 1.0.0
 */
declare(strict_types=1);

namespace Dafap\SimplyExclude;

trait ConfigTrait
{
    public const LANGUAGE_DOMAIN = 'simply-exclude';

    /**
     * Retourne le tableau des contextes traduit dans la langue utilisée
     */
    public static function getContexts(): array
    {
        return [
            'front_page' => __("Home page", self::LANGUAGE_DOMAIN),
            'search'     => __('Search', self::LANGUAGE_DOMAIN),
            'category'   => __('Category', self::LANGUAGE_DOMAIN),
            'archive'    => __('Archive', self::LANGUAGE_DOMAIN),
            'feed'       => __('RSS Feed', self::LANGUAGE_DOMAIN),
        ];
    }

    /**
     * Retourne le libellé de l'attribut title pour un mode donné.
     * Mode attendu: 'include' ou 'exclude'.
     */
    public static function titleForMode(string $mode): string
    {
        return $mode === 'include'
            ? __('Include if checked', self::LANGUAGE_DOMAIN)
            : __('Exclude if checked', self::LANGUAGE_DOMAIN);
    }

    /**
     * Message d'aide centralisé (traductible)
     */
    public static function helpMessage(): string
    {
        return __('Hover over a box to learn about the behavior of exclusions.', self::LANGUAGE_DOMAIN);
    }

    /**
     * Label used for the admin column header where exclusions are shown.
     */
    public static function columnLabel(): string
    {
        return __('Exclusions', self::LANGUAGE_DOMAIN);
    }
}
