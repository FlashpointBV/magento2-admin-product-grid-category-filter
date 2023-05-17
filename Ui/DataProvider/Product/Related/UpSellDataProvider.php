<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Utklasad\AdminProductGridCategoryFilter\Ui\DataProvider\Product\Related;

use Magento\Framework\Api\Filter;

/**
 * Class UpSellDataProvider
 *
 * @api
 * @since 101.0.0
 */
class UpSellDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider
{
    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    protected function getLinkType()
    {
        return 'up_sell';
    }

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
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
            return;
        }

        parent::addFilter($filter);
    }

}
