<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistItemFactory;
use BrunoDuarte\MultipleWishlist\Helper\Data;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistItemRepository;
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
    protected MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository;
    protected MultipleWishlistFactory $multipleWishlistFactory;
    protected MultipleWishlistItemFactory $multipleWishlistItemFactory;

    public function __construct(
        RedirectFactory $resultRedirectFactory,
        RedirectInterface $redirect,
        Validator $formKeyValidator,
        Data $helperModule,
        SessionFactory $sessionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository,
        MultipleWishlistItemFactory $multipleWishlistItemFactory
    ) {
        parent::__construct(
            $resultRedirectFactory,
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
        $this->multipleWishlistItemRepository = $multipleWishlistItemRepository;
        $this->multipleWishlistItemFactory = $multipleWishlistItemFactory;
    }

    public function execute(): Redirect
    {
        /** @todo Este método esta duplicado no código, centralizar em um Middleware. */
        $this->checkPermissions();

        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the page. Please refresh the page and try again.')
            );
            return $resultRedirect->setUrl(
                $this->redirect->getRefererUrl()
            );
        }

        $formData = $this->request->getParam('multiplewishlistItem');
        if (empty($formData)) {
            $this->messageManager->addErrorMessage(
                __('No data provided for adding the product to the wishlist.')
            );
            return $resultRedirect->setUrl(
                $this->redirect->getRefererUrl()
            );
        }

        try {
            // $multipleWishlistItem = $this->multipleWishlistItemFactory->create();
            // $multipleWishlistItem->setData($formData);
            // $this->multipleWishlistItemRepository->save($multipleWishlistItem);

            $wishlistId = $formData['wishlist_id'] ?? null;
            $productId  = $formData['product_id'] ?? null;
            $customerId = $formData['customer_id'] ?? null;

            $wishlistId = filter_var($wishlistId, FILTER_VALIDATE_INT);
            $productId  = filter_var($productId, FILTER_VALIDATE_INT);
            $customerId = filter_var($customerId, FILTER_VALIDATE_INT);

            $wishlistItem = $this->multipleWishlistItemFactory->create();
            $wishlistItem->setWishlistId($wishlistId);
            $wishlistItem->setProductId($productId);
            $wishlistItem->setCustomerId($customerId);
            $wishlistItem->setQuantity(1);
            $this->multipleWishlistItemRepository->save($wishlistItem);

            $this->messageManager->addSuccessMessage(
                __('Product added to the wishlist successfully.')
            );
        } catch (\Exception $e) {
            $this->logger->error('Error adding product to wishlist: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('An error occurred while adding the product to the wishlist. Please try again.')
            );
        }

        return $resultRedirect->setUrl(
            $this->redirect->getRefererUrl()
        );
    }
}
