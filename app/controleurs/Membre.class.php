<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Membre extends Routeur {
  private $id_membre;
  private $oConnexion;

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
     $this->oConnexion = $_SESSION['oConnexion'] ?? null;
        $this->id_membre = $_GET['id_membre'] ?? null;
        $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Afficher la connexion.
   * @return void
   */  
  public function connecter() {

     new Vue(
            '/Frontend/vConnexion',
            array(

                'titre'  => 'Connexion membre'
            ),
            'Frontend/gabarit-frontend'
        );
}


public function connexion() {
        // Récupérer les données du formulaire
        $courriel = $_POST['courriel'];
        $mdp = $_POST['mdp'];
        
        var_dump($courriel);
        // Vérifier les identifiants
        $membre = new Membre();
        $oConnexion = $membre->verifConnexion($courriel, $mdp);

        if ($oConnexion) {
            // Les identifiants sont corrects, créer une session
            session_start();
            $_SESSION['id_membre'] = $membre['id_membre'];
            $_SESSION['courriel'] = $membre['email'];
            $_SESSION['nom'] = $membre['nom'];
            $_SESSION['prenom'] = $membre['prenom'];

            // Rediriger l'utilisateur vers une page après la connexion réussie
            header('Location: accueil.twig');
            exit;
        } else {
            // Les identifiants sont incorrects, afficher un message d'erreur
            echo "Identifiants incorrects";
        }
    }
public function verifConnexion($courriel, $mdp)

  {
    
    $this->sql = "
      SELECT id_membre, nom, prenom, courriel, mdp, idprofil
      FROM membres
      WHERE courriel = :courriel AND mdp = SHA2(:mdp, 512)";
        
        $membre = fetch(PDO::FETCH_ASSOC);

        if ($membre && password_verify($password, $membre['password'])) {
            // Les identifiants sont corrects
            var_dump($membre);
            return $membre;
        } else {
            // Les identifiants sont incorrects
            return false;
  }

  

}


/**
     * Afficher la page inscription
     */
    public function inscription()
    {

        new Vue(
            '/Frontend/vInscription',
            array(

                'titre'  => 'Inscription membre'
            ),
            'Frontend/gabarit-frontend'
        );
    }
    /**
     * Inscription d'un nouveau membre
     */

    public function validationInscription()
    {
        //var_dump($_POST);
        
        $membre  = [];
        $erreurs = [];
        if (count($_POST) !== 0) {
            if($_POST['mdp'] === $_POST['renouvelermdp']){
                // retour de saisie du formulaire
                $membre = $_POST;
                var_dump($membre);
                $oMembre = new Membres($membre); // création d'un objet membre pour contrôler la saisie
                $erreurs = $oMembre->erreurs;
                if (count($erreurs) === 0) {
                    $id_membre = $this->oRequetesSQL->getInscription([
                        'nom'    => $oMembre->nom,
                        'prenom' => $oMembre->prenom,
                        'courriel' => $oMembre->courriel,
                        'mdp' => $oMembre->mdp,
                        'renouvelermdp' => $oMembre->renouvelermdp,
                        'idprofil' => $oMembre->idprofil
                    ]);
                    if ($id_membre > 0) {
                        header("Location: /ProjetWebDeux/PW2-Vino/accueil"); // retour sur la page du profil
                        exit;
                    }
                }
            }
            new Vue(
                'Frontend/vInscription',
                array(
                    'titre'    => 'Ajouter un Membre',
                    'membre'   => $membre,
                    'erreurs'  => $erreurs
                ),
                'Frontend/gabarit-frontend'
            );
        }
    }

}