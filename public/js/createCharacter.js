function afficherReview() {
    const imageInput = document.getElementById('c_characterImage');
    const idInput = document.getElementById('c_characterID').value;
    const nomInput = document.getElementById('c_characterName').value;
    const rareteSelect = document.getElementById('c_characterRarity').value;
    const lfInput = document.getElementById('c_characterLL').checked;
    const couleurSelect = document.getElementById('c_characterColor').value;
    const tagsSelect = Array.from(document.getElementById('c_characterTags').selectedOptions).map(option => option.value);

    const reviewDiv = document.getElementById('create_page_4');
    reviewDiv.innerHTML = `
                                <div class="flex mb-4">
                                    <div class="flex-1 mr-4">
                                        <div class="flex items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg h-full focus:ring-primary-600 focus:border-primary-600 w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                            <img draggable="false" id="d_characterImage" src="${imageInput.files.length === 0 ? '/public/images/members/default.png' : window.URL.createObjectURL((imageInput.files[0]))}" alt="" width="auto" height="auto" class="inline-block align-middle">
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="p-2.5 mb-4">
                                            <label for="characterId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID</label>
                                            <input readonly id="characterId" type="text" name="characterId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="DBL-EVT-00S" required value="${idInput}">
                                        </div>
                                        <div class="p-2.5">
                                            <label for="characterName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom</label>
                                            <input readonly id="characterName" type="text" name="characterName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Name" required value="${nomInput}">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="flex my-4" style="justify-content: space-evenly">
                                    <span class="inline-flex items-center justify-center p-1 rounded-full bg-gray-600 border-4 border-gray-500"><img src="/public/images/rarities/${rareteSelect}.png" alt="rarete: ${rareteSelect}" width="50" height="50"></span>
                                    <span class="inline-flex items-center justify-center p-1 rounded-full bg-gray-600 border-4 border-gray-500"><img src="/public/images/lf/${lfInput ? 'LF' : 'NOTLF'}.png" alt="LF: ${lfInput ? 'OUI' : 'NON'}" width="50" height="50"></span>
                                    <span class="inline-flex items-center justify-center p-1 rounded-full bg-gray-600 border-4 border-gray-500"><img src="/public/images/colors/${couleurSelect}.png" alt="Couleur: ${couleurSelect}" width="50" height="50"></span>
                                </div>
                                <hr/>
                                <div class="flex my-4">
                                    <div id="c_selected-tags" class="w-full relative bg-gray-50 text-gray-900 text-sm px-4 py-3 rounded-lg border-t border-b border-gray-200 flex gap-x-4 flex-wrap gap-y-4 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white h-24 overflow-y-scroll">
                                    </div>
                                </div
                            `;
    addTagsDisplay(tagsSelect);
}

function addTagsDisplay(tags) {
    const selectedTagsDiv = document.getElementById('c_selected-tags');

    tags.forEach(tagName => {
        // Check if the tag is already added
        const existingDivs = selectedTagsDiv.querySelectorAll('div');
        for (const div of existingDivs) {
            if (div.querySelector('span').textContent === tagName) {
                return; // Do not add the tag if it already exists
            }
        }

        const span = document.createElement('span');
        span.innerText += tagName;
        span.classList.add('h-8', 'bg-blue-600', 'text-xs', 'text-white', 'rounded-full', 'px-2', 'py-1', 'mb-1', 'inline-flex', 'items-center');
        selectedTagsDiv.appendChild(span);
    });
}

