<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\network;

use pocketmine\network\protocol\BatchPacket;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class CompressBatchedTask extends AsyncTask{
	public $packets;
	public $pk;
	public $targets;
	public $level;

	public function __construct(array $packets, array $targets, $level = 7){
		$this->packets = $packets;
		$this->targets = $targets;
		$this->level = $level;
	}

	public function onRun(){
		try{
			$this->pk = new BatchPacket();
			$this->pk->packets = $this->packets;
			$this->pk->compressionLevel = $this->level;
			$this->pk->encode();
			$this->pk->isEncoded = true;
		}catch(\Throwable $e){

		}
	}

	public function onCompletion(Server $server){
		$server->broadcastPacketsCallback($this->pk, (array) $this->targets);
	}
}
