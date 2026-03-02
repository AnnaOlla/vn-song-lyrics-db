async function sendReportStatus(e) {
	const url = '/en/report/change-status';
	
	// select -> p -> td[n] -> tr -> td[0]
	const id     = e.target.parentElement.parentElement.parentElement.children[0].textContent;
	const status = e.target.value;
	
	const formData = new FormData();
	formData.append('id', id);
	formData.append('status', status);
	
	const response = await fetch(url, {
		method: "POST",
		body: formData
	});
	
	if (response.ok) {
		e.target.style.backgroundColor = '#7CBF5F';
	} else {
		const echo = await response.text();
		alert(response.status + ': ' + echo);
		e.target.style.backgroundColor = '#BF5F5F';
	}
}

const statusSelects = document.querySelectorAll('select.status-select');

for (statusSelect of statusSelects) {
	statusSelect.addEventListener('change', sendReportStatus);
}
