export default class Fetch{


    boireBouteille(requete, cb) {

        fetch(requete)
            .then(response => {
                if (response.status === 200) {
                  return response.json();
                } else {
                  throw new Error('Erreur');
                }
              })
              .then(response => {
                console.debug(response);
                cb();
              }).catch(error => {
                console.error(error);
              });
    }
}