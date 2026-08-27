<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Page;

use BrunoDuarte\MultipleWishlist\Controller\Page\AbstractPage;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Edit
 * @package BrunoDuarte\MultipleWishlist\Controller\Page
 */
class Edit extends AbstractPage
{
    public function execute(): ResultInterface
    {
        $this->checkPermissions();
        $result = $this->resultPageFactory->create();
        $result->getConfig()->getTitle()->unsetValue();
        return $result;
    }
}
