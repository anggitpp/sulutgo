function setPendidikan(data, getPar) {
	jQuery(".chosen-container").attr("style", "width:150px")
	if (data > 614) {
		jQuery("#divFakultas").fadeIn(500)
		jQuery("#divJurusan").fadeIn(500)
	} else {
		jQuery("#divFakultas").fadeOut()
		jQuery("#divJurusan").fadeOut()
	}
}