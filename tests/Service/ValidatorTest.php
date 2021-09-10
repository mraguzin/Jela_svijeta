<?php

namespace App\Tests\Service;

use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ValidatorTest extends KernelTestCase
{
    private function test1($validator)
    {
        $result = $validator->validateFields(
            ['name' => 'Giles', 'lastname' => 'Morgan', 'age' => 34, 'prio' => -1],
            ['name', 'lastname', 'age', 'prio', 'employed'],
            ['string', 'string', 'integer', 'integer', 'boolean'],
            [true, true, true, false, false],
            [null, null, 0, -1, null]
        );

        $this->assertEquals('Giles', $result['name']);
        $this->assertEquals('Morgan', $result['lastname']);
        $this->assertEquals(34, $result['age']);
        $this->assertEquals(-1, $result['prio']);
        $this->assertEquals(null, $result['employed']);
    }

    private function test2($validator)
    {
        $result = $validator->validateFields(
            ['name' => 'Giles', 'lastname' => 'Morgan', 'age' => 34, 'prio' => 0, 'employed' => 'true', 'awards' => ''],
            ['name', 'lastname', 'age', 'prio', 'employed', 'awards'],
            ['string', 'string', 'integer', 'integer', 'boolean', 'array'],
            [true, true, true, false, false, false],
            [null, null, 0, 0, null, null]
        );

        $this->assertEquals('Giles', $result['name']);
        $this->assertEquals('Morgan', $result['lastname']);
        $this->assertEquals(34, $result['age']);
        $this->assertEquals(0, $result['prio']);
        $this->assertEquals(true, $result['employed']);
        $this->assertEmpty($result['awards']);
    }

    private function test3($validator)
    {
        $result = $validator->validateFields(
            ['name' => 'Giles', 'lastname' => 'Morgan', 'age' => 34, 'prio' => 0, 'employed' => 'true', 'awards' => 'nano,giga'],
            ['name', 'lastname', 'age', 'prio', 'employed', 'awards'],
            ['string', 'string', 'integer', 'integer', 'boolean', 'array'],
            [true, true, true, false, false, false],
            [
                null, null, 0, 0, null,
                ['nano', 'micro', 'milli', 'kilo', 'mega', 'giga']
            ]
        );

        $this->assertEquals('Giles', $result['name']);
        $this->assertEquals('Morgan', $result['lastname']);
        $this->assertEquals(34, $result['age']);
        $this->assertEquals(0, $result['prio']);
        $this->assertEquals(true, $result['employed']);
        $this->assertEquals(['nano', 'giga'], $result['awards']);
    }

    public function testValidator()
    {
        $validator = static::getContainer()->get(ValidatorService::class);

        $this->test1($validator);
        $this->test2($validator);
        $this->test3($validator);
    }
}
