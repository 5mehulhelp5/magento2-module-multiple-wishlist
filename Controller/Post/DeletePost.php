<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Controller\Post\AbstractPost;
use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class DeletePost
 * @package BrunoDuarte\MultipleWishlist\Controller\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeletePost extends AbstractPost
{
    protected MultipleWishlistRepositoryInterface $multipleWishlistRepository;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \BrunoDuarte\MultipleWishlist\Helper\Data $helperModule,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository
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
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->init();

            $multipleWishlistId = (int) $this->request->getParam('id');
            if (!$multipleWishlistId) {
                throw new LocalizedException(
                    __('You must pass a valid ID to delete a list.')
                );
            }

            $this->multipleWishlistRepository->deleteById($multipleWishlistId);

            $this->setSuccessMessage(__(
                'List with ID %1 was deleted successfully.',
                $multipleWishlistId
            )->render());

            $resultRedirect->setPath('multiple_wishlist/page/listing')->setHttpResponseCode(301);

        } catch (LocalizedException $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('customer/account/login/')
                ->setHttpResponseCode(301);
        } catch (\Exception $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('multiple_wishlist/page/listing/')
                ->setHttpResponseCode(301);
        }

        return $resultRedirect;
    }
}
