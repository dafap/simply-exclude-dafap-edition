# Simply Exclude Dafap Edition

Plugin WordPress pour gérer l'exclusion conditionnelle de contenus (articles, taxonomies, médias) dans les boucles, widgets, et requêtes personnalisées. Version personnalisée et étendue du plugin Simply Exclude, adaptée aux besoins du projet Dafap.

## 🧩 Fonctionnalités principales

- Exclusion par type de contenu : articles, pages, taxonomies, médias
- Intégration dans l’interface d’administration :
  - Metaboxes contextuelles
  - Quick Edit avec cases à cocher
- Compatibilité avec les requêtes `WP_Query` et les widgets natifs
- Diagnostic intégré pour le suivi des exclusions
- Architecture modulaire : séparation des helpers, templates, assets

---

## 🛠 Installation

### 1. Préalable : Configuration de Plugin Update Checker (v6)

Pour activer les mises à jour automatiques via GitHub, la bibliothèque globale doit être installée en tant que `mu-plugin` :

1. Placez la bibliothèque _Plugin Update Checker_ dans un dossier `plugin-update-checker` à l'intérieur de `wp-content/mu-plugins/`.
2. Créez ou complétez un fichier de chargement global (par exemple `wp-content/mu-plugins/dafap-loader.php`) avec le code suivant :

```php
<?php
/**
 * Plugin Name: Dafap Shared Libraries Loader
 * Description: Charge les bibliothèques communes (Plugin Update Checker v6) pour les plugins Dafap.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Charger PUC v6 globalement si ce n'est pas déjà fait
$puc_file = WPMU_PLUGIN_DIR . '/plugin-update-checker/plugin-update-checker.php';
if ( file_exists( $puc_file ) && ! class_exists( 'YahnisElsts\PluginUpdateChecker\v6\PucFactory' ) ) {
    require_once $puc_file;
}
```

### 2. Installation du plugin

1. Copiez ou clonez le dossier du plugin dans `wp-content/plugins/simply-exclude-dafap-edition`.
2. Installez les dépendances via Composer si nécessaire :
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Activez le plugin via l’interface d’administration WordPress (**Extensions**).

---

## ⚙️ Configuration

- Les exclusions se configurent via :
  - Les metaboxes dans l’édition d’un contenu.
  - Les cases à cocher dans le _Quick Edit_ (Modification rapide).
- Les exclusions sont prises en compte dans :
  - Les boucles principales.
  - Les widgets de catégories, tags, archives.
  - Les requêtes personnalisées via `WP_Query`.

---

## 🧪 Diagnostic

Un système de diagnostic est activable pour :

- Afficher les exclusions appliquées
- Tracer les requêtes `WP_Query`
- Identifier les conflits éventuels

---

## 📦 Exigences techniques

- PHP ≥ 8.1 (Recommandé : PHP 8.4)
- WordPress ≥ 6.0
- Composer (pour les helpers externes)

---

## 🤝 Contributing (Repository Mirror)

This repository serves as a mirror for the local plugin `simply-exclude-dafap-edition` for Dafap.

- Make changes on feature branches and open PRs targeting `main`.
- Keep commits small and atomic.

---

## 📄 License

GPL-2.0-or-later
