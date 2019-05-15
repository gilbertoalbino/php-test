<?php

namespace Live\Collection;

/**
 * File collection
 *
 * @package Live\Collection
 */
class FileCollection extends MemoryCollection
{
    /**
     * The file path to handle.
     *
     * @var string $filepath
     */
    public $filepath;

    /**
     * Constructor
     */
    /**
     * FileCollection constructor receive the file path to handle.
     * If a file exists its content will be used.
     * Otherwise, when the collection is stored,
     * it will be used to name a newly created file.
     *
     * @param string|null $filepath
     */
    public function __construct(string $filepath = null)
    {
        if (is_null($filepath)) {
            throw new \InvalidArgumentException(
                'FileCollection must have a valid filepath'
            );
        }

        $this->filepath = $filepath;
        $struct = $this->getPreparedStructure();

        $this->data = $struct['data'];
        $this->metadata = $struct['metadata'];
    }

    /**
     * Prepare the data structure.
     *
     * @return array
     */
    public function getPreparedStructure(): array
    {
        $struct = ['data' => null, 'metadata' => null];

        $contents = is_file($this->filepath) ? file_get_contents($this->filepath) : null;

        $contents_struct = ($contents) ? $contents_struct = unserialize($contents) : [];

        if ($contents_struct &&
            array_key_exists('data', $contents_struct) &&
            array_key_exists('metadata', $contents_struct)
        ) {
            $struct = [
                'data' => $contents_struct['data'],
                'metadata' => $contents_struct['metadata']
            ];
        }

        return $struct;
    }

    /**
     * Get the structure serialized for file storage.
     *
     * @return string
     */
    public function getSerializedStructure(): string
    {
        return serialize([
            'data' => $this->data,
            'metadata' => $this->metadata
        ]);
    }

    /**
     * Store the serialized collection to
     * file named with constructor's filepath argument.
     * Otherwise, the file will be created.
     */
    public function save(): void
    {
        file_put_contents($this->filepath, $this->getSerializedStructure());
    }
}
