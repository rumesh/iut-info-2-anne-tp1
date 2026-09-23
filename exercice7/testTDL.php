<?php
require_once "TODOList.php";

$title = "Exercice 7 - Test TODOList";
require "debut_code_html.php";

$tdl = new TODOList();
$tdl->add_to_do("Faire les courses");
$tdl->add_to_do("Réviser le PHP");
$tdl->add_to_do("Appeler le dentiste");
$tdl->add_to_do("Ranger la chambre");

echo "<h2>Liste initiale (4 tâches)</h2>";
echo $tdl->get_html();

$tdl->remove_to_do(1); // supprime "Réviser le PHP"

echo "<h2>Après suppression de la tâche d'indice 1</h2>";
echo $tdl->get_html();

echo "<h2>Tests</h2><ul>";

echo '<li>is_empty() sur une liste non vide : ' . var_export($tdl->is_empty(), true) . ' (attendu false)</li>';

$tdl->add_to_do("");
$tdl->add_to_do("   ");
echo '<li>Ajout d\'une chaîne vide/espaces : le nombre de tâches ne doit pas changer -> ' . $tdl->get_html() . '</li>';

$listeVide = new TODOList();
echo '<li>is_empty() sur une liste vide : ' . var_export($listeVide->is_empty(), true) . ' (attendu true)</li>';
echo '<li>get_html() sur une liste vide : ' . $listeVide->get_html() . '</li>';

echo "</ul>";

require "fin_code_html.php";
