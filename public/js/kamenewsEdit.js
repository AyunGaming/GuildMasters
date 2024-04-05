//region positionButtons
function moveUp(divId) {
	let div = document.getElementById(divId);
	let prev = div.previousElementSibling;
	if (div && prev) {
		let parent = div.parentNode;
		parent.insertBefore(div, prev);
	}
	return prev;
}

function moveDown(divId) {
	let div = document.getElementById(divId);
	let next = div.nextElementSibling;
	if (div && next) {
		let parent = div.parentNode;
		parent.insertBefore(next, div);
	}
	return next;
}

function updateButtons(div,upBtnId,downBtnId) {
	let selectedDiv = document.getElementById(div).id;
	if (selectedDiv) {
		let firstDiv = document.getElementById("articlesAccordion").firstElementChild.id;
		let lastDiv = document.getElementById("articlesAccordion").lastElementChild.id;
		document.getElementById(upBtnId).disabled = selectedDiv === firstDiv;
		document.getElementById(downBtnId).disabled = selectedDiv === lastDiv;
	}
}
//endregion

//region ArticleModals
function getArticleInfos(id) {
	const article = {
		id: id,
		title: document.getElementById(`title-${id}`).innerText,
		content: document.getElementById(`text-${id}`).innerText,
	}
	document.getElementById("updateArticleId").value = article.id;
	initModal(article)
}

function initModal(article){
	document.getElementById("updateArticleId").value = article.id;
	document.getElementById("updateArticleTitle").value = article.title;
	document.getElementById("updateArticleText").value = article.content;
}

//endregion


window.addEventListener('load',() => {

	updateButtons("article-1","up-article-1","down-article-1");
	parent = document.getElementById("articlesAccordion");
	updateButtons(parent.lastElementChild.id,`up-${parent.lastElementChild.id}`,`down-${parent.lastElementChild.id}`);

	document.getElementById("articlesAccordion").lastElementChild.disabled = true;
	document.querySelectorAll(".move-up").forEach((el) => {
		el.addEventListener("click", () => {
			let divId = el.parentElement.parentElement.id
			let moved = moveUp(divId);
			updateButtons(divId,`up-${divId}`,`down-${divId}`);
			updateButtons(moved.id,`up-${moved.id}`,`down-${moved.id}`)
		});
	});

	document.querySelectorAll(".move-down").forEach((el) => {
		el.addEventListener("click", () => {
			let divId = el.parentElement.parentElement.id
			let moved = moveDown(divId);
			updateButtons(divId,`up-${divId}`,`down-${divId}`);
			updateButtons(moved.id,`up-${moved.id}`,`down-${moved.id}`)
		});
	});
	/*
	document.getElementById("updateKamenewsBtn").addEventListener("click", async () => {
		await updateArticle();
	})*/
})

