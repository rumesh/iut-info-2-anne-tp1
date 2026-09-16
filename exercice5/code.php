<?php
$title = "Exercice 5";
require "debut_code_html.php";

$personnes = [
  'mdupond' => ['Prénom' => 'Martin', 'Nom' => 'Dupond', 'Age' => 25, 'Ville' => 'Paris'],
  'jm'      => ['Prénom' => 'Jean'  , 'Nom' => 'Martin', 'Age' => 20, 'Ville' => 'Villetaneuse'],
  'toto'    => ['Prénom' => 'Tom'   , 'Nom' => 'Tonge' , 'Age' => 18, 'Ville' => 'Epinay'],
  'arn'     => ['Prénom' => 'Arnaud', 'Nom' => 'Dupond', 'Age' => 33, 'Ville' => 'Paris'],
  'email'   => ['Prénom' => 'Emilie', 'Nom' => 'Ailta' , 'Age' => 46, 'Ville' => 'Villetaneuse'],
  'dask'    => ['Prénom' => 'Damien', 'Nom' => 'Askier', 'Age' => 7 , 'Ville' => 'Villetaneuse']
];

/*
Question 1 : les clés du tableau $personnes ('mdupond', 'jm', 'toto', ...) sont
des chaînes de caractères. Les valeurs sont elles-mêmes des tableaux associatifs
(clés string 'Prénom', 'Nom', 'Age', 'Ville'). $personnes['toto'] vaut
['Prénom' => 'Tom', 'Nom' => 'Tonge', 'Age' => 18, 'Ville' => 'Epinay'].

Question 2 :
- valeur 33          -> $personnes['arn']['Age']
- valeur 'Epinay'     -> $personnes['toto']['Ville']
- tableau de 'dask'   -> $personnes['dask']
*/

// Question 3 : fonction sans en-têtes
function tableau_vers_html($tableau) {
    $html = "<table>\n";

    foreach ($tableau as $ligne) {

        $html .= "<tr>\n";

        foreach ($ligne as $valeur) {
            $html .= "<td>$valeur</td>\n";
        }

        $html .= "</tr>\n";
    }

    $html .= "</table>\n";

    return $html;
}

// Question 4 : fonction avec une ligne d'en-têtes
function tableau_vers_html_avec_entetes($tableau) {
    $entetes = array_keys($tableau[array_keys($tableau)[0]]);

    $html = "<table>\n";
    $html .= "<tr>\n";

    foreach ($entetes as $entete) {
        $html .= "<th>$entete</th>\n";
    }

    $html .= "</tr>\n";

    foreach ($tableau as $ligne) {
        $html .= "<tr>\n";

        foreach ($ligne as $valeur) {
            $html .= "<td>$valeur</td>\n";
        }

        $html .= "</tr>\n";
    }

    $html .= "</table>\n";

    return $html;
}

echo "<h2>Sans en-têtes</h2>";
echo tableau_vers_html($personnes);

echo "<h2>Avec en-têtes</h2>";
echo tableau_vers_html_avec_entetes($personnes);

// Question 5 : test avec une structure différente
$scores = [
  ['Joueur' => 'Camille'  , 'score' => 156],
  ['Joueur' => 'Guillaume', 'score' => 254],
  ['Joueur' => 'Julien'   , 'score' => 192],
  ['Joueur' => 'Nabila'   , 'score' => 317],
  ['Joueur' => 'Lorianne' , 'score' => 235],
  ['Joueur' => 'Tom'      , 'score' => 83 ],
  ['Joueur' => 'Michael'  , 'score' => 325],
  ['Joueur' => 'Eddy'     , 'score' => 299]
];

echo "<h2>Test avec \$scores</h2>";
echo tableau_vers_html_avec_entetes($scores);

require "fin_code_html.php";