function validateFormPage(page_number) {
    const current_page = document.getElementById(`create_page_${page_number}`);
    const inputs = current_page.querySelectorAll('input[required]');
    for (let input of inputs) {
        if (!input.value.trim()) {
            console.log("need all required inputs (*) to be filled");
            return false;
        }
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    let createPage = 1;
    const form = document.getElementById('character-create-form');
    const previousButton = document.getElementById('previous-create-button');
    const nextButton = document.getElementById('next-create-button');
    const totalSteps = 4;  // Nombre total de pages/étapes

    function updatePage() {
        previousButton.disabled = createPage === 1;
        nextButton.textContent = createPage === totalSteps ? 'Soumettre' : 'Suivant';

        if (createPage === 1) {
            previousButton.disabled = true;
            previousButton.classList.remove('text-gray-900', 'dark:text-gray-400');
            previousButton.classList.add('text-red-900', 'dark:text-red-400');
            previousButton.style.cursor = 'not-allowed';
        } else {
            previousButton.disabled = false;
            previousButton.classList.remove('text-red-900', 'dark:text-red-400');
            previousButton.classList.add('text-gray-900', 'dark:text-gray-400');
            previousButton.style.cursor = '';
        }

        if (createPage === totalSteps) {
            afficherReview();
            nextButton.textContent = 'Soumettre';
            nextButton.setAttribute("type", "submit");
            nextButton.classList.remove('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300', 'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');
            nextButton.classList.add('bg-green-700', 'hover:bg-green-800', 'focus:ring-green-300', 'dark:bg-green-600', 'dark:hover:bg-green-700', 'dark:focus:ring-green-800');
        } else {
            nextButton.textContent = 'Suivant';
            nextButton.setAttribute("type", "button");
            nextButton.classList.remove('bg-green-700', 'hover:bg-green-800', 'focus:ring-green-300', 'dark:bg-green-600', 'dark:hover:bg-green-700', 'dark:focus:ring-green-800');
            nextButton.classList.add('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-blue-300', 'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');
        }

        for (let i = 1; i <= totalSteps; i++) {
            const section = document.getElementById(`create_page_${i}`);
            if (section) {
                section.style.display = i === createPage ? 'block' : 'none';
            }
        }

        // Mettre à jour les étapes dans le stepper
        const stepper = document.getElementById('create-stepper');
        if (stepper) {
            stepper.querySelectorAll('li').forEach((li, index) => {
                if (index < createPage) {
                    li.classList.add('text-blue-600', 'dark:text-blue-500');
                    li.classList.remove('text-gray-500', 'dark:text-gray-400');
                    const span = li.querySelector('span');
                    if (span) {
                        span.classList.add('border-blue-600', 'dark:border-blue-500');
                        span.classList.remove('border-gray-500', 'dark:border-gray-400');
                    }
                } else {
                    li.classList.remove('text-blue-600', 'dark:text-blue-500');
                    li.classList.add('text-gray-500', 'dark:text-gray-400');
                    const span = li.querySelector('span');
                    if (span) {
                        span.classList.remove('border-blue-600', 'dark:border-blue-500');
                        span.classList.add('border-gray-500', 'dark:border-gray-400');
                    }
                }

            });
        }
    }

    previousButton.addEventListener('click', function () {
        if (createPage > 1) {
            createPage--;
            updatePage();
        }
    });

    nextButton.addEventListener('click', function () {
        if (createPage < totalSteps) {
            if (validateFormPage(createPage)) {
                createPage++;
                updatePage();
            }
        } else {
            // Soumettre le formulaire lorsque toutes les étapes sont complétées
            form.submit();
        }
    });

    // Initialiser l'affichage de la page
    updatePage();

    // Fermeture de la modal
    document.addEventListener('click', function (event) {
        const modal = document.getElementById('crud-modal-create');
        const isCloseButton = event.target.getAttribute('data-modal-toggle') === 'crud-modal-create';
        const isOutsideModal = event.target === modal;

        if (isCloseButton || isOutsideModal) {
            modal.classList.add('hidden');
            document.getElementById('character-create-form').reset()
            createPage = 1;
            updatePage()
            handleCreateModalClose();
        }
    });

    document.getElementById('close-create-modal').addEventListener('click', function () {
        const modal = document.getElementById('crud-modal-create');
        modal.classList.add('hidden');
        document.getElementById('character-create-form').reset();
        createPage = 1;
        updatePage()
        handleCreateModalClose();
    });

    document.getElementById('next-create-button').addEventListener('submit', function () {
        document.getElementById('crud-modal-create').classList.add('hidden');
        console.log(1);
        console.log(document.querySelectorAll('#c_characterTags .tag-option'));

        handleCreateModalClose();
    });
});


document.getElementById('create-tag-search').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const dropdown = document.getElementById('create-tag-dropdown');
    const dropdownList = document.getElementById('create-tag-dropdown-list');
    const options = document.querySelectorAll('#c_characterTags .tag-option');

    dropdownList.innerHTML = '';
    let hasMatch = false;

    console.log(options)

    options.forEach(option => {
        console.log(option.textContent.toLowerCase()+" : "+option.textContent.toLowerCase().includes(searchValue))
        if (option.textContent.toLowerCase().includes(searchValue)) {
            const li = document.createElement('li');
            li.textContent = option.textContent;
            li.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-500', 'text-white');
            li.addEventListener('click', function() {
                option.setAttribute('selected', 'true');
                addCreateTagButton(option.value);
                dropdown.classList.add('hidden');
                document.getElementById('create-tag-search').value = '';
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

document.getElementById('create-add-tag').addEventListener('click', function(event) {
    event.preventDefault();
    const searchValue = document.getElementById('create-tag-search').value.toLowerCase();
    const options = document.querySelectorAll('#c_characterTags .tag-option');

    let optionExists = false;

    options.forEach(option => {
        if (option.textContent.toLowerCase() === searchValue) {
            option.setAttribute('selected', 'true');
            addCreateTagButton(option.value);
            optionExists = true;
        }
    });

    if (!optionExists && searchValue) {
        const newOption = document.createElement('option');
        newOption.value = searchValue;
        newOption.textContent = searchValue;
        newOption.setAttribute('selected', 'true');
        newOption.classList.add('tag-option');
        document.getElementById('c_characterTags').appendChild(newOption);
        addCreateTagButton(searchValue);
    }

    document.getElementById('create-tag-search').value = '';
    document.getElementById('create-tag-dropdown').classList.add('hidden');
});

function addCreateTagButton(tagName) {
    const selectedTagsDiv = document.getElementById('create-selected-tags');

    // Check if the tag is already added
    const existingButtons = selectedTagsDiv.querySelectorAll('button');
    for (const button of existingButtons) {
        if (button.querySelector('span').textContent === tagName) {
            return; // Do not add the tag if it already exists
        }
    }

    const button = document.createElement('button');
    button.innerHTML += `<span>${tagName}</span>`;
    button.innerHTML += `<svg class="ml-2 w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                            </svg>
                                `;
    button.classList.add('h-8', 'bg-blue-600', 'text-xs', 'text-white', 'rounded-full', 'px-2', 'py-1', 'mb-1','inline-flex', 'items-center');
    button.addEventListener('click', function() {
        button.remove();
        const option = document.querySelector(`#c_characterTags option[value="${tagName}"]`);
        if (option) {
            option.removeAttribute('selected');
        }
    });
    selectedTagsDiv.appendChild(button);
}

function clearCreateSelectedTags() {
    document.getElementById('create-selected-tags').innerHTML = '';
    const options = document.querySelectorAll('#c_characterTags .tag-option');
    options.forEach(option => {
        option.removeAttribute('selected');
    });
}

function handleCreateModalClose() {
    clearCreateSelectedTags();
}

document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('crud-modal-create')
    if (event.key === 'Escape' && modal.classList.contains('hidden')) {
        modal.classList.add('hidden');
        handleModalClose();
    }
});