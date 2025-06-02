document.addEventListener('DOMContentLoaded', function () {
	const modifDepenseModal = document.getElementById('modifDepenseModal');

	modifDepenseModal.addEventListener('show.bs.modal', event => {
		const button = event.relatedTarget;

		const id = button.getAttribute('data-id');
		const montant = button.getAttribute('data-montant');
		const description = button.getAttribute('data-description');
		const isRecurrente = button.getAttribute('data-recurrente') === 'true';
		const frequence = button.getAttribute('data-frequence');
		const dateDebut = button.getAttribute('data-date-debut');
		const dateFin = button.getAttribute('data-date-fin');

		document.getElementById('modifMontantDepense').value = montant;
		document.getElementById('modifDescriptionDepense').value = description;
		document.getElementById('modifRecurrenteDepense').checked = isRecurrente;

		const recurrenceFields = document.getElementById('recurrenceFields');
		if (isRecurrente) {
			const frequenceSelect = document.getElementById('frequenceModif');
			frequenceSelect.value = frequence;
			document.getElementById('date_debut_modif').value = dateDebut;
			document.getElementById('date_fin_modif').value = dateFin || '';

			recurrenceFields.style.display = 'block';
		} else {
			recurrenceFields.style.display = 'none';
		}

		const form = document.getElementById('formModifDepense');
		form.action = '/modifierDepense/' + id;
	});

	const checkbox = document.getElementById('modifRecurrenteDepense');
	checkbox.addEventListener('change', function () {
		document.getElementById('recurrenceFields').style.display = this.checked ? 'block' : 'none';
	});
});
