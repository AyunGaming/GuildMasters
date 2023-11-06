<?php

namespace division\Models;

use division\Exceptions\InvalidEnumException;
use division\Models\Enums\Color;
use division\Models\Enums\Rarity;

class Character {
	private string $image;
	private Rarity $rarity;
	private bool $isLF;
	private string $name;
	private Color $color;
	private array $tags;

	public function getImage(): string {
		return $this->image;
	}

	public function getRarity(): Rarity {
		return $this->rarity;
	}

	public function setRarity(Rarity $rarity): void {
		$this->rarity = $rarity;
	}

	public function isLF(): bool {
		return $this->isLF;
	}

	public function setIsLF(bool $isLF): void {
		$this->isLF = $isLF;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function getColor(): Color {
		return $this->color;
	}

	public function setColor(Color $color): void {
		$this->color = $color;
	}

	public function getTags(): array {
		return $this->tags;
	}

	public function getTagString(): string {
		$string = "";
		for ($i = 0; $i <= count($this->tags)-1; $i++) {
			if ($i === count($this->tags)-1) {
				$string .= $this->tags[$i]->getName();
			} else {
				$string .= $this->tags[$i]->getName() . ', ';
			}
		}

		return $string;
	}

	public function hydrate(array $data): void {
		if(array_key_exists('Id',$data)){
			$this->image = $data['Id'];
		}

		if (array_key_exists('Image', $data)) {
			$this->image = $data['Image'];
		}

		if (array_key_exists('Rarity', $data)) {
			$rarity = Rarity::tryFrom($data['Rarity']);
			if ($rarity === null) {
				throw new InvalidEnumException(Rarity::class, $data['Rarity']);
			}
			$this->rarity = $rarity;
		}

		if (array_key_exists('IsLF', $data)) {
			if (is_bool($data['IsLF'])) {
				$this->isLF = $data['IsLF'];
			} elseif (is_numeric($data['IsLF'])) {
				$this->isLF = (int)$data['IsLF'] === 1;
			}
		}

		if (array_key_exists('Name', $data)) {
			$this->name = $data['Name'];
		}

		if (array_key_exists('Color', $data)) {
			$color = Color::tryFrom($data['Color']);
			if ($color === null) {
				throw new InvalidEnumException(Color::class, $data['Color']);
			}
			$this->color = $color;
		}

		if (array_key_exists('Tags', $data) && is_array($data['Tags'])) {
			$this->tags = [];
			foreach ($data['Tags'] as $tagData) {
				$tag = new Tag();
				$tag->hydrate($tagData);
				$this->tags[] = $tag;
			}
		}
	}
}
