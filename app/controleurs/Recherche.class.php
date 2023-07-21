<?php

/**
 * Classe Contrôleur des requêtes de l'interface Recherche.
 */

class Recherche extends Routeur {

    private $oUtilConn;

    /**
     * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
     * 
     */
    public function __construct() {

        $this->oUtilConn = $_SESSION['oConnexion'] ?? null;
        $this->oRequetesSQL = new RequetesSQL;
    }


    /**
     * Affiche la page des requêtes de recherche.
     */
    public function rechercher() {

        if (!$this->oUtilConn) {
            header("Location: accueil"); // retour sur la page de connexion
            exit;
        } else {
            $utilisateur_id = $this->oUtilConn->id_membre;
        }

        $pays =$this->oRequetesSQL->obtenirPaysCourantsCatalogue($utilisateur_id);

        new Vue("/Recherche/vRecherche",
        array(
            'titre'       => "Recherche",
            'pays'        => $pays
        ),
        "/Frontend/gabarit-frontend");
    }
}