<?
ini_set('display_errors', 0);
date_default_timezone_set("Asia/Tokyo");

// em0.si by weepjp
// 2023-06-01

$tit = '#em0si';

$sql_path = './siem0.db';
$sql_time = @date("Ymd\THis", @filemtime( $sql_path )).'JST';
$dev_time = @date("Ymd\THis", @filemtime( 'index.php' )).'JST';




$get1 = @htmlentities($_GET['get'],ENT_QUOTES,mb_internal_encoding());                        // ?get= (ただのテスト)
$req1 = @htmlentities(substr($_SERVER['REQUEST_URI'],1), ENT_QUOTES, mb_internal_encoding()); // 現在地URLからドメイン以外の部分を割り出す
$srv1 = @htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, mb_internal_encoding());           // 現在地URLからドメイン部分を割り出す


$ext0 = @explode('?', $req1);      // ?区切り(クエリ)      /$ext0[0]?$ext0[1]?$ext0[2]
$ext1 = @explode('.', $ext0[0]);   // .区切り(拡張子)      /$ext1[0].$ext1[1].$ext1[2]
$ext2 = @explode('/', $ext1[0]);   // /区切り(ディレクトリ)/$ext2[0]/$ext2[1]/$ext2[2]

$exts  = $ext1[1]; //雑に拡張子と判定されたもの


// base-62 encode and decode
// 62進数にして画像パスの文字数を削減させるバカな作戦
$xchar = array_merge(range('0','9'), range('a', 'z'), range('A', 'Z'));
function encode($number, $xchar){
	$result = '';
	$base = count($xchar);

	while($number > 0){
	  $result = $xchar[ fmod($number, $base) ] . $result;
	  $number = floor($number / $base);
	}
	return ($result == '' ) ? 0 : $result;
}
//デコード
function decode($str, $xchar){
	$result = 0;
	$base = count($xchar);
	$table = array_flip($xchar);
	$digit = array_reverse(preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY));
	foreach($digit as $i => $value){
	  if(!isset($table[$value])) return false;
	  $result += pow($base, $i) * $table[$value];
	}
	return $result;
}

