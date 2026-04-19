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
        
        <!-- Revenue Stats Card with Navigation -->
        <div
          class="stat-main-card"
          :class="{ expanded: expandedCard === 'revenue', contracted: expandedCard !== 'revenue' }"
          @click="toggleExpand('revenue')"
        >
          <div class="stat-nav-header">
            <button class="stat-nav-btn" @click.stop="cycleStat('revenue', -1)">‹</button>
            <span class="stat-main-label">{{ revenueStats[revenueStatIndex].label }}</span>
            <button class="stat-nav-btn" @click.stop="cycleStat('revenue', 1)">›</button>
          </div>
          <div class="stat-main-value">${{ formatNumber(currentRevenueValue) }}</div>
          <div class="stat-main-subtitle">{{ revenueStats[revenueStatIndex].subtitle }}</div>

          <!-- Expansion Combo Field -->
          <div v-if="expandedCard === 'revenue'" class="stat-combo-field">
            <div class="combo-item" v-for="item in revenueBreakdown" :key="item.label">
              <div class="combo-label">{{ item.label }}</div>
              <div class="combo-value">${{ formatNumber(item.value) }}</div>
            </div>
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

          <!-- Chart -->
          <div class="chart-container">
            <apexchart
              type="donut"
              width="100%"
              height="320"
              :options="simpleDonutOptions"
              :series="[2500, 15000, 45000, 120000]"
            />
          </div>
        </div>

      </div>

      <!-- Right Column - Booking Section -->
      <div style="flex: 1; min-width: 400px; padding-left: 24px;">
        
        <!-- Bookings Stats Card with Navigation -->
        <div
          class="stat-main-card"
          :class="{ expanded: expandedCard === 'bookings', contracted: expandedCard !== 'bookings' }"
          @click="toggleExpand('bookings')"
        >
          <div class="stat-nav-header">
            <button class="stat-nav-btn" @click.stop="cycleStat('bookings', -1)">‹</button>
            <span class="stat-main-label">{{ bookingStats[bookingStatIndex].label }}</span>
            <button class="stat-nav-btn" @click.stop="cycleStat('bookings', 1)">›</button>
          </div>
          <div class="stat-main-value">{{ currentBookingValue }}</div>
          <div class="stat-main-subtitle">{{ bookingStats[bookingStatIndex].subtitle }}</div>

          <!-- Expansion Combo Field -->
          <div v-if="expandedCard === 'bookings'" class="stat-combo-field">
            <div class="combo-item" v-for="item in bookingBreakdown" :key="item.label">
              <div class="combo-label">{{ item.label }}</div>
              <div class="combo-value">{{ item.value }}</div>
            </div>
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
  components: { 
    AdminLayout, 
    Link,
    apexchart: VueApexCharts
  },

  data() {
    return {
      expandedCard: null,
      bookingStatIndex: 0,
      revenueStatIndex: 0,
      bookingStats: [
        { label: "Today's Bookings", getValue: (s) => s.todayBookings, subtitle: 'Check-ins today' },
        { label: 'Available Rooms', getValue: (s) => s.availableRooms, subtitle: 'Ready for booking' },
        { label: 'Occupied Rooms', getValue: (s) => s.occupiedRooms, subtitle: 'Currently in use' },
        { label: 'Reserved Rooms', getValue: (s) => s.reservedRooms, subtitle: 'booked for later' },
      ],
      revenueStats: [
        { label: 'Total Revenue', getValue: (s) => s.totalRevenue, subtitle: 'All time' },
        { label: 'Daily Revenue', getValue: (s) => s.dailyRevenue, subtitle: 'Today' },
        { label: 'Monthly Revenue', getValue: (s) => s.monthlyRevenue, subtitle: 'This month' },
        { label: 'Yearly Revenue', getValue: (s) => s.yearlyRevenue, subtitle: 'This year' },
      ],
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

    currentBookingValue() {
      return this.bookingStats[this.bookingStatIndex].getValue(this.stats)
    },

    currentRevenueValue() {
      return this.revenueStats[this.revenueStatIndex].getValue(this.stats)
    },

    bookingBreakdown() {
      return [
        { label: 'Available', value: this.stats.availableRooms },
        { label: 'Occupied', value: this.stats.occupiedRooms },
        { label: 'Reserved', value: this.stats.reservedRooms },
      ]
    },

    revenueBreakdown() {
      const weeklyVal = Array.isArray(this.stats.weeklyRevenue) 
        ? this.stats.weeklyRevenue.reduce((sum, d) => sum + (d.revenue || 0), 0)
        : 0
      return [
        { label: 'Daily', value: this.stats.dailyRevenue || 0 },
        { label: 'Weekly', value: Math.round(weeklyVal) },
        { label: 'Monthly', value: this.stats.monthlyRevenue || 0 },
        { label: 'Yearly', value: this.stats.yearlyRevenue || 0 },
      ]
    },

pieChartSeries() {
      return [
        this.stats.dailyRevenue || 0,
        Array.isArray(this.stats.weeklyRevenue) ? this.stats.weeklyRevenue.reduce((sum, d) => sum + (d.revenue || 0), 0) : 0,
        this.stats.monthlyRevenue || 0,
        this.stats.yearlyRevenue || 0
      ]
    },

    pieChartOptions() {
      return {
        chart: {
          type: 'pie'
        },
        labels: ['Daily', 'Weekly', 'Monthly', 'Yearly'],
        legend: {
          position: 'bottom'
        }
      }
    },

    simpleDonutOptions() {
      return {
        chart: {
          type: 'donut'
        },
        labels: ['Daily', 'Weekly', 'Monthly', 'Yearly'],
        colors: ['#00B4FF', '#8B5CF6', '#10B981', '#F59E0B'],
        plotOptions: {
          donut: {
            size: '65%',
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: '14px'
              },
              value: {
                show: true,
                fontSize: '18px'
              }
            }
          }
        },
        legend: {
          position: 'bottom'
        },
        dataLabels: {
          enabled: true
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: { position: 'bottom' }
          }
        }]
      }
    },

    pieChartOptions() {
      return {
        chart: {
          type: 'pie',
          animations: {
            enabled: true
          }
        },
        labels: ['Daily', 'Weekly', 'Monthly', 'Yearly'],
        colors: ['#00E5FF', '#9C27B0', '#4CAF50', '#FF9800'],
        legend: {
          position: 'bottom',
          fontSize: '14px',
          markers: {
            size: 8
          }
        },
        dataLabels: {
          enabled: true,
          dropShadow: {
            enabled: false
          },
          formatter: (val, opts) => {
            return '$' + this.formatNumber(Number(val))
          }
        },
        tooltip: {
          enabled: true,
          theme: 'light',
          y: {
            formatter: (val) => '$' + this.formatNumber(val)
          }
        },
        fill: {
          type: 'solid',
          opacity: 1
        },
        stroke: {
          width: 2,
          colors: ['#fff']
        },
        plotOptions: {
          pie: {
            expandOnClick: true,
            customScale: 1
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: { position: 'bottom' }
          }
        }]
      }
    },
  },

  methods: {
    toggleExpand(card) {
      if (this.expandedCard === card) {
        this.expandedCard = null
      } else {
        this.expandedCard = card
      }
    },

    cycleStat(type, direction) {
      if (type === 'bookings') {
        const len = this.bookingStats.length
        this.bookingStatIndex = (this.bookingStatIndex + direction + len) % len
      } else if (type === 'revenue') {
        const len = this.revenueStats.length
        this.revenueStatIndex = (this.revenueStatIndex + direction + len) % len
      }
    },

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
.stat-main-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  overflow: hidden;
}

.stat-main-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.stat-main-card.expanded {
  box-shadow: 0 8px 32px rgba(0, 180, 255, 0.2);
  transform: scale(1.02);
}

.stat-main-card.contracted {
  margin-bottom: 12px;
}

.stat-nav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.stat-nav-btn {
  background: #f3f4f6;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 18px;
  color: #6b7280;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-nav-btn:hover {
  background: #00B4FF;
  color: #fff;
}

.stat-main-label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.stat-main-value {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 4px;
}

.stat-main-subtitle {
  font-size: 13px;
  color: #6b7280;
}

.stat-combo-field {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
  animation: slideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.stat-combo-field .combo-item {
  flex: 1;
  min-width: 0;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.combo-item {
  text-align: center;
  padding: 12px 8px;
  background: #f9fafb;
  border-radius: 8px;
}

.combo-label {
  font-size: 11px;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.combo-value {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
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