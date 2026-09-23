# Sessions et cookies en PHP

> Objectif : comprendre pourquoi le serveur « oublie » tout entre deux pages,
> et comment la **session** et le **cookie** règlent ce problème.

---

## Le problème : le web n'a pas de mémoire

Chaque fois que vous cliquez, le navigateur envoie une **requête**, le serveur
répond une page… **et oublie tout**. Le script PHP s'arrête, ses variables sont
détruites.

```
   NAVIGATEUR                             SERVEUR PHP

   n°1 : « multiplie par 6 »  ────────▶   $produit = 1 * 6 = 6
                              ◀────────   « Produit : 6 »
                                          💥 fin du script : tout est effacé

   n°2 : « multiplie par 2 »  ────────▶   $produit = ??? = 1 * 2 = 2
                              ◀────────   « Produit : 2 »  ❌ on voulait 12
```

👉 Une variable PHP ne survit **jamais** d'une page à l'autre.
Il faut donc ranger la valeur **quelque part**.

---

## L'image à retenir : le vestiaire

Vous arrivez à une soirée, vous déposez votre manteau au vestiaire.
On vous donne **un ticket**. Vos affaires restent **dans le casier**.

```
        NAVIGATEUR (poste de l'élève)      │      SERVEUR (la machine PHP)
                                           │
              🎟  ticket                   │           🗄  casier n° a1b2c3
         PHPSESSID = a1b2c3                │           ┌──────────────────┐
                                           │           │ produit  =  6    │
   Le ticket repart avec CHAQUE requête ───┼──────────▶│ panier   =  ...  │
                                           │           └──────────────────┘
```

| Le vestiaire | Le web |
| --- | --- |
| Le **ticket** dans votre poche | le **cookie** stocké par le navigateur |
| Le **casier** derrière le comptoir | la **session** stockée sur le serveur |

**Un cookie = une petite valeur chez le client. Une session = des données chez
le serveur, retrouvées grâce à un cookie.**

---

## La session : `session_start()` et `$_SESSION`

```php
session_start();                    // ouvre (ou retrouve) le casier

$_SESSION['produit'] = 6;           // on range une valeur dedans
echo $_SESSION['produit'];          // on la relit à la page suivante
```

- `$_SESSION` est un **tableau associatif** comme les autres… sauf qu'il est
  **rechargé automatiquement** à chaque page.
- Les données sont **sur le serveur** : l'utilisateur ne peut ni les voir ni les
  modifier. ✅ On y met ce qui est sensible.
