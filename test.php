<?php
class MonException extends Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message;
    }
}

function additionner($a, $b)
{
    if (!is_numeric($a) || !is_numeric($b))
    {
        throw new MonException('Les deux paramètres doivent être des nombres'); // On lance une exception "MonException".
    }

    return $a + $b;
}

try // Nous allons essayer d'effectuer les instructions situées dans ce bloc.
{
    echo additionner(12, 3), '<br />';
    echo additionner('azerty', 54), '<br />';
    echo additionner(4, 8);
}

catch (MonException $e) // Nous allons attraper les exceptions "MonException" s'il y en a une qui est levée.
{
    echo $e; // On affiche le message d'erreur grâce à la méthode __toString que l'on a écrite.
}

echo '<br />Fin du script'; // Ce message s'affiche, ça prouve bien que le script est exécuté jusqu'au bout.