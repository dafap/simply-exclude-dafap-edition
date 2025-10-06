<?php

/**
 * Plugin Name: Simply Exclude – Dafap Edition
 * Plugin URI: https://example.com/simply-exclude-dafap-edition
 * Description: Version personnalisée du plugin Simply Exclude pour les besoins Dafap.
 * Version: 1.0.0
 * Author: Alain
 * License: GPL2
 */

declare(strict_types=1);

namespace Dafap\SimplyExclude;

use Dafap\SimplyExclude\Backend;
use Dafap\SimplyExclude\Frontend;
use Dafap\SimplyExclude\ConfigTrait;
use PSpell\Config;

final class SimplyExclude
{
    use ConfigTrait;

    private static ?self $instance = null;
    private Backend $backend;
    private Frontend $frontend;

    /**
     * Impossible de créer une instance directement (singleton pattern).
     */
    private function __construct()
    {
        if (is_admin()) {
            $this->backend = new Backend();
        } 
        $this->frontend = new Frontend();
    }    

    /**
     * Retourne l'instance unique de la classe SimplyExclude.
     */
    public static function get_instance(): SimplyExclude
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run(): void
    {
        $this->load_textdomain();
        if (isset($this->backend)) {
            $this->backend->init();
        }
        $this->frontend->init();
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain(self::LANGUAGE_DOMAIN, false, dirname(plugin_basename(__DIR__)) . '/languages');
    }
}
