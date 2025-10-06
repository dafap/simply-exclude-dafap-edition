# Simply Exclude – Dafap Edition

Plugin WordPress pour gérer l'exclusion conditionnelle de contenus (articles, taxonomies, médias) dans les boucles, widgets, et requêtes personnalisées. Version personnalisée et étendue du plugin Simply Exclude, adaptée aux besoins du projet Dafap.

## 🧩 Fonctionnalités principales

- Exclusion par type de contenu : articles, pages, taxonomies, médias
- Intégration dans l’interface d’administration :
  - Metaboxes contextuelles
  - Quick Edit avec cases à cocher
- Compatibilité avec les requêtes WP_Query et les widgets natifs
- Diagnostic intégré pour le suivi des exclusions
- Architecture modulaire : séparation des helpers, templates, assets

## 🛠 Installation

1. Copier le dossier du plugin dans `wp-content/plugins/simply-exclude-dafap-edition`
2. Activer le plugin via l’interface d’administration WordPress
3. Vérifier les options d’exclusion dans les écrans d’édition de contenu

## ⚙️ Configuration

- Les exclusions se configurent via :
  - Les metaboxes dans l’édition d’un contenu
  - Les cases à cocher dans Quick Edit
- Les exclusions sont prises en compte dans :
  - Les boucles principales
  - Les widgets de catégories, tags, archives
  - Les requêtes personnalisées via `WP_Query`

## 🧪 Diagnostic

Un système de diagnostic est activable pour :
- Afficher les exclusions appliquées
- Tracer les requêtes WP_Query
- Identifier les conflits éventuels

## 📦 Dépendances

- PHP ≥ 8.1
- WordPress ≥ 6.0
- Composer (pour les helpers externes)

```bash
composer install
```

# simply-exclude-dafap-edition

Repo mirror of the local plugin `simply-exclude-dafap-edition` for Dafap.

This repository contains a WordPress admin plugin that provides a per-post and per-term "simply exclude" mechanism with Quick Edit integration.

Contributing
- Make changes on feature branches and open PRs targeting `main`.
- Keep commits small and atomic.

License
- GPL-2.0-or-later

