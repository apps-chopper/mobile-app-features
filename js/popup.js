function getMetas() {
	var message = document.querySelector('#detected');
	chrome.tabs.executeScript(null, {
		file: "js/getPageMetas.js"
	}, function() {
		// If you try it into an extensions page or the webstore/NTP you'll get an error
		if (chrome.runtime.lastError) {
			message.innerText = 'There was an error : \n' + chrome.runtime.lastError.message;
		}
	});
}


chrome.runtime.onMessage.addListener(function(request, sender) {
	var detected = document.getElementById('detected');
	if (request.method == "getMetas") {
        chrome.tabs.query({
			active: true,
			currentWindow: true
		}, function(tabs) {
			var tabURL = tabs[0].url;
			request.metas['url'] = tabURL;
			
			detected.innerHTML = '<p>Please wait ..</p>';
			$.ajax({
				type: "POST",
				async: true,
				data: {data: request.metas},
				url: "http://example.com/api/api.php", // Place your api server url here.
				success: function(data){
					console.log(data);
					detected.innerHTML = data;
				}
			});
			
		});
		
		function getCall() {
				chrome.tabs.query({
				active: true,
				currentWindow: true
			}, function(tabs) {
				var tabURL = tabs[0].url;
				request.metas['url'] = tabURL;
				
			});
			return request.metas;
		}
				
	}
});

window.onload = getMetas;
