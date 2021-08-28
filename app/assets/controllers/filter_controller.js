import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ["card", "seen", "toWatch"];

    //Filtre l'affichage pour ne laisser que les séries vues
    toggleSeen = () => {
        if (this.cardTargets.length === 0)
            return;
        const filteredCardsSeen = this.cardTargets.filter(card => Boolean(card.dataset.isSeen));
        const filteredCardsOthers = this.cardTargets.filter(card => !Boolean(card.dataset.isSeen));
        //gestion des boutons de filtrage
        if (this.seenTarget.classList.contains("btn-success")) {
            //si le bouton est déjà actif, on le rend neutre
            this.seenTarget.classList.add("btn-outline-secondary");
            this.seenTarget.classList.remove("btn-success");
            //et on affiche à nouveau toutes les cards
            filteredCardsSeen.forEach(card => card.classList.remove("d-none"));
            filteredCardsOthers.forEach(card => card.classList.remove("d-none"));
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
        const filteredCardsToWatch = this.cardTargets.filter(card => !!card.dataset.isToWatch);
        const filteredCardsOthers = this.cardTargets.filter(card => !Boolean(card.dataset.isToWatch));
        //gestion des boutons de filtrage
        if (this.toWatchTarget.classList.contains("btn-warning")) {
            //si le bouton est déjà actif, on le rend neutre
            this.toWatchTarget.classList.add("btn-outline-secondary");
            this.toWatchTarget.classList.remove("btn-warning");
            //et on affiche à nouveau toutes les cards
            filteredCardsToWatch.forEach(card => card.classList.remove("d-none"));
            filteredCardsOthers.forEach(card => card.classList.remove("d-none"));
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
