const modalModifDepense = document.getElementById('modifDepenseModal');
modalModifDepense.addEventListener('show.bs.modal', event => {
	const bouton = event.relatedTarget;

	const idDepense = bouton.getAttribute('data-id');
	const montant = bouton.getAttribute('data-montant');
	const description = bouton.getAttribute('data-description');
	const recurrente = bouton.getAttribute('data-recurrente') === '1';

	modalModifDepense.querySelector('#modifMontantDepense').value = montant;
	modalModifDepense.querySelector('#modifDescriptionDepense').value = description;
	modalModifDepense.querySelector('#modifRecurrenteDepense').checked = recurrente;

	const formulaire = modalModifDepense.querySelector('#formModifDepense');
	formulaire.action = `/modifierDepense/${idDepense}`;
});
