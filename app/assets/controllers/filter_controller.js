import { Controller } from 'stimulus';
import misc from "../services/misc";

export default class extends Controller {
    static targets = ["card", "seen", "toWatch", "search"];

    //Filtre l'affichage en fonction du terme recherché
    search = e => {
        if (this.cardTargets.length === 0)
            return;
        const slugifiedQuery = misc.slugify(e.target.value, "");
        //On commence par trier les cards qui doivent apparaître et celles qui doivent disparaître
        const filteredCardsVisible = [];
        const filteredCardsHidden = [];
        this.cardTargets.forEach(card => {
            //si on a une correspondance entre la recherche et le titre de la card
            if (misc.slugify(card.dataset.title, "").includes(slugifiedQuery)) {
                //si aucun autre filtre n'est actif, on valide
                if (!this.seenTarget.classList.contains("btn-success") && !this.toWatchTarget.classList.contains("btn-warning"))
                    filteredCardsVisible.push(card);
                //si le filtre vues est actif, on ne garde la card que si elle correspond aussi à ce filtre là
                else if (this.seenTarget.classList.contains("btn-success") && Boolean(card.dataset.isSeen))
                    filteredCardsVisible.push(card);
                //si le filtre à voir est actif, on ne garde la card que si elle correspond aussi à ce filtre là
                else if (this.toWatchTarget.classList.contains("btn-warning") && Boolean(card.dataset.isToWatch))
                    filteredCardsVisible.push(card);
                //sinon, on cache
                else
                    filteredCardsHidden.push(card);
            }
            //si on n'a aucune correspondance
            else
                filteredCardsHidden.push(card);
        });
        //On peut maintenant afficher les cartes concernées et cacher les autres
        filteredCardsVisible.forEach(card => card.classList.remove("d-none"));
        filteredCardsHidden.forEach(card => card.classList.add("d-none"));
    }

    //Filtre l'affichage pour ne laisser que les séries vues
    toggleSeen = () => {
        if (this.cardTargets.length === 0)
            return;
        //on met de côté la valeur actuelle du champ de recherche slugifiée, ça servira ensuite
        const slugifiedQuery = misc.slugify(this.searchTarget.value, "");
        //On commence par trier les cards qui doivent apparaître et celles qui doivent disparaître
        const filteredCardsSeen = [];
        const filteredCardsOthers = [];
        this.cardTargets.forEach(card => {
            if (Boolean(card.dataset.isSeen)) {
                //Si on n'a pas de recherche en cours ou si la card correspond aussi à la recherche en cours, on valide
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    filteredCardsSeen.push(card);
                //sinon, on enlève
                else
                    filteredCardsOthers.push(card);
            }
            else
                filteredCardsOthers.push(card);
        });
        //gestion des boutons de filtrage
        if (this.seenTarget.classList.contains("btn-success")) {
            //si le bouton est déjà actif, on le rend neutre
            this.seenTarget.classList.add("btn-outline-secondary");
            this.seenTarget.classList.remove("btn-success");
            console.log(slugifiedQuery);
            //et on affiche à nouveau toutes les cards sauf celles qui ne correspondent pas à une éventuelle recherche en cours
            filteredCardsSeen.forEach(card => {
                //on ne retire pas la classe hidden s'il y a une recherche en cours et que la card n'y correspond pas
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    card.classList.remove("d-none");
            });
            filteredCardsOthers.forEach(card => {
                //on ne retire pas la classe hidden s'il y a une recherche en cours et que la card n'y correspond pas
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    card.classList.remove("d-none");
            });
        } else {
            //sinon on rend le bouton actif
            this.seenTarget.classList.remove("btn-outline-secondary");
            this.seenTarget.classList.add("btn-success");
            //on affiche les cards seen et on cache toutes les autres
            filteredCardsSeen.forEach(card => card.classList.remove("d-none"));
            filteredCardsOthers.forEach(card => card.classList.add("d-none"));
        }
        //et dans tous les cas on désactive l'autre filtre
        this.toWatchTarget.classList.remove("btn-warning");
        this.toWatchTarget.classList.add("btn-outline-secondary");
    }

    //Filtre l'affichage pour ne laisser que les séries à voir
    toggleToWatch = () => {
        if (this.cardTargets.length === 0)
            return;
        //on met de côté la valeur actuelle du champ de recherche slugifiée, ça servira ensuite
        const slugifiedQuery = misc.slugify(this.searchTarget.value, "");
        //On commence par trier les cards qui doivent apparaître et celles qui doivent disparaître
        const filteredCardsToWatch = [];
        const filteredCardsOthers = [];
        this.cardTargets.forEach(card => {
            if (Boolean(card.dataset.isToWatch)) {
                //Si on n'a pas de recherche en cours ou si la card correspond aussi à la recherche en cours, on valide
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    filteredCardsToWatch.push(card);
                //sinon, on enlève
                else
                    filteredCardsOthers.push(card);
            }
            else
                filteredCardsOthers.push(card);
        });
        //gestion des boutons de filtrage
        if (this.toWatchTarget.classList.contains("btn-warning")) {
            //si le bouton est déjà actif, on le rend neutre
            this.toWatchTarget.classList.add("btn-outline-secondary");
            this.toWatchTarget.classList.remove("btn-warning");
            //et on affiche à nouveau toutes les cards sauf celles qui ne correspondent pas à une éventuelle recherche en cours
            filteredCardsToWatch.forEach(card => {
                //on ne retire pas la classe hidden s'il y a une recherche en cours et que la card n'y correspond pas
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    card.classList.remove("d-none");
            });
            filteredCardsOthers.forEach(card => {
                //on ne retire pas la classe hidden s'il y a une recherche en cours et que la card n'y correspond pas
                if (!slugifiedQuery || misc.slugify(card.dataset.title, "").includes(slugifiedQuery))
                    card.classList.remove("d-none");
            });
        } else {
            //sinon on rend le bouton actif
            this.toWatchTarget.classList.remove("btn-outline-secondary");
            this.toWatchTarget.classList.add("btn-warning");
            //on affiche les cards toWatch et on cache toutes les autres
            filteredCardsToWatch.forEach(card => card.classList.remove("d-none"));
            filteredCardsOthers.forEach(card => card.classList.add("d-none"));
        }
        //et dans tous les cas on désactive l'autre filtre
        this.seenTarget.classList.remove("btn-success");
        this.seenTarget.classList.add("btn-outline-secondary");
    }
}