// エラー webp
$err_webp = 'UklGRlQFAABXRUJQVlA4WAoAAAACAAAAHwAAHwAAQU5JTQYAAAD/////AABBTk1G+gAAAAAAAAAAAB8AAB8AAGQAAAJWUDgg4gAAABAGAJ0BKiAAIAA/rc7ZZrwyqyeoCquANYlpABRgX4Nq5+AZYDLR/AK3G/COZ1/3HmAIVkNpzbLRAADLBxrCZvgJ8s+93VaXwD/N0thdb2Bb0/jqdeX8g4xpEAKBfJZrAhS9xSdTBewLlGZHyPOhBCS6xPZBTkTiUJMxCVvUwEtxU2MssYCOySlGP/rl11/LgE6DYi9TX5KXorWBlGSXHSVS6ihc7tAvdlbHFXm+PpJ5K1tnZSh6xAbGD2FiD5cx/qENo3MhSH55YZ89yQ02IAkvBEH4JieDWiMgjVGryvzQAABBTk1GAgEAAAAAAAAAAB8AAB8AADIAAABWUDgg6gAAALQFAJ0BKiAAIAA/rc7eaAKAgAADWJaTuHoDEgjsLbNqRa7cAmgXmCZpN15+Act55gDT/DL4AADefVeii1hXm0kvDu5FBHPDtQl4lzUvA/ik7tInGb+1tpaId0GPX2B2lUGHuyTWc9H0ViiFlyekoSbSbsWzTVgQvkRVXCpfJHPSirwm5cUVHzy12VGdJb3hMoZPTPEUSGJKLOjHN+/k+kX5X+aNEyvsRRiFWcztUkJmiG0R3XNsYrWdBWZc/aF7Lv+ldOV8svJY4CPp6tK2kcAWBqQALab1E89PrhnwbcbcNUNLz3oV9cAAAEFOTUYGAQAAAAAAAAAAHwAAHwAAMgAAAFZQOCDuAAAAtAUAnQEqIAAgAD+t0OBogoCAAANYlpABRgX4Oyl3ZcgBlh/juoAa9r+C8tT/APQA6AAg998AAOhu9CjtdmerPp8TEfvQsiq9RF+TzJlhrvxpSZ9HxTQAo9FmiaK1kg3wdOxluxNXzb+X9E7Hgws8oUiFA/6NKfAmoSYy8s76/8ICuwxfrgzYVP5vJD/bNoy+yuPG7vf0WRRwS8bhLOX0nHDs3VfiznBWHaU6qWmoyPUJ8Gv9X3RBFahcofMtl4fGF1pYEIvhl+PlJETerfOeJ2peQg4ZBaMSXVeCHguckcXLz2+iRA7LmzhnZAIAAEFOTUYEAQAAAAAAAAAAHwAAHwAAFAAAAFZQOCDsAAAAlAUAnQEqIAAgAD+txNNhAwGAwAADWJaQAUYF+Dagfju9/9oDLW0UnXuPwzlvfMYOTbi8rAAA+iN6NBOWbFot6k9DO9p2Y/s2qxBxqaofCJRdg99YaYMYDakbbRUWkMpg8GCOOEWTbyQ5VuvqGqeCWLzxbAoYfQKbitYOpLG0QmP7XowwxIgB7k9DF0UY+pOPHCemF1NydLFBPZoLPH49EmTBlp7dxnhK2Ca83sIGar5YA66DpJUhGrW+3lRN+A5o+6Ea08jedv5iuNiO0Ltruw1Yk8TOnCEwwjYD36g4GKcXmnaVING1zs2QAABBTk1GAgEAAAAAAAAAAB8AAB8AABQAAABWUDgg6gAAALQFAJ0BKiAAIAA/rc7faAKAgAADWJaQAUYF+Dsq91yZeFGbXwPzX9gue8/gHoVlF2hAkiruAADVt3JO9mwayD+U8JZEBKzg891dXeGx+mtunxm5Pq1HXYjBOR9r2mvJDAfahCSwnMg4bDSzU6f6mbrFLMjIMX5bMrbYXTP1lZkss6mRfbH4W1KkbBrHhB3hGPdzlCbLKv7/YPbYpQKyW/mXOhxffzjODyEFU63D3AISZ0lfnd5wjhBiHteKpb+NEWYhkLFMZlBaH3aE0uEDXbUiGwHcMJ+OCPBsuqZ4thp9utx4dT1xx2kwAA==';


// エラー png
$err_webp = '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';



function err_msg($txt){ // エラーメッセージテンプレート
	if(empty($txt)){ $txt = 'Unknown'; }
	$result  = "";
	$result .= '<html><head><title>'.$tit.' - ERROR: '.$txt.'</title>';
	$result .= '<style>';
	$result .= '.wrap{width:480px;margin-right:auto;margin-left:auto;margin-top:32px;}';
	$result .= '.box{background:#fff;width:450px;margin-top:32px;}';
	$result .= 'body{background:#eee;} ';
	$result .= 'th,td{border:solid 1px; ';
	$result .= 'padding:3px; background:#fff} ';
	$result .= 'table{border-collapse:collapse;}</style>';
	$result .= '<link rel="icon" href="https://em0.si/favicon32.png" type="image/png" />';
	$result .= '<link rel="shortcut icon" href="https://em0.si/favicon32.png" type="image/png" /></head><body>';
	$result .= '<div class="wrap"><div class="box"><h1>ERROR: '.$txt.'</h1></div>';
	$result .= '<div class="box"><a href="/"><img src="/favicon32.png" title=":em0si:" alt=":em0si:"></a></div>';
	$result .= '<p style="text-align:center; font-size:2em;">by weepjp</p>';
	$result .= '</div></body></html>';
	return $result;
}




