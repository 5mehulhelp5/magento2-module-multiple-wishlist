<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Data helper class
 *
 * @package BrunoDuarte\MultipleWishlist\Helper
 */
class Data extends AbstractHelper
{
    public const MODULE_REGISTRATION = 'BrunoDuarte_MultipleWishlist';
    private const MULTIPLE_WISHLIST_MODULE_ENABLE = 'multiple_wishlist/general/enabled';
    private const MULTIPLE_WISHLIST_MODULE_TITLE = 'multiple_wishlist/general/title';
    private const MULTIPLE_WISHLIST_AVAILABLE_CUSTOMER_GROUPS = 'multiple_wishlist/general/available_customer_groups';
    private const DELETE_LISTS = 'multiple_wishlist/general/wishlist_delete';

    public function isModuleEnable(?int $storeId = null): bool
    {
        return $this->_moduleManager->isEnabled(self::MODULE_REGISTRATION) && $this->scopeConfig->isSetFlag(
            self::MULTIPLE_WISHLIST_MODULE_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getModuleTitle(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::MULTIPLE_WISHLIST_MODULE_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getAvailableCustomerGroups(?int $storeId = null): array
    {
        return explode(
            ',',
            $this->scopeConfig->getValue(
                self::MULTIPLE_WISHLIST_AVAILABLE_CUSTOMER_GROUPS,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }

    public function getMaxQtyItemsByList(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            'multiple_wishlist/customer/maximum_items_wishlist',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMaxQtyListsByCustomer(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            'multiple_wishlist/customer/limit_max_wishlist',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isCustomerGroupIdAvailable(?int $customerGroupId = null): bool
    {
        if ($customerGroupId === null) {
            return false;
        }

        return in_array($customerGroupId, $this->getAvailableCustomerGroups());

    }

    public function canDeleteLists(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::DELETE_LISTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

}
