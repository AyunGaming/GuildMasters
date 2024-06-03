document.getElementById('tag-search').addEventListener('input', function() {
	const searchValue = this.value.toLowerCase();
	const dropdown = document.getElementById('dropdown');
	const dropdownList = document.getElementById('dropdown-list');
	const options = document.querySelectorAll('#updateTags .tag-option');

	dropdownList.innerHTML = '';
	let hasMatch = false;

	options.forEach(option => {
		if (option.textContent.toLowerCase().includes(searchValue)) {
			const li = document.createElement('li');
			li.textContent = option.textContent;
			li.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-500', 'text-white');
			li.addEventListener('click', function() {
				option.setAttribute('selected', 'true');
				addTagButton(option.value);
				dropdown.classList.add('hidden');
				document.getElementById('tag-search').value = '';
			});
			dropdownList.appendChild(li);
			hasMatch = true;
		}
	});

	if (hasMatch && searchValue) {
		dropdown.classList.remove('hidden');
	} else {
		dropdown.classList.add('hidden');
	}
});

document.getElementById('add-tag').addEventListener('click', function(event) {
	event.preventDefault();
	const searchValue = document.getElementById('tag-search').value.toLowerCase();
	const options = document.querySelectorAll('#updateTags .tag-option');

	let optionExists = false;

	options.forEach(option => {
		if (option.textContent.toLowerCase() === searchValue) {
			option.setAttribute('selected', 'true');
			addTagButton(option.value);
			optionExists = true;
		}
	});

	if (!optionExists && searchValue) {
		const newOption = document.createElement('option');
		newOption.value = searchValue;
		newOption.textContent = searchValue;
		newOption.setAttribute('selected', 'true');
		newOption.classList.add('tag-option');
		document.getElementById('updateTags').appendChild(newOption);
		addTagButton(searchValue);
	}

	document.getElementById('tag-search').value = '';
	document.getElementById('dropdown').classList.add('hidden');
});

function addTagButton(tagName) {
	const selectedTagsDiv = document.getElementById('selected-tags');
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
	selectedTagsDiv.appendChild(button);
}



function clearSelectedTags() {
	document.getElementById('selected-tags').innerHTML = '';
	const options = document.querySelectorAll('#updateTags .tag-option');
	options.forEach(option => {
		option.removeAttribute('selected');
	});
}

function handleModalClose() {
	clearSelectedTags();
}

// Attach event listeners for closing the modal
document.addEventListener('click', function(event) {
	const modal = document.getElementById('crud-modal-update');
	const isCloseButton = event.target.getAttribute('data-modal-toggle') === 'crud-modal-update';
	const isOutsideModal = event.target === modal;

	if (isCloseButton || isOutsideModal) {
		modal.classList.add('hidden');
		handleModalClose();
	}
});

document.getElementById('close-update-modal').addEventListener('click', function() {
	const modal = document.getElementById('crud-modal-update');
	modal.classList.add('hidden');
	handleModalClose();
});

document.getElementById('update-form-sender').addEventListener('submit', function(event) {
	event.preventDefault(); // Prevent form submission for demonstration
	document.getElementById('crud-modal-update').classList.add('hidden');
	handleModalClose();
});