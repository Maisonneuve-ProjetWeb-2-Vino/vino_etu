export default class Fetch{


    boireBouteille(requete, cb) {

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


    ajouterBouteille(requete, cb) {
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

    rechercherBouteille(requete, cb) {
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

    obtenirDetailsBouteille(requete, cb) {
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

    verifierDuplicationBouteille(requete, cb) {
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

    ajouterNouvelleBouteille(requete, cb) {
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

    ajouterBouteillePersonnalisee(requete, cb) {
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
}