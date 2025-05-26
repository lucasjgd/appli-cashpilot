const modifModal = document.getElementById('modifLivretModal');
modifModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const livretId = button.getAttribute('data-id');
    const nom = button.getAttribute('data-nom');

    const inputId = modifModal.querySelector('#modifLivretId');
    const inputNom = modifModal.querySelector('#modifNomLivret');
    const form = modifModal.querySelector('#formModifLivret');

    inputId.value = livretId;
    inputNom.value = nom;

    form.action = `/modifierLivret/${livretId}`;
});