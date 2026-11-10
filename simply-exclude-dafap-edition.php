<?php
/**
 * Plugin Name: Simply Exclude Dafap Edition
 * Plugin URI: https://github.com/dafap/simply-exclude-dafap-edition
 * Description: Version modernisée et compatible PHP 8.4 du plugin Simply Exclude.
 * Version: 1.0.3
 * Author: Dafap
 * Author URI: https://www.dafap.fr
 * Text Domain: simplyexclude-dafap-edition
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Si ce fichier est accédé directement, bloquer.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Définition des constantes de base
if ( ! defined( 'SE_EDITION_VERSION' ) ) {
    define( 'SE_EDITION_VERSION', '1.0.3' );
}
if ( ! defined( 'SE_EDITION_PATH' ) ) {
    define( 'SE_EDITION_PATH', plugin_dir_path( __FILE__ ) );
}

// 1. Chargement de l'Autoloader de Composer
// Composer gère le chargement de toutes vos classes PSR-4 (Dafap\SimplyExclude\)
require_once SE_EDITION_PATH . 'vendor/autoload.php';

// 1. bis : Initialisation de Plugin Update Checker (v6 via la bibliothèque globale mu-plugin)
if ( function_exists( 'puc_load_update_checker' ) ) {
    $updateChecker = puc_load_update_checker(
        'https://github.com/dafap/simply-exclude-dafap-edition',
        __FILE__,
        'simply-exclude-dafap-edition'
    );
    
    // Définition explicite de la branche pour les mises à jour - supprimé pour ne tenir compte que des releases et tags GitHub
    // $updateChecker->setBranch( 'main' );
}

// 2. Initialisation du plugin
// La classe principale SimplyExclude est le point d'entrée
use Dafap\SimplyExclude\SimplyExclude;

if ( ! function_exists( 'simply_exclude_run' ) ) {
    /**
     * Instancie et exécute le plugin une seule fois.
     */
    function simply_exclude_run() {
        // La classe SimplyExclude gérera l'instanciation de Backend et Frontend
        SimplyExclude::get_instance()->run();
    }
}

// Hook principal de WordPress
add_action( 'plugins_loaded', 'simply_exclude_run' );