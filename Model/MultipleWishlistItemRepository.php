<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Model;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem as MultipleWishlistItemResourceModel;
use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class MultipleWishlistItemRepository implements MultipleWishlistItemRepositoryInterface
{
    private MultipleWishlistItemFactory $multipleWishlistItemFactory;
    private MultipleWishlistItemResourceModel $resourceModelMultipleWishlistItem;

    public function __construct(
        MultipleWishlistItemFactory $multipleWishlistItemFactory,
        MultipleWishlistItemResourceModel $resourceModelMultipleWishlistItem
    ) {
        $this->multipleWishlistItemFactory = $multipleWishlistItemFactory;
        $this->resourceModelMultipleWishlistItem = $resourceModelMultipleWishlistItem;
    }

    public function save(MultipleWishlistItemInterface $multipleWishlistItem): MultipleWishlistItem
    {
        try {
            $this->resourceModelMultipleWishlistItem->save($multipleWishlistItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Unable to save object. Error: %1', $exception->getMessage())
            );
        }

        return $multipleWishlistItem;
    }

    public function getById(int $multipleWishlistId): ?MultipleWishlistItemInterface
    {
        $multipleWishlistItem = $this->multipleWishlistItemFactory->create();
        $this->resourceModelMultipleWishlistItem->load($multipleWishlistItem, $multipleWishlistId, 'wishlist_id');

        if (!$multipleWishlistItem->getId()) {
            // throw new \Magento\Framework\Exception\NoSuchEntityException(
            //     __('Object with ID "%1" does not exist.', $multipleWishlistId)
            // );
            return null; // Return null if the entity does not exist
        }

        return $multipleWishlistItem;
    }
}
