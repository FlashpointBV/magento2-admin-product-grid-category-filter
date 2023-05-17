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

    private function renderTree(CategoryInterface $category, array &$tree): void
    {
        if ($category->getLevel() >= 1) {
            $tree[] = $this->createOption($category);
        }

        foreach ($category->getChildrenData() as $childCategory) {
            $tree[] = [$childCategory->getId() => $this->createLabel($childCategory)];

            if ( ! $childCategory->getChildrenData()) {
                continue;
            }

            $this->renderTree($childCategory, $tree);
        }
    }

    private function createOption(CategoryInterface $category): array
    {
        return ['label' => $this->createLabel($category), 'value' => $category->getId()];
    }

    private function createLabel(CategoryInterface $category): string
    {
        $level = $category->getLevel() - 1;

        return sprintf(
            '%s %s (ID: %s)',
            '|' . ($level > 0 ? str_repeat('-', $level) : ''),
            $category->getName(),
            $category->getId()
        );
    }
}
