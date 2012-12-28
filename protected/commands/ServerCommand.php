<?php

class ServerCommand extends CConsoleCommand
{
	public function run($args)
	{
		file_put_contents(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'monitor.receive.pid', getmypid());
		$pubsub = Redis::client()->pubsub();
		$pubsub->subscribe('monitor_center');
		foreach ($pubsub as $message) {
			switch ($message->kind) {
				case 'subcribe':
					echo "Subcribed to {$message->channel}\n";
					break;
				case 'message':
					$this->updateFetching($message->payload);
					break;
				default:
					# code...
					break;
			}
		}
	}
	
	/**
	 * @param	string	$body
	 */
	private function updateFetching($body)
	{
		$body = json_decode($body);
		if (is_null($body)) return;
		
		$fetching = MFetching::model()->findByPK(new MongoID($body->fid));
		if (is_null($fetching)) return;
		
		$fetching->rt = new MongoDate();
		$fetching->rc = $body->client;
		$fetching->status = $body->status;
		
		foreach ($body->result as $current) {
			if ($current->if_buybox) {
				$fetching->bp = $current->sell_price;
				$fetching->br = $current->rank;
			}
			$item = array();
			$item['r'] = $current->rank;
			$item['p'] = $current->sell_price;
			$item['sp'] = $current->shipping_price;
			$item['s'] = $this->getOrCreateSeller($current->sid, $current->seller, $current->avatar);
			$item['f'] = $current->if_fba;
			$item['b'] = $current->if_buybox;
			$fetching->l[] = $item;
		}
		$fetching->save();
		
		$asin = MAsin::model()->findByPK($fetching->a{'$id'});
		switch ($body->status) {
			case 200:
			case 201:
				$asin->last = MFetching::model()->getCollection()->createDbRef($fetching);
				$asin->fs = count($body->result);
				$asin->_x = false;
				$asin->_r = 0;
				break;
			default:
				$asin->_x = true;
				$asin->_e = $body->msg;
				$asin->_r = $asin->_r ? $asin->_r + 1 : 1;
				$asin->next = new MongoDate();
				break;
		}
		$asin->save();
		
	}
	
	private function getOrCreateSeller($sid, $name, $avatar)
	{
		$seller = MSeller::model()->findByAttributes(array('sid'=>$sid));
		if (!$seller) {
			$seller = new MSeller;
			$seller->sid = $sid;
			$seller->name = $name;
			$seller->avatar = $avatar;
			$seller->save();
		}
		return MSeller::model()->getCollection()->createDbRef($seller);
	}
}
