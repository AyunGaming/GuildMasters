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
	const selectedTags = document.getElementById("selected-tags");
	function addTagButton(tagName) {
		const button = document.createElement('button');
		button.innerHTML += `<span>${tagName}</span>`;
		button.innerHTML += `<svg class="ml-2 w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                            </svg>
                                `;
		button.classList.add('bg-blue-600', 'text-xs', 'text-white', 'rounded-full', 'px-2', 'py-1', 'mb-1','inline-flex', 'items-center');
		button.addEventListener('click', function() {
			button.remove();
			const option = document.querySelector(`#updateTags option[value="${tagName}"]`);
			if (option) {
				option.removeAttribute('selected');
			}
		});
		selectedTags.appendChild(button);
	}
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