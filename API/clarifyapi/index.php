<!--Author: onlyzs-->
<!--2016/07/22-->

<html>
	<head>
		<?php
			$client_id="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
			$client_secret="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
			
			//Retrieve an Access Token
			function retrieve_access_token($client_id, $client_secret){
				$access=http_build_query(array(
					"client_id" => $client_id,
					"client_secret" => $client_secret,
					"grant_type" => "client_credentials",
				));
				
				$api_url="https://api.clarifai.com/v1/token/";
				
				$curl = curl_init();
				curl_setopt( $curl, CURLOPT_URL, $api_url );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $access );
				$res=json_decode(curl_exec($curl));
				curl_close( $curl ) ;
				
				return $res;
			}
			
			//get tags from array
			function array_to_tags_recursive($arr, &$results){
				foreach($arr as $key=>$val) {
					if ($key === 'classes'){
						$results=$val;
					}
					else if (is_array($val))
						array_to_tags_recursive($val, $results);
				}
				
			}
			
			
			//request for tags
			function request_tags($access_token, $data_url){
				$data=http_build_query( array(
					"url" => $data_url,
				));
				$api_url="https://api.clarifai.com/v1/tag/";
				$header=array("Authorization: Bearer ".$access_token);
				
				$curl = curl_init();
				curl_setopt( $curl, CURLOPT_URL, $api_url );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_HEADER, true ); 
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				$res1 = curl_exec( $curl );
				$res2 = curl_getinfo( $curl );
				curl_close( $curl );

				$json = substr( $res1, $res2["header_size"] );
				$header = substr( $res1, 0, $res2["header_size"] );
				
				$arr = json_decode($json, true);
				
				return $arr;
			}
			
		?>
		
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Image &amp; Video Analysis</title>
	</head>
	
	<body>
		<header id="image_header">
			<p id="header_text">
				<img src="./logo.png" id="logo" />
				Image &amp; Video Analysis based on Clarifai API
			</p>
		</header>
		<div class="image_request">
			<form action="" method="post">
				<p class="content_p">
					Link: <input type="text" name="link" size=30px value=<?php echo isset($_POST["link"])?$_POST["link"]:""; ?>>
					<input type="submit" value="Analyze" id="ana_btn">
				</p>
			</form>
		</div>
		<div class="image_response">
			<?php
				$access_token=retrieve_access_token($client_id, $client_secret)->access_token;
				if(isset($_POST["link"])){
					$data_url = $_POST["link"];
					$results=array();
					$arr=request_tags($access_token, $data_url);
					array_to_tags_recursive($arr, $results);
					foreach($results as $key=>$res){
						if(is_array($res)){//video
							echo "<h4　class='content_p'>".$key."-".($key+1)." Sec</h4>";
							foreach($res as $tags){
								echo "<li>".$tags."</li>";
							}
						}
						else//image
							echo "<li>".$res."</li>";
					}
				}
			?>
		</div>
		<footer id="image_footer">
			<p id="footer_text">
				Copyright &copy; 2016 onlyzs All Rights Reserved.
			</p>
		</footer>
	</body>
</html>
