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
                <div class="dropdown">
                    <button class="filter-button">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                        </svg>
                        <span>Filter</span>
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M6 9L12 15L18 9" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                        </svg>
                    </button>
                    <div class="dropdown-content">
                        <a href="#" data-filter="all">All</a>
                        <a href="#" data-filter="income">Income</a>
                        <a href="#" data-filter="expense">Expense</a>
                    </div>
                </div>
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

        <div class="pagination-wrapper">
            {{ $bookmarkedTransactions->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        gap: 0.5rem;
    }

    .pagination li {
        display: flex;
    }

    .pagination li a,
    .pagination li span {
        padding: 0.5rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination li.active span {
        background: #71D881;
        color: white;
        border-color: #71D881;
    }

    .pagination li a:hover {
        background: var(--hover-bg);
    }

    [data-theme="dark"] .pagination li a,
    [data-theme="dark"] .pagination li span {
        border-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush


