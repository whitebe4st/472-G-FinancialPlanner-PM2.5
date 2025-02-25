@extends('layout/layout')

@section('title')
    Bookmark
@endsection

@section('content')
<div class="content-wrapper" style="padding: 2rem;">
    <h1 style="margin-bottom: 1rem;">Bookmark</h1>
    <p style="margin-bottom: 2rem;">Your recurring transactions.</p>

    <div id="transactionTable">
        <div class="transaction-header">
            <h2 class="transaction-title">Bookmark</h2>
            <div class="transaction-actions">
                <button class="filter-button">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                    </svg>
                    Filter
                </button>
                <button class="year-button">
                    <span>Yearly</span>
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M6 9L12 15L18 9" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                    </svg>
                </button>
            </div>
        </div>

        <table style="width: 100%;">
            <tr class="head-table">
                <th style="width: 5%">
                    <input type="checkbox" class="select-all">
                </th>
                <th style="width: 25%">Transaction</th>
                <th style="width: 20%">Amount</th>
                <th style="width: 15%">Type</th>
                <th style="width: 35%">Note</th>
            </tr>
            @foreach($bookmarkedTransactions as $transaction)
            <tr>
                <td>
                    <input type="checkbox" class="transaction-checkbox">
                </td>
                <td data-category="{{ $transaction->category }}">{{ $transaction->description }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td data-type="{{ $transaction->type }}">
                    <span class="{{ $transaction->type === 'income' ? 'income' : 'expense' }}">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </td>
                <td>{{ $transaction->note ?? '-' }}</td>
            </tr>
            @endforeach
        </table>

        <div class="table-actions">
            <button class="action-btn items-btn">
                <span class="count">0</span> Items
            </button>
            <button class="action-btn edit-btn">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path d="M11 4H4V20H20V13" stroke="#A0A0A0" stroke-width="2"/>
                    <path d="M18 5L21 8L12 17H9V14L18 5Z" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                </svg>
                Edit
            </button>
            <button class="action-btn remove-btn">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path d="M4 7H20M10 11V17M14 11V17M5 7L6 19C6 19.5304 6.21071 20.0391 6.58579 20.4142C6.96086 20.7893 7.46957 21 8 21H16C16.5304 21 17.0391 20.7893 17.4142 20.4142C17.7893 20.0391 18 19.5304 18 19L19 7M9 7V4C9 3.73478 9.10536 3.48043 9.29289 3.29289C9.48043 3.10536 9.73478 3 10 3H14C14.2652 3 14.5196 3.10536 14.7071 3.29289C14.8946 3.48043 15 3.73478 15 4V7" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                </svg>
                Remove
            </button>
            <button class="action-btn close-btn">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#A0A0A0" stroke-width="2"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection


