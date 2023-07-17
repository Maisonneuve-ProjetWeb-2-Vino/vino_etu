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

    obtenirDetailsBouteille(requete, bouteille, cb) {
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
            cb(bouteille, response);
          }).catch(error => {
            console.error(error);
          });
    }

    verifierDuplicationBouteille(requete, cb) {
      fetch(requeteVerification) 
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

    ajouterNouvelleBouteille() {
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
}