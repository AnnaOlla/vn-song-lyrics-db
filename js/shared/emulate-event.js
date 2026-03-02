function emulateEvent(node, eventName) {
	let emulatedEvent = new Event(eventName);
	node.dispatchEvent(emulatedEvent);
}
