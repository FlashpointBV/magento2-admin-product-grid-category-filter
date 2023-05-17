<?php

namespace Utklasad\AdminProductGridCategoryFilter\Ui\DataProvider\Product;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider as MagentoProductDataProvider;
use Magento\Framework\Api\Filter;

class ProductDataProvider extends MagentoProductDataProvider
{
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() === 'category_id') {
            if(in_array('-1', (array)$filter->getValue(), true)) {
                $this->getCollection()->getSelect()->joinLeft(
                    ['ccp' => $this->getCollection()->getTable('catalog_category_product')],
                    'e.entity_id = ccp.product_id',
                    []
                )->where('ccp.category_id IS NULL');
                return;
            }

            $this->getCollection()->addCategoriesFilter(['in' => $filter->getValue()]);
            return;
        }

        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]->addFilter(
                $this->getCollection(),
                $filter->getField(),
                [$filter->getConditionType() => $filter->getValue()]
            );
            return;
        }

        parent::addFilter($filter);
    }
}
