function getUpdateUserInfos(image, rarity, lf, name, color, tags) {
	document.getElementById('oldId').value = image
	document.getElementById('updateId').value = image;
	document.getElementById('updateName').value = name;
	document.getElementById('updateRarity').value = rarity;
	document.getElementById('updateIsLfCheckbox').checked = lf === 1;
	document.getElementById('updateColor').value = color;

	let s2 = $('#updateTags').select2({
		theme: "bootstrap-5",
		placeholder: 'Sélectionner les tags',
		language: {
			noResults: () => 'Aucun tag trouvé'
		},
		closeOnSelect: false,
	});

	let tagArray = tags.split(', ');
	tagArray.forEach(function (e){
		if(!s2.find('option:contains('+ e +')').length){
			s2.append($('<option>').text(e));
		}
	})

	s2.val(tagArray).trigger("change");
}


function getDeleteUserInfos(image,name){
	document.getElementById('deleteConfirm').innerText = `Voulez-vous supprimer définitivement le personnage: ${image}: ${name} ?`;
	document.getElementById('characterId').value = image;
	console.log(document.getElementById('deleteConfirm').innerText);
}




$('#filterTags').select2({
	theme: "bootstrap-5",
	placeholder: 'Sélectionner les tags',
	language: {
		noResults: () => 'Aucun tag trouvé'
	},
	closeOnSelect: false,
});