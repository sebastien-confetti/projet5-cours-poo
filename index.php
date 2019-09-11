<?php
// on enregistre notre autoload
function chargerClasse($classname)
{
    require $classname.'.php';
}

spl_autoload_register('chargerClasse');

// on appelle session_start() APRES avoir enregistré l'autoload
session_start();

// si on clique sur déconnexion
if (isset($_GET['deconnexion']))
{
    session_destroy();
    header('Location: .');
    exit();
}

// si la session perso existe
if (isset($_SESSION['perso']))
{
    // on restaure l'objet
    $perso = $_SESSION['perso'];
}

require './config/db.php';

// création du nouvel objet $manager de type PersonnagesManager
$manager = new PersonnagesManager($db); // on instancie la classe PersonnagesManager

// si on a voulu créer un personnage
if (isset($_POST['creer']) && isset($_POST['nom']))
{
    // on crée un nouveau personnage (création du nouvel objet $perso de type Personnage)
    $perso = new Personnage(['nom' => $_POST['nom']]);

    if (!$perso->nomValide())
    {
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);  // détruit l'objet $perso
    }
    elseif ($manager->exists($perso->nom()))
    {
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    }
    else
    {
        $manager->add($perso);  // donne la fonction add de l'objet $perso à $manager
    }
}

// si on a voulu utiliser un personnage
elseif (isset($_POST['utiliser']) && isset($_POST['nom']))
{
    // si celui-ci existe
    if ($manager->exists($_POST['nom']))
    {
        $perso = $manager->get($_POST['nom']);
    }
    else
    {
        // s'il n'existe pas, on affichera ce message
        $message = 'Ce personnage n\'existe pas !';
    }
}
// si on a cliqué sur un personnage pour le frapper
elseif (isset($_GET['frapper']))
{
    if (!isset($perso))
    {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    }
    else
    {
        if (!$manager->exists((int) $_GET['frapper']))
        {
            $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        }
        else
        {
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            // on stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper
            $retour = $perso->frapper($persoAFrapper);

            switch ($retour)
            {
                case Personnage::CEST_MOI :
                    $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                    break;

                case Personnage::PERSONNAGE_FRAPPE :
                    $message = 'Le personnage a bien été frappé !';

                    $manager->update($perso);
                    $manager->update($persoAFrapper);

                    break;
                case Personnage::PERSONNAGE_TUE :
                    $message = 'Vous avez tué ce personnage !';

                    $manager->update($perso);
                    $manager->delete($persoAFrapper);

                    break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TP : Mini jeu de combat</title>

        <meta charset="utf-8" />
    </head>
    <body>
        <p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
// s'il y a un message à afficher
if (isset($message))
{
    echo '<p>', $message, '</p>';   // on l'affiche
}

// si on utilise un personnage (nouveau ou pas)
if (isset($perso))
{
?>
        <p><a href="?deconnexion=1">Déconnexion</a></p>

        <fieldset>
            <legend>Mes informations</legend>
            <p>
                Nom : <?= htmlspecialchars($perso->nom()) ?><br />
                Dégâts : <?= $perso->degats() ?>
            </p>
        </fieldset>

        <fieldset>
            <legend>Qui frapper ?</legend>
            <p>
<?php
$persos = $manager->getList($perso->nom());

// s'il n'y a aucun personnage
if (empty($persos))
{
    echo 'Personne à frapper !';
}
else
{
    foreach ($persos as $unPerso)
        echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a> (dégâts : ', $unPerso->degats(), ' ; Expérience : ', $unPerso->experience(), ')<br />';
}
?>
            </p>
        </fieldset>
<?php
}
else
{
?>
            <form action="" method="post">
                <p>
                    Nom : <input type="text" name="nom" maxlength="50" />
                    <input type="submit" value="Créer ce personnage" name="creer" />
                    <input type="submit" value="Utiliser ce personnage" name="utiliser" />
                </p>
            </form>
<?php
}
?>
    </body>
</html>
<?php
// si on créé un personnage
if (isset($perso))
{
    // on le stocke dans une variable session afin d'économiser une requ^te SQL
    $_SESSION['perso'] = $perso;
}