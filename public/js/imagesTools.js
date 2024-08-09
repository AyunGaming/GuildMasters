function expandImage(image){
	image.style.display = 'flex';
	image.style.transform = "scale(2)";
	image.style.transition = "transform 0.25s ease";
	image.style.zIndex = "5";
}

function resetImage(image){
	image.style.display = "relative";
	image.style.zIndex = "0";
	image.style.transform = "scale(1)";
	image.style.transition = "transform 0.25s ease";
}

window.addEventListener('load', () => {
	let images = document.querySelectorAll('.images')
	document.getElementById('container').addEventListener('click', () => {
		images.forEach(image => {
			resetImage(image)
		});
	});

	images.forEach(expand => {
		expand.addEventListener('click', function (ev) {
			ev.stopPropagation();
			images.forEach(image => {
				if(image !== expand){
					resetImage(image)
				}
			});
			expandImage(expand);
		});
	});


})