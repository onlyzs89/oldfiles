<html>
	<head>
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script type="text/javascript"> 
			var sW,sH;
			window_load();
			//ウィンドウサイズ変更時に更新
			window.onresize = window_load;
			//サイズの表示
			function window_load() {
				sW = window.innerWidth;
				sH = window.innerHeight;
				
				$(function() {
					$('#img').width(sW-15);
				});
			}
		</script>
		<?php
		
			$client_id = "cloud_vision";
			$client_secret = "EPlZ5djjDTfwtHK1jX2PtpP0T5jVN9dlui5PxUaYx2I=";
			
			
			function getAccessToken($client_id, $client_secret, $grant_type = "client_credentials", $scope = "http://api.microsofttranslator.com"){
				$ch = curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_URL => "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/",
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query(array(
						"grant_type" => $grant_type,
						"scope" => $scope,
						"client_id" => $client_id,
						"client_secret" => $client_secret
						))
					));
				return json_decode(curl_exec($ch));
			}
			
			$access_token = getAccessToken($client_id,$client_secret)->access_token;
			
			function Translator($access_token, $params){
				$ch = curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_URL => "https://api.microsofttranslator.com/v2/Http.svc/Translate?".http_build_query($params),
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER => true,
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ". $access_token),
					));
				preg_match('/>(.+?)<\/string>/',curl_exec($ch), $m);
				return $m[1];
			}
		?>

		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Image Analysis</title>
	</head>
	<body>
		<header id="image_header">
			<p id="header_text">
				<img src="./logo.png" id="logo" />
				Image Analysis based on Google Cloud Vision API
			</p>
		</header>
		<div class="image_request">
			<form action="" method="post">
				<p class="content_p">
					画像リンク: <input type="text" name="link" size=28px>
					<input type="submit" value="分析" id="ana_btn">
				</p>
			</form>
		</div>
		<div class="image_response">
			<?php
				$api_key = "AIzaSyAEcsMYlzf2qbI_NoDQOeUMf3QLjTVwlus" ;

				//$referer = "https://testpro.com/" ;

				$image_path = $_POST["link"];

				$json = json_encode( array(
					"requests" => array(
						array(
							"image" => array(
								"content" => base64_encode( file_get_contents( $image_path ) ) ,
							) ,
							"features" => array(
							  //  array(
							  //      "type" => "FACE_DETECTION" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
							  //  array(
							  //      "type" => "LANDMARK_DETECTION" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
							  //  array(
							  //      "type" => "LOGO_DETECTION" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
								array(
									"type" => "LABEL_DETECTION" ,
							  //      "maxResults" => 3 ,
								) ,
							  //  array(
							  //      "type" => "TEXT_DETECTION" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
							  //  array(
							  //      "type" => "SAFE_SEARCH_DETECTION" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
							  //  array(
							  //      "type" => "IMAGE_PROPERTIES" ,
							  //      "maxResults" => 3 ,
							  //  ) ,
							) ,
						) ,
					) ,
				) ) ;

				$curl = curl_init() ;
				curl_setopt( $curl, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=" . $api_key ) ;
				curl_setopt( $curl, CURLOPT_HEADER, true ) ; 
				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" ) ;
				curl_setopt( $curl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ) ) ;
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;
				if( isset($referer) && !empty($referer) ) curl_setopt( $curl, CURLOPT_REFERER, $referer ) ;
				curl_setopt( $curl, CURLOPT_TIMEOUT, 15 ) ;
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $json ) ;
				$res1 = curl_exec( $curl ) ;
				$res2 = curl_getinfo( $curl ) ;
				curl_close( $curl ) ;

				$json = substr( $res1, $res2["header_size"] ) ;

				// 保存用
				//$filename = './json.json';
				//if (!file_exists($filename)) {
				//    touch($filename);
				//} else {
				//    echo ('すでにファイルが存在しています。file name:' . $filename);
				//}
				//if (!file_exists($filename) && !is_writable($filename)
				//    || !is_writable(dirname($filename))) {
				//    echo "書き込みできないか、ファイルがありません。",PHP_EOL;
				//    exit(-1);
				//}

				//$fp = fopen($filename,'a') or dir('ファイルを開けません');

				//fwrite($fp, $json);

				//fclose($fp);

				//decode from json
				$arr = json_decode($json, true);
				$results=array();
				
				if(isset($image_path) && $results) echo "<p class='content_p'>分析結果</p>";
				array_to_tags_recursive($arr);
				
				function array_to_tags_recursive($arr)
				{
					global $results;
					foreach($arr as $key=>$val) {
						if ($key === 'description'){
							$results[]=$val;
						}
						else if (is_array($val))
							array_to_tags_recursive($val);
					}
					
				}
				
				if(isset($image_path) && !$results) echo "<p class='content_p' style='color: #ff4d4d'>画像リンクは正しくありません</p>";
				
				foreach($results as $key=>$val){
					echo "<li>".Translator($access_token, array('text' =>$val, 'to' => 'ja', 'from' => 'en'))."</li>";
				}
			?>
		</div>
			<?php 
				if(isset($image_path) && $results) echo "<br /><img id='img' src=".$image_path.">";
			?>
		<footer id="image_footer">
			<p id="footer_text">
				Copyright &copy; 2016 onlyzs All Rights Reserved.
			</p>
		</footer>
	</body>
</html>
