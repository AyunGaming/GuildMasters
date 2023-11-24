<?php

namespace division\Models;

class Kamenews {
	private int $id;

	private string $titre;

	private string $date;

	private string $desc;

	/**
	 * @var array Key-value array (article => position in Kamenews)
	 */
	private array $articles;

	private User $writer;


	public function hydrate(array $data) {
		if (array_key_exists('id', $data)) {
			$this->id = $data['id'];
		}

		if (array_key_exists('titre', $data)) {
			$this->titre = $data['titre'];
		}

		if (array_key_exists('date', $data)) {
			$this->date = $data['date'];
		}

		if (array_key_exists('description', $data)) {
			$this->desc = $data['description'];
		}

		if (array_key_exists('articles', $data)) {
			$this->articles = $data['articles'];
		}

		if (array_key_exists('writer', $data)) {
			$this->writer = $data['write'];
		}
	}

	public function getId(): int {
		return $this->id;
	}

	public function getTitre(): string {
		return $this->titre;
	}

	public function getDate(): string {
		return $this->date;
	}

	public function getDesc(): string {
		return $this->desc;
	}
}