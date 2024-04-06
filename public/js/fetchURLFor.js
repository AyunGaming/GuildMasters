/**
 * Handles the `fetch` response.
 *
 * @param {Response} response Response data.
 * @param {number} expectedCodes Expected response codes.
 * @return {Promise<void>} Promise that holds nothing.
 * @private
 */
const _handleResponse = async (response, expectedCodes) => {
	if (response.status in expectedCodes) {
		const json = await response.json()
		alert(`Erreur: ${json.error}`)
	}
	window.location = response.url
}

/**
 * "Builds" the URL to fetch.
 *
 * @param {string} url API URL.
 * @param {{[T : string] : string|number}} mapUrlParams URL placeholder replacement.
 * @return {string} Final URL.
 * @private
 */
const _prepareUrl = (url, mapUrlParams) => {
	let newUrl = url
	Object.keys(mapUrlParams).forEach(
		param => newUrl = newUrl.replace(param, mapUrlParams[param])
	)
	return newUrl
}

/**
 * Fetches a URL from other scripts and errors if status code is not `204`.
 *
 * @param {string} url API URL.
 * @param {{[T : string] : string|number}} mapUrlParams URL placeholder replacement.
 * @returns {Promise<void>} Promise that holds nothing.
 */
const fetchURLFor = async (url, mapUrlParams) => {
	const resp = await fetch(_prepareUrl(url, mapUrlParams), {method: 'POST'})
	await _handleResponse(resp, [204,200,302])
}

/**
 * Fetches a URL from other scripts with the given body data as a JSON and errors if status code is not `204`.
 *
 * @param {string} url API URL.
 * @param {{[T : string] : string|number}} mapUrlParams URL placeholder replacement.
 * @param {any[] | {[T : string] : any}} body Body data to send in request.
 * @return {Promise<void>} Promise that holds nothing.
 */
const fetchURLForWithJSONBody = async (url, mapUrlParams, body) => {
	const resp = await fetch(_prepareUrl(url, mapUrlParams), {
		body: JSON.stringify(body),
		headers: {
			'Content-Type': 'application/json',
		},
		method: 'POST',
	})
	await _handleResponse(resp, 204)
}
