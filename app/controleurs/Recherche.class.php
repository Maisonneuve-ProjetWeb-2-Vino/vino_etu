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
            $resultats = $this->filtrer($body, $utilisateur_id);
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
    private function filtrer($parametres, $utilisateur_id) {

        if (empty($parametres->recherche)) {
            if ($parametres->donnees == "catalogue") {
                $donnees = $this->oRequetesSQL->obtenirListeBouteillesCatalogue($utilisateur_id);
            } else if ($parametres->donnees == "celliers") {
                $donnees = $this->oRequetesSQL->obtenirListeBouteillesCelliers($utilisateur_id);
            } else {
                throw new Exception ("Type de données inconnu");
            }
        } else {
            if ($parametres->donnees == "catalogue") {
                $donnees = $this->oRequetesSQL->obtenirRechercheBouteillesCatalogue($utilisateur_id, $parametres->recherche);
            } else if ($parametres->donnees == "celliers") {
                $donnees = $this->oRequetesSQL->obtenirRechercheBouteillesCelliers($utilisateur_id, $parametres->recherche);
            } else {
                throw new Exception ("Type de données inconnu");
            }
        }

        $resultats = $this->filtrerResultats($donnees, $parametres->filtre);

        return $resultats;
    }

    private function filtrerResultats($donnees, $parametres) {

        $resultats = [];

        // Filtre de couleur
        if (count($parametres->couleur) != 0) {

            foreach($parametres->couleur as $couleur) {

                $resultatsIntermediaires = array_filter($donnees, function($resultat) use($couleur) {
                    return ($resultat['idtype'] == $couleur) ? true : false;
                });

                $resultats = $resultats + $resultatsIntermediaires;
            }

        } else {
            $resultats = $donnees;
        }

        // Filtre de pays
        if (count($parametres->pays) != 0) {

            $resultatsIntermediaires = [];
            foreach($parametres->pays as $unPays) {
                $resultatsBoucle = array_filter($resultats, function($resultat) use($unPays) {
                    return ($resultat['idpays'] == $unPays) ? true : false;
                });

                $resultatsIntermediaires = $resultatsIntermediaires + $resultatsBoucle;
            }

            $resultats = $resultatsIntermediaires;

        } else {
            $resultats = $resultats;
        } 

        return $resultats;

    }
}