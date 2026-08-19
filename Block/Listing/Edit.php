<?php
/**
 * @category BrunoDuarte
 * @package BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Block\Listing;

use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory as MultipleWishlistCollectionFactory;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection as MultipleWishlistCollection;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Pager;

/**
 * class Listing
 *
 * @package BrunoDuarte\MultipleWishlist\Block\Listing
 */
class Listing extends Template
{
    private const QTY_DEFAULT = 5;
    private const QTY_DEFAULT_DOUBLE = 10;
    private const QTY_DEFAULT_TRIPLE = 15;

    private MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory;
    private SessionFactory $sessionFactory;
    private StoreManagerInterface $storeManager;

    /**
     * Listing constructor.
     *
     * @param Template\Context $context
     * @param MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory
     * @param SessionFactory $sessionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory,
        SessionFactory $sessionFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->multipleWishlistCollectionFactory = $multipleWishlistCollectionFactory;
        $this->sessionFactory = $sessionFactory;
        $this->storeManager = $storeManager;
    }

    private function getCustomer(): Customer
    {
        return $this->sessionFactory->create()->getCustomer();
    }

    public function getCustomerMultipleWishlist(): ?MultipleWishlistCollection
    {
        $page = ($this->getRequest()->getParam('p')) ?
            $this->getRequest()->getParam('p') : self::QTY_DEFAULT;

        $pageSize = ($this->getRequest()->getParam('limit')) ?
            $this->getRequest()->getParam('limit') : self::QTY_DEFAULT;

        /** @var MultipleWishlistCollection $wishlistCollection */
        // @phpstan-ignore-next-line
        $wishlistCollection = $this->multipleWishlistCollectionFactory->create();

        $wishlistCollection
            //->addFieldToFilter('store_id', $this->getStoreId())
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
            ->setPageSize($pageSize)
            ->setCurPage($page);

        if (!$wishlistCollection->getSize()) {
            return null;
        }

        return $wishlistCollection;
    }

    public function formatStatus(int $isActive): string
    {
        return $isActive ? 'Active' : 'Inactive';
    }

    public function getSaveUrl(int $multipleWishlistId): string
    {
        return $this->getUrl('multiple_wishlist/page/save', ['id' => $multipleWishlistId]);
    }

    public function getOrderUrl(int $multipleWishlistId): string
    {
        return $this->getUrl('multiple_wishlist/post/orderPost', ['id' => $multipleWishlistId]);
    }

    public function getGoBackUrl(int $multipleWishlistId): string
    {
        return $this->getUrl('multiple_wishlist/page/listing');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }

    protected function getStoreId(): int
    {
        return (int) $this->storeManager->getStore()->getId();
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codeCoverageIgnore
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _prepareLayout(): self
    {
        parent::_prepareLayout();

        if ($this->getCustomerMultipleWishlist() !== null) {
            /** @var Pager $pagerBlock */
            $pagerBlock = $this->getLayout()->createBlock(Pager::class, 'multiple.wishlist.listing.pager');
            $pagerBlock->setAvailableLimit([
                self::QTY_DEFAULT => self::QTY_DEFAULT,
                self::QTY_DEFAULT_DOUBLE => self::QTY_DEFAULT_DOUBLE,
                self::QTY_DEFAULT_TRIPLE => self::QTY_DEFAULT_TRIPLE
            ])->setShowPerPage(true)
                ->setCollection($this->getCustomerMultipleWishlist());
            $this->setChild('pager', $pagerBlock);
        }

        return $this;
    }
}
