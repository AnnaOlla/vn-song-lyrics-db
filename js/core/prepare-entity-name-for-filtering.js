// User may enter the name romanized differently, remove all whitespaces
function prepareEntityNameForFiltering(entityName) {
	return entityName.replaceAll(/[^\p{L}\p{Nd}]/gu, '');
}
