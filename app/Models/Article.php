<?php

namespace division\Models;

class Article {
	private int $id;
	private string $title;

	private array $image;

	private string $text;

	private int $number;


	public function hydrate(array $data): void {
		if (array_key_exists('id', $data)) {
			$this->id = $data['id'];
		}

		if (array_key_exists('title', $data)) {
			$this->title = $data['title'];
		}

		if (array_key_exists('image', $data)) {
			is_array($data['image']) ? $this->image = $data['image'] : $this->image[] = $data['image'];
		}
		else{
			$this->image = [];
		}

		if (array_key_exists('text', $data)) {
			$this->text = $data['text'];
		}

		if (array_key_exists('number', $data)) {
			$this->number = $data['number'];
		}
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getImage(): array {
		return $this->image;
	}

	public function getText(): string {
		return $this->text;
	}

	public function getId(): int {
		return $this->id;
	}

	public function getNumber(): int {
		return $this->number;
	}

	public function setNumber(int $number): void {
		$this->number = $number;
	}
}