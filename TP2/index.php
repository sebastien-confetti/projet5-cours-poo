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
    //$perso = new Personnage(['nom' => $_POST['nom']]); POUR LE TP1
    switch ($_POST['typePerso'])
    {
        case 'magicien' :
            $perso = new Magicien(['nom' => $_POST['nom']]);
            break;

        case 'guerrier' :
            $perso = new Guerrier(['nom' => $_POST['nom']]);
            break;

        default :
            $message = 'Le type du personnage est invalide.';
            break;
    }

    // si le type du personnage est valide
    if (isset($perso))
    {
        // on crée un  personnage
        if (!$perso->nomValide())
        {
            $message = 'Le nom choisi est invalide.';
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

                case Personnage::PERSO_ENDORMI :
                    $message = 'Vous êtes endormi, vous ne pouvez pas frapper de personnage !';
                    break;
            }
        }
    }
}
// si on a cliqué sur un personnage pour l'ensorceler
elseif (isset($_GET['ensorceler']))
{
    if (!isset($perso))
    {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    }
    else
    {
        // il faut bien vérifier que le personnage est un magicien
        if ($perso->typePerso() != 'magicien')
        {
            $message = 'Seuls les magiciens peuvent ensorceler des personnages !';
        }
        else
        {
            if (!$manager->exists((int) $_GET['ensorceler']))
            {
                $message = 'Le personnage que vous voulez frapper n\'existe pas !';
            }
            else
            {
                $persoAFrapper = $manager->get((int) $_GET['ensorceler']);
                $retour = $perso->lancerUnSort($persoAFrapper);

                switch ($retour)
                {
                    case Personnage::CEST_MOI :
                        $message = 'Mais... pourquoi voulez-vous vous ensorceler ???';
                        break;

                    case Personnage::PERSONNAGE_ENSORCELE :
                        $message = 'Le personnage a bien été ensorcelé !';

                        $manager->update($perso);
                        $manager->update($persoAFrapper);

                        break;

                    case Personnage::PAS_DE_MAGIE :
                        $message = 'Vous n\'avez pas de magie !';
                        break;

                    case Personnage::PERSO_ENDORMI :
                        $message = 'Vous êtes endormi, vous ne pouvez pas lancer de sort !';
                        break;
                }
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
                Type : <?= ucfirst($perso->typePerso()) ?><br />
                Nom : <?= htmlspecialchars($perso->nom()) ?><br />
                Dégâts : <?= $perso->degats() ?><br/>
    <?php
    // on affiche l'atout du personnage suivant son type
    switch ($perso->typePerso())
    {
        case 'magicien' :
            echo 'Magie : ';
            break;

        case 'guerrier' :
            echo 'Protection : ';
            break;
    }

    echo $perso->atout();
    ?>
            </p>
        </fieldset>

        <fieldset>
            <legend>Qui frapper ?</legend>
            <p>
    <?php
    // on récupère tous les personnages ($persos) par ordre alphabétique, dont le nom est différent de celui de notre personnage (on ne va pas se frapper nous-même :p)
    $persos = $manager->getList($perso->nom());

    // s'il n'y a aucun personnage
    if (empty($persos))
    {
        echo 'Personne à frapper !';
    }
    else
    {
        if ($perso->estEndormi())
        {
            echo 'Un magicien vous a endormi ! Vous allez vous réveiller dans ', $perso->reveil(),'.';
        }
        else
        {
            foreach ($persos as $unPerso)
            {
                echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a> (dégats : ', $unPerso->degats(), ' | type : ', $unPerso->typePerso(), ')';

                // si le personnage est un magicien
                if ($perso->typePerso() == 'magicien')
                {
                    // on ajoute un lien pour lancer un sort
                    echo ' | <a href=?ensorceler=', $unPerso->id(), '">Lancer un sort</a>';
                }

                echo '<br />';
            }
        }
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
                    <br />
                    Type :
                    <select name="typePerso">
                        <option value="magicien">Magicien</option>
                        <option value="guerrier">Guerrier</option>
                    </select>
                    <input type="submit" value="Créer ce personnage" name="creer" />
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