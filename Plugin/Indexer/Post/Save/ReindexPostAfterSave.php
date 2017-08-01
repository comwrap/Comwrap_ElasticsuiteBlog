<?php
namespace Comwrap\ElasticsuiteBlog\Plugin\Indexer\Post\Save;

use Magento\Framework\Indexer\IndexerRegistry;
use Comwrap\ElasticsuiteBlog\Model\Post\Indexer\Fulltext;
use Magefan\Blog\Model\Post;


class ReindexPostAfterSave
{
    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * ReindexCategoryAfterSave constructor.
     *
     * @param IndexerRegistry $indexerRegistry The indexer registry
     */
    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Reindex blog posts data into search engine after saving the blog post
     *
     * @param Post $subject The blog post being reindexed
     * @param Post $result  The parent function we are plugged on
     *
     * @return \Magefan\Blog\Model\Post
     */
    public function afterSave(
        Post $subject,
        $result
    ) {
        if ($subject->getIsSearchable()) {
            $blogPostIndexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
            $blogPostIndexer->reindexRow($subject->getId());
        }

        return $result;
    }
}
