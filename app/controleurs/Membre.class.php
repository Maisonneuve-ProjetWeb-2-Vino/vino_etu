<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend.
 */

class Membre extends Routeur {
  
  private $oUtilConn;
  private $courriel;

  /**
   * Constructeur qui initialise la propriété oRequetesSQL déclarée dans la classe Routeur.
   * 
   */
  public function __construct() {
    $this->oUtilConn = $_SESSION['oConnexion'] ?? null;
    $this->courriel = $_GET['courriel'] ?? null;
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


/**
   * Afficher page oubli mot de passe.
   * @return void
   */  
  public function oubliMdp() {
    $membre  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
        $membre = [
            'courriel'   => $_POST['courriel']
        ];
        
        $oMembre = new Membres($membre);
        $erreurs = $oMembre->erreurs;
        $courrielDansLaBase= $this->oRequetesSQL->controleMail(['courriel' => $oMembre->courriel]);
        if($courrielDansLaBase === false){
            $erreurs['mauvaisMail'] = "Votre courriel n'existe pas";            
        }
        else{
            
            $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $mdpProvisoire = '';
            $longueurCaracteres = strlen($caracteres);
            // Generer mdp à 25 caracteres
            $randomBytes = random_bytes(25);
            for ($i = 0; $i < 25; $i++) {
                $index = ord($randomBytes[$i]) % $longueurCaracteres;
                $mdpProvisoire .= $caracteres[$index];
            }
            $champs = [
                'mdpProvisoire' => $mdpProvisoire,
                'courriel' => $courrielDansLaBase['courriel']
            ];
            $insererMdpProvisoire = $this->oRequetesSQL->insererMdpProvisoire($champs);
            //envoi du mail
            $to      = $courrielDansLaBase['courriel'] ;
            $subject = 'Oubli de votre mot de passe';
            $messagemail = '<p>Bonjour '. $this->oUtilConn->prenom. ',</p>';
            $messagemail .='<p>Vous avez oublié votre mot de passe pour l\'application Cepacave.com. </p>
            <p>Pour pouvoir le réinitialiser, copiez-collez ce code <strong>'.$mdpProvisoire.'</strong>.</p>
            <p>Toute l\'équipe de Cepacave.com vous remercie et vous souhaite une belle journée.</p>';
            // Pour envoyer un mail HTML, l'en-tête Content-type doit être définie
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset="UTF-8';
            // En-têtes additionnels
            $headers[] = 'From: Cepacave.com <rachelcrevoisier@gmail.com>';
            // Envoi
            mail($to, $subject, $messagemail, implode("\r\n", $headers));
            header("Location: genererMdp"); // envoi sur la page mdp
            exit;
        
    };
            
        }
    
     new Vue(
            '/Frontend/vOubliMotDePasse',
            array(

                'titre'  => 'Oubli du mot de passe',
                'erreurs' => $erreurs
            ),
            'Frontend/gabarit-vide'
        );
    }

/**
 * Générer un mot de passe aléatoire et envoyer un mail pour modif mdp
 * @return void
 */  

function genererMdp() {
    $message = '';
    $membre  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
        $membre = [
            'mdp'  => $_POST['mdp'],
        ];
       
        $oMembre = new Membres($membre);
        $erreurs = $oMembre->erreurs;
        //verifier si le mdp provisoire est bien dans la base avec ce mail
        $validMdpProvisoire = $this->oRequetesSQL->controleMdpProvisoire($_POST['mdpProvisoire']);
         
        if ($validMdpProvisoire === false) { 
          $erreurs['mdpProvisoire'] = "Ce mot de passe provisoire n'est pas le bon.";
        }
    
        if($oMembre->mdp !== $_POST['renouvelermdp']){
            $erreurs['renouvelermdp'] = "Votre mot de passe et la confirmation ne correspondent pas";
        }
        
        if (count($erreurs) === 0) {
            $membreValide = $this->oRequetesSQL->modifierMotDePasse([
                'mdp' => password_hash($oMembre->mdp, PASSWORD_BCRYPT),
                'id_membre' => $validMdpProvisoire['id_membre']
            ]);
             
            if ($membreValide !== false) { 
                $membre = [
                'id_membre'  => $validMdpProvisoire['id_membre'],
                'nom'  => $validMdpProvisoire['nom'],
                'prenom'  => $validMdpProvisoire['prenom'],
                'mdp'  => $validMdpProvisoire['mdp'],
                'date_creation'  => $validMdpProvisoire['date_creation'],
                'courriel'  => $validMdpProvisoire['courriel'],
                'idprofil'  => $validMdpProvisoire['idprofil']
                ];
       
            $oMembre = new Membres($membre);
            $_SESSION['oConnexion'] = new Membres($membre);
            $supMdpTemporaire = $this->oRequetesSQL->supMdpTemporaire([
                'mdpProvisoire' => '',
                'id_membre' => $validMdpProvisoire['id_membre']
            ]);
            header("Location: profil"); // retour sur la page du profil
        exit;
        
    }  
        }}
    new Vue(
      'Frontend/vGenererMdp',
      array(
        'oUtilConn' => $this->oUtilConn,
        'titre'     => "Générer un nouveau mot de passe",
        'message' => $message,
        'erreurs' => $erreurs
      ),
      'Frontend/gabarit-vide'
    );
}

}
