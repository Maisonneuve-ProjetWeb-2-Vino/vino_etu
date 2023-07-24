<?php

/**
 * Classe de l'entité BouteilleCellier
 *
 */
class BouteilleCellier
{
    private $id_bouteille;
    private $id_bouteille_cellier;
    private $id_cellier;
    private $quantite;


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
    public function getId_bouteille()       { return $this->id_bouteille; }
    public function getId_bouteille_cellier()       { return $this->id_bouteille_cellier; }
    public function getId_cellier()       { return $this->id_cellier; }
    public function getQuantite()       { return $this->quantite; }


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
     * Mutateur de la propriété id_bouteille
     * 
     * @param int $id_bouteille
     * @return $this
     */    
    public function setId_bouteille($id_bouteille) {
        unset($this->erreurs['id_bouteille']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $id_bouteille)) {
            $this->erreurs['id_bouteille'] = "Numéro de bouteille incorrect.";
        }
        $this->id_bouteille = $id_bouteille;
        return $this;
    }

    /**
     * Mutateur de la propriété id_bouteille_cellier
     * 
     * @param int $id_bouteille_cellier
     * @return $this
     */    
    public function setId_bouteille_cellier($id_bouteille_cellier) {
        unset($this->erreurs['id_bouteille_cellier']);
        $regExp = '/^[1-9]\d*$/';
        if (!preg_match($regExp, $id_bouteille_cellier)) {
            $this->erreurs['id_bouteille_cellier'] = "Numéro de bouteille de cellier incorrect.";
        }
        $this->id_bouteille_cellier = $id_bouteille_cellier;
        return $this;
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
     * Mutateur de la propriété quantite
     * 
     * @param int $quantite
     * @return $this
     */    
    public function setQuantite($quantite) {
        unset($this->erreurs['quantite']);
        $regExp = '/^[1-9]\d*$/';
        if (!empty($quantite)) {
            if (!preg_match($regExp, $quantite)) {
                $this->erreurs['quantite'] = "Quantité incorrecte";
            }
        }
        $this->quantite = $quantite;
        return $this;
    }
    

}