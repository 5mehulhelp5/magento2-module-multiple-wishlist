<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use BrunoDuarte\MultipleWishlist\Helper\Data;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class AddProductToMultipleWishlist extends AbstractPost
{
    protected MultipleWishlistRepositoryInterface $multipleWishlistRepository;
    protected MultipleWishlistFactory $multipleWishlistFactory;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        Validator $formKeyValidator,
        Data $helperModule,
        SessionFactory $sessionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        MultipleWishlistFactory $multipleWishlistFactory
    ) {
        parent::__construct(
            $redirectFactory,
            $redirect,
            $formKeyValidator,
            $helperModule,
            $sessionFactory,
            $request,
            $storeManager,
            $messageManager,
            $logger
        );
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->multipleWishlistFactory = $multipleWishlistFactory;
    }

    public function execute(): Redirect
    {
        $redirect = $this->redirectResultFactory->create();

        $request = $this->request;

        return $redirect;
    }
}
