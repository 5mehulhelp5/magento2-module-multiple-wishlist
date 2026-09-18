<?php
/**
 * @category    BrunoDuarte
 * @package     BrunoDuarte_MultipleWishlist
 * @copyright   Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Block\Product;

use BrunoDuarte\MultipleWishlist\Helper\Data as DataHelper;
use Magento\Framework\View\Element\Template as ElementTemplate;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class View extends ElementTemplate
{
    public DataHelper $dataHelper;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
    }

    public function canDisplayBlock()
    {
        return false;
    }

    protected function getStoreId(): int
    {
        return (int) $this->storeManager->getStore()->getId();
    }
}
