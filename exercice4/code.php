<?php
$title = "Exercice 4";
require "debut_code_html.php";

$t = [
    'english',
    'first'=>'html',
    2 => 'css',
    'best'=>'php',
    'javascript',
    5 => 'jQuery'
];

/*
Question 1 : clés et valeurs prédites
- 'english'    -> clé 0        (première clé numérique auto : commence à 0)
- 'first'      -> clé 'first'
- 'css'        -> clé 2        (clé explicite)
- 'php'        -> clé 'best'
- 'javascript' -> clé 3        (clé numérique auto = plus grande clé entière déjà
                                 utilisée (2) + 1, les clés string ne comptent pas)
- 'jQuery'     -> clé 5        (clé explicite)
*/
?>

<h2>Question 2 : var_dump</h2>
<pre><?php var_dump($t); ?></pre>

<h2>Question 3 : valeurs</h2>
<ol>
<?php foreach ($t as $valeur): ?>
    <li><?php echo $valeur; ?></li>
<?php endforeach; ?>
</ol>

<h2>Question 4 : clés</h2>
<ol>
<?php foreach ($t as $cle => $valeur): ?>
    <li><?php echo $cle; ?></li>
<?php endforeach; ?>
</ol>

<?php require "fin_code_html.php"; ?>
