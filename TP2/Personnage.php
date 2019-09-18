<?php
abstract class Personnage   // "abstract" (abstraite) empêche l'instanciation ($perso = new Personnage) cette classe
{
    // ---------------------------------------
    // Création d'ATTRIBUTS
    // (caractéristiques du personnage)
    // ---------------------------------------
    // on ne met pas "_" entre dans "$_id" car la classe est "abstract"
    protected   $id,
                $nom,
                $degats;
    protected $experience;
    protected $niveau;
    protected $forcePerso;   // car l'identifiant "force" est un mot-clé MySQL réservé
    protected   $timeEndormi,
                $typePerso;


    // ---------------------------------------
    // Création des CONSTANTES
    // ---------------------------------------
    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même
    const PERSONNAGE_TUE = 2;   // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant
    const PERSONNAGE_FRAPPE = 3;    // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage
    const PERSONNAGE_ENSORCELE = 4; // // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage
    const PAS_DE_MAGIE = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0
    const PERSO_ENDORMI = 6;    // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi

    // ---------------------------------------
    // CONSTRUCTEUR (pour initialiser les attributs dès sa création)
    // ---------------------------------------
    public function __construct(array $donnees)
    {
        // l'objet $this appelle la méthode hydrate()
        $this->hydrate($donnees);   // Implémentation du constructeur qui sera ensuite hydraté avec le tableau $donnees
        $this->typePerso = strtolower(static::class);    // "static" permet de faire référence à la classe appelée et non celle contenant la fonction
                        // "strtolower()" pour mettre en munuscule
    }

    // ---------------------------------------
    // Création des METHODES
    // (fonctionnalités d'un personnage)
    // ---------------------------------------
    public function nomValide()
    {
        // si $_nom n'est pas vide, retourne "true" sinon retourne "false"
        return !empty($this->nom);
    }

    public function estEndormi()
    {
        return $this->timeEndormi > time();
    }

    // "abstract" force toute classe fille à écrire cette méthode car chaque personnage frappe différemment
    // (on ne peut faire ça que si la classe est "abstract")
    //abstract public function frapper(Personnage $perso);
    public function frapper(Personnage $perso)
    {
        // Avant tout : vérifier qu'on ne se frappe pas soi-même.
        // Si c'est le cas, on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est le personnage qui attaque.
        if ($perso->id() == $this->id)
        {
            return self::CEST_MOI;  // "self::" permet l'accès à l'attribut statique CEST_MOI
        }

        // si le personnage est endormi
        if ($this->estEndormi())
        {
            return self::PERSO_ENDORMI; // renvoi la valeur de PERSO_ENDORMI soit 6
        }

        // On indique au personnage frappé qu'il doit recevoir des dégâts
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats();
    }

    public function recevoirDegats()
    {
        // On augmente de 5 les dégâts
        $this->degats += 5;

        // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué
        if ($this->degats >= 100)
        {
            return self::PERSONNAGE_TUE;  // "self::" permet l'accès à l'attribut statique PERSONNAGE_TUE
        }

        // Sinon, on se contente de dire que le personnage a bien été frappé
        return self::PERSONNAGE_FRAPPE;  // "self::" permet l'accès à l'attribut statique PERSONNAGE_FRAPPE
    }

    public function gagnerExperience()
    {
        // on augmente de 5 l'experience
        $this->experience += 5;

        if ($this->experience >= 100)
        {
            augmenterNiveau();
        }
    }

    public function reveil()
    {
        $secondes = $this->timeEndormi; // donne la valeur de timeEndormi à $secondes
        $secondes -= time();    // soustrait time() à $secondes

        $heures = floor($secondes / 3600);  // floor() reourne la valeur arrondie par défaut de $secondes
        $secondes -= $heures * 3600;
        $minutes = floor($secondes / 60);
        $secondes -= $minutes * 60;

        $heures .= $heures <= 1 ? ' heure' : ' heures'; // donne la valeur... A EXPLIQUER !!!
        $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
        $secondes .= $secondes <= 1 ? ' secondes' : ' secondes';

        return $heures . ', ' . $minutes . ' et ' . $secondes;
    }

    // ---------------------------------------
    // HYDRATATION (pour assigner des valeurs aux attributs d'un objet)
    // ---------------------------------------
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value)
        {
            $method = 'set'.ucfirst($key);  // ucfirst() met la 1ère lettre du mot en majuscule
            // cela revient à écrire "$method = setId" par exemple

            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }

    // ---------------------------------------
    // Création des GETTERS pour pouvoir lire les attributs de nos objets
    // ---------------------------------------
    public function id()
    {
        return $this->id;  // retourne l'attribut $id
    }

    public function nom()
    {
        return $this->nom; // retourne l'attribut $nom
    }

    public function degats()
    {
        return $this->degats;  // retourne l'attribut $degats
    }

    public function experience()
    {
        return $this->experience;  // retourne l'attribut $experience
    }

    public function niveau()
    {
        return $this->niveau;  // retourne l'attribut $niveau
    }

    public function forcePerso()
    {
        return $this->forcePerso;  // retourne l'attribut $forcePerso
    }

    public function atout()
    {
        return $this->atout;    // retourne l'attribut $atout
    }

    public function timeEndormi()
    {
        return $this->timeEndormi;  // retourne l'attribut $timeEndormi
    }

    public function typePerso()
    {
        return $this->typePerso; // reourne l'attribut $typePerso
    }

    // ---------------------------------------
    // Création des SETTERS pour pouvoir modifier et lire les valeurs des attributs de nos objets
    // ---------------------------------------
    public function setId($id)
    {
        $id = (int) $id;    // Le convertit en nombre entier

        if ($id > 0)
        {
            $this->id = $id;   // donne la valeur $id à l'attribut $id
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom))    // "is_string" vérifie qu'il s'agit bien d'une chaîne de caractères
        {
            $this->nom = $nom; // donne la valeur $nom à l'attribut $nom
        }
    }

    public function setDegats($degats)
    {
        $degats = (int) $degats;    // Le convertit en nombre entier

        if ($degats >= 0 && $degats <= 100)
        {
            $this->degats = $degats;   // donne la valeur $degats à l'attribut $degats
        }
    }

    public function setExperience($experience)
    {
        $experience = (int) $experience;    // Le convertit en nombre entier

        // on vérifie que l'expérience est comprise entre 0 et 100
        if ($experience >= 0 && $experience <= 100)
        {
            $this->experience = $experience;   // donne la valeur $experience à l'attribut $experience
        }
    }

    public function setNiveau($niveau)
    {
        $niveau = (int) $niveau;    // le convertit en nombre entier

        // onvérifie que le niveau n'est pas négatif
        if ($niveau >= 00)
        {
            $this->niveau = $niveau;   // donne la valeur $niveau à l'attribut $niveau
        }
    }

    public function SetForcePerso($forcePerso)
    {
        $forcePerso = (int) $forcePerso;   // le convertit en nombre entier

        // on vérifie que la force passée est comprise entre 0 et 100
        if ($forcePerso >= 0 && $forcePerso <= 100)
        {
            $this->forcePerso = $forcePerso;
        }
    }

    public function setAtout($atout)
    {
        $atout = (int) $atout;   // le convertit en nombre entier

        // si l'atout est compris entre 0 et 100
        if ($atout >= 0 && $atout <= 100)
        {
            $this->atout = $atout;  // donne la valeur $atout à l'attribut $atout
        }
    }

    public function setTimeEndormi($time)
    {
        $this->timeEndormi = (int) $time;   // donne la valeur intière $time à l'attribut $timeEndormi
    }
}