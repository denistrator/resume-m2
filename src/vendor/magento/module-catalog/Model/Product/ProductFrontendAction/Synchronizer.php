<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Product\ProductFrontendAction;

use Magento\Catalog\Api\Data\ProductFrontendActionInterface;
use Magento\Catalog\Model\FrontendStorageConfigurationInterface;
use Magento\Catalog\Model\FrontendStorageConfigurationPool;
use Magento\Catalog\Model\ProductFrontendActionFactory;
use Magento\Catalog\Model\ResourceModel\ProductFrontendAction\Collection;
use Magento\Catalog\Model\ResourceModel\ProductFrontendAction\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\EntityManager\EntityManager;

/**
 * A Product Widget Synchronizer.
 *
 * Service which allows to sync product widget information, such as product id with db. In order to reuse this info
 * on different devices
 */
class Synchronizer
{
    /**
     * Considered that for some action, customer should spent some time (e.g. products comparing or product page visit)
     * This constant used in order to track and filter suspicious actions, that happens frequently than expected
     */
    const TIME_TO_DO_ONE_ACTION = 1;

    /** Flag, which says, can we synchronize product actions with backend or not */
    const ALLOW_SYNC_WITH_BACKEND_PATH = "catalog/recently_products/synchronize_with_backend";

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var ProductFrontendActionFactory
     */
    private $productFrontendActionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FrontendStorageConfigurationPool
     */
    private $frontendStorageConfigurationPool;

    /**
     * @param Session $session
     * @param Visitor $visitor
     * @param ProductFrontendActionFactory $productFrontendActionFactory
     * @param EntityManager $entityManager
     * @param CollectionFactory $collectionFactory
     * @param FrontendStorageConfigurationPool $frontendStorageConfigurationPool
     */
    public function __construct(
        Session $session,
        Visitor $visitor,
        ProductFrontendActionFactory $productFrontendActionFactory,
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        FrontendStorageConfigurationPool $frontendStorageConfigurationPool
    ) {
        $this->session = $session;
        $this->visitor = $visitor;
        $this->productFrontendActionFactory = $productFrontendActionFactory;
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->frontendStorageConfigurationPool = $frontendStorageConfigurationPool;
    }

    /**
     * Finds lifetime in configuration.
     *
     * Configuration is hold in Stores Configuration. Also this configuration is generated by
     * {@see Magento\Catalog\Model\Widget\RecentlyViewedStorageConfiguration}
     *
     * @param string $namespace
     * @return int
     */
    private function getLifeTimeByNamespace($namespace)
    {
        $configurationObject = $this->frontendStorageConfigurationPool->get($namespace);
        if ($configurationObject) {
            $configuration = $configurationObject->get();
        } else {
            $configuration = [
                'lifetime' => FrontendStorageConfigurationInterface::DEFAULT_LIFETIME
            ];
        }

        return isset($configuration['lifetime']) ?
            (int) $configuration['lifetime'] : FrontendStorageConfigurationInterface::DEFAULT_LIFETIME;
    }

    /**
     * Filters actions.
     *
     * In order to avoid suspicious actions, we need to filter them in DESC order, and slice only items that
     * can be persisted in database.
     *
     * @param array $productsData (product action data, that came from frontend)
     * @param string $typeId namespace (type of action)
     * @return array
     */
    private function filterNewestActions(array $productsData, $typeId)
    {
        $lifetime = $this->getLifeTimeByNamespace($typeId);
        $actionsNumber = $lifetime * self::TIME_TO_DO_ONE_ACTION;

        uasort($productsData, function (array $firstProduct, array $secondProduct) {
            return $firstProduct['added_at'] > $secondProduct['added_at'];
        });

        return array_slice($productsData, 0, $actionsNumber, true);
    }

    /**
     * Retrieve product ids
     *
     * @param array $actions
     * @return array
     */
    private function getProductIdsByActions(array $actions)
    {
        $productIds = [];

        foreach ($actions as $action) {
            if (isset($action['product_id'])) {
                $productIds[] = $action['product_id'];
            }
        }

        return $productIds;
    }

    /**
     * Save ids by action -> recently viewed or recently compared product ids data (product id and js timestamp)
     * Javascript timestamp is used because all filtering information is done on frontend and Magento backend
     * application do not know about ids relevance
     *
     * @param array $productsData
     * @param string $typeId
     * @return void
     */
    public function syncActions(array $productsData, $typeId)
    {
        $productsData = $this->filterNewestActions($productsData, $typeId);
        $customerId = $this->session->getCustomerId();
        $visitorId = $this->visitor->getId();
        $collection = $this->getActionsByType($typeId);
        $productIds = $this->getProductIdsByActions($productsData);

        if ($productIds) {
            $collection->addFieldToFilter('product_id', $productIds);

            /**
             * Note that collection is also filtered by visitor id and customer id
             * This collection shouldn't be flushed when visitor has products and then login
             * It can remove only products for visitor, or only products for customer
             *
             * ['product_id' => 'added_at']
             * @var ProductFrontendActionInterface $item
             */
            foreach ($collection as $item) {
                $this->entityManager->delete($item);
            }

            foreach ($productsData as $productId => $productData) {
                /** @var ProductFrontendActionInterface $action */
                $action = $this->productFrontendActionFactory->create([
                    'data' => [
                        'visitor_id' => $customerId ? null : $visitorId,
                        'customer_id' => $this->session->getCustomerId(),
                        'added_at' => $productData['added_at'],
                        'product_id' => $productId,
                        'type_id' => $typeId
                    ]
                ]);

                $this->entityManager->save($action);
            }
        }
    }

    /**
     * Find and fetch product actions (id and timestamp) by type id (recently_viewed or recently_compared)
     *
     * @param string $typeId
     * @return Collection
     */
    public function getActionsByType($typeId)
    {
        $actions = $this->getAllActions();
        $actions->addFieldToFilter('type_id', $typeId);

        return $actions;
    }

    /**
     * Find and fetch product actions (id and timestamp)  (recently_viewed or recently_compared)
     *
     * @return Collection
     */
    public function getAllActions()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $customerId = $this->session->getCustomerId();
        $visitorId = $this->visitor->getId();
        $collection->addFilterByUserIdentities($customerId, $visitorId);

        return $collection;
    }
}