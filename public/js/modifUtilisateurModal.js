document.addEventListener('DOMContentLoaded', function () {
    const modifModal = document.getElementById('modifUtilisateurModal');
    modifModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const nom = button.getAttribute('data-nom');
        const mail = button.getAttribute('data-mail');
        const role = button.getAttribute('data-role');

        modifModal.querySelector('#modif-id').value = id;
        modifModal.querySelector('#modif-nom').value = nom;
        modifModal.querySelector('#modif-mail').value = mail;
        modifModal.querySelector('#modif-role').value = role;

        const form = document.getElementById('modifUtilisateurForm');
        form.action = `/gestion_utilisateur/modif/${id}`;
    });
});