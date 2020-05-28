<?php

namespace Comwrap\ElasticsuiteBlog\Model\Autocomplete\Post;

use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Helper\Autocomplete as ConfigurationHelper;
use Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\CollectionFactory as PostCollectionFactory;
use Smile\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider as TermDataProvider;

/**
 * Catalog product autocomplete data provider.
 *
 */
class DataProvider implements DataProviderInterface
{
    /**
     * Autocomplete type
     */
    const AUTOCOMPLETE_TYPE = "blog_post";

    /**
     * Autocomplete result item factory
     *
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * Query factory
     *
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var TermDataProvider
     */
    protected $termDataProvider;

    /**
     * @var CmsCollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string Autocomplete result type
     */
    private $type;

    /**
     * Max posts autocomplete search results
     * @var int
     */
    protected $maxAutocompleteResults = 5;

    /**
     * Constructor.
     *
     * @param ItemFactory           $itemFactory          Suggest item factory.
     * @param QueryFactory          $queryFactory         Search query factory.
     * @param TermDataProvider      $termDataProvider     Search terms suggester.
     * @param postCollectionFactory  $postCollectionFactory Post collection factory.
     * @param ConfigurationHelper   $configurationHelper  Autocomplete configuration helper.
     * @param StoreManagerInterface $storeManager         Store manager.
     * @param string                $type                 Autocomplete provider type.
     */
    public function __construct(
        ItemFactory $itemFactory,
        QueryFactory $queryFactory,
        TermDataProvider $termDataProvider,
        PostCollectionFactory $postCollectionFactory,
        ConfigurationHelper $configurationHelper,
        StoreManagerInterface $storeManager,
        $type = self::AUTOCOMPLETE_TYPE
    ) {
        $this->itemFactory          = $itemFactory;
        $this->queryFactory         = $queryFactory;
        $this->termDataProvider     = $termDataProvider;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->configurationHelper  = $configurationHelper;
        $this->type                 = $type;
        $this->storeManager         = $storeManager;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        $result = [];
        if ($this->configurationHelper->isEnabled($this->getType())) {
            $postCollection = $this->getBlogPostCollection();
            $i = 0;
            if ($postCollection) {
                /** @var \Magefan\Blog\Model\Post $post */
                foreach ($postCollection as $post) {
                    $result[] = $this->itemFactory->create([
                            'title' => $post->getTitle(),
                            'url'   => $post->getPostUrl(),
                            'type' => $this->getType()]);
                    $i++;
                    if ($i == $this->maxAutocompleteResults) {
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * List of search terms suggested by the search terms data daprovider.
     *
     * @return array
     */
    private function getSuggestedTerms()
    {
        $terms = array_map(
            function (\Magento\Search\Model\Autocomplete\Item $termItem) {
                return $termItem->getTitle();
            },
            $this->termDataProvider->getItems()
        );

        return $terms;
    }

    /**
     * Suggested post collection.
     * Returns null if no suggested search terms.
     *
     * @return \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Fulltext\Collection|null
     */
    private function getBlogPostCollection()
    {
        $pageCollection = null;
        $suggestedTerms = $this->getSuggestedTerms();
        $terms          = [$this->queryFactory->get()->getQueryText()];

        if (!empty($suggestedTerms)) {
            $terms = array_merge($terms, $suggestedTerms);
        }

        $postCollection = $this->postCollectionFactory->create();
        $postCollection->addSearchFilter($terms);
        $postCollection->setPageSize($this->getResultsPageSize());

        return $postCollection;
    }

    /**
     * Retrieve number of pages to display in autocomplete results
     *
     * @return int
     */
    private function getResultsPageSize()
    {
        return $this->configurationHelper->getMaxSize($this->getType());
    }
}
