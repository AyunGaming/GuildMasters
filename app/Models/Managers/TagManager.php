<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\ITagDAO;

class TagManager {
	private ITagDAO $tagDAO;

	public function __construct(ITagDAO $tagDAO){
		$this->tagDAO = $tagDAO;
	}


}
