@extends('layouts.app')

@section('title', 'Dynamic Fields — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">Categories</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ $category->name }} — Fields</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">Dynamic Fields</h1>
        <p class="text-sm text-[#6B7280] mt-1">
            Fields for <span class="font-medium text-[#6B21A8]">{{ $category->name }}</span>
            @if($category->parent_id)
                <span class="text-[#6B7280]"> (subcategory)</span>
            @endif
        </p>
    </div>
    <x-button variant="primary" href="{{ route('admin.categories.fields.create', $category) }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Field
    </x-button>
</div>

<x-card>
    @if($fields->isEmpty())
        <x-empty-state
            message="No dynamic fields yet."
            action="Add Field"
            :href="route('admin.categories.fields.create', $category)"
        />
    @else
        <x-data-table :headers="['Label', 'Type', 'Required', 'Sort', 'Options', 'Actions']">
            @foreach($fields as $field)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3">
                    <div class="font-medium text-[#1F2937]">{{ $field->label }}</div>
                    <div class="text-xs text-[#6B7280]" dir="{{ app()->getLocale() === 'ar' ? 'ltr' : 'rtl' }}">{{ $field->getTranslation('label', app()->getLocale() === 'ar' ? 'en' : 'ar') }}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-[#F5F3FF] text-[#6B21A8]">
                        {{ ucfirst($field->field_type) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($field->is_required)
                        <span class="text-[#16A34A] font-medium text-sm">Yes</span>
                    @else
                        <span class="text-[#6B7280] text-sm">No</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $field->sort_order }}</td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    @if($field->field_type === 'select' && !empty($field->options))
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($field->options, 0, 3) as $opt)
                                <span class="px-1.5 py-0.5 bg-[#F5F3FF] text-[#6B21A8] text-xs rounded">{{ $opt }}</span>
                            @endforeach
                            @if(count($field->options) > 3)
                                <span class="text-xs text-[#6B7280]">+{{ count($field->options) - 3 }} more</span>
                            @endif
                        </div>
                    @else
                        <span class="text-[#6B7280]">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <x-button variant="outline" size="sm" :href="route('admin.categories.fields.edit', [$category, $field])">
                            Edit
                        </x-button>
                        <form method="POST" action="{{ route('admin.categories.fields.destroy', [$category, $field]) }}" onsubmit="return confirm('Delete this field?')">
                            @csrf
                            @method('DELETE')
                            <x-button variant="danger" size="sm" type="submit">Delete</x-button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </x-data-table>
    @endif
</x-card>

@endsection
