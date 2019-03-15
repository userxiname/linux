<!DOCTYPE html>
<html>
<?php
	$appid = "XinLu-csci571a-PRD-016e557a4-b1f7e7db";
	$rooturl = "http://phpapp-hw6.azurewebsites.net";

	if (isset($_POST['query'])){
		$keyword = $_POST['keyword'];
		$category = $_POST['category'];
		$condition_new = isset($_POST['condition_new']);
		$condition_used = isset($_POST['condition_used']);
		$condition_unspecified = isset($_POST['condition_unspecified']);
		$local_pickup = isset($_POST['local_pickup']);
		$free_shipping = isset($_POST['free_shipping']);
		$enable_nearby_search = isset($_POST['enable_nearby_search']);
		$text_miles = $_POST['distance'];
		$here_code = $_POST['herecode'];
		$text_zipcode = $_POST['postcode_input'];
		$zipcode_option = $_POST['zipcode_option'];
		$miles = $_POST['miles'];
		$place = $_POST['place'];
		$succ = true;

		$error_miles = '';
		if ($enable_nearby_search && !preg_match("/^[0-9]*$/", $miles)){
			$error_miles = 'Distance is invalid';
			$succ = false;
		}

		$error_zipcode = '';
		if ($zipcode_option=='zipcode' && isset($_POST['postcode_input']) && !preg_match("/^[0-9]{5}$/", $text_zipcode)){
			echo preg_match("/^[0-9]{5}$/", $zipcode);
			$error_zipcode = 'Zipcode is invalid';
			$succ = false;
		}

		if ($succ && isset($_POST['query'])){
			$pagesize = 20;
			$itemIdx = 0;
			$url = "http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=";
			$url .= $appid;
			$url .= '&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&paginationInput.entriesPerPage=' . $pagesize;
			$url .= '&keywords=' . $keyword;
			if ($category != 'all'){
				$url .= '&categoryId=' . $category;
			}
			if ($enable_nearby_search){
				if ($zipcode_option == 'here'){
					$url .= '&buyerPostalCode=' . $here_code;
				}else{
					$url .= '&buyerPostalCode=' . $text_zipcode;
				}
				$url .= '&itemFilter(' . $itemIdx . ').name=MaxDistance&itemFilter(' . $itemIdx . ').value=' . $miles;
				$itemIdx ++;
			}
			$url .= '&itemFilter(' . $itemIdx . ').name=FreeShippingOnly&itemFilter(' . $itemIdx . ').value=' . ($free_shipping ? 'true' : 'false');
			$itemIdx ++;
			$url .= '&itemFilter(' . $itemIdx . ').name=LocalPickupOnly&itemFilter(' . $itemIdx . ').value=' . ($local_pickup ? 'true' : 'false');
			$itemIdx ++;
			$url .= '&itemFilter(' . $itemIdx . ').name=HideDuplicateItems&itemFilter(' . $itemIdx . ').value=true';
			$itemIdx ++;
			$condiIdx = 0;
			if ($condition_new){
				$url .= '&itemFilter(' . $itemIdx . ').value(' . $condiIdx . ')=New';
				$condiIdx ++;
			}
			if ($condition_used){
				$url .= '&itemFilter(' . $itemIdx . ').value(' . $condiIdx . ')=Used';
				$condiIdx ++;
			}
			if ($condition_unspecified){
				$url .= '&itemFilter(' . $itemIdx . ').value(' . $condiIdx . ')=Unspecified';
				$condiIdx ++;
			}
			if ($condiIdx > 0){
				$url .= '&itemFilter(' . $itemIdx . ').name=Condition';
			}
			$itemIdx ++;
			$url = str_replace(" ", "%20", $url);
			$content = file_get_contents($url);
			$query_content = rawurlencode($content);
			$json_content = json_decode($content, true);
			$item_count = $json_content['findItemsAdvancedResponse'][0]['searchResult'][0]['@count'];
			$items = $json_content['findItemsAdvancedResponse'][0]['searchResult'][0]['item'];
		}
	}
	if (isset($_GET['detail'])){
		$get_itemid = $_GET['itemID'];
		$keyword = $_GET['keyword'];
		$category = $_GET['category'];
		$condition_new = $_GET['condition_new'];
		$condition_used = $_GET['condition_used'];
		$condition_unspecified = $_GET['condition_unspecified'];
		$local_pickup = $_GET['local_pickup'];
		$free_shipping = $_GET['free_shipping'];
		$enable_nearby_search = $_GET['enable_nearby_search'];
		$zipcode_option = $_GET['place'];
		$text_miles = $_GET['miles'];
		$text_zipcode = $_GET['zipcode'];

		$detail_url = 'http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON';
		$detail_url .= '&appid=' . $appid;
		$detail_url .= '&siteid=0&version=967';
		$detail_url .= '&ItemID=' . $get_itemid;
		$detail_url .= '&IncludeSelector=Description,Details,ItemSpecifics';
		$detail_content = file_get_contents($detail_url);
		$detail_content = rawurlencode($detail_content);

		$similar_url = 'http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0';
		$similar_url .= '&CONSUMER-ID=' . $appid;
		$similar_url .= '&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD';
		$similar_url .= '&itemId=' . $get_itemid;
		$similar_url .= '&maxResults=8';
		$similar_content = file_get_contents($similar_url);
		$similar_content = rawurlencode($similar_content);
	}
