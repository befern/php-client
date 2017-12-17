<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Repository;

use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Abstract class Repository.
 */
abstract class Repository extends RepositoryWithCredentials
{
    /**
     * @var array
     *
     * Elements to update
     */
    private $elementsToUpdate;

    /**
     * @var array
     *
     * Elements to delete
     */
    private $elementsToDelete;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->resetCachedElements();
    }

    /**
     * Reset cache.
     */
    private function resetCachedElements()
    {
        $this->elementsToUpdate = [];
        $this->elementsToDelete = [];
    }

    /**
     * Generate item document.
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $itemUUID = $item
            ->getUUID()
            ->composeUUID();

        $this->elementsToUpdate[$itemUUID] = $item;
        unset($this->elementsToDelete[$itemUUID]);
    }

    /**
     * Generate item documents.
     *
     * @param Item[] $items
     */
    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    /**
     * Delete item document by uuid.
     *
     * @param ItemUUID $uuid
     */
    public function deleteItem(ItemUUID $uuid)
    {
        $itemUUID = $uuid->composeUUID();
        $this->elementsToDelete[$itemUUID] = $uuid;
        unset($this->elementsToUpdate[$itemUUID]);
    }

    /**
     * Delete item documents by uuid.
     *
     * @param ItemUUID[] $uuids
     */
    public function deleteItems(array $uuids)
    {
        foreach ($uuids as $uuid) {
            $this->deleteItem($uuid);
        }
    }

    /**
     * Flush all.
     *
     * This flush can be avoided if not enough items have been generated by
     * setting $skipIfLess = true
     *
     * @param int  $bulkNumber
     * @param bool $skipIfLess
     *
     * @throws ResourceNotAvailableException
     */
    public function flush(
        int $bulkNumber = 500,
        bool $skipIfLess = false
    ) {
        if (
            $skipIfLess &&
            count($this->elementsToUpdate) < $bulkNumber
        ) {
            return;
        }

        $offset = 0;
        while (true) {
            $items = array_slice(
                $this->elementsToUpdate,
                $offset,
                $bulkNumber
            );

            if (empty($items)) {
                break;
            }

            $this->flushItems($items, []);
            $offset += $bulkNumber;
        }

        $this->flushItems([], $this->elementsToDelete);
        $this->resetCachedElements();
    }

    /**
     * Flush items.
     *
     * @param Item[]     $itemsToUpdate
     * @param ItemUUID[] $itemsToDelete
     */
    abstract protected function flushItems(
        array $itemsToUpdate,
        array $itemsToDelete
    );

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    abstract public function query(Query $query): Result;

    /**
     * Create an index.
     *
     * @param null|string $language
     *
     * @throws ResourceExistsException
     */
    abstract public function createIndex(? string $language);

    /**
     * Delete an index.
     *
     * @throws ResourceNotAvailableException
     */
    abstract public function deleteIndex();

    /**
     * Reset the index.
     *
     * @throws ResourceNotAvailableException
     */
    abstract public function resetIndex();
}
