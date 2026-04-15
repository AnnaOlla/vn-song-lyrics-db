function emulateEvent(node, eventName) {
	const emulatedEvent = new Event(eventName);
	node.dispatchEvent(emulatedEvent);
}
