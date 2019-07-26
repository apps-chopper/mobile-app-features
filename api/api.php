<?php
header("Access-Control-Allow-Origin: *");
try {
	
	if(isset($_REQUEST['data']['url']) && $_REQUEST['data']['url']){
		
		$domain = getDomainUrl($_REQUEST['data']['url']);
		
		if($domain != rtrim($_REQUEST['data']['url'], '/')){
						
			$_REQUEST['data']['title'] 			= getTitle($domain);
			$tags 								= getMetaTags($domain);
			$_REQUEST['data']['keywords'] 		= $tags['keywords'];
			$_REQUEST['data']['description']	= $tags['description'];
		}
		
		$websiteCategory = getWebsiteCategory($_REQUEST['data']);	
		$appFeatures = getFeatures($websiteCategory);	
		
		$html = '';		
		if(!empty($websiteCategory)){
			foreach($websiteCategory as $category => $subCategories){
				$html .= '<div class="detected__category">
							<div class="detected__category-name"><a class="detected__category-link" target="_blank" href="#">'.$category.'</a></div>';
				foreach($subCategories as $subCategory){
					$html .= '<div class="detected__apps"><a class="detected__app" target="_blank" href="#"><img class="detected__app-icon" src="img/logo.png"><span class="detected__app-name">'.$subCategory.'</span></a></div>';
				}
				$html .= '<p class="help-text">Detected wrong category for your website? Write us on <a href="mailto:tools@appschopper.com">tools@appschopper.com</a></p>';
				$html .= '</div>';
			}
		}else{
			$html = '<p>Not Available</p>';
		}
		
		$html2 = '';		
		if(!empty($appFeatures)){
			foreach($appFeatures as $category => $features){	
				$html2 .= '<div class="detected__category">
							<div class="detected__category-name"><a class="detected__category-link" target="_blank" href="#">Suggested App Features</a></div>';
				foreach($features as $feature){
					$html2 .= '<div class="detected__apps"><a class="detected__app" target="_blank" href="#"><img class="detected__app-icon" src="img/logo.png"><span class="detected__app-name">'.$feature.'</span></a></div>';
				}
				$html2 .= '</div>';		
			}
		}
		echo $html.$html2; die;		
			
	} 	
}
catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}

function getDomainUrl($url) {
	$urlParts = parse_url($url);
	return $urlParts['scheme'].'://'.$urlParts['host'];
}

function getTitle($url) {
	$data = file_get_contents($url);
	$title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
	return $title;
}

function getMetaTags($url) {
	return get_meta_tags($url);
}

function getWebsiteCategory($data){
	include(__DIR__ . "/constant.php");
	$categories = json_decode(WEBSITE_CATEGORIES, 1);
		
	$matchCategories = [];
	
	$metaString = "";
	
	if(isset($data['title']) && $data['title']){
		$metaString .= $data['title'];
	}
	if(isset($data['keywords']) && $data['keywords']){
		$metaString .= ' '.$data['keywords'];
	}
	if(isset($data['description']) && $data['description']){
		$metaString .= ' '.$data['description'];
	}
	
	foreach ($categories as $category => $subCategories) {
		foreach($subCategories as $subCategory){	
			if (strpos($metaString, $subCategory) !== false) {
				$matchCategories[$category][] = $subCategory;				
			}
		}
		if (!isset($matchCategories[$category]) && strpos($metaString, $category) !== false) {
			$matchCategories[$category] = [];
		}
	}
	
	$matchMostCategory = [];
	$pevCategory = "";
	foreach($matchCategories as $category => $subCategories){
		if(empty($matchMostCategory)){
			$matchMostCategory = [$category => $subCategories];
		}else{
			if(count($subCategories) > count($matchMostCategory[$pevCategory])){
				$matchMostCategory = [$category => $subCategories];
				$pevCategory = $category;
			}
		}		
	}
	
	return $matchMostCategory;
}

function file_get_contents_curl($url)
{
	$timeout = 30; 
    $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0'; 

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function getFeatures($websiteCategory){
	include(__DIR__ . "/constant.php");
	$matchFeatures = [];
	$features = json_decode(APP_FEATURES, 1);
	foreach(array_keys($websiteCategory) as $category){
		$matchFeatures[$category] = isset($features[$category]) ? $features[$category] : [];
	}
	return $matchFeatures;
}

