export default class Fetch{


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