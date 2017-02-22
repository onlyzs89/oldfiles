<?php
	function array_to_tags_recursive($arr, &$results)
	{
		global $results;
		foreach($arr as $key=>$val) {
			if($key === "timestamps"){
				$results[]=$val;
			}
			else if (is_array($val))
				array_to_tags_recursive($val, $results);
		}
		
	}
	
	$stream = fopen('output.wav','r+');
	$data=fread($stream, filesize('output.wav'));
	
	$username="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
	$password="xxxxxxxxxxxxxxxxxxxxxxxxxxxx";
	$api_url="https://stream.watsonplatform.net/speech-to-text/api/v1/";
	$header=array("Content-Type: audio/wav");
	
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, $api_url.'/recognize?model=ja-JP_BroadbandModel&timestamps=true&word_alternatives_threshold=0.9&continuous=true' );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_USERPWD, $username.":".$password );
	curl_setopt( $curl, CURLOPT_BINARYTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HEADER, true ); 
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $curl, CURLOPT_TIMEOUT, 60 );
	
	$res1 = curl_exec( $curl );
	$res2 = curl_getinfo( $curl );
	curl_close( $curl );
	
	$json = substr( $res1, $res2["header_size"] );
	//echo $json;
	
	$arr = json_decode($json, true);
	$results=array();
	array_to_tags_recursive($arr, $results);
	
	$mecab = new MeCab_Tagger();
	//$str="";
	$tags_voice=array();
	foreach($results as $sentence){
		foreach($sentence as $word){
			//$str.=$word[0];
			$tim1=(int)$word[1];
			$tim2=(int)$word[2];
			$nodes=$mecab->parseToNode($word[0]);
			foreach ($nodes as $n){
				if($n->getPosId()>=36 && $n->getPosId()<=48){
					//echo $n->getSurface();
					$tags_voice[$tim1][]=$word[0];
					if($tim1!=$tim2)$tags_voice[$tim2][]=$word[0];
				}
			}
		}
	}
	echo json_encode($tags_voice);
