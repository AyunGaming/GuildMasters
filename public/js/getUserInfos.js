function getUpdateUserInfos(image, rarity, lf, name, color, tags) {
	document.getElementById('oldId').value = image;
	document.getElementById('updateId').value = image;
	document.getElementById('updateName').value = name;
	document.getElementById('updateRarity').value = rarity;
	document.getElementById('updateIsLfCheckbox').checked = lf === "1";
	document.getElementById('updateColor').value = color;
	document.getElementById('updateTags').value = tags;
	const tagsArray = tags.split(", ");
	const select = document.getElementById('updateTags');

	for (let i = 0; i < tagsArray.length; i++) {
		for (let j=0; j < select.options.length; j++) {
			if (tagsArray[i] === select.options[j].value) {
				addTagButton(select.options[j].value);
				select.options[j].setAttribute('selected', 'selected');
			}
		}
	}
}

function getDeleteUserInfos(image,name){
	document.getElementById('deleteConfirm').innerText = `Voulez-vous supprimer définitivement le personnage: ${image}: ${name} ?`;
	document.getElementById('characterId').value = image;
	console.log(document.getElementById('deleteConfirm').innerText);
}