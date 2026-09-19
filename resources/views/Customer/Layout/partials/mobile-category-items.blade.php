@foreach($items as $category)
    <li class="mobile-category-item {{ !empty($category['has_children']) ? 'has-children' : '' }}">
        <div class="mobile-category-row">
            <a href="{{ route('products_category', ['slug' => $category['slug']]) }}" class="mobile-category-link">
                {{ $category['name'] }}
            </a>
            @if(!empty($category['has_children']))
                <button type="button" class="mobile-category-toggle" aria-label="نمایش زیر‌دسته‌ها">
                    <i class="fa fa-angle-down"></i>
                </button>
            @endif
        </div>
        @if(!empty($category['has_children']))
            <ul class="mobile-category-children">
                @include('Customer.Layout.partials.mobile-category-items', ['items' => $category['children']])
            </ul>
        @endif
    </li>
@endforeach
