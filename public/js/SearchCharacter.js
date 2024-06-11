function handleSearch() {
    const searchType = document.getElementById('character-research');
    const searchTerm = document.getElementById('search').value;

    const table_body = document.getElementById('shown_table').body;
}

function switch_filters() {
    const filter_sec_classes=document.getElementById('filters').classList;
    const search_div_classes = document.getElementById('search_zone').classList;

    if (!filter_sec_classes.contains('hidden')) {
        search_div_classes.remove('mt-4');
        search_div_classes.add('my-4');
        search_div_classes.add('rounded-lg');
        search_div_classes.remove('rounded-t-lg');
    } else {
        search_div_classes.remove('my-4');
        search_div_classes.add('mt-4');
        search_div_classes.remove('rounded-lg');
        search_div_classes.add('rounded-t-lg');
    }
    filter_sec_classes.toggle('block');
    filter_sec_classes.toggle('hidden');
}
function toggleInputType() {
    const andorCheckbox = document.getElementById('filter_andor');
    const rarityRadios = document.querySelectorAll('#filters input[name="rarity"]');
    const colorRadios = document.querySelectorAll('#filters input[name="color"]');

    if (andorCheckbox.checked) {
        // Change radio inputs to checkboxes
        rarityRadios.forEach(function(radio) {
            radio.type = 'checkbox';
            radio.classList.add('rounded');
        });
        colorRadios.forEach(function(radio) {
            radio.type = 'checkbox';
            radio.classList.add('rounded');
        });
    } else {
        // Change checkboxes back to radio inputs
        rarityRadios.forEach(function(radio) {
            radio.type = 'radio';
            radio.classList.remove('rounded');
        });
        colorRadios.forEach(function(radio) {
            radio.type = 'radio';
            radio.classList.remove('rounded');
        });
    }
}
function decocherInputsDansDiv(divId) {
    // Sélectionne le div spécifié
    const div = document.getElementById(divId);

    // Vérifie si le div existe
    if (div) {
        // Sélectionne tous les éléments input dans ce div
        const inputs = div.querySelectorAll('input');

        // Parcours chaque input
        inputs.forEach(function(input) {
            // Vérifie si c'est un radio ou un checkbox
            if (input.type === 'radio' || input.type === 'checkbox') {
                // Décoche l'input
                input.checked = false;
            }
        });
    } else {
        console.error("Le div spécifié n'existe pas.");
    }
}
function deselectionnerInputsDansFiltres() {
    // Sélectionne la section spécifiée
    const section = document.getElementById('filters');
    const characterResearchType = document.getElementById('character-research');
    const characterResearchZone = document.getElementById('search');

    // Vérifie si la section existe
    if (section) {
        // Sélectionne tous les éléments input de type checkbox ou radio dans cette section
        const inputs = section.querySelectorAll('input[type="checkbox"], input[type="radio"]');

        // Parcours chaque input
        inputs.forEach(function(input) {
            // Désélectionne l'input
            input.checked = false;
        });
        characterResearchType.options[0].selected = true;
        characterResearchZone.value="";
        toggleInputType();
        clearFilterSelectedTags();
    } else {
        console.error("La section spécifiée n'existe pas.");
    }
}
document.getElementById('filter-tag-search').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const dropdown = document.getElementById('filter-tag-dropdown');
    const dropdownList = document.getElementById('filter-tag-dropdown-list');
    const options = document.querySelectorAll('#filter_tags .tag-option');

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
                addFilterTagButton(option.value);
                dropdown.classList.add('hidden');
                document.getElementById('filter-tag-search').value = '';
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

document.getElementById('filter-add-tag').addEventListener('click', function(event) {
    event.preventDefault();
    const searchValue = document.getElementById('filter-tag-search').value.toLowerCase();
    const options = document.querySelectorAll('#filter_tags .tag-option');

    let optionExists = false;

    options.forEach(option => {
        if (option.textContent.toLowerCase() === searchValue) {
            option.setAttribute('selected', 'true');
            addFilterTagButton(option.value);
            optionExists = true;
        }
    });

    if (!optionExists && searchValue) {
        const newOption = document.createElement('option');
        newOption.value = searchValue;
        newOption.textContent = searchValue;
        newOption.setAttribute('selected', 'true');
        newOption.classList.add('tag-option');
        document.getElementById('filter_tags').appendChild(newOption);
        addFilterTagButton(searchValue);
    }

    document.getElementById('filter-tag-search').value = '';
    document.getElementById('filter-tag-dropdown').classList.add('hidden');
});

function addFilterTagButton(tagName) {
    const selectedTagsDiv = document.getElementById('filter-selected-tags');

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
        const option = document.querySelector(`#filter_tags option[value="${tagName}"]`);
        if (option) {
            option.removeAttribute('selected');
        }
    });
    selectedTagsDiv.appendChild(button);
}

function clearFilterSelectedTags() {
    document.getElementById('filter-selected-tags').innerHTML = '';
    const options = document.querySelectorAll('#filter_tags .tag-option');
    options.forEach(option => {
        option.removeAttribute('selected');
    });
}