<?php
class PersonnagesManager
{
    private $_db;   // Instance de PDO

    // ---------------------------------------
    // CONSTRUCTEUR (pour initialiser les attributs dès sa création)
    // ---------------------------------------
    public function __construct($db)
    {
        // l'objet $this appelle la méthode setDb()
        $this->setDb($db);
    }

    public function add(Personnage $perso)
    {
        // Préparation de la requête d'insertion
        // Assignation des valeurs pour le nom du personnage
        $req = $this->_db->prepare('INSERT INTO Personnages(nom) VALUES(:nom)');
        $req->bindValue(':nom', $perso->nom());
        $req->execute(); // Exécution de la requête

        // Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0)
        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }

    public function count()
    {
        // Exécute une requête COUNT() et retourne le nombre de résultats retourné
        return $this->_db->query('SELECT COUNT(*) FROM Personnages')->fetchColumn();
    }

    public function delete(Personnage $perso)
    {
        // Exécute une requête de type DELETE
        $this->_db->exec('DELETE FROM Personnages WHERE id = '.$perso->id());
    }

    public function exists($info)
    {
        // Si le paramètre est un entier, c'est qu'on a fourni un identifiant
        if (is_int($info))
        {
            // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean
            return (bool) $this->_db->query('SELECT COUNT(*) FROM Personnages WHERE id = '.$info)->fetchColumn();
        }

        // Sinon c'est qu'on peut vérifier que le nom existe ou pas
        // grâce à une requête COUNT() avec une clause WHERE
        $req = $this->_db->prepare('SELECT COUNT(*) FROM Personnages WHERE nom = :nom');
        $req->execute([':nom' => $info]);
        // Retourne un boolean
        return (bool) $req->fetchColumn();
    }

    public function get($info)
    {
        // Si le paramètre est un entier
        if (is_int($info))
        {
            // on récupére le personnage avec son identifiant
            $req = $this->_db->query('SELECT id, nom, degats FROM Personnages WHERE id = '.$info);
            $donnees = $req->fetch(PDO::FETCH_ASSOC);
            // Retourne un objet Personnage
            return new Personnage($donnees);
        }
        else    // Sinon, on veut récupérer le personnage avec son nom
        {
            // Exécute une requête de type SELECT avec une clause WHERE
            $req = $this->_db->prepare('SELECT id, nom, degats FROM Personnages WHERE nom = :nom');
            $req->execute([':nom' => $info]);
            // Retourne un objet Personnage
            return new Personnage($req->fetch(PDO::FETCH_ASSOC));
        }
    }

    public function getList($nom)
    {
        $persos = [];

        // Retourne la liste des personnages dont le nom n'est pas $nom
        $req = $this->_db->prepare('SELECT id, nom, degats FROM Personnages WHERE nom <> :nom ORDER BY nom');
        $req->execute([':nom' => $nom]);

        // Le résultat sera un tableau d'instances de Personnage
        while ($donnees = $req->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Personnage($donnees);
        }

        return $persos;
    }

    public function update(Personnage $perso)
    {
        // Prépare une requête de type UPDATE
        $req = $this->_db->prepare('UPDATE Personnages SET degats = :degats WHERE id = :id');
        // Assignation des valeurs à la requête
        $req->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
        $req->bindValue(':id', $perso->id(), PDO::PARAM_INT);
        // Exécution de la requête
        $req->execute();
    }

    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}