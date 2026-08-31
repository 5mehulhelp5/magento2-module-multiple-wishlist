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
use Magento\Framework\App\Action\HttpPostActionInterface;
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
class EditPost extends AbstractPost implements HttpPostActionInterface
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

            $validFormKey = $this->formKeyValidator->validate($this->request);
            if ($validFormKey) { // verificar se o método da requisição é post
                // form data
                $multipleWishlistParams = $this->request->getParam('multiple_wishlist');
                if (empty($multipleWishlistParams)) {
                    throw new LocalizedException(
                        __('You must fill in all fields required to edit list.')
                    );
                }

                // $storeId = (int) $this->getStore()->getId();
                $multipleWishlistParams['title']      = filter_var($multipleWishlistParams['title'], FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
                $multipleWishlistParams['is_active']  = filter_var($multipleWishlistParams['is_active'], FILTER_VALIDATE_INT, [
                    'options' => [
                        'min_range' => 0,
                        'max_range' => 1
                    ]
                ]) ?? 0;

                // Wishlist id
                $multipleWishlistId = $this->request->getParam('id');
                $result = $this->multipleWishlistRepository->update($multipleWishlistId, $multipleWishlistParams);
                if (!$result) {
                    throw new LocalizedException(__('Unable to update your wishlist.'));
                }

                $this->setSuccessMessage(__('Wishlist %s was updated!', $multipleWishlistParams['title']));
            }

            if (!$validFormKey) {
                $this->setErrorMessage(__('Form key invalid!'));
            }

            $resultRedirect->setPath('*/*/edit');

        } catch (LocalizedException $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('*/*/edit');
        } catch (\Exception $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('multiple_wishlist/page/create/');
        }

        $resultRedirect->setHttpResponseCode(301);
        return $resultRedirect;
    }
}
