<?php
/**
 * Settings page for Simply Exclude plugin.
 *
 * Registers and renders the options page used to control default behaviors
 * for each exclusion context.
 *
 * @package Dafap\SimplyExclude\Admin\Settings
 * @since 1.0.0
 */
declare(strict_types=1);

namespace Dafap\SimplyExclude\Admin\Settings;

use Dafap\SimplyExclude\Admin\MetaBox\SimplyExcludeMetaBox;
use Dafap\SimplyExclude\ConfigTrait;

final class SimplyExcludeSettingsPage
{
    use ConfigTrait;
    private const PAGE_TITLE = 'Simply Exclude Settings';
    private const PAGE_SLUG = 'simply-exclude-settings';
    public const OPTION_GROUP = 'simply_exclude_settings';

    public static function get_instance(): SimplyExcludeSettingsPage
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    public function init(): void
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addSettingsPage(): void
    {
        /**
         * Ajoute la page de réglages Simply Exclude
         */
        add_options_page(
            __(self::PAGE_TITLE, self::LANGUAGE_DOMAIN),
            'Simply Exclude',
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderSettingsPage']
        );
        /**
         * Ajoute un lien "Réglages" sur la page des plugins
         */
        add_filter(
            'plugin_action_links_' . plugin_basename(SE_EDITION_PATH . 'simply-exclude-dafap-edition.php'),
            [$this, 'add_settings_link']
        );
    }

    public function add_settings_link(array $links): array
    {
        $path = admin_url('options-general.php?page=' . self::PAGE_SLUG);
        $settings_link = '<a href="' . $path . '">' . __('Settings', self::LANGUAGE_DOMAIN) . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function registerSettings(): void
    {
        register_setting(self::OPTION_GROUP, 'simply_exclude_behavior');

        add_settings_section(
            'default', // ← identifiant de la section
            'Choisir les comportements par défaut',        // ← titre (laisser vide si tu ne veux pas de titre)
            null,      // ← callback d’introduction (facultatif)
            self::PAGE_SLUG // ← identifiant de la page
        );
        foreach (SimplyExcludeMetaBox::getContexts() as $key => $label) {
            add_settings_field(
                $key,
                $label,
                function () use ($key) {
                    $options = get_option('simply_exclude_behavior', []);
                    $value = $options[$key] ?? 'exclude';
                                        $label_exclude = self::titleForMode('exclude');
                                        $label_include = self::titleForMode('include');
                                        echo "<select name='simply_exclude_behavior[$key]'>
                                                        <option value='exclude' " . selected($value, 'exclude', false) . ">" . esc_html($label_exclude) . "</option>
                                                        <option value='include' " . selected($value, 'include', false) . ">" . esc_html($label_include) . "</option>
                                                    </select>";
                },
                self::PAGE_SLUG,
                'default'
            );
        }
    }

    public function renderSettingsPage(): void
    {
        echo '<div class="wrap"><h1>' . esc_html__(self::PAGE_TITLE, self::LANGUAGE_DOMAIN) . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields(self::OPTION_GROUP);
        do_settings_sections(self::PAGE_SLUG);
        submit_button();
        echo '</form></div>';
    }
}
