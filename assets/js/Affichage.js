export default class Affichage{

    /**
     * 
     * @param {Object | Array} data - Données 
     * @param {String} gabarit 
     * @param {HTMLElement} domParent 
     * @returns {String} - Chaine HTML avec les données
     */
    static afficher(data, gabarit, domParent){
        let chaineHTML = "";         
        if(Array.isArray(data)){
            data.forEach(unElement=>{
                chaineHTML += Affichage.#remplacement(unElement, gabarit);        
            })
        }
        else {
            chaineHTML += Affichage.#remplacement(data, gabarit);        
        }
        //chaineHTML = Affichage.#remplacement(data, gabarit);
        domParent.innerHTML = chaineHTML;
        return chaineHTML;
    }

    static #remplacement(unElement, sGabarit){
        for(let prop in unElement){
            let regExp = new RegExp("\\[\\[\\[\\s*"+prop+"\\s*\\]\\]\\]", "g");
            sGabarit = sGabarit.replace(regExp, unElement[prop]);
        }
        return sGabarit;
    }
}