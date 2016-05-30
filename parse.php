<?php
	$data = json_decode(file_get_contents("quotation.json"), true);
	$len = count($data);
	
	$file_name = "cpu.csv";
	$str = "";
	for($index=0;$index<$len;$index++) {
		if($data[$index]["name"] === "處理器 CPU") {
			$len_product = count($data[$index]["products"]);
			for($index_cpu=0;$index_cpu<$len_product;$index_cpu++) {
				if(!empty($data[$index]["products"][$index_cpu]["label"])) {
					$label = $data[$index]["products"][$index_cpu]["label"];
					
					$socket = explode(" ", $label);

					$brand_name = "";
					if(strpos($socket[0], "Intel") !== false) {
						$brand_name = "Intel美商英特爾";
						if(empty($socket[2])) {
							$socket = str_replace("腳位Haswell-E(僅適用X99)", "", $socket[1]);
						}
						else {
							if(strpos($socket[2], "腳位(Server級.桌機通用)") !== false)
								$socket = str_replace("腳位(Server級.桌機通用)", "", $socket[2]);
							else if(strpos($socket[2], "腳位") !== false) {
								$socket = trim($socket[2], "四代");
								$socket = trim($socket, "腳位");
								$socket = trim($socket, "腳位(僅適用");
								$socket = trim($socket, "腳位(Server級、X99通用)");
							}
							else {
								$socket = trim($socket[1], "Skylake六代");
								$socket = trim($socket, "腳位");
							}
						}
					}
					else {
						$brand_name = "AMD美商超微";
						if(empty($socket[2])) {
							$socket = str_replace("【高時脈、大快取】", "", $socket[1]);
						}
						else {
							$socket = $socket[1];
						}
					}
				
					$info = explode(" ", $data[$index]["products"][$index_cpu]["content"]);
					
					if(strpos($info[0], "絕搭組合") !== false) {
						continue;
					}
					
					$info = str_replace("★", "", $info);
					$info = str_replace("↘", "", $info);
					
					if(strpos($info[3], "$") !== false) {
						$price = explode("$", $info[3]);
						$price_len = count($price);
						if($price_len > 2)
							$price = $price[2];
						else
							$price = $price[1];
						
						$label = explode("【", $info[2]);
						$label = $label[0];

						if(strpos($label, "組合優惠") !== false)
							continue;
						if(strpos($label, "(Haswell-E)") !== false || trim($label) === "" || strpos($info[2], "靜音風扇") !== false) {
							if(strpos("【", $info[1]) !== false) {
								$label = explode("【",$info[1]);
								$label = $label[0];
							}
							else {
								$label = $info[1];
							}
						}
						else {
							if(strpos($info[2], "4M快取") !== false) {
								$label = explode("【",$info[1]);
								$label = $label[0];
							}
						}
					}
					else if(strpos($info[2], "$") !== false) {
						$price = explode("$", $info[2]);
						$price_len = count($price);
						if($price_len > 2)
							$price = $price[2];
						else
							$price = $price[1];
						
						$label = explode("【", $info[1]);
						$label = $label[0];
					}
					else if(strpos($info[4], "$") !== false) {
						$price = explode("$", $info[4]);
						$price_len = count($price);
						if($price_len > 2)
							$price = $price[2];
						else
							$price = $price[1];
						
						$label = explode("【", $info[3]);
						$label = $label[0];
						if($label === "V3" || $label === "V5" || $label === "V4")
							$label = $info[2] . $label;
						if(strpos($info[3], "組合優惠") !== false)
							continue;
						if(strpos($info[3], "HITMAN") !== false || strpos($info[3], "1M快取") !== false ||
							strpos($info[3], "4M快取") !== false) {
							$label = explode("【", $info[1]);
							$label = $label[0];
						}
					}
					else if(strpos($info[5], "$") !== false) {
						$price = explode("$", $info[5]);
						$price_len = count($price);
						if($price_len > 2)
							$price = $price[2];
						else
							$price = $price[1];
						
						$label = explode("【", $info[4]);
						$label = $label[0];
						
						if(strpos($info[4], "組合優惠") !== false)
							continue;
						if(strpos($info[4], "代理盒裝") !== false) {
							$label = explode("【", $info[3]);
							$label = $info[2] . $label[0];
						}
						if(strpos($info[4], "HITMAN") !== false) {
							$label = $info[1];
						}
					}
					else {
						$price = explode("$", $info[6]);
						$price_len = count($price);
						if($price_len > 2)
							$price = $price[2];
						else
							$price = $price[1];
						
						$label = explode("【", $info[5]);
						$label = $label[0];
						
						if(strpos($info[5], "代理盒裝") !== false) {
							$label = explode("【", $info[3]);
							$label = $info[2] . $label[0];
						}
					}
				}
				else {
					$info = explode(" ", $data[$index]["products"][$index_cpu]["content"]);
					if($info[0] === "Intel") {
						$brand_name = "Intel美商英特爾";
						$label = explode("【", $info[3]);
						$label = $info[2] . $label[0];
						$socket = "1155";
						$price = $info[4];
					}
					else {
						$brand_name = "AMD美商超微";
						$label = explode("【", $info[2]);
						$label = $info[1] . $label[0];
						$socket = "AM1";
						$price = $info[3];
					}
					
					$price = str_replace("$", "", $price);
					//var_dump($data[$index]["products"][$index_cpu]["content"]);
				}
				
				$str .=  $price . "," . $brand_name . "," . $label . "," . $socket . "\r\n";
			}
			
			file_put_contents($file_name, trim($str));
		}

		if($data[$index]["name"] === "記憶體 RAM") {
			$file_name = "ram.csv";
			$len_product = count($data[$index]["products"]);
			for($index_ram=0;$index_ram<$len_product;$index_ram++) {
				if(!empty($data[$index]["products"][$index_ram]["label"])) {
					$label = $data[$index]["products"][$index_ram]["label"];
					$for_server = explode(" ", $label);
					if($for_server[0] === "伺服器專用記憶體") {
						$info = explode(" ", $data[$index]["products"][$index_ram]["content"]);
						$brand_name = $info[0];
						$capcity = $info[1];
						$spec = str_replace("低電壓", "", $info[2] . $info[3]);
						$spec = str_replace("/Hynix顆粒【超微認證100%相容C232/236晶片】,", "", $spec);
						
						$info = explode(",", $data[$index]["products"][$index_ram]["content"]);
						$info = str_replace("★\n", "", $info[1]);
						$price = str_replace("$", "", $info);
					}
					else {
						
					}
					
					
				}
				else {
					
				}
			}
		}
		
	}
?>