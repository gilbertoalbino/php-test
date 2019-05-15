<?php

namespace Live\Collection;

use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase
{

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function objectCanBeConstructed()
    {
        $collection = new FileCollection('collection.txt');
        return $collection;
    }


    /**
     * @test
     */
    public function shouldPassValidFilePathArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FileCollection();
    }

    /**
     * @test
     */
    public function collectionShouldSerializeStructure()
    {
        $filename = uniqid(time()) . '.txt';

        $collection = new FileCollection($filename);
        $collection->set('index1', 'value1');

        $serialized_struct = $collection->getSerializedStructure();

        $unserialized_struct = unserialize($serialized_struct);

        $this->assertEquals($unserialized_struct['data']['index1'], $collection->get('index1'));

        @unlink($filename);
    }

    /**
     * @test
     */
    public function collectionShouldUseDefaultStructIfFileDoesNotExist()
    {
        $filename = uniqid(time()) . '.txt';

        $collection = new FileCollection($filename);

        $struct = ['data' => null, 'metadata' => null];

        $this->assertEquals($struct, $collection->getPreparedStructure());

        @unlink($filename);
    }

    /**
     * @test
     */
    public function collectionShouldUseFileStructIfFileExists()
    {
        $filename = dirname(__FILE__) . '/../data/collection.txt';

        $collection = new FileCollection($filename);

        $struct = [
            'data' => ['index1' => 'value1'],
            'metadata' => ['index1' => ['ttl' => 1558803596]],
        ];

        $collection->get('index1');

        $this->assertEquals($struct, $collection->getPreparedStructure());
    }

    /**
     * @test
     */
    public function collectionCanBeSaved()
    {
        $filename = dirname(__FILE__) . '/../data/' . uniqid(time()) . '.txt';

        $collection = new FileCollection($filename);

        $collection->set('index2', 'value2', 120);

        $collection->save();

        /**
         * to check on a new instance.
         */
        $saved_collection = new FileCollection($filename, true);
        $this->assertEquals('value2', $saved_collection->get('index2'));

        @unlink($filename);
    }
}
