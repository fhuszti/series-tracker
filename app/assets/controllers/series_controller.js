import { Controller } from 'stimulus';
import { Toast } from "bootstrap";

export default class extends Controller {
    static targets = ["card", "seen", "toWatch"];

    static values = {
        updateSeriesUrl: String,
    };

    //Met à jour une série suite à un click sur le bouton "Vue"
    toggleSeen = async () => {
        //si la série était déjà seen, alors on vient de unseen. Sinon, on vient de seen
        const isNowSeen = !Boolean(this.cardTarget.dataset.isSeen);
        //On met à jour l'entité
        const response = await fetch(this.updateSeriesUrlValue, {
            method: 'PUT',
            body: JSON.stringify({ setIsSeen: isNowSeen, setIsToWatch: false }),
            headers: { 'Content-Type': 'application/json' },
        });

        //on prépare des variables qui seront utiles par la suite
        const message = await response.json();
        const toastBodyElmt = document.getElementById('toastBody');
        toastBodyElmt.innerText = message;

        const toastElmt = document.getElementById('liveToast');
        const toastTitleElmt = document.getElementById('toastTitle');

        //On agit maintenant en fonction du retour du Controller
        if (response.status === 200) {
            //On met à jour l'affichage des boutons
            if (isNowSeen) {
                this.seenTarget.classList.remove("btn-outline-secondary");
                this.seenTarget.classList.add("btn-success");
            } else {
                this.seenTarget.classList.add("btn-outline-secondary");
                this.seenTarget.classList.remove("btn-success");
            }
            this.toWatchTarget.classList.add("btn-outline-secondary");
            this.toWatchTarget.classList.remove("btn-warning");
            //ainsi que les meta-data de la card
            this.cardTarget.dataset.isSeen = isNowSeen ? "1" : "";
            this.cardTarget.dataset.isToWatch = "";

            //et on formate le toast pour indiquer que tout est bon
            toastElmt.classList.add("toast-success");
            toastTitleElmt.innerText = "Succès";
        }
        else {
            //Erreur, on formate juste le toast
            toastElmt.classList.add("toast-danger");
            toastTitleElmt.innerText = "Erreur";
        }
        //puis on déclenche le toast
        const toast = new Toast(toastElmt);
        toast.show();
    }

    //Met à jour une série suite à un click sur le bouton "à voir"
    toggleToWatch = async () => {
        //si la série était déjà toWatch, alors on vient de unToWatch. Sinon, on vient de toWatch
        const isNowToWatch = !Boolean(this.cardTarget.dataset.isToWatch);
        //On met à jour l'entité
        const response = await fetch(this.updateSeriesUrlValue, {
            method: 'PUT',
            body: JSON.stringify({ setIsSeen: false, setIsToWatch: isNowToWatch }),
            headers: { 'Content-Type': 'application/json' },
        });

        //on prépare des variables qui seront utiles par la suite
        const message = await response.json();
        const toastBodyElmt = document.getElementById('toastBody');
        toastBodyElmt.innerText = message;

        const toastElmt = document.getElementById('liveToast');
        const toastTitleElmt = document.getElementById('toastTitle');

        //On agit maintenant en fonction du retour du Controller
        if (response.status === 200) {
            //On met à jour l'affichage des boutons
            if (isNowToWatch) {
                this.toWatchTarget.classList.remove("btn-outline-secondary");
                this.toWatchTarget.classList.add("btn-warning");
            } else {
                this.toWatchTarget.classList.add("btn-outline-secondary");
                this.toWatchTarget.classList.remove("btn-warning");
            }
            this.seenTarget.classList.add("btn-outline-secondary");
            this.seenTarget.classList.remove("btn-success");
            //ainsi que les meta-data de la card
            this.cardTarget.dataset.isSeen = "";
            this.cardTarget.dataset.isToWatch = isNowToWatch ? "1" : "";

            //et on formate le toast pour indiquer que tout est bon
            toastElmt.classList.add("toast-success");
            toastTitleElmt.innerText = "Succès";
        }
        else {
            //Erreur, on formate juste le toast
            toastElmt.classList.add("toast-danger");
            toastTitleElmt.innerText = "Erreur";
        }
        //puis on déclenche le toast
        const toast = new Toast(toastElmt);
        toast.show();
    }
}
