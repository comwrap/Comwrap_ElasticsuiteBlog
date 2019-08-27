<?php
namespace Comwrap\ElasticsuiteBlog\Model\Post\Indexer;

use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Comwrap\ElasticsuiteBlog\Model\Post\Indexer\Fulltext\Action\Full;
use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class Fulltext implements ActionInterface, MviewActionInterface
{
    /**
     * @var string
     */
    const INDEXER_ID = 'elasticsuite_blog_fulltext';

    /**
     * @var IndexerInterface
     */
    private $indexerHandler;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @var Full
     */
    private $fullAction;

    /**
     * @param Full                  $fullAction       The full index action
     * @param IndexerInterface      $indexerHandler   The index handler
     * @param StoreManagerInterface $storeManager     The Store Manager
     * @param DimensionFactory      $dimensionFactory The dimension factory
     * @param array                 $data             The data
     */
    public function __construct(
        Full $fullAction,
        IndexerInterface $indexerHandler,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory
    ) {
        $this->fullAction = $fullAction;
        $this->indexerHandler = $indexerHandler;
        $this->storeManager = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids The ids
     *
     * @return void
     */
    public function execute($ids)
    {
        $storeIds = array_keys($this->storeManager->getStores());
        /** @var IndexerHandler $saveHandler */
        $saveHandler = $this->indexerHandler;
        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $saveHandler->deleteIndex([$dimension], new \ArrayObject($ids));
            $saveHandler->saveIndex([$dimension], $this->fullAction->rebuildStoreIndex($storeId, $ids));
        }
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $storeIds = array_keys($this->storeManager->getStores());

        /** @var IndexerHandler $saveHandler */
        $saveHandler = $this->indexerHandler;

        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $saveHandler->cleanIndex([$dimension]);
            $saveHandler->saveIndex([$dimension], $this->fullAction->rebuildStoreIndex($storeId));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function executeList(array $categoryIds)
    {
        $this->execute($categoryIds);
    }

    /**
     * {@inheritDoc}
     */
    public function executeRow($categoryId)
    {
        $this->execute([$categoryId]);
    }
}
