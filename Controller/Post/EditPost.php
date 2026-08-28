<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */
declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Controller\Post\AbstractPost;
use BrunoDuarte\MultipleWishlist\Helper\Data;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class EditPost
 *
 * @package BrunoDuarte\MultipleWishlist\Controller\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends AbstractPost
{
    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * @var MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * EditPost constructor
     *
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param Validator $formKeyValidator
     * @param Data $helperModule
     * @param SessionFactory $sessionFactory
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
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
        MultipleWishlistFactory $multipleWishlistFactory
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
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->init();

            if (!$this->formKeyValidator->validate($this->request)) {
                throw new LocalizedException(
                    __('Something went wrong while saving the page. Please refresh the page and try again.')
                );
            }

            $multipleWishlistParams = $this->request->getParam('multiple_wishlist');
            if (empty($multipleWishlistParams)) {
                throw new LocalizedException(
                    __('You must fill in all fields required to edit list.')
                );
            }

            // continuar com o processo de edição...

        } catch (LocalizedException $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('customer/account/login/');
        } catch (\Exception $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('multiple_wishlist/page/create/');
        }

        $resultRedirect->setHttpResponseCode(301);
        return $resultRedirect;
    }
}