- Durée de vie : tant que le navigateur reste ouvert (~20 min d'inactivité).

⚠️ **Piège n°1** — `session_start()` doit être appelé **avant le moindre
affichage** (avant tout `echo`, tout HTML, même un espace avant `<?php`). C'est
pour cela que dans `nombre.php` il est tout en haut, **avant**
`require "debut_code_html.php"`.

---

## Le cookie : `setcookie()` et `$_COOKIE`

```php
// On demande au navigateur de garder cette valeur pendant 24 h
setcookie('dernier_produit', 42, time() + 3600 * 24);

// Aux visites suivantes, il nous la renvoie :
echo $_COOKIE['dernier_produit'];   // 42
```

- Le cookie est stocké **dans le navigateur**, et renvoyé au serveur à chaque
  requête, automatiquement.
- Il **survit à la fermeture du navigateur** (jusqu'à sa date d'expiration).
- L'utilisateur peut le **lire et le modifier** avec les outils du navigateur.
  ❌ Jamais de mot de passe ni de prix dans un cookie !

⚠️ **Piège n°2** — `setcookie()` envoie une *consigne* au navigateur. La valeur
n'arrive dans `$_COOKIE` qu'à la **requête suivante**.

```
   Page 1 : setcookie('x', 42)  ─────▶ « navigateur, retiens x=42 »
            echo $_COOKIE['x']  ❌ n'existe pas encore !

   Page 2 : le navigateur renvoie x=42
            echo $_COOKIE['x']  ✅ 42
```

---

## Les deux ensemble dans l'exercice 10

Le produit courant vit **en session**. Au moment du « Réinitialiser », on garde
une trace du dernier résultat **dans un cookie**, pour le revoir même demain.

```
 ① 1ʳᵉ visite         ──────────────▶  session_start() crée le casier
                      ◀── Set-Cookie: PHPSESSID=a1b2c3
                                        $_SESSION['produit'] = 1

 ② « Multiplier 6 »   ── PHPSESSID ──▶  $_SESSION['produit'] *= 6   →  6
 ③ « Multiplier 2 »   ── PHPSESSID ──▶  $_SESSION['produit'] *= 2   →  12
                                        (le serveur a retrouvé le bon casier 🎉)

 ④ « Réinitialiser »  ── PHPSESSID ──▶  setcookie('dernier_produit', 12, 24 h)
                      ◀── Set-Cookie: dernier_produit=12
                                        $_SESSION['produit'] = 1

 ⑤ Demain             ── dernier_produit=12 ──▶  « Dernier produit : 12 »
    (session expirée, mais le cookie, lui, a survécu)
```

L'ossature du code de `nombre.php` (allégée ici des vérifications de saisie) :

```php
session_start();                                   // ① le casier

if (!isset($_SESSION['produit'])) {
    $_SESSION['produit'] = 1;                      // ① valeur de départ
}

if (isset($_POST['reinitialiser'])) {
    setcookie('dernier_produit',                   // ④ la trace longue durée
              $_SESSION['produit'],
              time() + 3600 * 24);
    $_SESSION['produit'] = 1;
} elseif (isset($_POST['nombre'])) {
    $_SESSION['produit'] *= (int) $_POST['nombre']; // ②③ la mémoire courte
}

if (isset($_COOKIE['dernier_produit'])) {          // ⑤ relecture
    echo 'Dernier produit : ' . $_COOKIE['dernier_produit'];
}
```

---

## Récapitulatif

|  | 🗄 **Session** | 🍪 **Cookie** |
| --- | --- | --- |
| Stocké où ? | sur le **serveur** | dans le **navigateur** |
| Visible par l'utilisateur ? | non | **oui** (et modifiable) |
| Taille | libre | ~4 Ko |
| Durée de vie | la visite (~20 min) | la date choisie (heures, mois…) |
| On y met | panier, utilisateur connecté, données sensibles | préférences, « se souvenir de moi » |
| En PHP | `session_start()` puis `$_SESSION[...]` | `setcookie(...)` puis `$_COOKIE[...]` |

### À retenir en trois phrases

1. HTTP oublie tout : sans session ni cookie, chaque page repart de zéro.
2. La session garde les données **côté serveur** ; le navigateur ne transporte
   qu'un ticket, le cookie `PHPSESSID`.
3. Un cookie classique garde une petite valeur **côté navigateur**, plus
   longtemps, mais l'utilisateur peut la lire et la changer.

---

## 🔍 Démo à faire en direct

Ouvrir la page **`demo_session.php`**, puis les outils du navigateur :
`F12` → onglet **Application** (Chrome) ou **Stockage** (Firefox) → **Cookies**.

Cette page ne fait qu'une chose : ranger un compteur dans la session. Elle
affiche côte à côte le numéro du casier (`session_id()`) et le ticket renvoyé
par le navigateur (`$_COOKIE['PHPSESSID']`) — les deux sont identiques.

1. **Première visite** : le compteur vaut 1 et `$_COOKIE` est encore **vide**.
   Le serveur vient juste d'envoyer le ticket, le navigateur ne l'a pas encore
   renvoyé. (C'est le piège n°2, en vrai.)
2. **Recharger (F5)** : le compteur monte, et `PHPSESSID` apparaît. Le serveur
   n'a rien mémorisé entre les deux — c'est le ticket qui a fait le lien.
3. **Supprimer le cookie `PHPSESSID`** dans les outils du navigateur, puis
   recharger : le compteur repart à 1. Ticket perdu, casier introuvable.

Sur l'exercice lui-même (`nombre.php`), on voit ensuite apparaître le cookie
`dernier_produit` après un clic sur « Réinitialiser » : celui-là, contrairement
au ticket de session, survivra à la fermeture du navigateur.
