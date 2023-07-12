<?php

/**
 * Classe de l'entité Cellier
 *
 */
class Cellier
{
    private $id_cellier;
    private $nom;
    private $id_membre;

    private $erreurs = array();

    /**
     * Constructeur de la classe
     * 
     * @param array $proprietes, tableau associatif des propriétés 
     *
     */ 
    public function __construct($proprietes = []) {
        $t = array_keys($proprietes);
        foreach ($t as $nom_propriete) {
        $this->__set($nom_propriete, $proprietes[$nom_propriete]);
        } 
    }

    /**
     * Accesseur magique d'une propriété de l'objet
     * 
     * @param string $prop, nom de la propriété
     * @return property value
     */     
    public function __get($prop) {
        return $this->$prop;
    }

    // Getters explicites nécessaires au moteur de templates TWIG
    public function getId_cellier()       { return $this->id_cellier; }
    public function getNom()       { return $this->nom; }
    public function getId_membre()       { return $this->id_membre; }


    /**
     * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
     * 
     * @param string $prop, nom de la propriété
     * @param $val, contenu de la propriété à mettre à jour    
     */   
    public function __set($prop, $val) {
        $setProperty = 'set'.ucfirst($prop);
        $this->$setProperty($val);
    }

    /**
     * Mutateur de la propriété id_cellier
     * 
     * @param int $id_cellier
     * @return $this
     */    
    public function setId_cellier($id_cellier) {
        unset($this->erreurs['id_cellier']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $id_cellier)) {
            $this->erreurs['id_cellier'] = "Numéro de cellier incorrect.";
        }
        $this->id_cellier = $id_cellier;
        return $this;
    }

    /**
     * Mutateur de la propriété nom
     * 
     * @param string $nom
     * @return $this
     */    
    public function setNom($nom) {
        unset($this->erreurs['nom']);
        if (empty($nom)) {
            $this->erreurs['nom'] = "Nom de cellier vide.";
        }
        $this->nom = $nom;
        return $this;
    }

    /**
     * Mutateur de la propriété id_membre
     * 
     * @param int $id_membre
     * @return $this
     */    
    public function setId_membre($id_membre) {
        unset($this->erreurs['id_membre']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $id_membre)) {
            $this->erreurs['id_membre'] = "Id de membre incorrect.";
        }
        $this->id_membre = $id_membre;
        return $this;
    }

}