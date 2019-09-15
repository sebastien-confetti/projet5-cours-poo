<?php
class Guerrier extends Personnage
{
    public function recevoirDegats()
    {
        if ($this->degats >= 0 && $this->degats <= 25) {
            $this->atout = 4;
        } elseif ($this->degats > 25 && $this->degats <= 50) {
            $this->atout = 3;
        } elseif ($this->degats > 50 && $this->degats <= 75) {
            $this->atout = 2;
        } elseif ($this->degats > 75 && $this->degats <= 90) {
            $this->atout = 1;
        } else {
            $this->atout = 0;
        }

        $this->degats += 5 - $this->atout;

        // si on a 100 de dégâts ou plus
        if ($this->degats >= 100) {
            // on supprime le personnage de la BDD
            return self::PERSONNAGE_TUE;
        }

        // sinon, on se contente de mettre à jour les dégâts du personnage
        return self::PERSONNAGE_FRAPPE;
    }
}