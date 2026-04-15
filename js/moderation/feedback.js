async function deleteFeedback(e) {
	const currentUriPart = window.location.pathname.split('/');
	const language       = currentUriPart[1];
	const url            = '/' + language + '/delete-feedback';
	
	const thisButton   = e.target;
	const thisFeedback = thisButton.parentNode.parentNode;
	
	const feedbackId = thisFeedback.getAttribute('data-id');
	
	const formData = new FormData();
	formData.append('feedback-id', feedbackId);
	
	const response = await fetch(url, {
		method: "POST",
		body: formData
	});
	
	if (response.ok) {
		window.location.reload();
	} else {
		const echo = await response.text();
		alert(response.status + ': ' + echo);
	}
}

async function sendReply(e) {
	const currentUriPart = window.location.pathname.split('/');
	const language       = currentUriPart[1];
	const url            = '/' + language + '/add-feedback-reply';
	
	const thisButton   = e.target;
	const thisFeedback = thisButton.parentNode.parentNode;
	const thisTextarea = thisFeedback.querySelector('textarea');
	
	const feedbackId = thisFeedback.getAttribute('data-id');
	const replyText  = thisTextarea.value;
	
	const formData = new FormData();
	formData.append('feedback-id', feedbackId);
	formData.append('reply-text', replyText);
	
	const response = await fetch(url, {
		method: "POST",
		body: formData
	});
	
	if (response.ok) {
		window.location.reload();
	} else {
		const echo = await response.text();
		alert(response.status + ': ' + echo);
	}
}

/* function main() */ {
	const deleteButtons = document.getElementsByClassName('delete-feedback-button');
	
	for (deleteButton of deleteButtons) {
		deleteButton.addEventListener('click', deleteFeedback);
	}
	
	const replyButtons = document.getElementsByClassName('send-reply-button');
	
	for (replyButton of replyButtons) {
		replyButton.addEventListener('click', sendReply);
	}
}
