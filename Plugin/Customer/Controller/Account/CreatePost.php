<?php

namespace Magemadani\Loginwithmobile\Plugin\Customer\Controller\Account;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;

/**
 * Class CreatePost
 */
class CreatePost
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var RedirectInterface
     */
    protected $_redirect;

    /**
     * CreatePost constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param Session $customerSession
     * @param RedirectInterface $_redirect
     */
    public function __construct(
        UrlFactory        $urlFactory,
        RedirectFactory   $resultRedirectFactory,
        CollectionFactory $customerCollectionFactory,
        ManagerInterface  $messageManager,
        RequestInterface  $request,
        Session           $customerSession,
        RedirectInterface $_redirect
    ) {
        $this->messageManager = $messageManager;
        $this->_redirect = $_redirect;
        $this->session = $customerSession;
        $this->urlModel = $urlFactory->create();
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed
    )
    {
        $postData = $this->request->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($mobile = $postData['mobile_number']) {
            $customer = $this->customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('mobile_number', $mobile);

            if (!empty($customer)) {
                $this->messageManager->addErrorMessage('Mobile Number Already Exist');
                $this->session->setCustomerFormData($this->request->getPostValue());
                $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                return $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
            }
        }
        return $proceed();
    }
}
