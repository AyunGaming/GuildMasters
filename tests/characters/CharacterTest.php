<?php

namespace division\tests\characters;

use division\Data\DAO\character\CharacterDAO;
use division\Data\DAO\character\CharacterTagDAO;
use division\Models\Managers\CharacterManager;
use PHPUnit\Framework\TestCase;

class CharacterTest extends TestCase
{
    public function testBuildQuery()
    {
        $characterDAO = new CharacterDAO(null);
        $characterTagDAO = new CharacterTagDAO(null);
        $characterManager = new CharacterManager($characterDAO, $characterTagDAO);

        $filters = [
            'operator' => 'on',
            'filtres' => [
                'image' => 'DBL-EVT-00S', // or "'name' => 'Goku',"
                'rarity' => 'SPARKING',
                'color' => 'BLE',
                'lf' => 'off'
            ],
            'tags' => ['Saiyan', 'Super Saiyan']
        ];

        $taglist = [
//            ['bite', '4'],
//            []
//            ...
        ];

        $res = $characterDAO->characterSearchQuery($filters);
        $expected = "SELECT * FROM dbl_characters WHERE Image LIKE '%DBL-EVT-00S%' OR Rarity = 'SPARKING' OR Color = 'BLE' OR IsLF = 0 ORDER BY Image ASC";

        $this->assertSame($expected, $res);
    }
}