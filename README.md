# Nelson Galley — Encarts

Plugin WordPress pour diffuser ses **propres publicités** sur un blog : offres de service, formations, applications, événements, partenaires et espaces sponsorisés vendus à des entreprises.

Les encarts se rédigent depuis l'admin, s'insèrent automatiquement aux bons endroits et mesurent leurs affichages et leurs clics. Interface en français.

![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0%2B-21759b) ![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777bb4) ![Licence GPL-2.0+](https://img.shields.io/badge/licence-GPL--2.0%2B-10b981)

## Fonctionnalités

- **6 types d'encarts**, chacun avec son badge : Mon offre, Formation, Nouveau (application / produit), Événement, Partenaire, Sponsorisé.
- **Emplacements automatiques** : au milieu des articles (après le paragraphe de votre choix), à la fin des articles, sur l'accueil avant la grille d'articles.
- **Code court** `[encart id="123"]` pour placer un encart précis dans n'importe quel article.
- **Ciblage par rubrique** : un encart peut ne s'afficher que dans certaines catégories.
- **Période de diffusion** : dates de début et de fin, idéal pour un événement ou un espace vendu au mois.
- **Rotation** : plusieurs encarts sur un même emplacement s'affichent à tour de rôle.
- **Statistiques** : affichages, clics et taux de clic par encart, sans cookie ni service externe. Les administrateurs connectés et les robots ne sont pas comptés.
- **Conformité** : les encarts Partenaire et Sponsorisé portent la mention visible « Proposé par… » et un lien `rel="sponsored nofollow"`, comme l'exige Google pour les liens commerciaux.
- **Mises à jour en un clic** depuis le tableau de bord WordPress, à partir des releases GitHub.

## Installation

1. Téléchargez `nelson-galley-encarts.zip` depuis la [dernière release](https://github.com/Nels-G/nelson-galley-encarts/releases/latest).
2. Dans WordPress : **Extensions › Ajouter › Téléverser une extension**, choisissez le fichier, puis **Activer**.
3. Un menu **Encarts** apparaît dans l'admin.

## Utilisation

1. **Encarts › Ajouter un encart**.
2. Renseignez le titre, le type, le texte, le bouton et son lien. Ajoutez une image dans « Image de l'encart » (format paysage, 1200 × 675 px conseillé).
3. Cochez les emplacements, éventuellement les rubriques ciblées et la période de diffusion.
4. **Publiez** : l'encart apparaît immédiatement. Pour le retirer, repassez-le en brouillon.

Les statistiques sont visibles dans la liste des encarts et dans chaque fiche.

## Personnalisation

Les couleurs reprennent celles du thème si les variables `--ng-*` existent, sinon un vert émeraude par défaut. Pour adapter le style, redéfinissez dans le CSS de votre thème :

```css
.ng-encart {
  --nge-accent: #10b981;        /* bouton et pastille */
  --nge-accent-hover: #059669;
  --nge-badge-bg: rgba(16, 185, 129, 0.10);
  --nge-badge-text: #065f46;
}
```

Ajouter un type d'encart :

```php
add_filter( 'nge_types', function ( $types ) {
	$types['podcast'] = array( 'label' => 'Podcast', 'badge' => 'Podcast' );
	return $types;
} );
```

L'emplacement « Accueil » utilise le hook `csco_main_content_before` des thèmes Code Supply (Revision). Sur un autre thème, utilisez le code court.

## Publier une mise à jour

Les sites qui utilisent le plugin voient la mise à jour dans **Tableau de bord › Mises à jour**, comme pour une extension classique.

1. Modifiez le code.
2. Augmentez la version **à deux endroits** de `nelson-galley-encarts.php` : l'en-tête `Version:` et la constante `NGE_VERSION`.
3. Ajoutez une entrée dans [CHANGELOG.md](CHANGELOG.md).
4. Commitez, puis créez et poussez l'étiquette de version :

```bash
git commit -am "Version 1.1.0"
git tag v1.1.0
git push origin main --tags
```

L'action GitHub [release.yml](.github/workflows/release.yml) vérifie que l'étiquette correspond à la version du plugin, construit `nelson-galley-encarts.zip` et publie la release. WordPress la détecte dans les 6 heures (ou immédiatement via **Mises à jour › Vérifier à nouveau**).

## Structure

```text
nelson-galley-encarts.php   En-tête du plugin, constantes, chargement
includes/
  helpers.php               Types, emplacements, fonctions utilitaires
  post-type.php             Type de contenu « Encart »
  admin.php                 Formulaire d'édition et colonnes de la liste
  render.php                Sélection d'un encart et rendu HTML
  placements.php            Insertion dans les articles, accueil, code court
  tracking.php              Comptage des clics et redirection
  updater.php               Mises à jour depuis les releases GitHub
assets/css/encarts.css      Styles front-end
```

## Données

Les encarts sont des contenus de type `ng_encart`. Leurs réglages et statistiques sont stockés en métadonnées (`_ng_*`). Désactiver ou supprimer le plugin ne supprime aucune donnée.

## Licence

[GPL-2.0-or-later](LICENSE). Développé par [Nelson Galley](https://www.nelsongalley.online/), développeur web et designer UX/UI à Lomé, Togo.
