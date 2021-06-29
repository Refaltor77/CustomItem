<?php

namespace refaltor_Natof\CustomItem\Items;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class Items extends Item
{
	public int $stack;

	public function __construct(int $id, int $stack, int $meta = 0, string $name = "Unknown")
	{
		$nbt = new CompoundTag("item_properties", [
			new IntTag("max_stack_size", $stack)
		]);
		$this->setNamedTag($nbt);
		$this->stack = $stack;
		parent::__construct($id, $meta, $name);
	}


	public function getMaxStackSize(): int
	{
		return $this->stack;
	}
}
