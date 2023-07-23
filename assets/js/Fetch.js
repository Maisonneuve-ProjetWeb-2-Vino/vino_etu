export default class Fetch{

    /**
     * Fait la requête pour diminuer la quantité d'une bouteille de 1.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    boireBouteille(param, cb) {
        let requete = new Request("cellier?action=b", {method: 'POST', body:JSON.stringify(param)});
        fetch(requete)
          .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw new Error('Erreur au retour de boireBouteille');
              }
            })
            .then(response => {
              cb();
            }).catch(error => {
              console.error(error);
            });
    }

    /**
     * Fait la requête pour augmenter la quantité d'une bouteille de 1.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    ajouterBouteille(param, cb) {
      let requete = new Request("cellier?action=a", {method: 'POST', body:JSON.stringify(param)});
      fetch(requete)
        .then(response => {
          if (response.status === 200) {
            return response.json();
          } else {
            throw new Error('Erreur');
          }
        })
        .then(response => {
          cb();
        }).catch(error => {
          console.error(error);
        });
                          
    }

    /**
     * Fait la requête pour rechercher une bouteille pour ajout.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    rechercherBouteille(param, cb) {
      let requete = new Request("cellier?action=c", {method: 'POST', body:JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
              return response.json();
            } else {
              throw new Error('Erreur');
            }
          })
          .then(response => {
            console.log(response);
            cb(response);
          }).catch(error => {
            console.error(error);
          });
    }

    /**
     * Fait la requête pour obtenir les détails d'une bouteille.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    obtenirDetailsBouteille(param, cb) {
      let requete = new Request("cellier?action=r", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
              return response.json();
            } else {
              throw new Error('Erreur');
            }
          })
          .then(response => {
            console.log(response)
            cb(response);
          }).catch(error => {
            console.error(error);
          });
    }

    /**
     * Fait la requête pour vérifier si une bouteille existe déjà dans un cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    verifierDuplicationBouteille(param, cb) {
      let requete = new Request("cellier?action=v", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete) 
        .then(response => {
            if (response.status === 200) {
              return response.json();
            } else {
              throw new Error('Erreur');
            }
          })
          .then(response => {
            console.log(response);
            cb(response);
          }).catch(error => {
            console.error(error);
          });
    }

    /**
     * Fait la requête pour ajouter une nouvelle bouteille au cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    ajouterNouvelleBouteille(param, cb) {
      let requete = new Request("cellier?action=n", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
                return response.json();
            } else {
                throw new Error('Erreur');
            }
            })
            .then(response => {
              console.log(response);
              cb();
            }).catch(error => {
            console.error(error);
            });
    }

    /**
     * Fait la requête pour ajouter une nouvelle bouteille personnalisée au catalogue et cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    ajouterBouteillePersonnalisee(param, cb) {
      let requete = new Request("cellier?action=e", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
              return response.json();
            } else {
              throw new Error('Erreur');
            }
          })
          .then(response => {
            console.log(response);
            cb();
          
          }).catch(error => {
            console.error(error);
          });
    }

    /**
     * Modifie une bouteille de la SAQ dans le cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    modifierBouteille(param, cb) {
      let requete = new Request("cellier?action=m", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
            return response.json();
            } else {
            throw new Error('Erreur');
            }
        })
        .then(response => {
            console.log(response);
            cb();
        }).catch(error => {
            console.error(error);
        });
    }

    /**
     * Fait la requête en ajout de bouteille personnalisée au catalogue et cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    modifierBouteillePersonnalisee(param, cb) {
      let requete = new Request("cellier?action=m", {method: 'POST', body: JSON.stringify(param)});
      fetch(requete)
        .then(response => {
            if (response.status === 200) {
              return response.json();
            } else {
              throw new Error('Erreur');
            }
          })
          .then(response => {
            console.log(response);
            cb();
          
          }).catch(error => {
            console.error(error);
          });
    }

    /**
     * Fait la requête en modification de cellier.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    modifierCellier(param, cb) {
      let requete = new Request("cellier?action=q", {method: 'POST', body: JSON.stringify(param)});
        fetch(requete)
          .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw new Error('Erreur');
              }
            })
            .then(response => {
              console.log(response);
              cb();
            
            }).catch(error => {
              console.error(error);
            });
    }

    /**
     * Fait la requête pour la vérification de nom de cellier déjà existant.
     * @param {Object} param Paramètres de la requête.
     * @param {function} cb Fonction de rappel.
     */
    validerNomCellier(param, cb) {
      let requete = new Request("cellier?action=u", {method: 'POST', body: JSON.stringify(param)});
        fetch(requete)
          .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw new Error('Erreur');
              }
            })
            .then(response => {
              console.log(response);
              cb(response);
            
            }).catch(error => {
              console.error(error);
            });
    }

    filtrerBouteilles(param, cb) {
      let requete = new Request("recherche", {method: 'POST', body: JSON.stringify(param)});
        fetch(requete)
          .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw new Error('Erreur');
              }
            })
            .then(response => {
              console.log(response);
              cb(response);
            
            }).catch(error => {
              console.error(error);
            });
    }
}
