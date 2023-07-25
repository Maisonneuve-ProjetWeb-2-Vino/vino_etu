<?php

/**
 * Classe de l'entité Usagers
 *
 */
class Membres
{
  private $id_membre;
  private $nom;
  private $prenom;
  private $courriel;
  private $mdp;
  private $mdpProvisoire;
  private $date_creation;
  private $idprofil;
 

  const PROFIL_ADMINISTRATEUR = "administrateur";
  const PROFIL_MEMBRE         = "membre";


  private $erreurs = array();

  /**
   * Constructeur de la classe
   * @param array $proprietes, tableau associatif des propriétés 
   *
   */
  public function __construct($proprietes = [])
  {
    $t = array_keys($proprietes);
    foreach ($t as $nom_propriete) {
      $this->__set($nom_propriete, $proprietes[$nom_propriete]);
    }
  }

  /**
   * Accesseur magique d'une propriété de l'objet
   * @param string $prop, nom de la propriété
   * @return property value
   */
  public function __get($prop)
  {
    return $this->$prop;
  }

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getid_membre()
  {
    return $this->id_membre;
  }
  public function getnom()
  {
    return $this->nom;
  }
  public function getprenom()
  {
    return $this->prenom;
  }
  
  public function getcourriel()
  {
    return $this->courriel;
  }

  public function getmdp()
  {
    return $this->mdp;
  }
  
  public function getdate_creation()
  {
    return $this->date_creation;
  }
  public function getidprofil()
  {
    return $this->idprofil;
  }
  public function getErreurs()
  {
    return $this->erreurs;
  }

  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour    
   */
  public function __set($prop, $val)
  {
    $setProperty = 'set' . ucfirst($prop);
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété id_usager 
   * @param int $id_usager
   * @return $this
   */
  public function setid_membre($id_membre)
  {
    $this->id_membre = $id_membre; 
  }

  /**
   * Mutateur de la propriété nom 
   * @param string $nom
   * @return $this
   */
  public function setnom($nom)
  {
    $this->nom = $nom;
    unset($this->erreurs['nom']);
    $nom = trim($nom);
    
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ-]{2,}( [a-zÀ-ÖØ-öø-ÿ-]{2,})*$/i';
    if (!preg_match($regExp, $nom)) {
      $this->erreurs['nom'] = "Au moins 2 caractères alphabétiques pour chaque nom.";
    }
    $this->nom = $nom;
    return $this;
  }

  /**
   * Mutateur de la propriété prenom 
   * @param string $prenom
   * @return $this
   */
  public function setprenom($prenom)
  {
    $this->prenom = $prenom;
    unset($this->erreurs['prenom']);
    $prenom = trim($prenom);
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ-]{2,}( [a-zÀ-ÖØ-öø-ÿ-]{2,})*$/i';
    if (!preg_match($regExp, $prenom)) {
      $this->erreurs['prenom'] = "Au moins 2 caractères alphabétiques pour chaque prénom.";
    }
    $this->prenom = $prenom;
    return $this;
  }
  
  /**
   * Mutateur de la propriété courriel
   * @param string $courriel
   * @return $this
   */
  public function setcourriel($courriel)
  {
    $this->courriel = $courriel;
    unset($this->erreurs['courriel']);
    $courriel = trim($courriel);
    $regExp = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/i';
    if (!preg_match($regExp, $courriel)) {
      $this->erreurs['courriel'] = "Saisissez une adresse mail valide.";
    }
    $this->courriel = $courriel;
    return $this;
  }

  
  /**
   * Mutateur de la propriété mdp
   * @param string $mdp
   * @return $this
   */
  public function setmdp($mdp)
  {
    $this->mdp = $mdp;
    unset($this->erreurs['mdp']);
    $mdp = trim($mdp);
    $regExp = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[%!\:=])([A-Za-z0-9%!\:=]{10,})$/i';
    if (!preg_match($regExp, $mdp)) {
      $this->erreurs['mdp'] = "Saisissez au moins 10 caractères et un caractère parmi %!:= ainsi qu'un chiffre";
    }
    $this->mdp = $mdp;
    return $this;
  }

  
  /**
   * Mutateur de la propriété mdpprovisoire
   * @param string $mdpprovisoire
   * @return $this
   */
  public function setmdpProvisoire($mdpProvisoire)
  {
    $this->mdpProvisoire = $mdpProvisoire;
    unset($this->erreurs['mdpProvisoire']);
    $mdpProvisoire = trim($mdpProvisoire);
    $regExp = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[%!\:=])([A-Za-z0-9%!\:=]{10,})$/i';
    if (!preg_match($regExp, $mdpProvisoire)) {
      $this->erreurs['mdp'] = "Saisissez au moins 10 caracteres et un caractere parmi %!:= ainsi qu'un chiffre";
    }
    $this->mdpProvisoire = $mdpProvisoire;
    return $this;
  }

/**
     * Mutateur de la propriété date_creataion
     * @param string $date_creation
     * @return $this
     */
    public function setdate_creation($date_creation)
    {
        $this->date_creation = $date_creation;
        unset($this->erreurs['date_creation']);
        $date_creation = trim($date_creation);
        $this->date_creation = $date_creation;
        return $this;
    }
  /**
   * Mutateur de la propriété idprofil
   * @param string $idprofil
   * @return $this
   */
  public function setidprofil($idprofil)
  {
    $this->idprofil = $idprofil;
    unset($this->erreurs['idprofil']);
    $this->idprofil = $idprofil;
    return $this;
  }
}
