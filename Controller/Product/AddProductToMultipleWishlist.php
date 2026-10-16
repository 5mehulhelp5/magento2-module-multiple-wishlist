<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Product;

use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;

class AddProductToMultipleWishlist extends AbstractProduct
{
    public function __construct(RedirectFactory $redirectFactory)
    {
        return parent::__construct($redirectFactory);
    }

    public function execute(): Redirect
    {
        $redirect = $this->redirectResultFactory->create();
        return $redirect;
    }
}
