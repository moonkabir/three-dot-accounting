<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

<style>
/* ─── Dashboard Variables ──────────────────────────────── */
:root {
    --card-radius: 16px;
    --card-bg: rgba(255,255,255,0.04);
    --card-border: rgba(255,255,255,0.08);
}

/* ─── Page Header ─────────────────────────────────────── */
.dash-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.dash-greeting {
    font-size: 1.6rem;
    font-weight: 800;
    color: #f1f5f9;
    letter-spacing: -0.03em;
}
.dash-greeting span {
    background: linear-gradient(90deg, #6366f1, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.dash-date {
    font-size: 0.82rem;
    color: #64748b;
    margin-top: 0.25rem;
}
.dash-actions {
    display: flex;
    gap: 0.75rem;
}
.btn-action {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.55rem 1.1rem;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.15s;
    border: none;
}
.btn-outline {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #94a3b8;
}
.btn-outline:hover { background: rgba(255,255,255,0.08); color: #f1f5f9; }
.btn-primary-sm {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    box-shadow: 0 4px 16px rgba(99,102,241,0.3);
}
.btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }

/* ─── KPI Cards ──────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}
@media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--card-radius);
    padding: 1.4rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    cursor: default;
}
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    border-radius: var(--card-radius) var(--card-radius) 0 0;
}
.kpi-card.kpi-revenue::before  { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.kpi-card.kpi-invoices::before { background: linear-gradient(90deg, #f59e0b, #fb923c); }
.kpi-card.kpi-expenses::before { background: linear-gradient(90deg, #ef4444, #f97316); }
.kpi-card.kpi-profit::before   { background: linear-gradient(90deg, #10b981, #34d399); }

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.3);
    border-color: rgba(255,255,255,0.14);
}

.kpi-glow {
    position: absolute;
    top: -20px; right: -20px;
    width: 100px; height: 100px;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.15;
    pointer-events: none;
}
.kpi-revenue  .kpi-glow { background: #6366f1; }
.kpi-invoices .kpi-glow { background: #f59e0b; }
.kpi-expenses .kpi-glow { background: #ef4444; }
.kpi-profit   .kpi-glow { background: #10b981; }

.kpi-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; }
.kpi-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
}
.kpi-revenue  .kpi-icon { background: rgba(99,102,241,0.15);  }
.kpi-invoices .kpi-icon { background: rgba(245,158,11,0.15);  }
.kpi-expenses .kpi-icon { background: rgba(239,68,68,0.15);   }
.kpi-profit   .kpi-icon { background: rgba(16,185,129,0.15);  }

.kpi-trend {
    display: flex; align-items: center; gap: 0.3rem;
    font-size: 0.75rem; font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}
.trend-up   { color: #34d399; background: rgba(52,211,153,0.1); }
.trend-down { color: #f87171; background: rgba(248,113,113,0.1); }

.kpi-label {
    font-size: 0.78rem;
    font-weight: 500;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.35rem;
}
.kpi-value {
    font-size: 1.85rem;
    font-weight: 800;
    color: #f1f5f9;
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 0.5rem;
}
.kpi-sub {
    font-size: 0.78rem;
    color: #475569;
}

/* ─── Charts Row ─────────────────────────────────────── */
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}
@media (max-width: 1100px) { .charts-grid { grid-template-columns: 1fr; } }

.chart-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--card-radius);
    padding: 1.5rem;
    transition: border-color 0.2s;
}
.chart-card:hover { border-color: rgba(99,102,241,0.2); }

.card-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #e2e8f0;
}
.card-subtitle {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 0.15rem;
}
.chart-legend {
    display: flex;
    gap: 1rem;
}
.legend-item {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.78rem; color: #64748b;
}
.legend-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
}

.chart-container {
    position: relative;
    height: 240px;
}

/* Donut chart info */
.donut-stats {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}
.donut-stat {
    text-align: center;
}
.donut-stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #f1f5f9;
}
.donut-stat-label {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 0.2rem;
}

/* ─── Tables Row ─────────────────────────────────────── */
.tables-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}
@media (max-width: 1000px) { .tables-grid { grid-template-columns: 1fr; } }

.table-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--card-radius);
    overflow: hidden;
    transition: border-color 0.2s;
}
.table-card:hover { border-color: rgba(99,102,241,0.2); }

