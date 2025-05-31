const modifModal = document.getElementById('modifCategorieModal');
modifModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const libelle = button.getAttribute('data-libelle');

    document.getElementById('modif-libelle').value = libelle;
    document.getElementById('formModifCategorie').action = '/gestion_categorie/modif/' + id;
});