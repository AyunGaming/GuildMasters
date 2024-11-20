function initKamenewsCreator(){
	document.getElementById('hidden-date').valueAsDate = new Date();
	document.getElementById('date').valueAsDate = new Date();

	if(localStorage.getItem('kamenews') !== null){
		const kamenews = JSON.parse(localStorage.getItem('kamenews'))
		document.getElementById('title').value = kamenews[0].title
		document.getElementById('description').value = kamenews[0].description
	}

	document.getElementById('nbArticles').value = localStorage.getItem('articles') === null ? 0 : JSON.parse(localStorage.getItem('articles')).length
}

function checkHasArticle(){
	const articles = JSON.parse(localStorage.getItem('articles'))
	if(articles == null){
		document.getElementById('articles-list').innerHTML = '<p class="ms-2 mt-1 italic text-gray-900 dark:text-white">Aucun article</p>'
	}
	else{
		document.getElementById('articles-list').innerHTML = ''
		articles.forEach(article => {
			document.getElementById('articles-list').innerHTML += `
					<li class="article-item mb-2">
						<label class="text-gray-900 dark:text-white">${article.title}</label>
						<button id="delete-${article.id}" class="btn btn-sm btn-danger ms-1 delete-articles" type="button" 
						data-bs-toggle="modal" data-bs-target="#deleteArticleModal"
						onclick="initDeleteModal('${article.id}', '${article.title}')">
							<i class="bi-trash"></i>
						</button>
					</li>`
		})
	}
}

function initDeleteModal(id, name){
	document.getElementById('delete-id').value = id
	document.getElementById('deleteConfirm').innerHTML = `Voulez-vous supprimer définitvement l'article ${name} ?`
}

function removeArticle(id){
	let articles = JSON.parse(localStorage.getItem('articles'))
	articles.splice(articles.indexOf(id)-1, 1)

	if(articles.length === 0){
		localStorage.removeItem('articles')
	}
	else{
		localStorage.setItem('articles', JSON.stringify(articles))
	}
}

function saveArticle(){
	const kTitle = document.getElementById('title').value
	const desc = document.getElementById('description').value
	if(kTitle !== '' || desc  !== ''){
		localStorage.setItem('kamenews', JSON.stringify([{'title': kTitle, 'description': desc}]))
	}

	if(localStorage.getItem('articles') === null){
		localStorage.setItem('articles', JSON.stringify([]))
	}

	const articles = JSON.parse(localStorage.getItem('articles'))

	let title = document.getElementById('article-title').value
	let content = document.getElementById('content').value
	let image = ""
	if(document.getElementById('articleImage').files.item(0) !== null){
		let image = document.getElementById('articleImage').files.item(0).name
	}
	let article = {
		id : articles.length+ 1,
		title: title,
		content: content,
		image: image
	}

	articles.push(article)
	localStorage.setItem('articles', JSON.stringify(articles))
}

window.addEventListener('load', () => {
	initKamenewsCreator();
	checkHasArticle();

	document.getElementById('create-article').addEventListener('click', () => {
		saveArticle();
	})

	document.querySelectorAll('.delete-articles').forEach(button => {
		button.addEventListener('click', () => {
			const id = button.id.split('-')[1]
			document.getElementById('delete-confirm').addEventListener('click', () => {
				removeArticle(id)
			})
		})
	})

	document.getElementById('createKamenews').addEventListener('click', () => {
		localStorage.clear()
	})
})