.table-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--card-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.view-all {
    font-size: 0.78rem;
    color: #818cf8;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.15s;
}
.view-all:hover { color: #a78bfa; }

/* Data Table */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    font-size: 0.7rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    padding: 0.6rem 1.5rem;
    text-align: left;
    border-bottom: 1px solid var(--card-border);
    background: rgba(255,255,255,0.02);
}
.data-table td {
    padding: 0.8rem 1.5rem;
    font-size: 0.84rem;
    color: #cbd5e1;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tbody tr { transition: background 0.12s; }
.data-table tbody tr:hover { background: rgba(255,255,255,0.03); }

.badge {
    display: inline-flex; align-items: center;
    padding: 2px 10px;
    border-radius: 100px;
    font-size: 0.72rem;
    font-weight: 600;
}
.badge-success { background: rgba(16,185,129,0.12); color: #34d399; }
.badge-warning { background: rgba(245,158,11,0.12); color: #fbbf24; }
.badge-danger  { background: rgba(239,68,68,0.12);  color: #f87171; }
.badge-info    { background: rgba(59,130,246,0.12);  color: #60a5fa; }

/* Rank indicator */
.rank-num {
    width: 24px; height: 24px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 700;
    background: rgba(99,102,241,0.12);
    color: #818cf8;
}

/* Mini progress bar */
.progress-bar-wrap {
    width: 80px;
    height: 6px;
    background: rgba(255,255,255,0.06);
    border-radius: 3px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
}

/* ─── Ticker Strip ─────────────────────────────────── */
.ticker-strip {
    display: flex;
    gap: 2rem;
    padding: 0.65rem 1.5rem;
    background: rgba(99,102,241,0.06);
    border: 1px solid rgba(99,102,241,0.15);
    border-radius: 12px;
    margin-bottom: 1.75rem;
    overflow: hidden;
    position: relative;
}
.ticker-item {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.82rem; white-space: nowrap;
}
.ticker-label { color: #64748b; font-weight: 500; }
.ticker-val   { color: #f1f5f9; font-weight: 600; }
.ticker-chg   { font-size: 0.75rem; font-weight: 600; }

/* ─── Activity Feed (small panel) ─────────────────── */
.activity-feed { padding: 0.5rem 0; }
.activity-item {
    display: flex; align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    transition: background 0.12s;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: rgba(255,255,255,0.02); }
.activity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-top: 0.35rem;
    flex-shrink: 0;
}
.activity-text { font-size: 0.82rem; color: #94a3b8; line-height: 1.5; }
.activity-text strong { color: #e2e8f0; font-weight: 600; }
.activity-time { font-size: 0.72rem; color: #475569; margin-top: 0.15rem; }
</style>

{{-- ── Page Header ─────────────────────────────────────── --}}
<div class="dash-header">
    <div>
        <h1 class="dash-greeting">Good morning, <span>{{ explode(' ', Auth::user()->name)[0] }}</span> 👋</h1>
        <div class="dash-date" id="dash-datetime"></div>
    </div>
    <div class="dash-actions">
        <button class="btn-action btn-outline">📥 Export Report</button>
        <button class="btn-action btn-primary-sm">+ New Invoice</button>
    </div>
</div>

{{-- ── Ticker Strip ─────────────────────────────────────── --}}
<div class="ticker-strip">
    <div class="ticker-item">
        <span class="ticker-label">USD/BDT</span>
        <span class="ticker-val">110.45</span>
        <span class="ticker-chg trend-up">▲ 0.3%</span>
    </div>
    <div class="ticker-item">
        <span class="ticker-label">Total Receivables</span>
        <span class="ticker-val">৳ 8,42,500</span>
        <span class="ticker-chg trend-down">▼ 2.1%</span>
    </div>
    <div class="ticker-item">
        <span class="ticker-label">Payables Due</span>
        <span class="ticker-val">৳ 3,12,000</span>
        <span class="ticker-chg trend-up">▲ 0.8%</span>
    </div>
    <div class="ticker-item">
        <span class="ticker-label">Cash Balance</span>
        <span class="ticker-val">৳ 24,80,000</span>
        <span class="ticker-chg trend-up">▲ 5.4%</span>
    </div>
    <div class="ticker-item">
        <span class="ticker-label">Active Employees</span>
        <span class="ticker-val">148</span>
        <span class="ticker-chg trend-up">▲ 3</span>
    </div>
</div>

{{-- ── KPI Cards ─────────────────────────────────────────── --}}
<div class="kpi-grid">

    <div class="kpi-card kpi-revenue">
        <div class="kpi-glow"></div>
        <div class="kpi-header">
            <div class="kpi-icon">💰</div>
            <div class="kpi-trend trend-up">▲ 12.5%</div>
        </div>
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-value">৳ 48.2L</div>
        <div class="kpi-sub">vs ৳ 42.8L last month</div>
    </div>

    <div class="kpi-card kpi-invoices">
        <div class="kpi-glow"></div>
        <div class="kpi-header">
            <div class="kpi-icon">🧾</div>
            <div class="kpi-trend trend-down">▼ 3.2%</div>
        </div>
        <div class="kpi-label">Outstanding Invoices</div>
        <div class="kpi-value">৳ 8.4L</div>
        <div class="kpi-sub">34 invoices pending</div>
    </div>

    <div class="kpi-card kpi-expenses">
        <div class="kpi-glow"></div>
        <div class="kpi-header">
            <div class="kpi-icon">📉</div>
            <div class="kpi-trend trend-up">▲ 4.1%</div>
        </div>
        <div class="kpi-label">Monthly Expenses</div>
        <div class="kpi-value">৳ 19.6L</div>
        <div class="kpi-sub">Budget limit: ৳ 22L</div>
    </div>

    <div class="kpi-card kpi-profit">
        <div class="kpi-glow"></div>
        <div class="kpi-header">
            <div class="kpi-icon">📊</div>
            <div class="kpi-trend trend-up">▲ 18.7%</div>
        </div>
        <div class="kpi-label">Net Profit</div>
        <div class="kpi-value">৳ 28.6L</div>
        <div class="kpi-sub">Margin: 59.3%</div>
    </div>

</div>

{{-- ── Charts Row ────────────────────────────────────────── --}}
<div class="charts-grid">

    {{-- Revenue vs Expenses Bar Chart --}}
    <div class="chart-card">
        <div class="card-title-row">
            <div>
                <div class="card-title">Revenue vs Expenses</div>
                <div class="card-subtitle">Last 7 months comparison</div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-dot" style="background:#6366f1;"></div>
                    <span>Revenue</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#ef4444;"></div>
                    <span>Expenses</span>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Expense Breakdown Donut --}}
    <div class="chart-card">
        <div class="card-title-row">
            <div>
                <div class="card-title">Expense Breakdown</div>
                <div class="card-subtitle">By category this month</div>
            </div>
        </div>
        <div class="chart-container" style="height: 200px;">
            <canvas id="expenseDonut"></canvas>
        </div>
        <div class="donut-stats">
            <div class="donut-stat">
                <div class="donut-stat-value" style="color:#6366f1;">42%</div>
                <div class="donut-stat-label">Operations</div>
            </div>
            <div class="donut-stat">
                <div class="donut-stat-value" style="color:#f59e0b;">28%</div>
                <div class="donut-stat-label">Payroll</div>
            </div>
            <div class="donut-stat">
                <div class="donut-stat-value" style="color:#10b981;">18%</div>
                <div class="donut-stat-label">Marketing</div>
            </div>
            <div class="donut-stat">
                <div class="donut-stat-value" style="color:#3b82f6;">12%</div>
                <div class="donut-stat-label">Other</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Tables Row ────────────────────────────────────────── --}}
<div class="tables-grid">

    {{-- Recent Transactions --}}
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="card-title">Recent Transactions</div>
                <div class="card-subtitle" style="margin-top:0.15rem;">Latest financial activity</div>
            </div>
            <span class="view-all">View all →</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Invoice #INV-0082</div>
                        <div style="font-size:0.72rem; color:#475569;">Apex Solutions Ltd</div>
                    </td>
                    <td style="color:#34d399; font-weight:600;">+৳ 45,000</td>
                    <td><span class="badge badge-success">Paid</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Office Rent — Sep</div>
                        <div style="font-size:0.72rem; color:#475569;">Recurring expense</div>
                    </td>
                    <td style="color:#f87171; font-weight:600;">-৳ 32,000</td>
                    <td><span class="badge badge-success">Cleared</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Invoice #INV-0081</div>
                        <div style="font-size:0.72rem; color:#475569;">Meridian Corp</div>
                    </td>
                    <td style="color:#34d399; font-weight:600;">+৳ 1,20,000</td>
                    <td><span class="badge badge-warning">Pending</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Vendor Payment</div>
                        <div style="font-size:0.72rem; color:#475569;">Star Supplies Co.</div>
                    </td>
                    <td style="color:#f87171; font-weight:600;">-৳ 18,500</td>
                    <td><span class="badge badge-success">Cleared</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Invoice #INV-0080</div>
                        <div style="font-size:0.72rem; color:#475569;">BlueSky Ventures</div>
                    </td>
                    <td style="color:#34d399; font-weight:600;">+৳ 75,000</td>
                    <td><span class="badge badge-danger">Overdue</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Top Customers --}}
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="card-title">Top Customers</div>
                <div class="card-subtitle" style="margin-top:0.15rem;">By revenue this quarter</div>
            </div>
            <span class="view-all">View all →</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Revenue</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="rank-num">1</span></td>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Apex Solutions</div>
                        <div style="font-size:0.72rem; color:#475569;">Enterprise</div>
                    </td>
                    <td style="font-weight:600; color:#a5b4fc;">৳ 4.8L</td>
                    <td>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:88%;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="rank-num">2</span></td>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Meridian Corp</div>
                        <div style="font-size:0.72rem; color:#475569;">Corporate</div>
                    </td>
                    <td style="font-weight:600; color:#a5b4fc;">৳ 3.6L</td>
                    <td>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:66%;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="rank-num">3</span></td>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">BlueSky Ventures</div>
                        <div style="font-size:0.72rem; color:#475569;">SME</div>
                    </td>
                    <td style="font-weight:600; color:#a5b4fc;">৳ 2.9L</td>
                    <td>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:53%;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="rank-num">4</span></td>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">NexaGroup Ltd</div>
                        <div style="font-size:0.72rem; color:#475569;">Corporate</div>
                    </td>
                    <td style="font-weight:600; color:#a5b4fc;">৳ 2.1L</td>
                    <td>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:38%;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="rank-num">5</span></td>
                    <td>
                        <div style="font-weight:500; color:#e2e8f0;">Delta Imports</div>
                        <div style="font-size:0.72rem; color:#475569;">SME</div>
                    </td>
                    <td style="font-weight:600; color:#a5b4fc;">৳ 1.7L</td>
                    <td>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:31%;"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- ── Chart.js Scripts ─────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Datetime display ──────────────────────────────
    const dtEl = document.getElementById('dash-datetime');
    function updateTime() {
        const now = new Date();
        dtEl.textContent = now.toLocaleDateString('en-GB', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        }) + '  •  ' + now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }
    updateTime(); setInterval(updateTime, 60000);

    // ── Chart Defaults ─────────────────────────────────
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    Chart.defaults.font.family = 'Inter';

    // ── Revenue vs Expenses Bar Chart ─────────────────
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    const revGrad = ctx1.createLinearGradient(0,0,0,240);
    revGrad.addColorStop(0,   'rgba(99,102,241,0.8)');
    revGrad.addColorStop(1,   'rgba(99,102,241,0.1)');
    const expGrad = ctx1.createLinearGradient(0,0,0,240);
    expGrad.addColorStop(0,   'rgba(239,68,68,0.7)');
    expGrad.addColorStop(1,   'rgba(239,68,68,0.05)');

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
            datasets: [
                {
                    label: 'Revenue',
                    data: [38, 42, 36, 44, 40, 43, 48],
                    backgroundColor: revGrad,
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Expenses',
                    data: [22, 25, 20, 27, 18, 21, 19],
                    backgroundColor: expGrad,
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(13,13,31,0.95)',
                    borderColor: 'rgba(99,102,241,0.3)',
                    borderWidth: 1,
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ৳ ${ctx.raw}L`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#475569', font: { size: 12 } },
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#475569', font: { size: 12 },
                        callback: v => '৳ ' + v + 'L'
                    },
                    beginAtZero: true,
                }
            }
        }
    });

    // ── Expense Donut ─────────────────────────────────
    const ctx2 = document.getElementById('expenseDonut').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Operations', 'Payroll', 'Marketing', 'Other'],
            datasets: [{
                data: [42, 28, 18, 12],
                backgroundColor: [
                    'rgba(99,102,241,0.85)',
                    'rgba(245,158,11,0.85)',
                    'rgba(16,185,129,0.85)',
                    'rgba(59,130,246,0.85)',
                ],
                borderColor: '#0d0d1f',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(13,13,31,0.95)',
                    borderColor: 'rgba(99,102,241,0.3)',
                    borderWidth: 1,
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}%` }
                }
            }
        }
    });

});
</script>

</x-app-layout>
