<?php
// session_start() et setcookie() doivent être appelés avant tout affichage,
// donc avant l'include de debut_code_html.php.
session_start();

if (!isset($_SESSION['produit'])) {
    $_SESSION['produit'] = 1;
}

$message = '';

// Question 3 : bouton de réinitialisation
if (isset($_POST['reinitialiser'])) {
    // Question 4 : on sauvegarde le produit dans un cookie avant de le réinitialiser
    if ($_SESSION['produit'] != 1) {
        setcookie('dernier_produit', $_SESSION['produit'], time() + 3600 * 24);
    }
    $_SESSION['produit'] = 1;
} elseif (isset($_POST['nombre'])) {
    // Question 1 : multiplication du produit stocké en session
    $nombre = $_POST['nombre'];
    if (preg_match('/^-?[0-9]+$/', $nombre)) {
        $_SESSION['produit'] *= (int) $nombre;
        $message = '"' . htmlspecialchars($nombre) . '" est un nombre entier, il a été multiplié au produit.';
    } else {
        // Question 2 : sans cette vérification, un $_POST['nombre'] non numérique
        // (ex: "abc") ferait échouer la multiplication (TypeError en PHP 8) ou
        // fausserait silencieusement le produit sur les anciennes versions de PHP.
        $message = '"' . htmlspecialchars($nombre) . '" n\'est pas un nombre entier, il est ignoré.';
    }
}

$title = "Exercice 10";
require "debut_code_html.php";

if ($message !== '') {
    echo '<p>' . $message . '</p>';
}

echo '<p>Produit actuel : ' . $_SESSION['produit'] . '</p>';

if (isset($_COOKIE['dernier_produit'])) {
    echo '<p>Dernier produit calculé avant la dernière réinitialisation : ' . htmlspecialchars($_COOKIE['dernier_produit']) . '</p>';
}
?>

<form action="nombre.php" method="post">
    <label for="nombre">Saisir un nombre :</label>
    <input type="text" id="nombre" name="nombre" />
    <input type="submit" name="valider" value="Multiplier" />
    <input type="submit" name="reinitialiser" value="Réinitialiser" />
</form>

<?php require "fin_code_html.php"; ?>
