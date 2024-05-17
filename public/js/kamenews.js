async function readKamenews(url,id){
	await fetchURLFor(url,{
		Id: parseInt(id)
	});
}

async function editKamenews(url, id){
	await fetchURLFor(url,{
		Id: parseInt(id)
	});
}