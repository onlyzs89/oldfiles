<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>Natural Language Analysis</title>
		<?php
			function array_to_tags_recursive($arr,&$results)
			{
				foreach($arr as $key=>$val) {
					if ($key === 'content'){
						$results["content"][]=$val;
					}
					else if ($key === 'tag'){
						$results["tag"][]=$val;
					}
					else if ($key === 'label'){
						$results["label"][]=$val;
					}
					else if ($key === 'type'){
						$results["type"][]=$val;
					}
					else if ($key === 'name'){
						$results["name"][]=$val;
					}
					else if ($key === 'wikipedia_url'){
						$results["wikipedia_url"][]=$val;
					}
					else if ($key === 'polarity'){
						$results["polarity"][]=$val;
					}
					else if ($key === 'magnitude'){
						$results["magnitude"][]=$val;
					}
					else if (is_array($val)){
						array_to_tags_recursive($val,$results);
					}
				}
				
			}
		?>
	</head>
	<body>
		<header id="lang_header">
			<p id="header_text">
				<img src="./logo.png" id="logo" />
				Natural Language Analysis based on Google API
			</p>
		</header>
		<div class="lang_request">
			<form action="" method="post">
				<p class="content_p">
					<textarea name="content" cols=90 rows=8><?php echo isset($_POST["content"])?$_POST["content"]:""; ?></textarea>
					<br />
					<input name="text" type="submit" value="テキスト分析" id="ana_btn">
					<input name="entities" type="submit" value="エンティティ分析" id="ana_btn">
					<input name="sentiment" type="submit" value="センチメント分析" id="ana_btn">
				</p>
			</form>
		</div>
		<div class="lang_response">
			<?php
				$api_key = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" ;

				// $referer = "https://analang.com/" ;
				
				$text=$_POST["content"];
				
				
				
				if(isset($text)){
					
					if(isset($_POST["text"])){
						$json = json_encode( array(
							"document" => array(
								"type" => "PLAIN_TEXT",
								"content" => $text,
							) ,
							"features" => array(
								"extract_syntax" => True,
							),
							"encodingType" => "UTF8",

						) ) ;
						$link_par="annotateText";
						
					}else if(isset($_POST["entities"])){
						$json = json_encode( array(
							"document" => array(
								"type" => "PLAIN_TEXT",
								"content" => $text,
							) ,
							"encodingType" => "UTF8",

						) ) ;
						$link_par="analyzeEntities";
						
					}else if(isset($_POST["sentiment"])){
						$json = json_encode( array(
							"document" => array(
								"type" => "PLAIN_TEXT",
								"content" => $text,
							) ,

						) ) ;
						$link_par="analyzeSentiment";
						
					}

					$curl = curl_init() ;
					curl_setopt( $curl, CURLOPT_URL, "https://language.googleapis.com/v1beta1/documents:".$link_par."?key=" . $api_key ) ;
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
					$header = substr( $res1, 0, $res2["header_size"] ) ; 

					$arr = json_decode($json, true);
					
					$results_text=array();
					$results_entities=array();
					$results_sentiment=array();
					
					echo "<p class='content_p'>分析結果</p>";
					
					
					if(isset($_POST["text"])){
						array_to_tags_recursive($arr["tokens"],$results_text);
					}else if(isset($_POST["entities"])){
						array_to_tags_recursive($arr["entities"],$results_entities);
					}else if(isset($_POST["sentiment"])){
						array_to_tags_recursive($arr["documentSentiment"],$results_sentiment);
					}
					
					
					if(!empty($results_text)){
						echo "<table><tr>";
						foreach($results_text["content"] as $res){
							echo "<td>".$res."</td>";
						}
						echo "</tr><tr>";
						foreach($results_text["tag"] as $res){
							echo "<td>".$res."</td>";
						}
						echo "</tr><tr>";
						foreach($results_text["label"] as $res){
							echo "<td>".$res."</td>";
						}
						echo "</tr></table>";
					}
					if(!empty($results_entities)){
						echo "<table><tr>";
						foreach($results_entities["type"] as $res){
							echo "<td>".$res."</td>";
						}
						echo "</tr><tr>";
						foreach($results_entities["name"] as $res){
							echo "<td>".$res."</td>";
						}
						echo "</tr></table>";
					}
					if(!empty($results_sentiment)){
						foreach($results_sentiment["polarity"] as $res){
							echo $res."\n";
						}
						foreach($results_sentiment["magnitude"] as $res){
							echo $res."\n";
						}
					}
					
				}
				
			?>
		</div>
		<footer id="lang_footer">
			<p id="footer_text">
				Copyright &copy; 2016 onlyzs All Rights Reserved.
			</p>
		</footer>
	</body>
</html>
