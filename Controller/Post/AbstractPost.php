<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Post;

use BrunoDuarte\MultipleWishlist\Helper\Data as HelperModule;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract class AbstractPost
 *
 * @category BrunoDuarte\MultipleWishlist\Controller\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractPost implements ActionInterface
{
    protected RedirectFactory $resultRedirectFactory;
    protected RedirectInterface $redirect;
    protected Validator $formKeyValidator;
    protected HelperModule $helperModule;
    protected SessionFactory $sessionFactory;
    protected RequestInterface $request;
    protected StoreManagerInterface $storeManager;
    protected ManagerInterface $messageManager;
    protected LoggerInterface $logger;

    /**
     * AbstractPost constructor.
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param Validator $formKeyValidator
     * @param HelperModule $helperModule
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
        HelperModule $helperModule,
        SessionFactory $sessionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->formKeyValidator = $formKeyValidator;
        $this->helperModule = $helperModule;
        $this->sessionFactory = $sessionFactory;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    protected function init(): void
    {
        $session = $this->getSession();

        if (!$this->helperModule->isModuleEnable()) {
            throw new LocalizedException(__('The multiple wishlist module is disabled.'));
        }

        /**
         * @todo Verificar se o redirect bloco catch está redirecionando para a página de login
         */
        if (!$session->isLoggedIn()) {
            throw new LocalizedException(
                __('Something went wrong while saving the page. Please refresh the page and try again.')
            );
        }

        $customerGroupId = (int) $session->getCustomer()->getGroupId();
        if (!$this->helperModule->isCustomerGroupIdAvailable($customerGroupId)) {
            throw new LocalizedException(__('This customer is not available to multiple wishlists.'));
        }
    }

    protected function getSession(): Session
    {
        return $this->sessionFactory->create();
    }

    protected function setSuccessMessage(string $messageSuccess): void
    {
        $this->messageManager->addSuccessMessage($messageSuccess);
        $this->logger->info($messageSuccess);
    }

    protected function setErrorMessage(string $errorSuccess): void
    {
        $this->messageManager->addErrorMessage($errorSuccess);
        $this->logger->error($errorSuccess);
    }

    protected function getStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }

    abstract public function execute(): Redirect;
}
