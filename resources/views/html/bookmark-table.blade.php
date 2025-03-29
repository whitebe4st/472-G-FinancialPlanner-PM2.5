@foreach($bookmarkedTransactions as $bookmark)
<tr data-bookmark-id="{{ $bookmark->bookmark_id }}">
    <td>
        <input type="checkbox" 
               class="bookmark-checkbox" 
               id="bookmark-checkbox-{{ $bookmark->bookmark_id }}" 
               data-id="{{ $bookmark->bookmark_id }}" 
               autocomplete="off">
    </td>
    <td data-category="{{ $bookmark->category }}">{{ $bookmark->description }}</td>
    <td>${{ number_format($bookmark->amount, 2) }}</td>
    <td data-type="{{ $bookmark->type }}">
        <span class="{{ $bookmark->type === 'income' ? 'income' : 'expense' }}">
            {{ ucfirst($bookmark->type) }}
        </span>
    </td>
    <td>{{ $bookmark->category }}</td>
</tr>
@endforeach 