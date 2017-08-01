<?php

namespace Comwrap\ElasticsuiteBlog\Block\Post;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\CollectionFactory as PostCollectionFactory;
use Comwrap\ElasticsuiteBlog\Helper\Configuration;

class Suggest extends \Magento\Framework\View\Element\Template
{
    /**
     * Name of field to get max results.
     *
     * @var string
     */
    const MAX_RESULT = 'max_result';

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Configuration
     */
    private $helper;

    /**
     * @var \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\Collection
     */
    private $postCollection;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext          $context               Template contexte.
     * @param QueryFactory             $queryFactory          Query factory.
     * @param PostCollectionFactory   $postCollectionFactory  Post collection factory.
     * @param Configuration            $helper                Configuration helper.
     * @param array                    $data                  Data.
     */
    public function __construct(
        TemplateContext $context,
        QueryFactory $queryFactory,
        PostCollectionFactory $postCollectionFactory,
        Configuration $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->helper         = $helper;
        $this->postCollection = $this->initPostCollection($postCollectionFactory);
    }

    /**
     * Returns if block can be display.
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->getResultCount() > 0;
    }

    /**
     * Returns blog post collection.
     *
     * @return \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\Collection
     */
    public function getPostCollection()
    {
        return $this->postCollection;
    }

    /**
     * Returns number of results.
     *
     * @return int
     */
    public function getNumberOfResults()
    {
        return $this->helper->getConfigValue(self::MAX_RESULT);
    }

    /**
     * Returns collection size.
     *
     * @return int|null
     */
    public function getResultCount()
    {
        return $this->getPostCollection()->getSize();
    }

    /**
     * Returns query.
     *
     * @return \Magento\Search\Model\Query
     */
    public function getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Returns query text.
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->getQuery()->getQueryText();
    }

    /**
     * Returns all results url page.
     *
     * @return string
     */
    public function getShowAllUrl()
    {
        return $this->getUrl('elasticsuite_blog/result', ['q' => $this->getQueryText()]);
    }

    /**
     * Init blog post collection.
     *
     * @param PostCollectionFactory $collectionFactory Blog post collection.
     *
     * @return mixed
     */
    private function initPostCollection($collectionFactory)
    {
        $postCollection = $collectionFactory->create();

        $postCollection->setPageSize($this->getNumberOfResults());

        $queryText = $this->getQueryText();
        $postCollection->addSearchFilter($queryText);

        return $postCollection;
    }
}
