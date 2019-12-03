<?php


namespace Comwrap\ElasticsuiteBlog\Block\Post;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\CollectionFactory as PostCollectionFactory;

class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\Collection
     */
    private $postCollection;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext          $context               Template contexte.
     * @param QueryFactory             $queryFactory          Query factory.
     * @param PageCollectionFactory    $pageCollectionFactory Page collection factory.
     * @param array                    $data                  Data.
     */
    public function __construct(
        TemplateContext $context,
        QueryFactory $queryFactory,
        PostCollectionFactory $postCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->postCollection = $this->initPostCollection($postCollectionFactory);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->getSearchQueryText();
        $this->pageConfig->getTitle()->set($title);

        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                [
                	'label' => $title,
                	'title' => $title,
                	'link' => $this->getSearchUrl()
                ]
            )->addCrumb(
                'blog_search',
                [
                	'label' => __('Results in blog posts.'),
                	'title' => __('Results in blog posts.')
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        return __("Search results for: '%1'", $this->escapeHtml($this->getQueryText()));
    }

    /**
     * Returns catalog search url.
     *
     * @return string
     */
    public function getSearchUrl()
    {
	    $params = [QueryFactory::QUERY_VAR_NAME => $this->getQueryText()];
        return $this->getUrl('catalogsearch/result', ['_query' => $params]);
    }

    /**
     * Returns blog bost collection.
     *
     * @return \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\Collection
     */
    public function getPostCollection()
    {
        return $this->postCollection;
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
     * Init blog post collection.
     *
     * @param postCollectionFactory $collectionFactory Blog post collection.
     *
     * @return mixed
     */
    private function initPostCollection($collectionFactory)
    {
        $postCollection = $collectionFactory->create();

        $queryText = $this->getQueryText();
        $postCollection->addSearchFilter($queryText);

        return $postCollection;
    }
}
