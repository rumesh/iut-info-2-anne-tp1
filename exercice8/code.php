<?php
$title = "Exercice 8";
require "debut_code_html.php";

// Question 1
function check_er($er, $tableau) {
    $erreurs = 0;
    echo '<ul>';
    foreach ($tableau as $chaine => $attendu) {
        $resultat = (bool) preg_match($er, $chaine);
        if ($resultat !== $attendu) {
            $erreurs++;
            if ($resultat) {
                echo '<li>ERREUR : "' . $chaine . '" vérifie l\'er alors que la valeur est false !</li>';
            } else {
                echo '<li>ERREUR : "' . $chaine . '" ne vérifie pas l\'er alors que la valeur est true !</li>';
            }
        }
    }
    echo '</ul>';
    echo '<p>Il y a ' . $erreurs . ' erreur' . ($erreurs > 1 ? 's' : '') . ' !</p>';
}

echo "<h2>Exemple de l'énoncé</h2>";
check_er("/php/", [
    "J'adore le php !" => true,
    "Génial le php !!!" => false,
    "Javascript est mieux" => false,
    "J'adore le javascript" => true
]);

// Question 2 : nombre entier (avec négatifs possibles)
echo "<h2>Question 2 : nombre entier</h2>";
$er_entier = '/^-?[0-9]+$/';
check_er($er_entier, [
    "10" => true,
    "0" => true,
    "-34539" => true,
    "--44" => false,
    "" => false,
    "123a456" => false,
    "10.2" => false
]);

// Question 3 : nombre décimal (séparateur ".", un chiffre obligatoire après le séparateur s'il est présent)
echo "<h2>Question 3 : nombre décimal</h2>";
$er_decimal = '/^-?([0-9]+(\.[0-9]+)?|\.[0-9]+)$/';
check_er($er_decimal, [
    "10" => true,
    "0" => true,
    "-34539" => true,
    "--44" => false,
    "" => false,
    "123a456" => false,
    "10.2" => true,
    "0.001" => true,
    ".001" => true,
    "10." => false
]);

// Question 4 : date JJ/MM/AAAA (jour/mois 1 ou 2 chiffres, année obligatoirement 4 chiffres)
echo "<h2>Question 4 : date JJ/MM/AAAA</h2>";
$er_date = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/';
check_er($er_date, [
    "10/10/2021" => true,
    "9/9/1234" => true,
    "90/9/5476" => true,
    "8/23/0014" => true,
    "111/23/0423" => false,
    "12/12/123" => false,
    "1/11234" => false,
    "10/2" => false,
    "1a/2b/8790" => false
]);

require "fin_code_html.php";
