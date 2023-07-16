<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Membre extends Routeur {
  private $id_membre;
  private $oUtilConn;

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->oUtilConn = $_SESSION['oConnexion'] ?? null;
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

                'titre'  => 'Se connecter'
            ),
            'Frontend/gabarit-vide'
        );
}

/**
     * Connecter un membre
     */
public function connexion() {
    
    $membre = $this->oRequetesSQL->connecter($_POST);
    if ($membre !== false) {
        $_SESSION['oConnexion'] = new Membres($membre);
        
        // Rediriger l'utilisateur vers une page après la connexion réussie
        header("Location: cellier"); // retour sur la page du profil
                            exit;
    }
    else{
         $erreurs['connexion'] = "Votre courriel ou votre mot de passe ne sont pas bons.";
    }
    new Vue(
                'Frontend/vConnexion',
                array(
                
                    'titre'    => 'Se connecter',
                    'membre'   => $membre,
                    'erreurs'  => $erreurs
                ),
                'Frontend/gabarit-vide'
            );
    
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
            'Frontend/gabarit-vide'
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
            $membre = [
                'nom'    => $_POST['nom'],
                'prenom'  => $_POST['prenom'],
                'courriel'   => $_POST['courriel'],
                'mdp'  => $_POST['mdp'],
                'idprofil'  => $_POST['idprofil']
            ];
                // retour de saisie du formulaire
               
                $oMembre = new Membres($membre); // création d'un objet membre pour contrôler la saisie
                $erreurs = $oMembre->erreurs;
                if($_POST['mdp'] !== $_POST['renouvelermdp']){
                    $erreurs['renouvelermdp'] = "Votre mot de passe et la confirmation ne correspondent pas";
                }
                $courrielendouble= $this->oRequetesSQL->controleMail(['courriel' => $oMembre->courriel]);
                if($courrielendouble == true){
                    $erreurs['courriel'] = "Votre courriel existe déjà dans la base.";
                }
                    if (count($erreurs) === 0) {
                        $id_membre = $this->oRequetesSQL->inscriptionMembre([
                            'nom'    => $oMembre->nom,
                            'prenom' => $oMembre->prenom,
                            'courriel' => $oMembre->courriel,
                            'mdp' => $oMembre->mdp,
                            'renouvelermdp' => $oMembre->mdp,
                            'idprofil' => $oMembre->idprofil
                        ]);
                        if ($id_membre > 0) {
                            header("Location: accueil"); // retour sur la page du profil
                            exit;
                        }
                    }
                
            
            new Vue(
                'Frontend/vInscription',
                array(
                    'titre'    => 'Ajouter un Membre',
                    'membre'   => $membre,
                    'erreurs'  => $erreurs
                ),
                'Frontend/gabarit-vide'
            );
        }
    }


    /**
     * Voir les informations d'un membre
     */
    public function profil()
    {
        $membre = false;
        if (!is_null($this->oUtilConn->id_membre)) {
            $membre = $this->oRequetesSQL->infoMembre($this->id_membre);
            if (!$membre) throw new Exception("Ce membre n'existe pas");
               
            new Vue(
                'Frontend/vProfil',
                array(
                    
                    'oUtilConn' => $this->oUtilConn,
                    'titre' => 'Fiche d\'un membre',
                    'membre' => $membre
                ),
                'Frontend/gabarit-frontend'
            );
       
        }
         else{
           header("Location: connecter"); 
        }
    }

}