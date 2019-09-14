<?php
class Magicien extends Personnage   // la classe Magicien hérite de toutes les méthodes de la classe Personnage
{
    private $_magie;    // indique la puissance du magicien sur 100, sa capacité à produire de la magie.

    public  function lancerUnSort($perso)
    {
        // on dit que la magie du magicien représente sa force
        $perso->recevoirDegats($this->_magie);
    }

    public function gagnerExperience()
    {
        /*
           On met ce qui suit seulement si la classe mère n'est pas "abstract"
        // on appelle la méthode gagnerExpérience() de la classe parente
        parent::gagnerExperience();
        */



        // si sa magie est < 100
        if ($this->_magie < 100)
        {
            // on augmente sa magie de 10
            $this->_magie += 10;
        }
    }
}