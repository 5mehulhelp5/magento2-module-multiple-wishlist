<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Model;

use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist as ResourceModelMultipleWishlist;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlist as MultipleWishlistModel;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class MultipleWishlistRepository implements MultipleWishlistRepositoryInterface
{
    private MultipleWishlistFactory $multipleWishlistFactory;
    private ResourceModelMultipleWishlist $resourceModelMultipleWishlist;

    protected CustomerSession $customerSession;

    public function __construct(
        MultipleWishlistFactory $multipleWishlistFactory,
        ResourceModelMultipleWishlist $resourceModelMultipleWishlist,
        CustomerSession $customerSession
    ) {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->resourceModelMultipleWishlist = $resourceModelMultipleWishlist;
        $this->customerSession = $customerSession;
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

    public function update(array $multipleWishlistFormData): bool
    {
        $multipleWishlist = $this->getById($multipleWishlistFormData['id']);

        if ($multipleWishlist->getCustomerId() != $this->customerSession->getCustomerId()) {
            throw new NoSuchEntityException(__('You are not authorized to update this wishlist.'));
        }

        $multipleWishlist->setTitle($multipleWishlistFormData['title']);
        $multipleWishlist->setIsActive($multipleWishlistFormData['is_active']);

        $isSave = $this->save($multipleWishlist);

        return $isSave instanceof MultipleWishlistInterface;
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
