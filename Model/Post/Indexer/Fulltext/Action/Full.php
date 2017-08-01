<?php
namespace Comwrap\ElasticsuiteBlog\Model\Post\Indexer\Fulltext\Action;

use Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Indexer\Fulltext\Action\Full as ResourceModel;
use Magento\Cms\Model\Template\FilterProvider;

class Full
{
    /**
     * @var \Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Indexer\Fulltext\Action\Full
     */
    private $resourceModel;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * Constructor.
     *
     * @param ResourceModel  $resourceModel  Indexer resource model.
     * @param FilterProvider $filterProvider Model template filter provider.
     */
    public function __construct(ResourceModel $resourceModel, FilterProvider $filterProvider)
    {
        $this->resourceModel  = $resourceModel;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Get data for a list of blog posts in a store id.
     *
     * @param integer    $storeId    Store id.
     * @param array|null $blogPostIds List of cms page ids.
     *
     * @return \Traversable
     */
    public function rebuildStoreIndex($storeId, $blogPostIds = null)
    {
        $lastBlogPostId  = 0;

        do {
            $blogPosts = $this->getSearchableBlogPost($storeId, $blogPostIds, $lastBlogPostId);
            foreach ($blogPosts as $postData) {
                $postData = $this->processPostData($postData);
                $lastBlogPostId = (int) $postData['post_id'];
                yield $lastBlogPostId => $postData;
            }
        } while (!empty($blogPosts));
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
    private function getSearchableBlogPost($storeId, $blogPostIds = null, $fromId = 0, $limit = 100)
    {
        return $this->resourceModel->getSearchableBlogPost($storeId, $blogPostIds, $fromId, $limit);
    }

    /**
     * Parse template processor blog poct content
     *
     * @param array $postData Blog post data.
     *
     * @return array
     */
    private function processPostData($postData)
    {
        if (isset($postData['content'])) {
            $postData['content'] = $this->filterProvider->getPageFilter()->filter($postData['content']);
        }

        return $postData;
    }
}
