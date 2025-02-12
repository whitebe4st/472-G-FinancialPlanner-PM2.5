@extends('layout/layout')

@section('titie')
    Transaction
@endsection

@section('content')
<h1>Transactions</h1>
                <p>Track your finances and achieve your financial goal.</p>

                <div class="table-container">
                    <div class="table-header">
                        <h2>Transaction History</h2>
                        <div class="filter-buttons">
                            <button class="filter-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <!-- Filter icon path -->
                                    <path d="M4 4H20M8 12H16M10 20H14" stroke="#A0A0A0" stroke-width="2"/>
                                </svg>
                                Filter
                            </button>
                            <button class="filter-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24">
                                    <!-- Calendar icon path -->
                                    <rect x="4" y="4" width="16" height="16" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                                </svg>
                                Yearly
                            </button>
                        </div>
                    </div>

                    <table>
                        <thead class="head-table">
                            <tr>
                                <th><input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()"></th>
                                <th>Transaction</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(Auth::user()->transactions()->orderBy('transaction_date', 'desc')->get() as $transaction)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->transaction_date->format('d/m/y') }}</td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->type === 'expense' ? 'Exp.' : 'Inc.' }}</td>
                                <td>{{ $transaction->category }}</td>
                                <td>
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path d="M6 4H18V20L12 14L6 20V4Z" stroke="#A0A0A0" stroke-width="2" fill="none"/>
                                    </svg>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="addBar-btn">
                        <button onclick="showAddTransactionPopup()">+</button>
                    </div>

                    <div class="pagination">
                        <button class="page-nav" data-page="prev">&lt;</button>
                        <div class="page-numbers">
                            <button class="page-btn" data-page="1">1</button>
                            <button class="page-btn active" data-page="2">2</button>
                            <button class="page-btn" data-page="3">3</button>
                            <button class="page-btn" data-page="4">4</button>
                            <button class="page-btn" data-page="5">5</button>
                            <button class="page-btn" data-page="6">6</button>
                            <button class="page-btn" data-page="7">7</button>
                            <button class="page-btn" data-page="8">8</button>
                            <button class="page-btn" data-page="9">9</button>
                            <button class="page-btn" data-page="10">10</button>
                        </div>
                        <button class="page-nav" data-page="next">&gt;</button>
                    </div>
                </div>
@endsection

@section('addTransactionPopup')
<div class="popup-content">
            <h2>Add Transaction</h2>
            <form id="transactionForm">
                @csrf
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <div class="category-input-container">
                        <input 
                            type="text" 
                            id="category" 
                            name="category" 
                            list="categories" 
                            autocomplete="off"
                            required
                            oninput="filterCategories(this.value)"
                        >
                        <div id="categoryDropdown" class="category-dropdown">
                            <!-- Categories will be populated here -->
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="transaction_date">Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" required>
                </div>
                <div class="button-group">
                    <button type="button" onclick="hideAddTransactionPopup()">Cancel</button>
                    <button type="submit">Add</button>
                </div>
            </form>
        </div>
@endsection