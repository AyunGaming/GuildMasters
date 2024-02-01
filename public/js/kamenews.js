async function readKamenews(url,id){
	await fetchURLFor(url,{
		Id: parseInt(id)
	});
}