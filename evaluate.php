<?php
	require 'libs/LIB_http.php';
	ob_start("ob_gzhandler");
	header("Content-Type: application/json; charset=UTF-8");
	
	//coolpc原價屋
	$product = array();
	$web_page = http_get("https://query.yahooapis.com/v1/public/yql?q=select%20*%20%20from%20html%20where%20url%3D%22http%3A%2F%2Fcoolpc.com.tw%2Fevaluate.php%22&format=json&diagnostics=true&callback=", $ref= "");
	$web_json = json_decode($web_page["FILE"], true);
	$web_json = $web_json["query"]["results"]["body"]["form"][1]["center"]["table"][2]["tbody"][1]["tr"];
	$web_json_count = count($web_json);
	$name_j = 0;
	for($count=0;$count<$web_json_count;$count++)
	{
		$temp = $web_json[$count]["td"];
		$product[$name_j]["name"] = $temp[1];
		$products = array();
		$optgroup = $temp[2]["select"]["optgroup"];
		$optgroup_len = count($optgroup);
		$content_k = 0;
		for($optgroup_i=0;$optgroup_i<$optgroup_len;$optgroup_i++)
		{
			$option = @$optgroup[$optgroup_i]["option"];
			$option_label = @$optgroup[$optgroup_i]["label"];
			
			if(@$option[0]["content"] !== null)
			{
				$option_len = count($option);
				for($option_i=0;$option_i<$option_len;$option_i++)
				{
					$products[$content_k]["label"] = $option_label;
					$products[$content_k]["content"] = $option[$option_i]["content"];
					$content_k += 1;
				}
			}
			else
			{
				if($option["content"] !== null)
				{
					$products[$content_k]["content"] = $option["content"];
					$content_k += 1;
				}
			}
		}
		$product[$name_j]["products"] = $products;
		$name_j += 1;
	}
	
	file_put_contents("quotation.json", json_encode($product));
?>