?>



<head>
	<title>Homework 6</title>
	<style type="text/css">
		a {
			color: black;
			text-decoration: none;
		}

		.show_msg{
			width: 800px;
			height: 17px;
			background-color: #EEEEEE;
			margin: 0 auto;
			top: 80px;
			position: relative;
			text-align: center;
			border-color: #CBCBCB;
			border-style: solid;
			border-width: thin;
		}

		.homepage_form{
			width: 603px;
			height: 290px;
			margin: 0 auto;
			top: 50px;
			position: relative;
			border: 3px;
			border-color: #cbcbcb;
			border-style: solid;
			background-color: #F9F9F9;
		}

		.homepage_title{
			font-style: italic;
			font-size: 32px;
			margin: 0 auto;
			width: 603px;
			height: 40px;
		}

		.items_table{
			width: 1200px;
			margin: 0 auto;
			top: 70px;
			position: relative;
		}

		.table_list{
			font-size: 14px;
		}

		.item_detail_div{
			width: 680px;
			height: 30px;
			text-align: center;
			font-weight: bolder;
			font-size: 30px;
			margin: 0 auto;
			top: 65px;
			position: relative;
		}

		.detail_table{
			margin: 0 auto;
			top: 70px;
			position: relative;
			width: 800px;
		}

		.table_class{
			margin: 0 auto;
			width: 800px;
		}

		.space2left{
			padding-left: 10px;
		}

		.iframe_div{
			margin: 0 auto;
			top: 100px;
			position: relative;
			width: 800px;
			color: red;
		}

		.pagediv{
			background-color: red;
			margin: 0 auto;
			width: 950px;
			top: 150px;
			position: relative;
			text-align: left;

		}

		.togglingtitle{
			width: 300px;
			margin: 0 auto;
			height: 20px;
			color: grey;
			top: 100px;
			position: relative;
			text-align: center;
		}

		.togglingtpic{
			width: 300px;
			margin: 0 auto;
			height: 20px;
			color: grey;
			top: 110px;
			position: relative;
			text-align: center;
		}

		.togglingtitle2{
			width: 300px;
			margin: 0 auto;
			height: 20px;
			color: grey;
			top: 130px;
			position: relative;
			text-align: center;
		}

		.togglingtpic2{
			width: 300px;
			margin: 0 auto;
			height: 20px;
			color: grey;
			top: 140px;
			position: relative;
			text-align: center;
		}

		.ifdiv1{
			width: 1000px;
			height: 0px;
			margin: 0 auto;
			top: 130px;
			position: relative;
			text-align: center;
			border: 1px;
		}

		.ifdiv2{
			width: 780px;
			height: 0px;
			margin: 0 auto;
			top: 165px;
			position: relative;
			text-align: center;
			border-style: solid;
  			border-width: 2px;
  			border-color: #DDDDDD;
  			display: none;
		}

		.bot_blank{
			width: 780px;
			height: 0px;
			margin: 0 auto;
			top: 180px;
			position: relative;
		}

	</style>
	<script type="text/javascript">
		var jobj;
		var none_similar = false;
		var query_item_count = 0;
		var rooturl = '<?php echo $rooturl;?>';
		var appid = '<?php echo $appid;?>';
		var error_nonitem = '';
		var error_miles = '';
		var error_zipcode = '';
		var query_items = '';
		function getPostalCode(){
			var url = 'http://ip-api.com/json/';
			var xmlhttp = new XMLHttpRequest();
			var result = {};
			result["succ"] = false;
			xmlhttp.overrideMimeType("application/json");
			xmlhttp.open("GET", url, false);
			try{
				xmlhttp.send();
				result["succ"] = true;
				return JSON.parse(xmlhttp.responseText)['zip'];
			}catch(e){
				return '90007';
			}
		}

		function formClear(){
			window.location = "<?php echo $rooturl;?>";
			document.getElementById('keyword').value = '';
			document.getElementById('category')[0].selected = true;
			document.getElementById('condition_new').checked = false;
			document.getElementById('condition_used').checked = false;
			document.getElementById('condition_unspecified').checked = false;
			document.getElementById('local_pickup').checked = false;
			document.getElementById('free_shipping').checked = false;
			document.getElementById('enable_nearby_search').checked = false;
			document.getElementById('miles').value = 10;
			document.getElementById('zipcodetxt').value = '';
			document.getElementById('zipcodetxt').required = false;
			document.getElementById('place1').checked = true;
			disableSearchNearby();
		}

		function changeEnable(){
			if (document.getElementById('enable_nearby_search').checked){
				document.getElementById('miles').disabled = false;
				document.getElementById('milestext').style.opacity = 1;
				document.getElementById('place1').disabled = false;
				document.getElementById('place2').disabled = false;
				placeChange();
			}else{
				disableSearchNearby();
			}
		}

		function disableSearchNearby(){
			document.getElementById('miles').disabled = true;
			document.getElementById('milestext').style.opacity = 0.5;
			document.getElementById('here').style.opacity = 0.5;
			document.getElementById('place1').disabled = true;
			document.getElementById('place2').disabled = true;
			document.getElementById('zipcodetxt').disabled = true;
			document.getElementById('zipcodetxt').required = false;
		}

		function placeChange(){
			if (document.getElementById('place1').checked){
				document.getElementById('zipcodetxt').disabled = true;
				document.getElementById('zipcodetxt').required = false;
				document.getElementById('here').style.opacity = 1;
			}
			if (document.getElementById('place2').checked){
				document.getElementById('zipcodetxt').disabled = false;
				document.getElementById('zipcodetxt').required = true;
				document.getElementById('here').style.opacity = 0.5;
			}
		}

		function restoreForm(){
			document.getElementById('keyword').value = '<?php echo $keyword ?>';
			document.getElementById('category').value = '<?php echo $category ?>';
			if ('<?php echo $condition_new ?>' == '1'){
				document.getElementById('condition_new').checked = true;
			}
			if ('<?php echo $condition_used ?>' == '1'){
				document.getElementById('condition_used').checked = true;
			}
			if ('<?php echo $condition_unspecified ?>' == '1'){
				document.getElementById('condition_unspecified').checked = true;
			}
			if ('<?php echo $local_pickup ?>' == '1'){
				document.getElementById('local_pickup').checked = true;
			}
			if ('<?php echo $free_shipping ?>' == '1'){
				document.getElementById('free_shipping').checked = true;
			}
			if ('<?php echo $enable_nearby_search ?>' == '1'){
				document.getElementById('enable_nearby_search').checked = true;
			}
			document.getElementById('miles').value = '<?php echo $text_miles ?>';
			document.getElementById('zipcodetxt').value = '<?php echo $text_zipcode ?>';
			if ('<?php echo $zipcode_option?>' == 'here'){
				document.getElementById('place1').checked = true;
			}else{
				document.getElementById('place2').checked = true;
			}
			changeEnable();
		}

		// resolve query result
		function queryDetails(){
			var qd = '<?php echo $query_content; ?>';
			qd = decodeURIComponent(qd);
			queryResult = JSON.parse(qd);
			query_item_count = queryResult['findItemsAdvancedResponse'][0]['searchResult'][0]['@count'];
			if (query_item_count == '' || query_item_count == undefined){
				query_item_count = 0;
			}else{
				query_item_count = parseInt(query_item_count);
			}
			if (query_item_count == 0){
				error_nonitem = 'No Records has been found';
			}
			query_items = queryResult['findItemsAdvancedResponse'][0]['searchResult'][0]['item'];
		}

		function checkErrors(){
			error_miles = '<?php echo $error_miles;?>';
			error_zipcode = '<?php echo $error_zipcode;?>';
			var err_msg = '';
			if (error_miles != ''){
				err_msg = error_miles;
			}else if (error_zipcode != ''){
				err_msg = error_zipcode;
			}else if (error_nonitem != ''){
				err_msg = error_nonitem;
			}
			if (err_msg != ''){
				document.write('<div class="show_msg">');
				document.write(err_msg);
				document.write('</div>');
			}
		}

		function createQueryList(){
			var paras = '?';
			paras += 'detail=true';
			paras += '&keyword=' + '<?php echo str_replace(" ",  "%20", $keyword); ?>';
			paras += '&category=' + '<?php echo $category; ?>';
			paras += '&condition_new=' + '<?php echo $condition_new; ?>';
			paras += '&condition_used=' + '<?php echo $condition_used; ?>';
			paras += '&condition_unspecified=' + '<?php echo $condition_unspecified; ?>';
			paras += '&local_pickup=' + '<?php echo $local_pickup; ?>';
			paras += '&free_shipping=' + '<?php echo $free_shipping; ?>';
			paras += '&enable_nearby_search=' + '<?php echo $enable_nearby_search; ?>';
			paras += '&place=' + '<?php echo $zipcode_option; ?>';
			paras += '&miles=' + '<?php echo $text_miles; ?>';
			paras += '&zipcode=' + '<?php echo $text_zipcode; ?>'; 

			document.write('<div class="table_list" id="table_list">');
			document.write('<table class="items_table" border="1">');
			document.write('<tr>');
			document.write('<th>Index</th>');
			document.write('<th>Photo</th>');
			document.write('<th>Name</th>');
			document.write('<th>Price</th>');
			document.write('<th>Zip code</th>');
			document.write('<th>Condition</th>');
			document.write('<th>Shipping Option</th>');
			document.write('</tr>');

			for (var idx = 0; idx < query_item_count; idx++){
				var detail_url = query_items[idx]['viewItemURL'][0];
				var app_itemID = '&itemID=' + query_items[idx]['itemId'][0];
				document.write('<tr>');
				document.write('<td>' + (idx+1) + '</td>');
				document.write('<td width="100px"><img src="' + query_items[idx]['galleryURL'][0] + '"></td>');
				document.write('<td><a href="' + rooturl + paras + app_itemID + '">' + query_items[idx]['title'][0] + '</a></td>');
				document.write('<td>$' + query_items[idx]['sellingStatus'][0]['currentPrice'][0]['__value__'] + '</td>');
				if (query_items[idx]['postalCode'] == undefined || query_items[idx]['postalCode'][0] == ''){
					document.write('<td>N/A</td>');
				}else{
					document.write('<td>' + query_items[idx]['postalCode'][0] + '</td>');
				}
				if (query_items[idx]['condition'] == undefined || query_items[idx]['condition'][0]['conditionDisplayName'] == undefined || query_items[idx]['condition'][0]['conditionDisplayName'][0] == ''){
					document.write('<td>N/A</td>');
				}else{
					document.write('<td>' + query_items[idx]['condition'][0]['conditionDisplayName'][0] + '</td>');
				}

				if (query_items[idx]['shippingInfo'] == undefined || query_items[idx]['shippingInfo'][0]['shippingServiceCost'] == undefined || query_items[idx]['shippingInfo'][0]['shippingServiceCost'][0]['__value__'] == undefined || query_items[idx]['shippingInfo'][0]['shippingServiceCost'][0]['__value__'] == ''){
					document.write('<td>N/A</td>');
				}else if (query_items[idx]['shippingInfo'][0]['shippingServiceCost'][0]['__value__'] == '0.0'){
					document.write('<td>Free Shipping</td>');
				}else{
					document.write('<td>$' + query_items[idx]['shippingInfo'][0]['shippingServiceCost'][0]['__value__'] + '</td>');
				}
				document.write("</tr>");
			}
			document.write('</table>');
			document.write('</div>');
		}
		function drawItemDetails(){
			var st = '<?php echo $detail_content; ?>';
			st = decodeURIComponent(st);
			jobj = JSON.parse(st);
			if (jobj['Errors'] != undefined){
				document.write('<div class="item_detail_div">Item Details</div>');
				var error_msg = jobj['Errors'][0]['ShortMessage'];
				document.write('<div style="height:300px; width:1000px; text-align:center; top:300px; margin:0 auto; position:relative;"><b>'+error_msg+'</b></div>')
				return;
			}
			document.write('<div class="item_detail_div">Item Details</div>');
			document.write('<div class="detail_table"><table border="1" class="table_class">');
			var detail_list = jobj['Item']['ItemSpecifics']['NameValueList'];
			var img_tmp = jobj['Item']['PictureURL'][0];
			if (img_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Photo</b></div></td>');
				document.write('<td><div class="space2left"><img width="100px" src="' + img_tmp + '"></div></td></tr>');
			}
			var title_tmp =  jobj['Item']['Title'];
			if (title_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Title</b></div></td>');
				document.write('<td><div class="space2left">' + title_tmp + '</div></td></tr>');
			}
			var subtitle_tmp =  jobj['Item']['Subtitle'];
			if (subtitle_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Subtitle</b></div></td>');
				document.write('<td><div class="space2left">' + subtitle_tmp + '</div></td></tr>');
			}
			var price_tmp =  jobj['Item']['CurrentPrice']['Value'];
			if (price_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Price</b></div></td>');
				var price_name = jobj['Item']['CurrentPrice']['CurrencyID']
				document.write('<td><div class="space2left">' + price_tmp + ' ' + price_name + '</div></td></tr>');
			}
			var location_tmp =  jobj['Item']['Location'];
			if (location_tmp != ''){
				var postalcode_tmp = jobj['Item']['PostalCode'];
				if (postalcode_tmp != ''){
					postalcode_tmp = ', ' + postalcode_tmp;
				}
				document.write('<tr><td><div class="space2left"><b>Location</b></div></td>');
				document.write('<td><div class="space2left">' + location_tmp + postalcode_tmp + '</div></td></tr>');
			}
			var seller_tmp =  jobj['Item']['Seller']['UserID'];
			if (seller_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Seller</b></div></td>');
				document.write('<td><div class="space2left">' + seller_tmp + '</div></td></tr>');
			}
			var return_tmp =  jobj['Item']['ReturnPolicy']['ReturnsAccepted'];
			if (return_tmp != ''){
				document.write('<tr><td><div class="space2left"><b>Return Policy(US)</b></div></td>');
				document.write('<td><div class="space2left">' + return_tmp + '</div></td></tr>');
			}
			for (var i=0; i<detail_list.length; i++){
				document.write('<tr>');
				document.write('<td><div class="space2left"><b>' + detail_list[i]['Name'] + '</b></div></td>');
				document.write('<td><div class="space2left">');
				var str_tmp = '';
				for (var j=0; j<detail_list[i]['Value'].length; j++){
					str_tmp += detail_list[i]['Value'][j] + '<br>';
				}
				document.write(str_tmp);
				document.write('</div></td>');
				document.write('</tr>');
			}
			document.write('</table></div>');
		}

		function beforeFormSubmit(){
			if (document.getElementById('place1').checked){
				document.getElementById('herecode').value = getPostalCode();
			}else{
				document.getElementById('zipcode_option').value = 'zipcode';
			}
			document.getElementById('distance').value = document.getElementById('miles').value;
			document.getElementById('postcode_input').value = document.getElementById('zipcodetxt').value;
		}

		function changeArrows(name){
			var setDiv1 = false;
			var setDiv2 = false;
			if (document.getElementById(name).src.includes('up')){
				document.getElementById(name).src = "http://csci571.com/hw/hw6/images/arrow_down.png";
				if (name == 'arrow1'){
					document.getElementById('iframed1').setAttribute("style", "width:0; height:0; border:0; border:none");
					document.getElementById('ifdiv1').style.height = "0";
					document.getElementById('ifdiv1').style.display = "none";
					document.getElementById('seller_title').innerHTML = 'click to show seller message';
				}else{
					document.getElementById('iframed2').setAttribute("style", "width:0; height:0; border:0; border:none");
					document.getElementById('ifdiv2').style.height = "0px";
					document.getElementById('ifdiv2').style.width = "780px";
					document.getElementById('ifdiv2').style.display = "none";
					document.getElementById('similar_title').innerHTML = 'click to show similar items';
					document.getElementById('bot_blank').style.height = "0px";
				}
			}else{
				document.getElementById(name).src = "http://csci571.com/hw/hw6/images/arrow_up.png";
				if (name == 'arrow1'){
					document.getElementById('iframed1').setAttribute("style", "color:red; width:100%; height:100%; border:1; border:none; margin: 0 auto;");
					document.getElementById('ifdiv1').style.display = "block";
					document.getElementById('seller_title').innerHTML = 'click to hide seller message';
					setDiv1 = true;
				}else{
					document.getElementById('iframed2').setAttribute("style", "color:red; width:100%; height:100%; border:1; border:none; margin: 0 auto;");
					if (none_similar){
						document.getElementById('ifdiv2').style.height = "38px";
						document.getElementById('ifdiv2').style.width = "968px";
					}else{
						document.getElementById('ifdiv2').style.height = "278px";
						setDiv2 = true;
					}
					document.getElementById('ifdiv2').style.display = "block";
					document.getElementById('similar_title').innerHTML = 'click to hide similar items';
					document.getElementById('bot_blank').style.height = "35px";
				}
			}
			if (setDiv1){
				document.getElementById('ifdiv1').style.height = setIframeHeight(document.getElementById('iframed1')) + 'px';
				document.getElementById('iframed1').setAttribute("style", "width:100%; height:100%; border:2; border:none; margin: 0 auto;");
			}
			if (setDiv2){
				document.getElementById('ifdiv2').style.height = '279px';
				document.getElementById('iframed2').setAttribute("style", "width:100%; height:100%; border:2; border:none; margin: 0 auto;");
			}
		}

		function setIframeHeight(iframe) {
			var height = '0';
    		if (iframe) {
        		var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
        		if (iframeWin.document.body) {
        			height = iframeWin.document.body.scrollHeight;
        			height += 30;
        		}
    		}
    		return height.toString();
		}

		function setIframeContent() {
			var content = '';
			if (jobj['Item'] != undefined && jobj['Item']['Description'] != undefined){
				content = jobj['Item']['Description'];
			}
			if (content == '' || content == undefined){
				var msg = "No Seller Message found.";
				if (content != ''){
					msg = jobj['Errors'][0]['ShortMessage'];
				}
				content = '<html><body><div style="height:20px; background-color:#DDDDDD; width:950px; margin: 0 auto; text-align:center;"><b>'+msg+'</b></div></body></html>';
			}
			document.getElementById('iframed1').setAttribute("srcdoc", content);
		}

		function setSimilarContent(){
			var similar_content = '<?php echo $similar_content; ?>';
			similar_content = decodeURIComponent(similar_content);
			similar_content = JSON.parse(similar_content);
			var items = similar_content['getSimilarItemsResponse']['itemRecommendations']['item'];
			var item_count = items.length;
			var content = '';
			if (item_count == 0){
				content = '<html><body><div style="height:20px; border-style:solid; border-width:1px; border-color:#DDDDDD; width:950px; margin: 0 auto; text-align:center;"><b>No Similar Item found.</b></div></body></html>';
				none_similar = true;
			}else{
				var computed_width = item_count*220;
				content += '<html><script>function jumpPage(url){window.top.location.href = url;}<\/script><body style="width:'+computed_width+'px"><div style="height=200px; width='+computed_width+'px; margin:0 auto; text-align:center; background-color:red; display:block;">';

				var similar_redirect = '?';
				similar_redirect += 'detail=true';
				similar_redirect += '&keyword=' + '<?php echo $keyword; ?>'.replace(' ', '%20');
				similar_redirect += '&category=' + '<?php echo $category; ?>';
				similar_redirect += '&condition_new=' + '<?php echo $condition_new; ?>';
				similar_redirect += '&condition_used=' + '<?php echo $condition_used; ?>';
				similar_redirect += '&condition_unspecified=' + '<?php echo $condition_unspecified; ?>';
				similar_redirect += '&local_pickup=' + '<?php echo $local_pickup?>';
				similar_redirect += '&free_shipping=' + '<?php echo $free_shipping?>';
				similar_redirect += '&enable_nearby_search=' + '<?php echo $enable_nearby_search?>';
				similar_redirect += '&place=' + '<?php echo $zipcode_option?>';
				similar_redirect += '&miles=' + '<?php echo $text_miles?>';
				similar_redirect += '&zipcode=' + '<?php echo $text_zipcode?>';
				similar_redirect += '&itemID=';

				for (var i=0; i<item_count; i++){
					content += '<div style="float:left; width:220px; height:271px; display:block;">';
					content += '<div><img src="'+ items[i]['imageURL'] +'"><p></div>';
					content += '<div><a href="" onclick="jumpPage(\'';
					content += rooturl+similar_redirect+items[i]['itemId'];
					content += '\')">';
					content += items[i]['title'] + '</a><p></div>';
					content += '<div><b>$' + items[i]['buyItNowPrice']['__value__'] + '</b><p></div>';
					content += '</div>';

				}
				content += '</div></body></html>';
			}
			document.getElementById('iframed2').setAttribute("srcdoc", content);
		}

	</script>
