<?php
$title = "Exercice 6";
require "debut_code_html.php";

$tabMagazines = [
  'le monde'              => ['frequence' => 'quotidien', 'type' => 'actualité', 'prix' => 220],
  'le point'              => ['frequence' => 'hebdo'    , 'type' => 'actualité', 'prix' => 80 ],
  'causette'              => ['frequence' => 'mensuel'  , 'type' => 'féminin'  , 'prix' => 180],
  'politis'               => ['frequence' => 'hebdo'    , 'type' => 'opinion'  , 'prix' => 100],
  'le monde diplomatique' => ['frequence' => 'mensuel'  , 'type' => 'analyse'  , 'prix' => 60 ],
  'libération'            => ['frequence' => 'quotidien', 'type' => 'actualité', 'prix' => 190],
];

$tabMagazinesAbonne = ['le monde', 'le monde diplomatique'];

// Question 1 : noms triés par ordre alphabétique, sans boucle
$noms = array_keys($tabMagazines);
sort($noms);
echo '<p>' . implode(', ', $noms) . '</p>';

// Question 2 : uniquement les quotidiens, sans boucle explicite (array_filter)
$quotidiens = array_keys(array_filter($tabMagazines, function ($magazine) {
    return $magazine['frequence'] === 'quotidien';
}));
echo '<p>' . implode(', ', $quotidiens) . '</p>';

// Question 3 : "nom (frequence, type, prix)" pour chaque magazine
echo '<ul>';
foreach ($tabMagazines as $nom => $infos) {
    echo '<li>' . $nom . ' (' . implode(', ', $infos) . ')</li>';
}
echo '</ul>';

// Question 4 : prix total de l'abonnement
$total = 0;
foreach ($tabMagazinesAbonne as $nomMagazine) {
    $total += $tabMagazines[$nomMagazine]['prix'];
}
echo '<p>Prix total de l\'abonnement : ' . $total . '</p>';

require "fin_code_html.php";
