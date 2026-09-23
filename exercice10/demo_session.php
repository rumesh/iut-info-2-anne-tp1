<?php
/**
 * Page de démonstration : voir le cookie PHPSESSID en direct.
 *
 * Le but n'est pas de calculer quoi que ce soit, mais de MONTRER le mécanisme :
 * le serveur range une valeur dans la session, et le navigateur ne transporte
 * qu'un ticket (le cookie PHPSESSID) pour la retrouver.
 *
 * À faire en même temps : F12 → Application (Chrome) ou Stockage (Firefox)
 * → Cookies.
 */

// Toujours en premier : avant le moindre affichage.
session_start();

// Lien "repartir de zéro" : on vide la session ET on jette le ticket, pour que
// le navigateur en reçoive un nouveau (le PHPSESSID affiché changera).
if (isset($_GET['reset'])) {

    session_destroy();                          // efface les données côté serveur
    setcookie('PHPSESSID', '', time() - 3600);  // demande au navigateur d'oublier le ticket

    // On revient sur la page "propre", sans le ?reset dans l'URL.
    header('Location: demo_session.php');
    exit;
}

// La valeur rangée dans la session : un simple compteur de visites.
// Il n'existe qu'au premier passage, puis PHP le retrouve à chaque rechargement.
if (!isset($_SESSION['compteur'])) {
    $_SESSION['compteur'] = 0;
}

$_SESSION['compteur'] = $_SESSION['compteur'] + 1;

$title = "Démo : la session et le cookie PHPSESSID";
require "debut_code_html.php";
?>

<h1>Démo : session et cookie PHPSESSID</h1>

<h2>1. La valeur rangée dans la session</h2>

<p>
    Vous avez chargé cette page
    <strong><?php echo $_SESSION['compteur']; ?></strong> fois.
</p>
<p>
    <em>Rechargez (F5) : le nombre augmente. Pourtant PHP a tout oublié entre
    les deux — c'est la session qui s'en souvient.</em>
</p>

<h2>2. Le numéro du casier, côté serveur</h2>

<p>
    <code>session_id()</code> =
    <strong><?php echo htmlspecialchars(session_id()); ?></strong>
</p>

<h2>3. Le ticket, côté navigateur</h2>

<?php if (isset($_COOKIE['PHPSESSID'])) { ?>

    <p>
        Le navigateur vient de me renvoyer le cookie<br />
        <code>PHPSESSID</code> =
        <strong><?php echo htmlspecialchars($_COOKIE['PHPSESSID']); ?></strong>
    </p>
    <p><em>C'est le même numéro qu'au point 2 : le ticket a servi à ouvrir le
    bon casier.</em></p>

<?php } else { ?>

    <p>
        <strong>Le tableau <code>$_COOKIE</code> est vide !</strong>
    </p>
    <p><em>C'est la toute première visite : le serveur vient seulement de créer
    le ticket et de l'envoyer au navigateur. Celui-ci ne le renverra qu'à la
    requête suivante. Rechargez la page pour le voir apparaître.</em></p>

<?php } ?>

<h2>4. Tout le contenu de la session</h2>

<pre><?php print_r($_SESSION); ?></pre>

<hr />

<p><a href="demo_session.php">Recharger la page</a></p>
<p><a href="demo_session.php?reset=1">Repartir de zéro (vider la session et jeter le ticket)</a></p>

<p>
    <em>À tester aussi : supprimer le cookie <code>PHPSESSID</code> à la main
    dans les outils du navigateur, puis recharger. Le compteur repart à 1 : sans
    le ticket, le casier est introuvable.</em>
</p>

<?php require "fin_code_html.php"; ?>