</head>

<body>
	<form id="formid" class="homepage_form" action="<?php echo $rooturl ?>" method="POST" onsubmit="beforeFormSubmit()">
		<div class="homepage_title">
			<div style="width: 210px; height: 28px; margin: 0 auto;">Product Search</div>
			<hr width="590px" align="center" style="border-width: 1px; border-style: inset; color: #cbcbcb;">
		</div>
		<div class="keyword-wrap" style="width: 570px; height: 19px; margin: 0 auto; top: 19px; position: relative; font-size: 16px;">
			<div style="font-weight: bolder; float: left; height: 19px; width: 65px">Keyword</div>
			<div style="height: 19px; float: left;"><input id="keyword" type="text" name="keyword" size="21" height="18" required></div>
		</div>
		<div class="keyword-wrap" style="width: 570px; height: 18px; margin: 0 auto; top: 36px; position: relative; font-size: 16px; font-weight: bolder;">
			Category
			<select id="category" name="category">
				<option value="all">All Categories</option>
				<option value="550">Art</option>
				<option value="2984">Baby</option>
				<option value="267">Books</option>
				<option value="11450">Clothing, Shoes & Accessories</option>
				<option value="58058">Computers/Tablets &
Networking</option>
				<option value="26395">Health & Beauty</option>
				<option value="11233">Music</option>
				<option value="1249">Video Games & Consoles</option>
			</select>
		</div>
		<div class="condition-wrap" style="width: 570px; height: 18px; margin: 0 auto; top: 54px; position: relative; font-size: 16px;">
			<div style="font-weight: bolder; float: left; height: 18px;">Condition</div>
			<div style="float: left; width: 80px; height: 18px; left: 20px; position: relative;"><input type="checkbox" name="condition_new" id="condition_new">New</div>
			<div style="float: left; width: 70px; height: 18px; left: 20px; position: relative;"><input type="checkbox" name="condition_used" id="condition_used">Used</div>
			<div style="float: left; width: 100px; height: 18px; left: 20px; position: relative;"><input type="checkbox" name="condition_unspecified" id="condition_unspecified">Unspecified</div>
		</div>
		<div class="shipping-wrap" style="width: 570px; height: 18px; margin: 0 auto; ; top: 72px; position: relative; font-size: 16px;">
			<div style="font-weight: bolder; height: 18px; float: left;">Shipping options</div>
			<div style="float: left; width: 120px; height: 18px; left: 53px; position: relative;"><input type="checkbox" name="local_pickup" id="local_pickup">Local Pickup</div>
			<div style="float: left; width: 120px; height: 18px; left: 75px; position: relative;"><input type="checkbox" name="free_shipping" id="free_shipping">Free Shipping</div>
		</div>
		<div class="nearby-wrap" style="width: 570px; height: 19px; margin: 0 auto; ; top: 90px; position: relative; font-size: 16px;">
			<div style="float: left; height: 19px;"><input type="checkbox" id="enable_nearby_search" name="enable_nearby_search" onclick="changeEnable()"></div>
			<div style="float: left; font-weight: bolder; height: 19px; left: 2px; position: relative; width: 193px">Enable Nearby Search</div>
			<div style="float: left; height: 19px; width: 20px;"><input type="text" id="miles" name="miles" size="4" height="18" placeholder="10" value="10" disabled></div>
			<div id="milestext" style="float: left; font-weight: bolder; width: 100px; height: 19px; left: 33px; position: relative; opacity: 0.5;">miles from</div>
			<div id="here" style="float: left; height: 19px; left: 7px; position: relative; opacity: 0.5;"><input type="radio" id="place1" name="place" value="here" checked disabled onclick="placeChange()"><label for="here">Here</label></div>
		</div>
		<div class="nearby2-wrap" style="width: 570px; height: 19px; margin: 0 auto; top: 90px; position: relative; font-size: 16px;">
			<div style="float: left; height: 19px; left: 340px; position: relative;"><input type="radio" id="place2" name="place" value="zipcode" disabled  onclick="placeChange()"><input type="text" id="zipcodetxt" name="zipcodetxt" size="17" placeholder="zip code" disabled></div>
		</div>
		<input type="hidden" id="query" name="query" value="true" >
		<input type="hidden" id="herecode" name="herecode" value="90007">
		<input type="hidden" id="distance" name="distance" value="10">
		<input type="hidden" id="postcode_input" name="postcode_input" value="">
		<input type="hidden" id="zipcode_option" name="zipcode_option" value="here">
		<div class="buttons-wrap" style="width: 570px; height: 19px; margin: 0 auto; top: 100px; position: relative; font-size: 16px;">
			<div style="height: 19px; left: 210px; position: relative;">
				<input type="submit" name="submit" value="Search">
				<input type="button" name="clear" value="Clear" onclick="formClear()">
			</div>
		</div>
	</form>
	<?php
	if (isset($_POST['query']) || isset($_GET['detail'])){
		echo "<script type='text/javascript'>restoreForm();</script>";
	}
	?>

	<?php
		if ($succ && isset($_POST['query'])){
			echo "<script type='text/javascript'>queryDetails();</script>";
			echo "<script type='text/javascript'>checkErrors();</script>";
		}

		if (!$succ && isset($_POST['query'])) {
			echo "<script type='text/javascript'>checkErrors();</script>";
		}
	?>

	<?php
		if ($succ && $item_count > 0){
			echo "<script type='text/javascript'>createQueryList();</script>";
		}
	?>

	<?php echo '<script>getPostalCode();</script>'; ?>

 	<?php
		if (isset($_GET['detail']) && isset($detail_content)){
			echo '<script>drawItemDetails()</script>';
			echo '<div class="togglingtitle"><p id="seller_title">click to show seller message</p></div>';
			echo '<div class="togglingtpic"><img id="arrow1" src="http://csci571.com/hw/hw6/images/arrow_down.png" onclick="changeArrows(\'arrow1\')" width="50px" height="30px"></div>';
			echo '<div id="ifdiv1" class="ifdiv1"><iframe id="iframed1" style="width:0; height:0; border:0; border:none"></iframe></div>';
			echo '<script>setIframeContent();</script>';
			echo '<div class="togglingtitle2"><p id="similar_title">click to show similar items</p></div>';
			echo '<div class="togglingtpic2"><img id="arrow2" src="http://csci571.com/hw/hw6/images/arrow_down.png" onclick="changeArrows(\'arrow2\')" width="50px" height="30px"></div>';
			echo '<div id="ifdiv2" class="ifdiv2"><iframe id="iframed2" style="width:0; height:0; border:0; border:none"></iframe></div>';
			echo '<div class="bot_blank" id="bot_blank"></div>';
			echo '<script>setSimilarContent();</script>';
		}
	?>
</body>
</html>
