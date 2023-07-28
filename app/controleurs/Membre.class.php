<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Membre extends Routeur {
  
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
   
        $erreurs = [];
    $membre = $this->oRequetesSQL->connecter($_POST['courriel']);
    
    if ($membre !== false AND password_verify($_POST['mdp'], $membre['mdp'])) {
        
        $_SESSION['oConnexion'] = new Membres($membre);
        
        // Rediriger l'utilisateur vers une page après la connexion réussie
        header("Location: profil"); // retour sur la page du profil
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
     * Déconnecter un membre
     */
    public function deconnecter()
    {
        session_destroy();
        header("Location: connecter"); 
        
    }


/**
     * Afficher la page inscription
     */
    public function inscription()
    {
         if (is_null($this->oUtilConn)) {
        new Vue(
            '/Frontend/vInscription',
            array(

                'titre'  => 'Inscription membre'
            ),
            'Frontend/gabarit-vide'
        );
         }
        else{
        header("Location: accueil"); 
    }
}
    /**
     * Inscription d'un nouveau membre
     */

    public function validationInscription()
    {
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
            $oMembre = new Membres($membre);
            $erreurs = $oMembre->erreurs;
                if($oMembre->mdp !== $_POST['renouvelermdp']){
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
                            'mdp' => password_hash($oMembre->mdp, PASSWORD_BCRYPT),
                            'idprofil' => $oMembre->idprofil
                        ]);
                        if ($id_membre > 0) {
                            $membre = $this->oRequetesSQL->connecter($_POST['courriel']);
                            if ($membre !== false AND password_verify($_POST['mdp'], $membre['mdp'])) {
                            $_SESSION['oConnexion'] = new Membres($membre);
                            header("Location: profil"); // retour sur la page du profil
                            exit;
                            }
                        }
                    }
            
        new Vue(
            'Frontend/vInscription',
            array(
                'titre'  => 'Inscription membre',
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
            $membre = $this->oRequetesSQL->infoMembre($this->oUtilConn->id_membre);
            //$nombreCellier = $this->oRequetesSQL->nombreCellierParMembre($this->oUtilConn->id_membre);
            //$nombreBouteille = $this->oRequetesSQL->nombreBouteilleParMembre($this->oUtilConn->id_membre);
            
            $message = "Voulez-vous vraiment supprimer votre compte ainsi que tout son contenu ?";
            
            new Vue(
                'Frontend/vProfil',
                array(
                    
                    'oUtilConn' => $this->oUtilConn,
                    'titre' => 'Profil',
                    'membre' => $membre,
                    'message' => $message,
                    
                    //'nombreCellier' => $nombreCellier
                ),
                'Frontend/gabarit-frontend'
            );
       
        }
         else{
           header("Location: connecter"); 
        }
    }


    /**
     * Modifier les coordonnees du membre
     */
    public function modifierMembre()
    {
    if (count($_POST) !== 0) {
        $membre =  [
            'nom'  => $_POST['nom'],
            'prenom'    => $_POST['prenom'],
            'courriel'  => $_POST['courriel'],
            'id_membre'  => $_POST['id_membre']
        ];
        $oMembre = new Membres($membre);
        $erreurs = $oMembre->erreurs;
            if($_POST['courriel'] != $this->oUtilConn->courriel){
            $courrielendouble= $this->oRequetesSQL->controleMail(['courriel' => $oMembre->courriel]);
                if($courrielendouble == true){
                    $erreurs['courriel'] = "Ce courriel est déjà utilisé";
                }
            }
            if (count($erreurs) === 0) {
                $this->oRequetesSQL->modifiermembre([
                    'nom'    => $oMembre->nom,
                    'prenom' => $oMembre->prenom,
                    'courriel' => $oMembre->courriel,
                    'id_membre' => $oMembre->id_membre
                ]);   
                header(
                    "Location: profil"
                ); // retour sur la page du profil
                exit;
                }
    } else {
    // chargement initial du formulaire  
    // initialisation des champs dans la vue formulaire avec les données SQL de cet utilisateur  
        $membre  = $this->oRequetesSQL->infoMembre($this->oUtilConn->id_membre);    
        $erreurs = [];
    }
           
    new Vue(
      'Frontend/vModifierMembre',
      array(
        'oUtilConn' => $this->oUtilConn,
        'titre'     => "Modifier un membre",
        'membre'    => $membre,
        'erreurs'   => $erreurs
      ),
      'Frontend/gabarit-frontend'
    );
  }


      /**
     * Modifier le mot de passe du membre
     */
    public function modifierMotDePasse()
    {
        $membre  = [];
        $erreurs = [];
        if (count($_POST) !== 0) {
            $membre = [
                'mdp'  => $_POST['mdp'],
                'id_membre'  => $_POST['id_membre']
            ];
            $oMembre = new Membres($membre);
            $erreurs = $oMembre->erreurs;
            if (!password_verify($_POST['ancienmdp'], $this->oUtilConn->mdp)) {
                $erreurs['ancienmdp'] = "Votre mot de passe n'est pas le bon";
            }
            if($oMembre->mdp !== $_POST['renouvelermdp']){
                $erreurs['renouvelermdp'] = "Votre mot de passe et la confirmation ne correspondent pas";
            }
            if (count($erreurs) === 0) {
                $id_membre = $this->oRequetesSQL->modifierMotDePasse([
                    'mdp' => password_hash($oMembre->mdp, PASSWORD_BCRYPT),
                    'id_membre' => $oMembre->id_membre
                ]);
                if ($id_membre > 0) {
                    $_SESSION['oConnexion'] = new Membres($membre);
                    header("Location: profil"); // retour sur la page du profil
                    exit;
                }
            }
        }
        
    new Vue(
      'Frontend/vModifierMotDePasse',
      array(
        'oUtilConn' => $this->oUtilConn,
        'titre'     => "Modifier le mot de passe",
        'membre'    => $membre,
        'erreurs'   => $erreurs
      ),
      'Frontend/gabarit-frontend'
    );
  }

/**
   * Supprime un membre ainsi que ses celliers, bouteilles, commentaires, notes, liste achat.
   * 
   * @throws Exception Si la requête ne contient pas l'id du membre, ou s'il y a 
   *                   une erreur lors de la suppression.
   * @return void
   */
  public function supprimerMembre() {

    $membre_id = $this->oUtilConn->id_membre;

    if (!$this->oUtilConn->id_membre) {
      throw new Exception(self::ERROR_BAD_REQUEST);
    }

    $suppression = $this->oRequetesSQL->supprimerMembre($membre_id);

    if (!$suppression) {
      throw new Exception("La suppression de votre compte n'a pas fonctionné");
    }else{
        $deconnexion = $this->deconnecter($membre_id);
    }

    // Redirection vers la connexion
    header('Location: inscription');
  }

}
