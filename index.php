<?php
/**
 * Page d'accueil du TP.
 *
 * Elle affiche trois zones côte à côte :
 *   - à gauche   : la liste des exercices déjà corrigés, avec les pages disponibles pour chacun
 *   - au centre  : le RÉSULTAT de l'exercice choisi, dans une iframe
 *   - à droite   : le CODE SOURCE, coloré, des fichiers de cet exercice
 *
 * Aucune liste n'est écrite "en dur" : la page regarde le contenu du dossier
 * dans lequel elle se trouve, repère les dossiers "exercice1", "exercice2"...
 * et construit les liens automatiquement. Dès qu'un nouveau dossier d'exercice
 * est ajouté (ou récupéré depuis GitHub), il apparaît ici tout seul.
 *
 * Le code est volontairement découpé en 4 étapes bien visibles :
 *   1. Configuration   : les réglages que l'on peut modifier
 *   2. Functions       : les outils réutilisables
 *   3. Processing      : on construit la liste et on lit le choix de l'élève
 *   4. Display         : le HTML, tout en bas
 */


/* =====================================================================
 * ÉTAPE 1 — CONFIGURATION
 * ===================================================================== */

// Dossier dans lequel se trouve CE fichier (index.php).
// __DIR__ est une "constante magique" de PHP : elle vaut toujours le chemin
// du dossier du fichier en cours, quel que soit l'endroit d'où on l'appelle.
$rootFolder = __DIR__;

// Tous les dossiers d'exercice commencent par ce préfixe, suivi d'un numéro.
$folderPrefix = 'exercice';

// Fichiers PHP qui ne sont PAS des pages à ouvrir dans le navigateur :
//  - debut_code_html.php / fin_code_html.php : morceaux de page inclus (require)
//  - TODOList.php                            : une classe, pas une page
// Ils restent visibles dans la colonne "code source" : on ne cache que le lien
// qui servirait à les exécuter tout seuls.
$notRunnableFiles = array(
    'debut_code_html.php',
    'fin_code_html.php',
    'TODOList.php',
);

// Couleurs utilisées par highlight_file() pour colorer le code source.
// PHP utilise par défaut des couleurs prévues pour un fond blanc : on les
// remplace ici par des couleurs lisibles sur le fond sombre de la page.
ini_set('highlight.html',    '#d4d4d4'); // le HTML, hors des balises PHP
ini_set('highlight.default', '#9cdcfe'); // les variables, les fonctions...
ini_set('highlight.keyword', '#569cd6'); // if, foreach, echo, function...
ini_set('highlight.string',  '#ce9178'); // le contenu des guillemets
ini_set('highlight.comment', '#6a9955'); // les commentaires


/* =====================================================================
 * ÉTAPE 2 — FUNCTIONS
 * ===================================================================== */

/**
 * Protège un texte avant de l'écrire dans la page HTML.
 * Indispensable dès qu'on affiche une donnée qui ne vient pas de nous
 * (ici : des noms de fichiers et des valeurs reçues dans l'URL).
 */
