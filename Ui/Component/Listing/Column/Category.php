<?php

namespace Utklasad\AdminProductGridCategoryFilter\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $productId = $item['entity_id'];

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);

                $categoryIds = $product->getCategoryIds();
                $categories = array();

                if (count($categoryIds)) {
                    foreach ($categoryIds as $categoryId) {
                        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
                        $categories[] = $this->getCategoryTree($category);
                    }
                }
                sort($categories);
                $item[$fieldName] = implode(', ', $categories);
            }
        }

        return $dataSource;
    }

    protected function getCategoryTree($category) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $parentCategory = $objectManager->create('Magento\Catalog\Model\Category')->load($category->getParentId());
        if ($parentCategory->getId() && $parentCategory->getLevel() >= 1) {
            return $this->getCategoryTree($parentCategory) . ' » ' . $category->getName();
        } else {
            return $category->getName();
        }
    }
}
