<?php
/**
 * @category BrunoDuarte
 * @package BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Page;

use Magento\Framework\Controller\ResultInterface;

class View extends AbstractPage
{
    public function execute(): ResultInterface
    {
        $this->checkPermissions();

        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->unsetValue();

        return $page;
    }
}
