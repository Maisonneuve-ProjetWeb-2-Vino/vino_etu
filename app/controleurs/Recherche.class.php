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

            $body = json_decode(file_get_contents('php://input'));

        if(!empty($body)){
            $resultats = $this->filtrer($body);
            echo json_encode($resultats);
        }
        else { // Affichage de la page de recherche
            $pays = $this->oRequetesSQL->obtenirPaysCourantsCatalogue($utilisateur_id);
            $types = $this->oRequetesSQL->obtenirTypesCourantsCatalogue($utilisateur_id);

            new Vue("/Recherche/vRecherche",
            array(
                'titre'       => "Recherche",
                'pays'        => $pays,
                'types'       => $types
            ),
            "/Frontend/gabarit-frontend");
        }

    }

    /**
     * Effectue les opérations de recherche et filtrage des résultats
     * 
     * @param Array - Tableau avec les paramètres de la recherche et filtre.
     */
    private function filtrer($parametres) {
        $parametres
    }
}