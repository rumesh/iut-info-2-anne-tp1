<?php
$title = "Exercice 9";
require "debut_code_html.php";

// Question 1 & 2 : le script ne doit générer aucune erreur, avec ou sans paramètre
if (isset($_GET['nombre'])) {
    $nombre = $_GET['nombre'];
    if (preg_match('/^-?[0-9]+$/', $nombre)) {
        echo '<p>"' . htmlspecialchars($nombre) . '" est un nombre entier.</p>';
    } else {
        echo '<p>"' . htmlspecialchars($nombre) . '" n\'est pas un nombre entier.</p>';
    }
} else {
    echo '<p>Aucun paramètre "nombre" n\'a été transmis.</p>';
}
?>

<!-- Question 3 : formulaire de saisie -->
<form action="nombre.php" method="get">
    <label for="nombre">Saisir un nombre :</label>
    <input type="text" id="nombre" name="nombre" />
    <input type="submit" value="Valider" />
</form>

<?php require "fin_code_html.php"; ?>
