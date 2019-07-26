var metaArr = {};

metaArr['title'] = document.title;

var metas = document.getElementsByTagName('meta'); 
for (var i=0; i<metas.length; i++) { 
	var name = metas[i].getAttribute("name");
	var content = metas[i].getAttribute("content");
	if(name == "keywords" || name == "description"){
		//~ metaArr.push([name, content]);
		metaArr[name] = content;
	}
} 

chrome.runtime.sendMessage({
	method:"getMetas",
	metas:metaArr
});
