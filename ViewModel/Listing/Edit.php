<?php
/**
 * @category BrunoDuarte
 * @package BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\ViewModel\Listing;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory as MultipleWishlistCollectionFactory;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection as MultipleWishlistCollection;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Edit implements ArgumentInterface
{
    private MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory;

    /**
     * @var MultipleWishlistRepositoryInterface
     */
    private $multipleWishlistRepository;

    private SessionFactory $sessionFactory;

    private StoreManagerInterface $storeManager;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var ManagerInterface
     */
    public $messageManager;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    public function __construct(
        MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        SessionFactory $sessionFactory,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        UrlInterface $urlBuilder
    ) {
        $this->multipleWishlistCollectionFactory = $multipleWishlistCollectionFactory;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->sessionFactory = $sessionFactory;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    private function getCustomer(): Customer
    {
        return $this->sessionFactory->create()->getCustomer();
    }

    /**
     * Get the wishlist id by request
     *
     * @return int
     */
    public function getWishlistId(): int
    {
        return (int) $this->request->getParam('id');
    }

    /**
     * Get wishlist all data
     *
     * @return mixed
     */
    public function getWishlist()
    {
        try {
            $wishlistData = $this->multipleWishlistRepository->getById($this->getWishlistId());
            if (empty($wishlistData)) {
                return [];
            }

            return $wishlistData;

        } catch (\InvalidArgumentException $e) {
            $this->messageManager->addErrorMessage(__('Wishlist data not found.'));
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Get the name of wishlist
     *
     * @return mixed|string
     */
    public function getTitle(): string
    {
        $customer = $this->getCustomer();

        $collection = $this->multipleWishlistCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customer->getId());
        $collection->setPageSize(1);

        $wishlist = $collection->getFirstItem();

        return $wishlist->getTitle() ?: '';
    }

    /**
     * Builder URL by requested the edition of the wishlist
     *
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->urlBuilder->getUrl('multiple_wishlist/post/EditPost');
    }

}
