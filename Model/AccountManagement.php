<?php


namespace Magemadani\Loginwithmobile\Model;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class AccountManagement
 */

class AccountManagement
{
    /**
     * @var CollectionFactory
     */
    protected $customerFactory;

    /**
     * AccountManagement constructor.
     * @param CollectionFactory $CustomerFactory
     */

    public function __construct(CollectionFactory $CustomerFactory)
    {
        $this->customerFactory = $CustomerFactory;
    }

    /**
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param $username
     * @param $password
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeAuthenticate(\Magento\Customer\Model\AccountManagement $subject, $username, $password): array
    {
        if ($username) {
            $customer = $this->customerFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('mobile_number', $username)
                ->getFirstItem();

            if (!empty($customer)) {
                $username = $customer->getEmail();
            }
        }
        return [$username, $password];
    }
}