function escape($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Construit une adresse vers cette même page, avec les paramètres choisis.
 * http_build_query() se charge d'assembler et d'encoder le "?a=1&b=2".
 */
function buildUrl($parameters)
{
    return escape('index.php?' . http_build_query($parameters));
}

/**
 * Retourne la liste des noms de dossiers d'exercice présents sur le disque,
 * triés dans l'ordre des numéros (exercice2 avant exercice10).
 *
 * @return array ex. array('exercice1', 'exercice2', 'exercice10')
 */
function findExerciseFolders($rootFolder, $folderPrefix)
{
    $folders = array();

    // scandir() liste TOUT ce que contient le dossier : fichiers ET dossiers.
    $entries = scandir($rootFolder);

    foreach ($entries as $entry) {

        // On ne garde que les vrais dossiers (on ignore index.php, .git, etc.).
        if (!is_dir($rootFolder . '/' . $entry)) {
            continue;
        }

        // On ne garde que ceux qui s'appellent "exercice" suivi d'un numéro.
        // Le \d+ du motif signifie "un ou plusieurs chiffres".
        if (!preg_match('/^' . $folderPrefix . '\d+$/', $entry)) {
            continue;
        }

        $folders[] = $entry;
    }

    // Tri "naturel" : sans lui, PHP trierait comme du texte et placerait
    // "exercice10" juste après "exercice1".
    natsort($folders);

    // natsort() conserve les anciens indices : array_values() les renumérote.
    return array_values($folders);
}

/**
 * Retourne tous les fichiers PHP d'un dossier : ce sont les fichiers dont on
 * affichera le code source.
 *
 * @return array ex. array('debut_code_html.php', 'code.php', ...)
 */
function listSourceFiles($folderPath)
{
    $files = array();

    // glob() récupère directement tous les fichiers dont le nom finit par .php
    foreach (glob($folderPath . '/*.php') as $filePath) {

        // basename() ne garde que le nom du fichier, sans le chemin.
        $files[] = basename($filePath);
    }

    sort($files);

    return $files;
}

/**
 * Parmi les fichiers d'un dossier, retourne ceux qui sont de vraies pages,
 * c'est-à-dire celles que l'on peut ouvrir directement dans le navigateur.
 *
 * @return array ex. array('code.php')
 */
function keepRunnableFiles($files, $notRunnableFiles)
{
    $pages = array();

    foreach ($files as $fileName) {

        // in_array() vérifie si le nom fait partie des fichiers non exécutables.
        if (in_array($fileName, $notRunnableFiles)) {
            continue;
        }

        $pages[] = $fileName;
    }

    return $pages;
}

/**
 * Extrait le numéro contenu dans un nom de dossier.
 * "exercice10" devient 10.
 */
function getExerciseNumber($folderName, $folderPrefix)
{
    return (int) substr($folderName, strlen($folderPrefix));
}

/**
 * Choisit une valeur SÛRE parmi une liste autorisée.
 *
 * Règle de sécurité fondamentale : une valeur qui vient de l'URL ($_GET) ne
 * doit jamais être utilisée telle quelle dans un chemin de fichier. Sans cette
 * vérification, un visiteur pourrait demander "../../un/autre/fichier" et lire
 * n'importe quel fichier du serveur.
 *
 * @param  string|null $requested      la valeur demandée dans l'URL
 * @param  array       $allowedValues  la seule liste de valeurs acceptées
 * @param  string|null $fallback       valeur à utiliser si la demande est invalide
 * @return string|null
 */
function chooseFromList($requested, $allowedValues, $fallback = null)
{
    if ($requested !== null && in_array($requested, $allowedValues)) {
        return $requested;
    }

    if ($fallback !== null && in_array($fallback, $allowedValues)) {
        return $fallback;
    }

    // Sinon : la première valeur de la liste, ou rien si la liste est vide.
    return count($allowedValues) > 0 ? $allowedValues[0] : null;
}

/**
 * Lit un paramètre de l'URL, ou null s'il n'a pas été transmis.
 */
function readParameter($name)
{
    return isset($_GET[$name]) ? $_GET[$name] : null;
}


/* =====================================================================
 * ÉTAPE 3 — PROCESSING
 * ===================================================================== */

/* --- 3a. On construit la liste des exercices ------------------------- */

// $exercises est un tableau associatif : la clé est le nom du dossier, et la
// valeur décrit l'exercice :
//   'exercice9' => array(
//        'number' => 9,
//        'folder' => 'exercice9',
//        'files'  => array('debut_code_html.php', 'fin_code_html.php', 'nombre.php'),
//        'pages'  => array('nombre.php'),
//   )
$exercises = array();

foreach (findExerciseFolders($rootFolder, $folderPrefix) as $folderName) {

    $sourceFiles = listSourceFiles($rootFolder . '/' . $folderName);

    $exercises[$folderName] = array(
        'number' => getExerciseNumber($folderName, $folderPrefix),
        'folder' => $folderName,
        'files'  => $sourceFiles,
        'pages'  => keepRunnableFiles($sourceFiles, $notRunnableFiles),
    );
}

// count() compte les éléments d'un tableau : utile pour l'affichage du total.
$exerciseCount = count($exercises);


/* --- 3b. On lit le choix de l'élève dans l'URL ----------------------- */

// Trois paramètres, tous facultatifs :
//   exercise = le dossier choisi        (ex. exercice9)
//   page     = la page à exécuter       (ex. nombre.php)
//   file     = le fichier source à lire (ex. debut_code_html.php)
$selectedExercise = null;
$selectedPage     = null;
$selectedFile     = null;

$requestedExercise = readParameter('exercise');

// isset() sur le tableau suffit à valider le dossier : s'il n'est pas une clé
// de $exercises, c'est qu'il n'existe pas dans notre liste.
if ($requestedExercise !== null && isset($exercises[$requestedExercise])) {

    $selectedExercise = $exercises[$requestedExercise];

    // La page exécutée : celle demandée si elle est valide, sinon la première.
    $selectedPage = chooseFromList(
        readParameter('page'),
        $selectedExercise['pages']
    );

    // Le source affiché : celui demandé s'il est valide, sinon par défaut
    // celui de la page en cours d'exécution.
    $selectedFile = chooseFromList(
        readParameter('file'),
        $selectedExercise['files'],
        $selectedPage
    );
}

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>TP PHP — Sommaire des exercices</title>
<style>
    :root {
        --bg: #1e1e2e;
        --panel: #252538;
        --code-bg: #14141f;
        --border: #3a3a55;
        --text: #e4e4f0;
        --text-muted: #9a9ac0;
        --accent: #6c8cff;
        --accent-hover: #86a1ff;
    }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
        margin: 0;
        font-family: "Segoe UI", Roboto, sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.5;
    }

    /* --- En-tête --- */
    header {
        display: flex;
        align-items: baseline;
        gap: 1rem;
        padding: 0.7rem 1.25rem;
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    header h1 { font-size: 1.05rem; margin: 0; font-weight: 600; }
    header .subtitle { font-size: 0.8rem; color: var(--text-muted); }

    /* --- Les trois colonnes --- */
    .layout {
        display: grid;
        grid-template-columns: 230px 1fr 1fr;
        height: calc(100% - 49px);
    }
    .column {
        display: flex;
        flex-direction: column;
        min-width: 0;          /* sans cela, le code trop large déforme la grille */
        border-left: 1px solid var(--border);
    }
    .column:first-child { border-left: none; }
    .column-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    .column-body { flex: 1; overflow: auto; }

    /* --- Colonne 1 : la liste des exercices --- */
    .exercise { padding: 0.55rem 1rem; border-bottom: 1px solid var(--border); }
    .exercise h2 {
        font-size: 0.8rem;
        margin: 0 0 0.35rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .exercise a {
        display: block;
        padding: 0.3rem 0.5rem;
        margin-bottom: 0.2rem;
        border-radius: 5px;
        color: var(--text);
        text-decoration: none;
        font-family: Consolas, monospace;
        font-size: 0.8rem;
    }
    .exercise a:hover { background: var(--border); }
    .exercise a.active { background: var(--accent); color: #fff; }
    .exercise .empty { font-size: 0.75rem; color: var(--text-muted); font-style: italic; }

    /* --- Colonne 2 : le résultat --- */
    iframe { width: 100%; height: 100%; border: none; background: #fff; }

    /* --- Colonne 3 : le code source --- */
    .tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        padding: 0.45rem 1rem;
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    .tabs a {
        padding: 0.22rem 0.55rem;
        border-radius: 5px;
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        font-family: Consolas, monospace;
        font-size: 0.75rem;
    }
    .tabs a:hover { color: var(--text); background: var(--border); }
    .tabs a.active { background: var(--accent); border-color: var(--accent); color: #fff; }
    .source { background: var(--code-bg); padding: 0.75rem 1rem; }
    /* highlight_file() produit lui-même un <pre> : on ne fait que le styler. */
    .source pre {
        margin: 0;
        font-family: "Fira Code", Consolas, monospace;
        font-size: 0.8rem;
        line-height: 1.55;
    }

    /* --- Message affiché quand rien n'est sélectionné --- */
    .placeholder {
        padding: 2rem 1.5rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .open-link { color: var(--accent); text-decoration: none; font-size: 0.72rem; }
    .open-link:hover { color: var(--accent-hover); }

    /* --- Écrans étroits : on empile les trois zones --- */
    @media (max-width: 950px) {
        .layout { grid-template-columns: 1fr; height: auto; }
        .column { border-left: none; border-top: 1px solid var(--border); }
        .column-body { max-height: 70vh; }
        iframe { height: 70vh; }
    }
</style>
</head>
<body>

<?php
/* =====================================================================
 * ÉTAPE 4 — DISPLAY
 * ===================================================================== */
?>

<header>
    <h1>TP PHP — Sommaire des exercices</h1>
    <span class="subtitle">
        <?php echo $exerciseCount; ?> exercice(s) détecté(s) automatiquement
        dans ce dossier
    </span>
</header>

<div class="layout">

    <?php /* ---------- Colonne 1 : la liste des exercices ---------- */ ?>
    <div class="column">
        <div class="column-title">Exercices</div>
        <div class="column-body">

            <?php if ($exerciseCount === 0) { ?>

                <p class="placeholder">
                    Aucun dossier « <?php echo escape($folderPrefix); ?>N »
                    n'a été trouvé à côté de index.php.
                </p>

            <?php } ?>

            <?php foreach ($exercises as $exercise) { ?>

                <div class="exercise">

                    <h2>Exercice <?php echo $exercise['number']; ?></h2>

                    <?php if (count($exercise['pages']) === 0) { ?>

                        <p class="empty">Aucune page à ouvrir.</p>

                    <?php } ?>

                    <?php foreach ($exercise['pages'] as $page) { ?>
                        <?php
                        // Le lien est "actif" si c'est exactement ce que l'on
                        // est en train de regarder.
                        $isActive = $selectedExercise !== null
                            && $selectedExercise['folder'] === $exercise['folder']
                            && $selectedPage === $page;
                        ?>
                        <a class="<?php echo $isActive ? 'active' : ''; ?>"
                           href="<?php echo buildUrl(array(
                               'exercise' => $exercise['folder'],
                               'page'     => $page,
                           )); ?>">
                            <?php echo escape($page); ?>
                        </a>
                    <?php } ?>

                </div>

            <?php } ?>

        </div>
    </div>

    <?php /* ---------- Colonne 2 : le résultat dans une iframe ---------- */ ?>
    <div class="column">
        <div class="column-title">
            <span>Résultat</span>
            <?php if ($selectedPage !== null) { ?>
                <a class="open-link" target="_blank"
                   href="<?php echo escape($selectedExercise['folder'] . '/' . $selectedPage); ?>">
                    ouvrir dans un onglet ↗
                </a>
            <?php } ?>
        </div>
        <div class="column-body">

            <?php if ($selectedPage === null) { ?>

                <p class="placeholder">
                    Choisis un exercice dans la colonne de gauche : sa page
                    s'exécutera ici, et son code source s'affichera à droite.
                </p>

            <?php } else { ?>

                <?php
                // L'iframe demande la page au serveur exactement comme si on
                // l'ouvrait dans un onglet : le PHP y est donc bien exécuté.
                ?>
                <iframe
                    title="Résultat de <?php echo escape($selectedPage); ?>"
                    src="<?php echo escape($selectedExercise['folder'] . '/' . $selectedPage); ?>"></iframe>

            <?php } ?>

        </div>
    </div>

    <?php /* ---------- Colonne 3 : le code source coloré ---------- */ ?>
    <div class="column">
        <div class="column-title">
            <span>Code source</span>
            <?php if ($selectedFile !== null) { ?>
                <span><?php echo escape($selectedExercise['folder'] . '/' . $selectedFile); ?></span>
            <?php } ?>
        </div>

        <?php if ($selectedExercise !== null) { ?>
            <?php // Un onglet par fichier du dossier, y compris ceux inclus. ?>
            <div class="tabs">
                <?php foreach ($selectedExercise['files'] as $file) { ?>
                    <a class="<?php echo $file === $selectedFile ? 'active' : ''; ?>"
                       href="<?php echo buildUrl(array(
                           'exercise' => $selectedExercise['folder'],
                           'page'     => $selectedPage,
                           'file'     => $file,
                       )); ?>">
                        <?php echo escape($file); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="column-body source">

            <?php if ($selectedFile === null) { ?>

                <p class="placeholder">Aucun fichier à afficher pour le moment.</p>

            <?php } else { ?>

                <?php
                // highlight_file() lit le fichier et renvoie du HTML déjà
                // colorié. On l'affiche donc tel quel, SANS escape() : ici le
                // HTML est produit par PHP lui-même, pas par le visiteur.
                // Le chemin est sûr : $selectedFile a été validé à l'étape 3b.
                echo highlight_file(
                    $rootFolder . '/' . $selectedExercise['folder'] . '/' . $selectedFile,
                    true
                );
                ?>

            <?php } ?>

        </div>
    </div>

</div>

</body>
</html>
