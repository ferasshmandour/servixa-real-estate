<?php

namespace App\Services;

use App\Models\Category;
use App\Models\DynamicField;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function listParents(): Collection
    {
        return Category::whereNull('parent_id')
            ->withCount('children')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): Category
    {
        $category = new Category();
        $category->setTranslation('name', 'ar', $data['name_ar'])
                 ->setTranslation('name', 'en', $data['name_en']);
        $category->parent_id   = !empty($data['parent_id']) ? $data['parent_id'] : null;
        $category->icon        = $data['icon'] ?? null;
        $category->sort_order  = $data['sort_order'] ?? 0;
        $category->save();

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->setTranslation('name', 'ar', $data['name_ar'])
                  ->setTranslation('name', 'en', $data['name_en']);
        $category->parent_id   = !empty($data['parent_id']) ? $data['parent_id'] : null;
        $category->icon        = $data['icon'] ?? null;
        $category->sort_order  = $data['sort_order'] ?? 0;
        $category->save();

        return $category;
    }

    public function delete(Category $category): void
    {
        abort_if(
            $category->children()->exists(),
            422,
            'Cannot delete a category that has subcategories. Delete subcategories first.'
        );

        abort_if(
            $category->services()->exists(),
            422,
            'Cannot delete a category that has active services.'
        );

        $category->delete();
    }

    public function createField(Category $category, array $data): DynamicField
    {
        $field = new DynamicField();
        $field->category_id  = $category->id;
        $field->setTranslation('label', 'ar', $data['label_ar'])
               ->setTranslation('label', 'en', $data['label_en']);
        $field->field_type   = $data['field_type'];
        $field->options      = $this->parseOptions($data);
        $field->is_required  = isset($data['is_required']) ? (bool) $data['is_required'] : false;
        $field->sort_order   = $data['sort_order'] ?? 0;
        $field->save();

        return $field;
    }

    public function updateField(DynamicField $field, array $data): DynamicField
    {
        $field->setTranslation('label', 'ar', $data['label_ar'])
               ->setTranslation('label', 'en', $data['label_en']);
        $field->field_type  = $data['field_type'];
        $field->options     = $this->parseOptions($data);
        $field->is_required = isset($data['is_required']) ? (bool) $data['is_required'] : false;
        $field->sort_order  = $data['sort_order'] ?? 0;
        $field->save();

        return $field;
    }

    public function deleteField(DynamicField $field): void
    {
        $field->delete();
    }

    public function allWithSubcategories(): Collection
    {
        return Category::whereNull('parent_id')
            ->with([
                'dynamicFields' => fn($q) => $q->orderBy('sort_order'),
                'children'      => fn($q) => $q->orderBy('sort_order'),
                'children.dynamicFields' => fn($q) => $q->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function parseOptions(array $data): ?array
    {
        if (($data['field_type'] ?? '') !== 'select') {
            return null;
        }

        $raw = $data['options_raw'] ?? '';
        $options = array_filter(array_map('trim', explode("\n", $raw)));

        return array_values($options);
    }
}
