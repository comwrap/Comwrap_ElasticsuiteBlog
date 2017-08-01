<?php

namespace Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Indexer\Fulltext\Action;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Model\ResourceModel\Indexer\AbstractIndexer;

class Full extends AbstractIndexer
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * Full constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection     $resource     Resource Connection
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager Store Manager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool Metadata Pool
     */
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($resource, $storeManager);
    }

    /**
     * Load a bulk of blog post data.
     *
     * @param int     $storeId    Store id.
     * @param string  $blogPostIds Blog post ids filter.
     * @param integer $fromId     Load product with id greater than.
     * @param integer $limit      Number of product to get loaded.
     *
     * @return array
     */
    public function getSearchableBlogPost($storeId, $blogPostIds = null, $fromId = 0, $limit = 100)
    {
        $select = $this->getConnection()->select()
                       ->from(['p' => $this->getTable('magefan_blog_post')]);

        $this->addIsVisibleInStoreFilter($select, $storeId);

        if ($blogPostIds !== null) {
            $select->where('p.post_id IN (?)', $blogPostIds);
        }

        $select->where('p.post_id > ?', $fromId)
               ->where('p.is_searchable = ?', true)
               ->limit($limit)
               ->order('p.post_id');

        return $this->connection->fetchAll($select);
    }

    /**
     * Filter the select to append only blog post of current store.
     *
     * @param \Zend_Db_Select $select  Product select to be filtered.
     * @param integer         $storeId Store Id
     *
     * @return \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Indexer\Fulltext\Action\Full Self Reference
     */
    private function addIsVisibleInStoreFilter($select, $storeId)
    {
        $select->join(
            ['ps' => $this->getTable('magefan_blog_post_store')],
            "p.post_id = ps.post_id"
        );
        $select->where('ps.store_id IN (?)', [0, $storeId]);

        return $this;
    }
}
