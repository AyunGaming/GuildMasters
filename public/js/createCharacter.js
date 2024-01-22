let currentStep = 1;

function nextStep(step) {
	// Récupérer le formulaire
	const form = document.getElementById('characterForm');

	// Vérifier la validité du formulaire
	if (form.checkValidity()) {
		// Le formulaire est valide, passer à la prochaine étape
		document.getElementById(`step${currentStep}`).style.display = 'none';
		document.getElementById(`step${step}`).style.display = 'block';
		updateConfirmation();
		currentStep = step;
	} else {
		// Le formulaire n'est pas valide, avertir l'utilisateur (ou prendre d'autres mesures nécessaires)
		alert("Veuillez remplir tous les champs obligatoires avant de passer à la prochaine étape.");
	}
}

function prevStep(step) {
	document.getElementById(`step${currentStep}`).style.display = 'none';
	document.getElementById(`step${step}`).style.display = 'block';
	updateConfirmation();
	currentStep = step;
}

function updateConfirmation() {
	// Mettez à jour le contenu du récapitulatif
	const confirmationSummary = document.getElementById('confirmationSummary');
	confirmationSummary.innerHTML = generateSummary();
}

function generateSummary() {
	const characterImageInput = document.getElementById('characterImage');
	const characterColorInput = document.getElementById('characterColor');
	const characterRarityInput = document.getElementById('characterRarity');
	const characterTagsInput = document.getElementById('characterTags');
	let characterImage = "";

	// Générez le récapitulatif en fonction des valeurs du formulaire
	if (characterImageInput.files.length > 0) {
		characterImage =  characterImageInput.files[0].name;
	} else {
		characterImage = 'Aucun fichier sélectionné';
	}

	const characterId = document.getElementById('characterID').value;
	const characterName = document.getElementById('characterName').value;
	const characterRarity = characterRarityInput.options[characterRarityInput.selectedIndex].text;
	const characterLL = (document.getElementById('characterLL').value === 'on') ? 'Oui' : 'Non'
	const characterColor = characterColorInput.options[characterColorInput.selectedIndex].text;
	const characterTags = Array.from(characterTagsInput.selectedOptions).map(option => option.text);


	// Créez le récapitulatif
	const summary = `
	<p><strong>ID du personnage:</strong> ${characterId}</p>
	<p><strong>Image du personnage:</strong> ${characterImage}</p>
    <p><strong>Nom du personnage:</strong> ${characterName}</p>
    <p><strong>Rareté du personnage:</strong> ${characterRarity}</p>
    <p><strong>Le personnage est-il un legends limited ?</strong> ${characterLL}</p>
    <p><strong>Couleur du personnage:</strong> ${characterColor}</p>
    <p><strong>Tags du personnage:</strong> ${characterTags.join(', ')}</p>
    <!-- Ajoutez ici d'autres lignes du récapitulatif -->
  `;

	return summary;
}

$('#characterTags').select2({
	theme: "bootstrap-5",
	placeholder: 'Sélectionner les tags',
	language: {
		noResults: () => 'Aucun tag trouvé'
	},
	closeOnSelect: false,
});