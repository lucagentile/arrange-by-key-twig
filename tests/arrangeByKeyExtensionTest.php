<?php

namespace Tests;

use Gautile\Twig\ArrangeByKeyExtension;
use PHPUnit_Framework_TestCase;
use Tests\Classes\Player;

class arrangeByKeyExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ArrangeByKeyExtension
     */
    private $extension;

    public function setup()
    {
        $this->extension = new ArrangeByKeyExtension();
    }

    /**
     * @dataProvider provideValidArrays
     */
    public function testFilter($input, $expected, $msg)
    {
        $key = 'age';
        $output = $this->extension->collectionFilter($input, $key);
        $this->assertEquals($output, $expected, $msg);
    }

    /**
     * @dataProvider provideDoctrineCollection
     * @group collection
     */
    public function testFilterWithDoctrineCollection($input, $expected, $msg)
    {
        $output = $this->extension->collectionFilter($input, 'age');
        $this->assertEquals($output, $expected, $msg);
    }

    /**
     * @dataProvider provideEloquentCollection
     * @group collection
     */
    public function testFilterWithEloquentCollection($input, $expected, $msg)
    {
        $output = $this->extension->collectionFilter($input, 'age');
        $this->assertEquals($output, $expected, $msg);
    }

    /**
     * @dataProvider provideEmptyArray
     */
    public function testFilterWithEmptyArray($input, $expected, $msg)
    {
        $output = $this->extension->collectionFilter($input, 'age');
        $this->assertEquals($output, $expected, $msg);
    }

    /**
     * @group invalid
     */
    public function testFilterWithNoArrayProvided()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->extension->collectionFilter('anything', 'age');
    }

    /**
     * @dataProvider provideValidArrays
     * @group invalid
     */
    public function testFilterWithInvalidKey($input)
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->extension->collectionFilter($input, null);
    }

    /**
     * @group invalid
     */
    public function testFilterWithInvalidMultidimensionalArray()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->extension->collectionFilter([[[]], []], 'anything');
    }

    /**
     * @group invalid
     * @group key
     */
    public function testFilterWithAnArrayWithoutAttribute()
    {
        $array = [
            ['name' => 'Mark', 'surname' => 'Lenders', 'age' => '17'],
            ['name' => 'Oliver', 'surname' => 'Hutton'], //miss age
            ['name' => 'Benji', 'surname' => 'Price', 'age' => '17'],
            ['name' => 'Bruce', 'surname' => 'Harper', 'age' => '15'],
        ];
        $this->setExpectedException('InvalidArgumentException');
        $this->extension->collectionFilter($array, 'age');
    }

    /**
     * @group invalid
     * @group key
     */
    public function testFilterpWithAnObjectWithoutAttribute()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $ob1 = new Player('Mark', 'Lenders', null); //miss age
        $collection->add($ob1);

        $ob2 = new Player('Oliver', 'Hutton', '15');
        $collection->add($ob2);

        $ob3 = new Player('Benjamin', 'Price', '17');
        $collection->add($ob3);

        $ob4 = new Player('Bruce', 'Harper', '15');
        $collection->add($ob4);

        $this->setExpectedException('InvalidArgumentException');
        $this->extension->collectionFilter($collection, 'age');
    }

    public function provideValidArrays()
    {
        return [

           [
               [
                   ['name' => 'Mark', 'surname' => 'Lenders', 'age' => '17'],
                   ['name' => 'Oliver', 'surname' => 'Hutton', 'age' => '15'],
                   ['name' => 'Benji', 'surname' => 'Price', 'age' => '17'],
                   ['name' => 'Bruce', 'surname' => 'Harper', 'age' => '15'],
               ],
               [
                   '15' => [
                       ['name' => 'Oliver', 'surname' => 'Hutton', 'age' => '15'],
                       ['name' => 'Bruce', 'surname' => 'Harper', 'age' => '15'],
                   ],
                   '17' => [
                       ['name' => 'Mark', 'surname' => 'Lenders', 'age' => '17'],
                       ['name' => 'Benji', 'surname' => 'Price', 'age' => '17'],
                   ],
               ],
               "New array should have as keys the values of the key 'age'",
           ],

        ];
    }

    public function provideEmptyArray()
    {
        return [
            [
                [],
                [],
                'Resulting array should be an empty array',
            ],
        ];
    }

    public function provideDoctrineCollection()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $ob1 = new Player('Mark', 'Lenders', '17');
        $collection->add($ob1);

        $ob2 = new Player('Oliver', 'Hutton', '15');
        $collection->add($ob2);

        $ob3 = new Player('Benjamin', 'Price', '17');
        $collection->add($ob3);

        $ob4 = new Player('Bruce', 'Harper', '15');
        $collection->add($ob4);

        return [
            [
                $collection,
                [
                    '15' => [$ob2, $ob4],
                    '17' => [$ob1, $ob3],
                ],
                'New array from DoctrineCollection should have as keys the values of the key \'age\'',
            ],
        ];
    }

    public function provideEloquentCollection()
    {
        $collection = new \Illuminate\Database\Eloquent\Collection();

        $ob1 = new Player('Mark', 'Lenders', '17');
        $collection->add($ob1);

        $ob2 = new Player('Oliver', 'Hutton', '15');
        $collection->add($ob2);

        $ob3 = new Player('Benjamin', 'Price', '17');
        $collection->add($ob3);

        $ob4 = new Player('Bruce', 'Harper', '15');
        $collection->add($ob4);

        return [
            [
                $collection,
                [
                    '15' => [$ob2, $ob4],
                    '17' => [$ob1, $ob3],
                ],
                'New array from EloquentCollection should have as keys the values of the key \'age\'',
            ],
        ];
    }
}
