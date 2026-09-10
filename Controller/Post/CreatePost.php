<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BrunoDuarte\MultipleWishlist\Model\MultipleWishlistFactory;
use BrunoDuarte\MultipleWishlist\Controller\Post\AbstractPost;
use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreatePost
 *
 * @package BrunoDuarte\MultipleWishlist\Controller\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends AbstractPost
{
    protected MultipleWishlistRepositoryInterface $multipleWishlistRepository;
    protected MultipleWishlistFactory $multipleWishlistFactory;

    /**
     * CreatePost constructor.
     *
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
                    __('You must fill in all fields to create a new list.')
                );
            }

            $multipleWishlist = $this->createMultipleWishlist($multipleWishlistParams);
            $multipleWishlistId = $multipleWishlist->getId();

            $this->setSuccessMessage(__(
                'List with ID %1 created successfully.',
                $multipleWishlistId
            )->render());

            $resultRedirect->setPath('multiple_wishlist/page/listing/');

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

    protected function createMultipleWishlist(array $multipleWishlistParams): MultipleWishlistInterface
    {
        $customer = $this->getSession()->getCustomer();

        if (!$this->helperModule->isCustomerGroupIdAvailable((int) $customer->getGroupId())) {
            throw new LocalizedException(__('This customer can\'t have more lists.'));
        }

        $titleSanitized = filter_var($multipleWishlistParams['title'], FILTER_SANITIZE_SPECIAL_CHARS);

        $multipleWishlist = $this->multipleWishlistFactory->create();
        $multipleWishlist->setStoreId((int) $this->getStore()->getId())
            ->setCustomerId((int) $customer->getId())
            ->setTitle($titleSanitized)
            ->setIsActive(!!$multipleWishlistParams['is_active']);

        return $this->multipleWishlistRepository->save($multipleWishlist);
    }
}
