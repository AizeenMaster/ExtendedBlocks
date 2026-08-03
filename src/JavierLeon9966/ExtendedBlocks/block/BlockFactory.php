<?php

declare(strict_types=1);

namespace JavierLeon9966\ExtendedBlocks\block;

use pocketmine\block\BlockFactory as PMFactory;
use pocketmine\block\UnknownBlock;

class BlockFactory extends PMFactory
{

	public static function init(): void
	{
		$factoryClass = new \ReflectionClass(PMFactory::class);

		$properties = [
			'solid',
			'transparent',
			'hardness',
			'light',
			'lightFilter',
			'diffusesSkyLight',
			'blastResistance'
		];

		foreach ($properties as $propName) {
			if ($factoryClass->hasProperty($propName)) {
				$prop = $factoryClass->getProperty($propName);
				$prop->setAccessible(true);
				$array = $prop->getValue(null);

				if ($array instanceof \SplFixedArray) {
					$array->setSize(1024);
					$prop->setValue(null, $array);
				}
			}
		}

		if ($factoryClass->hasProperty('fullList')) {
			$fullListProp = $factoryClass->getProperty('fullList');
			$fullListProp->setAccessible(true);
			$fullList = $fullListProp->getValue(null);
			if ($fullList instanceof \SplFixedArray) {
				$fullList->setSize(16384);
				$fullListProp->setValue(null, $fullList);
			}
		}

		self::registerBlock(new Placeholder(), true);

		for ($id = 0; $id < 1024; ++$id) {
			try {
				$block = self::get($id, 0);
				if ($block instanceof UnknownBlock && $block->getId() !== $id) {
					self::registerBlock(new UnknownBlock($id));
				}
			} catch (\Throwable $e) {
				self::registerBlock(new UnknownBlock($id));
			}
		}
	}
}

