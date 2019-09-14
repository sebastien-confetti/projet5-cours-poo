<?php
class Personnage   // "abstract" (abstraite) empêche l'instanciation ($perso = new Personnage) cette classe
{
    // ---------------------------------------
    // Création d'ATTRIBUTS
    // (caractéristiques du personnage)
    // ---------------------------------------
    private $_id,
            $_nom,
            $_degats;
    private $_experience;
    private $_niveau;
    private $_forcePerso;   // car l'identifiant "force" est un mot-clé MySQL réservé.


    // ---------------------------------------
    // Création des CONSTANTES
    // ---------------------------------------
    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même
    const PERSONNAGE_TUE = 2;   // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant
    const PERSONNAGE_FRAPPE = 3;    // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage

    // ---------------------------------------
    // CONSTRUCTEUR (pour initialiser les attributs dès sa création)
    // ---------------------------------------
    public function __construct(array $donnees)
    {
        // l'objet $this appelle la méthode hydrate()
        $this->hydrate($donnees);   // Implémentation du constructeur qui sera ensuite hydraté avec le tableau $donnees
    }

    // ---------------------------------------
    // Création des METHODES
    // (fonctionnalités d'un personnage)
    // ---------------------------------------
    public function nomValide()
    {
        // si $_nom n'est pas vide, retourne "true" sinon retourne "false"
        return !empty($this->_nom);
    }

    // "abstract" force toute classe fille à écrire cette méthode car chaque personnage frappe différemment
    // (on ne peut faire ça que si la classe est "abstract")
    //abstract public function frapper(Personnage $perso);
    public function frapper(Personnage $perso)
    {
        // Avant tout : vérifier qu'on ne se frappe pas soi-même.
        // Si c'est le cas, on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est le personnage qui attaque.
        if ($perso->id() == $this->_id)
        {
            return self::CEST_MOI;  // "self::" permet l'accès à l'attribut statique CEST_MOI
        }

        // On indique au personnage frappé qu'il doit recevoir des dégâts
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats();
    }

    public function recevoirDegats()
    {
        // On augmente de 5 les dégâts
        $this->_degats += 5;

        // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué
        if ($this->_degats >= 100)
        {
            return self::PERSONNAGE_TUE;
        }

        // Sinon, on se contente de dire que le personnage a bien été frappé
        return self::PERSONNAGE_FRAPPE;
    }

    public function gagnerExperience()
    {
        // on augmente de 5 l'experience
        $this->_experience += 5;

        if ($this->_experience >= 100)
        {
            augmenterNiveau();
        }

        //
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
        return $this->_id;  // retourne l'attribut $_id
    }

    public function nom()
    {
        return $this->_nom; // retourne l'attribut $_nom
    }

    public function degats()
    {
        return $this->_degats;  // retourne l'attribut $_degats
    }

    public function experience()
    {
        return $this->_experience;  // retourne l'attribut $_experience
    }

    public function niveau()
    {
        return $this->_niveau;  // retourne l'attribut $_niveau
    }

    public function forcePerso()
    {
        return $this->_forcePerso;  // retourne l'attribut $_forcePerso
    }

    // ---------------------------------------
    // Création des SETTERS pour pouvoir modifier et lire les valeurs des attributs de nos objets
    // ---------------------------------------
    public function setId($id)
    {
        $id = (int) $id;    // Le convertit en nombre entier

        if ($id > 0)
        {
            $this->_id = $id;   // donne la valeur $id à l'attribut $_id
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom))    // "is_string" vérifie qu'il s'agit bien d'une chaîne de caractères
        {
            $this->_nom = $nom; // donne la valeur $nom à l'attribut $_nom
        }
    }

    public function setDegats($degats)
    {
        $degats = (int) $degats;    // Le convertit en nombre entier

        if ($degats >= 0 && $degats <= 100)
        {
            $this->_degats = $degats;   // donne la valeur $degats à l'attribut $_degats
        }
    }

    public function setExperience($experience)
    {
        $experience = (int) $experience;    // Le convertit en nombre entier

        // on vérifie que l'expérience est comprise entre 0 et 100
        if ($experience >= 0 && $experience <= 100)
        {
            $this->_experience = $experience;   // donne la valeur $experience à l'attribut $_experience
        }
    }

    public function setNiveau($niveau)
    {
        $niveau = (int) $niveau;    // le convertit en nombre entier

        // onvérifie que le niveau n'est pas négatif
        if ($niveau >= 00)
        {
            $this->_niveau = $niveau;   // donne la valeur $niveau à l'attribut $_niveau
        }
    }

    public function SetForcePerso($forcePerso)
    {
        $forcePerso = (int) $forcePerso;

        // on vérifie que la force passée est comprise entre 0 et 100
        if ($forcePerso >= 0 && $forcePerso <= 100)
        {
            $this->_forcePerso = $forcePerso;
        }
    }
}