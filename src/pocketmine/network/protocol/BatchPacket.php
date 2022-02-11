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

namespace pocketmine\network\protocol;

use pocketmine\utils\BinaryStream;

#include <rules/DataPacket.h>


class BatchPacket extends DataPacket{
	const NETWORK_ID = Info::BATCH_PACKET;

	public $packets = [];
	public $compressionLevel = 7;

	public function decode(){
		$this->packets = [];
		$stream = new BinaryStream(zlib_decode($this->get(true), 1024 * 1024 * 64));
		while (!$stream->feof()) {
			$size = $stream->getUnsignedVarInt();
			$this->packets[] = $stream->get($size);
		}
	}

	public function encode(){
		$this->reset();
		$stream = new BinaryStream();
		foreach ($this->packets as $packet) {
			$stream->putUnsignedVarInt(strlen($packet));
			$stream->put($packet);
		}
		$this->put(zlib_encode($stream->getBuffer(), ZLIB_ENCODING_RAW, $this->compressionLevel));
		$stream->reset();
	}
}