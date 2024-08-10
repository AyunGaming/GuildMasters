function expandImage(image){
	image.style.display = 'flex';
	image.style.transform = "scale(2)";
	image.style.transition = "transform 0.4s ease";
	image.style.zIndex = "5";
}

function resetImage(image){
	image.style.transform = "scale(1)";
	image.style.transition = "transform 0.4s ease";

	setTimeout(() => {
		image.style.display = "relative";
		image.style.zIndex = "0";
	},400);
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