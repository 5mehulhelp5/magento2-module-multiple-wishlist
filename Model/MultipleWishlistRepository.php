<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Model;

use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist as ResourceModelMultipleWishlist;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlist as MultipleWishlistModel;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class MultipleWishlistRepository implements MultipleWishlistRepositoryInterface
{
    private MultipleWishlistFactory $multipleWishlistFactory;
    private ResourceModelMultipleWishlist $resourceModelMultipleWishlist;

    public function __construct(
        MultipleWishlistFactory $multipleWishlistFactory,
        ResourceModelMultipleWishlist $resourceModelMultipleWishlist
    ) {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->resourceModelMultipleWishlist = $resourceModelMultipleWishlist;
    }

    public function getById(int $multipleWishlistId): MultipleWishlistInterface
    {
        /** @var MultipleWishlistModel $multipleWishlist */
        $multipleWishlist = $this->multipleWishlistFactory->create();

        $this->resourceModelMultipleWishlist->load($multipleWishlist, $multipleWishlistId);

        if (!$multipleWishlist->getId()) {
            throw NoSuchEntityException::singleField(MultipleWishlistInterface::ID, $multipleWishlistId);
        }

        return $multipleWishlist;
    }

    public function save(MultipleWishlistInterface $multipleWishlist): MultipleWishlistInterface
    {
        try {
            /** @var MultipleWishlist $multipleWishlist */
            $this->resourceModelMultipleWishlist->save($multipleWishlist);

            $multipleWishlist = $this->getById((int) $multipleWishlist->getEntityId());
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Unable to save object. Error: %1', $exception->getMessage())
            );
        }

        return $multipleWishlist;
    }
    public function update(int $multipleWishlistId, Array $formData): bool
    {
        $multipleWishlist = $this->getById($multipleWishlistId);
        $multipleWishlist
            ->setTitle($formData['title'])
            ->setIsActive($formData['is_active'])
            ->save(); // <---- parei aqui

        return true;
    }

    public function delete(MultipleWishlistInterface $multipleWishlist): bool
    {
        try {
            /** @var MultipleWishlist $multipleWishlist */
            $this->resourceModelMultipleWishlist->delete($multipleWishlist);

        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Unable to remove object with ID %1. Error: %2',
                $multipleWishlist->getId(),
                $exception->getMessage()
            ));
        }

        return true;
    }

    public function deleteById(int $multipleWishlistId): bool
    {
        $multipleWishlist = $this->getById($multipleWishlistId);
        return $this->delete($multipleWishlist);
    }
}
