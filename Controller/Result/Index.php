<?php

namespace Comwrap\ElasticsuiteBlog\Controller\Result;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * PHP Constructor
     *
     * @param \Magento\Framework\App\Action\Context      $context     Action context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory Page Factory
     *
     * @return Index
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $pageFactory;
    }

    /**
     * Execute the action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
