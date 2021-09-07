<?php

namespace App\Tests\Controller;

use App\Controller\MealController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class MealsTest extends KernelTestCase
{
    public function testMeals()
    {
        $mealController = static::getContainer()->get(MealController::class);

        $request = new Request(['lang'=>'de', 'per_page'=>2, 'page'=>2, 'with'=>'tags']);
        $response = $mealController->meals($request);

        $this->assertJson($response);

        $obj = json_decode($response);
        $this->assertEquals(2, $obj->meta->currentPage);
        $this->assertEquals(10, $obj->meta->totalItems);
        $this->assertEquals(2, $obj->meta->itemsPerPage);
        $this->assertEquals(5, $obj->meta->totalPages);

        $this->assertEquals(2, count($obj->data));
        $this->assertNotEmpty($obj->data);

        $this->assertStringStartsWith('Dolores', $obj->data[0]->description);
        $this->assertNotEmpty($obj->data[0]->tags);
        $this->assertStringStartsWith('Edeltraud', $obj->data[0]->tags[0]->title);
        $this->assertStringStartsWith('Irmtraud', $obj->data[0]->tags[1]->title);
        $this->assertStringStartsWith('Isabella', $obj->data[0]->tags[2]->title);
        $this->assertEquals(3, count($obj->data[0]->tags));

        $this->assertStringStartsWith('Sunt', $obj->data[1]->description);
        $this->assertNotEmpty($obj->data[1]->tags);
        $this->assertStringStartsWith('Frau', $obj->data[1]->tags[0]->title);
        $this->assertStringStartsWith('Karolina', $obj->data[1]->tags[1]->title);
        $this->assertStringStartsWith('Isabella', $obj->data[1]->tags[2]->title);
        $this->assertEquals(3, count($obj->data[1]->tags));

        $this->assertEquals('/meals?per_page=2&page=1&with=tags&lang=de', $obj->links->prev);
        $this->assertEquals('/meals?per_page=2&page=3&with=tags&lang=de', $obj->links->next);
        $this->assertEquals('/meals?per_page=2&page=2&with=tags&lang=de', $obj->links->self);

    }
}