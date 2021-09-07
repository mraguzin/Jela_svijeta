<?php

namespace App\Tests\Controller;

use App\Controller\MealController;
use App\Service\SlugService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class MealsTest extends KernelTestCase
{
    private $slugger;

    private function test1($mealController)
    {
        //$request = new Request(['lang'=>'de', 'per_page'=>2, 'page'=>2, 'with'=>'tags']);
        $_GET = ['lang'=>'de', 'per_page'=>2, 'page'=>2, 'with'=>'tags'];
        $response = $mealController->meals()->getContent();

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

    private function test2($mealController)
    {
        //$request = new Request(['lang'=>'en', 'per_page'=>2, 'page'=>1, 'with'=>'tags,category', 'category'=>'NULL', 'tags'=>'91,94']);
        $_GET = ['lang'=>'en', 'per_page'=>2, 'page'=>1, 'with'=>'tags,category', 'category'=>'NULL', 'tags'=>'91,94'];
        $response = $mealController->meals()->getContent();

        $this->assertJson($response);

        $obj = json_decode($response);
        $this->assertEquals(1, $obj->meta->currentPage);
        $this->assertEquals(2, $obj->meta->totalItems);
        $this->assertEquals(2, $obj->meta->itemsPerPage);
        $this->assertEquals(1, $obj->meta->totalPages);

        $this->assertEquals(2, count($obj->data));
        $this->assertNotEmpty($obj->data);

        $this->assertStringStartsWith('In qui commodi', $obj->data[0]->description);
        $this->assertNotEmpty($obj->data[0]->tags);
        $this->assertStringStartsWith('Miss Celestine', $obj->data[0]->tags[0]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[0]->tags[0]->title), $obj->data[0]->tags[0]->slug);
        $this->assertStringStartsWith('Mr. Tyrell', $obj->data[0]->tags[1]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[0]->tags[1]->title), $obj->data[0]->tags[1]->slug);
        $this->assertStringStartsWith('Nyasia', $obj->data[0]->tags[2]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[0]->tags[2]->title), $obj->data[0]->tags[2]->slug);
        $this->assertEquals(3, count($obj->data[0]->tags));
        $this->assertNull($obj->data[0]->category);

        $this->assertStringStartsWith('Sunt sequi', $obj->data[1]->description);
        $this->assertNotEmpty($obj->data[1]->tags);
        $this->assertStringStartsWith('Miss Celestine', $obj->data[1]->tags[0]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[1]->tags[0]->title), $obj->data[1]->tags[0]->slug);
        $this->assertStringStartsWith('Mr. Tyrell', $obj->data[1]->tags[1]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[1]->tags[1]->title), $obj->data[1]->tags[1]->slug);
        $this->assertStringStartsWith('Nyasia', $obj->data[1]->tags[2]->title);
        $this->assertEquals($this->slugger->escapeText($obj->data[1]->tags[2]->title), $obj->data[1]->tags[2]->slug);
        $this->assertEquals(3, count($obj->data[1]->tags));
        $this->assertNull($obj->data[1]->category);

        $this->assertNull($obj->links->prev);
        $this->assertNull($obj->links->next);
        $this->assertEquals('/meals?per_page=2&page=1&category=NULL&tags=91,94&with=tags,category&lang=en', $obj->links->self);
    }

    public function testMeals()
    {
        $mealController = static::getContainer()->get(MealController::class);
        $this->slugger  = static::getContainer()->get(SlugService::class);

        $this->test1($mealController);
        $this->test2($mealController);

    }
}