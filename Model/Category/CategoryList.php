<?php

namespace Utklasad\AdminProductGridCategoryFilter\Model\Category;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Option\ArrayInterface;

class CategoryList implements ArrayInterface
{
    public function __construct(
        private readonly CategoryManagementInterface $_categoryManagement
    ) {}

    public function toOptionArray(bool $addEmpty = true): array
    {
        $options = [];

        $this->renderTree($this->_categoryManagement->getTree(1), $options);

        $options = array_merge([
            ['label' => 'No categories assigned', 'value' => -1]
        ], $options);

        if ($addEmpty) {
            $options = array_merge([
                ['label' => __('-- Please Select a Category --'), 'value' => '']
            ], $options);
        }

        return $options;
    }

    private function renderTree($node, &$options, $path = '') {
        if ($node->getLevel() >= 1) {
            $path .= (empty($path) ? '' : ' » ') . $node->getName();
            $options[] = ['label' => $path, 'value' => $node->getId()];
        }

        $children = $node->getChildrenData();
        usort($children, function($a, $b) {
            return strcmp($a->getName() ?? '', $b->getName() ?? '');
        });

        foreach ($children as $child) {
            $this->renderTree($child, $options, $path);
        }
    }
}
