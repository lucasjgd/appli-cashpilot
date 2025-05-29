const modifCategorieModal = document.getElementById('modifCategorieModal');
modifCategorieModal.addEventListener('show.bs.modal', event => {
	const button = event.relatedTarget;

	const livretId = button.getAttribute('data-livret');
	const categId = button.getAttribute('data-categ');
	const budget = button.getAttribute('data-budget');

	modifCategorieModal.querySelector('#modifCategorieLivretId').value = livretId;
	modifCategorieModal.querySelector('#modifCategorieCategId').value = categId;
	modifCategorieModal.querySelector('#modifBudgetCategorie').value = budget;

	const form = modifCategorieModal.querySelector('#formModifCategorie');
	form.action = `/modifierCategorie/${livretId}/${categId}`;
});
