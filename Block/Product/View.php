<?php
/**
 * @category    BrunoDuarte
 * @package     BrunoDuarte_MultipleWishlist
 * @copyright   Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Block\Product;

use BrunoDuarte\MultipleWishlist\Helper\Data as DataHelper;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory as MultipleWishlistCollectionFactory;
use BrunoDuarte\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection as MultipleWishlistCollection;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template as ElementTemplate;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class View extends ElementTemplate
{
    public DataHelper $dataHelper;
    private StoreManagerInterface $storeManager;
    protected CustomerSession $customerSession;
    protected MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory;

    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        MultipleWishlistCollectionFactory $multipleWishlistCollectionFactory
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->multipleWishlistCollectionFactory = $multipleWishlistCollectionFactory;
    }

    /**
     * Tests whether or not the block can be displayed.
     *
     * @return bool
     */
    public function canDisplayBlock(): bool
    {
        if (!$this->dataHelper->isModuleEnable() || !$this->customerSession->isLoggedIn()) {
            return false;
        }

        return true;
    }

    /**
     * Get a multiple wishlist list active order by title.
     *
     * @return MultipleWishlistCollection
     */
    public function getMultipleWishlistList()
    {
        $wishlistActiveCollection = $this->multipleWishlistCollectionFactory->create();
        $wishlistActiveCollection->addFieldToFilter('is_active', '1')->addOrder('title', 'asc');
        return $wishlistActiveCollection;
    }


    protected function getStoreId(): int
    {
        return (int) $this->storeManager->getStore()->getId();
    }
}
