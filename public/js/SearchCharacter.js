let searchBox = document.getElementById('search')
let filtersBtn = document.getElementById('filtersBtn');
let charactersString = document.getElementById('charactersList').innerText;
let filters_buttons = document.querySelectorAll('.filter-btn');
let filter_check = document.querySelectorAll('.filter-check');
let resetTagsBtn = document.getElementById('resetTags');

let filters = [];

filtersBtn.addEventListener('click', function () {
	displayFilters();
})

searchBox.addEventListener('keyup', function () {
	search();
})

filters_buttons.forEach(function (input) {
	input.addEventListener('click', function () {
		search();
	})
})

filter_check.forEach(function (input) {
	input.addEventListener('change', function () {
		search();
	})
})

resetTagsBtn.addEventListener('click', function () {
	resetFilters();
})

function displayFilters() {
	let filterDiv = document.getElementById('filters');

	if (filterDiv.style.display === 'none') {
		filterDiv.style.display = "flex";
	} else {
		filterDiv.style.display = 'none';
	}
}

function search() {
	// Declare variables
	let category, filter, table;
	category = document.getElementById("character-research");
	filter = searchBox.value.toUpperCase();
	table = document.getElementById("characters");

	let rarity = document.querySelector('input[name="rarity"]:checked');

	if (rarity !== null) {
		rarity = rarity.value;
	} else {
		rarity = "all";
	}

	let color = document.querySelector('input[name="color"]:checked');
	if (color !== null) {
		color = color.value.toLowerCase();
	} else {
		color = "all";
	}

	let lf = document.querySelector('input[name="updateIsLfCheckbox"]:checked');
	if (lf) {
		lf = "lf";
	} else {
		lf = "notlf";
	}

	let selectedValues = [];
	let select = document.querySelector('#filterTags');

	for (let i = 0; i < select.length; i++) {
		if (select.options[i].selected) {
			selectedValues.push(select.options[i].value);
		}
	}

	let dict = {
		"name": 4,
		"id": 0
	}

	let rows = table.rows;

	for (let i = 1; i < rows.length; i++) {

		let row = rows[i];

		let cells = row.cells;

		let rarCell = cells[2].children[0].src.slice(47, -4);
		let lfCell = cells[3].children[0].src.slice(41, -4);
		let colCell = cells[5].children[0].src.slice(45, -4);
		let tagsCell = cells[6].innerHTML;

		let searchCell = cells[dict[category.value]];

		let sfil = search_filter(rarity, lf, color, selectedValues);
		let showRow = true;

		if (sfil["rarity"].length === 1 && showRow) //si une seule rareté et doit etre montré
		{
			sfil["rarity"].forEach(element => {
				if (element.toUpperCase() !== rarCell.toUpperCase()) {
					showRow = false;
				}
			});
		}

		if (sfil["lf"].length === 1 && showRow) //si lf et doit etre montré
		{
			sfil["lf"].forEach(element => {
				if (element.toUpperCase() !== lfCell.toUpperCase()) {
					showRow = false;
				}
			});
		}

		if (sfil["color"].length === 1 && showRow) //si une seule couleur et doit etre montré
		{
			sfil["color"].forEach(element => {
				if (element.toUpperCase() !== colCell.toUpperCase()) {
					showRow = false;
				}
			});
		}


		if (sfil["tags"].length !== 0 && showRow) //si au moins un tag et doit etre montré
		{
			let tagArray = tagsCell.split(",")
			tagArray = trimTagArray(tagArray)
			sfil["tags"].forEach(element => {
				if (!tagArray.includes(element)) {
					showRow = false;
				}
			});
		}

		if (!(searchCell.innerHTML.toUpperCase().indexOf(filter) > -1)) {
			showRow = false;
		}

		if (showRow) {
			rows[i].style.display = "";
		} else {
			rows[i].style.display = "none";
		}
	}
}

function search_filter(rarity, lf, color, tags) {
	if (rarity === "all") {
		filters["rarity"] = ["HERO", "EXTREME", "SPARKING", "ULTRA"];
	} else {
		filters["rarity"] = [rarity];
	}

	if (lf === "lf") {
		filters["lf"] = [lf];
	} else {
		filters["lf"] = ["lf", "notlf"];
	}

	if (color === "all") {
		filters["color"] = ["rou", "jau", "vio", "ver", "ble", "lum"];
	} else {
		filters["color"] = [color];
	}

	if (tags === []) {
		filters["tags"] = [];
	} else {
		filters["tags"] = tags;
	}

	return filters;
}

function resetFilters() {
	filters = [];
	$("#filterTags").val('').trigger('change')
	filter_check.forEach(function (input) {
		input.checked = false;
	})
	filters_buttons.forEach(function (input) {
		input.checked = false;
	})
	search();
}

function trimTagArray(array) {
	for (let i = 1; i < array.length; i++) {
		array[i] = array[i].trim()
	}
	return array
}

let characters = JSON.parse(charactersString);