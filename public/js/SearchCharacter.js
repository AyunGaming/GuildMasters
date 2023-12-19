const IMAGE = 0;
const RARITY = 1;
const LF = 2;
const NAME = 3;
const COLOR = 4;
const TAGS = 5;

let searchBox = document.getElementById('search')
let filtersBtn = document.getElementById('filtersBtn');
let charactersString = document.getElementById('charactersList').innerText;

filtersBtn.addEventListener('click',function (){
	displayFilters();
})

function displayFilters(){
	var filterDiv = document.getElementById('filters');

	if(filterDiv.style.display === 'none'){
		filterDiv.style.display = "flex";
	}
	else{
		filterDiv.style.display = 'none';
	}
}

let characters = JSON.parse(charactersString);