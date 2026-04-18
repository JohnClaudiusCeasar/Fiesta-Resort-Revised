<template>
  <AdminLayout page="overview">

    <!-- Page Header -->
    <div class="page-header">
      <h1 class="page-title">Good morning, {{ firstName }} 👋</h1>
      <p class="page-subtitle">Here's what's happening at Fiesta Resort today.</p>
    </div>

    <!-- Main Content - Two Columns with Divider -->
    <div style="display: flex; gap: 0; flex-wrap: wrap;">
      
      <!-- Left Column - Revenue Section -->
      <div style="flex: 1; min-width: 400px; padding-right: 24px; border-right: 1px solid #e5e7eb;">
        
        <!-- Revenue Stats Grid (2x2) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Total Revenue</span>
              <div class="stat-icon">💰</div>
            </div>
            <div class="stat-value">${{ formatNumber(stats.totalRevenue) }}</div>
            <div class="stat-change">All time</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Monthly Revenue</span>
              <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value">${{ formatNumber(stats.monthlyRevenue) }}</div>
            <div class="stat-change">This month</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Daily Revenue</span>
              <div class="stat-icon">📈</div>
            </div>
            <div class="stat-value">${{ formatNumber(stats.dailyRevenue) }}</div>
            <div class="stat-change">Today</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Yearly Revenue</span>
              <div class="stat-icon">💵</div>
            </div>
            <div class="stat-value">${{ formatNumber(stats.yearlyRevenue) }}</div>
            <div class="stat-change">This year</div>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="admin-card">
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <div>
              <div class="page-title" style="font-size:15px;">Revenue Trends</div>
              <div class="page-subtitle" style="font-size:12px; margin-top:2px;">Track your revenue over time</div>
            </div>
          </div>

          <!-- Period Toggle -->
          <div style="display:flex; gap:8px; margin-bottom:20px;">
            <button
              v-for="period in periods"
              :key="period.value"
              @click="selectedPeriod = period.value"
              :class="['period-btn', { active: selectedPeriod === period.value }]"
            >
              {{ period.label }}
            </button>
          </div>

          <!-- Chart -->
          <div class="chart-container">
            <apexchart
              type="area"
              height="300"
              :options="chartOptions"
              :series="chartSeries"
            />
          </div>
        </div>

      </div>

      <!-- Right Column - Booking Section -->
      <div style="flex: 1; min-width: 400px; padding-left: 24px;">
        
        <!-- Booking Stats Grid (2x2) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Today's Bookings</span>
              <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value">{{ stats.todayBookings }}</div>
            <div class="stat-change up">↑ 3 from yesterday</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Occupied Rooms</span>
              <div class="stat-icon">🔑</div>
            </div>
            <div class="stat-value">{{ stats.occupiedRooms }}</div>
            <div class="stat-change">{{ occupancyRate }}% occupancy rate</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Available Rooms</span>
              <div class="stat-icon">🛏️</div>
            </div>
            <div class="stat-value">{{ stats.availableRooms }}</div>
            <div class="stat-change">of {{ stats.totalRooms }} total rooms</div>
          </div>

          <div class="stat-card">
            <div class="stat-header">
              <span class="stat-label">Reserved Rooms</span>
              <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value">{{ stats.reservedRooms }}</div>
            <div class="stat-change">rooms booked for later</div>
          </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="admin-card">
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <div>
              <div class="page-title" style="font-size:15px;">Recent Bookings</div>
              <div class="page-subtitle" style="font-size:12px; margin-top:2px;">Latest 5 reservations</div>
            </div>
            <Link href="/admin/bookings" class="btn btn-secondary" style="font-size:12.5px; padding:6px 14px;">
              View All →
            </Link>
          </div>

          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Guest</th>
                  <th>Room</th>
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in recentBookings" :key="booking.id">
                  <td>
                    <span style="font-family: var(--font-mono); font-size:12px; color: var(--admin-blue);">
                      #{{ booking.display_id }}
                    </span>
                  </td>
                  <td>{{ booking.guest }}</td>
                  <td>{{ booking.room }}</td>
                  <td>{{ booking.checkIn }}</td>
                  <td>{{ booking.checkOut }}</td>
                  <td>
                    <span class="badge" :class="badgeClass(booking.status)">
                      {{ booking.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div v-if="recentBookings.length === 0" class="empty-state">
              <div class="empty-icon">📋</div>
              <div class="empty-title">No bookings yet</div>
              <div class="empty-text">Recent reservations will appear here.</div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'
import { Link } from "@inertiajs/vue3";
import VueApexCharts from "vue3-apexcharts";

export default {
  name: 'AdminOverview',
  components: { AdminLayout, Link, apexchart: VueApexCharts },

  data() {
    return {
      selectedPeriod: 'weekly',
      periods: [
        { label: 'Weekly', value: 'weekly' },
        { label: 'Monthly', value: 'monthly' },
        { label: 'Yearly', value: 'yearly' },
      ]
    }
  },

  computed: {
    firstName() {
      return this.$page?.props?.user?.name?.split(' ')[0] || 'Admin'
    },

    stats() {
      const backendStats = this.$page?.props?.stats || {}
      return {
        todayBookings: backendStats.todayBookings || 0,
        availableRooms: backendStats.availableRooms || 0,
        occupiedRooms: backendStats.occupiedRooms || 0,
        reservedRooms: backendStats.reservedRooms || 0,
        totalRooms: backendStats.totalRooms || 0,
        totalGuests: backendStats.totalGuests || 0,
        totalRevenue: backendStats.totalRevenue || 0,
        dailyRevenue: backendStats.dailyRevenue || 0,
        monthlyRevenue: backendStats.monthlyRevenue || 0,
        yearlyRevenue: backendStats.yearlyRevenue || 0,
        weeklyRevenue: backendStats.weeklyRevenue || [],
        monthlyRevenueData: backendStats.monthlyRevenueData || [],
        yearlyRevenueData: backendStats.yearlyRevenueData || [],
      }
    },

    recentBookings() {
      return (this.$page?.props?.bookings || []).slice(0, 5)
    },

    occupancyRate() {
      if (!this.stats.totalRooms) return 0
      return Math.round((this.stats.occupiedRooms / this.stats.totalRooms) * 100)
    },

    chartData() {
      const data = this.stats
      switch (this.selectedPeriod) {
        case 'weekly':
          return {
            labels: data.weeklyRevenue.map(d => this.formatDate(d.date)),
            values: data.weeklyRevenue.map(d => d.revenue)
          }
        case 'monthly':
          return {
            labels: data.monthlyRevenueData.map(d => d.month),
            values: data.monthlyRevenueData.map(d => d.revenue)
          }
        case 'yearly':
          return {
            labels: data.yearlyRevenueData.map(d => d.year),
            values: data.yearlyRevenueData.map(d => d.revenue)
          }
        default:
          return { labels: [], values: [] }
      }
    },

    chartOptions() {
      return {
        chart: {
          type: 'area',
          toolbar: { show: false },
          fontFamily: 'inherit',
          background: 'transparent',
        },
        colors: ['#00B4FF'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [0, 100]
          }
        },
        xaxis: {
          categories: this.chartData.labels,
          labels: { style: { colors: '#6b7280', fontSize: '12px' } },
          axisBorder: { show: false },
          axisTicks: { show: false },
        },
        yaxis: {
          labels: {
            style: { colors: '#6b7280', fontSize: '12px' },
            formatter: (val) => '$' + this.formatNumber(val)
          }
        },
        grid: {
          borderColor: '#e5e7eb',
          strokeDashArray: 4,
        },
        tooltip: {
          y: {
            formatter: (val) => '$' + this.formatNumber(val)
          }
        }
      }
    },

    chartSeries() {
      return [{
        name: 'Revenue',
        data: this.chartData.values
      }]
    }
  },

  methods: {
    badgeClass(status) {
      const map = {
        'Confirmed':   'badge-confirmed',
        'Pending':     'badge-pending',
        'Cancelled':   'badge-cancelled',
        'Checked In':  'badge-checked-in',
        'Checked Out': 'badge-checked-out',
      }
      return map[status] || ''
    },

    formatNumber(num) {
      if (!num) return '0'
      return new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num)
    },

    formatDate(dateStr) {
      const date = new Date(dateStr)
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    }
  }
}
</script>

<style scoped>
.period-btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: #fff;
  color: #6b7280;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.period-btn:hover {
  border-color: #00B4FF;
  color: #00B4FF;
}

.period-btn.active {
  background: linear-gradient(135deg, #00B4FF, #009CE0);
  border-color: #00B4FF;
  color: #fff;
}

.chart-container {
  width: 100%;
  min-height: 300px;
}

.btn-primary {
  background: linear-gradient(135deg, #00B4FF, #009CE0);
  color: #fff;
  border: none;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0, 180, 255, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #009CE0, #0088C4);
  box-shadow: 0 4px 16px rgba(0, 180, 255, 0.4);
  transform: translateY(-1px);
}
</style>