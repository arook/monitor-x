<?php

/**
* 
*/
class AbsCommand extends CConsoleCommand
{
	
	public function run($args)
	{
		$abses = $this->get_abses();
		foreach ($abses as $item) {
			echo $item->link, "\n";
			$this->fetching($item);
			sleep(3);
		}
	}
	
	private function get_abses()
	{
		return MAbs::model()->findAll();
	}
	
	private function fetching($item)
	{
		$ref = MAbs::model()->getCollection()->createDBRef($item);
		$dt = new MongoDate(strtotime(date('Y-m-d')));
		$insert = array();
		$reg = '/<span class="zg_rankNumber">([\d]+)\.<\/span><span class="zg_rankMeta"><\/span><\/div><div class="zg_title"><a  href=".*?\/dp\/([0-9A-Z]{10})\/[^"]+">(.*?)<\/a><\/div>/is';
		for ($i=1; $i <= 5; $i++) { 
			$html = $this->__wget($item->link . '/?pg=' . $i);
			preg_match_all($reg, $html, $matches);
			foreach ($matches[0] as $key=>$match) {
				$insert[] = array('dt' => $dt,'cat' => $ref, 'rank' => (int) $matches[1][$key], 'asin' => $matches[2][$key], 'name' => new MongoBinData($matches[3][$key]));
			}
		}
		
		if (count($insert) > 0) {
			MAbsRanking::model()->getCollection()->batchInsert($insert);
		}
	}
	
	private function __wget($url)
	{
		$html_contents  = "";
    $ch = curl_init();
    $timeout = 10;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($ch, CURLOPT_ENCODING, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Host: www.amazon.com',
      'Connection: keep-alive',
      'Cache-Control: max-age=0',
      'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.54 Safari/536.5',
      'Accept-Encoding: gzip,deflate,sdch',
      'Accept-Language: en-US,en;q=0.8',
    ));
//    curl_setopt ($ch, CURLOPT_PROXY, "127.0.0.1:8889");
//    curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    $html_contents = curl_exec($ch);
    curl_close($ch);
    return $html_contents=="" ? false : $html_contents;
	}
}
