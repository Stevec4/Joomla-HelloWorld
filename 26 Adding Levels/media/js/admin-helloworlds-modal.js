(function() {
	"use strict";
	/**
	 * Javascript to set up onclick listeners on the helloworld greetings
	 * When a greeting is clicked the listener invokes the function in the parent window
	 * which is given by the data-function attribute of the helloworld greeting html element
	 * In this way the identity of the helloworld record selected in the modal is passed to the field in the parent window
	 */

	document.addEventListener('DOMContentLoaded', function(){
		
		var elements = document.querySelectorAll('.select-link');

		for(var i = 0, l = elements.length; l>i; i++) {
			
			elements[i].addEventListener('click', function (event) {
				event.preventDefault();
				var functionName = event.target.getAttribute('data-function');
				window.parent[functionName](event.target.getAttribute('data-id'), event.target.getAttribute('data-title'), null, null, event.target.getAttribute('data-uri'), event.target.getAttribute('data-language'), null);
			})
		}
	});
})();