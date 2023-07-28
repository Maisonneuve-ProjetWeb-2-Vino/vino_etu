<?php

/**
 * Classe de l'entité Commentaire
 *
 */
class Commentaire
{
    private $commentaire;

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
    public function getCommentaire()       { return $this->commentaire; }

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
     * Mutateur de la propriété commentaire
     * 
     * @param string $commentaire
     * @return $this
     */    
    public function setCommentaire($commentaire) {
        unset($this->erreurs['commentaire']);
        if (empty($commentaire)) {
            $this->erreurs['commentaire'] = "Commentaire vide.";
        }
        $this->commentaire = $commentaire;
        return $this;
    }
}