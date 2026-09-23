# TP PHP — Corrigés des exercices

## 👉 Voir les corrigés en ligne

**<https://static.rumesh.net/iut-info-2-anne-tp1/index.php>**

Rien à installer : les exercices s'exécutent et leur code source s'affiche
directement dans le navigateur.

---

Ce dépôt contient les corrigés du TP, un dossier par exercice, ainsi que la page
d'accueil (`index.php`) qui permet de tous les parcourir facilement.

## Lancer le projet en local

Pour travailler sur sa propre machine plutôt qu'en ligne.

Une page PHP doit être exécutée par un serveur : il ne suffit pas de
double-cliquer sur un fichier `.php`, sinon le navigateur affiche le code au lieu
de l'exécuter. PHP contient un petit serveur intégré, c'est tout ce dont on a
besoin ici.

1. Ouvrir un terminal dans le dossier du projet.
2. Lancer le serveur :

   ```bash
   php -S localhost:8000
   ```

3. Ouvrir cette adresse dans le navigateur :

   **<http://localhost:8000>**

Pour arrêter le serveur : `Ctrl + C` dans le terminal.

> Si le port 8000 est déjà utilisé, on peut en choisir un autre, par exemple
> `php -S localhost:8080`, puis ouvrir <http://localhost:8080>.

## Ce que l'on voit sur la page

La page d'accueil est découpée en trois colonnes :

| Colonne | Contenu |
| --- | --- |
| **Exercices** (gauche) | La liste des exercices disponibles, avec les pages de chacun |
| **Résultat** (centre) | La page choisie, réellement exécutée par le serveur |
| **Code source** (droite) | Le code des fichiers de l'exercice, avec la coloration syntaxique |

Dans la colonne de droite, un onglet par fichier : on y retrouve aussi les
fichiers inclus (`debut_code_html.php`, `fin_code_html.php`) et les classes
(`TODOList.php`), même s'ils ne s'ouvrent pas directement dans le navigateur.

Le lien « ouvrir dans un onglet ↗ » affiche l'exercice seul, en pleine page.

## Organisation du dépôt

```
index.php        la page d'accueil
exercice1/       un dossier par exercice
exercice2/
...
```

La liste n'est écrite nulle part : `index.php` lit le contenu du dossier à chaque
affichage et construit les liens tout seul. Les exercices sont publiés au fur et
à mesure de l'avancement du TP : la version en ligne se complète donc toute
seule, il suffit de recharger la page.

En local, pour récupérer les exercices qui viennent d'être ajoutés :

```bash
git pull
```

Il n'y a rien à modifier dans `index.php` : les nouveaux dossiers apparaissent
automatiquement dans la colonne de gauche.

## Prérequis

PHP 8 ou plus récent. Pour vérifier qu'il est installé :

```bash
php -v
```
