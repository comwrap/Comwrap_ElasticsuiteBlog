<?php
namespace Comwrap\ElasticsuiteBlog\Model\Post\Indexer\Fulltext\Action;

use Magento\Framework\Filter\RemoveTags;
use Comwrap\ElasticsuiteBlog\Model\ResourceModel\Post\Indexer\Fulltext\Action\Full as ResourceModel;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Store\Model\App\Emulation;

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
     * @var \Magento\Framework\App\AreaList
     */
    private $areaList;

    /**
     * @var \Magento\Framework\Filter\RemoveTags
     */
    private $stripTags;

    /**
     * Constructor.
     *
     * @param ResourceModel  $resourceModel  Indexer resource model.
     * @param FilterProvider $filterProvider Model template filter provider.
     * @param AreaList       $areaList       Area List
     * @param RemoveTags     $stripTags      HTML Tags remover
     */
    public function __construct(
        ResourceModel $resourceModel,
        FilterProvider $filterProvider,
        AreaList $areaList,
        RemoveTags $stripTags
    ) {
        $this->resourceModel  = $resourceModel;
        $this->filterProvider = $filterProvider;
        $this->areaList = $areaList;
        $this->stripTags = $stripTags;
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

        try {
            $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        } catch (\InvalidArgumentException | \LogicException $exception) {
            // Can occur especially when magento sample data are triggering a full reindex.
            ;
        }

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
            $content = html_entity_decode($this->filterProvider->getPageFilter()->filter($postData['content']));
            $content = $this->stripTags->filter($content);
            $content = preg_replace('/\s\s+/', ' ', $content);
            $postData['content'] = $content;
        }
        return $postData;
    }
}
