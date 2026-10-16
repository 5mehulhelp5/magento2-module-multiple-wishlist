<?php
/**
 * @category  BrunoDuarte
 * @package   BrunoDuarte_MultipleWishlist
 * @copyright Copyright (c) 2026 BrunoDuarte
 */
declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Controller\Product;

// use Magento\Checkout\Controller\Cart as CartController;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;

/**
 * Abstract class product controller
 *
 * @package BrunoDuarte\MultipleWishlist\Controller\Product
 */
abstract class AbstractProduct implements ActionInterface
{
    /** @var RedirectFactory */
    protected $redirectResultFactory;

    /** @var Redirect */
    protected $redirect;

    /**
     * AbstractProduct constructor.
     *
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(RedirectFactory $redirectFactory)
    {
        $this->redirectResultFactory = $redirectFactory;
    }

    abstract public function execute(): Redirect;
}
