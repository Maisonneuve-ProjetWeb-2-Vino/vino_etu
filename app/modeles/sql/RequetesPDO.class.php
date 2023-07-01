<?php

/**
 * Classe des requêtes PDO 
 *
 */
class RequetesPDO {

  protected $sql;

  // Pour les transactions seulement, utilisation avec TransactionCUDLigne()
  protected $sql_multiple = [];

  const UNE_SEULE_LIGNE = true;

  /**
   * Récupération d'une ou plusieurs ligne de la requête $sql
   * @param array   $params paramètres de la requête préparée
   * @param boolean $uneSeuleLigne true si une seule ligne à récupérer false sinon 
   * @return array|false false si aucune ligne retournée par fetch
   */ 
  public function getLignes($params = [], $uneSeuleLigne = false) {
    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    foreach ($params as $nomParam => $valParam) $oPDOStatement->bindValue(':'.$nomParam, $valParam);
    $oPDOStatement->execute();
    $result = $uneSeuleLigne ? $oPDOStatement->fetch(PDO::FETCH_ASSOC) : $oPDOStatement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  /**
   * Requête $sql de Création Update ou Delete d'une ligne
   * @param array   $params paramètres de la requête préparée
   * @return boolean|string chaîne contenant lastInsertId s'il est > 0
   */ 
  public function CUDLigne($params = []) {
    $sPDO = SingletonPDO::getInstance();

    $oPDOStatement = $sPDO->prepare($this->sql);
    foreach ($params as $nomParam => $valParam) $oPDOStatement->bindValue(':'.$nomParam, $valParam);
    $oPDOStatement->execute();
    if ($oPDOStatement->rowCount() <= 0) return false;
    if ($sPDO->lastInsertId() > 0)       return $sPDO->lastInsertId();
    return true;
  }

  /**
   * Requête $sq de suppression de plusieurs tables, avec transaction. 
   * @param array  $params tableau de tableau des paramètres de la requête préparée
   * @return boolean retourne vrai, lance une exception si problème
   */ 
  public function TransactionCUDLigne($aParams) {
    $sPDO = SingletonPDO::getInstance();

    if (!$sPDO->beginTransaction()) return false;
    $i = 0;
    try {

      foreach ($this->sql_multiple as $sql) {
        $oPDOStatement = $sPDO->prepare($sql);
        $params = $aParams[$i];
        foreach ($params as $nomParam => $valParam) $oPDOStatement->bindValue(':'.$nomParam, $valParam);
        $oPDOStatement->execute();
        $i++;
      }

    }
    catch (PDOException $e) {
      $sPDO->rollBack();
      $message = $e->getMessage();
      throw new Exception($message);
    }
    $sPDO->commit();
    return true;
  }
}