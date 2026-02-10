@extends('layout')

@section('content')
<style>
    .profit-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .date-selector {
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 14px;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        margin: 10px 0;
    }

    .stat-card.revenue {
        border-top: 4px solid #4CAF50;
    }

    .stat-card.revenue .stat-value {
        color: #4CAF50;
    }

    .stat-card.expenses {
        border-top: 4px solid #FF9800;
    }

    .stat-card.expenses .stat-value {
        color: #FF9800;
    }

    .stat-card.profit {
        border-top: 4px solid #2196F3;
    }

    .stat-card.profit .stat-value {
        color: #2196F3;
    }

    .stat-card.loss {
        border-top: 4px solid #f44336;
    }

    .stat-card.loss .stat-value {
        color: #f44336;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFD700;
    }

    .section-header h3 {
        margin: 0;
        color: #800000;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-add:hover {
        background: #e6c200;
    }

    .expense-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .expense-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .expense-item:last-child {
        border-bottom: none;
    }

    .expense-info {
        flex: 1;
    }

    .expense-category {
        display: inline-block;
        background: #FFD700;
        color: #800000;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .expense-desc {
        font-size: 14px;
        color: #333;
        margin: 3px 0;
    }

    .expense-amount {
        font-size: 16px;
        font-weight: bold;
        color: #FF9800;
        margin-right: 10px;
    }

    .btn-delete {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .modal.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
    }

    .modal-header {
        margin-bottom: 20px;
    }

    .modal-header h3 {
        margin: 0;
        color: #800000;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: #800000;
        color: #fff;
    }

    .btn-primary:hover {
        background: #a00000;
    }

    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profit-container">
    <h2 style="color:#800000; margin-bottom:10px;">💰 Daily Profit Calculator</h2>
    <p style="color:#666; margin-bottom:20px;">Track your income and expenses</p>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background:#d4edda; padding:15px; border-radius:5px; margin-bottom:20px; color:#155724;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Date Selector -->
    <div class="date-selector">
        <label for="date" style="font-weight:bold; color:#800000;">Select Date:</label>
        <input type="date" id="date" value="{{ $date }}"
            onchange="window.location.href='{{ route('expenses.index') }}?date=' + this.value"
            style="padding:8px; border:1px solid #ddd; border-radius:5px;">
        <button onclick="document.getElementById('date').value='{{ \Carbon\Carbon::today()->toDateString() }}'; document.getElementById('date').dispatchEvent(new Event('change'));"
            style="padding:8px 15px; background:#FFD700; color:#800000; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">
            Today
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card revenue">
            <h3>💵 Total Revenue</h3>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <p style="color:#666; font-size:13px; margin:0;">From sales</p>
        </div>

        <div class="stat-card expenses">
            <h3>📊 Total Expenses</h3>
            <div class="stat-value">₱{{ number_format($totalExpenses, 2) }}</div>
            <p style="color:#666; font-size:13px; margin:0;">Daily costs</p>
        </div>

        <div class="stat-card {{ $profit >= 0 ? 'profit' : 'loss' }}">
            <h3>{{ $profit >= 0 ? '✅ Net Profit' : '⚠️ Net Loss' }}</h3>
            <div class="stat-value">₱{{ number_format(abs($profit), 2) }}</div>
            <p style="color:#666; font-size:13px; margin:0;">
                {{ $profit >= 0 ? 'Keep it up!' : 'Reduce expenses' }}
            </p>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Expenses List -->
        <div class="section-card">
            <div class="section-header">
                <h3>📝 Expenses</h3>
                <button class="btn-add" onclick="openModal()">+ Add Expense</button>
            </div>

            <div class="expense-list">
                @forelse($expenses as $expense)
                <div class="expense-item">
                    <div class="expense-info">
                        <span class="expense-category">{{ $expense->category }}</span>
                        <div class="expense-desc">{{ $expense->description }}</div>
                    </div>
                    <span class="expense-amount">₱{{ number_format($expense->amount, 2) }}</span>
                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Delete this expense?')">✕</button>
                    </form>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-receipt" style="font-size:48px; color:#ccc;"></i>
                    <p>No expenses recorded for this date</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Summary & Tips -->
        <div class="section-card">
            <div class="section-header">
                <h3>📊 Summary</h3>
            </div>

            <div style="padding:15px; background:#f8f9fa; border-radius:8px; margin-bottom:15px;">
                <h4 style="margin:0 0 10px 0; color:#800000;">Profit Margin</h4>
                @php
                // Calculate margin - FIXED: Define it outside the if block
                $margin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
                @endphp

                @if($totalRevenue > 0)
                <div style="font-size:24px; font-weight:bold; color:{{ $margin >= 0 ? '#4CAF50' : '#f44336' }};">
                    {{ number_format($margin, 1) }}%
                </div>
                @else
                <div style="color:#999;">No sales yet</div>
                @endif
            </div>

            <div style="padding:15px; background:#fff3cd; border-radius:8px; border-left:4px solid:#ffc107;">
                <h4 style="margin:0 0 10px 0; color:#856404;">💡 Tips</h4>
                <ul style="margin:0; padding-left:20px; color:#856404; font-size:13px; line-height:1.6;">
                    @if($profit < 0)
                        <li>Your expenses exceed revenue. Review your costs.</li>
                        <li>Consider increasing prices or reducing waste.</li>
                        @elseif($margin < 30)
                            <li>Profit margin is low. Aim for at least 30%.</li>
                            <li>Look for ways to reduce ingredient costs.</li>
                            @else
                            <li>Great profit margin! Keep up the good work!</li>
                            <li>Consider investing in more inventory.</li>
                            @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="expenseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Expense</h3>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Category*</label>
                <select name="category" required>
                    <option value="">Select category...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Description*</label>
                <input type="text" name="description" placeholder="e.g., Fishballs from supplier" required>
            </div>

            <div class="form-group">
                <label>Amount (₱)*</label>
                <input type="number" name="amount" step="0.01" min="0" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label>Date*</label>
                <input type="date" name="expense_date" value="{{ $date }}" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Expense</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('expenseModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('expenseModal').classList.remove('active');
    }

    // Close modal when clicking outside
    document.getElementById('expenseModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>

@endsection