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
 * Class EditPost
 */
class EditPost
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
     * EditPost constructor.
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
     * @param \Magento\Customer\Controller\Account\EditPost $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\EditPost $subject,
        \Closure $proceed
    )
    {
        $postData = $this->request->getParams();
        if ($mobile = $postData['mobile_number']) {
            $verifyNumber = $postData['mobile_number_verified'];
            $customer = $this->customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('mobile_number', $mobile);
            foreach ($customer as $item) {
                $customerNumber = $item->getMobileNumber();
            }
            if (!empty($customer) && $verifyNumber != $customerNumber) {
                $this->messageManager->addError('Mobile Number Already Exist');
                $this->session->setCustomerFormData($this->request->getParams());
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/edit');
                return $resultRedirect;
            }
        }
        return $proceed();
    }

}
