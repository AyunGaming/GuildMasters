<?php

namespace division\Models;

class Article {
	private string $title;

	private string $image;

	private string $text;

	private int $number;


	public function hydrate(array $data): void {
		if (array_key_exists('title', $data)) {
			$this->title = $data['title'];
		}

		if (array_key_exists('image', $data)) {
			$this->image = $data['image'];
		}

		if (array_key_exists('text', $data)) {
			$this->text = $data['text'];
		}

		if (array_key_exists('number', $data)) {
			$this->number = $data['number'];
		}
	}
}