<!--
NOTE : cette explication évite volontairement d'écrire les balises d'ouverture/
fermeture PHP littéralement en dehors du code exécutable ci-dessous : PHP les
détecte même à l'intérieur d'un commentaire HTML et tenterait de les exécuter,
ce qui casserait ce fichier.

Question 1 : parties PHP / HTML du code original
Code HTML : tout ce qui n'est pas dans une zone de script PHP, à savoir le
doctype, les balises html/head/title/meta/body, le h1 et les paragraphes de
texte statique.
Code PHP (zones de script, cinq au total) :
  1. celle qui affiche 'Premiers pas en PHP' dans le title
  2. celle qui définit $tps et affiche le paragraphe "Je débute depuis ..."
  3. celle qui affiche le '!'
  4. celle qui affiche le bloc "Vive le PHP / Les pages vont pouvoir ..."
  5. celle, à corriger (voir question 2), qui affiche "Avant dernier paragraphe"

Question 2 : la ligne contenant l'instruction echo pour "Avant dernier
paragraphe" n'est pas entourée par une zone de script PHP. Le fichier étant un
.php, cette ligne est donc interprétée comme du texte/HTML brut par le
serveur : le navigateur affichera littéralement le texte
  echo '<p> Avant dernier paragraphe </p>';
au lieu d'exécuter l'instruction. Le fichier est donc syntaxiquement valide
en PHP (ce n'est pas une erreur PHP), mais le résultat affiché n'est pas celui
attendu. Correction : entourer cette ligne d'une zone de script PHP (voir le
code plus bas).

Question 3 : les trois echo qui affichaient "Je débute depuis ... heures..."
sont remplacés ci-dessous par une seule instruction echo utilisant la
concaténation (opérateur .).
-->
<!doctype html>
<html>
<head>
<title> <?php echo 'Premiers pas en PHP'; ?> </title>
<meta charset="utf-8"/>
</head>
<body>
<h1> Mes premiers pas en PHP </h1>
<?php $tps = 2; echo '<p> Je débute depuis ' . $tps . ' heures... </p>'; ?>
<p> Mais cela a l'air intéressant <?php echo '!' ?> </p>
<?php echo '
<h2> Vive le PHP </h2>
<p> Les pages vont pouvoir être dynamiques! </p>
'; ?>
<p> Encore quelques paragraphes </p>
<?php echo '<p> Avant dernier paragraphe </p>'; ?>
<p> Voilà, c'est terminé! </p>
</body>
</html>