if(is_file($sql_path)){// database 存在します




	// 秒から時間単位(?日?時間?分?秒)に変換する    
	function convt($seconds, $lang){

		$m = 60;                    // 分
		$h = pow($m, 2);            // 時
		$d = pow($m, 2) * 24;       // 日
		$y = pow($m, 2) * 24 * 365; // 年

		$time = round($seconds);

		if($lang == 'ja'){
			$syear   = '年';
			$sday    = '日';
			$shour   = '時間';
			$sminute = '分';
			$ssecond = '秒'; 

		}else{
			$syear   = 'year ';
			$sday    = 'd ';
			$shour   = 'h ';
			$sminute = 'm ';
			$ssecond = 's ';
		}

		if($time >= $y){

			// 年
			$year = floor($time / $y);
			$result = $time % $y;

			// 日
			$day = floor($result / $d);
			$result = $result % $d;

			// 時
			$hour = floor($result / $h);
			$result = $result % $h;

			// 分/秒
			$minute = floor($result / $m);
			$result = $result % $m;

			$resu2 = '';
			if($year  != 0){ $resu2 .= $year.$syear; }
			//if($day   != 0){ $resu2 .= $day.$sday; }
			//if($hour  != 0){ $resu2 .= $hour.$shour; }
			//if($minute!= 0){ $resu2 .= $minute.$sminute; }
			//if($result!= 0){ $resu2 .= $result.$ssecond; }
			return $resu2;

		}else if($time >= $d){

			// 日
			$day = floor($time / $d);
			$result = $time % $d;

			// 時
			$hour = floor($result / $h);
			$result = $result % $h;

			// 分/秒
			$minute = floor($result / $m);
			$result = $result % $m;

			$resu2 = '';
			if($day   != 0){ $resu2 .= $day.$sday; }
			//if($hour  != 0){ $resu2 .= $hour.$shour; }
			//if($minute!= 0){ $resu2 .= $minute.$sminute; }
			//if($result!= 0){ $resu2 .= $result.$ssecond; }
			return $resu2;

		}else if($time >= $h){

			// 時
			$hour = floor($time / $h);
			$result = $time % $h;

			// 分/秒
			$minute = floor($result / $m);
			$result = $result % $m;

			$resu2 = '';
			if($hour  != 0){ $resu2 .= $hour.$shour; }
			//if($minute!= 0){ $resu2 .= $minute.$sminute; }
			//if($result!= 0){ $resu2 .= $result.$ssecond; }
			return $resu2;

		}else if($time >= $m){

			// 分/秒
			$minute = floor($time / $m);
			$result = $time % $m;

			$resu2 = '';
			if($minute!= 0){ $resu2 .= $minute.$sminute; }
			//if($result!= 0){ $resu2 .= $result.$ssecond; }
			return $resu2;

		}else{
			//return   $time . $ssecond;
			return   '1m';
		}
	}


	$sql_times = strtotime("now") - strtotime($sql_time);
	$dev_times = strtotime("now") - strtotime($dev_time);

	$sql_timea = convt($sql_times,'');
	$dev_timea = convt($dev_times,'');

	$sql_time  = ''.$sql_timea.' ago';
	$dev_time  = ''.$dev_timea.' ago';

	function top_msg($tit, $txt, $sql_time, $dev_time){ // トップメッセージテンプレート
		if(empty($tit)){ $tit = 'Unknown'; }
		if(empty($txt)){ $txt = 'Unknown'; }
		$result  = "";
		$result .= '<html><head><title>'.$tit.'</title>';
		$result .= '<style>';
		$result .= '.wrap{width:480px;margin-right:auto;margin-left:auto;margin-top:32px;}';
		$result .= '.box{background:#fff;width:450px;margin:12px;}';
		$result .= 'body{background:#eee;} a{color:#00f;}a{color:#00f;}';
		$result .= 'th,td{border:solid 1px; ';
		$result .= 'padding:3px; background:#fff} ';
		$result .= 'table{border-collapse:collapse;}</style>';
		$result .= '<link rel="icon" href="https://em0.si/favicon32.png" type="image/png" />';
		$result .= '<link rel="shortcut icon" href="https://em0.si/favicon32.png" type="image/png" />';
		$result .= '<meta name="viewport" content="width=device-width,initial-scale=1" /></head><body>';
		$result .= '<div class="wrap"><div class="box"><h1>'.$tit.'</h1></div>';
		$result .= '<div class="box"><a href="/"><img src="/favicon64.png"';
		$result .= ' title=":em0si: (This cat\'s name is ZER0NYAN)" alt=":em0si: (This cat\'s name is ZER0NYAN)"></a></div>';
		$result .= ''.$txt.'';
		$result .= '<p style="text-align:center; font-size:1em;"><a href="hhttps://github.com/weepjp/zer0nyan" target="_blank">Zer0nyan</a> ©2023 weepjp<br>Released under the MIT license.</p>';
		$result .= '<div class="box" style="text-align:center;">';
		$result .= 'emoji list update:'.$sql_time.' / dev update:'.$dev_time.'</div></div></body></html>';
		return $result;
	}
	
	

	
	function des_msg($imgurl, $rowi, $que1, $rowa, $rowp, $naming, $rowl, $rowo, $daturi, $iformat){ // 情報表示テンプレ
			$result  = '<div class="box"><a href="'.$perurl.'">';
			$result .= '<img src="'.$imgurl.'" title=":'.$naming.':" alt=":'.$naming.':" border="0"></a></div>';
			$result .= '<div class="box"><table><tbody>';
			$result .= '<tr><td>id</td><td>'.$rowi.'</td></tr>';
			$result .= '<tr><td>xid</td><td><a href="'.$perurl.'">'.$que1.'</a></td></tr>';
			$result .= '<tr><td>author</td><td>'.$rowa.'</td></tr>';
			$result .= '<tr><td>package</td><td>'.$rowp.'</td></tr>';
			$result .= '<tr><td>name</td><td>';
			$result .= '<textarea onclick="this.focus(); this.select();" wrap="on"';
			$result .= ' style="font-size:1.4em; border:none; overflow:hidden; font-weight:bold; width:320px; height:32px;">';
			$result .= '​:'.$naming.':​</textarea></p></td></tr>';
			$result .= '<tr><td>format</td><td>'.$iformat.'</td></tr>';
			$result .= '<tr><td>licence or ref URL</td><td>'.$rowl.'</td></tr>';
			$result .= '<tr><td>entry day</td><td>'.$rowo.'</td></tr>';
			$result .= '</tbody></table></div>';
			
			$result .= '<div class="box"><p>json (<a href="/'.$que1.'.json" target="_blank">json file</a>)<br>';
			$result .= '<textarea onclick="this.focus(); this.select();" wrap="on" style="width:460px; height:32px;">';
			$result .= '{"'.$naming.'":"'.$daturi.'"}</textarea></p></div>';
		return $result;
	}
	
	
	
	function dus($uri){ // Data URI Scheme ファイルチェッカー
			$data = @explode(',', $uri);
			
			switch ($data[0]){
				case 'data:image/webp;base64':
					$result  = '.webp';
				break;
			
				case 'data:image/gif;base64':
					$result  = '.gif';
				break;
			
				case 'data:image/png;base64':
					$result  = '.png';
				break;
			
				case 'data:text/plain;base64':
					$result  = '.txt';
				break;
			
				default:
					$result  = '';
				break;
			}
		return $result;
	}
	
	
	
	// SQLite ぶん回す処理。
	$db = new SQLite3($sql_path);
	$sql0 = "SELECT * FROM `em0si` ORDER BY `p` ASC, `a` ASC, `n` ASC;";
	$res = $db->query($sql0);
	$json_temp  = '{';
	$json_temp2 = '{';
	$list_temp  = '<div class="box">';
	
	$lis2 = 0;
	while( $row = $res->fetchArray() ) {
		if(!empty($row['a']) and !empty($row['p']) and !empty($row['n']) and empty($row['x'])){
			
			$rowi = $row['i']; $rowm = $row['m']; $rowa = $row['a'];
			$rowp = $row['p']; $rown = $row['n']; $rowd = $row['d'];
			$que1 = encode($rowi, $xchar);
			$ifor = dus($rowd);
			
			if($rowm == 1){
				$naming = $rown;
				$imgurl = 'https://'.$srv1.'/'.$que1.$ifor;
				$perurl = 'https://'.$srv1.'/'.$que1.'';
				$daturi = $imgurl;
			}else{
				$naming = $rowp.'_'.$rown;
				$imgurl = 'https://'.$srv1.'/'.$rowa.'/'.$rowp.'/'.$rown.$ifor;
				$perurl = 'https://'.$srv1.'/'.$que1.'';
				$daturi = $imgurl;
			}
			
			
			$json_temp  .= '"'.$naming.'":"'.$daturi.'",';
			$json_temp2 .= '"'.$naming.'":"'.$rowd.'",';
			$list_temp  .= '<a href="'.$perurl.'"><img src="'.$imgurl.'" title=":'.$naming.':" alt=":'.$naming.':" border="0"></a>';
			$lis2++;
		}
	}
	$json_temp  .= '"em0si":"https://em0.si/favicon32.png","weepjp":"https://nostr.weep.me/a/random.webp"}';
	$json_temp2 .= '"em0si":"data:image/webp;base64,UklGRhYCAABXRUJQVlA4IAoCAAAwDACdASogACAAPpE+mEilo6IhMBgIALASCWwAtvqDzU5kPAbavcAbwxz5fsN/tj6VVHAx15gB2gGtB/SdVe/vfIX9AewN+nP+39Wb1V+g3+vC3dgy9SwN0Jsgjg/EgbnwrH5ILQLiHPbgAP7/MrLt8YunsRyDw4LSS35MsG2lmioKviy7r9Zf/COKN8hvK5d8VM0p9G4X3TF5gD3//81fRIWAg7O+N2jT4XJ+SCKvyK//t89A7Oqj8IbAzp2b4J0b9WT6hxhLcv/w019+Qh5uWVFu/UHZr86zu2bo/dH7+7XQjEd6E2hdHIYyfBdEgyb2fD86yfjkNcHywoAE/HgR//AYn6fNQvjJtpnh5XtaAc2NhoZjTYskcGdr/WqN+QNuPeqUb9Q3BJk/iUrfmwWUlNM2f144hz8tvr2Bf6KM5t08qUZz8B2WqzVENMtSLFrbndy6Ur3Mp5F4A0yYN0D1BqboMaoaIuO/8fAR09VOIYuiHMD9ZvSxIgFTmZdQH+r8tT3BPDvoc47ZnrFPYQ47YLyTCaimUdSgRW/y5aQs5CcXnvk4/wFPt1k1YCfsFEKzufPkfJSJFFrWVwmuwgLun1B2/O77xLPub0MXGcA+b2rTBe0khkIiv0l1X6i5TEnH/7aAr2ft5HDR9/LGPuAd9uq0uzH//ENPOIoSMdYnQjj/dQRzkyyAAAA=","weepjp":"https://nostr.weep.me/a/random.webp"}';
	$list_temp  .= '<p style="text-align:center; font-size:2em;">'.$lis2.' emojis</p></div>';
	
	
	
	
	
	


	switch ($req1){
		
		
		case '_a.json':
			header('Cache-Control: public,must-revalidate,max-age=60');
			header('Content-Type: text/plain');
      		header('Access-Control-Allow-Origin: *');
			echo $json_temp; exit;
		break;
		
		
		case '_b.json':
			header('Cache-Control: public,must-revalidate,max-age=60');
			header('Content-Type: text/plain');
			header('Access-Control-Allow-Origin: *');
			echo $json_temp2; exit;
		break;
	}
		

	
	
	
	
	if(!empty($req1)){

		$db = new SQLite3($sql_path);
		
		
		
		
		if(!empty($ext2[1]) and !empty($ext2[2])){
		
			$sql1  = "SELECT * FROM `em0si` WHERE a='$ext2[0]' AND p='$ext2[1]' AND n='$ext2[2]';";
			$row1  = $db->querySingle($sql1, true);
			$img  = $row1['d'];
			$ico = @explode(',', $img);
			
			$rowi = $row1['i']; $rowm = $row1['m']; $rowa = $row1['a'];
			$rowp = $row1['p']; $rown = $row1['n']; $rowd = $row1['d'];
			$rowl = $row1['l']; $rowo = $row1['o']; $rowx = $row1['x'];
			$ifor = dus($rowd);
			
			if($rowm == 1){
				$naming = $rown;
				$imgurl = 'https://'.$srv1.'/'.$que1.$ifor;
				$perurl = 'https://'.$srv1.'/'.$que1.'';
				$daturi = $imgurl;
			}else{
				$naming = $rowp.'_'.$rown;
				$imgurl = 'https://'.$srv1.'/'.$rowa.'/'.$rowp.'/'.$rown.$ifor;
				$perurl = 'https://'.$srv1.'/'.$que1.'';
				$daturi = $imgurl;
			}
			
			if($exts=='webp'){
				
				
				if(!empty($img) and $ico[0] == 'data:image/webp;base64' and empty($row1['x']) ){
					header('Cache-Control: public,must-revalidate,max-age=6000');
					header('Content-Type: image/webp');
					header('Access-Control-Allow-Origin: *');
					echo base64_decode($ico[1]); exit;
				}else{
					header('Cache-Control: public,must-revalidate,max-age=60');
					header('HTTP', true, 404);
					header('Content-Type: image/webp');
					header('Access-Control-Allow-Origin: *');
					echo base64_decode($err_webp); exit;
				}
				
			}elseif($exts=='png'){
				
				if(!empty($img) and $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
					header('Cache-Control: public,must-revalidate,max-age=6000');
					header('Content-Type: image/png');
					header('Access-Control-Allow-Origin: *');
					echo base64_decode($ico[1]); exit;
				}else{
					header('Cache-Control: public,must-revalidate,max-age=60');
					header('HTTP', true, 404);
					header('Content-Type: image/png');
					header('Access-Control-Allow-Origin: *');
					echo base64_decode($err_png); exit;
				}
				
				
			}elseif($exts=='json'){
				
				if(!empty($img) and $ico[0] == 'data:image/webp;base64' or $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
					header('Cache-Control: public,must-revalidate,max-age=60');
					header('Content-Type: text/plain');
					header('Access-Control-Allow-Origin: *');
					echo '{"'.$naming.'":"'.$daturi.'"}'; exit;
				}else{
					header('Cache-Control: public,must-revalidate,max-age=60');
					header('HTTP', true, 404);
					header('Content-Type:  text/plain');
					header('Access-Control-Allow-Origin: *');
					echo json_encode(new stdClass); exit;
				}
				
			}else{

				
				
				if(empty($rowd) or !empty($rowx)){
					header('HTTP', true, 404);
					echo err_msg('What the file??'); exit;
				}
				
				$que1 = encode($rowi, $xchar);
				$tit .= ' - '.$naming;
				
				$msg  = des_msg($imgurl, $rowi, $que1, $rowa, $rowp, $naming, $rowl, $rowo, $daturi, $ifor);
				echo top_msg($tit, $msg, $sql_time, $dev_time); exit;
			
			
			}
			
		}else{
			
			if(!empty($ext2[0])){
			
				$sql3 = "SELECT * FROM `em0si` WHERE n='$ext2[0]';"; // 名前で呼び出しバージョン
				$row3 = $db->querySingle($sql3, true);
				$img  = $row3['d'];
				$ico = @explode(',', $img);
					
				$rowi = $row3['i']; $rowm = $row3['m']; $rowa = $row3['a'];
				$rowp = $row3['p']; $rown = $row3['n']; $rowd = $row3['d'];
				$rowl = $row3['l']; $rowo = $row3['o']; $rowx = $row3['x'];
				
				$que1 = encode($rowi, $xchar);
				
				$ifor = dus($rowd);
				
				if($rowm == 1){
					$naming = $rown;
					$imgurl = 'https://'.$srv1.'/'.$que1.$ifor;
					$perurl = 'https://'.$srv1.'/'.$que1.'';
					$daturi = $imgurl;
				}else{
					$naming = $rowp.'_'.$rown;
					$imgurl = 'https://'.$srv1.'/'.$rowa.'/'.$rowp.'/'.$rown.$ifor;
					$perurl = 'https://'.$srv1.'/'.$que1.'';
					$daturi = $imgurl;
				}
				
				if($row3['m'] == 1){
					
					if($exts=='webp'){
					
						if(!empty($img) and $ico[0] == 'data:image/webp;base64' and empty($row3['x'])){
							header('Cache-Control: public,must-revalidate,max-age=6000');
							header('Content-Type: image/webp');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($ico[1]); exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type: image/webp');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($err_webp); exit;
						}
						
					}elseif($exts=='png'){
						
						if(!empty($img) and $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
							header('Cache-Control: public,must-revalidate,max-age=6000');
							header('Content-Type: image/png');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($ico[1]); exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type: image/png');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($err_png); exit;
						}
						
						
					}elseif($exts=='json'){
						
						if(!empty($img) and $ico[0] == 'data:image/webp;base64' or $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('Content-Type: text/plain');
							header('Access-Control-Allow-Origin: *');
							echo '{"'.$naming.'":"'.$daturi.'"}'; exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type:  text/plain');
							header('Access-Control-Allow-Origin: *');
							echo json_encode(new stdClass); exit;
						}
						
					}else{

						
						if(empty($rowd) or !empty($rowx)){
							header('HTTP', true, 404);
							echo err_msg('What the file??'); exit;
						}
						
						$tit .= ' - '.$naming;
						
						$msg  = des_msg($imgurl, $rowi, $que1, $rowa, $rowp, $naming, $rowl, $rowo, $daturi, $ifor);
						echo top_msg($tit, $msg, $sql_time, $dev_time); exit;
					
					}
				
				
				
				
				}else{
				
					$que2 = decode($ext2[0], $xchar);
					
					if($que2 == 0){ // デコードして 0 になった場合は404
						
						switch($exts){
						
							case 'webp':
								header('Cache-Control: public,must-revalidate,max-age=60');
								header('HTTP', true, 404);
								header('Content-Type: image/webp');
								echo base64_decode($err_webp); exit;
							break;
							
							case 'png':
								header('Cache-Control: public,must-revalidate,max-age=60');
								header('HTTP', true, 404);
								header('Content-Type: image/png');
								echo base64_decode($err_png); exit;
							break;
							
							case 'json':
								header('Cache-Control: public,must-revalidate,max-age=60');
								header('HTTP', true, 404);
								header('Content-Type:  text/plain');
								echo json_encode(new stdClass); exit;
							break;
							
							default:
								header('Cache-Control: public,must-revalidate,max-age=60');
								header('HTTP', true, 404);
								echo err_msg('What you look for here?'); exit;
							break;
						}
						
						
						
						

					}
					
					$sql2 = "SELECT * FROM `em0si` WHERE i=$que2;"; // 短縮キーで呼び出しバージョン
					
					
					$row2 = $db->querySingle($sql2, true);
					
					$img  = $row2['d'];
					$ico = @explode(',', $img);
					
						
					$rowi = $row2['i']; $rowm = $row2['m']; $rowa = $row2['a'];
					$rowp = $row2['p']; $rown = $row2['n']; $rowd = $row2['d'];
					$rowl = $row2['l']; $rowo = $row2['o']; $rowx = $row2['x'];
					
					$que1 = encode($rowi, $xchar);
					$ifor = dus($rowd);
					
					if($rowm == 1){
						$naming = $rown;
						$imgurl = 'https://'.$srv1.'/'.$que1.$ifor;
						$perurl = 'https://'.$srv1.'/'.$que1.'';
						$daturi = $imgurl;
					}else{
						$naming = $rowp.'_'.$rown;
						$imgurl = 'https://'.$srv1.'/'.$rowa.'/'.$rowp.'/'.$rown.$ifor;
						$perurl = 'https://'.$srv1.'/'.$que1.'';
						$daturi = $imgurl;
					}
					
					if($exts=='webp'){
					
						if(!empty($img) and $ico[0] == 'data:image/webp;base64' and empty($row2['x'])){
							header('Cache-Control: public,must-revalidate,max-age=6000');
							header('Content-Type: image/webp');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($ico[1]); exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type: image/webp');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($err_webp); exit;
						}
					
					}elseif($exts=='png'){
						
						if(!empty($img) and $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
							header('Cache-Control: public,must-revalidate,max-age=6000');
							header('Content-Type: image/png');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($ico[1]); exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type: image/png');
							header('Access-Control-Allow-Origin: *');
							echo base64_decode($err_png); exit;
						}
						
						
					}elseif($exts=='json'){
						
						if(!empty($img) and $ico[0] == 'data:image/webp;base64' or $ico[0] == 'data:image/png;base64' and empty($row3['x'])){
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('Content-Type: text/plain');
							header('Access-Control-Allow-Origin: *');
							echo '{"'.$naming.'":"'.$daturi.'"}'; exit;
						}else{
							header('Cache-Control: public,must-revalidate,max-age=60');
							header('HTTP', true, 404);
							header('Content-Type: text/plain');
							header('Access-Control-Allow-Origin: *');
							echo json_encode(new stdClass); exit;
						}
						
					}else{

						
						if(empty($rowd) or !empty($rowx)){
							header('HTTP', true, 404);
							echo err_msg('What the file??'); exit;
						}
						
						$tit .= ' - '.$naming;
						
						$msg  = des_msg($imgurl, $rowi, $que1, $rowa, $rowp, $naming, $rowl, $rowo, $daturi, $ifor);
						echo top_msg($tit, $msg, $sql_time, $dev_time); exit;
					
					}
					
					
				}
					
					
					
					

				
			}else{
				
				switch($exts){
				
					case 'webp':
						header('Cache-Control: public,must-revalidate,max-age=60');
						header('HTTP', true, 404);
						header('Content-Type: image/webp');
						header('Access-Control-Allow-Origin: *');
						echo base64_decode($err_webp); exit;
					break;
					
							
					case 'png':
						header('Cache-Control: public,must-revalidate,max-age=60');
						header('HTTP', true, 404);
						header('Content-Type: image/webp');
						header('Access-Control-Allow-Origin: *');
						echo base64_decode($err_png); exit;
					break;
					
					case 'json':
						header('Cache-Control: public,must-revalidate,max-age=60');
						header('HTTP', true, 404);
						header('Content-Type:  text/plain');
						header('Access-Control-Allow-Origin: *');
						echo json_encode(new stdClass); exit;
					break;
					
					default:
						header('Cache-Control: public,must-revalidate,max-age=60');
						header('HTTP', true, 404);
						echo err_msg('What the file?'); exit;
					break;
				}
			}
			
			
		}


	}else{// $req1 がない場合は index 表示



?><!DOCTYPE html>
<?
		$msg  = $list_temp.'<div class="box"><p>json (<a href="/_a.json" target="_blank">json file</a>)<br>';
		$msg .= '<textarea onclick="this.focus(); this.select();" wrap="on" style="width:420px; height:96px;">';
		$msg .= $json_temp.'</textarea></p></div>';
		
		
		switch($req1){
		
			case '_a.json':
				header('Content-Type: text/plain');
				header('Access-Control-Allow-Origin: *');
				$json_temp; exit;
			break;
			
			case '_b.json':
				header('Content-Type: text/plain');
				header('Access-Control-Allow-Origin: *');
				$json_temp2; exit;
			break;
			
			default:
				$tit .= ' - emojis for Nostr. (temporary)';
				echo top_msg($tit, $msg, $sql_time, $dev_time); exit;
			break;
		
		}
		
		
		
		
	}// $req1 なしここまで

}else{// database 存在しません

	header('HTTP', true, 500);
	echo err_msg('where is database?'); exit;

}


	?>