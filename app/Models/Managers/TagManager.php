<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\characters\ITagDAO;

class TagManager {
	private ITagDAO $tagDAO;

	public function __construct(ITagDAO $tagDAO){
		$this->tagDAO = $tagDAO;
	}


}
