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
use Magento\TestFramework\Helper\Customer;
use Psr\Log\LoggerInterface;

/**
 * Class EditPost
 *
 * @package BrunoDuarte\MultipleWishlist\Controller\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends AbstractPost implements HttpPostActionInterface
{
    protected MultipleWishlistRepositoryInterface $multipleWishlistRepository;
    protected MultipleWishlistFactory $multipleWishlistFactory;
    protected object $customerSession;

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
        $this->customerSession = $sessionFactory->create();
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->init();

            $validFormKey = $this->formKeyValidator->validate($this->request);
            if (!$validFormKey) {
                throw new LocalizedException(
                    __('Something went wrong while saving the page. Please refresh the page and try again.')
                );
            }

            // form data
            $multipleWishlistParams = $this->request->getParam('multiple_wishlist');
            if (empty($multipleWishlistParams)) {
                throw new LocalizedException(
                    __('You must fill in all fields required to edit list.')
                );
            }

            $multipleWishlistParams = $this->sanitizeFormData($multipleWishlistParams);
            $this->validateFormData($multipleWishlistParams);

            // carrega os dados da wishlist pelo id.
            $wishlist = $this->multipleWishlistRepository->getById($multipleWishlistParams['id']);

            /** @todo - Eu não sei se essa lógica fica aqui, validar. */
            if ( intval($wishlist->getCustomerId()) !== intval($this->customerSession->getCustomerId()) ) {
                throw new LocalizedException( __( 'You are not allowed to edit this wishlist.' ) );
            }

            // atualiza os dados da wishlist com os dados do formulário.
            $wishlist->setTitle($multipleWishlistParams['title']);
            $wishlist->setIsActive($multipleWishlistParams['is_active']);

            // persiste os dados da wishlist no banco de dados.
            $this->multipleWishlistRepository->save($wishlist);

            // mensagem de sucesso.
            $message = (string) __('Wishlist %1 was updated!', $multipleWishlistParams['title']);
            $this->messageManager->addSuccessMessage($message);

            // redirecionamento para a página de listagem de wishlists.
            $resultRedirect->setPath('multiple_wishlist/page/listing');

        } catch(LocalizedException $exception) {
            $this->setErrorMessage($exception->getMessage());
            $resultRedirect->setPath('multiple_wishlist/page/edit', ['id' => $multipleWishlistParams['id'] ?? 0]);
        } catch (\Exception $exception) {

            $this->setErrorMessage(__('We can\'t update your wishlist right now.'));
            $this->logger->error($exception->getMessage());
            $resultRedirect->setPath('multiple_wishlist/page/listing/');
        }

        $resultRedirect->setHttpResponseCode(301);
        return $resultRedirect;
    }

    private function sanitizeFormData(array $multipleWishlistParams): array
    {
        $multipleWishlistParams['id']        = (int) filter_var($multipleWishlistParams['id'], FILTER_VALIDATE_INT) ?? 0;
        $multipleWishlistParams['title']     = (string) filter_var($multipleWishlistParams['title'], FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $multipleWishlistParams['is_active'] = (bool) filter_var($multipleWishlistParams['is_active'], FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0,
                'max_range' => 1
            ]
        ]) ?? false;

        return $multipleWishlistParams;
    }

    private function validateFormData(array $multipleWishlistParams): void
    {
        if (empty($multipleWishlistParams['id'])) {
            throw new LocalizedException(__('Wishlist ID is required.'));
        }

        if (empty($multipleWishlistParams['title'])) {
            throw new LocalizedException(__('Title is required.'));
        }

        if (mb_strlen($multipleWishlistParams['title']) > 255) {
            throw new LocalizedException(__('Title must be less than 255 characters.'));
        }

        if (mb_strlen($multipleWishlistParams['title']) < 3) {
            throw new LocalizedException(__('Title must be at least 3 characters.'));
        }

        if (!isset($multipleWishlistParams['is_active'])) {
            throw new LocalizedException(__('Is Active is required.'));
        }
    }
}
